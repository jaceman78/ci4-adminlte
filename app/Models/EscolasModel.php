<?php

namespace App\Models;

use CodeIgniter\Model;

class EscolasModel extends Model
{
    protected $table = 'escolas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nome',
        'morada',
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
        'nome' => 'required|max_length[150]|is_unique[escolas.nome,id,{id}]',
        'morada' => 'permit_empty|max_length[255]'
    ];

    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome da escola é obrigatório.',
            'max_length' => 'O nome da escola não pode ter mais de 150 caracteres.',
            'is_unique' => 'Já existe uma escola com este nome.'
        ],
        'morada' => [
            'max_length' => 'A morada não pode ter mais de 255 caracteres.'
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
     * Obter todas as escolas com paginação
     */
    public function getAllEscolas($limit = 10, $offset = 0)
    {
        return $this->orderBy('nome', 'ASC')
                    ->findAll($limit, $offset);
    }

    /**
     * Pesquisar escolas por nome ou morada
     */
    public function searchEscolas($search)
    {
        return $this->groupStart()
                    ->like('nome', $search)
                    ->orLike('morada', $search)
                    ->groupEnd()
                    ->orderBy('nome', 'ASC')
                    ->findAll();
    }

    /**
     * Obter escola por nome
     */
    public function getEscolaByNome($nome)
    {
        return $this->where('nome', $nome)->first();
    }

    /**
     * Obter escolas por cidade (extraindo da morada)
     */
    public function getEscolasByCidade($cidade)
    {
        return $this->like('morada', $cidade)
                    ->orderBy('nome', 'ASC')
                    ->findAll();
    }

    /**
     * Contar total de escolas
     */
    public function countEscolas()
    {
        return $this->countAllResults();
    }

    /**
     * Obter estatísticas das escolas
     */
    public function getEscolasStats()
    {
        $stats = [];
        $stats['total'] = $this->countAllResults();
        $stats['com_morada'] = $this->where('morada IS NOT NULL')
                                   ->where('morada !=', '')
                                   ->countAllResults();
        $stats['sem_morada'] = $stats['total'] - $stats['com_morada'];
        
        // Reset builder para próximas queries
        $this->builder()->resetQuery();
        
        return $stats;
    }

    /**
     * Obter escolas criadas numa data específica
     */
    public function getEscolasCreatedOn($date)
    {
        return $this->where('DATE(created_at)', $date)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Obter escolas criadas entre datas
     */
    public function getEscolasCreatedBetween($startDate, $endDate)
    {
        return $this->where('created_at >=', $startDate)
                    ->where('created_at <=', $endDate)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Obter dados para DataTable com filtros
     */
    public function getDataTableData($start = 0, $length = 10, $search = '', $orderColumn = 'id', $orderDir = 'asc')
    {
        $builder = $this->builder();
        
        // Aplicar pesquisa se fornecida
        if (!empty($search)) {
            $builder->groupStart()
                   ->like('nome', $search)
                   ->orLike('morada', $search)
                   ->groupEnd();
        }
        
        // Total de registos filtrados
        $recordsFiltered = $builder->countAllResults(false);
        
        // Aplicar ordenação
        $builder->orderBy($orderColumn, $orderDir);
        
        // Aplicar paginação
        $builder->limit($length, $start);
        
        // Obter dados
        $data = $builder->get()->getResultArray();
        
        // Total de registos sem filtro
        $recordsTotal = $this->countAllResults();
        
        return [
            'data' => $data,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ];
    }

    /**
     * Verificar se nome da escola existe (excluindo um ID específico)
     */
    public function nomeExists($nome, $excludeId = null)
    {
        $builder = $this->where('nome', $nome);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Obter escolas ordenadas por nome
     */
    public function getEscolasOrderedByName()
    {
        return $this->orderBy('nome', 'ASC')->findAll();
    }

    /**
     * Obter escolas ordenadas por data de criação
     */
    public function getEscolasOrderedByDate($order = 'DESC')
    {
        return $this->orderBy('created_at', $order)->findAll();
    }

    /**
     * Obter escolas com morada preenchida
     */
    public function getEscolasWithMorada()
    {
        return $this->where('morada IS NOT NULL')
                    ->where('morada !=', '')
                    ->orderBy('nome', 'ASC')
                    ->findAll();
    }

    /**
     * Obter escolas sem morada
     */
    public function getEscolasWithoutMorada()
    {
        return $this->groupStart()
                    ->where('morada IS NULL')
                    ->orWhere('morada', '')
                    ->groupEnd()
                    ->orderBy('nome', 'ASC')
                    ->findAll();
    }

    /**
     * Validar dados antes de inserir/atualizar
     */
    public function validateEscolaData($data, $id = null)
    {
        $validation = \Config\Services::validation();

        $rules = $this->validationRules;

        // Ajustar a regra de validação do nome dinamicamente
        $nomeRule = 'required|max_length[150]';
        if ($id) {
            // Se for uma atualização, ignorar o ID atual na verificação de unicidade
            $nomeRule .= '|is_unique[escolas.nome,id,' . $id . ']';
        } else {
            // Se for uma criação, o nome deve ser único
            $nomeRule .= '|is_unique[escolas.nome]';
        }
        $rules['nome'] = $nomeRule;

        $validation->setRules($rules);

        $this->validation = $validation; // Save validation instance for controller access

        if (!$validation->run($data)) {
            return [
                'success' => false,
                'errors' => $validation->getErrors()
            ];
        }

        return ['success' => true];
    }

    /**
     * Obter lista de escolas para dropdown/select
     */
    public function getEscolasForDropdown()
    {
        $escolas = $this->select('id, nome')
                       ->orderBy('nome', 'ASC')
                       ->findAll();
        
        $dropdown = [];
        foreach ($escolas as $escola) {
            $dropdown[$escola['id']] = $escola['nome'];
        }
        
        return $dropdown;
    }

    /**
     * Pesquisa avançada com múltiplos critérios
     */
    public function advancedSearch($filters = [])
    {
        $builder = $this->builder();
        
        if (!empty($filters['nome'])) {
            $builder->like('nome', $filters['nome']);
        }
        
        if (!empty($filters['morada'])) {
            $builder->like('morada', $filters['morada']);
        }
        
        if (!empty($filters['data_inicio'])) {
            $builder->where('created_at >=', $filters['data_inicio']);
        }
        
        if (!empty($filters['data_fim'])) {
            $builder->where('created_at <=', $filters['data_fim']);
        }
        
        if (isset($filters['tem_morada'])) {
            if ($filters['tem_morada'] == '1') {
                $builder->where('morada IS NOT NULL')
                       ->where('morada !=', '');
            } elseif ($filters['tem_morada'] == '0') {
                $builder->groupStart()
                       ->where('morada IS NULL')
                       ->orWhere('morada', '')
                       ->groupEnd();
            }
        }
        
        return $builder->orderBy('nome', 'ASC')->findAll();
    }

    /**
     * Obter escolas recentes (últimos 30 dias)
     */
    public function getRecentEscolas($days = 30)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('created_at >=', $date)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Atualizar múltiplas escolas
     */
    public function updateMultipleEscolas($ids, $data)
    {
        return $this->whereIn('id', $ids)->set($data)->update();
    }

    /**
     * Eliminar múltiplas escolas
     */
    public function deleteMultipleEscolas($ids)
    {
        return $this->whereIn('id', $ids)->delete();
    }
}