<!DOCTYPE html>
<html>
<head>
    <title>Ticket Atribuído a Você</title>
</head>
<body>
    <p>Olá, <strong><?= esc($assignedUser["name"]) ?></strong>,</p>
    <p>Um novo ticket foi atribuído a você para resolução. Detalhes:</p>
    <ul>
        <li><strong>Ticket ID:</strong> #<?= esc($ticket["id"]) ?></li>
        <li><strong>Equipamento:</strong> <?= esc($ticket["equipamento_marca"]) ?> <?= esc($ticket["equipamento_modelo"]) ?></li>
        <li><strong>Sala:</strong> <?= esc($ticket["codigo_sala"]) ?></li>
        <li><strong>Tipo de Avaria:</strong> <?= esc($ticket["tipo_avaria_descricao"]) ?></li>
        <li><strong>Descrição:</strong> <?= esc($ticket["descricao"]) ?></li>
        <li><strong>Estado Atual:</strong> <?= esc($ticket["estado"]) ?></li>
        <li><strong>Prioridade:</strong> <?= esc($ticket["prioridade"]) ?></li>
        <li><strong>Criado em:</strong> <?= esc($ticket["created_at"]) ?> por <?= esc($ticket["user_nome"]) ?></li>
    </ul>
    <p>Por favor, clique no link abaixo para aceitar o ticket e iniciar a resolução:</p>
    <p><a href="<?= site_url("tickets/accept/" . $ticket["id"]) ?>">Aceitar Ticket #<?= esc($ticket["id"]) ?></a></p>
    <p>Obrigado,</p>
    <p>A sua Equipa de Suporte</p>
</body>
</html>
