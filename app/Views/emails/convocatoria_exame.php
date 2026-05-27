<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convocatória de Exame</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin: -30px -30px 20px -30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 20px 0;
        }
        .info-row {
            display: flex;
            margin: 10px 0;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
            color: #555;
        }
        .info-value {
            flex: 1;
        }
        .btn-confirm {
            display: inline-block;
            background-color: #28a745;
            color: white !important;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .btn-confirm:hover {
            background-color: #218838;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .btn-gcalendar {
            display: inline-block;
            background-color: #4285f4;
            color: white !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin: 10px 5px 0 5px;
        }
        .btn-gcalendar:hover {
            background-color: #3367d6;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
        @media only screen and (max-width: 600px) {
            .info-row {
                flex-direction: column;
            }
            .info-label {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php
    // Título e texto de introdução dinâmicos baseados na função
    switch ($convocatoria['funcao'] ?? 'Vigilante') {
        case 'Apoio TIC':
            $tituloEmail    = '🖥️ Convocatória de Apoio TIC';
            $textoIntro     = 'Vem por este meio convocar V. Exa. para a realização de <strong>apoio técnico informático</strong> no seguinte evento:';
            break;
        case 'Estrutura de Apoio':
            $tituloEmail    = '🛠️ Convocatória de Estrutura de Apoio';
            $textoIntro     = 'Vem por este meio convocar V. Exa. para integrar a <strong>estrutura de apoio</strong> no seguinte evento:';
            break;
        case 'Verificar Calculadoras':
            $tituloEmail    = '🔢 Convocatória de Verificação de Calculadoras';
            $textoIntro     = 'Vem por este meio convocar V. Exa. para a <strong>verificação de calculadoras</strong> no seguinte exame:';
            break;
        case 'Verificar Materiais':
            $tituloEmail    = '📋 Convocatória de Verificação de Materiais';
            $textoIntro     = 'Vem por este meio convocar V. Exa. para a <strong>verificação de materiais</strong> no seguinte exame:';
            break;
        case 'Suplente':
            $tituloEmail    = '📋 Convocatória de Suplência';
            $textoIntro     = 'Vem por este meio convocar V. Exa. como <strong>suplente</strong> para o seguinte exame:';
            break;
        case 'Coadjuvante':
            $tituloEmail    = '📋 Convocatória de Coadjuvante';
            $textoIntro     = 'Vem por este meio convocar V. Exa. como <strong>coadjuvante</strong> no seguinte exame:';
            break;
        default: // Vigilante e outros
            $tituloEmail    = '📋 Convocatória de Vigilância';
            $textoIntro     = 'Vem por este meio convocar V. Exa. para a realização de <strong>vigilância</strong> no seguinte exame:';
            break;
    }
    ?>
    <div class="email-container">
        <div class="header">
            <h1><?= $tituloEmail ?></h1>
        </div>

        <p>Exmo(a) Professor(a) <strong><?= esc($convocatoria['professor_nome']) ?></strong>,</p>

        <p><?= $textoIntro ?></p>

        <div class="info-box">
            <h3 style="margin-top: 0; color: #007bff;">Informações do Exame</h3>
            
            <div class="info-row">
                <span class="info-label">Prova:</span>
                <span class="info-value"><strong><?= esc($convocatoria['codigo_prova']) ?> - <?= esc($convocatoria['nome_prova']) ?></strong></span>
            </div>

            <div class="info-row">
                <span class="info-label">Tipo:</span>
                <span class="info-value"><?= esc($convocatoria['tipo_prova']) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Fase:</span>
                <span class="info-value"><strong><?= esc($convocatoria['fase']) ?></strong></span>
            </div>

            <div class="info-row">
                <span class="info-label">Data:</span>
                <span class="info-value"><?= date('d/m/Y', strtotime($convocatoria['data_exame'])) ?></span>
            </div>

            <div class="info-row">
                <span class="info-label">Hora de Início:</span>
                <span class="info-value"><?= date('H:i', strtotime($convocatoria['hora_exame'])) ?>h</span>
            </div>

            <div class="info-row">
                <span class="info-label">Duração:</span>
                <span class="info-value"><?= $convocatoria['duracao_minutos'] ?> minutos</span>
            </div>

            <?php if ($convocatoria['tolerancia_minutos'] > 0): ?>
            <div class="info-row">
                <span class="info-label">Tolerância:</span>
                <span class="info-value"><?= $convocatoria['tolerancia_minutos'] ?> minutos</span>
            </div>
            <?php endif; ?>

            <div class="info-row">
                <span class="info-label">Função:</span>
                <span class="info-value"><strong><?= esc($convocatoria['funcao']) ?></strong></span>
            </div>

            <?php if (!empty($convocatoria['escola_nome'])): ?>
            <div class="info-row">
                <span class="info-label">Escola:</span>
                <span class="info-value"><strong><?= esc($convocatoria['escola_nome']) ?></strong></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($convocatoria['codigo_sala'])): ?>
            <div class="info-row">
                <span class="info-label">Sala:</span>
                <span class="info-value"><?= esc($convocatoria['codigo_sala']) ?>
                    <?php if (!empty($convocatoria['sala_descricao'])): ?>
                        (<?= esc($convocatoria['sala_descricao']) ?>)
                    <?php endif; ?>
                </span>
            </div>
            <?php else: ?>
            <div class="info-row">
                <span class="info-label">Observação:</span>
                <span class="info-value"><em>Função de <?= esc($convocatoria['funcao']) ?> (sem sala específica atribuída)</em></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="alert-warning">
            <?php
            // Todos os docentes devem comparecer 45 minutos antes do início
            $minutosAntecedencia = 45;
            // 'prova': Prova Ensaio, ModA, 4º ano, 6º ano; 'exame': restantes
            $ehProva = ($convocatoria['fase'] === 'Prova Ensaio')
                    || in_array($convocatoria['tipo_prova'] ?? '', ['MODa', 'ModA'])
                    || in_array($convocatoria['ano_escolaridade'] ?? 0, [4, 6]);
            $textoEvento = $ehProva ? 'prova' : 'exame';
            
            // Local de comparecimento:
            // - Estrutura de Apoio: Para 4º ano
            // - Coordenação da Escola: Para 6º ano
            // - Secretariado de Exames: Para outros anos
            
            // Verificar se é equipa de apoio ou suplentes
            $ehEquipaEspecial = isset($convocatoria['tipo_prova']) && in_array($convocatoria['tipo_prova'], ['Verificacao Calculadoras', 'Apoio TIC', 'Estrutura de Apoio', 'Suplentes', 'Verificação de Materiais']);

            // Para SUP-MANHA e TIC-APOIO em Fase única, usar o local específico da Escola Básica de Corroios
            $usaLocalEscolaCorroiosFaseUnica = in_array($convocatoria['codigo_prova'] ?? '', ['SUP-MANHA', 'TIC-APOIO'])
                && (($convocatoria['fase'] ?? '') === 'Fase única');

            // Para equipas especiais do 4º ano, usar texto/local específico de Estrutura de Apoio
            $usaTexto4AnoEspecial = in_array($convocatoria['codigo_prova'] ?? '', ['SUP-MANHA', 'SUP-TARDE', 'TIC-APOIO', 'EST-APOIO'])
                && isset($temProva4AnoNoDia) && $temProva4AnoNoDia;
            
            if ($usaTexto4AnoEspecial) {
                $localComparecimento = 'Estrutura de Apoio';
            } elseif ($usaLocalEscolaCorroiosFaseUnica) {
                $localComparecimento = 'Escola Básica de Corroios, na Sala de Apoio ao Secretariado de Exames';
            } elseif ($convocatoria['ano_escolaridade'] == 4) {
                $localComparecimento = 'Estrutura de Apoio';
            } elseif (isset($convocatoria['tipo_prova']) && ($convocatoria['tipo_prova'] === 'MODa' || $convocatoria['tipo_prova'] === 'ModA')) {
                $localComparecimento = 'Escola Básica de Corroios, na Sala de Apoio ao Secretariado de Exames';
            } elseif ($ehEquipaEspecial && isset($temProva4AnoNoDia) && $temProva4AnoNoDia) {
                $localComparecimento = 'Estrutura de Apoio';
            } elseif ($convocatoria['ano_escolaridade'] == 6) {
                // Prova é do 6º ano
                $localComparecimento = 'Coordenação da Escola';
            } elseif ($ehEquipaEspecial && isset($temProva6AnoNoDia) && $temProva6AnoNoDia) {
                // Equipa de apoio/suplentes em dia com prova do 6º ano
                $localComparecimento = 'Coordenação da Escola';
            } else {
                // Outros casos
                $localComparecimento = 'Secretariado de Exames';
            }
            ?>
            <?php if ($convocatoria['ano_escolaridade'] == 4 || $usaTexto4AnoEspecial): ?>
            <strong>⚠ IMPORTANTE:</strong> Todos os docentes convocados devem comparecer na escola, junto à <strong>Estrutura de Apoio</strong>, com <strong>45 minutos de antecedência</strong> ao horário de início da prova, para receberem as informações necessárias.
            <?php elseif ($usaLocalEscolaCorroiosFaseUnica): ?>
            <strong>⚠ IMPORTANTE:</strong> Todos os docentes convocados devem comparecer na <strong>Escola Básica de Corroios, na Sala de Apoio ao Secretariado de Exames</strong>, com <strong>45 minutos de antecedência</strong> ao horário de início da prova, para receberem as instruções necessárias.
            <?php else: ?>
            <strong>⚠ IMPORTANTE:</strong> Todos os docentes convocados devem comparecer na <strong><?= $localComparecimento ?></strong> com <strong><?= $minutosAntecedencia ?> minutos de antecedência</strong> ao horário de início da <?= $textoEvento ?>, para receberem as informações necessárias.
            <?php endif; ?>
        </div>

        <?php if (!empty($convocatoria['observacoes'])): ?>
        <div class="info-box">
            <strong>📝 Observações:</strong>
            <p style="margin: 10px 0 0 0;"><?= nl2br(esc($convocatoria['observacoes'])) ?></p>
        </div>
        <?php endif; ?>

        <?php
        // Gerar URL do Google Calendar
        $gcDataHora  = $convocatoria['data_exame'] . ' ' . $convocatoria['hora_exame'];
        $gcInicioTs  = strtotime($gcDataHora) - (45 * 60); // comparecer 45 min antes
        $gcFimTs     = strtotime($gcDataHora) + (($convocatoria['duracao_minutos'] + ($convocatoria['tolerancia_minutos'] ?? 0)) * 60);
        $gcText      = $convocatoria['funcao'] . ' – ' . $convocatoria['codigo_prova'] . ' ' . $convocatoria['nome_prova'] . ' (' . $convocatoria['fase'] . ')';
        $gcDetails   = 'Função: ' . $convocatoria['funcao']
                     . '&#10;Sala: ' . ($convocatoria['codigo_sala'] ?? 'Sem sala atribuída')
                     . '&#10;Comparecer na ' . $localComparecimento . ' 45 minutos antes do início.';
        $gcLocation  = 'Agrupamento de Escolas João de Barros';
        $googleCalUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE'
            . '&text='     . rawurlencode($gcText)
            . '&dates='    . date('Ymd\THis', $gcInicioTs) . '/' . date('Ymd\THis', $gcFimTs)
            . '&details='  . rawurlencode(html_entity_decode($gcDetails))
            . '&location=' . rawurlencode($gcLocation);
        ?>
        <div style="text-align: center; margin: 30px 0;">
            <p><strong>Por favor, confirme a sua presença clicando no botão abaixo:</strong></p>
            <a href="<?= $confirmUrl ?>" class="btn-confirm">
                ✓ CONFIRMAR PRESENÇA
            </a>
            <p style="font-size: 12px; color: #6c757d; margin-top: 10px;">
                Ou copie e cole este link no seu navegador:<br>
                <span style="word-break: break-all;"><?= $confirmUrl ?></span>
            </p>
            <p style="margin-top: 20px;">
                <a href="<?= $googleCalUrl ?>" class="btn-gcalendar" target="_blank">
                    📅 Adicionar ao Google Calendar
                </a>
            </p>
            <p style="font-size: 11px; color: #6c757d; margin-top: 6px;">
                O evento inclui 45 minutos de antecedência para comparecimento.
            </p>
        </div>

        <p>Agradecemos a sua colaboração e disponibilidade.</p>

        <p>Com os melhores cumprimentos,<br>
        <strong>A Direção</strong></p>

        <div class="alert-warning" style="margin-top: 30px;">
            <strong>ℹ️ Nota Importante:</strong> Este email é meramente informativo. A convocatória oficial encontra-se afixada em local de estilo.
        </div>

        <div class="footer">
            <p>Este é um email automático gerado pelo Sistema de Gestão Escolar.</p>
            <p>Em caso de dúvidas, contacte o secretariado de exames.</p>
        </div>
    </div>
</body>
</html>
