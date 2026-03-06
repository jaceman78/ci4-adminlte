<?php

namespace App\Models;

use CodeIgniter\Model;

class ExameModel extends Model
{
    protected $table = 'exame';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'codigo_prova',
        'nome_prova',
        'tipo_prova',
        'ano_escolaridade',
        'ativo',
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
        'id' => 'permit_empty|is_natural_no_zero',
        'codigo_prova' => 'required|max_length[10]|is_unique[exame.codigo_prova,id,{id}]',
        'nome_prova' => 'required|max_length[100]',
        'tipo_prova' => 'required|in_list[Exame Nacional,Prova Final,MODa]',
        'ano_escolaridade' => 'required|integer|greater_than[0]|less_than[13]',
    ];

    protected $validationMessages = [
        'id' => [
            'is_natural_no_zero' => 'ID inválido fornecido.',
        ],
        'codigo_prova' => [
            'required' => 'O código da prova é obrigatório',
            'is_unique' => 'Este código de prova já existe',
        ],
        'nome_prova' => [
            'required' => 'O nome da prova é obrigatório',
        ],
        'tipo_prova' => [
            'required' => 'O tipo de prova é obrigatório',
            'in_list' => 'Tipo de prova inválido',
        ],
        'ano_escolaridade' => [
            'required' => 'O ano de escolaridade é obrigatório',
            'integer' => 'O ano de escolaridade deve ser um número',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Busca exames por tipo
     */
    public function getByTipo($tipo)
    {
        return $this->where('tipo_prova', $tipo)
                    ->where('ativo', 1)
                    ->orderBy('ano_escolaridade', 'ASC')
                    ->orderBy('nome_prova', 'ASC')
                    ->findAll();
    }

    /**
     * Busca exames por ano de escolaridade
     */
    public function getByAnoEscolaridade($ano)
    {
        return $this->where('ano_escolaridade', $ano)
                    ->where('ativo', 1)
                    ->orderBy('nome_prova', 'ASC')
                    ->findAll();
    }

    /**
     * Busca exame por código
     */
    public function getByCodigo($codigo)
    {
        return $this->where('codigo_prova', $codigo)
                    ->first();
    }

    /**
     * Lista todos os exames ativos com paginação
     */
    public function getExamesAtivos($limit = 10, $offset = 0)
    {
        return $this->where('ativo', 1)
                    ->orderBy('tipo_prova', 'ASC')
                    ->orderBy('ano_escolaridade', 'ASC')
                    ->orderBy('nome_prova', 'ASC')
                    ->findAll($limit, $offset);
    }

    /**
     * Conta exames por tipo
     */
    public function countByTipo()
    {
        return $this->select('tipo_prova, COUNT(*) as total')
                    ->where('ativo', 1)
                    ->groupBy('tipo_prova')
                    ->findAll();
    }
}
