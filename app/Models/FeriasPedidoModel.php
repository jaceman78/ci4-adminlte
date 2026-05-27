<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model para gestão de pedidos de férias
 * Tabela: ferias_pedido
 */
class FeriasPedidoModel extends Model
{
    protected $table            = 'ferias_pedido';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_nif',
        'anoletivo_id',
        'estado',
        'numero_remarcacao',
        'motivo_remarcacao',
        'datas_remarcacao_propostas',
        'total_dias',
        'documento_pdf',
        'documento_assinado',
        'documento_remarcacao',
        'documento_remarcacao_assinado',
        'pedido_remarcado_de',
        'observacoes_professor',
        'observacoes_secretaria',
        'submetido_em',
        'aprovado_em',
        'aprovado_por',
        'rejeitado_em',
        'rejeitado_por',
        'upload_assinatura_em',
        'requer_acumulacao',
        'dias_sobrantes',
        'motivo_acumulacao',
        'doc_acumulacao_pdf',
        'doc_acumulacao_assinado',
        'acumulacao_estado',
        'acumulacao_despacho_por',
        'acumulacao_despacho_em',
        'upload_acumulacao_em',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'criado_em';
    protected $updatedField  = 'atualizado_em';

    // Validation
    protected $validationRules = [
        'user_nif'     => 'required|integer',
        'anoletivo_id' => 'required|integer',
        'estado'       => 'required|in_list[por_preencher,submetido,em_aprovacao,aprovado,rejeitado,cancelado,aguarda_assinatura,concluido,remarcacao_solicitada,remarcacao_aprovada]',
        'total_dias'   => 'permit_empty|integer|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'user_nif' => [
            'required' => 'O NIF do professor é obrigatório'
        ],
        'estado' => [
            'in_list' => 'Estado inválido'
        ]
    ];

    /**
     * Obter pedidos de um professor num ano letivo
     * 
     * @param string $userNif NIF do professor
     * @param int $anoLetivoId ID do ano letivo
     * @return array
     */
    public function getPedidosProfessor($userNif, $anoLetivoId = null)
    {
        $builder = $this->select('ferias_pedido.*, 
                                 user.name as nome_professor, 
                                 user.email as email_professor,
                                 ano_letivo.anoletivo as ano,
                                 aprovador.name as nome_aprovador,
                                 rejeitador.name as nome_rejeitador')
                        ->join('user', 'user.NIF = ferias_pedido.user_nif', 'left')
                        ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_pedido.anoletivo_id', 'left')
                        ->join('user as aprovador', 'aprovador.id = ferias_pedido.aprovado_por', 'left')
                        ->join('user as rejeitador', 'rejeitador.id = ferias_pedido.rejeitado_por', 'left')
                        ->where('ferias_pedido.user_nif', $userNif);

        if ($anoLetivoId) {
            $builder->where('ferias_pedido.anoletivo_id', $anoLetivoId);
        }

        return $builder->orderBy('ferias_pedido.criado_em', 'DESC')->findAll();
    }

    /**
     * Obter pedidos pendentes de aprovação
     * 
     * @param int $anoLetivoId ID do ano letivo (opcional)
     * @return array
     */
    public function getPedidosPendentes($anoLetivoId = null)
    {
        $builder = $this->select('ferias_pedido.*, 
                                 user.name as nome_professor, 
                                 user.email as email_professor,
                                 ano_letivo.anoletivo as ano')
                        ->join('user', 'user.NIF = ferias_pedido.user_nif', 'left')
                        ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_pedido.anoletivo_id', 'left')
                        ->whereIn('ferias_pedido.estado', ['submetido', 'em_aprovacao']);

        if ($anoLetivoId) {
            $builder->where('ferias_pedido.anoletivo_id', $anoLetivoId);
        }

        return $builder->orderBy('ferias_pedido.submetido_em', 'ASC')->findAll();
    }

    /**
     * Obter pedido com períodos
     * 
     * @param int $pedidoId ID do pedido
     * @return array|null
     */
    public function getPedidoComPeriodos($pedidoId)
    {
        $pedido = $this->select('ferias_pedido.*, 
                                user.name as nome_professor, 
                                user.email as email_professor,
                                ano_letivo.anoletivo as ano,
                                aprovador.name as nome_aprovador')
                       ->join('user', 'user.NIF = ferias_pedido.user_nif', 'left')
                       ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_pedido.anoletivo_id', 'left')
                       ->join('user as aprovador', 'aprovador.id = ferias_pedido.aprovado_por', 'left')
                       ->find($pedidoId);

        if (!$pedido) {
            return null;
        }

        // Carregar períodos
        $periodosModel = new FeriasPeriodoModel();
        $pedido['periodos'] = $periodosModel->where('pedido_id', $pedidoId)
                                           ->orderBy('data_inicio', 'ASC')
                                           ->findAll();

        return $pedido;
    }

    /**
     * Criar novo pedido de férias com períodos
     * 
     * @param string $userNif NIF do professor
     * @param int $anoLetivoId ID do ano letivo
     * @param array $periodos Array de períodos ['data_inicio' => '2026-08-01', 'data_fim' => '2026-08-10', 'dias_uteis' => 8]
     * @param string $observacoes Observações do professor
     * @return int|bool ID do pedido criado ou false se falhar
     */
    public function criarPedido($userNif, $anoLetivoId, $periodos, $observacoes = null)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Calcular total de dias
            $totalDias = array_sum(array_column($periodos, 'dias_uteis'));

            // Criar pedido
            $pedidoData = [
                'user_nif'                => $userNif,
                'anoletivo_id'            => $anoLetivoId,
                'estado'                  => 'submetido',
                'total_dias'              => $totalDias,
                'observacoes_professor'   => $observacoes,
                'submetido_em'            => date('Y-m-d H:i:s')
            ];

            $this->insert($pedidoData);
            $pedidoId = $this->getInsertID();

            // Criar períodos
            $periodosModel = new FeriasPeriodoModel();
            foreach ($periodos as $periodo) {
                $periodosModel->insert([
                    'pedido_id'   => $pedidoId,
                    'data_inicio' => $periodo['data_inicio'],
                    'data_fim'    => $periodo['data_fim'],
                    'dias_uteis'  => $periodo['dias_uteis'],
                    'observacoes' => $periodo['observacoes'] ?? null
                ]);
            }

            // Log
            $logModel = new FeriasLogModel();
            $logModel->registrarAcao($pedidoId, $userNif, 'Pedido submetido', "Total de {$totalDias} dias em " . count($periodos) . " período(s)", null);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return false;
            }

            return $pedidoId;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Erro ao criar pedido de férias: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Aprovar pedido de férias
     * 
     * @param int $pedidoId ID do pedido
     * @param int $aprovadoPor ID do utilizador que aprovou
     * @param string $observacoes Observações da secretaria
     * @return bool
     */
    public function aprovarPedido($pedidoId, $aprovadoPor, $observacoes = null)
    {
        $pedido = $this->find($pedidoId);
        if (!$pedido) {
            return false;
        }

        $data = [
            'estado'                  => 'aprovado',
            'aprovado_em'             => date('Y-m-d H:i:s'),
            'aprovado_por'            => $aprovadoPor,
            'observacoes_secretaria'  => $observacoes
        ];

        $result = $this->update($pedidoId, $data);

        if ($result) {
            // Log
            $logModel = new FeriasLogModel();
            $logModel->registrarAcao($pedidoId, $pedido['user_nif'], 'Pedido aprovado', $observacoes, $aprovadoPor);
        }

        return $result;
    }

    /**
     * Rejeitar pedido de férias
     * 
     * @param int $pedidoId ID do pedido
     * @param int $rejeitadoPor ID do utilizador que rejeitou
     * @param string $motivo Motivo da rejeição
     * @return bool
     */
    public function rejeitarPedido($pedidoId, $rejeitadoPor, $motivo)
    {
        $pedido = $this->find($pedidoId);
        if (!$pedido) {
            return false;
        }

        $data = [
            'estado'                  => 'rejeitado',
            'rejeitado_em'            => date('Y-m-d H:i:s'),
            'rejeitado_por'           => $rejeitadoPor,
            'observacoes_secretaria'  => $motivo
        ];

        $result = $this->update($pedidoId, $data);

        if ($result) {
            // Log
            $logModel = new FeriasLogModel();
            $logModel->registrarAcao($pedidoId, $pedido['user_nif'], 'Pedido rejeitado', $motivo, $rejeitadoPor);
        }

        return $result;
    }

    /**
     * Marcar pedido como aguardando assinatura (após gerar PDF)
     * 
     * @param int $pedidoId ID do pedido
     * @param string $caminhoDocumento Caminho do documento PDF gerado
     * @return bool
     */
    public function marcarAguardaAssinatura($pedidoId, $caminhoDocumento)
    {
        $pedido = $this->find($pedidoId);
        if (!$pedido) {
            return false;
        }

        $result = $this->update($pedidoId, [
            'estado'        => 'aguarda_assinatura',
            'documento_pdf' => $caminhoDocumento
        ]);

        if ($result) {
            // Log
            $logModel = new FeriasLogModel();
            $logModel->registrarAcao($pedidoId, $pedido['user_nif'], 'Documento gerado - aguarda assinatura', null, null);
        }

        return $result;
    }

    /**
     * Registrar upload do documento assinado
     * 
     * @param int $pedidoId ID do pedido
     * @param string $caminhoDocumento Caminho do documento assinado
     * @return bool
     */
    public function registrarDocumentoAssinado($pedidoId, $caminhoDocumento)
    {
        $pedido = $this->find($pedidoId);
        if (!$pedido) {
            return false;
        }

        $result = $this->update($pedidoId, [
            'estado'               => 'concluido',
            'documento_assinado'   => $caminhoDocumento,
            'upload_assinatura_em' => date('Y-m-d H:i:s')
        ]);

        if ($result) {
            // Log
            $logModel = new FeriasLogModel();
            $logModel->registrarAcao($pedidoId, $pedido['user_nif'], 'Documento assinado - pedido concluído', null, null);
        }

        return $result;
    }

    /**
     * Obter estatísticas de pedidos para dashboard
     * 
     * @param int $anoLetivoId ID do ano letivo
     * @return array
     */
    public function getEstatisticas($anoLetivoId = null)
    {
        $builder = $this->builder();

        if ($anoLetivoId) {
            $builder->where('anoletivo_id', $anoLetivoId);
        }

        $result = $builder->select('estado, COUNT(*) as total')
                          ->groupBy('estado')
                          ->get()
                          ->getResultArray();

        $stats = [
            'por_preencher'       => 0,
            'submetido'           => 0,
            'em_aprovacao'        => 0,
            'aprovado'            => 0,
            'rejeitado'           => 0,
            'cancelado'           => 0,
            'aguarda_assinatura'  => 0,
            'concluido'           => 0,
            'total'               => 0
        ];

        foreach ($result as $row) {
            $stats[$row['estado']] = (int) $row['total'];
            $stats['total'] += (int) $row['total'];
        }

        return $stats;
    }
}
