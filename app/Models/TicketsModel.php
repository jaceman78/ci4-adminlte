<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketsModel extends Model
{
    protected $table      = 'tickets';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'equipamento_id',
        'sala_id',
        'tipo_avaria_id',
        'user_id',
        'atribuido_user_id',
        'ticket_aceite',
        'descricao',
        'estado',
        'prioridade'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [
        'equipamento_id' => 'required|integer',
        'sala_id'        => 'required|integer',
        'tipo_avaria_id' => 'required|integer',
        'user_id'        => 'required|integer',
        'descricao'      => 'required|min_length[10]',
        'estado'         => 'required|validar_estado_ticket',
        'prioridade'     => 'required|in_list[baixa,media,alta,critica]',
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Relacionamentos
    public function getTicketDetails($id = null)
    {
        $this->select('tickets.*, 
            e.marca as equipamento_marca, 
            e.modelo as equipamento_modelo, 
            e.numero_serie as equipamento_nserie,
            te.nome as equipamento_tipo,
            CONCAT(IFNULL(te.nome, ""), " ", e.marca, " ", e.modelo) as equipamento_info,
            s.codigo_sala as sala_nome, 
            esc.nome as escola_nome,
            ta.descricao as tipo_avaria_nome, 
            u.name as user_nome, 
            u.email as user_email, 
            au.name as atribuido_user_nome, 
            au.email as atribuido_user_email');
        $this->join('equipamentos e', 'e.id = tickets.equipamento_id');
        $this->join('tipos_equipamento te', 'te.id = e.tipo_id', 'left');
        $this->join('salas s', 's.id = tickets.sala_id');
        $this->join('escolas esc', 'esc.id = s.escola_id', 'left');
        $this->join('tipos_avaria ta', 'ta.id = tickets.tipo_avaria_id');
        $this->join('user u', 'u.id = tickets.user_id');
        $this->join('user au', 'au.id = tickets.atribuido_user_id', 'left');

        if ($id) {
            return $this->find($id);
        }

        return $this->findAll();
    }

    public function getMyTickets($userId)
    {
        $this->select('tickets.id, e.marca as equipamento_marca, e.modelo as equipamento_modelo, e.numero_serie as equipamento_nserie, te.nome as equipamento_tipo, s.codigo_sala, ta.descricao as tipo_avaria_descricao, tickets.descricao, tickets.estado, tickets.prioridade, tickets.created_at, tickets.updated_at, tickets.user_id, tickets.atribuido_user_id');
        $this->join('equipamentos e', 'e.id = tickets.equipamento_id');
        $this->join('tipos_equipamento te', 'te.id = e.tipo_id', 'left');
        $this->join('salas s', 's.id = tickets.sala_id');
        $this->join('tipos_avaria ta', 'ta.id = tickets.tipo_avaria_id');
        // Incluir tickets criados pelo utilizador OU atribuídos ao utilizador
        $this->groupStart()
            ->where('tickets.user_id', $userId)
            ->orWhere('tickets.atribuido_user_id', $userId)
        ->groupEnd();
        return $this->findAll();
    }

    public function getTicketsForTreatment()
    {
        $this->select('tickets.id, e.marca as equipamento_marca, e.modelo as equipamento_modelo, s.codigo_sala, ta.descricao as tipo_avaria_descricao, tickets.descricao, tickets.estado, tickets.prioridade, tickets.created_at, tickets.updated_at, u.name as user_nome, u.email as user_email, au.name as atribuido_user_nome');
        $this->join('equipamentos e', 'e.id = tickets.equipamento_id');
        $this->join('salas s', 's.id = tickets.sala_id');
        $this->join('tipos_avaria ta', 'ta.id = tickets.tipo_avaria_id');
        $this->join('user u', 'u.id = tickets.user_id');
        $this->join('user au', 'au.id = tickets.atribuido_user_id', 'left');
        $this->whereIn('tickets.estado', ['novo', 'em_resolucao', 'aguarda_peca']);
        return $this->findAll();
    }

    public function getAllTicketsOrdered()
    {
        $this->select('tickets.*, e.marca as equipamento_marca, e.modelo as equipamento_modelo, s.codigo_sala, ta.descricao as tipo_avaria_descricao, u.name as user_nome, u.email as user_email, au.name as atribuido_user_nome, au.email as atribuido_user_email');
        $this->join('equipamentos e', 'e.id = tickets.equipamento_id');
        $this->join('salas s', 's.id = tickets.sala_id');
        $this->join('tipos_avaria ta', 'ta.id = tickets.tipo_avaria_id');
        $this->join('user u', 'u.id = tickets.user_id');
        $this->join('user au', 'au.id = tickets.atribuido_user_id', 'left');
        $this->orderBy('FIELD(tickets.estado, \'novo\', \'em_resolucao\', \'aguarda_peca\', \'reparado\', \'anulado\')');
        return $this->findAll();
    }
}

