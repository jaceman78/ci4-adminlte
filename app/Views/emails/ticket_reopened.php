<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Reaberto</title>
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
            background-color: #ffc107;
            color: #000;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .ticket-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #ffc107;
        }
        .ticket-info strong {
            color: #ffc107;
        }
        .alert-warning {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
        }
        .alert-warning strong {
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 0.9em;
        }
        .btn {
            display: inline-block;
            background-color: #ffc107;
            color: #000;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>🔄 Ticket Reaberto</h2>
    </div>
    
    <div class="content">
        <p>Olá, <strong><?= esc($tecnico['name']) ?></strong>,</p>
        
        <div class="alert-warning">
            <strong>⚠️ Atenção:</strong> O ticket #<?= esc($ticket['id']) ?> que estava marcado como <strong>reparado</strong> foi reaberto por <?= esc($adminNome) ?>.
        </div>
        
        <div class="ticket-info">
            <p><strong>Número do Ticket:</strong> #<?= esc($ticket['id']) ?></p>
            <p><strong>Estado Atual:</strong> <span style="color: #ffc107; font-weight: bold;">Em Resolução</span></p>
            <p><strong>Motivo da Reabertura:</strong></p>
            <p style="background-color: #fff3cd; padding: 10px; border-radius: 3px; margin-top: 5px;">
                <?= nl2br(esc($motivo)) ?>
            </p>
        </div>
        
        <div class="ticket-info">
            <h4 style="margin-top: 0; color: #6c757d;">📋 Informações do Ticket Original</h4>
            <p><strong>Tipo de Avaria:</strong> <?= esc($ticket['tipo_avaria_descricao'] ?? 'N/A') ?></p>
            <p><strong>Escola:</strong> <?= esc($ticket['escola_nome'] ?? 'N/A') ?></p>
            <p><strong>Sala:</strong> <?= esc($ticket['codigo_sala'] ?? 'N/A') ?></p>
            <p><strong>Equipamento:</strong> <?= esc($ticket['equipamento_marca'] . ' ' . $ticket['equipamento_modelo']) ?></p>
            <p><strong>Prioridade:</strong> 
                <?php
                $prioridades = ['baixa' => 'Baixa', 'media' => 'Média', 'alta' => 'Alta', 'critica' => 'Crítica'];
                echo esc($prioridades[$ticket['prioridade']] ?? $ticket['prioridade']);
                ?>
            </p>
            <p><strong>Descrição Original do Problema:</strong></p>
            <p style="background-color: #f8f9fa; padding: 10px; border-radius: 3px; margin-top: 5px; white-space: pre-wrap;">
                <?= esc($ticket['descricao']) ?>
            </p>
            <p><strong>Criado em:</strong> <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></p>
        </div>
        
        <p>Por favor, aceda ao sistema e proceda à resolução deste ticket com a maior brevidade possível.</p>
        
        <p style="text-align: center;">
            <a href="<?= base_url('tickets/view/' . $ticket['id']) ?>" class="btn">Ver Detalhes do Ticket</a>
        </p>
    </div>
    
    <div class="footer">
        <p>Este é um email automático. Por favor, não responda a esta mensagem.</p>
        <p>Sistema de Gestão Escolar - <?= date('Y') ?></p>
    </div>
</body>
</html>
