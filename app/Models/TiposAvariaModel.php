<?php

namespace App\Models;

use CodeIgniter\Model;

class TiposAvariaModel extends Model
{
    protected $table            = 'tipos_avaria';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['descricao'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // Not used, but good practice to define

    protected $validationRules = [
        'descricao' => 'required|min_length[3]|max_length[150]|is_unique[tipos_avaria.descricao,id,{id}]'
    ];
    protected $validationMessages = [
        'descricao' => [
            'required'   => 'A descrição do tipo de avaria é obrigatória.',
            'min_length' => 'A descrição do tipo de avaria deve ter pelo menos 3 caracteres.',
            'max_length' => 'A descrição do tipo de avaria não pode exceder 150 caracteres.',
            'is_unique'  => 'Já existe um tipo de avaria com esta descrição.'
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