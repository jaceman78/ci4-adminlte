<!DOCTYPE html>
<html>
<head>
    <title>Ticket Aceite e em Resolução</title>
</head>
<body>
    <p>Olá, <strong><?= esc($user["name"]) ?></strong>,</p>
    <p>O seu ticket #<?= esc($ticket["id"]) ?> foi aceite e está agora <strong>Em Resolução</strong>.</p>
    <ul>
        <li><strong>Equipamento:</strong> <?= esc($ticket["equipamento_marca"]) ?> <?= esc($ticket["equipamento_modelo"]) ?></li>
        <li><strong>Sala:</strong> <?= esc($ticket["codigo_sala"]) ?></li>
        <li><strong>Tipo de Avaria:</strong> <?= esc($ticket["tipo_avaria_descricao"]) ?></li>
        <li><strong>Descrição:</strong> <?= esc($ticket["descricao"]) ?></li>
        <li><strong>Atribuído a:</strong> <?= esc($assignedUser["name"]) ?></li>
        <li><strong>Estado Atual:</strong> <?= esc($ticket["estado"]) ?></li>
        <li><strong>Prioridade:</strong> <?= esc($ticket["prioridade"]) ?></li>
        <li><strong>Última Atualização:</strong> <?= esc($ticket["updated_at"]) ?></li>
    </ul>
    <p>Pode acompanhar o progresso na secção "Meus Tickets".</p>
    <p>Obrigado,</p>
    <p>A sua Equipa de Suporte</p>
</body>
</html>
