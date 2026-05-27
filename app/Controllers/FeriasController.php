<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FeriasAtribuicaoModel;
use App\Models\FeriasPedidoModel;
use App\Models\FeriasPeriodoModel;
use App\Models\FeriasLogModel;
use App\Models\FeriasFeriadosModel;
use App\Models\FeriasConfiguracaoModel;
use App\Models\AnoLetivoModel;
use App\Models\UserModel;
use App\Models\EscolasModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\API\ResponseTrait;

/**
 * Controller para gestão de férias de professores
 */
class FeriasController extends BaseController
{
    use ResponseTrait;

    protected $atribuicaoModel;
    protected $pedidoModel;
    protected $periodoModel;
    protected $logModel;
    protected $feriadosModel;
    protected $configuracaoModel;
    protected $faltasDescontoModel;
    protected $anoLetivoModel;
    protected $userModel;
    protected $escolasModel;
    protected $db;

    public function __construct()
    {
        $this->atribuicaoModel = new FeriasAtribuicaoModel();
        $this->pedidoModel = new FeriasPedidoModel();
        $this->periodoModel = new FeriasPeriodoModel();
        $this->logModel = new FeriasLogModel();
        $this->feriadosModel = new FeriasFeriadosModel();
        $this->configuracaoModel = new FeriasConfiguracaoModel();
        $this->faltasDescontoModel = new \App\Models\FeriasFaltasDescontoModel();
        $this->anoLetivoModel = new AnoLetivoModel();
        $this->userModel = new UserModel();
        $this->escolasModel = new EscolasModel();
        $this->db = \Config\Database::connect();
        
        helper(['ferias', 'form', 'notification']);
    }

    /**
     * DEBUG: Endpoint temporário para verificar estado do sistema
     * REMOVER EM PRODUÇÃO APÓS DEBUG
     */
    public function debugStatus()
    {
        $userData = session()->get('LoggedUserData');
        
        $info = [
            'timestamp' => date('Y-m-d H:i:s'),
            'timezone' => date_default_timezone_get(),
            'authenticated' => $userData ? true : false,
            'user_nif' => $userData['NIF'] ?? null,
            'ano_ativo' => $this->anoLetivoModel->getAnoAtivo(),
            'helper_loaded' => function_exists('validar_periodo_ferias'),
            'php_version' => PHP_VERSION,
            'ci_version' => \CodeIgniter\CodeIgniter::CI_VERSION
        ];
        
        // Testar função helper
        if (function_exists('validar_periodo_ferias')) {
            $info['teste_validacao'] = validar_periodo_ferias('2025-07-01', '2025-07-10', 2025);
        }
        
        return $this->respond($info);
    }

    // ====================================================
    // PÁGINAS - PROFESSOR
    // ====================================================

    /**
     * Página principal de férias do professor
     */
    public function index()
    {
        // Verificar autenticação
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return redirect()->to('/login')->with('error', 'É necessário fazer login');
        }

        $userNif = $userData['NIF'] ?? null;
        if (!$userNif) {
            return redirect()->to('/dashboard')->with('error', 'Utilizador sem NIF associado');
        }

        // Obter ano letivo ativo
        $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
        if (!$anoAtivo) {
            return redirect()->to('/dashboard')->with('error', 'Nenhum ano letivo ativo');
        }

        // Obter atribuição de férias
        $atribuicao = $this->atribuicaoModel->getAtribuicaoProfessor($userNif, $anoAtivo['id_anoletivo']);
        
        // Se não tem dias atribuídos, mostrar mensagem
        if (!$atribuicao) {
            $data = [
                'title' => 'Férias',
                'sem_atribuicao' => true,
                'ano_letivo' => $anoAtivo
            ];
            return view('ferias/professor_index', $data);
        }

        // Calcular saldo
        $saldo = $this->atribuicaoModel->calcularSaldo($userNif, $anoAtivo['id_anoletivo']);

        // Obter pedidos
        $pedidos = $this->pedidoModel->getPedidosProfessor($userNif, $anoAtivo['id_anoletivo']);

        // Obter ano anterior
        $anoAnterior = $this->anoLetivoModel
                            ->where('id_anoletivo <', $anoAtivo['id_anoletivo'])
                            ->orderBy('id_anoletivo', 'DESC')
                            ->first();

        // Dados do ano anterior
        $diasAtribuidosAnterior = 0;
        $diasGozadosAnterior = 0;
        
        if ($anoAnterior) {
            // Obter atribuição do ano anterior
            $atribuicaoAnterior = $this->atribuicaoModel->getAtribuicaoProfessor($userNif, $anoAnterior['id_anoletivo']);
            
            if ($atribuicaoAnterior) {
                $diasAtribuidosAnterior = $atribuicaoAnterior['dias_total'];
            }
            
            // Calcular dias efetivamente gozados no ano anterior
            $diasGozadosAnterior = $this->atribuicaoModel->calcularDiasGastos($userNif, $anoAnterior['id_anoletivo']);
        }
        
        // Se não há atribuição no ano anterior mas há valores guardados na atribuição atual
        if ($diasAtribuidosAnterior == 0 && isset($atribuicao['dias_atribuidos_anterior'])) {
            $diasAtribuidosAnterior = $atribuicao['dias_atribuidos_anterior'];
        }
        if ($diasGozadosAnterior == 0 && isset($atribuicao['dias_gozados_anterior'])) {
            $diasGozadosAnterior = $atribuicao['dias_gozados_anterior'];
        }

        // Obter faltas que descontam no ano atual
        $faltasDesconto = $this->faltasDescontoModel
                               ->where('user_nif', $userNif)
                               ->where('anoletivo_id_desconto', $anoAtivo['id_anoletivo'])
                               ->orderBy('data_falta', 'DESC')
                               ->findAll();

        $data = [
            'title' => 'Minhas Férias',
            'atribuicao' => $atribuicao,
            'saldo' => $saldo,
            'pedidos' => $pedidos,
            'ano_letivo' => $anoAtivo,
            'ano_anterior' => $anoAnterior,
            'dias_atribuidos_anterior' => $diasAtribuidosAnterior,
            'dias_gozados_anterior' => $diasGozadosAnterior,
            'faltas_desconto' => $faltasDesconto
        ];

