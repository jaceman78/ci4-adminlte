<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipamentosModel extends Model
{
    protected $table = 'equipamentos';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'sala_id',
        'tipo_id',
        'marca',
        'modelo',
        'numero_serie',
        'estado',
        'data_aquisicao',
        'observacoes',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Exemplo de método para estatísticas (opcional)
    public function getStats()
    {
        $stats = [];
        $stats['total'] = $this->countAllResults();
        $stats['ativos'] = $this->where('estado', 'ativo')->countAllResults();
        $stats['inativos'] = $this->where('estado', 'inativo')->countAllResults();
        $stats['pendentes'] = $this->where('estado', 'pendente')->countAllResults();

        // Por tipo
        $builder = $this->builder();
        $builder->select('tipo_id, COUNT(*) as total')->groupBy('tipo_id');
        $stats['por_tipo'] = $builder->get()->getResultArray();

        // Por sala
        $builder = $this->builder();
        $builder->select('sala_id, COUNT(*) as total')->groupBy('sala_id');
        $stats['por_sala'] = $builder->get()->getResultArray();

        return $stats;
    }
}