<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model para log de ações do sistema de férias
 * Tabela: ferias_log
 */
class FeriasLogModel extends Model
{
    protected $table            = 'ferias_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pedido_id',
        'user_nif',
        'acao',
        'detalhes',
        'realizado_por'
    ];

    // Dates
    protected $useTimestamps = false; // Apenas criado_em
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';

    /**
     * Registrar uma ação no log
     * 
     * @param int|null $pedidoId ID do pedido (null se for ação de atribuição)
     * @param string|null $userNif NIF do professor afetado
     * @param string $acao Descrição da ação
     * @param string|null $detalhes Detalhes adicionais
     * @param int|null $realizadoPor ID do utilizador que realizou a ação
     * @return int|bool
     */
    public function registrarAcao($pedidoId, $userNif, $acao, $detalhes = null, $realizadoPor = null)
    {
        return $this->insert([
            'pedido_id'     => $pedidoId,
            'user_nif'      => $userNif,
            'acao'          => $acao,
            'detalhes'      => $detalhes,
            'realizado_por' => $realizadoPor
        ]);
    }

    /**
     * Obter histórico de um pedido
     * 
     * @param int $pedidoId ID do pedido
     * @return array
     */
    public function getHistoricoPedido($pedidoId)
    {
        return $this->select('ferias_log.*, user.name as nome_realizador')
                    ->join('user', 'user.id = ferias_log.realizado_por', 'left')
                    ->where('ferias_log.pedido_id', $pedidoId)
                    ->orderBy('ferias_log.criado_em', 'ASC')
                    ->findAll();
    }

    /**
     * Obter histórico de um professor
     * 
     * @param string $userNif NIF do professor
     * @param int $limite Limite de registos
     * @return array
     */
    public function getHistoricoProfessor($userNif, $limite = 50)
    {
        return $this->select('ferias_log.*, user.name as nome_realizador')
                    ->join('user', 'user.id = ferias_log.realizado_por', 'left')
                    ->where('ferias_log.user_nif', $userNif)
                    ->orderBy('ferias_log.criado_em', 'DESC')
                    ->limit($limite)
                    ->findAll();
    }

    /**
     * Obter todas as ações registadas num período
     * 
     * @param string $dataInicio Data de início
     * @param string $dataFim Data de fim
     * @return array
     */
    public function getAcoesPorPeriodo($dataInicio, $dataFim)
    {
        return $this->select('ferias_log.*, 
                             user_prof.name as nome_professor,
                             user_realizador.name as nome_realizador')
                    ->join('user as user_prof', 'user_prof.NIF = ferias_log.user_nif', 'left')
                    ->join('user as user_realizador', 'user_realizador.id = ferias_log.realizado_por', 'left')
                    ->where('ferias_log.criado_em >=', $dataInicio)
                    ->where('ferias_log.criado_em <=', $dataFim)
                    ->orderBy('ferias_log.criado_em', 'DESC')
                    ->findAll();
    }

    /**
     * Limpar logs antigos (manutenção)
     * 
     * @param int $dias Número de dias para manter (padrão: 365 dias)
     * @return int Número de registos removidos
     */
    public function limparLogsAntigos($dias = 365)
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        return $this->where('criado_em <', $dataLimite)->delete();
    }

    /**
     * Registar log de ação (alias simplificado)
     * 
     * @param string $userNif NIF do professor afetado
     * @param string $acao Tipo de ação
     * @param string $detalhes Detalhes da ação
     * @return int|bool
     */
    public function registarLog($userNif, $acao, $detalhes = null)
    {
        $userData = session()->get('LoggedUserData');
        $realizadoPor = $userData['id'] ?? null;
        
        return $this->insert([
            'pedido_id'     => null,
            'user_nif'      => $userNif,
            'acao'          => $acao,
            'detalhes'      => $detalhes,
            'realizado_por' => $realizadoPor
        ]);
    }
}
