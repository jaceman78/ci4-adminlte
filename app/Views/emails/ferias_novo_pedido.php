<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Pedido de Férias</title>
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
            background-color: #17a2b8;
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
            border-left: 4px solid #17a2b8;
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
        .periodo {
            padding: 10px;
            margin: 8px 0;
            background-color: #f8f9fa;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .periodo-datas {
            font-weight: bold;
            color: #212529;
        }
        .periodo-dias {
            background-color: #17a2b8;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
        }
        .total {
            background-color: #d1ecf1;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
            color: #0c5460;
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
        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>🔔 Novo Pedido de Férias</h2>
    </div>
    
    <div class="content">
        <div class="alert-info">
            <strong>📬 Notificação:</strong> Foi submetido um novo pedido de férias que aguarda análise da secretaria.
        </div>
        
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Professor:</span>
                <span class="info-value"><?= esc($pedido['nome_professor']) ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">NIF:</span>
                <span class="info-value"><?= $pedido['user_nif'] ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Ano Letivo:</span>
                <span class="info-value"><?= $pedido['ano'] + 1 ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Submetido em:</span>
                <span class="info-value"><?= date('d/m/Y H:i', strtotime($pedido['submetido_em'])) ?></span>
            </div>
        </div>
        
        <p><strong>Períodos solicitados:</strong></p>
        
        <div class="info-box">
            <?php foreach ($pedido['periodos'] as $index => $periodo): ?>
                <div class="periodo">
                    <div class="periodo-datas">
                        <?= date('d/m/Y', strtotime($periodo['data_inicio'])) ?> 
                        a 
                        <?= date('d/m/Y', strtotime($periodo['data_fim'])) ?>
                    </div>
                    <div class="periodo-dias">
                        <?= $periodo['dias_uteis'] ?> dia<?= $periodo['dias_uteis'] != 1 ? 's' : '' ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="total">
            Total: <?= $pedido['total_dias'] ?> dia<?= $pedido['total_dias'] != 1 ? 's' : '' ?>
        </div>
        
        <?php if (!empty($pedido['observacoes_professor'])): ?>
        <div class="info-box">
            <strong>Observações do Professor:</strong>
            <p style="margin: 10px 0 0 0;">
                <?= nl2br(esc($pedido['observacoes_professor'])) ?>
            </p>
        </div>
        <?php endif; ?>
        
        <p>Por favor, aceda ao sistema para analisar e processar este pedido:</p>
        
        <div style="text-align: center;">
            <a href="<?= base_url('ferias/pedidos-pendentes') ?>" class="button">Ver Pedidos Pendentes</a>
        </div>
        
        <p style="margin-top: 20px; font-size: 0.9em; color: #6c757d;">
            Esta é uma notificação automática do sistema de gestão de férias.
        </p>
    </div>
    
    <div class="footer">
        <p>Sistema de Gestão Escolar</p>
        <p>Agrupamento de Escolas João de Barros</p>
        <p style="font-size: 0.85em; color: #999; margin-top: 10px;">
            Pedido #<?= $pedido['id'] ?>
        </p>
    </div>
</body>
</html>
