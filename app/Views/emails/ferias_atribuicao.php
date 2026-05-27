<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atribuição de Férias</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .info-box {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        .info-value {
            color: #212529;
        }
        .total {
            background-color: #d4edda;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
            color: #155724;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 0.9em;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>📅 Atribuição de Férias - <?= $ano ?>/<?= $ano_seguinte ?></h2>
    </div>
    
    <div class="content">
        <p>Exmo(a). Sr(a). <strong><?= esc($nome) ?></strong>,</p>
        
        <p>Informamos que foram atribuídos os seguintes dias de férias para o ano letivo <strong><?= $ano ?>/<?= $ano_seguinte ?></strong>:</p>
        
        <h3 style="color: #007bff; margin-top: 25px;">📊 Cálculo Detalhado</h3>
        
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Dias Base:</span>
                <span class="info-value"><?= $dias_base ?> dias</span>
            </div>
            
            <?php if ($dias_ajuste != 0): ?>
            <div class="info-row">
                <span class="info-label">+ Ajuste (Ano Anterior):</span>
                <span class="info-value" style="color: <?= $dias_ajuste >= 0 ? '#28a745' : '#dc3545' ?>;">
                    <?= $dias_ajuste >= 0 ? '+' : '' ?><?= $dias_ajuste ?> dias
                </span>
            </div>
            <?php endif; ?>
            
            <?php if ($dias_extra != 0): ?>
            <div class="info-row">
                <span class="info-label">+ Extras/Acertos:</span>
                <span class="info-value" style="color: <?= $dias_extra >= 0 ? '#28a745' : '#dc3545' ?>;">
                    <?= $dias_extra >= 0 ? '+' : '' ?><?= $dias_extra ?> dias
                </span>
            </div>
            <?php endif; ?>
            
            <div class="info-row" style="background-color: #d1ecf1; font-weight: bold;">
                <span class="info-label">= TOTAL ATRIBUÍDO:</span>
                <span class="info-value"><?= $dias_total ?> dias</span>
            </div>
            
            <?php if ($dias_gastos > 0): ?>
            <div class="info-row">
                <span class="info-label">- Dias Já Marcados:</span>
                <span class="info-value" style="color: #dc3545;">-<?= $dias_gastos ?> dias</span>
            </div>
            <?php endif; ?>
            
            <?php if ($dias_desconto_faltas > 0): ?>
            <div class="info-row">
                <span class="info-label">- Desconto por Faltas:</span>
                <span class="info-value" style="color: #dc3545;">-<?= number_format($dias_desconto_faltas, 1) ?> dias</span>
            </div>
            <?php endif; ?>
            
            <div class="info-row" style="background-color: #d4edda; font-weight: bold; font-size: 1.1em;">
                <span class="info-label">= DIAS DISPONÍVEIS:</span>
                <span class="info-value" style="color: <?= $dias_disponiveis >= 0 ? '#155724' : '#721c24' ?>;">
                    <?= $dias_disponiveis ?> dias
                </span>
            </div>
        </div>
        
        <?php if ($dias_atribuidos_anterior > 0 || $dias_gozados_anterior > 0): ?>
        <div style="background-color: #e7f3ff; padding: 15px; margin: 15px 0; border-radius: 4px; border-left: 4px solid #007bff;">
            <h4 style="margin-top: 0; color: #007bff;">ℹ️ Informação do Ano Anterior</h4>
            <?php if ($dias_atribuidos_anterior > 0): ?>
            <p style="margin: 5px 0;"><strong>Dias Atribuídos:</strong> <?= $dias_atribuidos_anterior ?> dias</p>
            <?php endif; ?>
            <?php if ($dias_gozados_anterior > 0): ?>
            <p style="margin: 5px 0;"><strong>Dias Gozados:</strong> <?= $dias_gozados_anterior ?> dias</p>
            <?php endif; ?>
            <?php if ($dias_ajuste != 0): ?>
            <p style="margin: 5px 0;"><strong>Diferença Transportada:</strong> 
                <span style="color: <?= $dias_ajuste >= 0 ? '#28a745' : '#dc3545' ?>;">
                    <?= $dias_ajuste >= 0 ? '+' : '' ?><?= $dias_ajuste ?> dias
                </span>
            </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($observacoes)): ?>
        <div class="alert">
            <strong>📝 Observações:</strong><br>
            <?= nl2br(esc($observacoes)) ?>
        </div>
        <?php endif; ?>
        
        <p>Para consultar o seu saldo e marcar as suas férias, aceda ao sistema através do link abaixo:</p>
        
        <div style="text-align: center;">
            <a href="<?= $link_ferias ?>" class="button">Aceder ao Sistema de Férias</a>
        </div>
        
        <p style="margin-top: 20px;">
            <strong>⚠️ Nota importante:</strong> Não responda a este email. Para qualquer esclarecimento, contacte os serviços administrativos.
        </p>
    </div>
    
    <div class="footer">
        <p>Esta é uma mensagem automática do Sistema de Gestão Escolar</p>
        <p>Agrupamento de Escolas João de Barros</p>
    </div>
</body>
</html>
