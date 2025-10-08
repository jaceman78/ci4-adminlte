<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoEquipamentosModel extends Model
{
    protected $table            = 'tipos_equipamento';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['nome', 'descricao'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Not used, but good practice to define

    protected $validationRules = [
        'id'   => 'permit_empty|integer',
        'nome'      => 'required|min_length[3]|max_length[255]|is_unique[tipos_equipamento.nome,id,{id}]',
        'descricao' => 'max_length[1000]'
    ];
    protected $validationMessages = [
        'nome' => [
            'required'   => 'O nome do tipo de equipamento é obrigatório.',
            'min_length' => 'O nome do tipo de equipamento deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome do tipo de equipamento não pode exceder 255 caracteres.',
            'is_unique'  => 'Já existe um tipo de equipamento com este nome.'
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
}

