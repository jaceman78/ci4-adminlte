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
        $isTipoEspecial = in_array($sessaoCompleta['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC']);

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
        $semValidacaoAlunos = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC']));
        $isSuplentes = ($exame && $exame['tipo_prova'] === 'Suplentes');
        $isTipoEspecial = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC']));

        // Use db->table() directly to avoid Model field filtering issues with JOINs
        $db = \Config\Database::connect();
        $builder = $db->table('sessao_exame_sala')
            ->select('sessao_exame_sala.*')
            ->select('salas.codigo_sala as sala_nome')
            ->select('(SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_sala_id = sessao_exame_sala.id AND funcao = "Vigilante" AND deleted_at IS NULL) as vigilantes_alocados')
            ->join('salas', 'salas.id = sessao_exame_sala.sala_id')
            ->where('sessao_exame_sala.sessao_exame_id', $sessaoExameId)
            ->where('sessao_exame_sala.deleted_at', null);

        // Search
        if (!empty($request['search']['value'])) {
            $search = $request['search']['value'];
            $builder->groupStart()
                ->like('salas.codigo_sala', $search)
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
        $orderColumn = 'sessao_exame_sala.id'; // default
        switch ($orderColumnIndex) {
            case 0: $orderColumn = 'sessao_exame_sala.id'; break;
            case 1: $orderColumn = 'salas.codigo_sala'; break;
            case 2: $orderColumn = 'sessao_exame_sala.num_alunos_sala'; break;
            case 3: $orderColumn = 'sessao_exame_sala.vigilantes_necessarios'; break;
            case 7: $orderColumn = 'sessao_exame_sala.observacoes'; break;
            // Columns 4, 5, 6, 8 are computed/action columns - default to id
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
                            title="Remover">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ';
                
                $data[] = [
                    $sala['id'],
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
                            title="Remover">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                ';

                $data[] = [
                    $sala['id'],
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

        // Buscar tipo de exame para validação especial
        $exame = $this->exameModel->find($sessaoExame['exame_id']);
        // Sessões especiais não requerem validação de alunos
        $semValidacaoAlunos = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC']));

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
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sala alocada com sucesso.'
                ]);
            }
        } else {
            // Inserir novo registo
            if ($this->sessaoExameSalaModel->insert($data)) {
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

        // Verificar se existe
        if (!$this->sessaoExameSalaModel->find($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sala não encontrada.'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        // Verificar se sala já está alocada (excluindo o próprio registro)
        $sessaoExameId = $data['sessao_exame_id'] ?? null;
        $salaId = $data['sala_id'] ?? null;
        
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

        // Buscar tipo de exame para validação especial
        $exame = $this->exameModel->find($sessaoExame['exame_id']);
        // Sessões especiais não requerem validação de alunos
        $semValidacaoAlunos = ($exame && in_array($exame['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC']));

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

        // Atualizar
        if ($this->sessaoExameSalaModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sala atualizada com sucesso.'
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

        // Verificar se tem convocatórias associadas
        $convocatorias = $this->convocatoriaModel->where('sessao_exame_sala_id', $id)->countAllResults();
        
        if ($convocatorias > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não é possível remover esta sala pois existem ' . $convocatorias . ' convocatória(s) associada(s). Elimine primeiro as convocatórias.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        if ($this->sessaoExameSalaModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Sala removida com sucesso.'
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

        if (!$sessaoExameId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID da sessão de exame é obrigatório.'
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Buscar salas disponíveis com informação da escola
        $db = \Config\Database::connect();
        $salas = $db->table('salas')
            ->select('salas.id, salas.codigo_sala, salas.escola_id, escolas.nome as escola_nome')
            ->join('escolas', 'escolas.id = salas.escola_id')
            ->where('salas.id NOT IN (SELECT sala_id FROM sessao_exame_sala WHERE sessao_exame_id = ' . (int)$sessaoExameId . ' AND deleted_at IS NULL)', null, false)
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
