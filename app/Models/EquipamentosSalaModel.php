<?php

namespace App\Models;

use CodeIgniter\Model;

class EquipamentosSalaModel extends Model
{
    protected $table = 'equipamentos_sala';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    
    protected $allowedFields = [
        'equipamento_id',
        'sala_id',
        'data_entrada',
        'data_saida',
        'motivo_movimentacao',
        'user_id',
        'observacoes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Obter sala atual de um equipamento
     */
    public function getSalaAtual($equipamentoId)
    {
        $db = \Config\Database::connect();
        $salaAtual = $db->table('equipamentos_sala')
            ->where('equipamento_id', $equipamentoId)
            ->where('data_saida', null)
            ->get()
            ->getRowArray();

        return $salaAtual;
    }

    /**
     * Obter histórico de movimentações de um equipamento
     */
    public function getHistoricoEquipamento($equipamentoId)
    {
        return $this->select('equipamentos_sala.*, salas.codigo_sala, user.name as movimentado_por')
                    ->join('salas', 'salas.id = equipamentos_sala.sala_id', 'left')
                    ->join('user', 'user.id = equipamentos_sala.user_id', 'left')
                    ->where('equipamento_id', $equipamentoId)
                    ->orderBy('data_entrada', 'DESC')
                    ->findAll();
    }

    /**
     * Obter equipamentos atualmente numa sala
     */
    public function getEquipamentosPorSala($salaId)
    {
        return $this->select('equipamentos_sala.*, equipamentos.marca, equipamentos.modelo, equipamentos.numero_serie, tipos_equipamento.nome as tipo_nome')
                    ->join('equipamentos', 'equipamentos.id = equipamentos_sala.equipamento_id')
                    ->join('tipos_equipamento', 'tipos_equipamento.id = equipamentos.tipo_id', 'left')
                    ->where('equipamentos_sala.sala_id', $salaId)
                    ->where('equipamentos_sala.data_saida', null)
                    ->findAll();
    }

    /**
     * Mover equipamento para outra sala
     */
    public function moverEquipamento($equipamentoId, $novaSalaId, $motivo = null, $userId = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Fechar localização atual
        $localizacaoAtual = $this->getSalaAtual($equipamentoId);
        if ($localizacaoAtual) {
            $this->update($localizacaoAtual['id'], [
                'data_saida' => date('Y-m-d H:i:s')
            ]);
        }

        // Criar nova localização
        $novaLocalizacao = [
            'equipamento_id' => $equipamentoId,
            'sala_id' => $novaSalaId,
            'data_entrada' => date('Y-m-d H:i:s'),
            'data_saida' => null,
            'motivo_movimentacao' => $motivo,
            'user_id' => $userId
        ];
        $this->insert($novaLocalizacao);

        $db->transComplete();

        return $db->transStatus();
    }

    /**
     * Registrar entrada de equipamento numa sala
     */
    public function atribuirSala($equipamentoId, $salaId, $userId = null)
    {
        return $this->insert([
            'equipamento_id' => $equipamentoId,
            'sala_id' => $salaId,
            'data_entrada' => date('Y-m-d H:i:s'),
            'data_saida' => null,
            'user_id' => $userId
        ]);
    }

    /**
     * Remover equipamento de uma sala (ex: para reparação)
     */
    public function removerDeSala($equipamentoId, $motivo = null, $userId = null)
    {
        $localizacaoAtual = $this->getSalaAtual($equipamentoId);
        
        if ($localizacaoAtual) {
            return $this->update($localizacaoAtual['id'], [
                'data_saida' => date('Y-m-d H:i:s'),
                'motivo_movimentacao' => $motivo,
                'user_id' => $userId
            ]);
        }
        
        return false;
    }

    /**
     * Obter equipamentos sem sala atribuída
     */
    public function getEquipamentosSemSala()
    {
        $builder = $this->db->table('equipamentos e');
        $builder->select('e.*, tipos_equipamento.nome as tipo_nome');
        $builder->join('tipos_equipamento', 'tipos_equipamento.id = e.tipo_id', 'left');
        $builder->join('equipamentos_sala es', 'es.equipamento_id = e.id AND es.data_saida IS NULL', 'left');
        $builder->where('es.id IS NULL'); // Equipamentos que não têm registro ativo em equipamentos_sala
        
        return $builder->get()->getResultArray();
    }

    public function getEquipamentosPorSalaAntiga($salaId)
    {
        $db = \Config\Database::connect();
        $equipamentos = $db->table('equipamentos_sala es')
            ->join('equipamentos e', 'e.id = es.equipamento_id')
            ->where('es.sala_id', $salaId)
            ->where('es.data_saida', null)
            ->get()
            ->getResultArray();

        return $equipamentos;
    }
}