        return view('ferias/professor_index', $data);
    }

    /**
     * Página para marcar período de férias
     */
    public function marcar()
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return redirect()->to('/login');
        }

        $userNif = $userData['NIF'] ?? null;
        if (!$userNif) {
            return redirect()->to('/ferias')->with('error', 'Utilizador sem NIF associado');
        }

        $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
        $atribuicao = $this->atribuicaoModel->getAtribuicaoProfessor($userNif, $anoAtivo['id_anoletivo']);
        
        if (!$atribuicao) {
            return redirect()->to('/ferias')->with('error', 'Não tem dias de férias atribuídos');
        }

        $saldo = $this->atribuicaoModel->calcularSaldo($userNif, $anoAtivo['id_anoletivo']);

        if ($saldo['dias_disponiveis'] <= 0) {
            return redirect()->to('/ferias')->with('warning', 'Não tem dias disponíveis para marcar');
        }

        // Verificar se existe pedido pendente (submetido/em aprovação)
        $pedidoPendente = $this->pedidoModel
            ->where('user_nif', $userNif)
            ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
            ->whereIn('estado', ['submetido', 'em_aprovacao'])
            ->first();

        // Verificar se existe pedido em processo de remarcação
        $pedidoRemarcacao = $this->pedidoModel
            ->where('user_nif', $userNif)
            ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
            ->whereIn('estado', ['remarcacao_solicitada', 'remarcacao_aprovada'])
            ->first();

        // Obter configuração do período permitido
        $configuracao = $this->configuracaoModel->getConfiguracaoAnoAtivo();

        $data = [
            'title' => 'Marcar Férias',
            'saldo' => $saldo,
            'ano_letivo' => $anoAtivo,
            'atribuicao' => $atribuicao,
            'pedido_pendente' => $pedidoPendente,
            'pedido_remarcacao' => $pedidoRemarcacao,
            'configuracao' => $configuracao,
            'permite_fora_periodo' => (bool) ($atribuicao['permite_marcar_fora_periodo'] ?? 0),
            'obriga_totalidade' => (bool) ($atribuicao['obriga_totalidade_dias'] ?? 1)
        ];

        return view('ferias/marcar', $data);
    }

    /**
     * Submeter pedido de férias (AJAX)
     */
    public function submeterPedido()
    {
        try {
            $userData = $this->getEffectiveUser();
            if (!$userData) {
                return $this->failUnauthorized('Não autenticado');
            }

            $userNif = $userData['NIF'] ?? null;
            
            if (!$userNif) {
                return $this->fail('Utilizador sem NIF associado');
            }

            $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
            
            if (!$anoAtivo) {
                return $this->fail('Nenhum ano letivo ativo');
            }

            // Obter dados do request
            $requestData = $this->request->getJSON(true);
            
            $periodos = $requestData['periodos'] ?? [];
            $observacoes = $requestData['observacoes'] ?? '';
            $motivoAcumulacao = trim($requestData['motivo_acumulacao'] ?? '');

        if (empty($periodos)) {
            return $this->fail('É necessário selecionar pelo menos um período');
        }

        // VALIDAR: Verificar se já existe pedido pendente para este ano letivo
        $pedidosPendentes = $this->pedidoModel
            ->where('user_nif', $userNif)
            ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
            ->whereIn('estado', ['submetido', 'em_aprovacao'])
            ->countAllResults();

        if ($pedidosPendentes > 0) {
            return $this->fail('Já existe um pedido pendente para este ano letivo. Deve cancelar o pedido anterior antes de submeter um novo.');
        }

        // Buscar atribuição do professor para verificar permissões especiais
        $atribuicao = $this->atribuicaoModel->getAtribuicaoProfessor($userNif, $anoAtivo['id_anoletivo']);
        if (!$atribuicao) {
            return $this->fail('Não tem dias de férias atribuídos para este ano letivo');
        }
        
        $permiteForaPeriodo = (bool) ($atribuicao['permite_marcar_fora_periodo'] ?? 0);
        $obrigaTotalidade = (bool) ($atribuicao['obriga_totalidade_dias'] ?? 1);

        // Validar e calcular dias de cada período
        $periodosValidados = [];
        $totalDias = 0;

        foreach ($periodos as $periodo) {
            $dataInicio = $periodo['data_inicio'] ?? '';
            $dataFim = $periodo['data_fim'] ?? '';

            // Validar período (APENAS se não tiver permissão especial)
            if (!$permiteForaPeriodo) {
                $validacao = validar_periodo_ferias($dataInicio, $dataFim, $anoAtivo['anoletivo']);
                if (!$validacao['valido']) {
                    return $this->fail($validacao['erro']);
                }
                $diasUteis = $validacao['dias_uteis'];
            } else {
                // Se permite marcar fora do período, calcular dias úteis sem validar datas
                $validacao = validar_periodo_ferias($dataInicio, $dataFim, null); // null = sem validação de período
                if (!$validacao['valido']) {
                    return $this->fail($validacao['erro']);
                }
                $diasUteis = $validacao['dias_uteis'];
            }

            // Verificar conflitos
            $conflito = verificar_conflito_ferias($userNif, $dataInicio, $dataFim);
            if ($conflito['tem_conflito']) {
                return $this->fail($conflito['mensagem']);
            }

            $diasUteis = $validacao['dias_uteis'];
            $totalDias += $diasUteis;

            $periodosValidados[] = [
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'dias_uteis' => $diasUteis,
                'observacoes' => $periodo['observacoes'] ?? null
            ];
        }

        // Verificar saldo
        $verificacaoSaldo = verificar_saldo_ferias($userNif, $anoAtivo['id_anoletivo'], $totalDias);
        if (!$verificacaoSaldo['tem_saldo']) {
            return $this->fail("Saldo insuficiente. Disponível: {$verificacaoSaldo['dias_disponiveis']} dias. Solicitado: {$totalDias} dias.");
        }

        // VALIDAR: O pedido deve contemplar TODOS os dias atribuídos (APENAS se obriga_totalidade_dias estiver ativo)
        $saldo = $this->atribuicaoModel->calcularSaldo($userNif, $anoAtivo['id_anoletivo']);
        $diasDisponiveis = $saldo['dias_disponiveis'];
        
        $requerAcumulacao = false;
        $diasSobrantes = 0;
        
        if ($obrigaTotalidade && $totalDias < $diasDisponiveis) {
            // Obrigação total: deve marcar tudo
            return $this->fail("Deve marcar a totalidade dos dias disponíveis ({$diasDisponiveis} dias). Apenas marcou {$totalDias} dias.");
        } elseif ($totalDias > $diasDisponiveis) {
            return $this->fail("O total de dias ({$totalDias}) excede os dias disponíveis ({$diasDisponiveis}).");
        } elseif (!$obrigaTotalidade && $totalDias < $diasDisponiveis) {
            // Sem obrigação total: submissão parcial requer pedido de acumulação
            if (empty($motivoAcumulacao)) {
                return $this->fail("Está a marcar {$totalDias} dias dos {$diasDisponiveis} disponíveis. Para submeter um pedido parcial é obrigatório indicar o motivo do pedido de acumulação de férias (art.º 89 do ECD).");
            }
            $requerAcumulacao = true;
            $diasSobrantes = $diasDisponiveis - $totalDias;
        }

        // Determinar se é uma remarcação (contar pedidos anteriores cancelados por remarcação)
        $numRemarcacao = $this->pedidoModel
            ->where('user_nif', $userNif)
            ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
            ->where('estado', 'cancelado')
            ->like('observacoes_secretaria', 'para remarca')
            ->countAllResults();

        // Criar pedido
        $pedidoId = $this->pedidoModel->criarPedido(
            $userNif,
            $anoAtivo['id_anoletivo'],
            $periodosValidados,
            $observacoes
        );

        if (!$pedidoId) {
            return $this->fail('Erro ao criar pedido de férias');
        }

        // Registar numero de remarcação se aplicável
        if ($numRemarcacao > 0) {
            $this->pedidoModel->update($pedidoId, ['numero_remarcacao' => $numRemarcacao]);
        }

        // ── Gerar PDF principal do pedido de férias ──────────────────────
        try {
            $caminhoDocumentoPrincipal = $this->gerarPDFFerias($pedidoId);
            if ($caminhoDocumentoPrincipal) {
                $this->pedidoModel->update($pedidoId, ['documento_pdf' => $caminhoDocumentoPrincipal]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar PDF do pedido na submissão: ' . $e->getMessage());
        }
        // ────────────────────────────────────────────────────────────────

        // ── Pedido de Acumulação de Férias ──────────────────────────────
        if ($requerAcumulacao) {
            $this->pedidoModel->update($pedidoId, [
                'requer_acumulacao'  => 1,
                'dias_sobrantes'     => $diasSobrantes,
                'motivo_acumulacao'  => $motivoAcumulacao,
                'acumulacao_estado'  => 'pendente',
            ]);
            // Gerar PDF de acumulação
            $caminhoAcumulacao = $this->gerarPDFAcumulacao($pedidoId);
            if ($caminhoAcumulacao) {
                $this->pedidoModel->update($pedidoId, ['doc_acumulacao_pdf' => $caminhoAcumulacao]);
            }
        }
        // ────────────────────────────────────────────────────────────────

        // Enviar email à secretaria
        try {
            $this->enviarEmailSecretariaNovoPedido($pedidoId);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao enviar email de notificação: ' . $e->getMessage());
        }

        log_activity(
            'ferias',
            'create',
            $pedidoId,
            'Pedido de férias submetido pelo professor (NIF: ' . $userNif . '). Total: ' . $totalDias . ' dias em ' . count($periodosValidados) . ' período(s)',
            null,
            ['total_dias' => $totalDias, 'periodos' => count($periodosValidados)]
        );

        // Notificar secretaria (levels 3, 6, 7, 8, 9) sobre novo pedido
        try {
            notificar_utilizadores_por_level(
                [3, 6, 7, 8, 9],
                'ferias',
                'warning',
                'Novo pedido de férias',
                'O professor (NIF: ' . $userNif . ') submeteu um pedido de férias de ' . $totalDias . ' dias.',
                base_url('ferias/secretaria'),
                (int) ($userData['id'] ?? 0)
            );
        } catch (\Exception $e) {
            log_message('warning', 'Erro ao notificar secretaria sobre pedido de férias: ' . $e->getMessage());
        }

        return $this->respondCreated([
            'success'           => true,
            'message'           => 'Pedido de férias submetido com sucesso',
            'pedido_id'         => $pedidoId,
            'requer_acumulacao' => $requerAcumulacao,
        ]);
        
        } catch (\Exception $e) {
            log_message('error', 'Erro ao submeter pedido de férias: ' . $e->getMessage());
            return $this->fail('Erro interno: ' . $e->getMessage());
        }
    }

    /**
     * Solicitar remarcação de férias (cancela pedido anterior e libera dias)
     */
    public function solicitarRemarcacao($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return $this->failUnauthorized('Não autenticado');
        }

        $userNif = $userData['NIF'] ?? null;
        if (!$userNif) {
            return $this->fail('Utilizador sem NIF associado');
        }

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido) {
            return $this->fail('Pedido não encontrado');
        }

        if ($pedido['user_nif'] != $userNif) {
            return $this->failForbidden('Sem permissão para alterar este pedido');
        }

        $estadosPermitidos = ['aprovado', 'aguarda_assinatura', 'concluido'];
        if (!in_array($pedido['estado'], $estadosPermitidos)) {
            return $this->fail('Apenas pedidos aprovados podem ser remarcados. Estado atual: ' . $pedido['estado']);
        }

        // Guardar estado anterior em observações para poder restaurar
        $moradaPost         = trim($this->request->getPost('morada') ?? '');
        $datasPropostasPost = trim($this->request->getPost('datas_propostas') ?? '');
        $updateData = [
            'estado'                     => 'remarcacao_solicitada',
            'motivo_remarcacao'          => $this->request->getPost('motivo_remarcacao') ?? '',
            'datas_remarcacao_propostas' => $datasPropostasPost ?: null,
            'observacoes_secretaria'     => 'Remarcação solicitada pelo funcionário em ' . date('d/m/Y H:i') . ' [estado_anterior:' . $pedido['estado'] . ']. Aguarda aprovação da secretaria.',
        ];
        if (!empty($moradaPost)) {
            $updateData['observacoes_professor'] = $moradaPost;
        }
        $this->pedidoModel->update($pedidoId, $updateData);

        // Se o professor forneceu telefone e ainda não tem na tabela user, atualizar
        $telefonePost = trim($this->request->getPost('telefone') ?? '');
        if (!empty($telefonePost)) {
            $professorData = $this->userModel->where('NIF', $userNif)->first();
            if ($professorData && empty($professorData['telefone'])) {
                $this->userModel->update($professorData['id'], ['telefone' => $telefonePost]);
            }
        }

        // Gerar PDF do documento de Alteração de Férias
        $pdfRemarcacao = $this->gerarPDFRemarcacao($pedidoId);
        $pdfGerado = false;
        if ($pdfRemarcacao) {
            $this->pedidoModel->update($pedidoId, ['documento_remarcacao' => $pdfRemarcacao]);
            $pdfGerado = true;
        }

        $this->logModel->registrarAcao(
            $pedidoId,
            $userNif,
            'Pedido de Remarcação',
            "Professor solicitou remarcação do pedido #{$pedidoId}. Estado anterior: {$pedido['estado']}. Aguarda aprovação da secretaria.",
            $userData['id'] ?? null
        );

        log_activity(
            'ferias',
            'remarcacao_solicitada',
            $pedidoId,
            'Professor (NIF: ' . $userNif . ') solicitou remarcação do pedido #' . $pedidoId,
            ['estado' => $pedido['estado']],
            ['estado' => 'remarcacao_solicitada']
        );

        // Sem emails nesta fase

        return $this->respond([
            'success'   => true,
            'pdf_gerado' => $pdfGerado,
            'message'   => 'Pedido de remarcação enviado com sucesso. Aguarde a aprovação da secretaria.'
        ]);
    }

    /**
     * Aprovar solicitação de remarcação (secretaria).
     * Aplica imediatamente as novas datas propostas ao pedido existente.
     */
    public function aprovarRemarcacao($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $pedido = $this->pedidoModel->find($pedidoId);
            if (!$pedido) {
                return $this->failNotFound('Pedido não encontrado');
            }

            if ($pedido['estado'] !== 'remarcacao_solicitada') {
                return $this->fail('Apenas pedidos com remarcação solicitada podem ser aprovados');
            }

            // Obter datas propostas
            $datasPropostas = json_decode($pedido['datas_remarcacao_propostas'] ?? '[]', true);
            if (empty($datasPropostas)) {
                return $this->fail('Não existem datas propostas para aplicar na remarcação');
            }

            // Calcular dias úteis para cada período proposto
            $periodosNovos = [];
            $totalDiasNovo = 0;
            foreach ($datasPropostas as $p) {
                $dataInicio = $p['data_inicio'] ?? '';
                $dataFim    = $p['data_fim']    ?? '';
                if (empty($dataInicio) || empty($dataFim)) {
                    continue;
                }
                $diasUteis      = calcular_dias_uteis($dataInicio, $dataFim);
                $totalDiasNovo += $diasUteis;
                $periodosNovos[] = [
                    'pedido_id'   => $pedidoId,
                    'data_inicio' => $dataInicio,
                    'data_fim'    => $dataFim,
                    'dias_uteis'  => $diasUteis,
                ];
            }

            if (empty($periodosNovos)) {
                return $this->fail('As datas propostas são inválidas');
            }

            // Recuperar estado anterior guardado nas observações
            $estadoAnterior = 'aprovado';
            if (preg_match('/\[estado_anterior:([a-z_]+)\]/', $pedido['observacoes_secretaria'] ?? '', $m)) {
                $estadoAnterior = $m[1];
            }

            // Substituir períodos existentes pelos novos
            $periodosModel = new \App\Models\FeriasPeriodoModel();
            $periodosModel->where('pedido_id', $pedidoId)->delete();
            foreach ($periodosNovos as $novoPeriodo) {
                $periodosModel->insert($novoPeriodo);
            }

            // Actualizar pedido com as novas datas (sem cancelar)
            $numRemarcacao = (int)($pedido['numero_remarcacao'] ?? 0) + 1;
            $this->pedidoModel->update($pedidoId, [
                'estado'                     => $estadoAnterior,
                'total_dias'                 => $totalDiasNovo,
                'numero_remarcacao'          => $numRemarcacao,
                'datas_remarcacao_propostas' => null,
                'motivo_remarcacao'          => null,
                'observacoes_secretaria'     => 'Remarcação #' . $numRemarcacao . ' aprovada e efetivada pela secretaria em ' . date('d/m/Y H:i') . ' (' . $userData['name'] . '). Novas datas aplicadas ao pedido.',
            ]);

            $this->logModel->registrarAcao(
                $pedidoId,
                $pedido['user_nif'],
                'Remarcação Aprovada e Efetivada',
                "Remarcação #{$numRemarcacao} aprovada por {$userData['name']}. Novas datas aplicadas. Total de dias: {$totalDiasNovo}. Estado restaurado para: {$estadoAnterior}.",
                $userData['id']
            );

            log_activity(
                'ferias',
                'remarcacao_aprovada',
                $pedidoId,
                'Remarcação #' . $numRemarcacao . ' do pedido #' . $pedidoId . ' aprovada e efetivada por ' . $userData['name'],
                ['estado' => 'remarcacao_solicitada'],
                ['estado' => $estadoAnterior]
            );

            return $this->respond([
                'success' => true,
                'message' => 'Remarcação aprovada. As novas datas foram aplicadas ao pedido.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao aprovar remarcação: ' . $e->getMessage());
            return $this->fail('Erro ao processar aprovação: ' . $e->getMessage());
        }
    }

    /**
     * Rejeitar solicitação de remarcação (secretaria)
     */
    public function rejeitarRemarcacao($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $json   = $this->request->getJSON();
            $motivo = $json->motivo ?? 'Sem motivo especificado';

            $pedido = $this->pedidoModel->find($pedidoId);
            if (!$pedido) {
                return $this->failNotFound('Pedido não encontrado');
            }

            if ($pedido['estado'] !== 'remarcacao_solicitada') {
                return $this->fail('Apenas pedidos com remarcação solicitada podem ser rejeitados');
            }

            // Recuperar estado anterior guardado nas observações
            $estadoAnterior = 'aprovado';
            if (preg_match('/\[estado_anterior:([a-z_]+)\]/', $pedido['observacoes_secretaria'] ?? '', $m)) {
                $estadoAnterior = $m[1];
            }

            // Apagar ficheiro PDF de remarcação, se existir
            if (!empty($pedido['documento_remarcacao'])) {
                $ficheiroPdf = FCPATH . $pedido['documento_remarcacao'];
                if (file_exists($ficheiroPdf)) {
                    @unlink($ficheiroPdf);
                }
            }

            $this->pedidoModel->update($pedidoId, [
                'estado'                 => $estadoAnterior,
                'documento_remarcacao'   => null,
                'observacoes_secretaria' => 'Remarcação rejeitada pela secretaria em ' . date('d/m/Y H:i') . '. Motivo: ' . $motivo,
            ]);

            $this->logModel->registrarAcao(
                $pedidoId,
                $pedido['user_nif'],
                'Remarcação Rejeitada',
                "Remarcação rejeitada por {$userData['name']}. Motivo: {$motivo}. Estado restaurado para: {$estadoAnterior}.",
                $userData['id']
            );

            log_activity(
                'ferias',
                'remarcacao_rejeitada',
                $pedidoId,
                'Remarcação do pedido #' . $pedidoId . ' rejeitada por ' . $userData['name'] . '. Motivo: ' . $motivo,
                ['estado' => 'remarcacao_solicitada'],
                ['estado' => $estadoAnterior],
                'warning'
            );

            // Sem emails nesta fase

            return $this->respond([
                'success' => true,
                'message' => 'Remarcação rejeitada. O pedido foi restaurado ao estado anterior.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao rejeitar remarcação: ' . $e->getMessage());
            return $this->fail('Erro ao processar rejeição: ' . $e->getMessage());
        }
    }

    /**
     * Professor confirma que vai remarcar (após secretaria aprovar).
     * Aplica as datas propostas ao pedido existente, sem o cancelar.
     */
    public function confirmarRemarcacao($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return $this->failUnauthorized('Não autenticado');
        }

        $userNif = $userData['NIF'] ?? null;

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido) {
            return $this->fail('Pedido não encontrado');
        }

        if ($pedido['user_nif'] != $userNif) {
            return $this->failForbidden('Sem permissão');
        }

        if ($pedido['estado'] !== 'remarcacao_aprovada') {
            return $this->fail('A remarcação ainda não foi aprovada pela secretaria');
        }

        // Obter datas propostas
        $datasPropostas = json_decode($pedido['datas_remarcacao_propostas'] ?? '[]', true);
        if (empty($datasPropostas)) {
            return $this->fail('Não existem datas propostas para a remarcação');
        }

        // Calcular dias úteis para cada período proposto
        $periodosNovos = [];
        $totalDiasNovo = 0;
        foreach ($datasPropostas as $p) {
            $dataInicio = $p['data_inicio'] ?? '';
            $dataFim    = $p['data_fim']    ?? '';
            if (empty($dataInicio) || empty($dataFim)) {
                continue;
            }
            $diasUteis      = calcular_dias_uteis($dataInicio, $dataFim);
            $totalDiasNovo += $diasUteis;
            $periodosNovos[] = [
                'pedido_id'   => $pedidoId,
                'data_inicio' => $dataInicio,
                'data_fim'    => $dataFim,
                'dias_uteis'  => $diasUteis,
            ];
        }

        if (empty($periodosNovos)) {
            return $this->fail('As datas propostas são inválidas');
        }

        // Recuperar estado anterior guardado nas observações
        $estadoAnterior = 'aprovado';
        if (preg_match('/\[estado_anterior:([a-z_]+)\]/', $pedido['observacoes_secretaria'] ?? '', $m)) {
            $estadoAnterior = $m[1];
        }

        // Substituir períodos existentes pelos novos
        $periodosModel = new \App\Models\FeriasPeriodoModel();
        $periodosModel->where('pedido_id', $pedidoId)->delete();
        foreach ($periodosNovos as $novoPeriodo) {
            $periodosModel->insert($novoPeriodo);
        }

        // Actualizar pedido com as novas datas (sem cancelar)
        $numRemarcacao = (int)($pedido['numero_remarcacao'] ?? 0) + 1;
        $this->pedidoModel->update($pedidoId, [
            'estado'                     => $estadoAnterior,
            'total_dias'                 => $totalDiasNovo,
            'numero_remarcacao'          => $numRemarcacao,
            'datas_remarcacao_propostas' => null,
            'motivo_remarcacao'          => null,
            'observacoes_secretaria'     => 'Remarcação #' . $numRemarcacao . ' efetivada em ' . date('d/m/Y H:i') . '. Novas datas aplicadas ao pedido existente.',
        ]);

        $this->logModel->registrarAcao(
            $pedidoId,
            $userNif,
            'Remarcação Efetivada',
            "Professor aplicou remarcação #{$numRemarcacao} ao pedido #{$pedidoId}. Total de dias: {$totalDiasNovo}. Estado: {$estadoAnterior}.",
            $userData['id'] ?? null
        );

        log_activity(
            'ferias',
            'remarcacao_efetivada',
            $pedidoId,
            'Professor (NIF: ' . $userNif . ') efetivou remarcação #' . $numRemarcacao . ' do pedido #' . $pedidoId . '. Novas datas aplicadas.',
            ['estado' => 'remarcacao_aprovada'],
            ['estado' => $estadoAnterior]
        );

        return $this->respond([
            'success' => true,
            'message' => 'Remarcação efetivada com sucesso. As novas datas estão agora ativas no pedido.',
        ]);
    }

    /**
     * Cancelar pedido pendente (submetido/em_aprovacao)
     */
    public function cancelarPedido($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return $this->failUnauthorized('Não autenticado');
        }

        $userNif = $userData['NIF'] ?? null;
        if (!$userNif) {
            return $this->fail('Utilizador sem NIF associado');
        }

        // Buscar pedido
        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido) {
            return $this->fail('Pedido não encontrado');
        }

        // Verificar se o pedido pertence ao utilizador
        if ($pedido['user_nif'] != $userNif) {
            return $this->failForbidden('Sem permissão para cancelar este pedido');
        }

        // Verificar se o pedido está num estado que permite cancelamento pelo professor
        $estadosPermitidos = ['submetido', 'em_aprovacao'];
        if (!in_array($pedido['estado'], $estadosPermitidos)) {
            return $this->fail('Apenas pedidos pendentes (submetidos ou em aprovação) podem ser cancelados. Estado atual: ' . $pedido['estado']);
        }

        // Alterar estado do pedido para 'cancelado'
        $this->pedidoModel->update($pedidoId, [
            'estado' => 'cancelado',
            'observacoes_secretaria' => 'Cancelado pelo funcionário em ' . date('d/m/Y H:i')
        ]);

        // Registrar no log
        $this->logModel->registrarAcao(
            $pedidoId,
            $pedido['user_nif'],
            'pedido_cancelado',
            "Pedido #{$pedidoId} cancelado pelo funcionário",
            $userData['id'] ?? null
        );

        log_activity(
            'ferias',
            'delete',
            $pedidoId,
            'Pedido #' . $pedidoId . ' cancelado pelo professor (NIF: ' . $userNif . ')',
            ['estado' => $pedido['estado']],
            ['estado' => 'cancelado'],
            'warning'
        );

        // Notificar secretaria sobre o cancelamento
        try {
            notificar_utilizadores_por_level(
                [3],
                'ferias',
                'warning',
                'Pedido de férias cancelado',
                'O professor (NIF: ' . $pedido['user_nif'] . ') cancelou o pedido de férias #' . $pedidoId . ' que estava pendente de aprovação.',
                base_url('ferias/secretaria'),
                (int) ($this->getEffectiveUser()['id'] ?? 0)
            );
        } catch (\Exception $e) {
            log_message('warning', 'Erro ao notificar secretaria sobre cancelamento de férias: ' . $e->getMessage());
        }

        return $this->respond([
            'success' => true,
            'message' => 'Pedido cancelado com sucesso. Os dias foram devolvidos ao seu saldo.'
        ]);
    }

    /**
     * Obter detalhes completos de um pedido (AJAX)
     */
    public function detalhesPedido($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return $this->failUnauthorized('Não autenticado');
        }

        try {
            // Buscar pedido com períodos
            $pedido = $this->pedidoModel->getPedidoComPeriodos($pedidoId);
            
            if (!$pedido) {
                return $this->failNotFound('Pedido não encontrado');
            }

            // Verificar permissões
            $userLevel = $userData['level'] ?? 0;
            if ($userLevel < 3 && $pedido['user_nif'] != $userData['NIF']) {
                return $this->failForbidden('Sem permissão para ver este pedido');
            }

            // Buscar aprovador (se existir)
            $aprovador = null;
            if (!empty($pedido['aprovado_por'])) {
                $aprovador = $this->userModel->find($pedido['aprovado_por']);
            }

            // Buscar rejeitador (se existir)
            $rejeitador = null;
            if (!empty($pedido['rejeitado_por'])) {
                $rejeitador = $this->userModel->find($pedido['rejeitado_por']);
            }

            // Buscar logs do pedido
            $logs = $this->logModel
                ->where('pedido_id', $pedidoId)
                ->orderBy('criado_em', 'DESC')
                ->limit(10)
                ->findAll();

            // Montar resposta
            $detalhes = [
                'pedido' => $pedido,
                'aprovador' => $aprovador,
                'rejeitador' => $rejeitador,
                'logs' => $logs
            ];

            return $this->respond([
                'success' => true,
                'data' => $detalhes
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar detalhes do pedido: ' . $e->getMessage());
            return $this->fail('Erro ao buscar detalhes do pedido');
        }
    }

    /**
     * Download do documento de férias (PDF gerado)
     */
    public function downloadDocumento($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return redirect()->to('/login');
        }

        $pedido = $this->pedidoModel->find($pedidoId);

        if (!$pedido) {
            return redirect()->to('/ferias')->with('error', 'Pedido não encontrado');
        }

        // Verificar permissões
        $userLevel = $userData['level'] ?? 0;
        if ($userLevel < 3 && $pedido['user_nif'] != $userData['NIF']) {
            return redirect()->to('/ferias')->with('error', 'Sem permissão para aceder a este documento');
        }

        $caminhoDocumento = FCPATH . $pedido['documento_pdf'];

        if (!file_exists($caminhoDocumento)) {
            return redirect()->to('/ferias')->with('error', 'Documento não encontrado');
        }

        return $this->response->download($caminhoDocumento, null)->setFileName(basename($caminhoDocumento));
    }

    /**
     * Download do documento assinado (PDF com assinatura do professor)
     */
    public function downloadDocumentoAssinado($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return redirect()->to('/login');
        }

        $pedido = $this->pedidoModel->find($pedidoId);

        if (!$pedido) {
            return redirect()->to('/ferias')->with('error', 'Pedido não encontrado');
        }

        // Verificar permissões
        $userLevel = $userData['level'] ?? 0;
        if ($userLevel < 3 && $pedido['user_nif'] != $userData['NIF']) {
            return redirect()->to('/ferias')->with('error', 'Sem permissão para aceder a este documento');
        }

        // Verificar se existe documento assinado
        if (empty($pedido['documento_assinado'])) {
            return redirect()->back()->with('error', 'Documento assinado ainda não foi enviado');
        }

        $caminhoDocumento = FCPATH . $pedido['documento_assinado'];

        if (!file_exists($caminhoDocumento)) {
            return redirect()->back()->with('error', 'Ficheiro do documento assinado não encontrado');
        }

        return $this->response->download($caminhoDocumento, null)->setFileName(basename($caminhoDocumento));
    }

    /**
     * Download ZIP de todos os PDFs assinados (filtrado por ano e/ou professor)
     */
    public function downloadZipAssinados()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        if (!class_exists('ZipArchive')) {
            return redirect()->back()->with('error', 'Extensão ZIP não disponível no servidor.');
        }

        // Filtros (mesmos parâmetros da página todos-pedidos)
        $ano      = $this->request->getGet('ano');
        $professor = $this->request->getGet('professor');

        $builder = $this->pedidoModel
            ->select('ferias_pedido.id, ferias_pedido.documento_assinado, user.name as nome_professor, ano_letivo.anoletivo as ano')
            ->join('user', 'user.NIF = ferias_pedido.user_nif')
            ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_pedido.anoletivo_id')
            ->where('ferias_pedido.documento_assinado IS NOT NULL', null, false)
            ->where('ferias_pedido.documento_assinado !=', '');

        if ($ano) {
            $builder->where('ano_letivo.anoletivo', $ano);
        }
        if ($professor) {
            $builder->like('user.name', $professor);
        }

        $pedidos = $builder->findAll();

        if (empty($pedidos)) {
            return redirect()->back()->with('error', 'Não existem documentos assinados com os filtros selecionados.');
        }

        // Criar ficheiro ZIP temporário
        $zipPath = sys_get_temp_dir() . '/ferias_assinados_' . time() . '.zip';
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return redirect()->back()->with('error', 'Erro ao criar ficheiro ZIP.');
        }

        $adicionados = 0;
        foreach ($pedidos as $pedido) {
            $caminho = FCPATH . $pedido['documento_assinado'];
            if (file_exists($caminho)) {
                // Nome do ficheiro no ZIP: Ano_NomeProfessor_idPedido.pdf
                $nomeNoZip = ($pedido['ano'] ?? 'ano') . '_'
                    . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $pedido['nome_professor']) . '_'
                    . $pedido['id'] . '.pdf';
                $zip->addFile($caminho, $nomeNoZip);
                $adicionados++;
            }
        }
        $zip->close();

        if ($adicionados === 0) {
            @unlink($zipPath);
            return redirect()->back()->with('error', 'Nenhum ficheiro encontrado no servidor.');
        }

        $nomeDownload = 'ferias_assinados' . ($ano ? '_' . $ano . '-' . ($ano + 1) : '') . '.zip';

        return $this->response
            ->setHeader('Content-Type', 'application/zip')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $nomeDownload . '"')
            ->setHeader('Content-Length', (string) filesize($zipPath))
            ->setBody(file_get_contents($zipPath))
            ->send();

        @unlink($zipPath);
    }

    /**
     * Upload do documento assinado
     */
    public function uploadDocumentoAssinado($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return $this->failUnauthorized('Não autenticado');
        }

        $pedido = $this->pedidoModel->find($pedidoId);

        if (!$pedido || $pedido['user_nif'] != $userData['NIF']) {
            return $this->fail('Pedido não encontrado ou sem permissão');
        }

        if ($pedido['estado'] != 'aguarda_assinatura') {
            // Permitir também para pedidos submetidos/em aprovação (professor assina antes da aprovação)
            if (!in_array($pedido['estado'], ['submetido', 'em_aprovacao'])) {
                return $this->fail('Não é possível carregar o documento assinado neste estado do pedido');
            }
        }

        $file = $this->request->getFile('documento_assinado');

        if (!$file || !$file->isValid()) {
            return $this->fail('Nenhum ficheiro válido foi enviado');
        }

        // Validar tipo de ficheiro (apenas PDF)
        if ($file->getMimeType() != 'application/pdf') {
            return $this->fail('Apenas ficheiros PDF são permitidos');
        }

        // Criar diretório do professor se não existir
        $pastaUser = FCPATH . 'docs/' . $pedido['user_nif'];
        if (!is_dir($pastaUser)) {
            mkdir($pastaUser, 0755, true);
        }

        // Mover ficheiro
        $nomeArquivo = "ferias_assinado_{$pedidoId}.pdf";
        $caminhoDestino = "docs/{$pedido['user_nif']}/{$nomeArquivo}";

        if ($file->move($pastaUser, $nomeArquivo)) {
            // Atualizar pedido — se já foi aprovado (aguarda_assinatura), concluir;
            // se ainda está submetido/em_aprovacao, apenas guardar o documento assinado
            if ($pedido['estado'] === 'aguarda_assinatura') {
                $this->pedidoModel->registrarDocumentoAssinado($pedidoId, $caminhoDestino);
            } else {
                $this->pedidoModel->update($pedidoId, [
                    'documento_assinado'   => $caminhoDestino,
                    'upload_assinatura_em' => date('Y-m-d H:i:s'),
                ]);
            }

            log_activity(
                'ferias',
                'upload_documento_assinado',
                $pedidoId,
                'Professor (NIF: ' . $pedido['user_nif'] . ') enviou documento assinado do pedido #' . $pedidoId,
                null,
                ['ficheiro' => $nomeArquivo]
            );

            // Enviar email à secretaria
            $this->enviarEmailSecretariaDocumentoAssinado($pedidoId);

            return $this->respond([
                'success' => true,
                'message' => 'Documento assinado enviado com sucesso'
            ]);
        }

        return $this->fail('Erro ao fazer upload do documento');
    }

    /**
     * Download do PDF de Alteração de Férias assinado
     */
    public function downloadRemarcacaoAssinado($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return redirect()->to(base_url('login'));
        }

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido) {
            return $this->fail('Pedido não encontrado');
        }

        // Professor só pode aceder ao seu próprio pedido
        if (in_array($userData['level'], [4, 5])) {
            if ($pedido['user_nif'] != ($userData['NIF'] ?? null)) {
                return $this->failForbidden('Sem permissão');
            }
        }

        if (empty($pedido['documento_remarcacao_assinado'])) {
            return $this->fail('Documento de alteração assinado não disponível');
        }

        $caminho = FCPATH . $pedido['documento_remarcacao_assinado'];
        if (!file_exists($caminho)) {
            return $this->fail('Ficheiro não encontrado no servidor');
        }

        $ext  = strtolower(pathinfo($caminho, PATHINFO_EXTENSION));
        $mime = $ext === 'pdf' ? 'application/pdf' : 'image/' . $ext;

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="alteracao_ferias_assinada_' . $pedidoId . '.' . $ext . '"')
            ->setBody(file_get_contents($caminho));
    }

    /**
     * Upload do PDF de Alteração de Férias assinado (professor)
     */
    public function uploadDocumentoRemarcacaoAssinado($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return $this->failUnauthorized('Não autenticado');
        }

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido || $pedido['user_nif'] != ($userData['NIF'] ?? null)) {
            return $this->fail('Pedido não encontrado ou sem permissão');
        }

        if (empty($pedido['documento_remarcacao'])) {
            return $this->fail('Este pedido não possui documento de Alteração de Férias');
        }

        $file = $this->request->getFile('documento_remarcacao_assinado');
        if (!$file || !$file->isValid()) {
            return $this->fail('Nenhum ficheiro válido foi enviado');
        }

        // Validar tipo de ficheiro
        $mimeType = $file->getMimeType();
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($mimeType, $allowedMimes)) {
            return $this->fail('Apenas ficheiros PDF, JPG ou PNG são permitidos');
        }

        $pastaUser = FCPATH . 'docs/' . $pedido['user_nif'];
        if (!is_dir($pastaUser)) {
            mkdir($pastaUser, 0755, true);
        }

        $ext = $file->getClientExtension();
        $nomeArquivo    = "alteracao_ferias_assinada_{$pedidoId}.{$ext}";
        $caminhoDestino = "docs/{$pedido['user_nif']}/{$nomeArquivo}";

        if ($file->move($pastaUser, $nomeArquivo)) {
            $this->pedidoModel->update($pedidoId, [
                'documento_remarcacao_assinado' => $caminhoDestino
            ]);

            log_activity(
                'ferias',
                'upload_remarcacao_assinada',
                $pedidoId,
                'Professor (NIF: ' . $pedido['user_nif'] . ') enviou Alteração de Férias assinada do pedido #' . $pedidoId,
                null,
                ['ficheiro' => $nomeArquivo]
            );

            return $this->respond([
                'success' => true,
                'message' => 'Documento de Alteração assinado enviado com sucesso'
            ]);
        }

        return $this->fail('Erro ao fazer upload do documento');
    }

    // ====================================================
    // PÁGINAS - SECRETARIA (NÍVEL 3+)
    // ====================================================

    /**
     * Dashboard da secretaria - gestão de férias
     */
    public function secretaria()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        $anoAtivo = $this->anoLetivoModel->getAnoAtivo();

        // Estatísticas
        $estatisticas = $this->pedidoModel->getEstatisticas($anoAtivo['id_anoletivo'] ?? null);

        // Pedidos pendentes
        $pedidosPendentes = $this->pedidoModel->getPedidosPendentes($anoAtivo['id_anoletivo'] ?? null);

        // Professores sem atribuição de dias para o ano letivo ativo
        $professoresSemAtribuicao = 0;
        if ($anoAtivo) {
            $nifComAtribuicao = array_column(
                $this->atribuicaoModel->where('anoletivo_id', $anoAtivo['id_anoletivo'])->findAll(),
                'user_nif'
            );
            $queryProf = $this->userModel->whereIn('level', [4, 5])->where('status', 1);
            if (!empty($nifComAtribuicao)) {
                $queryProf->whereNotIn('NIF', $nifComAtribuicao);
            }
            $professoresSemAtribuicao = $queryProf->countAllResults();
        }

        $data = [
            'title' => 'Gestão de Férias',
            'estatisticas' => $estatisticas,
            'pedidos_pendentes' => $pedidosPendentes,
            'professores_sem_atribuicao' => $professoresSemAtribuicao,
            'ano_letivo' => $anoAtivo,
            'configuracao' => $this->configuracaoModel->getConfiguracaoAnoAtivo()
        ];

        return view('ferias/secretaria_index', $data);
    }

    /**
     * Página de atribuição de dias de férias
     */
    public function atribuir()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        $anoAtivo = $this->anoLetivoModel->getAnoAtivo();

        // Obter todos os utilizadores exceto inativos e pendentes (nível >= 1)
        $professores = $this->userModel->select('user.id, user.NIF, user.name, user.cod_funcionario, user.telefone, user.grupo_id, user.categoria, user.escola_servico, escolas.nome AS nome_escola')
                                      ->join('escolas', 'escolas.id = user.escola_servico', 'left')
                                      ->where('user.level >=', 1)
                                      ->whereNotIn('user.status', [0, 2])
                                      ->where('user.NIF IS NOT NULL')
                                      ->where('user.NIF !=', '')
                                      ->orderBy('user.name', 'ASC')
                                      ->findAll();

        // Lista de escolas para o filtro
        $escolas = $this->db->table('escolas')->orderBy('nome', 'ASC')->get()->getResultArray();

        // Buscar ano anterior
        $anoAnterior = null;
        if ($anoAtivo) {
            $anoAnterior = $this->anoLetivoModel
                ->where('anoletivo', $anoAtivo['anoletivo'] - 1)
                ->first();
        }

        // Para cada professor, obter atribuição
        foreach ($professores as &$professor) {
            $professor['atribuicao'] = $this->atribuicaoModel->getAtribuicaoProfessor(
                $professor['NIF'],
                $anoAtivo['id_anoletivo']
            );
            
            // Buscar atribuição do ano anterior
            $professor['atribuicao_ano_anterior'] = null;
            $professor['dias_gozados_ano_anterior'] = 0;
            
            if ($anoAnterior) {
                $atribAnterior = $this->atribuicaoModel->getAtribuicaoProfessor(
                    $professor['NIF'],
                    $anoAnterior['id_anoletivo']
                );
                
                if ($atribAnterior) {
                    $professor['atribuicao_ano_anterior'] = $atribAnterior;
                    
                    // Calcular dias efetivamente gozados no ano anterior
                    $pedidosAnteriores = $this->pedidoModel
                        ->where('user_nif', $professor['NIF'])
                        ->where('anoletivo_id', $anoAnterior['id_anoletivo'])
                        ->whereIn('estado', ['aprovado', 'aguarda_assinatura', 'assinado', 'concluido'])
                        ->findAll();
                    
                    $diasGozados = 0;
                    foreach ($pedidosAnteriores as $pedAnt) {
                        $diasGozados += $pedAnt['total_dias'];
                    }
                    $professor['dias_gozados_ano_anterior'] = $diasGozados;
                }
            }
            
            if ($professor['atribuicao']) {
                $professor['saldo'] = $this->atribuicaoModel->calcularSaldo(
                    $professor['NIF'],
                    $anoAtivo['id_anoletivo']
                );
                
                // Verificar se já foi enviado email de notificação desta atribuição
                $emailLog = $this->logModel
                    ->where('user_nif', $professor['NIF'])
                    ->where('acao', 'Email notificação atribuição')
                    ->where('criado_em >=', $professor['atribuicao']['atualizado_em'])
                    ->first();
                
                $professor['email_enviado'] = $emailLog ? true : false;
            }
        }

        // Obter valores ENUM do campo categoria para modal de edição
        $query = $this->db->query("SHOW COLUMNS FROM user WHERE Field = 'categoria'");
        $row = $query->getRow();
        $categorias = [];
        
        if ($row && preg_match("/^enum\(\'(.*)\'\)$/", $row->Type, $matches)) {
            $categorias = explode("','", $matches[1]);
        }

        $data = [
            'title' => 'Atribuir Dias de Férias',
            'professores' => $professores,
            'ano_letivo' => $anoAtivo,
            'categorias' => $categorias,
            'escolas' => $escolas
        ];

        return view('ferias/secretaria_atribuir', $data);
    }

    /**
     * Salvar atribuição de dias (AJAX)
     */
    public function salvarAtribuicao()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $userNif = $this->request->getPost('user_nif');
            $diasBase = (int) $this->request->getPost('dias_base');
            $diasAjuste = (int) $this->request->getPost('dias_ajuste');
            $diasGozadosAnterior = (int) $this->request->getPost('dias_gozados_anterior');
            $diasAtribuidosAnterior = (int) $this->request->getPost('dias_atribuidos_anterior');
            $diasExtra = (int) $this->request->getPost('dias_extra');
            $observacoes = $this->request->getPost('observacoes');
            $permiteMarcarForaPeriodo = (int) $this->request->getPost('permite_marcar_fora_periodo');
            $obrigaTotalidadeDias = (int) $this->request->getPost('obriga_totalidade_dias');
            $alinea = $this->request->getPost('alinea') ?: null;
            // Validar alinea: só pode ser a-f ou null
            if ($alinea !== null && !in_array($alinea, ['a','b','c','d','e','f'])) {
                $alinea = null;
            }

            // Se a alínea está activa os campos dias_base/dias_extra ficam readonly no form;
            // o valor submetido é o que o utilizador escolheu (0 ou manter dias actuais).
            // Garantir pelo menos que não chegam valores negativos por engano:
            if ($alinea !== null) {
                $diasBase  = max(0, $diasBase);
                $diasExtra = max(0, $diasExtra);
            }

            // Campos usados para registo retroativo (não mais necessários)
            $idAtribuicaoAnoAnterior = (int) $this->request->getPost('id_atribuicao_ano_anterior');
            $diasAtribuidosAnoAnterior = (int) $this->request->getPost('dias_atribuidos_ano_anterior');

            // Validação básica
            if (!$userNif) {
                return $this->fail('NIF do professor é obrigatório');
            }
            
            // Validar se o funcionário tem cod_funcionario e categoria preenchidos
            $professor = $this->userModel->where('NIF', $userNif)->first();
            if (!$professor) {
                return $this->fail('Funcionário não encontrado');
            }
            
            if (empty($professor['cod_funcionario'])) {
                return $this->fail('O funcionário não tem o Código de Funcionário preenchido. Por favor, preencha este campo na gestão de utilizadores antes de atribuir férias.');
            }
            
            if (empty($professor['categoria'])) {
                return $this->fail('O funcionário não tem a Categoria preenchida. Por favor, preencha este campo na gestão de utilizadores antes de atribuir férias.');
            }

            $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
            if (!$anoAtivo) {
                return $this->fail('Nenhum ano letivo ativo encontrado');
            }

            // =============================================
            // REGISTO RETROATIVO DO ANO ANTERIOR
            // =============================================
            if ($diasAtribuidosAnoAnterior > 0) {
                // Buscar ano anterior
                $anoAnterior = $this->anoLetivoModel
                    ->where('anoletivo', $anoAtivo['anoletivo'] - 1)
                    ->first();
                
                if ($anoAnterior) {
                    // Calcular ajuste retroativo: dias_atribuidos - dias_gozados
                    $ajusteRetroativo = $diasAtribuidosAnoAnterior - $diasGozadosAnterior;
                    
                    // Verificar se já existe atribuição do ano anterior
                    $atribAnoAnteriorExistente = $this->atribuicaoModel
                        ->where('user_nif', $userNif)
                        ->where('anoletivo_id', $anoAnterior['id_anoletivo'])
                        ->first();
                    
                    if ($atribAnoAnteriorExistente) {
                        // Atualizar registo existente do ano anterior
                        $this->atribuicaoModel->update($atribAnoAnteriorExistente['id'], [
                            'dias_base' => $diasAtribuidosAnoAnterior,
                            'dias_ajuste' => 0, // No ano anterior não há ajuste de anos anteriores
                            'dias_extra' => 0,
                            'dias_gozados_anterior' => $diasGozadosAnterior,
                            'observacoes' => 'Registo retroativo atualizado',
                            'atribuido_por' => $userData['id']
                        ]);
                        
                        $this->logModel->registrarAcao(
                            null,
                            $userNif,
                            'Atualização retroativa ano anterior',
                            "Ano {$anoAnterior['anoletivo']}: Dias atribuídos: {$diasAtribuidosAnoAnterior}, Dias gozados: {$diasGozadosAnterior}",
                            $userData['id']
                        );
                    } else {
                        // Criar novo registo retroativo do ano anterior
                        $this->atribuicaoModel->insert([
                            'user_nif' => $userNif,
                            'anoletivo_id' => $anoAnterior['id_anoletivo'],
                            'dias_base' => $diasAtribuidosAnoAnterior,
                            'dias_ajuste' => 0,
                            'dias_extra' => 0,
                            'dias_gozados_anterior' => $diasGozadosAnterior,
                            'observacoes' => 'Registo retroativo criado',
                            'atribuido_por' => $userData['id']
                        ]);
                        
                        $this->logModel->registrarAcao(
                            null,
                            $userNif,
                            'Criação retroativa ano anterior',
                            "Ano {$anoAnterior['anoletivo']}: Dias atribuídos: {$diasAtribuidosAnoAnterior}, Dias gozados: {$diasGozadosAnterior}",
                            $userData['id']
                        );
                    }
                }
            }

            // =============================================
            // ATRIBUIÇÃO DO ANO ATUAL
            // =============================================
            $result = $this->atribuicaoModel->atribuirFerias(
                $userNif,
                $anoAtivo['id_anoletivo'],
                $diasBase,
                $diasAjuste,
                $diasExtra,
                $observacoes,
                $userData['id'],
                $diasGozadosAnterior,
                $permiteMarcarForaPeriodo,
                $obrigaTotalidadeDias,
                $diasAtribuidosAnterior,
                $alinea
            );

            if ($result) {
                // Log
                $this->logModel->registrarAcao(
                    null,
                    $userNif,
                    'Atribuição de férias',
                    "Dias base: {$diasBase}, Ajuste: {$diasAjuste}, Dias atribuídos anterior: {$diasAtribuidosAnterior}, Dias gozados anterior: {$diasGozadosAnterior}, Extra: {$diasExtra}",
                    $userData['id']
                );

                log_activity(
                    'ferias',
                    'create',
                    null,
                    'Atribuição de férias guardada para professor NIF: ' . $userNif . ' (' . ($diasBase + $diasAjuste + $diasExtra) . ' dias no total)',
                    null,
                    ['dias_base' => $diasBase, 'dias_ajuste' => $diasAjuste, 'dias_extra' => $diasExtra, 'user_nif' => $userNif]
                );

                return $this->respond([
                    'success' => true,
                    'message' => 'Atribuição guardada com sucesso'
                ]);
            }

            // Se chegou aqui, houve erro na atribuição
            $errors = $this->atribuicaoModel->errors();
            return $this->fail($errors ? implode(', ', $errors) : 'Erro ao guardar atribuição');
            
        } catch (\Exception $e) {
            log_message('error', 'Erro em salvarAtribuicao: ' . $e->getMessage());
            return $this->fail('Erro ao processar atribuição: ' . $e->getMessage());
        }
    }

    /**
     * Enviar email de notificação da atribuição de férias
     */
    public function enviarEmailAtribuicao($atribuicaoId)
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            // Buscar atribuição
            $atribuicao = $this->atribuicaoModel->find($atribuicaoId);
            if (!$atribuicao) {
                return $this->fail('Atribuição não encontrada');
            }

            // Buscar dados do funcionário
            $funcionario = $this->userModel->where('NIF', $atribuicao['user_nif'])->first();
            if (!$funcionario) {
                return $this->fail('Funcionário não encontrado');
            }

            if (empty($funcionario['email'])) {
                return $this->fail('Funcionário não tem email registado');
            }

            // Buscar ano letivo
            $anoLetivo = $this->anoLetivoModel->find($atribuicao['anoletivo_id']);
            
            // Calcular saldo completo
            $saldo = $this->atribuicaoModel->calcularSaldo($atribuicao['user_nif'], $atribuicao['anoletivo_id']);
            
            // Preparar dados do email
            $emailData = [
                'nome' => $funcionario['name'],
                'ano' => $anoLetivo ? $anoLetivo['anoletivo'] : date('Y'),
                'ano_seguinte' => $anoLetivo ? ($anoLetivo['anoletivo'] + 1) : date('Y'),
                'dias_base' => $atribuicao['dias_base'],
                'dias_ajuste' => $atribuicao['dias_ajuste'],
                'dias_atribuidos_anterior' => $atribuicao['dias_atribuidos_anterior'] ?? 0,
                'dias_gozados_anterior' => $atribuicao['dias_gozados_anterior'] ?? 0,
                'dias_extra' => $atribuicao['dias_extra'],
                'dias_total' => $saldo['dias_total'],
                'dias_gastos' => $saldo['dias_gastos'],
                'dias_desconto_faltas' => $saldo['dias_desconto_faltas'],
                'dias_disponiveis' => $saldo['dias_disponiveis'],
                'observacoes' => $atribuicao['observacoes'],
                'link_ferias' => base_url('ferias')
            ];

            // Enviar email
            $emailService = \Config\Services::email();
            $emailService->setFrom('noreply@aejoadebarros.pt', 'Sistema de Gestão Escolar');
            $emailService->setTo($funcionario['email']);
            $emailService->setSubject('Atribuição de Dias de Férias - ' . $emailData['ano'] . '/' . $emailData['ano_seguinte']);
            
            $mensagem = view('emails/ferias_atribuicao', $emailData);
            $emailService->setMessage($mensagem);

            if ($emailService->send()) {
                // Registar log
                $this->logModel->registrarAcao(
                    null,
                    $funcionario['NIF'],
                    'Email notificação atribuição',
                    "Enviado email com atribuição de {$atribuicao['dias_total']} dias para o ano {$emailData['ano']}",
                    $userData['id']
                );

                return $this->respond([
                    'success' => true,
                    'message' => 'Email enviado com sucesso para ' . $funcionario['email']
                ]);
            } else {
                log_message('error', 'Erro ao enviar email de atribuição: ' . $emailService->printDebugger(['headers']));
                return $this->fail('Erro ao enviar email. Verifique a configuração do servidor de email.');
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro em enviarEmailAtribuicao: ' . $e->getMessage());
            return $this->fail('Erro ao processar envio de email: ' . $e->getMessage());
        }
    }

    /**
     * Obter feriados para o date picker (AJAX)
     */
    public function obterFeriados()
    {
        try {
            $userData = $this->getEffectiveUser();
            if (!$userData) {
                return $this->failUnauthorized('Não autenticado');
            }

            // Obter ano letivo ativo
            $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
            if (!$anoAtivo) {
                return $this->fail('Nenhum ano letivo ativo');
            }

            // Obter configuração para determinar período
            $configuracao = $this->configuracaoModel->getConfiguracaoAnoAtivo();
            
            // Definir intervalo de datas para buscar feriados (período permitido + 1 ano antes e depois)
            $dataInicio = $configuracao && !empty($configuracao['data_inicio_permitida']) 
                ? date('Y-m-d', strtotime($configuracao['data_inicio_permitida'] . ' -1 year'))
                : date('Y') . '-01-01';
            
            $dataFim = $configuracao && !empty($configuracao['data_fim_permitida']) 
                ? date('Y-m-d', strtotime($configuracao['data_fim_permitida'] . ' +1 year'))
                : (date('Y') + 1) . '-12-31';

            // Buscar feriados ativos no intervalo
            $feriados = $this->feriadosModel
                ->where('ativo', 1)
                ->where('data >=', $dataInicio)
                ->where('data <=', $dataFim)
                ->orderBy('data', 'ASC')
                ->findAll();

            // Formatar apenas as datas para o frontend
            $feriadosFormatados = array_map(function($feriado) {
                return $feriado['data']; // Retorna no formato YYYY-MM-DD
            }, $feriados);

            return $this->respond([
                'success' => true,
                'feriados' => $feriadosFormatados
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro em obterFeriados: ' . $e->getMessage());
            return $this->fail('Erro ao buscar feriados: ' . $e->getMessage());
        }
    }

    /**
     * Aprovar pedido de férias
     */
    public function aprovarPedido($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $observacoes = $this->request->getPost('observacoes');

            $result = $this->pedidoModel->aprovarPedido($pedidoId, $userData['id'], $observacoes);

            if ($result) {
                // Recarregar pedido para verificar se PDF já foi gerado na submissão
                $pedidoAtualizado = $this->pedidoModel->find($pedidoId);

                try {
                    if (empty($pedidoAtualizado['documento_pdf'])) {
                        // PDF ainda não existe — gerar agora
                        $caminhoDocumento = $this->gerarPDFFerias($pedidoId);
                        if ($caminhoDocumento) {
                            $this->pedidoModel->marcarAguardaAssinatura($pedidoId, $caminhoDocumento);
                            try {
                                $this->enviarEmailProfessorAprovacao($pedidoId, $caminhoDocumento);
                            } catch (\Exception $e) {
                                log_message('error', 'Erro ao enviar email aprovação: ' . $e->getMessage());
                            }
                        }
                    } elseif (!empty($pedidoAtualizado['documento_assinado'])) {
                        // PDF existe e já está assinado → concluir diretamente
                        $this->pedidoModel->update($pedidoId, ['estado' => 'concluido']);
                    } else {
                        // PDF existe mas ainda não foi assinado → aguarda assinatura
                        $this->pedidoModel->update($pedidoId, ['estado' => 'aguarda_assinatura']);
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Erro ao processar PDF após aprovação: ' . $e->getMessage());
                    // Continua mesmo se PDF falhar
                }

                log_activity(
                    'ferias',
                    'approve',
                    $pedidoId,
                    'Pedido de férias #' . $pedidoId . ' aprovado por ' . ($userData['name'] ?? 'secretaria'),
                    null,
                    ['estado' => 'aguarda_assinatura']
                );

                // Notificar o professor
                try {
                    $pedido = $this->pedidoModel->find($pedidoId);
                    if ($pedido) {
                        $professor = $this->userModel->where('NIF', $pedido['user_nif'])->first();
                        if ($professor) {
                            criar_notificacao(
                                (int) $professor['id'],
                                'ferias',
                                'success',
                                'Férias aprovadas',
                                'O seu pedido de férias #' . $pedidoId . ' foi aprovado. Aguarda a sua assinatura.',
                                base_url('ferias')
                            );
                        }
                    }
                } catch (\Exception $e) {
                    log_message('warning', 'Erro ao criar notificação de aprovação de férias: ' . $e->getMessage());
                }

                return $this->respond([
                    'success' => true,
                    'message' => 'Pedido aprovado com sucesso'
                ]);
            }

            return $this->fail('Erro ao aprovar pedido');
            
        } catch (\Exception $e) {
            log_message('error', 'Erro em aprovarPedido: ' . $e->getMessage());
            return $this->fail('Erro ao processar aprovação: ' . $e->getMessage());
        }
    }

    /**
     * Rejeitar pedido de férias
     */
    public function rejeitarPedido($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $motivo = $this->request->getPost('motivo');

            if (empty($motivo)) {
                return $this->fail('É necessário indicar o motivo da rejeição');
            }

            $result = $this->pedidoModel->rejeitarPedido($pedidoId, $userData['id'], $motivo);

            if ($result) {
                // Enviar email ao professor (opcional - não bloqueia)
                try {
                    $this->enviarEmailProfessorRejeicao($pedidoId, $motivo);
                } catch (\Exception $e) {
                    log_message('error', 'Erro ao enviar email rejeição: ' . $e->getMessage());
                }

                log_activity(
                    'ferias',
                    'reject',
                    $pedidoId,
                    'Pedido de férias #' . $pedidoId . ' rejeitado por ' . ($userData['name'] ?? 'secretaria') . '. Motivo: ' . $motivo,
                    null,
                    ['estado' => 'rejeitado', 'motivo' => $motivo],
                    'warning'
                );

                // Notificar o professor
                try {
                    $pedido = $this->pedidoModel->find($pedidoId);
                    if ($pedido) {
                        $professor = $this->userModel->where('NIF', $pedido['user_nif'])->first();
                        if ($professor) {
                            criar_notificacao(
                                (int) $professor['id'],
                                'ferias',
                                'danger',
                                'Férias rejeitadas',
                                'O seu pedido de férias #' . $pedidoId . ' foi rejeitado. Motivo: ' . $motivo,
                                base_url('ferias')
                            );
                        }
                    }
                } catch (\Exception $e) {
                    log_message('warning', 'Erro ao criar notificação de rejeição de férias: ' . $e->getMessage());
                }

                return $this->respond([
                    'success' => true,
                    'message' => 'Pedido rejeitado'
                ]);
            }

            return $this->fail('Erro ao rejeitar pedido');
            
        } catch (\Exception $e) {
            log_message('error', 'Erro em rejeitarPedido: ' . $e->getMessage());
            return $this->fail('Erro ao processar rejeição: ' . $e->getMessage());
        }
    }

    // ====================================================
    // MÉTODOS AUXILIARES
    // ====================================================

    /**
     * Gerar PDF de férias
     */
    private function gerarPDFFerias($pedidoId)
    {
        try {
            // Carregar configuração
            $config = config('FeriasConfig');
            
            // Carregar biblioteca PDF (assumindo uso de TCPDF ou similar)
            $pedido = $this->pedidoModel->getPedidoComPeriodos($pedidoId);

            if (!$pedido) {
                return false;
            }

            // Buscar atribuição para obter dias_base, dias_ajuste, dias_extra
            $atribuicao = $this->atribuicaoModel
                ->where('user_nif', $pedido['user_nif'])
                ->where('anoletivo_id', $pedido['anoletivo_id'])
                ->first();

            // Buscar dados do ano anterior
            $anoAtual = $this->anoLetivoModel->find($pedido['anoletivo_id']);
            $anoAnterior = null;
            $diasAnoAnterior = 0;
            $diasGozadosAnterior = 0;

            // PRIORIDADE 1: Usar dias_atribuidos_anterior da atribuição atual (guardado na gestão)
            if (isset($atribuicao['dias_atribuidos_anterior']) && $atribuicao['dias_atribuidos_anterior'] > 0) {
                $diasAnoAnterior = $atribuicao['dias_atribuidos_anterior'];
            } else {
                // FALLBACK: Buscar do ano letivo anterior
                if ($anoAtual) {
                    $anoAnterior = $this->anoLetivoModel
                        ->where('anoletivo', $anoAtual['anoletivo'] - 1)
                        ->first();
                    
                    if ($anoAnterior) {
                        $atribuicaoAnterior = $this->atribuicaoModel
                            ->where('user_nif', $pedido['user_nif'])
                            ->where('anoletivo_id', $anoAnterior['id_anoletivo'])
                            ->first();
                        
                        if ($atribuicaoAnterior) {
                            $diasAnoAnterior = $atribuicaoAnterior['dias_total'];
                        }
                    }
                }
            }
            
            // PRIORIDADE 1: Usar dias_gozados_anterior da atribuição (guardado na gestão)
            if (isset($atribuicao['dias_gozados_anterior']) && $atribuicao['dias_gozados_anterior'] > 0) {
                $diasGozadosAnterior = $atribuicao['dias_gozados_anterior'];
            } else {
                // FALLBACK: Calcular dos pedidos aprovados no ano anterior
                if ($anoAtual) {
                    if (!$anoAnterior) {
                        $anoAnterior = $this->anoLetivoModel
                            ->where('anoletivo', $anoAtual['anoletivo'] - 1)
                            ->first();
                    }
                    
                    if ($anoAnterior) {
                        $pedidosAnteriores = $this->pedidoModel
                            ->where('user_nif', $pedido['user_nif'])
                            ->where('anoletivo_id', $anoAnterior['id_anoletivo'])
                            ->whereIn('estado', ['aprovado', 'aguarda_assinatura', 'assinado', 'concluido'])
                            ->findAll();
                        
                        foreach ($pedidosAnteriores as $pedAnt) {
                            $diasGozadosAnterior += $pedAnt['total_dias'];
                        }
                    }
                }
            }

            // Buscar professor
            $professor = $this->userModel->where('NIF', $pedido['user_nif'])->first();

            // Adicionar dados extras ao pedido
            $pedido['dias_base'] = $atribuicao['dias_base'] ?? $config->dias_base_default;
            $pedido['dias_ajuste'] = $atribuicao['dias_ajuste'] ?? 0;
            $pedido['dias_extra'] = $atribuicao['dias_extra'] ?? 0;
            $pedido['dias_ano_anterior'] = $diasAnoAnterior;
            $pedido['dias_gozados_anterior'] = $diasGozadosAnterior;
            $pedido['dias_concedidos'] = $pedido['total_dias'];
            
            // Buscar faltas do ano letivo atual que descontam nas férias
            $faltas = $this->faltasDescontoModel
                ->where('user_nif', $pedido['user_nif'])
                ->where('anoletivo_id_desconto', $pedido['anoletivo_id'])
                ->findAll();
            
            // Separar e calcular faltas por tipo
            $faltasJustificadas = [];
            $faltasInjustificadas = [];
            $totalFaltasJustificadas = 0;
            $totalFaltasInjustificadas = 0;
            
            foreach ($faltas as $falta) {
                if ($falta['tipo_falta'] === 'injustificada') {
                    $faltasInjustificadas[] = $falta;
                    $totalFaltasInjustificadas += $falta['dias_desconto'];
                } else {
                    // artigo_102 ou artigo_134_n3
                    $faltasJustificadas[] = $falta;
                    $totalFaltasJustificadas += $falta['dias_desconto'];
                }
            }
            
            $pedido['faltas_justificadas'] = $totalFaltasJustificadas;
            $pedido['faltas_injustificadas'] = $totalFaltasInjustificadas;
            $pedido['faltas_justificadas_lista'] = $faltasJustificadas;
            $pedido['faltas_injustificadas_lista'] = $faltasInjustificadas;
            
            // Dados da escola (da configuração)
            $dadosEscola = $config->getDadosEscola();
            $pedido = array_merge($pedido, $dadosEscola);
            
            // Dados do professor
            $pedido['cod_funcionario'] = $professor['cod_funcionario'] ?? '';
            $pedido['categoria'] = $professor['categoria'] ?? '';
            $pedido['residencia_ferias'] = $pedido['observacoes_professor'] ?? '';

            // Caminho do logo do Ministério da Educação
            $logoPath = FCPATH . 'ME_logo2024_horizontal_pdf.png';
            
            // Criar diretório se não existir
            $pastaUser = FCPATH . $config->pasta_documentos . '/' . $pedido['user_nif'];
            if (!is_dir($pastaUser)) {
                mkdir($pastaUser, $config->permissoes_pasta, true);
            }

            // Formato do nome: [Ano letivo]_[Primeiro nome]_[último_nome]_[cod_funcionario].pdf
            $nomeParts = explode(' ', trim($professor['name']));
            $primeiroNome = $nomeParts[0] ?? 'Professor';
            $ultimoNome = end($nomeParts);
            if ($ultimoNome === $primeiroNome) {
                $ultimoNome = '';
            }
            $codFuncionario = $professor['cod_funcionario'] ?? $pedido['user_nif'];
            $anoLetivoNome = ($anoAtual['anoletivo']) . '-' . ($anoAtual['anoletivo'] + 1);
            
            $nomeArquivo = "{$anoLetivoNome}_{$primeiroNome}_{$ultimoNome}_{$codFuncionario}.pdf";
            $nomeArquivo = str_replace(['  ', ' '], '_', $nomeArquivo); // Remover espaços duplos
            $caminhoCompleto = $pastaUser . '/' . $nomeArquivo;
            $caminhoRelativo = "{$config->pasta_documentos}/{$pedido['user_nif']}/{$nomeArquivo}";

            // Gerar PDF via View
            $html = view('ferias/documento_pdf', [
                'pedido' => $pedido,
                'logoPath' => $logoPath
            ]);

            // Usar biblioteca PDF do CodeIgniter ou TCPDF
            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('chroot', FCPATH);
            
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            file_put_contents($caminhoCompleto, $dompdf->output());

            return $caminhoRelativo;

        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar PDF de férias: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Regenerar PDF de um pedido já aprovado
     */
    public function regenerarPDF($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            // Verificar se o pedido existe
            $pedido = $this->pedidoModel->find($pedidoId);
            if (!$pedido) {
                return $this->fail('Pedido não encontrado');
            }

            // Verificar se o pedido está aprovado e ainda não foi assinado
            if (!in_array($pedido['estado'], ['aprovado', 'aguarda_assinatura'])) {
                return $this->fail('Apenas pedidos aprovados podem ter o PDF regenerado');
            }

            if (!empty($pedido['documento_assinado'])) {
                return $this->fail('Não é possível regenerar PDF de um documento já assinado e devolvido');
            }

            // Gerar PDF
            $caminhoDocumento = $this->gerarPDFFerias($pedidoId);

            if ($caminhoDocumento) {
                // Atualizar o caminho do documento
                $this->pedidoModel->update($pedidoId, [
                    'documento_pdf' => $caminhoDocumento
                ]);

                // Log
                $this->logModel->registrarAcao(
                    $pedidoId,
                    $pedido['user_nif'],
                    'PDF regenerado',
                    "PDF do pedido regenerado por {$userData['name']}",
                    $userData['id']
                );

                return $this->respond([
                    'success' => true,
                    'message' => 'PDF regenerado com sucesso',
                    'caminho' => $caminhoDocumento
                ]);
            }

            return $this->fail('Erro ao gerar PDF');
            
        } catch (\Exception $e) {
            log_message('error', 'Erro em regenerarPDF: ' . $e->getMessage());
            return $this->fail('Erro ao processar regeneração: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // PDF ACUMULAÇÃO DE FÉRIAS (art.º 89 ECD)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Gera o PDF "Pedido de Acumulação de Férias".
     */
    private function gerarPDFAcumulacao($pedidoId): string|false
    {
        try {
            $config  = config('FeriasConfig');
            $pedido  = $this->pedidoModel->getPedidoComPeriodos($pedidoId);
            if (!$pedido) {
                return false;
            }

            $professor = $this->userModel->where('NIF', $pedido['user_nif'])->first();
            $pedido['categoria']       = $professor['categoria'] ?? '';
            $pedido['cod_funcionario'] = $professor['cod_funcionario'] ?? '';

            $pastaUser = FCPATH . $config->pasta_documentos . '/' . $pedido['user_nif'];
            if (!is_dir($pastaUser)) {
                mkdir($pastaUser, $config->permissoes_pasta, true);
            }

            $nomeArquivo     = 'acumulacao_ferias_' . $pedidoId . '.pdf';
            $caminhoCompleto = $pastaUser . '/' . $nomeArquivo;
            $caminhoRelativo = $config->pasta_documentos . '/' . $pedido['user_nif'] . '/' . $nomeArquivo;

            $logoRP     = FCPATH . 'ME_logo2024_horizontal_pdf.png';
            $logoEscola = FCPATH . 'esjb_logo_com_nome_pdf.png';

            $html = view('ferias/documento_pdf_acumulacao', [
                'pedido'     => $pedido,
                'logoRP'     => $logoRP,
                'logoEscola' => $logoEscola,
            ]);

            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('chroot', FCPATH);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            file_put_contents($caminhoCompleto, $dompdf->output());

            return $caminhoRelativo;

        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar PDF de acumulação: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Download do PDF de acumulação gerado (professor / secretaria)
     */
    public function downloadDocumentoAcumulacao($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return $this->failUnauthorized('Não autenticado');
        }

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido || empty($pedido['doc_acumulacao_pdf'])) {
            return $this->failNotFound('Documento de acumulação não encontrado');
        }

        $isSecretaria = isset($userData['level']) && $userData['level'] >= 3 && !in_array($userData['level'], [4, 5]);
        if (!$isSecretaria && $pedido['user_nif'] != ($userData['NIF'] ?? null)) {
            return $this->failForbidden('Acesso negado');
        }

        $caminho = FCPATH . $pedido['doc_acumulacao_pdf'];
        if (!file_exists($caminho)) {
            return $this->failNotFound('Ficheiro não encontrado no servidor');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="acumulacao_ferias_' . $pedidoId . '.pdf"')
            ->setBody(file_get_contents($caminho));
    }

    /**
     * Upload do PDF de acumulação assinado (professor)
     */
    public function uploadDocumentoAcumulacaoAssinado($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return $this->failUnauthorized('Não autenticado');
        }

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido || !$pedido['requer_acumulacao']) {
            return $this->fail('Pedido não encontrado ou não requer acumulação');
        }

        if ($pedido['user_nif'] != ($userData['NIF'] ?? null)) {
            return $this->failForbidden('Acesso negado');
        }

        $ficheiro = $this->request->getFile('doc_acumulacao_assinado');
        if (!$ficheiro || !$ficheiro->isValid()) {
            return $this->fail('Nenhum ficheiro enviado ou ficheiro inválido');
        }

        $tiposPermitidos = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($ficheiro->getMimeType(), $tiposPermitidos)) {
            return $this->fail('Tipo de ficheiro não permitido. Use PDF, JPG ou PNG');
        }

        $config    = config('FeriasConfig');
        $pastaUser = FCPATH . $config->pasta_documentos . '/' . $pedido['user_nif'];
        if (!is_dir($pastaUser)) {
            mkdir($pastaUser, $config->permissoes_pasta, true);
        }

        $ext         = $ficheiro->getClientExtension();
        $nomeArquivo = 'acumulacao_assinada_' . $pedidoId . '.' . $ext;
        $ficheiro->move($pastaUser, $nomeArquivo);

        $caminhoRelativo = $config->pasta_documentos . '/' . $pedido['user_nif'] . '/' . $nomeArquivo;
        $this->pedidoModel->update($pedidoId, [
            'doc_acumulacao_assinado' => $caminhoRelativo,
            'upload_acumulacao_em'    => date('Y-m-d H:i:s'),
        ]);

        log_activity('ferias', 'upload', $pedidoId,
            'Upload do doc acumulação assinado pelo professor (NIF: ' . $pedido['user_nif'] . ')');

        return $this->respond(['success' => true, 'message' => 'Documento de acumulação enviado com sucesso']);
    }

    /**
     * Download do PDF de acumulação assinado (professor / secretaria)
     */
    public function downloadDocumentoAcumulacaoAssinado($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return $this->failUnauthorized('Não autenticado');
        }

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido || empty($pedido['doc_acumulacao_assinado'])) {
            return $this->failNotFound('Documento assinado não encontrado');
        }

        $isSecretaria = isset($userData['level']) && $userData['level'] >= 3 && !in_array($userData['level'], [4, 5]);
        if (!$isSecretaria && $pedido['user_nif'] != ($userData['NIF'] ?? null)) {
            return $this->failForbidden('Acesso negado');
        }

        $caminho = FCPATH . $pedido['doc_acumulacao_assinado'];
        if (!file_exists($caminho)) {
            return $this->failNotFound('Ficheiro não encontrado no servidor');
        }

        $ext     = pathinfo($caminho, PATHINFO_EXTENSION);
        $mimeMap = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
        $mime    = $mimeMap[strtolower($ext)] ?? 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="acumulacao_assinada_' . $pedidoId . '.' . $ext . '"')
            ->setBody(file_get_contents($caminho));
    }

    /**
     * Secretaria regista o despacho do diretor para a acumulação
     */
    public function registarDespachoAcumulacao($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido || !$pedido['requer_acumulacao']) {
            return $this->fail('Pedido não encontrado ou não requer acumulação');
        }

        $requestData = $this->request->getJSON(true);
        $estado      = $requestData['estado'] ?? '';

        if (!in_array($estado, ['autorizada', 'nao_autorizada'])) {
            return $this->fail('Estado de despacho inválido');
        }

        $this->pedidoModel->update($pedidoId, [
            'acumulacao_estado'       => $estado,
            'acumulacao_despacho_por' => $userData['id'],
            'acumulacao_despacho_em'  => date('Y-m-d H:i:s'),
        ]);

        // Regenerar PDF com checkbox do despacho preenchido
        $caminho = $this->gerarPDFAcumulacao($pedidoId);
        if ($caminho) {
            $this->pedidoModel->update($pedidoId, ['doc_acumulacao_pdf' => $caminho]);
        }

        $estadoLabel = $estado === 'autorizada' ? 'Autorizada' : 'Não Autorizada';
        log_activity('ferias', 'update', $pedidoId,
            "Despacho acumulação registado por {$userData['name']}: {$estadoLabel}");

        return $this->respond(['success' => true, 'message' => "Despacho registado: {$estadoLabel}"]);
    }

    /**
     * Secretaria regenera o PDF de acumulação
     */
    public function regenerarPDFAcumulacao($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido || !$pedido['requer_acumulacao']) {
            return $this->fail('Pedido não encontrado ou não requer acumulação');
        }

        $caminho = $this->gerarPDFAcumulacao($pedidoId);
        if (!$caminho) {
            return $this->fail('Erro ao regenerar PDF');
        }

        $this->pedidoModel->update($pedidoId, ['doc_acumulacao_pdf' => $caminho]);

        return $this->respond(['success' => true, 'message' => 'PDF de acumulação regenerado com sucesso']);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PDF ALTERAÇÃO DE FÉRIAS (documento de remarcação)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Gera o PDF "Alteração de Férias" para um pedido com remarcação solicitada.
     * Guarda o ficheiro na mesma pasta do utilizador e devolve o caminho relativo.
     */
    private function gerarPDFRemarcacao($pedidoId): string|false
    {
        try {
            $config  = config('FeriasConfig');
            $pedido  = $this->pedidoModel->getPedidoComPeriodos($pedidoId);

            if (!$pedido) {
                return false;
            }

            $professor = $this->userModel->where('NIF', $pedido['user_nif'])->first();
            if (!$professor) {
                return false;
            }

            // ── Detectar tipo de categoria ────────────────────────────
            $categoriaTexto = $professor['categoria'] ?? '';
            $grupoCat       = $professor['grupo_mapa_ferias'] ?? 'geral';

            if ($grupoCat === 'tecnico_superior') {
                $categoriaType = 'TS';
            } elseif (stripos($categoriaTexto, 'Assistente Operacional') !== false) {
                $categoriaType = 'AO';
            } elseif (stripos($categoriaTexto, 'Assistente Técnico') !== false ||
                      stripos($categoriaTexto, 'Assistente Tecnico') !== false) {
                $categoriaType = 'AT';
            } else {
                $categoriaType = 'Docente';
            }

            // ── Detectar vínculo ──────────────────────────────────────
            if (stripos($categoriaTexto, 'Quadro') !== false ||
                stripos($categoriaTexto, 'QZP')    !== false) {
                $vinculoType = 'Quadro';
            } elseif (stripos($categoriaTexto, 'Destacado') !== false) {
                $vinculoType = 'Destacado';
            } else {
                $vinculoType = 'Contratado';
            }

            // ── Logos ─────────────────────────────────────────────────
            $logoRP     = FCPATH . 'RP_Edu_pdf.png';
            $logoEscola = FCPATH . 'esjb_logo_com_nome_pdf.png';

            // ── Diretório ─────────────────────────────────────────────
            $pastaUser = FCPATH . $config->pasta_documentos . '/' . $pedido['user_nif'];
            if (!is_dir($pastaUser)) {
                mkdir($pastaUser, $config->permissoes_pasta, true);
            }

            // ── Nome do ficheiro ──────────────────────────────────────
            $nomeParts    = explode(' ', trim($professor['name']));
            $primeiroNome = $nomeParts[0] ?? 'Funcionario';
            $ultimoNome   = end($nomeParts);
            if ($ultimoNome === $primeiroNome) {
                $ultimoNome = '';
            }
            $nomeArquivo    = "alteracao_ferias_{$pedidoId}_{$primeiroNome}_{$ultimoNome}.pdf";
            $nomeArquivo    = preg_replace('/\s+/', '_', trim($nomeArquivo));
            $caminhoCompleto = $pastaUser . '/' . $nomeArquivo;
            $caminhoRelativo = "{$config->pasta_documentos}/{$pedido['user_nif']}/{$nomeArquivo}";

            // ── Gerar HTML e converter em PDF ─────────────────────────
            $html = view('ferias/documento_pdf_remarcacao', [
                'pedido'         => $pedido,
                'professor'      => $professor,
                'categoriaType'  => $categoriaType,
                'vinculoType'    => $vinculoType,
                'logoRP'         => $logoRP,
                'logoEscola'     => $logoEscola,
            ]);

            $options = new \Dompdf\Options();
            $options->set('isRemoteEnabled',    true);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('chroot', FCPATH);

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            file_put_contents($caminhoCompleto, $dompdf->output());

            return $caminhoRelativo;

        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar PDF de alteração de férias (pedido #' . $pedidoId . '): ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Download do PDF de Alteração de Férias (secretaria / professor)
     */
    public function downloadRemarcacao($pedidoId)
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return redirect()->to(base_url('login'));
        }

        $pedido = $this->pedidoModel->find($pedidoId);
        if (!$pedido) {
            return $this->fail('Pedido não encontrado');
        }

        // Professor só pode aceder ao seu próprio pedido
        if (in_array($userData['level'], [4, 5])) {
            if ($pedido['user_nif'] != ($userData['NIF'] ?? null)) {
                return $this->failForbidden('Sem permissão');
            }
        }

        if (empty($pedido['documento_remarcacao'])) {
            return $this->fail('Documento de alteração não disponível para este pedido');
        }

        $caminho = FCPATH . $pedido['documento_remarcacao'];
        if (!file_exists($caminho)) {
            return $this->fail('Ficheiro PDF não encontrado no servidor');
        }

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="alteracao_ferias_' . $pedidoId . '.pdf"')
            ->setBody(file_get_contents($caminho));
    }

    /**
     * Regenerar o PDF de Alteração de Férias (apenas secretaria)
     */
    public function regenerarPDFRemarcacao($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $pedido = $this->pedidoModel->find($pedidoId);
            if (!$pedido) {
                return $this->failNotFound('Pedido não encontrado');
            }

            $caminho = $this->gerarPDFRemarcacao($pedidoId);
            if (!$caminho) {
                return $this->fail('Erro ao gerar PDF de alteração');
            }

            $this->pedidoModel->update($pedidoId, ['documento_remarcacao' => $caminho]);

            log_activity(
                'ferias',
                'pdf_remarcacao_regenerado',
                $pedidoId,
                'PDF de alteração de férias regenerado por ' . $userData['name'],
                [],
                ['documento_remarcacao' => $caminho]
            );

            return $this->respond([
                'success' => true,
                'message' => 'PDF de alteração regenerado com sucesso'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro em regenerarPDFRemarcacao: ' . $e->getMessage());
            return $this->fail('Erro ao regenerar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Enviar email à secretaria sobre novo pedido
     */
    private function enviarEmailSecretariaNovoPedido($pedidoId)
    {
        $pedido = $this->pedidoModel->getPedidoComPeriodos($pedidoId);
        
        $secretarios = $this->userModel->where('level', 3)->where('status', 1)->findAll();

        foreach ($secretarios as $secretario) {
            if ($secretario['email']) {
                $assunto = "Novo Pedido de Férias - {$pedido['nome_professor']}";
                $mensagem = view('emails/ferias_novo_pedido', ['pedido' => $pedido]);
                
                enviar_email_ferias($secretario['email'], $assunto, $mensagem);
            }
        }
    }

    /**
     * Enviar email ao professor sobre atribuição
     */
    private function enviarEmailProfessorAtribuicao($professor, $totalDias)
    {
        $assunto = "Dias de Férias Atribuídos";
        $mensagem = view('emails/ferias_atribuicao', [
            'professor' => $professor,
            'total_dias' => $totalDias
        ]);

        enviar_email_ferias($professor['email'], $assunto, $mensagem);
    }

    /**
     * Enviar email ao professor sobre aprovação
     */
    private function enviarEmailProfessorAprovacao($pedidoId, $caminhoDocumento)
    {
        $pedido = $this->pedidoModel->getPedidoComPeriodos($pedidoId);
        
        $assunto = "Pedido de Férias Aprovado";
        $mensagem = view('emails/ferias_aprovado', ['pedido' => $pedido]);
        
        $caminhoCompleto = FCPATH . $caminhoDocumento;
        enviar_email_ferias($pedido['email_professor'], $assunto, $mensagem, [$caminhoCompleto]);
    }

    /**
     * Enviar email ao professor sobre rejeição
     */
    private function enviarEmailProfessorRejeicao($pedidoId, $motivo)
    {
        $pedido = $this->pedidoModel->getPedidoComPeriodos($pedidoId);
        
        $assunto = "Pedido de Férias Rejeitado";
        $mensagem = view('emails/ferias_rejeitado', [
            'pedido' => $pedido,
            'motivo' => $motivo
        ]);

        enviar_email_ferias($pedido['email_professor'], $assunto, $mensagem);
    }

    /**
     * Enviar email à secretaria sobre documento assinado
     */
    private function enviarEmailSecretariaDocumentoAssinado($pedidoId)
    {
        $pedido = $this->pedidoModel->find($pedidoId);
        
        $secretarios = $this->userModel->where('level', 3)->where('status', 1)->findAll();

        foreach ($secretarios as $secretario) {
            if ($secretario['email']) {
                $assunto = "Documento de Férias Assinado - {$pedido['user_nif']}";
                $mensagem = "O professor enviou o documento de férias assinado. Pedido ID: {$pedidoId}";
                
                enviar_email_ferias($secretario['email'], $assunto, $mensagem);
            }
        }
    }

    /**
     * Enviar email à secretaria sobre cancelamento de pedido pendente
     */
    /**
     * API: Calcular dias úteis entre datas (AJAX)
     */
    public function calcularDiasUteis()
    {
        $dataInicio = $this->request->getPost('data_inicio');
        $dataFim = $this->request->getPost('data_fim');

        if (!$dataInicio || !$dataFim) {
            return $this->fail('Datas inválidas');
        }

        $diasUteis = calcular_dias_uteis($dataInicio, $dataFim);

        return $this->respond([
            'success' => true,
            'dias_uteis' => $diasUteis
        ]);
    }

    /**
     * API: Obter feriados de um ano (para calendário)
     */
    public function getFeriados($ano)
    {
        $feriados = $this->feriadosModel->getFeriadosAno($ano);

        return $this->respond([
            'success' => true,
            'feriados' => $feriados
        ]);
    }

    // ====================================================
    // PÁGINAS ADICIONAIS - PROFESSOR
    // ====================================================

    /**
     * Página: Histórico de pedidos do professor
     */
    public function meusPedidos()
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return redirect()->to('/login')->with('error', 'É necessário fazer login');
        }

        $userNif = $userData['NIF'] ?? null;

        // Obter todos os pedidos (todos os anos)
        $pedidos = $this->pedidoModel
            ->select('ferias_pedido.*, ano_letivo.anoletivo as ano')
            ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_pedido.anoletivo_id')
            ->where('ferias_pedido.user_nif', $userNif)
            ->orderBy('ferias_pedido.criado_em', 'DESC')
            ->findAll();

        // Buscar períodos para cada pedido
        foreach ($pedidos as &$pedido) {
            $pedido['periodos'] = $this->periodoModel
                ->where('pedido_id', $pedido['id'])
                ->orderBy('data_inicio')
                ->findAll();
        }
        unset($pedido);

        // Buscar atribuições e configurações para informação de remarcação
        $anoLetivoIds      = array_unique(array_column($pedidos, 'anoletivo_id'));
        $atribuicoesPorAno   = [];
        $configuracoesPorAno = [];
        foreach ($anoLetivoIds as $anoId) {
            $at = $this->atribuicaoModel->getAtribuicaoProfessor($userNif, (int)$anoId);
            if ($at) $atribuicoesPorAno[$anoId] = $at;
            $cfg = $this->configuracaoModel->getConfiguracaoPorAno((int)$anoId);
            if ($cfg) $configuracoesPorAno[$anoId] = $cfg;
        }
        foreach ($pedidos as &$pedido) {
            $anoId = $pedido['anoletivo_id'];
            $at    = $atribuicoesPorAno[$anoId]  ?? null;
            $cfg   = $configuracoesPorAno[$anoId] ?? null;
            $pedido['remarcacao_info'] = [
                'total_dias_original'   => (int)($pedido['total_dias'] ?? 0),
                'permite_fora_periodo'  => (int)($at['permite_marcar_fora_periodo'] ?? 0),
                'obriga_totalidade'     => (int)($at['obriga_totalidade_dias'] ?? 1),
                'data_inicio_permitida' => $cfg['data_inicio_permitida'] ?? null,
                'data_fim_permitida'    => $cfg['data_fim_permitida']    ?? null,
            ];
        }
        unset($pedido);

        $userCompleto = $this->userModel->where('NIF', $userNif)->first();

        $data = [
            'title'         => 'Histórico de Pedidos',
            'pedidos'       => $pedidos,
            'userTelefone'  => $userCompleto['telefone'] ?? ''
        ];

        return view('ferias/professor_pedidos', $data);
    }

    /**
     * Página: Documentos PDF do professor
     */
    public function meusDocumentos()
    {
        $userData = $this->getEffectiveUser();
        if (!$userData) {
            return redirect()->to('/login')->with('error', 'É necessário fazer login');
        }

        $userNif = $userData['NIF'] ?? null;

        // Obter pedidos com documentos (principal, acumulação ou remarcação)
        $pedidos = $this->pedidoModel
            ->select('ferias_pedido.*, ano_letivo.anoletivo as ano')
            ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_pedido.anoletivo_id')
            ->where('ferias_pedido.user_nif', $userNif)
            ->where('(ferias_pedido.documento_pdf IS NOT NULL OR ferias_pedido.doc_acumulacao_pdf IS NOT NULL OR ferias_pedido.documento_remarcacao IS NOT NULL)', null, false)
            ->orderBy('ferias_pedido.submetido_em', 'DESC')
            ->findAll();

        // Buscar atribuições e configurações para informação de remarcação
        $anoLetivoIds      = array_unique(array_column($pedidos, 'anoletivo_id'));
        $atribuicoesPorAno   = [];
        $configuracoesPorAno = [];
        foreach ($anoLetivoIds as $anoId) {
            $at = $this->atribuicaoModel->getAtribuicaoProfessor($userNif, (int)$anoId);
            if ($at) $atribuicoesPorAno[$anoId] = $at;
            $cfg = $this->configuracaoModel->getConfiguracaoPorAno((int)$anoId);
            if ($cfg) $configuracoesPorAno[$anoId] = $cfg;
        }
        foreach ($pedidos as &$pedido) {
            $anoId = $pedido['anoletivo_id'];
            $at    = $atribuicoesPorAno[$anoId]  ?? null;
            $cfg   = $configuracoesPorAno[$anoId] ?? null;
            $pedido['remarcacao_info'] = [
                'total_dias_original'   => (int)($pedido['total_dias'] ?? 0),
                'permite_fora_periodo'  => (int)($at['permite_marcar_fora_periodo'] ?? 0),
                'obriga_totalidade'     => (int)($at['obriga_totalidade_dias'] ?? 1),
                'data_inicio_permitida' => $cfg['data_inicio_permitida'] ?? null,
                'data_fim_permitida'    => $cfg['data_fim_permitida']    ?? null,
            ];
        }
        unset($pedido);

        $userCompleto = $this->userModel->where('NIF', $userNif)->first();

        $data = [
            'title'         => 'Meus Documentos',
            'pedidos'       => $pedidos,
            'userTelefone'  => $userCompleto['telefone'] ?? ''
        ];

        return view('ferias/professor_documentos', $data);
    }

    // ====================================================
    // PÁGINAS ADICIONAIS - SECRETARIA
    // ====================================================

    /**
     * Página: Pedidos pendentes (secretaria)
     */
    public function pedidosPendentes()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        // Obter pedidos pendentes (submetido, em_aprovacao, remarcacao_solicitada)
        $pedidos = $this->pedidoModel
            ->select('ferias_pedido.*, user.name as nome_professor, user.email as email_professor, ano_letivo.anoletivo as ano')
            ->join('user', 'user.NIF = ferias_pedido.user_nif')
            ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_pedido.anoletivo_id')
            ->whereIn('ferias_pedido.estado', ['submetido', 'em_aprovacao', 'remarcacao_solicitada'])
            ->orderBy('ferias_pedido.submetido_em', 'ASC')
            ->findAll();

        // Buscar períodos para cada pedido
        foreach ($pedidos as &$pedido) {
            $pedido['periodos'] = $this->periodoModel
                ->where('pedido_id', $pedido['id'])
                ->orderBy('data_inicio')
                ->findAll();
        }

        $data = [
            'title' => 'Pedidos Pendentes',
            'pedidos' => $pedidos
        ];

        return view('ferias/secretaria_pendentes', $data);
    }

    /**
     * Página: Todos os pedidos (secretaria)
     */
    public function todosPedidos()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        // Buscar ano letivo ativo
        $anoAtivo = $this->anoLetivoModel->where('status', 1)->first();
        $anoAtivoNumero = $anoAtivo ? $anoAtivo['anoletivo'] : null;

        // Filtros
        $estado = $this->request->getGet('estado');
        $ano = $this->request->getGet('ano');
        $professor = $this->request->getGet('professor');
        $origem = $this->request->getGet('origem'); // 'professor' | 'secretaria' | ''
        
        // Se não houver filtro de ano aplicado, usar o ano ativo
        if (!$ano && $anoAtivoNumero) {
            $ano = $anoAtivoNumero;
        }

        // Query base
        $builder = $this->pedidoModel
            ->select('ferias_pedido.*, user.name as nome_professor, ano_letivo.anoletivo as ano')
            ->join('user', 'user.NIF = ferias_pedido.user_nif')
            ->join('ano_letivo', 'ano_letivo.id_anoletivo = ferias_pedido.anoletivo_id');

        // Aplicar filtros
        if ($estado) {
            $builder->where('ferias_pedido.estado', $estado);
        }
        if ($ano) {
            $builder->where('ano_letivo.anoletivo', $ano);
        }
        if ($professor) {
            $builder->like('user.name', $professor);
        }
        if ($origem === 'professor') {
            $builder->where('ferias_pedido.submetido_em IS NOT NULL', null, false);
        } elseif ($origem === 'secretaria') {
            $builder->where('ferias_pedido.submetido_em IS NULL', null, false);
        }

        $pedidos = $builder->orderBy('ferias_pedido.criado_em', 'DESC')->findAll();

        // Buscar períodos
        foreach ($pedidos as &$pedido) {
            $pedido['periodos'] = $this->periodoModel
                ->where('pedido_id', $pedido['id'])
                ->orderBy('data_inicio')
                ->findAll();
        }

        // Anos disponíveis para filtro
        $anos = $this->anoLetivoModel->orderBy('anoletivo', 'DESC')->findAll();

        $data = [
            'title' => 'Todos os Pedidos',
            'pedidos' => $pedidos,
            'anos' => $anos,
            'filtros' => [
                'estado' => $estado,
                'ano' => $ano,
                'professor' => $professor,
                'origem' => $origem
            ]
        ];

        return view('ferias/secretaria_todos_pedidos', $data);
    }

    /**
     * Página: Relatórios (secretaria)
     */
    public function relatorios()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        $anoAtivo = $this->anoLetivoModel->getAnoAtivo();

        // Estatísticas gerais
        $stats = [
            'total_professores_com_atribuicao' => $this->atribuicaoModel
                ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
                ->countAllResults(),
            
            'total_dias_atribuidos' => $this->db->table('ferias_atribuicao')
                ->selectSum('dias_total')
                ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
                ->get()->getRow()->dias_total ?? 0,
            
            'total_pedidos' => $this->pedidoModel
                ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
                ->countAllResults(),
            
            'pedidos_aprovados' => $this->pedidoModel
                ->whereIn('estado', ['aprovado', 'aguarda_assinatura', 'assinado', 'concluido'])
                ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
                ->countAllResults(),
            
            'pedidos_pendentes' => $this->pedidoModel
                ->whereIn('estado', ['submetido', 'em_aprovacao', 'remarcacao_solicitada'])
                ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
                ->countAllResults(),
            
            'professores_com_alinea' => $this->atribuicaoModel
                ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
                ->where('alinea IS NOT NULL')
                ->countAllResults(),
            
            'dias_gozados' => $this->db->table('ferias_pedido')
                ->selectSum('total_dias')
                ->whereIn('estado', ['aprovado', 'aguarda_assinatura', 'assinado', 'concluido'])
                ->where('anoletivo_id', $anoAtivo['id_anoletivo'])
                ->get()->getRow()->total_dias ?? 0
        ];

        // Relatório por professor (com descontos de faltas incluídos no cálculo)
        $relatorioProfessores = $this->db->query("
            SELECT
                u.name            AS nome_professor,
                u.NIF,
                fa.dias_total,
                COALESCE(gozados.total, 0)  AS dias_gozados,
                COALESCE(faltas.total, 0)   AS dias_faltas,
                (fa.dias_total
                    - COALESCE(gozados.total, 0)
                    - COALESCE(faltas.total, 0)) AS dias_disponiveis
            FROM ferias_atribuicao fa
            JOIN user u ON u.NIF = fa.user_nif
            LEFT JOIN (
                SELECT user_nif, anoletivo_id, SUM(total_dias) AS total
                FROM ferias_pedido
                WHERE estado IN ('aprovado','aguarda_assinatura','assinado','concluido')
                GROUP BY user_nif, anoletivo_id
            ) gozados ON gozados.user_nif = fa.user_nif
                      AND gozados.anoletivo_id = fa.anoletivo_id
            LEFT JOIN (
                SELECT user_nif, anoletivo_id_desconto, SUM(dias_desconto) AS total
                FROM ferias_faltas_desconto
                GROUP BY user_nif, anoletivo_id_desconto
            ) faltas ON faltas.user_nif = fa.user_nif
                     AND faltas.anoletivo_id_desconto = fa.anoletivo_id
            WHERE fa.anoletivo_id = ?
            ORDER BY u.name
        ", [$anoAtivo['id_anoletivo']])->getResultArray();

        // --- Relatórios de Faltas (3 tipos) ---
        $anoAnterior = $this->anoLetivoModel
            ->where('anoletivo', $anoAtivo['anoletivo'] - 1)
            ->first();

        $sqlFaltasDetalhadas = "
            SELECT
                u.cod_funcionario,
                u.name          AS nome_professor,
                u.NIF,
                ffd.data_falta,
                ffd.tipo_falta,
                ffd.dias_desconto,
                al_falta.anoletivo AS ano_falta,
                al_desc.anoletivo  AS ano_desconto
            FROM ferias_faltas_desconto ffd
            JOIN user u            ON u.NIF = ffd.user_nif
            JOIN ano_letivo al_falta ON al_falta.id_anoletivo = ffd.anoletivo_id_falta
            JOIN ano_letivo al_desc  ON al_desc.id_anoletivo  = ffd.anoletivo_id_desconto
            WHERE ffd.anoletivo_id_falta = ? AND ffd.anoletivo_id_desconto = ?
            ORDER BY u.cod_funcionario, ffd.data_falta
        ";

        // Tipo 1 — faltas do ano anterior a descontar neste ano
        $faltasAnteriorParaAtual = $anoAnterior
            ? $this->db->query($sqlFaltasDetalhadas, [
                $anoAnterior['id_anoletivo'],
                $anoAtivo['id_anoletivo']
              ])->getResultArray()
            : [];

        // Tipo 2 — faltas deste ano a descontar neste ano
        $faltasAtualParaAtual = $this->db->query($sqlFaltasDetalhadas, [
            $anoAtivo['id_anoletivo'],
            $anoAtivo['id_anoletivo']
        ])->getResultArray();

        // Tipo 3 — faltas deste ano a descontar noutro ano letivo (próximo)
        $faltasAtualParaProximo = $this->db->query("
            SELECT
                u.cod_funcionario,
                u.name          AS nome_professor,
                u.NIF,
                ffd.data_falta,
                ffd.tipo_falta,
                ffd.dias_desconto,
                al_falta.anoletivo AS ano_falta,
                al_desc.anoletivo  AS ano_desconto
            FROM ferias_faltas_desconto ffd
            JOIN user u            ON u.NIF = ffd.user_nif
            JOIN ano_letivo al_falta ON al_falta.id_anoletivo = ffd.anoletivo_id_falta
            JOIN ano_letivo al_desc  ON al_desc.id_anoletivo  = ffd.anoletivo_id_desconto
            WHERE ffd.anoletivo_id_falta = ? AND ffd.anoletivo_id_desconto != ?
            ORDER BY u.cod_funcionario, ffd.data_falta
        ", [$anoAtivo['id_anoletivo'], $anoAtivo['id_anoletivo']])->getResultArray();

        $data = [
            'title'                    => 'Relatórios de Férias',
            'stats'                    => $stats,
            'relatorio_professores'    => $relatorioProfessores,
            'ano_letivo'               => $anoAtivo,
            'ano_anterior'             => $anoAnterior,
            'faltas_anterior_para_atual' => $faltasAnteriorParaAtual,
            'faltas_atual_para_atual'    => $faltasAtualParaAtual,
            'faltas_atual_para_proximo'  => $faltasAtualParaProximo,
        ];

        return view('ferias/secretaria_relatorios', $data);
    }

    /**
     * Relatório de Absentismo dos Professores
     */
    public function relatorioAbsentismo()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        $anoAtivo   = $this->anoLetivoModel->getAnoAtivo();
        $anoLetivoId = (int) ($this->request->getGet('ano_letivo_id') ?? $anoAtivo['id_anoletivo']);

        // Lista de todos os anos letivos para o filtro
        $anosLetivos = $this->anoLetivoModel->orderBy('anoletivo', 'DESC')->findAll();

        // Dados agregados
        $faltasPorProfessor = $this->faltasDescontoModel->getAbsentismoPorProfessor($anoLetivoId);
        $faltasPorMes       = $this->faltasDescontoModel->getAbsentismoPorMes($anoLetivoId);
        $heatmapRaw         = $this->faltasDescontoModel->getHeatmapPorData($anoLetivoId);

        // Indexar heatmap por [mes][dia] — apenas datas dentro do intervalo do ano letivo
        $heatmap    = [];
        $maxHeatmap = 0;
        $anoLetivoObj  = current(array_filter($anosLetivos, fn($a) => $a['id_anoletivo'] == $anoLetivoId)) ?: $anoAtivo;
        $anoBase       = (int) $anoLetivoObj['anoletivo'];
        $dataInicioAno = new \DateTime("{$anoBase}-09-01");
        $dataFimAno    = new \DateTime(($anoBase + 1) . '-08-31');
        foreach ($heatmapRaw as $row) {
            $dt = new \DateTime($row['data_falta']);
            // Falta fora do intervalo do ano letivo: conta nos totais mas não aparece no calendário
            if ($dt < $dataInicioAno || $dt > $dataFimAno) {
                continue;
            }
            $mes = (int) $dt->format('n');
            $dia = (int) $dt->format('j');
            $heatmap[$mes][$dia] = [
                'val'  => (float) $row['total_dias'],
                'date' => $row['data_falta'],
            ];
            if ((float) $row['total_dias'] > $maxHeatmap) {
                $maxHeatmap = (float) $row['total_dias'];
            }
        }

        // Resumo geral
        $totalFaltas       = array_sum(array_column($faltasPorProfessor, 'total_faltas'));
        $totalDias         = array_sum(array_column($faltasPorProfessor, 'total_dias_desconto'));
        $totalProfessores  = count($faltasPorProfessor);
        $totalInjustificadas = array_sum(array_column($faltasPorProfessor, 'faltas_injustificadas'));

        $data = [
            'title'              => 'Relatório de Absentismo',
            'ano_letivo'         => $this->anoLetivoModel->find($anoLetivoId) ?? $anoAtivo,
            'anos_letivos'       => $anosLetivos,
            'ano_letivo_id'      => $anoLetivoId,
            'faltas_por_professor' => $faltasPorProfessor,
            'faltas_por_mes'     => $faltasPorMes,
            'heatmap'            => $heatmap,
            'max_heatmap'        => $maxHeatmap ?: 1,
            'total_faltas'       => $totalFaltas,
            'total_dias'         => $totalDias,
            'total_professores'  => $totalProfessores,
            'total_injustificadas' => $totalInjustificadas,
        ];

        return view('ferias/relatorio_absentismo', $data);
    }

    /**
     * AJAX: Detalhe das faltas num dia (para popover do heatmap)
     */
    public function faltasPorDia()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->response->setJSON(['success' => false])->setStatusCode(403);
        }

        $data        = $this->request->getGet('data');
        $anoLetivoId = (int) $this->request->getGet('ano_letivo_id');

        if (!$data || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data inválida']);
        }

        $faltas = $this->faltasDescontoModel->getFaltasPorDataDetalhe($data, $anoLetivoId);

        return $this->response->setJSON(['success' => true, 'faltas' => $faltas]);
    }

    /**
     * Página: Mapa de Férias (calendário visual)
     */
    public function mapaFerias()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
        $ano   = $this->request->getGet('ano')   ?? $anoAtivo['anoletivo'];
        $grupo = $this->request->getGet('grupo')  ?? 'geral';

        // Grupos disponíveis para o filtro
        $gruposMapaFerias = [
            'geral'               => 'Docentes Gerais',
            'direcao'             => 'Direção',
            'tecnico_superior'    => 'Técnicos Superiores',
        ];

        // Buscar todas as férias aprovadas ordenadas por escola e nome
        $feriasAprovadas = $this->db->table('ferias_pedido as fp')
            ->select('fp.id as pedido_id, fp.user_nif, fp.total_dias,
                      u.name as nome_professor, u.categoria, u.escola_servico, u.grupo_mapa_ferias,
                      e.nome as escola_nome,
                      al.anoletivo,
                      fa.alinea')
            ->join('user as u', 'u.NIF = fp.user_nif')
            ->join('ano_letivo as al', 'al.id_anoletivo = fp.anoletivo_id')
            ->join('escolas as e', 'e.id = u.escola_servico', 'left')
            ->join('ferias_atribuicao as fa', 'fa.user_nif = fp.user_nif AND fa.anoletivo_id = fp.anoletivo_id', 'left')
            ->whereIn('fp.estado', ['aprovado', 'aguarda_assinatura', 'assinado', 'concluido'])
            ->where('al.anoletivo', $ano)
            ->where('u.grupo_mapa_ferias', $grupo)
            ->orderBy('e.nome', 'ASC')
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();

        // Buscar professores com alínea definida mas sem pedidos aprovados
        $profComAlinea = $this->db->table('ferias_atribuicao as fa')
            ->select('NULL as pedido_id, fa.user_nif, 0 as total_dias,
                      u.name as nome_professor, u.categoria, u.escola_servico, u.grupo_mapa_ferias,
                      e.nome as escola_nome,
                      al.anoletivo,
                      fa.alinea')
            ->join('user as u', 'u.NIF = fa.user_nif')
            ->join('ano_letivo as al', 'al.id_anoletivo = fa.anoletivo_id')
            ->join('escolas as e', 'e.id = u.escola_servico', 'left')
            ->where('al.anoletivo', $ano)
            ->where('u.grupo_mapa_ferias', $grupo)
            ->where('fa.alinea IS NOT NULL')
            ->orderBy('e.nome', 'ASC')
            ->orderBy('u.name', 'ASC')
            ->get()->getResultArray();

        // Buscar períodos para cada pedido com ferias aprovadas
        $periodosModel = new \App\Models\FeriasPeriodoModel();
        foreach ($feriasAprovadas as &$ferias) {
            $ferias['periodos'] = $periodosModel
                ->where('pedido_id', $ferias['pedido_id'])
                ->orderBy('data_inicio', 'ASC')
                ->findAll();
        }
        unset($ferias);

        // Adicionar professores com alínea ao conjunto (sem períodos)
        // Apenas aqueles que NÃO aparecem já em feriasAprovadas
        $nifsComPedidos = array_column($feriasAprovadas, 'user_nif');
        foreach ($profComAlinea as &$pa) {
            $pa['periodos'] = [];
        }
        unset($pa);
        // Filtrar para não duplicar professores que já têm pedidos aprovados
        $profComAlineaSemPedidos = array_filter($profComAlinea, fn($p) => !in_array($p['user_nif'], $nifsComPedidos));
        $todasFerias = array_merge($feriasAprovadas, array_values($profComAlineaSemPedidos));

        // Agrupar por escola
        $feriasOrganizadas = [];
        foreach ($todasFerias as $ferias) {
            $escola = $ferias['escola_nome'] ?? 'Sem escola atribuída';
            if (!isset($feriasOrganizadas[$escola])) {
                $feriasOrganizadas[$escola] = [];
            }
            $feriasOrganizadas[$escola][] = $ferias;
        }

        // Ano civil (verão do ano letivo)
        $anoCivil = (int)$ano + 1;

        // Consolidar professores (um professor pode ter múltiplos pedidos aprovados)
        $feriasConsolidadas = [];
        foreach ($feriasOrganizadas as $escola => $professores) {
            $profMap = [];
            foreach ($professores as $prof) {
                $nif = $prof['user_nif'];
                if (!isset($profMap[$nif])) {
                    $profMap[$nif] = [
                        'user_nif'       => $nif,
                        'nome_professor' => $prof['nome_professor'],
                        'categoria'      => $prof['categoria'] ?? '',
                        'total_dias'     => 0,
                        'periodos'       => [],
                        'alinea'         => $prof['alinea'] ?? null,
                    ];
                }
                $profMap[$nif]['total_dias'] += (int)$prof['total_dias'];
                $profMap[$nif]['periodos'] = array_merge($profMap[$nif]['periodos'], $prof['periodos']);
                // Keep alinea if set on any record
                if (!empty($prof['alinea'])) {
                    $profMap[$nif]['alinea'] = $prof['alinea'];
                }
            }
            usort($profMap, fn($a, $b) => strcmp($a['nome_professor'], $b['nome_professor']));
            $feriasConsolidadas[$escola] = array_values($profMap);
        }
        ksort($feriasConsolidadas);
        $feriasOrganizadas = $feriasConsolidadas;

        // Gerar semanas úteis (2ª a 6ª) de junho a setembro
        $semanas = [];
        $nomeMesesSemana = [6 => 'JUNHO', 7 => 'JULHO', 8 => 'AGOSTO', 9 => 'SETEMBRO'];

        // Primeira segunda-feira da semana que contém 1 de junho
        $juno1 = new \DateTime("{$anoCivil}-06-01");
        $diaSemana = (int)$juno1->format('N'); // 1=Seg, 7=Dom
        $primeiraSegunda = clone $juno1;
        if ($diaSemana > 1) {
            $primeiraSegunda->modify('-' . ($diaSemana - 1) . ' days');
        }

        $semanaAtual    = clone $primeiraSegunda;
        $limiteLoop     = new \DateTime("{$anoCivil}-10-01");

        while ($semanaAtual < $limiteLoop) {
            $segunda = clone $semanaAtual;
            $sexta   = clone $semanaAtual;
            $sexta->modify('+4 days');

            $mesSexta = (int)$sexta->format('n');

            // Incluir apenas semanas cuja sexta-feira cai em jun-set
            if ($mesSexta >= 6 && $mesSexta <= 9) {
                $semanas[] = [
                    'numero'      => (int)$segunda->format('W'),
                    'segunda'     => clone $segunda,
                    'sexta'       => clone $sexta,
                    'segunda_str' => $segunda->format('Y-m-d'),
                    'sexta_str'   => $sexta->format('Y-m-d'),
                    'mes'         => $mesSexta,
                    'mes_nome'    => $nomeMesesSemana[$mesSexta],
                    'label'       => $segunda->format('j') . 'a' . $sexta->format('j'),
                ];
            }

            $semanaAtual->modify('+7 days');
        }

        $data = [
            'title'              => 'Mapa de Férias',
            'ferias_organizadas' => $feriasOrganizadas,
            'semanas'            => $semanas,
            'ano_letivo'         => $anoAtivo,
            'ano_civil'          => $anoCivil,
            'grupo_ativo'        => $grupo,
            'grupos_mapa'        => $gruposMapaFerias,
        ];

        return view('ferias/mapa_ferias', $data);
    }

    /**
     * Página: Gestão de feriados (secretaria)
     */
    public function feriados()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        $ano = $this->request->getGet('ano') ?? date('Y');

        // Obter feriados do ano
        $feriados = $this->feriadosModel
            ->where('YEAR(data)', $ano)
            ->orderBy('data', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Gestão de Feriados',
            'feriados' => $feriados,
            'ano' => $ano
        ];

        return view('ferias/secretaria_feriados', $data);
    }

    /**
     * Obter configuração atual do período permitido (AJAX)
     */
    public function getConfiguracao()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $config = $this->configuracaoModel->getConfiguracaoAnoAtivo();

            if (!$config) {
                // Retornar configuração padrão se não existir
                $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
                $anoLetivoInt = $anoAtivo ? $anoAtivo['anoletivo'] : date('Y');
                
                return $this->respond([
                    'success' => true,
                    'data' => [
                        'existe' => false,
                        'data_inicio_permitida' => "{$anoLetivoInt}-09-01",
                        'data_fim_permitida' => ($anoLetivoInt + 1) . "-12-31",
                        'permite_marcacao' => true,
                        'mensagem_bloqueio' => null,
                        'observacoes' => null
                    ]
                ]);
            }

            return $this->respond([
                'success' => true,
                'data' => [
                    'existe' => true,
                    'data_inicio_permitida' => $config['data_inicio_permitida'],
                    'data_fim_permitida' => $config['data_fim_permitida'],
                    'permite_marcacao' => (bool) $config['permite_marcacao'],
                    'mostrar_menu_ferias' => (bool) ($config['mostrar_menu_ferias'] ?? 1),
                    'mensagem_bloqueio' => $config['mensagem_bloqueio'],
                    'observacoes' => $config['observacoes']
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro em getConfiguracao: ' . $e->getMessage());
            return $this->fail('Erro ao obter configuração: ' . $e->getMessage());
        }
    }

    /**
     * Salvar configuração do período permitido (AJAX)
     */
    public function salvarConfiguracao()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $dataInicio = $this->request->getPost('data_inicio_permitida');
            $dataFim = $this->request->getPost('data_fim_permitida');
            $permiteMarcacao = (bool) $this->request->getPost('permite_marcacao');
            $mostrarMenuFerias = (bool) $this->request->getPost('mostrar_menu_ferias');
            $mensagemBloqueio = $this->request->getPost('mensagem_bloqueio');
            $observacoes = $this->request->getPost('observacoes');

            // Validações básicas
            if (!$dataInicio || !$dataFim) {
                return $this->fail('Data de início e data de fim são obrigatórias');
            }

            // Validar formato de data
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataInicio) || 
                !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataFim)) {
                return $this->fail('Formato de data inválido. Use YYYY-MM-DD');
            }

            // Validar que data fim é posterior à data início
            if (strtotime($dataFim) < strtotime($dataInicio)) {
                return $this->fail('A data de fim deve ser posterior à data de início');
            }

            // Obter ano letivo ativo
            $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
            if (!$anoAtivo) {
                return $this->fail('Nenhum ano letivo ativo encontrado');
            }

            // Salvar configuração
            $result = $this->configuracaoModel->salvarConfiguracao(
                $anoAtivo['id_anoletivo'],
                $dataInicio,
                $dataFim,
                $permiteMarcacao,
                $mostrarMenuFerias,
                $mensagemBloqueio,
                $observacoes,
                $userData['id']
            );

            if ($result) {
                log_activity(
                    'ferias',
                    'update',
                    null,
                    'Configuração do período de férias atualizada: ' . $dataInicio . ' a ' . $dataFim . ' (ano letivo ID: ' . $anoAtivo['id_anoletivo'] . ')',
                    null,
                    ['data_inicio' => $dataInicio, 'data_fim' => $dataFim, 'permite_marcacao' => $permiteMarcacao]
                );

                return $this->respond([
                    'success' => true,
                    'message' => 'Configuração guardada com sucesso'
                ]);
            }

            $errors = $this->configuracaoModel->errors();
            return $this->fail($errors ? implode(', ', $errors) : 'Erro ao guardar configuração');

        } catch (\Exception $e) {
            log_message('error', 'Erro em salvarConfiguracao: ' . $e->getMessage());
            return $this->fail('Erro ao processar configuração: ' . $e->getMessage());
        }
    }

    /**
     * Página: Gestão de Utilizadores simplificada (secretaria)
     */
    public function utilizadores()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        // Obter todos os utilizadores ativos
        $utilizadores = $this->userModel
            ->select('user.id, user.NIF, user.name, user.cod_funcionario, user.telefone, user.grupo_id, user.categoria, user.escola_servico, user.grupo_mapa_ferias, escolas.nome as escola_nome')
            ->join('escolas', 'escolas.id = user.escola_servico', 'left')
            ->where('user.status', 1)
            ->orderBy('user.name', 'ASC')
            ->findAll();

        // Obter valores ENUM do campo categoria
        $query = $this->db->query("SHOW COLUMNS FROM user WHERE Field = 'categoria'");
        $row = $query->getRow();
        $categorias = [];
        
        if ($row && preg_match("/^enum\(\'(.*)\'\)$/", $row->Type, $matches)) {
            $categorias = explode("','", $matches[1]);
        }
        
        // Obter escolas
        $escolas = $this->escolasModel->getEscolasOrderedByName();

        $data = [
            'title' => 'Gestão de Utilizadores',
            'utilizadores' => $utilizadores,
            'categorias' => $categorias,
            'escolas' => $escolas
        ];

        return view('ferias/secretaria_utilizadores', $data);
    }

    /**
     * Atualizar dados básicos do utilizador (AJAX)
     */
    public function atualizarUtilizador()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $userId = $this->request->getPost('user_id');
            $nome = $this->request->getPost('nome');
            $codFuncionario = $this->request->getPost('cod_funcionario');
            $telefone = $this->request->getPost('telefone');
            $grupoId = $this->request->getPost('grupo_id');
            $categoria = $this->request->getPost('categoria');
            $escolaServico = $this->request->getPost('escola_servico');

            // Log dos dados recebidos
            log_message('error', "[DEBUG] Atualizar utilizador - ID: {$userId}, Cod: {$codFuncionario}, Cat: {$categoria}");

            if (!$userId) {
                return $this->fail('ID do utilizador é obrigatório');
            }

            // Validações básicas
            if (empty($nome)) {
                return $this->fail('Nome é obrigatório');
            }

            // Preparar dados para atualização (apenas campos permitidos)
            $grupoMapaFerias = $this->request->getPost('grupo_mapa_ferias');

            $dadosAtualizacao = [
                'name' => $nome,
                'cod_funcionario' => $codFuncionario ?: null,
                'telefone' => $telefone ?: null,
                'grupo_id' => $grupoId ?: null,
                'categoria' => $categoria ?: null,
                'escola_servico' => $escolaServico ?: null,
                'grupo_mapa_ferias' => in_array($grupoMapaFerias, ['geral', 'direcao', 'tecnico_superior']) ? $grupoMapaFerias : 'geral',
            ];
            
            // Log dos dados a atualizar
            log_message('error', "[DEBUG] Dados para update: " . json_encode($dadosAtualizacao));

            // Atualizar utilizador
            $result = $this->userModel->update($userId, $dadosAtualizacao);

            if ($result) {
                // Verificar se os dados foram realmente atualizados
                $userAtualizado = $this->userModel->find($userId);
                
                // Log da atualização
                log_message('error', "[DEBUG] Utilizador ID {$userId} atualizado - Cod: " . ($userAtualizado['cod_funcionario'] ?? 'NULL') . ", Categoria: " . ($userAtualizado['categoria'] ?? 'NULL'));
                
                // Limpar cache do modelo se existir
                if (method_exists($this->userModel, 'clearCache')) {
                    $this->userModel->clearCache();
                }

                log_activity(
                    'ferias',
                    'update',
                    $userId,
                    'Dados do utilizador ID ' . $userId . ' atualizados pela secretaria (no módulo de férias)',
                    null,
                    $dadosAtualizacao
                );
                
                return $this->respond([
                    'success' => true,
                    'message' => 'Utilizador atualizado com sucesso',
                    'data' => [
                        'cod_funcionario' => $userAtualizado['cod_funcionario'],
                        'categoria' => $userAtualizado['categoria']
                    ]
                ]);
            }

            $errors = $this->userModel->errors();
            return $this->fail($errors ? implode(', ', $errors) : 'Erro ao atualizar utilizador');

        } catch (\Exception $e) {
            log_message('error', 'Erro em atualizarUtilizador: ' . $e->getMessage());
            return $this->fail('Erro ao processar atualização: ' . $e->getMessage());
        }
    }

    /**
     * Página: Logs do sistema (secretaria)
     */
    public function logs()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to('/dashboard')->with('error', 'Acesso negado');
        }

        // Filtros
        $userNif = $this->request->getGet('user_nif');
        $acao = $this->request->getGet('acao');
        $dataInicio = $this->request->getGet('data_inicio');
        $dataFim = $this->request->getGet('data_fim');

        // Query base
        $builder = $this->logModel
            ->select('ferias_log.*, u1.name as nome_usuario, u2.name as nome_responsavel')
            ->join('user as u1', 'u1.NIF = ferias_log.user_nif', 'left')
            ->join('user as u2', 'u2.id = ferias_log.realizado_por', 'left');

        // Aplicar filtros
        if ($userNif) {
            $builder->where('ferias_log.user_nif', $userNif);
        }
        if ($acao) {
            $builder->like('ferias_log.acao', $acao);
        }
        if ($dataInicio) {
            $builder->where('ferias_log.criado_em >=', $dataInicio . ' 00:00:00');
        }
        if ($dataFim) {
            $builder->where('ferias_log.criado_em <=', $dataFim . ' 23:59:59');
        }

        $logs = $builder->orderBy('ferias_log.criado_em', 'DESC')->paginate(50);
        $pager = $this->logModel->pager;

        // Professores para filtro
        $professores = $this->userModel->where('level', 1)->orderBy('name')->findAll();

        $data = [
            'title' => 'Logs do Sistema de Férias',
            'logs' => $logs,
            'pager' => $pager,
            'professores' => $professores,
            'filtros' => [
                'user_nif' => $userNif,
                'acao' => $acao,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim
            ]
        ];

        return view('ferias/secretaria_logs', $data);
    }

    /**
     * API: Adicionar feriado personalizado
     */
    public function adicionarFeriado()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        $data = $this->request->getPost('data');
        $descricao = $this->request->getPost('descricao');
        $tipo = $this->request->getPost('tipo') ?? 'fixo';

        if (!$data || !$descricao) {
            return $this->fail('Data e descrição são obrigatórios');
        }

        $result = $this->feriadosModel->insert([
            'data'     => $data,
            'descricao' => $descricao,
            'tipo'     => $tipo,
            'ativo'    => 1
        ]);

        if ($result) {
            log_activity(
                'ferias',
                'create',
                $result,
                'Feriado adicionado: ' . $descricao . ' em ' . $data . ' (tipo: ' . $tipo . ')',
                null,
                ['data' => $data, 'descricao' => $descricao, 'tipo' => $tipo]
            );

            return $this->respond([
                'success' => true,
                'message' => 'Feriado adicionado com sucesso'
            ]);
        }

        return $this->fail('Erro ao adicionar feriado');
    }

    /**
     * API: Remover feriado
     */
    public function removerFeriado($id)
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        $result = $this->feriadosModel->delete($id);

        if ($result) {
            log_activity(
                'ferias',
                'delete',
                $id,
                'Feriado ID ' . $id . ' removido',
                null,
                null,
                'warning'
            );

            return $this->respond([
                'success' => true,
                'message' => 'Feriado removido com sucesso'
            ]);
        }

        return $this->fail('Erro ao remover feriado');
    }

    /**
     * API: Gerar feriados automáticos para um ano
     */
    public function gerarFeriadosAno()
    {
        $userData = session()->get('LoggedUserData');
        // Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        $ano = $this->request->getPost('ano');

        if (!$ano || $ano < 2020 || $ano > 2100) {
            return $this->fail('Ano inválido');
        }

        $adicionados = $this->feriadosModel->adicionarFeriadosPortugal($ano);

        log_activity(
            'ferias',
            'create',
            null,
            $adicionados . ' feriados de Portugal gerados automaticamente para o ano ' . $ano,
            null,
            ['ano' => $ano, 'adicionados' => $adicionados]
        );

        return $this->respond([
            'success' => true,
            'message' => "{$adicionados} feriados adicionados para o ano {$ano}",
            'adicionados' => $adicionados
        ]);
    }

    // =====================================================
    // GESTÃO DE FALTAS QUE DESCONTAM EM FÉRIAS
    // =====================================================

    /**
     * Registar nova falta (AJAX)
     */
    public function faltasRegistar()
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $dados = [
                'user_nif' => $this->request->getPost('user_nif'),
                'anoletivo_id_falta' => $this->request->getPost('anoletivo_id_falta'),
                'data_falta' => $this->request->getPost('data_falta'),
                'tipo_falta' => $this->request->getPost('tipo_falta'),
                'dias_desconto' => $this->request->getPost('dias_desconto'),
                'anoletivo_id_desconto' => $this->request->getPost('anoletivo_id_desconto'),
                'motivo' => $this->request->getPost('motivo'),
                'observacoes' => $this->request->getPost('observacoes'),
                'registado_por' => $userData['id']
            ];

            // Validações básicas
            if (empty($dados['user_nif']) || empty($dados['anoletivo_id_falta']) ||
                empty($dados['data_falta']) || empty($dados['tipo_falta']) ||
                empty($dados['dias_desconto']) || empty($dados['anoletivo_id_desconto'])) {
                return $this->fail('Todos os campos obrigatórios devem ser preenchidos');
            }

            // Validar tipo de falta
            if (!in_array($dados['tipo_falta'], ['artigo_102', 'art_135_n4_ltfp', 'artigo_89_ecd', 'injustificada'])) {
                return $this->fail('Tipo de falta inválido');
            }

            // Validar dias_desconto (aceita décimas)
            if ((float)$dados['dias_desconto'] <= 0 || (float)$dados['dias_desconto'] > 365) {
                return $this->fail('Número de dias inválido');
            }

            // Verificar falta duplicada no mesmo dia (a menos que o utilizador confirme)
            if (!$this->request->getPost('forcar')) {
                $faltasMesmoDia = $this->faltasDescontoModel->getFaltasMesmoDia(
                    $dados['user_nif'],
                    $dados['data_falta']
                );

                if (!empty($faltasMesmoDia)) {
                    $totalDias = array_sum(array_column($faltasMesmoDia, 'dias_desconto'));
                    $numFaltas = count($faltasMesmoDia);
                    return $this->respond([
                        'success'         => false,
                        'duplicate'       => true,
                        'message'         => 'Já existe' . ($numFaltas === 1 ? ' 1 falta' : " {$numFaltas} faltas") . ' registada(s) para esta data (' . $totalDias . ' dia(s) de desconto). Pretende registar mesmo assim?',
                        'totalExistente'  => $totalDias,
                        'numFaltas'       => $numFaltas,
                    ], 200);
                }
            }

            $id = $this->faltasDescontoModel->registarFalta($dados);

            if ($id) {
                log_activity(
                    'ferias',
                    'create',
                    $id,
                    'Falta registada para professor NIF: ' . $dados['user_nif'] . ' em ' . $dados['data_falta'] . ' (' . $dados['tipo_falta'] . ', ' . $dados['dias_desconto'] . ' dias)',
                    null,
                    $dados
                );

                return $this->respond([
                    'success' => true,
                    'message' => 'Falta registada com sucesso',
                    'id' => $id
                ]);
            } else {
                return $this->fail('Erro ao registar falta');
            }
        } catch (\Exception $e) {
            return $this->fail('Erro ao registar falta: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar falta (AJAX)
     */
    public function faltasEliminar($id)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $falta = $this->faltasDescontoModel->find($id);
            if (!$falta) {
                return $this->failNotFound('Falta não encontrada');
            }

            $success = $this->faltasDescontoModel->delete($id);

            if ($success) {
                log_activity(
                    'ferias',
                    'delete',
                    $id,
                    'Falta ID ' . $id . ' eliminada (professor NIF: ' . $falta['user_nif'] . ', data: ' . $falta['data_falta'] . ')',
                    $falta,
                    null,
                    'warning'
                );

                return $this->respond([
                    'success' => true,
                    'message' => 'Falta eliminada com sucesso'
                ]);
            } else {
                return $this->fail('Erro ao eliminar falta');
            }
        } catch (\Exception $e) {
            return $this->fail('Erro ao eliminar falta: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar falta existente (AJAX)
     */
    public function faltasAtualizar($id)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $falta = $this->faltasDescontoModel->find($id);
            if (!$falta) {
                return $this->failNotFound('Falta não encontrada');
            }

            $dados = [
                'anoletivo_id_falta'    => $this->request->getPost('anoletivo_id_falta'),
                'data_falta'            => $this->request->getPost('data_falta'),
                'tipo_falta'            => $this->request->getPost('tipo_falta'),
                'dias_desconto'         => $this->request->getPost('dias_desconto'),
                'anoletivo_id_desconto' => $this->request->getPost('anoletivo_id_desconto'),
                'motivo'                => $this->request->getPost('motivo') ?? '',
                'observacoes'           => $this->request->getPost('observacoes') ?? '',
            ];

            if (empty($dados['anoletivo_id_falta']) || empty($dados['data_falta']) ||
                empty($dados['tipo_falta']) || empty($dados['dias_desconto']) ||
                empty($dados['anoletivo_id_desconto'])) {
                return $this->fail('Campos obrigatórios em falta');
            }

            if (!in_array($dados['tipo_falta'], ['artigo_102', 'art_135_n4_ltfp', 'artigo_89_ecd', 'injustificada'])) {
                return $this->fail('Tipo de falta inválido');
            }

            if ((float)$dados['dias_desconto'] <= 0 || (float)$dados['dias_desconto'] > 365) {
                return $this->fail('Número de dias inválido');
            }

            $this->faltasDescontoModel->update($id, $dados);

            log_activity(
                'ferias',
                'update',
                $id,
                'Falta ID ' . $id . ' atualizada (professor NIF: ' . $falta['user_nif'] . ')',
                $falta,
                $dados
            );

            return $this->respond([
                'success' => true,
                'message' => 'Falta atualizada com sucesso'
            ]);

        } catch (\Exception $e) {
            return $this->fail('Erro ao atualizar falta: ' . $e->getMessage());
        }
    }

    /**
     * API: Obter lista de anos letivos para selects (AJAX)
     */
    public function apiAnosLetivos()
    {
        try {
            $anos = $this->anoLetivoModel->orderBy('anoletivo', 'DESC')->findAll();
            
            return $this->respond([
                'success' => true,
                'data' => $anos
            ]);
        } catch (\Exception $e) {
            return $this->fail('Erro ao carregar anos letivos: ' . $e->getMessage());
        }
    }

    /**
     * API: Obter lista de professores para selects (AJAX)
     */
    public function apiProfessores()
    {
        try {
            $professores = $this->userModel
                                ->select('NIF, name, email')
                                ->where('level', 1)  // Apenas professores
                                ->where('status', 1) // Apenas ativos
                                ->whereNotIn('NIF', ['', 'NULL'])
                                ->orderBy('name', 'ASC')
                                ->findAll();
            
            return $this->respond([
                'success' => true,
                'data' => $professores
            ]);
        } catch (\Exception $e) {
            return $this->fail('Erro ao carregar professores: ' . $e->getMessage());
        }
    }

    // =====================================================
    // GESTÃO COMPLETA DE PROFESSOR (PÁGINA DEDICADA)
    // =====================================================

    /**
     * Secretaria marca férias directamente por um professor (sem pedido do professor)
     * Cria um ferias_pedido com estado='aprovado' e regista os períodos
     */
    public function marcarFeriasSecretaria($nif)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
            if (!$anoAtivo) {
                return $this->fail('Nenhum ano letivo ativo');
            }

            $professor = $this->userModel->where('NIF', $nif)->first();
            if (!$professor) {
                return $this->fail('Professor não encontrado');
            }

            $periodos = $this->request->getPost('periodos');
            $observacoes = $this->request->getPost('observacoes') ?? '';

            if (empty($periodos) || !is_array($periodos)) {
                return $this->fail('É necessário definir pelo menos um período');
            }

            // Calcular total de dias úteis
            $totalDias = 0;
            $periodosValidados = [];
            foreach ($periodos as $p) {
                if (empty($p['data_inicio']) || empty($p['data_fim'])) continue;
                if ($p['data_inicio'] > $p['data_fim']) {
                    return $this->fail('Data de início não pode ser posterior à data de fim');
                }
                $diasUteis = calcular_dias_uteis($p['data_inicio'], $p['data_fim']);
                if ($diasUteis <= 0) {
                    return $this->fail('O período ' . $p['data_inicio'] . ' - ' . $p['data_fim'] . ' não contém dias úteis');
                }
                $totalDias += $diasUteis;
                $periodosValidados[] = [
                    'data_inicio' => $p['data_inicio'],
                    'data_fim'    => $p['data_fim'],
                    'dias_uteis'  => $diasUteis,
                ];
            }

            if (empty($periodosValidados)) {
                return $this->fail('Nenhum período válido fornecido');
            }

            $now = date('Y-m-d H:i:s');

            // Criar pedido aprovado directamente
            $pedidoId = $this->pedidoModel->insert([
                'user_nif'                => $nif,
                'anoletivo_id'            => $anoAtivo['id_anoletivo'],
                'estado'                  => 'aprovado',
                'total_dias'              => $totalDias,
                'aprovado_por'            => $userData['id'],
                'aprovado_em'             => $now,
                'observacoes_secretaria'  => 'Marcado pela secretaria' . ($observacoes ? ': ' . $observacoes : ''),
            ]);

            if (!$pedidoId) {
                return $this->fail('Erro ao criar registo de férias');
            }

            // Criar períodos
            foreach ($periodosValidados as $periodo) {
                $this->periodoModel->insert([
                    'pedido_id'   => $pedidoId,
                    'data_inicio' => $periodo['data_inicio'],
                    'data_fim'    => $periodo['data_fim'],
                    'dias_uteis'  => $periodo['dias_uteis'],
                ]);
            }

            $this->logModel->registrarAcao(
                null,
                $nif,
                'Marcação de férias pela secretaria',
                "Pedido #{$pedidoId} criado com {$totalDias} dias em " . count($periodosValidados) . ' período(s)',
                $userData['id']
            );

            log_activity(
                'ferias',
                'create',
                $pedidoId,
                'Secretaria marcou férias directamente para professor NIF: ' . $nif . '. Pedido #' . $pedidoId . ' com ' . $totalDias . ' dias',
                null,
                ['total_dias' => $totalDias, 'periodos' => count($periodosValidados), 'user_nif' => $nif]
            );

            return $this->respond([
                'success' => true,
                'message' => "Férias registadas com sucesso ({$totalDias} dias úteis)",
                'pedido_id' => $pedidoId,
            ]);

        } catch (\Exception $e) {
            log_message('error', 'marcarFeriasSecretaria: ' . $e->getMessage());
            return $this->fail('Erro ao registar férias: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar um pedido de férias criado pela secretaria
     */
    public function eliminarPedidoSecretaria($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $pedido = $this->pedidoModel->find($pedidoId);
            if (!$pedido) {
                return $this->fail('Pedido não encontrado');
            }

            // Só pode eliminar pedidos criados pela secretaria (submetido_em IS NULL)
            if (!empty($pedido['submetido_em'])) {
                return $this->fail('Só é possível eliminar pedidos criados pela secretaria');
            }

            $nif = $pedido['user_nif'];

            // Eliminar períodos e pedido
            $this->periodoModel->where('pedido_id', $pedidoId)->delete();
            $this->pedidoModel->delete($pedidoId);

            $this->logModel->registrarAcao(
                null,
                $nif,
                'Eliminação de férias pela secretaria',
                "Pedido #{$pedidoId} eliminado",
                $userData['id']
            );

            log_activity(
                'ferias',
                'delete',
                $pedidoId,
                'Secretaria eliminou pedido de férias #' . $pedidoId . ' do professor NIF: ' . $nif,
                $pedido,
                null,
                'warning'
            );

            return $this->respond(['success' => true, 'message' => 'Registo de férias eliminado']);

        } catch (\Exception $e) {
            log_message('error', 'eliminarPedidoSecretaria: ' . $e->getMessage());
            return $this->fail('Erro ao eliminar: ' . $e->getMessage());
        }
    }

    /**
     * Interromper férias de um professor (AJAX)
     * Divide o período original: a parte já gozada fica registada, os restantes dias voltam ao saldo.
     */
    public function interromperFerias($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $dataRegresso = $this->request->getPost('data_regresso');
            $motivo       = trim($this->request->getPost('motivo') ?? '');

            if (!$dataRegresso) {
                return $this->fail('Data de regresso obrigatória');
            }

            $pedido = $this->pedidoModel->find($pedidoId);
            if (!$pedido) {
                return $this->fail('Pedido não encontrado');
            }

            // Só pedidos aprovados
            $estadosValidos = ['aprovado', 'aguarda_assinatura', 'assinado', 'concluido'];
            if (!in_array($pedido['estado'], $estadosValidos)) {
                return $this->fail('Só é possível interromper pedidos aprovados');
            }

            $nif = $pedido['user_nif'];

            // Obter períodos do pedido, ordenados por data
            $periodos = $this->periodoModel
                             ->where('pedido_id', $pedidoId)
                             ->orderBy('data_inicio', 'ASC')
                             ->findAll();

            if (empty($periodos)) {
                return $this->fail('Pedido sem períodos registados');
            }

            // Verificar que a data de regresso está dentro de algum período
            $periodoInterrompido = null;
            foreach ($periodos as $p) {
                if ($dataRegresso >= $p['data_inicio'] && $dataRegresso <= $p['data_fim']) {
                    $periodoInterrompido = $p;
                    break;
                }
            }

            if (!$periodoInterrompido) {
                return $this->fail('A data de regresso não pertence a nenhum período de férias deste pedido');
            }

            // Calcular nova data_fim do período interrompido (dia anterior ao regresso)
            $novaDataFim = date('Y-m-d', strtotime($dataRegresso . ' -1 day'));

            // Se o regresso for o próprio primeiro dia do período, apagar o período inteiro
            $novosDiasUteis = 0;
            if ($novaDataFim < $periodoInterrompido['data_inicio']) {
                // Período fica vazio — eliminar
                $this->periodoModel->delete($periodoInterrompido['id']);
            } else {
                // Recalcular dias úteis do período truncado
                $novosDiasUteis = calcular_dias_uteis($periodoInterrompido['data_inicio'], $novaDataFim);
                $this->periodoModel->update($periodoInterrompido['id'], [
                    'data_fim'   => $novaDataFim,
                    'dias_uteis' => $novosDiasUteis,
                ]);
            }

            // Eliminar todos os períodos posteriores ao interrompido
            foreach ($periodos as $p) {
                if ($p['data_inicio'] > $periodoInterrompido['data_fim']) {
                    $this->periodoModel->delete($p['id']);
                }
            }

            // Recalcular total_dias do pedido
            $periodosRestantes = $this->periodoModel
                                      ->where('pedido_id', $pedidoId)
                                      ->findAll();
            $novoTotal = array_sum(array_column($periodosRestantes, 'dias_uteis'));

            $obsInterrupcao = 'Férias interrompidas em ' . date('d/m/Y', strtotime($dataRegresso));
            if ($motivo) {
                $obsInterrupcao .= ' — ' . $motivo;
            }
            $obsInterrupcao .= ' (registado pela secretaria em ' . date('d/m/Y H:i') . ')';

            $this->pedidoModel->update($pedidoId, [
                'total_dias'              => $novoTotal,
                'observacoes_secretaria'  => $obsInterrupcao,
            ]);

            // Registar logs
            $this->logModel->registrarAcao(
                $pedidoId,
                $nif,
                'Interrupção de férias',
                "Férias interrompidas com data de regresso {$dataRegresso}. Novo total: {$novoTotal} dias. " . ($motivo ? "Motivo: {$motivo}" : ''),
                $userData['id']
            );

            log_activity(
                'ferias',
                'interrupcao',
                $pedidoId,
                "Secretaria interrompeu férias do professor NIF {$nif}. Regresso: {$dataRegresso}. Novo total: {$novoTotal} dias.",
                ['total_dias' => $pedido['total_dias']],
                ['total_dias' => $novoTotal],
                'warning'
            );

            return $this->respond([
                'success'    => true,
                'message'    => 'Férias interrompidas. Novo total de dias: ' . $novoTotal,
                'novo_total' => $novoTotal,
            ]);

        } catch (\Exception $e) {
            log_message('error', 'interromperFerias: ' . $e->getMessage());
            return $this->fail('Erro ao interromper férias: ' . $e->getMessage());
        }
    }

    /**
     * Página de gestão completa de um professor
     */
    public function gerirProfessor($nif)
    {
        $userData = session()->get('LoggedUserData');
        // Apenas secretaria (níveis 3 e >= 6, exceto 4-5)
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return redirect()->to(base_url())->with('error', 'Acesso negado');
        }

        // Obter ano letivo ativo
        $anoAtivo = $this->anoLetivoModel->getAnoAtivo();
        if (!$anoAtivo) {
            return redirect()->to(base_url('ferias/atribuir'))->with('error', 'Nenhum ano letivo ativo');
        }

        // Obter dados do professor (incluindo escola de serviço)
        $professor = $this->userModel
                          ->select('user.*, escolas.nome as escola_nome')
                          ->join('escolas', 'escolas.id = user.escola_servico', 'left')
                          ->where('user.NIF', $nif)
                          ->first();

        if (!$professor) {
            return redirect()->to(base_url('ferias/atribuir'))->with('error', 'Professor não encontrado');
        }

        // Obter atribuição do ano atual
        $atribuicao = $this->atribuicaoModel->getAtribuicaoProfessor($nif, $anoAtivo['id_anoletivo']);
        
        // Obter saldo
        $saldo = $this->atribuicaoModel->calcularSaldo($nif, $anoAtivo['id_anoletivo']);

        // Obter faltas registadas (ano atual e próximo ano)
        $faltasAnoAtual = $this->faltasDescontoModel->getFaltasPorProfessorEAno($nif, $anoAtivo['id_anoletivo']);
        
        // Obter ano seguinte
        $anoSeguinte = $this->anoLetivoModel
                            ->where('id_anoletivo >', $anoAtivo['id_anoletivo'])
                            ->orderBy('id_anoletivo', 'ASC')
                            ->first();
        
        $faltasAnoSeguinte = [];
        if ($anoSeguinte) {
            $faltasAnoSeguinte = $this->faltasDescontoModel->getFaltasPorProfessorEAno($nif, $anoSeguinte['id_anoletivo']);
        }
        
        // Combinar faltas dos dois anos
        $faltas = array_merge($faltasAnoAtual, $faltasAnoSeguinte);
        
        // Ordenar por data da falta (mais recente primeiro)
        usort($faltas, function($a, $b) {
            return strtotime($b['data_falta']) - strtotime($a['data_falta']);
        });

        // Obter pedidos de férias do ano
        $pedidos = $this->pedidoModel
                        ->select('ferias_pedido.*, 
                                 user_aprovador.name as nome_aprovador,
                                 user_rejeitador.name as nome_rejeitador')
                        ->join('user as user_aprovador', 'user_aprovador.id = ferias_pedido.aprovado_por', 'left')
                        ->join('user as user_rejeitador', 'user_rejeitador.id = ferias_pedido.rejeitado_por', 'left')
                        ->where('ferias_pedido.user_nif', $nif)
                        ->where('ferias_pedido.anoletivo_id', $anoAtivo['id_anoletivo'])
                        ->orderBy('ferias_pedido.criado_em', 'DESC')
                        ->findAll();

        // Obter períodos de cada pedido
        foreach ($pedidos as &$pedido) {
            $pedido['periodos'] = $this->periodoModel
                                       ->where('pedido_id', $pedido['id'])
                                       ->orderBy('data_inicio', 'ASC')
                                       ->findAll();
        }

        // Obter atribuição do ano anterior
        $anoAnterior = $this->anoLetivoModel
                            ->where('id_anoletivo <', $anoAtivo['id_anoletivo'])
                            ->orderBy('id_anoletivo', 'DESC')
                            ->first();

        $atribuicaoAnoAnterior = null;
        $diasGozadosAnterior = 0;
        $diasAtribuidosAnterior = 0;

        // Primeiro, tentar obter os valores já guardados na atribuição atual
        if ($atribuicao) {
            // Carregar dias atribuídos anterior se já foi guardado
            if (isset($atribuicao['dias_atribuidos_anterior'])) {
                $diasAtribuidosAnterior = (int) $atribuicao['dias_atribuidos_anterior'];
            }
            
            // Carregar dias gozados anterior se já foi guardado
            if (isset($atribuicao['dias_gozados_anterior'])) {
                $diasGozadosAnterior = (int) $atribuicao['dias_gozados_anterior'];
            }
        }
        
        // Se ainda forem 0 e existir ano anterior, tentar calcular/obter do ano anterior
        if ($anoAnterior) {
            $atribuicaoAnoAnterior = $this->atribuicaoModel->getAtribuicaoProfessor($nif, $anoAnterior['id_anoletivo']);
            
            // Se dias atribuídos ainda é 0, calcular do ano anterior
            if ($diasAtribuidosAnterior == 0) {
                if ($atribuicaoAnoAnterior) {
                    $diasAtribuidosAnterior = $atribuicaoAnoAnterior['dias_total'];
                } else {
                    // Calcular dias gastos do ano anterior
                    $diasGastosCalculados = $this->atribuicaoModel->calcularDiasGastos($nif, $anoAnterior['id_anoletivo']);
                    
                    // Se não há atribuição mas há dias gozados, assumir valor padrão
                    if ($diasGastosCalculados > 0) {
                        $diasAtribuidosAnterior = 22; // Valor padrão base
                    }
                }
            }
            
            // Se dias gozados ainda é 0, calcular do ano anterior
            if ($diasGozadosAnterior == 0) {
                $diasGozadosAnterior = $this->atribuicaoModel->calcularDiasGastos($nif, $anoAnterior['id_anoletivo']);
            }
        }

        // Obter logs recentes
        $logs = $this->logModel
                     ->select('ferias_log.*, user.name as nome_usuario')
                     ->join('user', 'user.id = ferias_log.realizado_por', 'left')
                     ->where('ferias_log.user_nif', $nif)
                     ->orderBy('ferias_log.criado_em', 'DESC')
                     ->limit(20)
                     ->findAll();

        // Obter faltas que descontam no ano atual
        $faltasDescontoAnoAtual = $this->faltasDescontoModel
                                       ->where('user_nif', $nif)
                                       ->where('anoletivo_id_desconto', $anoAtivo['id_anoletivo'])
                                       ->orderBy('data_falta', 'DESC')
                                       ->findAll();

        // Verificar se foi enviado email de atribuição a este professor
        $emailAtribuicaoLog = $this->logModel
                                   ->where('user_nif', $nif)
                                   ->where('acao', 'Email notificação atribuição')
                                   ->orderBy('criado_em', 'DESC')
                                   ->first();
        $emailAtribuicaoEnviado = $emailAtribuicaoLog ? true : false;

        $data = [
            'titulo' => 'Gestão de Férias - ' . $professor['name'],
            'userData' => $userData,
            'professor' => $professor,
            'ano_letivo' => $anoAtivo,
            'ano_anterior' => $anoAnterior,
            'ano_seguinte' => $anoSeguinte ?? null,
            'atribuicao' => $atribuicao,
            'atribuicao_ano_anterior' => $atribuicaoAnoAnterior,
            'dias_gozados_anterior' => $diasGozadosAnterior,
            'dias_atribuidos_anterior' => $diasAtribuidosAnterior,
            'saldo' => $saldo,
            'faltas' => $faltas,
            'faltas_desconto_ano_atual' => $faltasDescontoAnoAtual,
            'pedidos' => $pedidos,
            'logs' => $logs,
            'email_atribuicao_enviado' => $emailAtribuicaoEnviado,
        ];

        return view('ferias/secretaria_gerir_professor', $data);
    }

    /**
     * Editar pedido de férias criado pela secretaria (AJAX)
     */
    public function editarPedidoSecretaria($pedidoId)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $pedido = $this->pedidoModel->find($pedidoId);
            if (!$pedido) {
                return $this->failNotFound('Pedido não encontrado');
            }

            if (!empty($pedido['submetido_em'])) {
                return $this->fail('Só é possível editar pedidos criados pela secretaria');
            }

            $periodos    = $this->request->getPost('periodos');
            $observacoes = $this->request->getPost('observacoes') ?? '';

            if (empty($periodos) || !is_array($periodos)) {
                return $this->fail('É necessário definir pelo menos um período');
            }

            $totalDias = 0;
            $periodosValidados = [];
            foreach ($periodos as $p) {
                if (empty($p['data_inicio']) || empty($p['data_fim'])) continue;
                if ($p['data_inicio'] > $p['data_fim']) {
                    return $this->fail('Data de início não pode ser posterior à data de fim');
                }
                $diasUteis = calcular_dias_uteis($p['data_inicio'], $p['data_fim']);
                if ($diasUteis <= 0) {
                    return $this->fail('O período ' . $p['data_inicio'] . ' - ' . $p['data_fim'] . ' não contém dias úteis');
                }
                $totalDias += $diasUteis;
                $periodosValidados[] = [
                    'data_inicio' => $p['data_inicio'],
                    'data_fim'    => $p['data_fim'],
                    'dias_uteis'  => $diasUteis,
                ];
            }

            if (empty($periodosValidados)) {
                return $this->fail('Nenhum período válido fornecido');
            }

            // Substituir períodos existentes
            $this->periodoModel->where('pedido_id', $pedidoId)->delete();
            foreach ($periodosValidados as $periodo) {
                $this->periodoModel->insert([
                    'pedido_id'   => $pedidoId,
                    'data_inicio' => $periodo['data_inicio'],
                    'data_fim'    => $periodo['data_fim'],
                    'dias_uteis'  => $periodo['dias_uteis'],
                ]);
            }

            // Atualizar pedido
            $this->pedidoModel->update($pedidoId, [
                'total_dias'             => $totalDias,
                'observacoes_secretaria' => 'Marcado pela secretaria' . ($observacoes ? ': ' . $observacoes : ''),
            ]);

            $this->logModel->registrarAcao(
                null,
                $pedido['user_nif'],
                'Edição de férias pela secretaria',
                "Pedido #{$pedidoId} editado: {$totalDias} dias em " . count($periodosValidados) . ' período(s)',
                $userData['id']
            );

            return $this->respond([
                'success' => true,
                'message' => "Férias actualizadas com sucesso ({$totalDias} dias úteis)",
            ]);

        } catch (\Exception $e) {
            log_message('error', 'editarPedidoSecretaria: ' . $e->getMessage());
            return $this->fail('Erro ao editar férias: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar dados obrigatórios do professor (AJAX)
     */
    public function atualizarDadosProfessor($nif)
    {
        $userData = session()->get('LoggedUserData');
        if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
            return $this->failUnauthorized('Acesso negado');
        }

        try {
            $json = $this->request->getJSON();
            
            // Validar dados recebidos
            if (!isset($json->cod_funcionario) || empty($json->cod_funcionario)) {
                return $this->fail('Código de funcionário é obrigatório');
            }
            
            if (!isset($json->categoria) || empty($json->categoria)) {
                return $this->fail('Categoria é obrigatória');
            }
            
            if (!isset($json->escola_servico) || empty($json->escola_servico)) {
                return $this->fail('Escola de serviço é obrigatória');
            }

            // Verificar se professor existe
            $professor = $this->userModel->where('NIF', $nif)->first();
            if (!$professor) {
                return $this->failNotFound('Professor não encontrado');
            }

            // Atualizar dados no modelo User (bypass validação para update parcial)
            $dados = [
                'cod_funcionario' => $json->cod_funcionario,
                'categoria' => $json->categoria,
                'escola_servico' => $json->escola_servico
            ];

            // Usar Query Builder diretamente para evitar problemas de validação
            $db = \Config\Database::connect();
            $builder = $db->table('user');
            $builder->where('NIF', $nif);
            $builder->update($dados);

            // Verificar se dados foram atualizados
            $professorAtualizado = $this->userModel->where('NIF', $nif)->first();
            $sucesso = ($professorAtualizado['cod_funcionario'] === $json->cod_funcionario && 
                       $professorAtualizado['categoria'] === $json->categoria &&
                       $professorAtualizado['escola_servico'] == $json->escola_servico);

            if ($sucesso) {
                // Registar no log
                $this->logModel->registarLog(
                    $nif,
                    'atualizar_dados',
                    "Atualização de dados obrigatórios: Cód. Funcionário = {$json->cod_funcionario}, Categoria = {$json->categoria}, Escola = {$json->escola_servico}"
                );

                return $this->respond([
                    'success' => true,
                    'message' => 'Dados atualizados com sucesso'
                ]);
            } else {
                return $this->fail('Erro ao atualizar dados do professor');
            }
        } catch (\Exception $e) {
            return $this->fail('Erro ao atualizar dados: ' . $e->getMessage());
        }
    }

    /**
     * API: Obter categorias de professores (AJAX)
     */
    public function apiCategoriasProfessores()
    {
        try {
            $db = \Config\Database::connect();
            
            // Buscar valores ENUM da coluna categoria da tabela user
            $query = $db->query("SHOW COLUMNS FROM user LIKE 'categoria'");
            $row = $query->getRow();
            
            if ($row && isset($row->Type)) {
                // Extrair valores do ENUM
                // Formato: enum('valor1','valor2','valor3')
                preg_match_all("/'([^']+)'/", $row->Type, $matches);
                
                if (!empty($matches[1])) {
                    $categorias = array_map(function($nome) {
                        return ['nome' => $nome];
                    }, $matches[1]);
                    
                    return $this->respond([
                        'success' => true,
                        'data' => $categorias,
                        'total' => count($categorias)
                    ]);
                }
            }
            
            return $this->fail('Não foi possível obter as categorias');
        } catch (\Exception $e) {
            return $this->fail('Erro ao buscar categorias: ' . $e->getMessage());
        }
    }

    /**
     * API: Obter escolas (AJAX)
     */
    public function apiEscolas()
    {
        try {
            $escolas = $this->escolasModel
                            ->select('id, nome, morada')
                            ->orderBy('nome', 'ASC')
                            ->findAll();
            
            return $this->respond([
                'success' => true,
                'data' => $escolas,
                'total' => count($escolas)
            ]);
        } catch (\Exception $e) {
            return $this->fail('Erro ao buscar escolas: ' . $e->getMessage());
        }
    }
}
