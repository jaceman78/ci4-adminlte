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
    <div class="email-container">
        <div class="header">
            <h1>📋 Convocatória de Vigilância</h1>
        </div>

        <p>Exmo(a) Professor(a) <strong><?= esc($convocatoria['professor_nome']) ?></strong>,</p>

        <p>Vem por este meio convocar V. Exa. para a realização de vigilância/função de apoio no seguinte exame:</p>

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
            <strong>⏰ Importante:</strong> Deverá comparecer com <strong>45 minutos de antecedência</strong> ao horário de início do exame para receber as instruções necessárias.
        </div>

        <?php if (!empty($convocatoria['observacoes'])): ?>
        <div class="info-box">
            <strong>📝 Observações:</strong>
            <p style="margin: 10px 0 0 0;"><?= nl2br(esc($convocatoria['observacoes'])) ?></p>
        </div>
        <?php endif; ?>

        <div style="text-align: center; margin: 30px 0;">
            <p><strong>Por favor, confirme a sua presença clicando no botão abaixo:</strong></p>
            <a href="<?= $confirmUrl ?>" class="btn-confirm">
                ✓ CONFIRMAR PRESENÇA
            </a>
            <p style="font-size: 12px; color: #6c757d; margin-top: 10px;">
                Ou copie e cole este link no seu navegador:<br>
                <span style="word-break: break-all;"><?= $confirmUrl ?></span>
            </p>
        </div>

        <p>Agradecemos a sua colaboração e disponibilidade.</p>

        <p>Com os melhores cumprimentos,<br>
        <strong>A Direção</strong></p>

        <div class="alert-warning" style="margin-top: 30px;">
            <strong>ℹ️ Nota Importante:</strong> Este email é meramente informativo. A convocatória oficial encontra-se afixada em local de estilo.
        </div>

        <div class="footer">
            <p>Este é um email automático gerado pelo Sistema de Gestão Escolar.<br>
            Por favor, não responda diretamente a este email.</p>
            <p>Em caso de dúvidas, contacte o secretariado de exames.</p>
        </div>
    </div>
</body>
</html>
