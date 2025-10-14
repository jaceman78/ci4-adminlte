<?php

namespace App\Models;

use CodeIgniter\Model;

class RegistosReparacaoModel extends Model
{
    protected $table      = 'registos_reparacao';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'ticket_id',
        'user_id',
        'descricao',
        'tempo_gasto_min',
        'criado_em'
    ];

    protected $useTimestamps = false; // Usamos criado_em customizado

    protected $validationRules = [
        'ticket_id'      => 'required|is_natural_no_zero',
        'user_id'        => 'required|is_natural_no_zero',
        'descricao'      => 'required|min_length[10]',
        'tempo_gasto_min'=> 'permit_empty|is_natural',
        'criado_em'      => 'required|valid_date'
    ];

    protected $validationMessages = [
        'ticket_id' => [
            'required' => 'O ticket é obrigatório.',
            'is_natural_no_zero' => 'ID do ticket inválido.'
        ],
        'user_id' => [
            'required' => 'O utilizador é obrigatório.',
            'is_natural_no_zero' => 'ID do utilizador inválido.'
        ],
        'descricao' => [
            'required' => 'A descrição é obrigatória.',
            'min_length' => 'A descrição deve ter pelo menos 10 caracteres.'
        ]
    ];

    /**
     * Obter registos de reparação de um ticket com informações do utilizador
     */
    public function getRegistosByTicket($ticketId)
    {
        return $this->select('registos_reparacao.*, user.name as user_nome, user.email as user_email')
            ->join('user', 'user.id = registos_reparacao.user_id')
            ->where('registos_reparacao.ticket_id', $ticketId)
            ->orderBy('registos_reparacao.criado_em', 'DESC')
            ->findAll();
    }
}
