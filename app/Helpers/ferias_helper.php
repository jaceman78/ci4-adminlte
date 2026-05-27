<?php

/**
 * Helper de Férias
 * Funções auxiliares para o sistema de gestão de férias
 */

if (!function_exists('calcular_dias_uteis')) {
    /**
     * Calcular o número de dias úteis entre duas datas
     * Exclui fins de semana (sábado e domingo) e feriados de Portugal
     * 
     * @param string $dataInicio Data de início (Y-m-d)
     * @param string $dataFim Data de fim (Y-m-d)
     * @param bool $incluirFeriados Se false, não exclui feriados (apenas fins de semana)
     * @return int Número de dias úteis
     */
    function calcular_dias_uteis($dataInicio, $dataFim, $incluirFeriados = true)
    {
        $inicio = new DateTime($dataInicio);
        $fim = new DateTime($dataFim);
        $fim->modify('+1 day'); // Incluir o último dia

        $intervalo = new DateInterval('P1D');
        $periodo = new DatePeriod($inicio, $intervalo, $fim);

        $diasUteis = 0;
        $feriados = [];

        // Carregar feriados se necessário
        if ($incluirFeriados) {
            $feriadosModel = new \App\Models\FeriasFeriadosModel();
            $feriadosData = $feriadosModel->getFeriadosPorIntervalo($dataInicio, $dataFim);
            foreach ($feriadosData as $feriado) {
                $feriados[] = $feriado['data'];
            }
        }

        foreach ($periodo as $data) {
            $diaSemana = $data->format('N'); // 1 (segunda) a 7 (domingo)
            $dataFormatada = $data->format('Y-m-d');

            // Verificar se não é fim de semana (sábado ou domingo)
            $ehFimSemana = ($diaSemana == 6 || $diaSemana == 7);

            // Verificar se não é feriado
            $ehFeriado = in_array($dataFormatada, $feriados);

            // Contar como dia útil se não for fim de semana nem feriado
            if (!$ehFimSemana && !$ehFeriado) {
                $diasUteis++;
            }
        }

        return $diasUteis;
    }
}

if (!function_exists('validar_periodo_ferias')) {
    /**
     * Validar se um período de férias é válido
     * 
     * @param string $dataInicio Data de início
     * @param string $dataFim Data de fim
     * @param string $anoLetivoAtual Ano letivo atual (ex: "2025")
     * @return array ['valido' => bool, 'erro' => string]
     */
    function validar_periodo_ferias($dataInicio, $dataFim, $anoLetivoAtual = null)
    {
        // Validar formato de datas
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataInicio) || 
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataFim)) {
            return ['valido' => false, 'erro' => 'Formato de data inválido'];
        }

        $inicio = strtotime($dataInicio);
        $fim = strtotime($dataFim);

        // Data de fim deve ser maior ou igual à data de início
        if ($fim < $inicio) {
            return ['valido' => false, 'erro' => 'A data de fim deve ser posterior à data de início'];
        }

        // Calcular dias úteis
        $diasUteis = calcular_dias_uteis($dataInicio, $dataFim);

        if ($diasUteis == 0) {
            return ['valido' => false, 'erro' => 'O período selecionado não contém dias úteis'];
        }

        // Validar se está dentro do ano letivo atual (opcional)
        if ($anoLetivoAtual) {
            // Buscar configuração do banco de dados
            $db = \Config\Database::connect();
            $builder = $db->table('ferias_configuracao fc');
            $builder->select('fc.data_inicio_permitida, fc.data_fim_permitida, fc.permite_marcacao, fc.mensagem_bloqueio');
            $builder->join('ano_letivo al', 'al.id_anoletivo = fc.anoletivo_id');
            $builder->where('al.anoletivo', $anoLetivoAtual);
            $config = $builder->get()->getRowArray();

            if ($config) {
                // Usar configuração do banco de dados
                if (!$config['permite_marcacao']) {
                    $mensagem = $config['mensagem_bloqueio'] ?? 'A marcação de férias está temporariamente desativada';
                    return ['valido' => false, 'erro' => $mensagem];
                }

                $limiteInicio = strtotime($config['data_inicio_permitida']);
                $limiteFim = strtotime($config['data_fim_permitida']);
            } else {
                // Fallback para configuração padrão
                $anoLetivoInt = (int) $anoLetivoAtual;
                $limiteInicio = strtotime("{$anoLetivoInt}-09-01");
                $limiteFim = strtotime(($anoLetivoInt + 1) . "-12-31");
            }

            if ($inicio < $limiteInicio || $fim > $limiteFim) {
                $periodoPermitido = date('d/m/Y', $limiteInicio) . ' e ' . date('d/m/Y', $limiteFim);
                return [
                    'valido' => false, 
                    'erro' => "As férias devem ser marcadas dentro do período permitido: " . $periodoPermitido . ". Por favor, ajuste as datas selecionadas."
                ];
            }
        }

        return ['valido' => true, 'dias_uteis' => $diasUteis];
    }
}

