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
        
        // Buscar salas alocadas com estatísticas
        $salasAlocadas = $this->sessaoExameSalaModel->getSalasComEstatisticas($sessaoExameId);

        // Buscar todos os professores (user role = professor ou similar)
        // Assumindo que professores têm role_id específico
        $professores = $this->userModel
            ->select('user.id, user.name, user.email, user.grupo_id, 
                (SELECT COUNT(*) FROM convocatoria 
                 WHERE convocatoria.user_id = user.id 
                 AND convocatoria.funcao = "Vigilante") as total_vigilancias')
            ->where('user.status', 1)
            ->orderBy('user.name', 'ASC')
            ->findAll();

        // Buscar convocatórias já existentes desta sessão
        $convocatoriasExistentes = $this->convocatoriaModel
            ->select('convocatoria.*, user.name as user_nome, sessao_exame_sala.id as sala_id')
            ->join('user', 'user.id = convocatoria.user_id')
            ->join('sessao_exame_sala', 'sessao_exame_sala.id = convocatoria.sessao_exame_sala_id', 'left')
            ->where('convocatoria.sessao_exame_id', $sessaoExameId)
            ->where('convocatoria.funcao', 'Vigilante')
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

        // Verificar se já está convocado para esta sessão
        $jaConvocado = $this->convocatoriaModel
            ->where('sessao_exame_id', $sessaoExameId)
            ->where('user_id', $userId)
            ->where('funcao', 'Vigilante')
            ->first();

        if ($jaConvocado) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Este professor já está convocado para esta sessão de exame.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Verificar tipo de exame para determinar se há limite de vigilantes
        $sessao = $this->sessaoExameModel->find($sessaoExameId);
        $exame = $this->exameModel->find($sessao['exame_id']);
        // Sessões especiais não têm limite de vigilantes
        $semLimite = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC']));

        // Para sessões especiais (suplentes/verificação calculadoras/apoio TIC), não há limite de vigilantes
        if (!$semLimite) {
            // Verificar se a sala já atingiu o número de vigilantes necessários
            $sala = $this->sessaoExameSalaModel->find($sessaoExameSalaId);
            $vigilantesNaSala = $this->convocatoriaModel
                ->where('sessao_exame_sala_id', $sessaoExameSalaId)
                ->where('funcao', 'Vigilante')
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
            'funcao' => 'Vigilante',
            'estado_confirmacao' => 'Pendente'
        ];

        if ($this->convocatoriaModel->insert($convocatoriaData)) {
            // Buscar dados do professor para retornar
            $professor = $this->userModel->find($userId);
            
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
