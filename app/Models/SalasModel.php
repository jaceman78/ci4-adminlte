<?php

namespace App\Models;

use CodeIgniter\Model;

class SalasModel extends Model
{
    protected $table = 'salas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'escola_id',
        'codigo_sala',
        'descricao',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'escola_id' => 'required|integer|is_not_unique[escolas.id]',
        'codigo_sala' => 'required|max_length[50]'
    ];

    protected $validationMessages = [
        'escola_id' => [
            'required' => 'A escola é obrigatória.',
            'integer' => 'ID da escola deve ser um número.',
            'is_not_unique' => 'A escola selecionada não existe.'
        ],
        'codigo_sala' => [
            'required' => 'O código da sala é obrigatório.',
            'max_length' => 'O código da sala não pode ter mais de 50 caracteres.'
        ]
    ];

    protected $skipValidation = true;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Obter todas as salas com informações da escola
     */
    public function getAllSalasWithEscola($limit = 10, $offset = 0)
    {
        return $this->select('salas.*, escolas.nome as escola_nome')
                    ->join('escolas', 'escolas.id = salas.escola_id')
                    ->orderBy('escolas.nome', 'ASC')
                    ->orderBy('salas.codigo_sala', 'ASC')
                    ->findAll($limit, $offset);
    }

    /**
     * Obter salas de uma escola específica
     */
    public function getSalasByEscola($escolaId, $limit = 10, $offset = 0)
    {
        return $this->select('salas.*, escolas.nome as escola_nome')
                    ->join('escolas', 'escolas.id = salas.escola_id')
                    ->where('salas.escola_id', $escolaId)
                    ->orderBy('salas.codigo_sala', 'ASC')
                    ->findAll($limit, $offset);
    }

    /**
     * Pesquisar salas por código ou nome da escola
     */
    public function searchSalas($search, $escolaId = null)
    {
        $builder = $this->select('salas.*, escolas.nome as escola_nome')
                       ->join('escolas', 'escolas.id = salas.escola_id');

        if ($escolaId) {
            $builder->where('salas.escola_id', $escolaId);
        }

        return $builder->groupStart()
                      ->like('salas.codigo_sala', $search)
                      ->orLike('escolas.nome', $search)
                      ->groupEnd()
                      ->orderBy('escolas.nome', 'ASC')
                      ->orderBy('salas.codigo_sala', 'ASC')
                      ->findAll();
    }

    /**
     * Obter sala por código e escola
     */
    public function getSalaByCodigoAndEscola($codigoSala, $escolaId)
    {
        return $this->where('codigo_sala', $codigoSala)
                    ->where('escola_id', $escolaId)
                    ->first();
    }

    /**
     * Contar salas por escola
     */
    public function countSalasByEscola($escolaId = null)
    {
        if ($escolaId !== null) {
            return $this->where('escola_id', $escolaId)->countAllResults();
        }
        return $this->countAllResults();
    }

    /**
     * Obter estatísticas das salas
     */
    public function getSalasStats($escolaId = null)
    {
        $stats = [];
        
        if ($escolaId) {
            $stats['total'] = $this->where('escola_id', $escolaId)->countAllResults();
            $stats['escola_id'] = $escolaId;
        } else {
            $stats['total'] = $this->countAllResults();
            
            // Estatísticas por escola
            $builder = $this->select('escola_id, COUNT(*) as total_salas, escolas.nome as escola_nome')
                           ->join('escolas', 'escolas.id = salas.escola_id')
                           ->groupBy('escola_id')
                           ->orderBy('escolas.nome', 'ASC');
            
            $stats['por_escola'] = $builder->findAll();
        }
        
        // Reset builder para próximas queries
        $this->builder()->resetQuery();
        
        return $stats;
    }

    /**
     * Obter salas criadas numa data específica
     */
    public function getSalasCreatedOn($date, $escolaId = null)
    {
        $builder = $this->select('salas.*, escolas.nome as escola_nome')
                       ->join('escolas', 'escolas.id = salas.escola_id')
                       ->where('DATE(salas.created_at)', $date);

        if ($escolaId) {
            $builder->where('salas.escola_id', $escolaId);
        }

        return $builder->orderBy('salas.created_at', 'DESC')->findAll();
    }

    /**
     * Obter salas criadas entre datas
     */
    public function getSalasCreatedBetween($startDate, $endDate, $escolaId = null)
    {
        $builder = $this->select('salas.*, escolas.nome as escola_nome')
                       ->join('escolas', 'escolas.id = salas.escola_id')
                       ->where('salas.created_at >=', $startDate)
                       ->where('salas.created_at <=', $endDate);

        if ($escolaId) {
            $builder->where('salas.escola_id', $escolaId);
        }

        return $builder->orderBy('salas.created_at', 'DESC')->findAll();
    }

    /**
     * Obter dados para DataTable com filtros (por escola)
     */
    public function getDataTableData($escolaId, $start = 0, $length = 10, $search = '', $orderColumn = 'id', $orderDir = 'asc')
    {
        $builder = $this->select('salas.*, escolas.nome as escola_nome')
                       ->join('escolas', 'escolas.id = salas.escola_id')
                       ->where('salas.escola_id', $escolaId);
        
        // Aplicar pesquisa
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('salas.codigo_sala', $search)
                   ->orLike('salas.descricao', $search)
                   ->orLike('escolas.nome', $search)
                   ->groupEnd();
        }
        
        // Total de registos filtrados
        $recordsFiltered = $builder->countAllResults(false);
        
        // Aplicar ordenação
        // Mapear colunas da DataTable para colunas reais da base de dados
        $dbOrderColumn = $orderColumn;
        if ($orderColumn === 'escola_nome') {
            $dbOrderColumn = 'escolas.nome';
        } else if ($orderColumn !== 'id') { // 'id' é sempre da tabela salas
            $dbOrderColumn = 'salas.' . $orderColumn;
        }
        $builder->orderBy($dbOrderColumn, $orderDir);

        
        // Aplicar paginação
        $builder->limit((int)$length, (int)$start);
        
        // Obter dados
        $data = $builder->get()->getResultArray();
        
        // Total de registos sem filtro (para esta escola)
        $recordsTotal = $this->where('escola_id', $escolaId)->countAllResults();
        
        return [
            'data' => $data,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ];
    }

    /**
     * Verificar se código da sala existe numa escola (excluindo um ID específico)
     */
    public function codigoExists($codigoSala, $escolaId, $excludeId = null)
    {
        $builder = $this->where('codigo_sala', $codigoSala)
                       ->where('escola_id', $escolaId);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Obter salas ordenadas por código
     */
    public function getSalasOrderedByCodigo($escolaId = null)
    {
        $builder = $this->select('salas.*, escolas.nome as escola_nome')
                       ->join('escolas', 'escolas.id = salas.escola_id');

        if ($escolaId) {
            $builder->where('salas.escola_id', $escolaId);
        }

        return $builder->orderBy('salas.codigo_sala', 'ASC')->findAll();
    }

    /**
     * Obter salas ordenadas por data de criação
     */
    public function getSalasOrderedByDate($order = 'DESC', $escolaId = null)
    {
        $builder = $this->select('salas.*, escolas.nome as escola_nome')
                       ->join('escolas', 'escolas.id = salas.escola_id');

        if ($escolaId) {
            $builder->where('salas.escola_id', $escolaId);
        }

        return $builder->orderBy('salas.created_at', $order)->findAll();
    }

    /**
     * Validar dados antes de inserir/atualizar
     */
    public function validateSalaData($data, $id = null)
    {
        $validation = \Config\Services::validation();
        
        $rules = $this->validationRules;
        
        // Ajustar a regra de validação do código da sala dinamicamente 
        // Verificar unicidade do código dentro da mesma escola
        $codigoRule = 'required|max_length[50]';
        
        // Validação personalizada para unicidade do código na escola
        if (isset($data['escola_id']) && isset($data['codigo_sala'])) {
            $exists = $this->codigoExists($data['codigo_sala'], $data['escola_id'], $id);
            if ($exists) {
                return [
                    'success' => false,
                    'errors' => ['codigo_sala' => 'Já existe uma sala com este código nesta escola.']
                ];
            }
        }
        
        $rules['codigo_sala'] = $codigoRule;

        $validation->setRules($rules);
        
        if (!$validation->run($data)) {
            return [
                'success' => false,
                'errors' => $validation->getErrors()
            ];
        }
        
        return ['success' => true];
    }

    /**
     * Obter lista de salas para dropdown/select (por escola)
     */
    public function getSalasForDropdown($escolaId)
    {
        $salas = $this->select('id, codigo_sala')
                     ->where('escola_id', $escolaId)
                     ->orderBy('codigo_sala', 'ASC')
                     ->findAll();
        
        $dropdown = [];
        foreach ($salas as $sala) {
            $dropdown[$sala['id']] = $sala['codigo_sala'];
        }
        
        return $dropdown;
    }

    /**
     * Pesquisa avançada com múltiplos critérios
     */
    public function advancedSearch($filters = [])
    {
        $builder = $this->select('salas.*, escolas.nome as escola_nome')
                       ->join('escolas', 'escolas.id = salas.escola_id');
        
        if (!empty($filters['escola_id'])) {
            $builder->where('salas.escola_id', $filters['escola_id']);
        }
        
        if (!empty($filters['codigo_sala'])) {
            $builder->like('salas.codigo_sala', $filters['codigo_sala']);
        }
        
        if (!empty($filters['escola_nome'])) {
            $builder->like('escolas.nome', $filters['escola_nome']);
        }
        
        if (!empty($filters['data_inicio'])) {
            $builder->where('salas.created_at >=', $filters['data_inicio']);
        }
        
        if (!empty($filters['data_fim'])) {
            $builder->where('salas.created_at <=', $filters['data_fim']);
        }
        
        return $builder->orderBy('escolas.nome', 'ASC')
                      ->orderBy('salas.codigo_sala', 'ASC')
                      ->findAll();
    }

    /**
     * Obter salas recentes (últimos 30 dias)
     */
    public function getRecentSalas($days = 30, $escolaId = null)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $builder = $this->select('salas.*, escolas.nome as escola_nome')
                       ->join('escolas', 'escolas.id = salas.escola_id')
                       ->where('salas.created_at >=', $date);

        if ($escolaId) {
            $builder->where('salas.escola_id', $escolaId);
        }

        return $builder->orderBy('salas.created_at', 'DESC')->findAll();
    }

    /**
     * Atualizar múltiplas salas
     */
    public function updateMultipleSalas($ids, $data)
    {
        return $this->whereIn('id', $ids)->set($data)->update();
    }

    /**
     * Eliminar múltiplas salas
     */
    public function deleteMultipleSalas($ids)
    {
        return $this->whereIn('id', $ids)->delete();
    }

    /**
     * Eliminar todas as salas de uma escola
     */
    public function deleteSalasByEscola($escolaId)
    {
        return $this->where('escola_id', $escolaId)->delete();
    }

    /**
     * Obter sala com informações da escola
     */
    public function getSalaWithEscola($id)
    {
        return $this->select('salas.*, escolas.nome as escola_nome')
                    ->join('escolas', 'escolas.id = salas.escola_id')
                    ->where('salas.id', $id)
                    ->first();
    }

    /**
     * Verificar se escola existe
     */
    public function escolaExists($escolaId)
    {
        $escolasModel = new \App\Models\EscolasModel();
        return $escolasModel->find($escolaId) !== null;
    }
}