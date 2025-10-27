<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EquipamentosModel;
use App\Models\TipoEquipamentosModel;
use App\Models\SalasModel;
use App\Models\EquipamentosSalaModel;
use App\Models\TicketsModel;
use CodeIgniter\API\ResponseTrait;

class EquipamentosController extends BaseController
{
    use ResponseTrait;

    protected $equipamentosModel;
    protected $tipoEquipamentosModel;
    protected $salasModel;
    protected $equipamentosSalaModel;
    protected $ticketsModel;

    public function __construct()
    {
        $this->equipamentosModel = new EquipamentosModel();
        $this->tipoEquipamentosModel = new TipoEquipamentosModel();
        $this->salasModel = new SalasModel();
        $this->equipamentosSalaModel = new EquipamentosSalaModel();
        $this->ticketsModel = new TicketsModel();
    }

    public function index()
    {
        // Verificar nível de acesso
        $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
        if ($userLevel < 5) {
            return redirect()->to('/tickets/novo')->with('error', 'Acesso negado. Nível de permissão insuficiente.');
        }
        
        $escolasModel = new \App\Models\EscolasModel();
        $data = [
            'page_title' => 'Gestão de Equipamentos',
            'page_subtitle' => 'Listagem e gestão de equipamentos',
            'tipos_equipamento' => $this->tipoEquipamentosModel->findAll(),
            'salas' => $this->salasModel->findAll(),
            'escolas' => $escolasModel->orderBy('nome', 'ASC')->findAll()
        ];
        return view('equipamentos/equipamentos_index', $data);
    }

