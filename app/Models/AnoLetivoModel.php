<?php

namespace App\Models;

use CodeIgniter\Model;

class AnoLetivoModel extends Model
{
    protected $table            = 'ano_letivo';
    protected $primaryKey       = 'id_anoletivo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['anoletivo', 'status'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'anoletivo' => 'required|integer|min_length[4]|max_length[4]',
        'status'    => 'required|in_list[0,1]'
    ];
    protected $validationMessages   = [
        'anoletivo' => [
            'required'   => 'O ano letivo é obrigatório',
            'integer'    => 'O ano letivo deve ser um número',
            'min_length' => 'O ano letivo deve ter 4 dígitos',
            'max_length' => 'O ano letivo deve ter 4 dígitos'
        ],
        'status' => [
            'required' => 'O status é obrigatório',
            'in_list'  => 'O status deve ser 0 (Inativo) ou 1 (Ativo)'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Obter o ano letivo ativo
     * 
     * @return array|null
     */
    public function getAnoAtivo()
    {
        return $this->where('status', 1)->first();
    }

    /**
     * Ativar um ano letivo (e desativar os outros)
     * 
     * @param int $id
     * @return bool
     */
    public function ativarAno($id)
    {
        $this->db->transStart();
        
        // Desativar todos os anos usando query builder
        $this->builder()->update(['status' => 0]);
        
        // Ativar apenas o ano selecionado
        $this->update($id, ['status' => 1]);
        
        $this->db->transComplete();
        
        return $this->db->transStatus();
    }

    /**
     * Verificar se um ano letivo já existe
     * 
     * @param int $ano
     * @param int|null $excluirId
     * @return bool
     */
    public function anoExiste($ano, $excluirId = null)
    {
        $builder = $this->where('anoletivo', $ano);
        
        if ($excluirId) {
            $builder->where('id_anoletivo !=', $excluirId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Obter todos os anos letivos ordenados
     * 
     * @param string $ordem
     * @return array
     */
    public function getAnosOrdenados($ordem = 'DESC')
    {
        return $this->orderBy('anoletivo', $ordem)->findAll();
    }
}
