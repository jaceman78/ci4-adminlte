<?php

namespace App\Models;

use CodeIgniter\Model;

class EstadosTicketModel extends Model
{
    protected $table            = 'estados_ticket';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'codigo',
        'nome',
        'descricao',
        'cor',
        'icone',
        'ordem',
        'ativo',
        'permite_edicao',
        'permite_atribuicao',
        'estado_final',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'codigo'     => 'required|max_length[50]|is_unique[estados_ticket.codigo,id,{id}]',
        'nome'       => 'required|max_length[100]',
        'cor'        => 'required|in_list[primary,secondary,success,danger,warning,info,light,dark]',
        'ordem'      => 'required|integer',
    ];
    protected $validationMessages   = [];
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

    /**
     * Obter todos os estados ativos ordenados
     */
    public function getEstadosAtivos()
    {
        return $this->where('ativo', 1)
            ->orderBy('ordem', 'ASC')
            ->findAll();
    }

    /**
     * Obter estado pelo código
     */
    public function getEstadoPorCodigo(string $codigo)
    {
        return $this->where('codigo', $codigo)
            ->where('ativo', 1)
            ->first();
    }

    /**
     * Obter estados para dropdown
     */
    public function getEstadosDropdown()
    {
        $estados = $this->getEstadosAtivos();
        $dropdown = [];
        
        foreach ($estados as $estado) {
            $dropdown[$estado['codigo']] = $estado['nome'];
        }
        
        return $dropdown;
    }

    /**
     * Verificar se transição entre estados é permitida
     */
    public function transicaoPermitida(string $estadoOrigemCodigo, string $estadoDestinoCodigo, int $nivelUsuario): bool
    {
        $estadoOrigem = $this->getEstadoPorCodigo($estadoOrigemCodigo);
        $estadoDestino = $this->getEstadoPorCodigo($estadoDestinoCodigo);

        if (!$estadoOrigem || !$estadoDestino) {
            return false;
        }

        $transicao = $this->db->table('estados_ticket_transicoes')
            ->where('estado_origem_id', $estadoOrigem['id'])
            ->where('estado_destino_id', $estadoDestino['id'])
            ->where('ativo', 1)
            ->get()
            ->getRowArray();

        if (!$transicao) {
            return false;
        }

        return $nivelUsuario >= $transicao['nivel_minimo'];
    }

    /**
     * Obter próximos estados possíveis
     */
    public function getProximosEstados(string $estadoAtualCodigo, int $nivelUsuario): array
    {
        $estadoAtual = $this->getEstadoPorCodigo($estadoAtualCodigo);

        if (!$estadoAtual) {
            return [];
        }

        $query = $this->db->query("
            SELECT 
                et.*,
                ett.nivel_minimo,
                ett.requer_comentario
            FROM estados_ticket_transicoes ett
            INNER JOIN estados_ticket et ON et.id = ett.estado_destino_id
            WHERE ett.estado_origem_id = ?
            AND ett.ativo = 1
            AND ett.nivel_minimo <= ?
            AND et.ativo = 1
            ORDER BY et.ordem ASC
        ", [$estadoAtual['id'], $nivelUsuario]);

        return $query->getResultArray();
    }

    /**
     * Renderizar badge HTML do estado
     */
    public function renderBadge(string $codigo, bool $comIcone = true): string
    {
        $estado = $this->getEstadoPorCodigo($codigo);

        if (!$estado) {
            return '<span class="badge bg-secondary">Desconhecido</span>';
        }

        $icone = $comIcone && $estado['icone'] ? '<i class="' . $estado['icone'] . '"></i> ' : '';
        
        return sprintf(
            '<span class="badge bg-%s">%s%s</span>',
            esc($estado['cor']),
            $icone,
            esc($estado['nome'])
        );
    }

    /**
     * Obter estatísticas de tickets por estado
     */
    public function getEstatisticasPorEstado(): array
    {
        $query = $this->db->query("
            SELECT 
                et.codigo,
                et.nome,
                et.cor,
                et.icone,
                COUNT(t.id) as total_tickets
            FROM estados_ticket et
            LEFT JOIN tickets t ON t.estado = et.codigo
            WHERE et.ativo = 1
            GROUP BY et.id, et.codigo, et.nome, et.cor, et.icone, et.ordem
            ORDER BY et.ordem ASC
        ");

        return $query->getResultArray();
    }
}
