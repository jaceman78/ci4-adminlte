<?php

namespace App\Controllers;

use App\Models\SessaoExameSalaModel;
use App\Models\SessaoExameModel;
use App\Models\SalasModel;
use App\Models\ConvocatoriaModel;
use App\Models\ExameModel;
use CodeIgniter\HTTP\ResponseInterface;

class SessaoExameSalaController extends BaseController
{
    protected $sessaoExameSalaModel;
    protected $sessaoExameModel;
    protected $salasModel;
    protected $convocatoriaModel;
    protected $exameModel;

    public function __construct()
    {
        $this->sessaoExameSalaModel = new SessaoExameSalaModel();
        $this->sessaoExameModel = new SessaoExameModel();
        $this->salasModel = new SalasModel();
        $this->convocatoriaModel = new ConvocatoriaModel();
        $this->exameModel = new ExameModel();
    }

    /**
     * Página de alocação de salas para uma sessão de exame
     */
    public function alocarSalas($sessaoExameId)
    {
        // Verificar se sessão existe
        $sessao = $this->sessaoExameModel->find($sessaoExameId);
        
        if (!$sessao) {
            return redirect()->to('/sessoes-exame')->with('error', 'Sessão de exame não encontrada.');
        }

        // Buscar informações completas da sessão
        $sessaoCompleta = $this->sessaoExameModel->getSessaoComExame($sessaoExameId);
        
        // Buscar salas já alocadas
        $salasAlocadas = $this->sessaoExameSalaModel->getSalasComEstatisticas($sessaoExameId);
        
        // Buscar salas disponíveis com informação da escola
        $db = \Config\Database::connect();
        $salasDisponiveis = $db->table('salas')
            ->select('salas.id, salas.codigo_sala, salas.escola_id, escolas.nome as escola_nome')
            ->join('escolas', 'escolas.id = salas.escola_id')
            ->where('salas.id NOT IN (SELECT sala_id FROM sessao_exame_sala WHERE sessao_exame_id = ' . (int)$sessaoExameId . ' AND deleted_at IS NULL)', null, false)
            ->orderBy('escolas.nome', 'ASC')
            ->orderBy('salas.codigo_sala', 'ASC')
            ->get()
            ->getResultArray();

        // Calcular totais
        // Total de alunos INSCRITOS na sessão (não alocados!)
        $totalAlunosInscritos = $sessaoCompleta['num_alunos'] ?? 0;
        $totalAlunosAlocados = $this->sessaoExameSalaModel->getTotalAlunosSessao($sessaoExameId);
        $totalVigilantesNecessarios = $this->sessaoExameSalaModel->getTotalVigilantesNecessarios($sessaoExameId);

        // Determinar se é tipo especial
        $isTipoEspecial = in_array($sessaoCompleta['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC', 'Estrutura de Apoio', 'Verificação de Materiais']);

        $data = [
            'title' => 'Alocar Salas - Sessão de Exame',
            'sessao' => $sessaoCompleta,
            'salasAlocadas' => $salasAlocadas,
            'salasDisponiveis' => $salasDisponiveis,
            'totalAlunosInscritos' => $totalAlunosInscritos,
            'totalAlunosAlocados' => $totalAlunosAlocados,
            'totalVigilantesNecessarios' => $totalVigilantesNecessarios,
            'isTipoEspecial' => $isTipoEspecial,
        ];

        return view('sessoes_exame/alocar_salas', $data);
    }

