<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Eliminado</title>
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
            background-color: #dc3545;
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
            border-left: 4px solid #dc3545;
        }
        .ticket-info strong {
            color: #dc3545;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>🗑️ Ticket Eliminado</h2>
    </div>
    
    <div class="content">
        <p>Olá, <strong><?= esc($user['name']) ?></strong>,</p>
        
        <p>O ticket #<?= esc($ticket['id']) ?> foi eliminado do sistema.</p>
        
        <div class="ticket-info">
            <p><strong>Número do Ticket:</strong> #<?= esc($ticket['id']) ?></p>
            <p><strong>Tipo de Avaria:</strong> <?= esc($ticket['tipo_avaria_nome'] ?? 'N/A') ?></p>
            <p><strong>Descrição:</strong> <?= esc($ticket['descricao']) ?></p>
            <p><strong>Data de Criação:</strong> <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></p>
            <p><strong>Data de Eliminação:</strong> <?= date('d/m/Y H:i') ?></p>
        </div>
        
        <p>Este ticket foi removido permanentemente do sistema. Se tiver alguma questão, por favor contacte o administrador.</p>
    </div>
    
    <div class="footer">
        <p>Este é um email automático. Por favor, não responda a esta mensagem.</p>
        <p>Sistema de Gestão Escolar - <?= date('Y') ?></p>
    </div>
</body>
</html>
