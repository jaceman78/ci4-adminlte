<?php
namespace App\Models;

use CodeIgniter\Model;

class InutilizadosKitdigitalModel extends Model
{
    protected $table = 'inutilizados_kitdigital';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'n_serie',
        'marca',
        'modelo',
        'ram',
        'disco',
        'teclado',
        'ecra',
        'bateria',
        'caixa',
        'outros',
        'observacoes',
        'id_tecnico',
        'estado',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'n_serie' => 'required|min_length[3]|max_length[255]',
        'marca' => 'required|min_length[2]|max_length[100]',
        'modelo' => 'permit_empty|max_length[100]',
        'ram' => 'required|in_list[0,1]',
        'disco' => 'required|in_list[0,1]',
        'teclado' => 'required|in_list[0,1]',
        'ecra' => 'required|in_list[0,1]',
        'bateria' => 'required|in_list[0,1]',
        'caixa' => 'required|in_list[0,1]',
        'estado' => 'required|in_list[ativo,esgotado,descartado]'
    ];

    protected $validationMessages = [
        'n_serie' => [
            'required' => 'Número de série é obrigatório.',
            'min_length' => 'Número de série deve ter pelo menos 3 caracteres.',
            'max_length' => 'Número de série não pode exceder 255 caracteres.'
        ],
        'marca' => [
            'required' => 'Marca é obrigatória.',
            'min_length' => 'Marca deve ter pelo menos 2 caracteres.',
            'max_length' => 'Marca não pode exceder 100 caracteres.'
        ]
    ];

    /**
     * Obter todos os equipamentos com informações do técnico
     */
    public function getEquipamentosComDetalhes($incluirApagados = false)
    {
        $builder = $this->builder();
        $builder->select('inutilizados_kitdigital.*, 
                         user.name as tecnico_nome,
                         user.email as tecnico_email');
        $builder->join('user', 'user.id = inutilizados_kitdigital.id_tecnico', 'left');
        
        if (!$incluirApagados) {
            $builder->where('inutilizados_kitdigital.deleted_at', null);
        }
        
        $builder->orderBy('inutilizados_kitdigital.created_at', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Obter estatísticas gerais
     */
    public function getEstatisticas()
    {
        $db = \Config\Database::connect();
        
        $stats = [
            'total' => $this->where('deleted_at', null)->countAllResults(),
            'ativos' => $this->where('estado', 'ativo')->where('deleted_at', null)->countAllResults(),
            'esgotados' => $this->where('estado', 'esgotado')->where('deleted_at', null)->countAllResults(),
            'descartados' => $this->where('estado', 'descartado')->where('deleted_at', null)->countAllResults(),
        ];
        
        // Componentes disponíveis
        $stats['componentes'] = [
            'ram' => $this->where('ram', 1)->where('estado', 'ativo')->where('deleted_at', null)->countAllResults(),
            'disco' => $this->where('disco', 1)->where('estado', 'ativo')->where('deleted_at', null)->countAllResults(),
            'teclado' => $this->where('teclado', 1)->where('estado', 'ativo')->where('deleted_at', null)->countAllResults(),
            'ecra' => $this->where('ecra', 1)->where('estado', 'ativo')->where('deleted_at', null)->countAllResults(),
            'bateria' => $this->where('bateria', 1)->where('estado', 'ativo')->where('deleted_at', null)->countAllResults(),
            'caixa' => $this->where('caixa', 1)->where('estado', 'ativo')->where('deleted_at', null)->countAllResults(),
        ];
        
        return $stats;
    }

    /**
     * Obter estatísticas por marca
     */
    public function getEstatisticasPorMarca()
    {
        return $this->select('marca, COUNT(*) as total')
                    ->where('deleted_at', null)
                    ->groupBy('marca')
                    ->orderBy('total', 'DESC')
                    ->findAll();
    }

    /**
     * Verificar se todos os componentes foram utilizados
     */
    public function verificarComponentesEsgotados($id)
    {
        $equipamento = $this->find($id);
        
        if (!$equipamento) {
            return false;
        }
        
        // Se todos os componentes principais estão em 0, considerar esgotado
        $componentesPrincipais = [
            $equipamento['ram'],
            $equipamento['disco'],
            $equipamento['teclado'],
            $equipamento['ecra'],
            $equipamento['bateria'],
            $equipamento['caixa']
        ];
        
        return array_sum($componentesPrincipais) === 0;
    }

    /**
     * Atualizar estado automaticamente se esgotado
     */
    public function atualizarEstadoSeEsgotado($id)
    {
        if ($this->verificarComponentesEsgotados($id)) {
            $this->update($id, ['estado' => 'esgotado']);
            return true;
        }
        return false;
    }

    /**
     * Buscar equipamentos com componente específico disponível
     */
    public function buscarPorComponente($componente)
    {
        $componentesValidos = ['ram', 'disco', 'teclado', 'ecra', 'bateria', 'caixa'];
        
        if (!in_array($componente, $componentesValidos)) {
            return [];
        }
        
        return $this->where($componente, 1)
                    ->where('estado', 'ativo')
                    ->where('deleted_at', null)
                    ->findAll();
    }
}