    /**
     * DataTable para salas alocadas
     */
    public function getDataTable()
    {
        $request = $this->request->getPost();
        $sessaoExameId = $request['sessao_exame_id'] ?? null;

        if (!$sessaoExameId) {
            return $this->response->setJSON([
                'draw' => intval($request['draw'] ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        // Buscar informações da sessão (incluindo tipo de exame)
        $sessao = $this->sessaoExameModel->find($sessaoExameId);
        $exame = $this->exameModel->find($sessao['exame_id']);
        // Sessões especiais não requerem validação de alunos
        $semValidacaoAlunos = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC', 'Estrutura de Apoio', 'Verificação de Materiais']));
        $isSuplentes = ($exame && $exame['tipo_prova'] === 'Suplentes');
        $isTipoEspecial = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC', 'Estrutura de Apoio', 'Verificação de Materiais']));

        // Use db->table() directly to avoid Model field filtering issues with JOINs
        $db = \Config\Database::connect();
        $builder = $db->table('sessao_exame_sala')
            ->select('sessao_exame_sala.*')
            ->select('salas.codigo_sala as sala_nome')
            ->select('escolas.nome as escola_nome')
            ->select('(SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_sala_id = sessao_exame_sala.id AND deleted_at IS NULL) as vigilantes_alocados')
            ->join('salas', 'salas.id = sessao_exame_sala.sala_id')
            ->join('escolas', 'escolas.id = salas.escola_id', 'left')
            ->where('sessao_exame_sala.sessao_exame_id', $sessaoExameId)
            ->where('sessao_exame_sala.deleted_at', null);

        // Search
        if (!empty($request['search']['value'])) {
            $search = $request['search']['value'];
            $builder->groupStart()
                ->like('salas.codigo_sala', $search)
                ->orLike('escolas.nome', $search)
                ->orLike('sessao_exame_sala.observacoes', $search)
                ->groupEnd();
        }

        // Count total records
        $recordsTotal = $this->sessaoExameSalaModel->where('sessao_exame_id', $sessaoExameId)->countAllResults();
        
        // Count filtered records
        $recordsFiltered = $builder->countAllResults(false);

        // Order
        $orderColumnIndex = $request['order'][0]['column'] ?? 0;
        $orderDir = $request['order'][0]['dir'] ?? 'asc';
        
        // Map column index to actual database column
        $orderColumn = 'escolas.nome'; // default
        switch ($orderColumnIndex) {
            case 0: $orderColumn = 'escolas.nome'; break;
            case 1: $orderColumn = 'salas.codigo_sala'; break;
            case 2: $orderColumn = 'sessao_exame_sala.num_alunos_sala'; break;
            case 3: $orderColumn = 'sessao_exame_sala.vigilantes_necessarios'; break;
            case 7: $orderColumn = 'sessao_exame_sala.observacoes'; break;
            // Columns 4, 5, 6, 8 are computed/action columns - default to escola
        }
        
        $builder->orderBy($orderColumn, $orderDir);

        // Pagination
        $start = intval($request['start'] ?? 0);
        $length = intval($request['length'] ?? 10);
        $builder->limit($length, $start);

        $salas = $builder->get()->getResultArray();

        // Format data
        $data = [];
        foreach ($salas as $sala) {
            if ($isTipoEspecial) {
                // Para sessões especiais: não mostrar informações de vigilantes necessários
                $numPessoas = $sala['vigilantes_alocados'];
                
                // Definir labels conforme o tipo
                $labelSala = 'Sala';
                $labelPessoas = 'Pessoas';
                $labelBotao = 'Convocar';
                
                if ($exame['tipo_prova'] === 'Suplentes') {
                    $labelSala = 'Sala de espera';
                    $labelPessoas = 'suplentes';
                    $labelBotao = 'Convocar Suplentes';
                } elseif ($exame['tipo_prova'] === 'Verificacao Calculadoras') {
                    $labelSala = 'Sala de Verificação';
                    $labelPessoas = 'professores';
                    $labelBotao = 'Convocar Professores';
                } elseif ($exame['tipo_prova'] === 'Apoio TIC') {
                    $labelSala = 'Sala de Apoio TIC';
                    $labelPessoas = 'técnicos TIC';
                    $labelBotao = 'Convocar Equipa TIC';
                } elseif ($exame['tipo_prova'] === 'Estrutura de Apoio') {
                    $labelSala = 'Sala de Estrutura de Apoio';
                    $labelPessoas = 'elementos';
                    $labelBotao = 'Convocar Estrutura de Apoio';
                } elseif ($exame['tipo_prova'] === 'Verificação de Materiais') {
                    $labelSala = 'Sala de Verificação';
                    $labelPessoas = 'professores';
                    $labelBotao = 'Convocar Professores';
                }
                
                $statusBadge = '<span class="badge bg-info"><i class="bi bi-people"></i> ' . $numPessoas . ' ' . $labelPessoas . '</span>';
                
                $acoes = '
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-primary btn-editar" 
                            data-id="' . $sala['id'] . '"
                            title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="' . base_url('convocatorias/criar/' . $sala['sessao_exame_id']) . '" 
                            class="btn btn-success"
                            title="' . $labelBotao . '">
                            <i class="bi bi-person-plus"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-eliminar" 
                            data-id="' . $sala['id'] . '"
                            data-pessoas="' . $numPessoas . '"
                            data-label="' . $labelPessoas . '"
                            title="Remover">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ';
                
                $data[] = [
                    esc($sala['escola_nome'] ?? '—'),
                    $sala['sala_nome'],
                    '<em class="text-muted">' . $labelSala . '</em>',
                    '<em class="text-muted">—</em>',
                    $numPessoas,
                    '<em class="text-muted">—</em>',
                    $statusBadge,
                    $sala['observacoes'] ?? '<em class="text-muted">—</em>',
                    $acoes
                ];
            } else {
                // Para exames normais: mostrar informações completas
                $vigilantesEmFalta = max(0, $sala['vigilantes_necessarios'] - $sala['vigilantes_alocados']);
                
                $statusBadge = '';
                if ($vigilantesEmFalta == 0) {
                    $statusBadge = '<span class="badge bg-success"><i class="bi bi-check-circle"></i> Completo</span>';
                } elseif ($sala['vigilantes_alocados'] > 0) {
                    $statusBadge = '<span class="badge bg-warning"><i class="bi bi-exclamation-triangle"></i> Parcial</span>';
                } else {
                    $statusBadge = '<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Sem Vigilantes</span>';
                }

                $acoes = '
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-primary btn-editar" 
                            data-id="' . $sala['id'] . '"
                            title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="' . base_url('convocatorias/criar/' . $sala['sessao_exame_id']) . '" 
                            class="btn btn-success"
                            title="Convocar Vigilantes">
                            <i class="bi bi-person-plus"></i>
                        </a>
                        <button type="button" class="btn btn-danger btn-eliminar" 
                            data-id="' . $sala['id'] . '"
                            data-professores="' . $sala['vigilantes_alocados'] . '"
                            title="Remover">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ';

                $data[] = [
                    esc($sala['escola_nome'] ?? '—'),
                    $sala['sala_nome'],
                    number_format($sala['num_alunos_sala'], 0, ',', '.'),
                    '<strong>' . $sala['vigilantes_necessarios'] . '</strong>',
                    $sala['vigilantes_alocados'] . ' / ' . $sala['vigilantes_necessarios'],
                    $vigilantesEmFalta,
                    $statusBadge,
                    $sala['observacoes'] ?? '<em class="text-muted">—</em>',
                    $acoes
                ];
            }
        }

        return $this->response->setJSON([
            'draw' => intval($request['draw'] ?? 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    /**
     * Buscar dados de uma sala específica (para edição)
     */
    public function get($id)
    {
        // Buscar com informação da sala física (escola + código)
        $db = \Config\Database::connect();
        $sala = $db->table('sessao_exame_sala ses')
            ->select('ses.*, salas.codigo_sala, salas.escola_id, escolas.nome as escola_nome')
            ->join('salas', 'salas.id = ses.sala_id')
            ->join('escolas', 'escolas.id = salas.escola_id')
            ->where('ses.id', $id)
            ->get()
            ->getRowArray();

        if (!$sala) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sala não encontrada.'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $sala
        ]);
    }

    /**
     * Criar nova alocação de sala
     */
    public function store()
    {
        $data = $this->request->getJSON(true);

        // Validações personalizadas
        $sessaoExameId = $data['sessao_exame_id'] ?? null;
        $salaId = $data['sala_id'] ?? null;
        $numAlunos = $data['num_alunos_sala'] ?? 0;

        // Verificar se sala já está alocada (apenas registos ativos)
        if ($this->sessaoExameSalaModel->salaJaAlocada($sessaoExameId, $salaId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Esta sala já está alocada a esta sessão de exame.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Verificar se existe um registo soft-deleted com os mesmos dados
        $registoApagado = $this->sessaoExameSalaModel
            ->onlyDeleted()
            ->where([
                'sessao_exame_id' => $sessaoExameId,
                'sala_id' => $salaId
            ])
            ->first();

        // Verificar se não ultrapassa o número total de alunos inscritos
        $sessaoExame = $this->sessaoExameModel->find($sessaoExameId);
        if (!$sessaoExame) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão de exame não encontrada.'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // NOVA VALIDAÇÃO: Verificar se sala está ocupada nesse dia e período (manhã/tarde)
        $db = \Config\Database::connect();
        $dataExame = $sessaoExame['data_exame'];
        $horaExame = $sessaoExame['hora_exame'];
        
        // Determinar período (manhã: antes de 14:00, tarde: 14:00 ou depois)
        $horaInt = (int)date('H', strtotime($horaExame));
        $periodoAtual = $horaInt < 14 ? 'manha' : 'tarde';
        
        // Query para verificar conflitos de sala no mesmo dia e período
        $query = $db->query("
            SELECT 
                se.id,
                e.codigo_prova,
                e.nome_prova,
                e.tipo_prova,
                se.data_exame,
                se.hora_exame,
                s.codigo_sala
            FROM sessao_exame_sala ses
            INNER JOIN sessao_exame se ON se.id = ses.sessao_exame_id
            INNER JOIN exame e ON e.id = se.exame_id
            INNER JOIN salas s ON s.id = ses.sala_id
            WHERE ses.sala_id = ?
            AND se.data_exame = ?
            AND se.id != ?
            AND ses.deleted_at IS NULL
            AND (
                (HOUR(se.hora_exame) < 14 AND ? = 'manha') OR
                (HOUR(se.hora_exame) >= 14 AND ? = 'tarde')
            )
        ", [$salaId, $dataExame, $sessaoExameId, $periodoAtual, $periodoAtual]);
        
        $conflito = $query->getRowArray();
        
        if ($conflito) {
            $periodoTexto = $periodoAtual === 'manha' ? 'manhã' : 'tarde';
            $dataFormatada = date('d/m/Y', strtotime($conflito['data_exame']));
            $horaFormatada = date('H:i', strtotime($conflito['hora_exame']));
            
            return $this->response->setJSON([
                'success' => false,
                'message' => "A sala {$conflito['codigo_sala']} já está ocupada no período da {$periodoTexto} do dia {$dataFormatada} pelo exame: {$conflito['codigo_prova']} - {$conflito['nome_prova']} ({$conflito['tipo_prova']}) às {$horaFormatada}."
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Buscar tipo de exame para validação especial
        $exame = $this->exameModel->find($sessaoExame['exame_id']);
        // Sessões especiais não requerem validação de alunos
        $semValidacaoAlunos = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC', 'Estrutura de Apoio', 'Verificação de Materiais']));

        // Para sessões especiais, não validar número de alunos
        if (!$semValidacaoAlunos) {
            $totalAlunosInscritos = $sessaoExame['num_alunos'] ?? 0;
            $totalAlunosJaAlocados = $this->sessaoExameSalaModel->getTotalAlunosAlocados($sessaoExameId);
            $totalAposAlocacao = $totalAlunosJaAlocados + $numAlunos;

            if ($totalAposAlocacao > $totalAlunosInscritos) {
                $alunosRestantes = max(0, $totalAlunosInscritos - $totalAlunosJaAlocados);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Não é possível alocar {$numAlunos} alunos. Restam apenas {$alunosRestantes} alunos para alocar (Total inscritos: {$totalAlunosInscritos}, Já alocados: {$totalAlunosJaAlocados})."
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
        }

        // Se existe um registo soft-deleted, restaura-lo e atualizar
        if ($registoApagado) {
            // Restaurar o registo usando builder para atualizar deleted_at
            $builder = $this->sessaoExameSalaModel->builder();
            $builder->where('id', $registoApagado['id']);
            $builder->set($data);
            $builder->set('deleted_at', null);
            $builder->set('updated_at', date('Y-m-d H:i:s'));
            
            if ($builder->update()) {
                log_activity(
                    'exames',
                    'create',
                    $registoApagado['id'],
                    'Sala ID ' . $salaId . ' realocada à sessão de exame #' . $sessaoExameId . ' (' . $numAlunos . ' alunos)',
                    null,
                    $data
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sala alocada com sucesso.'
                ]);
            }
        } else {
            // Inserir novo registo
            $newId = $this->sessaoExameSalaModel->insert($data);
            if ($newId) {
                log_activity(
                    'exames',
                    'create',
                    $newId,
                    'Sala ID ' . $salaId . ' alocada à sessão de exame #' . $sessaoExameId . ' (' . $numAlunos . ' alunos)',
                    null,
                    $data
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sala alocada com sucesso.'
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao alocar sala.',
            'errors' => $this->sessaoExameSalaModel->errors()
        ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    /**
     * Atualizar alocação de sala
     */
    public function update($id)
    {
        $data = $this->request->getJSON(true);

        // Verificar se existe e buscar registro atual
        $registoAtual = $this->sessaoExameSalaModel->find($id);
        if (!$registoAtual) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sala não encontrada.'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Verificar se sala já está alocada (excluindo o próprio registro)
        $sessaoExameId = $data['sessao_exame_id'] ?? null;
        $salaId = $data['sala_id'] ?? null;
        $salaIdAnterior = $registoAtual['sala_id'];
        
        if ($this->sessaoExameSalaModel->salaJaAlocada($sessaoExameId, $salaId, $id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Esta sala já está alocada a esta sessão de exame.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Verificar se não ultrapassa o número total de alunos inscritos
        $numAlunos = $data['num_alunos_sala'] ?? 0;
        $sessaoExame = $this->sessaoExameModel->find($sessaoExameId);
        
        if (!$sessaoExame) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão de exame não encontrada.'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // VALIDAÇÃO: Verificar se sala está ocupada nesse dia e período (se sala mudou)
        if ($salaId != $salaIdAnterior) {
            $db = \Config\Database::connect();
            $dataExame = $sessaoExame['data_exame'];
            $horaExame = $sessaoExame['hora_exame'];
            
            // Determinar período (manhã: antes de 14:00, tarde: 14:00 ou depois)
            $horaInt = (int)date('H', strtotime($horaExame));
            $periodoAtual = $horaInt < 14 ? 'manha' : 'tarde';
            
            // Query para verificar conflitos de sala no mesmo dia e período
            $query = $db->query("
                SELECT 
                    se.id,
                    e.codigo_prova,
                    e.nome_prova,
                    e.tipo_prova,
                    se.data_exame,
                    se.hora_exame,
                    s.codigo_sala
                FROM sessao_exame_sala ses
                INNER JOIN sessao_exame se ON se.id = ses.sessao_exame_id
                INNER JOIN exame e ON e.id = se.exame_id
                INNER JOIN salas s ON s.id = ses.sala_id
                WHERE ses.sala_id = ?
                AND se.data_exame = ?
                AND se.id != ?
                AND ses.deleted_at IS NULL
                AND (
                    (HOUR(se.hora_exame) < 14 AND ? = 'manha') OR
                    (HOUR(se.hora_exame) >= 14 AND ? = 'tarde')
                )
            ", [$salaId, $dataExame, $sessaoExameId, $periodoAtual, $periodoAtual]);
            
            $conflito = $query->getRowArray();
            
            if ($conflito) {
                $periodoTexto = $periodoAtual === 'manha' ? 'manhã' : 'tarde';
                $dataFormatada = date('d/m/Y', strtotime($conflito['data_exame']));
                $horaFormatada = date('H:i', strtotime($conflito['hora_exame']));
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "A sala {$conflito['codigo_sala']} já está ocupada no período da {$periodoTexto} do dia {$dataFormatada} pelo exame: {$conflito['codigo_prova']} - {$conflito['nome_prova']} ({$conflito['tipo_prova']}) às {$horaFormatada}."
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
        }

        // Buscar tipo de exame para validação especial
        $exame = $this->exameModel->find($sessaoExame['exame_id']);
        // Sessões especiais não requerem validação de alunos
        $semValidacaoAlunos = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC', 'Estrutura de Apoio', 'Verificação de Materiais']));

        // Para sessões especiais, ignorar limite de alunos
        if (!$semValidacaoAlunos) {
            $totalAlunosInscritos = $sessaoExame['num_alunos'] ?? 0;
            // Obter total já alocado, excluindo o registro atual que está sendo editado
            $totalAlunosJaAlocados = $this->sessaoExameSalaModel->getTotalAlunosAlocados($sessaoExameId, $id);
            $totalAposAlocacao = $totalAlunosJaAlocados + $numAlunos;

            if ($totalAposAlocacao > $totalAlunosInscritos) {
                $alunosRestantes = max(0, $totalAlunosInscritos - $totalAlunosJaAlocados);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Não é possível alocar {$numAlunos} alunos. Restam apenas {$alunosRestantes} alunos para alocar (Total inscritos: {$totalAlunosInscritos}, Já alocados: {$totalAlunosJaAlocados})."
                ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
            }
        }

        // Atualizar sessão_exame_sala
        if ($this->sessaoExameSalaModel->update($id, $data)) {
            // Se a sala mudou, atualizar as convocatórias associadas
            if ($salaId != $salaIdAnterior) {
                $db = \Config\Database::connect();
                
                // Atualizar todas as convocatórias que apontavam para esta sessao_exame_sala
                // Importante: As convocatórias têm sessao_exame_sala_id que aponta para o ID da alocação
                // Não precisamos mudar o sessao_exame_sala_id, pois esse ID não muda
                // A sala já foi atualizada no registo sessao_exame_sala acima
                
                // Log para auditoria
                log_message('info', "Sala alterada de {$salaIdAnterior} para {$salaId} na sessao_exame_sala ID {$id}");
                
                // Contar convocatórias afetadas (para informar o usuário)
                $numConvocatorias = $this->convocatoriaModel
                    ->where('sessao_exame_sala_id', $id)
                    ->countAllResults(false); // false para não resetar o query
                
                if ($numConvocatorias > 0) {
                    log_message('info', "{$numConvocatorias} convocatórias foram automaticamente reatribuídas à nova sala.");
                }
            }

            log_activity(
                'exames',
                'update',
                $id,
                'Alocação de sala #' . $id . ' atualizada na sessão de exame #' . $sessaoExameId . ($salaId != $salaIdAnterior ? ' (sala alterada de ID ' . $salaIdAnterior . ' para ' . $salaId . ')' : ''),
                $registoAtual,
                $data
            );
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sala atualizada com sucesso.' . ($salaId != $salaIdAnterior && ($numConvocatorias ?? 0) > 0 ? " {$numConvocatorias} vigilante(s) foram automaticamente reatribuídos à nova sala." : '')
            ]);
        }

        // Obter erros de validação
        $errors = $this->sessaoExameSalaModel->errors();
        $errorMessage = 'Erro ao atualizar sala.';
        
        if (!empty($errors)) {
            // Se houver erros de validação, mostrar o primeiro
            $errorMessage = is_array($errors) ? reset($errors) : $errors;
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => $errorMessage,
            'errors' => $errors
        ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    /**
     * Eliminar alocação de sala
     */
    public function delete($id)
    {
        $sala = $this->sessaoExameSalaModel->find($id);

        if (!$sala) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sala não encontrada.'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Verificar se deve forçar a eliminação (remover convocatórias antes)
        $forceDelete = $this->request->getPost('force_delete') === 'true';

        // Verificar se tem convocatórias associadas
        $convocatorias = $this->convocatoriaModel->where('sessao_exame_sala_id', $id)->countAllResults();
        
        if ($convocatorias > 0 && $forceDelete) {
            // Remover todas as convocatórias associadas a esta sala
            $this->convocatoriaModel->where('sessao_exame_sala_id', $id)->delete();
            $msgProfessores = $convocatorias === 1 ? 'professor foi removido' : 'professores foram removidos';
            $mensagemExtra = ' ' . $convocatorias . ' ' . $msgProfessores . ' da sala.';
        } elseif ($convocatorias > 0) {
            // Não deve acontecer com a nova lógica, mas mantém como fallback
            $msgProfessores = $convocatorias === 1 ? 'professor' : 'professores';
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não é possível remover esta sala porque existem ' . $convocatorias . ' ' . $msgProfessores . ' alocado(s).'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        } else {
            $mensagemExtra = '';
        }

        if ($this->sessaoExameSalaModel->delete($id)) {
            log_activity(
                'exames',
                'delete',
                $id,
                'Alocação de sala #' . $id . ' removida da sessão de exame #' . $sala['sessao_exame_id'] . ($convocatorias > 0 ? ' (' . $convocatorias . ' convocatórias removidas)' : ''),
                $sala,
                null,
                'warning'
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sala removida com sucesso.' . $mensagemExtra
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao remover sala.'
        ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * API: Buscar salas disponíveis para alocação
     */
    public function getSalasDisponiveis()
    {
        $sessaoExameId = $this->request->getGet('sessao_exame_id');
        $excludeAlocacaoId = $this->request->getGet('exclude_alocacao_id'); // Para edição

        if (!$sessaoExameId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID da sessão de exame é obrigatório.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Buscar informações da sessão para validar disponibilidade por horário
        $sessaoExame = $this->sessaoExameModel->find($sessaoExameId);
        if (!$sessaoExame) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sessão de exame não encontrada.'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        $dataExame = $sessaoExame['data_exame'];
        $horaExame = $sessaoExame['hora_exame'];
        $horaInt = (int)date('H', strtotime($horaExame));
        $periodoAtual = $horaInt < 14 ? 'manha' : 'tarde';

        // Buscar salas disponíveis com informação da escola
        $db = \Config\Database::connect();
        
        // Query base: salas não alocadas para esta sessão (excluindo a alocação sendo editada)
        $subQueryAlocadas = "(SELECT sala_id FROM sessao_exame_sala WHERE sessao_exame_id = " . (int)$sessaoExameId . " AND deleted_at IS NULL";
        if ($excludeAlocacaoId) {
            $subQueryAlocadas .= " AND id != " . (int)$excludeAlocacaoId;
        }
        $subQueryAlocadas .= ")";

        // Query para salas ocupadas no mesmo dia e período
        $subQueryOcupadas = "(SELECT DISTINCT ses.sala_id 
            FROM sessao_exame_sala ses
            INNER JOIN sessao_exame se ON se.id = ses.sessao_exame_id
            WHERE se.data_exame = '{$dataExame}'
            AND se.id != {$sessaoExameId}
            AND ses.deleted_at IS NULL
            AND ((HOUR(se.hora_exame) < 14 AND '{$periodoAtual}' = 'manha') OR (HOUR(se.hora_exame) >= 14 AND '{$periodoAtual}' = 'tarde')))";

        $salas = $db->table('salas')
            ->select('salas.id, salas.codigo_sala, salas.escola_id, escolas.nome as escola_nome')
            ->join('escolas', 'escolas.id = salas.escola_id')
            ->where("salas.id NOT IN {$subQueryAlocadas}", null, false)
            ->where("salas.id NOT IN {$subQueryOcupadas}", null, false)
            ->orderBy('escolas.nome', 'ASC')
            ->orderBy('salas.codigo_sala', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data' => $salas
        ]);
    }

    /**
     * API: Estatísticas de alocação de uma sessão
     */
    public function getEstatisticas($sessaoExameId)
    {
        $totalSalas = $this->sessaoExameSalaModel->where('sessao_exame_id', $sessaoExameId)->countAllResults();
        $totalAlunos = $this->sessaoExameSalaModel->getTotalAlunosSessao($sessaoExameId);
        $totalVigilantesNecessarios = $this->sessaoExameSalaModel->getTotalVigilantesNecessarios($sessaoExameId);

        // Contar vigilantes já alocados
        $db = \Config\Database::connect();
        $vigilantesAlocados = $db->table('convocatoria c')
            ->join('sessao_exame_sala ses', 'ses.id = c.sessao_exame_sala_id')
            ->where('ses.sessao_exame_id', $sessaoExameId)
            ->where('c.funcao', 'Vigilante')
            ->where('c.deleted_at IS NULL')
            ->countAllResults();

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'total_salas' => $totalSalas,
                'total_alunos' => $totalAlunos,
                'vigilantes_necessarios' => $totalVigilantesNecessarios,
                'vigilantes_alocados' => $vigilantesAlocados,
                'vigilantes_em_falta' => max(0, $totalVigilantesNecessarios - $vigilantesAlocados)
            ]
        ]);
    }
}
