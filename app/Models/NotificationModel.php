<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table         = 'notificacoes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'user_id',
        'modulo',
        'tipo',
        'titulo',
        'mensagem',
        'url',
        'lida',
        'criado_em',
    ];

    protected $useTimestamps  = false;
    protected $useSoftDeletes = false;

    /**
     * Obter notificações não lidas de um utilizador
     */
    public function getNaoLidas(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
                    ->where('lida', 0)
                    ->orderBy('criado_em', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Contar notificações não lidas
     */
    public function contarNaoLidas(int $userId): int
    {
        return $this->where('user_id', $userId)
                    ->where('lida', 0)
                    ->countAllResults();
    }

    /**
     * Marcar uma notificação como lida
     */
    public function marcarComoLida(int $notifId, int $userId): bool
    {
        return $this->where('id', $notifId)
                    ->where('user_id', $userId)
                    ->set(['lida' => 1])
                    ->update() !== false;
    }

    /**
     * Marcar todas as notificações do utilizador como lidas
     */
    public function marcarTodasComoLidas(int $userId): bool
    {
        return $this->where('user_id', $userId)
                    ->where('lida', 0)
                    ->set(['lida' => 1])
                    ->update() !== false;
    }

    /**
     * Obter todas as notificações de um utilizador (paginadas)
     */
    public function getTodas(int $userId, int $limit = 30, int $offset = 0): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('criado_em', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Limpar notificações antigas (> 30 dias) já lidas
     */
    public function limparAntigas(int $diasRetenção = 30): int
    {
        $data = date('Y-m-d H:i:s', strtotime("-{$diasRetenção} days"));
        $this->where('lida', 1)->where('criado_em <', $data)->delete();
        return $this->db->affectedRows();
    }
}