    public function getDataTable()
    {
        $equipamentos = $this->equipamentosModel
            ->select('equipamentos.*, 
                      tipos_equipamento.nome as tipo_nome,
                      es.sala_id as sala_atual_id,
                      salas.codigo_sala as sala_nome,
                      escolas.nome as escola_nome,
                      es.data_entrada')
            ->join('tipos_equipamento', 'tipos_equipamento.id = equipamentos.tipo_id', 'left')
            ->join('equipamentos_sala es', 'es.equipamento_id = equipamentos.id AND es.data_saida IS NULL', 'left')
            ->join('salas', 'salas.id = es.sala_id', 'left')
            ->join('escolas', 'escolas.id = salas.escola_id', 'left')
            ->orderBy('equipamentos.id', 'DESC')
            ->findAll();

        // Adapta os dados para o DataTable
        $data = [];
        foreach ($equipamentos as $eq) {
            $data[] = [
                'id' => $eq['id'],
                'escola_nome' => $eq['escola_nome'] ?? '<span class="text-muted">Sem atribuição</span>',
                'sala_nome' => $eq['sala_nome'] ?? '<span class="text-muted">Sem atribuição</span>',
                'sala_id' => $eq['sala_atual_id'] ?? null,
                'tipo_nome' => $eq['tipo_nome'] ?? '',
                'marca' => $eq['marca'] ?? '',
                'modelo' => $eq['modelo'] ?? '',
                'marca_modelo' => trim(($eq['marca'] ?? '') . ' ' . ($eq['modelo'] ?? '')),
                'numero_serie' => $eq['numero_serie'] ?? '',
                'estado' => $eq['estado'],
                'observacoes' => $eq['observacoes'] ?? '',
                'tem_sala' => !empty($eq['sala_atual_id'])
            ];
        }

        return $this->respond(['data' => $data]);
    }

    public function all()
    {
        $equipamentos = $this->equipamentosModel
            ->select('equipamentos.id, equipamentos.marca, equipamentos.modelo, equipamentos.numero_serie, tipos_equipamento.nome as tipo_nome')
            ->join('tipos_equipamento', 'tipos_equipamento.id = equipamentos.tipo_id', 'left')
            ->orderBy('tipos_equipamento.nome', 'ASC')
            ->orderBy('equipamentos.marca', 'ASC')
            ->findAll();

        return $this->respond($equipamentos);
    }

    public function getEquipamento($id = null)
    {
        $eq = $this->equipamentosModel
            ->select('equipamentos.*, 
                      tipos_equipamento.nome as tipo_nome,
                      es.sala_id as sala_atual_id,
                      salas.codigo_sala as sala_nome')
            ->join('tipos_equipamento', 'tipos_equipamento.id = equipamentos.tipo_id', 'left')
            ->join('equipamentos_sala es', 'es.equipamento_id = equipamentos.id AND es.data_saida IS NULL', 'left')
            ->join('salas', 'salas.id = es.sala_id', 'left')
            ->find($id);

        if ($eq) {
            return $this->respond($eq);
        } else {
            return $this->failNotFound('Equipamento não encontrado.');
        }
    }

    /**
     * @deprecated Use createWithSala() instead
     * Método legado mantido para compatibilidade
     */
    public function create()
    {
        $rules = [
            'tipo_id'        => 'required|is_natural_no_zero',
            'marca'          => 'permit_empty|max_length[100]',
            'modelo'         => 'permit_empty|max_length[100]',
            'numero_serie'   => 'permit_empty|max_length[255]|is_unique[equipamentos.numero_serie]',
            'estado'         => 'required|in_list[ativo,fora_servico,por_atribuir,abate]',
            'observacoes'    => 'permit_empty|max_length[1000]',
            'sala_id'        => 'permit_empty|is_natural_no_zero'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $salaId = $this->request->getPost('sala_id');
        
        $data = [
            'tipo_id'        => $this->request->getPost('tipo_id'),
            'marca'          => $this->request->getPost('marca'),
            'modelo'         => $this->request->getPost('modelo'),
            'numero_serie'   => $this->request->getPost('numero_serie'),
            'estado'         => $this->request->getPost('estado'),
            'observacoes'    => $this->request->getPost('observacoes')
        ];

        $equipamentoId = $this->equipamentosModel->insert($data);
        
        if ($equipamentoId) {
            // Se foi associado a uma sala, criar registo na tabela equipamentos_sala
            if ($salaId) {
                $this->equipamentosSalaModel->insert([
                    'equipamento_id' => $equipamentoId,
                    'sala_id' => $salaId,
                    'data_entrada' => date('Y-m-d H:i:s'),
                    'motivo_movimentacao' => 'Equipamento criado e associado à sala',
                    'user_id' => session()->get('id'),
                    'observacoes' => 'Registo automático ao criar equipamento'
                ]);
                
                log_message('info', "Equipamento {$equipamentoId} criado e associado à sala {$salaId}");
            }
            
            return $this->respondCreated([
                'message' => 'Equipamento criado com sucesso.',
                'id' => $equipamentoId
            ]);
        } else {
            return $this->failServerError('Não foi possível criar o equipamento.');
        }
    }

    /**
     * Atualizar apenas dados do equipamento (não altera sala)
     * Para mudar sala, use editarSala()
     */
    public function update($id = null)
    {
        // Log para debug
        log_message('info', 'Update chamado para equipamento ID: ' . $id);
        log_message('info', 'Dados POST recebidos: ' . json_encode($this->request->getPost()));
        
        $rules = [
            'tipo_id'        => 'required|is_natural_no_zero',
            'marca'          => 'permit_empty|max_length[100]',
            'modelo'         => 'permit_empty|max_length[100]',
            'numero_serie'   => 'permit_empty|max_length[255]|is_unique[equipamentos.numero_serie,id,' . $id . ']',
            'estado'         => 'required|in_list[ativo,fora_servico,por_atribuir,abate]',
            'observacoes'    => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'Validação falhou: ' . json_encode($this->validator->getErrors()));
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $data = [
            'tipo_id'        => $this->request->getPost('tipo_id'),
            'marca'          => $this->request->getPost('marca'),
            'modelo'         => $this->request->getPost('modelo'),
            'numero_serie'   => $this->request->getPost('numero_serie'),
            'estado'         => $this->request->getPost('estado'),
            'observacoes'    => $this->request->getPost('observacoes')
        ];

        log_message('info', 'Dados a atualizar: ' . json_encode($data));

        if ($this->equipamentosModel->update($id, $data)) {
            log_message('info', 'Equipamento atualizado com sucesso');
            return $this->respond(['message' => 'Equipamento atualizado com sucesso.']);
        } else {
            log_message('error', 'Falha ao atualizar equipamento: ' . json_encode($this->equipamentosModel->errors()));
            return $this->failServerError('Não foi possível atualizar o equipamento.');
        }
    }

    public function delete($id = null)
    {
        if ($this->equipamentosModel->delete($id)) {
            return $this->respondDeleted(['message' => 'Equipamento eliminado com sucesso.']);
        } else {
            return $this->failServerError('Não foi possível eliminar o equipamento.');
        }
    }

    public function getStatistics()
    {
        $totalEquipamentos = $this->equipamentosModel->countAllResults();
        $equipamentosPorEstado = $this->equipamentosModel
            ->select('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->findAll();
        $equipamentosPorTipo = $this->equipamentosModel
            ->select('tipo_id, COUNT(*) as total')
            ->groupBy('tipo_id')
            ->findAll();

        return $this->respond([
            'total_equipamentos' => $totalEquipamentos,
            'por_estado'         => $equipamentosPorEstado,
            'por_tipo'           => $equipamentosPorTipo
        ]);
    }
    public function getAll()
    {
        $equipamentos = $this->equipamentosModel->findAll();
        return $this->respond($equipamentos);
    }

    public function getBySala($salaId = null)
    {
        if (!$salaId) {
            return $this->respond([]);
        }

        // Buscar equipamentos atualmente nesta sala via equipamentos_sala
        $equipamentos = $this->equipamentosModel
            ->select('equipamentos.*, tipos_equipamento.nome as tipo_nome')
            ->join('tipos_equipamento', 'tipos_equipamento.id = equipamentos.tipo_id', 'left')
            ->join('equipamentos_sala es', 'es.equipamento_id = equipamentos.id AND es.data_saida IS NULL', 'inner')
            ->where('es.sala_id', $salaId)
            ->where('equipamentos.estado', 'ativo')
            ->findAll();

        return $this->respond($equipamentos);
    }

    /**
     * Criar equipamento com atribuição de sala
     */
    public function createWithSala()
    {
        $rules = [
            'tipo_id'        => 'required|is_natural_no_zero',
            'marca'          => 'permit_empty|max_length[100]',
            'modelo'         => 'permit_empty|max_length[100]',
            'numero_serie'   => 'permit_empty|max_length[255]|is_unique[equipamentos.numero_serie]',
            'estado'         => 'required|in_list[ativo,fora_servico,por_atribuir,abate]',
            'observacoes'    => 'permit_empty|max_length[1000]',
            'sala_id'        => 'permit_empty|is_natural',
            'motivo_movimentacao' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Criar equipamento
            $equipData = [
                'tipo_id'        => $this->request->getPost('tipo_id'),
                'marca'          => $this->request->getPost('marca'),
                'modelo'         => $this->request->getPost('modelo'),
                'numero_serie'   => $this->request->getPost('numero_serie'),
                'estado'         => $this->request->getPost('estado'),
                'observacoes'    => $this->request->getPost('observacoes')
            ];

            $equipamentoId = $this->equipamentosModel->insert($equipData);

            if (!$equipamentoId) {
                throw new \Exception('Erro ao criar equipamento');
            }

            // Se tem sala, criar registro na equipamentos_sala
            $salaId = $this->request->getPost('sala_id');
            if (!empty($salaId)) {
                $this->equipamentosSalaModel->insert([
                    'equipamento_id' => $equipamentoId,
                    'sala_id' => $salaId,
                    'data_entrada' => date('Y-m-d H:i:s'),
                    'motivo_movimentacao' => $this->request->getPost('motivo_movimentacao') ?? 'Novo equipamento',
                    'user_id' => session()->get('user_id')
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->failServerError('Erro ao processar transação');
            }

            return $this->respondCreated([
                'message' => 'Equipamento criado com sucesso!',
                'id' => $equipamentoId
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->failServerError('Erro: ' . $e->getMessage());
        }
    }

    /**
     * Atribuir sala a um equipamento
     */
    public function atribuirSala()
    {
        $equipamentoId = $this->request->getPost('equipamento_id');
        $salaId = $this->request->getPost('sala_id');
        $motivo = $this->request->getPost('motivo_movimentacao');

        if (empty($equipamentoId) || empty($salaId)) {
            return $this->fail('Equipamento e sala são obrigatórios');
        }

        // Verificar se equipamento já tem sala
        $salaAtual = $this->equipamentosSalaModel->getSalaAtual($equipamentoId);
        
        if ($salaAtual) {
            return $this->fail('Equipamento já tem uma sala atribuída. Use "Editar Sala" para trocar.');
        }

        $result = $this->equipamentosSalaModel->atribuirSala(
            $equipamentoId, 
            $salaId, 
            session()->get('user_id')
        );

        if ($result) {
            // Atualizar motivo se fornecido
            if (!empty($motivo)) {
                $this->equipamentosSalaModel->update($result, ['motivo_movimentacao' => $motivo]);
            }

            log_activity(
                session()->get('user_id'), 
                'equipamentos', 
                'atribuir_sala', 
                "Sala atribuída ao equipamento ID: {$equipamentoId}",
                $equipamentoId,
                null,
                ['sala_id' => $salaId, 'motivo' => $motivo]
            );

            return $this->respond(['message' => 'Sala atribuída com sucesso!']);
        }

        return $this->failServerError('Erro ao atribuir sala');
    }

    /**
     * Editar/Mover equipamento para outra sala
     */
    public function editarSala()
    {
        $equipamentoId = $this->request->getPost('equipamento_id');
        $novaSalaId = $this->request->getPost('sala_id');
        $motivo = $this->request->getPost('motivo_movimentacao');
        $forcarMudanca = $this->request->getPost('forcar_mudanca') === 'true';

        if (empty($equipamentoId) || empty($novaSalaId)) {
            return $this->fail('Equipamento e nova sala são obrigatórios');
        }

        // Verificar se há tickets abertos (não finalizados) para este equipamento
        $ticketsAbertos = $this->ticketsModel
            ->where('equipamento_id', $equipamentoId)
            ->whereNotIn('estado', ['reparado', 'anulado'])
            ->findAll();

        if (!empty($ticketsAbertos) && !$forcarMudanca) {
            // Buscar informações da sala atual do ticket
            $salaAtual = $this->salasModel->find($ticketsAbertos[0]['sala_id']);
            $novaSala = $this->salasModel->find($novaSalaId);
            
            return $this->respond([
                'warning' => true,
                'message' => 'Este equipamento tem ' . count($ticketsAbertos) . ' ticket(s) em reparação na sala "' . ($salaAtual['codigo_sala'] ?? 'N/A') . '". Deseja continuar com a mudança para "' . ($novaSala['codigo_sala'] ?? 'N/A') . '"? Os tickets serão atualizados automaticamente.',
                'tickets_count' => count($ticketsAbertos),
                'sala_atual' => $salaAtual['codigo_sala'] ?? 'N/A',
                'sala_nova' => $novaSala['codigo_sala'] ?? 'N/A',
                'equipamento_id' => $equipamentoId
            ], 200);
        }

        // Se forçar mudança, atualizar também os tickets
        if (!empty($ticketsAbertos) && $forcarMudanca) {
            foreach ($ticketsAbertos as $ticket) {
                $this->ticketsModel->update($ticket['id'], ['sala_id' => $novaSalaId]);
                
                // Registrar atividade
                log_activity(
                    session()->get('user_id'), 
                    'tickets', 
                    'atualizar_sala', 
                    "Sala do ticket #{$ticket['id']} atualizada devido à mudança do equipamento",
                    $ticket['id'],
                    ['sala_antiga' => $ticket['sala_id']],
                    ['sala_nova' => $novaSalaId]
                );
            }
        }

        $result = $this->equipamentosSalaModel->moverEquipamento(
            $equipamentoId,
            $novaSalaId,
            $motivo,
            session()->get('user_id')
        );

        if ($result) {
            log_activity(
                session()->get('user_id'), 
                'equipamentos', 
                'mover_sala', 
                "Equipamento ID: {$equipamentoId} movido para nova sala",
                $equipamentoId,
                null,
                ['nova_sala_id' => $novaSalaId, 'motivo' => $motivo]
            );

            $message = 'Equipamento movido com sucesso!';
            if (!empty($ticketsAbertos) && $forcarMudanca) {
                $message .= ' ' . count($ticketsAbertos) . ' ticket(s) atualizado(s).';
            }

            return $this->respond(['message' => $message]);
        }

        return $this->failServerError('Erro ao mover equipamento');
    }

    /**
     * Remover equipamento de sala
     */
    public function removerSala()
    {
        $equipamentoId = $this->request->getPost('equipamento_id');
        $motivo = $this->request->getPost('motivo_movimentacao');

        if (empty($equipamentoId)) {
            return $this->fail('ID do equipamento é obrigatório');
        }

        $result = $this->equipamentosSalaModel->removerDeSala(
            $equipamentoId,
            $motivo,
            session()->get('user_id')
        );

        if ($result) {
            log_activity(
                session()->get('user_id'), 
                'equipamentos', 
                'remover_sala', 
                "Equipamento ID: {$equipamentoId} removido da sala",
                $equipamentoId,
                null,
                ['motivo' => $motivo]
            );

            return $this->respond(['message' => 'Equipamento removido da sala com sucesso!']);
        }

        return $this->failServerError('Erro ao remover equipamento da sala');
    }

    /**
     * Obter histórico de movimentações
     */
    public function getHistorico($equipamentoId = null)
    {
        if (!$equipamentoId) {
            return $this->fail('ID do equipamento é obrigatório');
        }

        $historico = $this->equipamentosSalaModel->getHistoricoEquipamento($equipamentoId);
        return $this->respond($historico);
    }

    /**
     * Obter dados completos do equipamento incluindo sala atual
     */
    public function getEquipamentoCompleto($id = null)
    {
        if (!$id) {
            return $this->failNotFound('ID não fornecido');
        }

        $equipamento = $this->equipamentosModel->find($id);
        
        if (!$equipamento) {
            return $this->failNotFound('Equipamento não encontrado');
        }

        // Buscar sala atual
        $salaAtual = $this->equipamentosSalaModel->getSalaAtual($id);
        
        if ($salaAtual) {
            $sala = $this->salasModel
                ->select('salas.*, escolas.nome as escola_nome, escolas.id as escola_id')
                ->join('escolas', 'escolas.id = salas.escola_id')
                ->find($salaAtual['sala_id']);
            
            $equipamento['sala_atual'] = $sala;
            $equipamento['sala_atual_id'] = $salaAtual['sala_id'];
            $equipamento['escola_id'] = $sala['escola_id'] ?? null;
        } else {
            $equipamento['sala_atual'] = null;
            $equipamento['sala_atual_id'] = null;
            $equipamento['escola_id'] = null;
        }

        return $this->respond($equipamento);
    }
}

