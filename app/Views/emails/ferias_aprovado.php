<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedido de Férias Aprovado</title>
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
            background-color: #28a745;
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
            border-left: 4px solid #28a745;
            border-radius: 4px;
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
            background-color: #28a745;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
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
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #28a745;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            color: #155724;
        }
        .alert-info {
            background-color: #d1ecf1;
            border: 1px solid #17a2b8;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            color: #0c5460;
        }
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            color: #856404;
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
        .anexo {
            background-color: #fff;
            border: 2px dashed #007bff;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            text-align: center;
        }
        .anexo-icon {
            font-size: 2em;
            color: #007bff;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>✅ Pedido de Férias Aprovado</h2>
    </div>
    
    <div class="content">
        <p>Exmo(a). Sr(a). <strong><?= esc($pedido['nome_professor']) ?></strong>,</p>
        
        <div class="alert-success">
            <strong>✓ Boas notícias!</strong> O seu pedido de férias para o ano <strong><?= $pedido['ano'] + 1 ?></strong> foi <strong>APROVADO</strong>.
        </div>
        
        <p>Foram aprovados os seguintes períodos de férias:</p>
        
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
            Total: <?= $pedido['total_dias'] ?> dia<?= $pedido['total_dias'] != 1 ? 's' : '' ?> de férias
        </div>
        
        <?php if (!empty($pedido['observacoes_secretaria'])): ?>
        <div class="alert-info">
            <strong>Observações da Secretaria:</strong><br>
            <?= nl2br(esc($pedido['observacoes_secretaria'])) ?>
        </div>
        <?php endif; ?>
        
        <div class="anexo">
            <div class="anexo-icon">📄</div>
            <strong>Documento em Anexo</strong>
            <p style="margin: 10px 0; color: #666;">
                Está anexado a este email o documento de Licença para Férias em formato PDF.
            </p>
        </div>
        
        <div class="alert-warning">
            <strong>⚠️ Próximos Passos - IMPORTANTE:</strong>
            <ol style="margin: 10px 0; padding-left: 20px;">
                <li>Abra o documento PDF em anexo</li>
                <li><strong>Imprima, assine e digitalize</strong> o documento</li>
                <li>Aceda ao sistema através do botão abaixo</li>
                <li>Carregue o documento assinado no sistema</li>
            </ol>
            <p style="margin-top: 10px; font-weight: bold;">
                ⏰ O processo só ficará concluído após o envio do documento assinado.
            </p>
        </div>
        
        <div style="text-align: center;">
            <a href="<?= base_url('ferias') ?>" class="button">Aceder ao Sistema de Férias</a>
        </div>
        
        <p style="margin-top: 20px;">
            <strong>Nota:</strong> Não responda a este email. Para qualquer esclarecimento, contacte os serviços administrativos.
        </p>
    </div>
    
    <div class="footer">
        <p>Esta é uma mensagem automática do Sistema de Gestão Escolar</p>
        <p>Agrupamento de Escolas João de Barros</p>
        <p style="font-size: 0.85em; color: #999; margin-top: 10px;">
            Pedido #<?= $pedido['id'] ?> | Aprovado em <?= date('d/m/Y', strtotime($pedido['aprovado_em'])) ?>
        </p>
    </div>
</body>
</html>
