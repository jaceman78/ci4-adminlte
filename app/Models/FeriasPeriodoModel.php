<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model para gestão de períodos de férias
 * Tabela: ferias_periodo
 */
class FeriasPeriodoModel extends Model
{
    protected $table            = 'ferias_periodo';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'pedido_id',
        'data_inicio',
        'data_fim',
        'dias_uteis',
        'observacoes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules = [
        'pedido_id'   => 'required|integer',
        'data_inicio' => 'required|valid_date',
        'data_fim'    => 'required|valid_date',
        'dias_uteis'  => 'required|integer|greater_than[0]'
    ];

    protected $validationMessages = [
        'pedido_id' => [
            'required' => 'O ID do pedido é obrigatório'
        ],
        'data_inicio' => [
            'required' => 'A data de início é obrigatória',
            'valid_date' => 'Data de início inválida'
        ],
        'data_fim' => [
            'required' => 'A data de fim é obrigatória',
            'valid_date' => 'Data de fim inválida'
        ],
        'dias_uteis' => [
            'required' => 'O número de dias úteis é obrigatório',
            'greater_than' => 'O número de dias úteis deve ser maior que 0'
        ]
    ];

    /**
     * Obter períodos de um pedido
     * 
     * @param int $pedidoId ID do pedido
     * @return array
     */
    public function getPeriodosPorPedido($pedidoId)
    {
        return $this->where('pedido_id', $pedidoId)
                    ->orderBy('data_inicio', 'ASC')
                    ->findAll();
    }

    /**
     * Verificar se existe conflito de datas para um professor
     * (útil para evitar férias sobrepostas)
     * 
     * @param string $userNif NIF do professor
     * @param string $dataInicio Data de início do novo período
     * @param string $dataFim Data de fim do novo período
     * @param int $excluirPedidoId ID do pedido a excluir da verificação (útil em edições)
     * @return array|null Retorna o período conflitante ou null se não houver conflito
     */
    public function verificarConflito($userNif, $dataInicio, $dataFim, $excluirPedidoId = null)
    {
        $builder = $this->select('ferias_periodo.*, ferias_pedido.user_nif, ferias_pedido.estado')
                        ->join('ferias_pedido', 'ferias_pedido.id = ferias_periodo.pedido_id')
                        ->where('ferias_pedido.user_nif', $userNif)
                        ->whereIn('ferias_pedido.estado', ['submetido', 'em_aprovacao', 'aprovado', 'aguarda_assinatura', 'concluido'])
                        ->groupStart()
                            ->where('ferias_periodo.data_inicio <=', $dataFim)
                            ->where('ferias_periodo.data_fim >=', $dataInicio)
                        ->groupEnd();

        if ($excluirPedidoId) {
            $builder->where('ferias_pedido.id !=', $excluirPedidoId);
        }

        return $builder->first();
    }

    /**
     * Obter todos os períodos de férias de professores num intervalo de datas
     * (útil para visualização de calendário geral)
     * 
     * @param string $dataInicio Data de início
     * @param string $dataFim Data de fim
     * @param int $anoLetivoId ID do ano letivo (opcional)
     * @return array
     */
    public function getPeriodosPorIntervalo($dataInicio, $dataFim, $anoLetivoId = null)
    {
        $builder = $this->select('ferias_periodo.*, 
                                 ferias_pedido.user_nif, 
                                 ferias_pedido.estado,
                                 user.name as nome_professor,
                                 user.email as email_professor')
                        ->join('ferias_pedido', 'ferias_pedido.id = ferias_periodo.pedido_id')
                        ->join('user', 'user.NIF = ferias_pedido.user_nif', 'left')
                        ->where('ferias_periodo.data_inicio <=', $dataFim)
                        ->where('ferias_periodo.data_fim >=', $dataInicio)
                        ->whereIn('ferias_pedido.estado', ['aprovado', 'aguarda_assinatura', 'concluido']);

        if ($anoLetivoId) {
            $builder->where('ferias_pedido.anoletivo_id', $anoLetivoId);
        }

        return $builder->orderBy('ferias_periodo.data_inicio', 'ASC')->findAll();
    }

    /**
     * Formatar período para exibição
     * 
     * @param array $periodo Período
     * @return string Período formatado (ex: "01/08/2026 a 10/08/2026 (8 dias)")
     */
    public function formatarPeriodo($periodo)
    {
        $inicio = date('d/m/Y', strtotime($periodo['data_inicio']));
        $fim = date('d/m/Y', strtotime($periodo['data_fim']));
        $dias = $periodo['dias_uteis'];

        return "{$inicio} a {$fim} ({$dias} " . ($dias == 1 ? 'dia' : 'dias') . ")";
    }
}
