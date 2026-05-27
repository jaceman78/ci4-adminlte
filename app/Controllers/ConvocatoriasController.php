<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SessaoExameModel;
use App\Models\SessaoExameSalaModel;
use App\Models\ConvocatoriaModel;
use App\Models\UserModel;
use App\Models\ExameModel;
use CodeIgniter\HTTP\ResponseInterface;

class ConvocatoriasController extends BaseController
{
    protected $sessaoExameModel;
    protected $sessaoExameSalaModel;
    protected $convocatoriaModel;
    protected $userModel;
    protected $exameModel;

    public function __construct()
    {
        $this->sessaoExameModel = new SessaoExameModel();
        $this->sessaoExameSalaModel = new SessaoExameSalaModel();
        $this->convocatoriaModel = new ConvocatoriaModel();
        $this->userModel = new UserModel();
        $this->exameModel = new ExameModel();
        helper('notification');
    }

    /**
     * Página de convocação de vigilantes para uma sessão de exame
     */
    public function criar($sessaoExameId)
    {
        // Verificar se sessão existe
        $sessao = $this->sessaoExameModel->find($sessaoExameId);
        
        if (!$sessao) {
            return redirect()->to('/sessoes-exame')->with('error', 'Sessão de exame não encontrada.');
        }

        // Buscar informações completas da sessão
        $sessaoCompleta = $this->sessaoExameModel->getSessaoComExame($sessaoExameId);
        
        // Determinar a função esperada baseada no tipo de prova
        $funcaoEsperada = 'Vigilante'; // Padrão
        if (isset($sessaoCompleta['tipo_prova'])) {
            if ($sessaoCompleta['tipo_prova'] === 'Apoio TIC') {
                $funcaoEsperada = 'Apoio TIC';
            } elseif ($sessaoCompleta['tipo_prova'] === 'Estrutura de Apoio') {
                $funcaoEsperada = 'Estrutura de Apoio';
            } elseif ($sessaoCompleta['tipo_prova'] === 'Verificacao Calculadoras' || $sessaoCompleta['tipo_prova'] === 'Verificação Calculadoras') {
                $funcaoEsperada = 'Verificar Calculadoras';
            } elseif ($sessaoCompleta['tipo_prova'] === 'Verificação de Materiais') {
                $funcaoEsperada = 'Verificar Materiais';
            } elseif ($sessaoCompleta['tipo_prova'] === 'Suplentes') {
                $funcaoEsperada = 'Suplente';
            }
        }
        
        // Buscar salas alocadas com estatísticas
        $salasAlocadas = $this->sessaoExameSalaModel->getSalasComEstatisticas($sessaoExameId);

        // Buscar todos os professores (user role = professor ou similar)
        // Assumindo que professores têm role_id específico
        $professores = $this->userModel
            ->select('user.id, user.name, user.email, user.grupo_id, 
                (SELECT COUNT(*) FROM convocatoria c
                 INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
                 INNER JOIN exame e ON e.id = se.exame_id
                 WHERE c.user_id = user.id 
                 AND c.funcao = "Vigilante"
                 AND e.tipo_prova != "Suplentes") as total_vigilancias,
                (SELECT COUNT(*) FROM convocatoria c
                 INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
                 INNER JOIN exame e ON e.id = se.exame_id
                 WHERE c.user_id = user.id 
                 AND c.funcao = "Vigilante"
                 AND e.tipo_prova = "Suplentes") as total_suplencias')
            ->where('user.status', 1)
            ->orderBy('user.name', 'ASC')
            ->findAll();

        // Buscar convocatórias já existentes desta sessão (filtrando pela função esperada)
        $convocatoriasExistentes = $this->convocatoriaModel
            ->select('convocatoria.*, user.name as user_nome, sessao_exame_sala.id as sala_id')
            ->join('user', 'user.id = convocatoria.user_id')
            ->join('sessao_exame_sala', 'sessao_exame_sala.id = convocatoria.sessao_exame_sala_id', 'left')
            ->where('convocatoria.sessao_exame_id', $sessaoExameId)
            ->where('convocatoria.funcao', $funcaoEsperada)
            ->findAll();

        // Organizar convocatórias por sala
        $convocatoriasPorSala = [];
        foreach ($convocatoriasExistentes as $conv) {
            $salaId = $conv['sessao_exame_sala_id'] ?? 'sem_sala';
            if (!isset($convocatoriasPorSala[$salaId])) {
                $convocatoriasPorSala[$salaId] = [];
            }
            $convocatoriasPorSala[$salaId][] = $conv;
        }

        // IDs de professores já alocados
        $professoresAlocados = array_column($convocatoriasExistentes, 'user_id');

        $data = [
            'title' => 'Convocar Vigilantes - Sessão de Exame',
            'sessao' => $sessaoCompleta,
            'salasAlocadas' => $salasAlocadas,
            'professores' => $professores,
            'professoresAlocados' => $professoresAlocados,
            'convocatoriasPorSala' => $convocatoriasPorSala,
        ];

        return view('convocatorias/form', $data);
    }

    /**
     * API: Adicionar vigilante a uma sala
     */
    public function adicionarVigilante()
    {
        $data = $this->request->getJSON(true);

        $userId = $data['user_id'] ?? null;
        $sessaoExameSalaId = $data['sessao_exame_sala_id'] ?? null;
        $sessaoExameId = $data['sessao_exame_id'] ?? null;

        if (!$userId || !$sessaoExameSalaId || !$sessaoExameId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados incompletos.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Verificar tipo de exame para determinar a função correta
        $sessao = $this->sessaoExameModel->find($sessaoExameId);
        $exame = $this->exameModel->find($sessao['exame_id']);
        
        // Determinar a função baseada no tipo de prova
        $funcao = 'Vigilante'; // Padrão
        if ($exame) {
            $tipoProva = $exame['tipo_prova'];
            if ($tipoProva === 'Apoio TIC') {
                $funcao = 'Apoio TIC';
            } elseif ($tipoProva === 'Estrutura de Apoio') {
                $funcao = 'Estrutura de Apoio';
            } elseif ($tipoProva === 'Verificacao Calculadoras' || $tipoProva === 'Verificação Calculadoras') {
                $funcao = 'Verificar Calculadoras';
            } elseif ($tipoProva === 'Verificação de Materiais') {
                $funcao = 'Verificar Materiais';
            } elseif ($tipoProva === 'Suplentes') {
                $funcao = 'Suplente';
            }
        }

        // Verificar se já está convocado para esta sessão (com a função correta)
        $jaConvocado = $this->convocatoriaModel
            ->where('sessao_exame_id', $sessaoExameId)
            ->where('user_id', $userId)
            ->where('funcao', $funcao)
            ->first();

        if ($jaConvocado) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Este professor já está convocado para esta sessão de exame.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Sessões especiais não têm limite de vigilantes
        $semLimite = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Verificação Calculadoras', 'Apoio TIC', 'Estrutura de Apoio', 'Verificação de Materiais']));

        // Para sessões normais, verificar limite de vigilantes na sala
        if (!$semLimite) {
            // Verificar se a sala já atingiu o número de vigilantes necessários
            $sala = $this->sessaoExameSalaModel->find($sessaoExameSalaId);
            $vigilantesNaSala = $this->convocatoriaModel
                ->where('sessao_exame_sala_id', $sessaoExameSalaId)
                ->where('funcao', $funcao)
                ->countAllResults();

            if ($vigilantesNaSala >= $sala['vigilantes_necessarios']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Esta sala já atingiu o número máximo de vigilantes necessários.'
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
        }

        // Criar convocatória
        $convocatoriaData = [
            'sessao_exame_id' => $sessaoExameId,
            'user_id' => $userId,
            'sessao_exame_sala_id' => $sessaoExameSalaId,
            'funcao' => $funcao, // Função determinada pelo tipo de prova
            'estado_confirmacao' => 'Pendente'
        ];

        if ($this->convocatoriaModel->insert($convocatoriaData)) {
            // Buscar dados do professor para retornar
            $professor = $this->userModel->find($userId);

            // Notificar o professor convocado
            try {
                $nomeProva  = $exame['nome_prova'] ?? 'exame';
                $dataExame  = isset($sessao['data_exame']) ? date('d/m/Y', strtotime($sessao['data_exame'])) : '';
                $horaExame  = isset($sessao['hora_exame']) ? substr($sessao['hora_exame'], 0, 5) : '';
                $detalhe    = trim($dataExame . ($horaExame ? ' às ' . $horaExame : ''));
                criar_notificacao(
                    (int) $userId,
                    'convocatorias',
                    'warning',
                    'Nova convocatória de vigilância',
                    'Foi convocado(a) como ' . $funcao . ' para o exame "' . $nomeProva . '"' . ($detalhe ? ' (' . $detalhe . ')' : '') . '.',
                    base_url('dashboard')
                );
            } catch (\Exception $e) {
                log_message('warning', 'Erro ao notificar professor sobre convocatória: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Vigilante adicionado com sucesso.',
                'data' => [
                    'convocatoria_id' => $this->convocatoriaModel->getInsertID(),
                    'user_id' => $userId,
                    'user_nome' => $professor['name']
                ]
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao adicionar vigilante.',
            'errors' => $this->convocatoriaModel->errors()
        ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    /**
     * API: Remover vigilante de uma sala
     */
    public function removerVigilante($convocatoriaId)
    {
        $convocatoria = $this->convocatoriaModel->find($convocatoriaId);

        if (!$convocatoria) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Convocatória não encontrada.'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        if ($this->convocatoriaModel->delete($convocatoriaId)) {
            // Notificar o professor que a convocatória foi cancelada
            try {
                $sessao = $this->sessaoExameModel->find($convocatoria['sessao_exame_id']);
                $exame  = $sessao ? $this->exameModel->find($sessao['exame_id']) : null;
                $nomeProva = $exame['nome_prova'] ?? 'exame';
                $dataExame = isset($sessao['data_exame']) ? date('d/m/Y', strtotime($sessao['data_exame'])) : '';
                criar_notificacao(
                    (int) $convocatoria['user_id'],
                    'convocatorias',
                    'danger',
                    'Convocatória cancelada',
                    'A sua convocatória como ' . ($convocatoria['funcao'] ?? 'vigilante') . ' para o exame "' . $nomeProva . '"' . ($dataExame ? ' (' . $dataExame . ')' : '') . ' foi cancelada.',
                    base_url('dashboard')
                );
            } catch (\Exception $e) {
                log_message('warning', 'Erro ao notificar professor sobre cancelamento de convocatória: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Vigilante removido com sucesso.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao remover vigilante.'
        ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    /**
     * API: Buscar professores disponíveis (não convocados para esta sessão)
     */
    public function getProfessoresDisponiveis($sessaoExameId)
    {
        // IDs dos professores já convocados
        $convocados = $this->convocatoriaModel
            ->select('user_id')
            ->where('sessao_exame_id', $sessaoExameId)
            ->where('funcao', 'Vigilante')
            ->findAll();

        $idsConvocados = array_column($convocados, 'user_id');

        // Buscar professores não convocados
        $builder = $this->userModel
            ->select('user.id, user.name, user.email')
            ->where('user.status', 1);

        if (!empty($idsConvocados)) {
            $builder->whereNotIn('user.id', $idsConvocados);
        }

        $professoresDisponiveis = $builder->orderBy('user.name', 'ASC')->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $professoresDisponiveis
        ]);
    }

    /**
     * API: Confirmar presença do professor na convocatória
     */
    public function confirmarPresenca()
    {
        $data = $this->request->getJSON(true);
        $convocatoriaId = $data['convocatoria_id'] ?? null;

        if (!$convocatoriaId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID da convocatória não fornecido.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $convocatoria = $this->convocatoriaModel->find($convocatoriaId);

        if (!$convocatoria) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Convocatória não encontrada.'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Atualizar presença
        $updateData = [
            'presenca' => 1,
            'estado_confirmacao' => 'Confirmado',
            'data_confirmacao' => date('Y-m-d H:i:s')
        ];

        if ($this->convocatoriaModel->update($convocatoriaId, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Presença confirmada com sucesso!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao confirmar presença.'
        ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }
}