if (!function_exists('verificar_saldo_ferias')) {
    /**
     * Verificar se o professor tem saldo suficiente para o período solicitado
     * 
     * @param string $userNif NIF do professor
     * @param int $anoLetivoId ID do ano letivo
     * @param int $diasSolicitados Dias solicitados
     * @return array ['tem_saldo' => bool, 'dias_disponiveis' => int, 'dias_faltam' => int]
     */
    function verificar_saldo_ferias($userNif, $anoLetivoId, $diasSolicitados)
    {
        $atribuicaoModel = new \App\Models\FeriasAtribuicaoModel();
        $saldo = $atribuicaoModel->calcularSaldo($userNif, $anoLetivoId);

        $diasDisponiveis = $saldo['dias_disponiveis'];
        $temSaldo = $diasDisponiveis >= $diasSolicitados;
        $diasFaltam = $temSaldo ? 0 : ($diasSolicitados - $diasDisponiveis);

        return [
            'tem_saldo'         => $temSaldo,
            'dias_disponiveis'  => $diasDisponiveis,
            'dias_faltam'       => $diasFaltam,
            'dias_total'        => $saldo['dias_total'],
            'dias_gastos'       => $saldo['dias_gastos']
        ];
    }
}

if (!function_exists('formatar_estado_ferias')) {
    /**
     * Formatar estado do pedido de férias com badge Bootstrap
     * 
     * @param string $estado Estado do pedido
     * @return string HTML do badge
     */
    function formatar_estado_ferias($estado)
    {
        $badges = [
            'por_preencher'          => '<span class="badge bg-secondary">Por Preencher</span>',
            'submetido'              => '<span class="badge bg-info">Submetido</span>',
            'em_aprovacao'           => '<span class="badge bg-warning text-dark">Em Aprovação</span>',
            'aprovado'               => '<span class="badge bg-success">Aprovado</span>',
            'rejeitado'              => '<span class="badge bg-danger">Rejeitado</span>',
            'cancelado'              => '<span class="badge bg-dark">Cancelado</span>',
            'aguarda_assinatura'     => '<span class="badge bg-primary">Aguarda Assinatura</span>',
            'concluido'              => '<span class="badge bg-success"><i class="fas fa-check"></i> Concluído</span>',
            'remarcacao_solicitada'  => '<span class="badge bg-warning"><i class="fas fa-clock"></i> Remarcação Solicitada</span>',
            'remarcacao_aprovada'    => '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Remarcação Aprovada</span>'
        ];

        return $badges[$estado] ?? '<span class="badge bg-secondary">Desconhecido</span>';
    }
}

if (!function_exists('verificar_conflito_ferias')) {
    /**
     * Verificar se existe conflito de datas de férias para um professor
     * 
     * @param string $userNif NIF do professor
     * @param string $dataInicio Data de início
     * @param string $dataFim Data de fim
     * @param int|null $excluirPedidoId ID do pedido a excluir (útil em edições)
     * @return array ['tem_conflito' => bool, 'periodo_conflitante' => array|null]
     */
    function verificar_conflito_ferias($userNif, $dataInicio, $dataFim, $excluirPedidoId = null)
    {
        $periodoModel = new \App\Models\FeriasPeriodoModel();
        $conflito = $periodoModel->verificarConflito($userNif, $dataInicio, $dataFim, $excluirPedidoId);

        if ($conflito) {
            return [
                'tem_conflito'         => true,
                'periodo_conflitante'  => $conflito,
                'mensagem'             => "Existe um conflito com férias já marcadas de {$conflito['data_inicio']} a {$conflito['data_fim']}"
            ];
        }

        return ['tem_conflito' => false, 'periodo_conflitante' => null];
    }
}

if (!function_exists('obter_periodos_ferias_ano')) {
    /**
     * Obter todos os períodos de férias de um ano letivo
     * (útil para gerar calendário visual)
     * 
     * @param int $anoLetivoId ID do ano letivo
     * @return array
     */
    function obter_periodos_ferias_ano($anoLetivoId)
    {
        // Obter datas do ano letivo
        $anoLetivoModel = new \App\Models\AnoLetivoModel();
        $anoLetivo = $anoLetivoModel->find($anoLetivoId);

        if (!$anoLetivo) {
            return [];
        }

        $ano = (int) $anoLetivo['anoletivo'];
        $dataInicio = date("{$ano}-09-01");
        $dataFim = date(($ano + 1) . "-08-31");

        $periodoModel = new \App\Models\FeriasPeriodoModel();
        return $periodoModel->getPeriodosPorIntervalo($dataInicio, $dataFim, $anoLetivoId);
    }
}

