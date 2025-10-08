<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriaisModel extends Model
{
    protected $table            = 'materiais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'referencia',
        'stock_atual',
        'created_at',
        'updated_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nome'        => 'required|min_length[3]|max_length[150]',
        'referencia'  => 'permit_empty|max_length[100]',
        'stock_atual' => 'required|integer|greater_than_equal_to[0]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required'   => 'O nome do material é obrigatório.',
            'min_length' => 'O nome do material deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome do material não pode exceder 150 caracteres.',
        ],
        'stock_atual' => [
            'required'            => 'O stock atual é obrigatório.',
            'integer'             => 'O stock atual deve ser um número inteiro.',
            'greater_than_equal_to' => 'O stock atual não pode ser negativo.',
        ],
    ];
    protected $skipValidation = false;

    /**
     * Obter dados para DataTable
     */
    public function getMateriaisDataTable($start = 0, $length = 10, $search = '', $orderColumn = 'nome', $orderDir = 'asc')
    {
        $builder = $this->builder();

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('nome', $search)
                    ->orLike('referencia', $search)
                    ->groupEnd();
        }

        $recordsFiltered = $builder->countAllResults(false);

        $builder->orderBy($orderColumn, $orderDir);
        $builder->limit((int)$length, (int)$start);

        $data = $builder->get()->getResultArray();

        $recordsTotal = $this->countAllResults();

        return [
            'data' => $data,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ];
    }

    /**
     * Adicionar um novo material
     */
    public function addMaterial(array $data): bool
    {
        return $this->insert($data);
    }

    /**
     * Atualizar um material existente
     */
    public function updateMaterial(int $id, array $data): bool
    {
        return $this->update($id, $data);
    }

    /**
     * Eliminar um material
     */
    public function deleteMaterial(int $id): bool
    {
        return $this->delete($id);
    }





    /**
     * Obter estatísticas dos materiais
     */
    public function getMateriaisStats()
    {
        $stats = [];
        
        $stats["total"] = $this->countAllResults();
        
        // Materiais com stock baixo (ex: < 10)
        $stats["stock_baixo"] = $this->where("stock_atual <", 10)->countAllResults();

        // Materiais sem stock
        $stats["sem_stock"] = $this->where("stock_atual", 0)->countAllResults();

        // Materiais adicionados nos últimos 30 dias
        $last30Days = date("Y-m-d H:i:s", strtotime("-30 days"));
        $stats["adicionados_30dias"] = $this->where("created_at >=", $last30Days)->countAllResults();

        // Valor médio de stock (exemplo, se houvesse um campo de valor)
        // $stats["stock_medio"] = $this->selectAvg("stock_atual")->first()["stock_atual"];

        // Reset builder para próximas queries
        $this->builder()->resetQuery();
        
        return $stats;
    }


}