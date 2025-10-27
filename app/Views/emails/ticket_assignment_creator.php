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
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .ticket-info {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
            border-left: 4px solid #28a745;
        }
        .ticket-info strong {
            color: #28a745;
        }
        .highlight-box {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin: 15px 0;
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
            background-color: #28a745;
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
        <h2>✅ Ticket Atribuído</h2>
    </div>
    
    <div class="content">
        <p>Olá, <strong><?= esc($creatorUser['name']) ?></strong>,</p>
        
        <p>O seu ticket foi atribuído a um técnico para resolução.</p>
        
        <div class="highlight-box">
            <strong>✓ Técnico Atribuído:</strong> <?= esc($assignedUser['name'] ?? 'N/A') ?>
            <?php if (isset($assignedUser['email'])): ?>
                <br><small>Email: <?= esc($assignedUser['email']) ?></small>
            <?php endif; ?>
        </div>
        
        <div class="ticket-info">
            <p><strong>Número do Ticket:</strong> #<?= esc($ticket['id']) ?></p>
            <p><strong>Tipo de Avaria:</strong> <?= esc($ticket['tipo_avaria_nome'] ?? 'N/A') ?></p>
            <p><strong>Escola:</strong> <?= esc($ticket['escola_nome'] ?? 'N/A') ?></p>
            <p><strong>Sala:</strong> <?= esc($ticket['sala_nome'] ?? 'N/A') ?></p>
            <p><strong>Equipamento:</strong> <?= esc($ticket['equipamento_info'] ?? 'N/A') ?></p>
            <p><strong>Descrição:</strong> <?= esc($ticket['descricao']) ?></p>
            <p><strong>Estado:</strong> 
                <?php
                $estados = [
                    'novo' => 'Novo',
                    'em_resolucao' => 'Em Resolução',
                    'aguarda_peca' => 'Aguarda Peça',
                    'reparado' => 'Reparado'
                ];
                echo esc($estados[$ticket['estado']] ?? $ticket['estado']);
                ?>
            </p>
            <p><strong>Prioridade:</strong> 
                <?php
                $prioridades = ['baixa' => 'Baixa', 'media' => 'Média', 'alta' => 'Alta', 'critica' => 'Crítica'];
                echo esc($prioridades[$ticket['prioridade']] ?? $ticket['prioridade']);
                ?>
            </p>
        </div>
        
        <p>Será notificado sobre o progresso da resolução do seu ticket.</p>
        
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