if (!function_exists('calcular_saldo_final_ano')) {
    /**
     * Calcular saldo final de férias de um professor num ano letivo
     * (para transição de dias para o ano seguinte)
     * 
     * @param string $userNif NIF do professor
     * @param int $anoLetivoId ID do ano letivo
     * @return array ['saldo_final' => int, 'pode_transitar' => bool, 'dias_a_transitar' => int]
     */
    function calcular_saldo_final_ano($userNif, $anoLetivoId)
    {
        $atribuicaoModel = new \App\Models\FeriasAtribuicaoModel();
        $saldo = $atribuicaoModel->calcularSaldo($userNif, $anoLetivoId);

        $saldoFinal = $saldo['dias_disponiveis'];
        
        // Limitar transição (máximo 5 dias positivos ou negativos)
        $diasATransitar = min(max($saldoFinal, -5), 5);
        $podeTransitar = $diasATransitar != 0;

        return [
            'saldo_final'      => $saldoFinal,
            'pode_transitar'   => $podeTransitar,
            'dias_a_transitar' => $diasATransitar,
            'mensagem'         => $podeTransitar 
                ? "Serão transitados {$diasATransitar} dias para o próximo ano letivo" 
                : "Sem dias a transitar"
        ];
    }
}

if (!function_exists('enviar_email_ferias')) {
    /**
     * Enviar email relacionado com férias
     * 
     * @param string $destinatario Email do destinatário
     * @param string $assunto Assunto do email
     * @param string $mensagem Mensagem do email
     * @param array $anexos Array de caminhos de anexos (opcional)
     * @return bool
     */
    function enviar_email_ferias($destinatario, $assunto, $mensagem, $anexos = [])
    {
        $email = \Config\Services::email();

        $email->setTo($destinatario);
        $email->setReplyTo('secretaria@aejoaodebarros.pt', 'Secretaria');
        $email->setSubject($assunto);
        $email->setMessage($mensagem);

        // Adicionar anexos se fornecidos
        foreach ($anexos as $anexo) {
            if (file_exists($anexo)) {
                $email->attach($anexo);
            }
        }

        if ($email->send()) {
            log_message('info', "Email de férias enviado para {$destinatario}: {$assunto}");
            return true;
        } else {
            log_message('error', "Erro ao enviar email de férias para {$destinatario}: " . $email->printDebugger(['headers']));
            return false;
        }
    }
}

if (!function_exists('gerar_array_calendario')) {
    /**
     * Gerar array para exibição de calendário mensal
     * 
     * @param int $mes Mês (1-12)
     * @param int $ano Ano (4 dígitos)
     * @param array $periodos Array de períodos de férias a marcar
     * @return array
     */
    function gerar_array_calendario($mes, $ano, $periodos = [])
    {
        $primeiroDia = mktime(0, 0, 0, $mes, 1, $ano);
        $diasNoMes = date('t', $primeiroDia);
        $diaSemanaInicio = date('N', $primeiroDia); // 1 (seg) a 7 (dom)

        $calendario = [];
        $dia = 1;

        // Gerar array de datas marcadas
        $datasComFerias = [];
        foreach ($periodos as $periodo) {
            $inicio = strtotime($periodo['data_inicio']);
            $fim = strtotime($periodo['data_fim']);
            
            for ($d = $inicio; $d <= $fim; $d += 86400) { // 86400 = 1 dia em segundos
                $data = date('Y-m-d', $d);
                if (date('m', $d) == $mes && date('Y', $d) == $ano) {
                    $datasComFerias[$data] = $periodo;
                }
            }
        }

        // Construir calendário (semanas)
        for ($semana = 0; $semana < 6; $semana++) {
            $linha = [];

            for ($diaSemana = 1; $diaSemana <= 7; $diaSemana++) {
                if (($semana == 0 && $diaSemana < $diaSemanaInicio) || $dia > $diasNoMes) {
                    $linha[] = ['dia' => '', 'tem_ferias' => false];
                } else {
                    $dataAtual = date('Y-m-d', mktime(0, 0, 0, $mes, $dia, $ano));
                    $temFerias = isset($datasComFerias[$dataAtual]);
                    
                    $linha[] = [
                        'dia' => $dia,
                        'data' => $dataAtual,
                        'tem_ferias' => $temFerias,
                        'periodo' => $temFerias ? $datasComFerias[$dataAtual] : null
                    ];
                    
                    $dia++;
                }
            }

            $calendario[] = $linha;

            if ($dia > $diasNoMes) {
                break;
            }
        }

        return $calendario;
    }
}
