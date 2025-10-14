<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Atribuído</title>
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
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .ticket-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #17a2b8;
        }
        .ticket-info strong {
            color: #17a2b8;
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
            background-color: #17a2b8;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>📋 Novo Ticket Atribuído</h2>
    </div>
    
    <div class="content">
        <p>Olá, <strong><?= esc($assignedUser['name']) ?></strong>,</p>
        
        <p>Foi-lhe atribuído um novo ticket para resolução.</p>
        
        <div class="ticket-info">
            <p><strong>Número do Ticket:</strong> #<?= esc($ticket['id']) ?></p>
            <p><strong>Tipo de Avaria:</strong> <?= esc($ticket['tipo_avaria_nome'] ?? 'N/A') ?></p>
            <p><strong>Escola:</strong> <?= esc($ticket['escola_nome'] ?? 'N/A') ?></p>
            <p><strong>Sala:</strong> <?= esc($ticket['sala_nome'] ?? 'N/A') ?></p>
            <p><strong>Equipamento:</strong> <?= esc($ticket['equipamento_info'] ?? 'N/A') ?></p>
            <p><strong>Descrição:</strong> <?= esc($ticket['descricao']) ?></p>
            <p><strong>Prioridade:</strong> 
                <?php
                $prioridades = ['baixa' => 'Baixa', 'media' => 'Média', 'alta' => 'Alta', 'urgente' => 'Urgente'];
                echo esc($prioridades[$ticket['prioridade']] ?? $ticket['prioridade']);
                ?>
            </p>
            <p><strong>Data de Criação:</strong> <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></p>
        </div>
        
        <p>Por favor, verifique os detalhes e proceda à resolução do problema.</p>
        
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
