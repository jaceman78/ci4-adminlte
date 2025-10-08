<!DOCTYPE html>
<html>
<head>
    <title>Ticket Aceite</title>
</head>
<body>
    <p>Olá, <strong><?= esc($user["name"]) ?></strong>,</p>
    <p>Confirmamos que aceitou o ticket #<?= esc($ticket["id"]) ?>. O estado foi atualizado para <strong>Em Resolução</strong>.</p>
    <ul>
        <li><strong>Equipamento:</strong> <?= esc($ticket["equipamento_marca"]) ?> <?= esc($ticket["equipamento_modelo"]) ?></li>
        <li><strong>Sala:</strong> <?= esc($ticket["codigo_sala"]) ?></li>
        <li><strong>Tipo de Avaria:</strong> <?= esc($ticket["tipo_avaria_descricao"]) ?></li>
        <li><strong>Descrição:</strong> <?= esc($ticket["descricao"]) ?></li>
        <li><strong>Estado Atual:</strong> <?= esc($ticket["estado"]) ?></li>
        <li><strong>Prioridade:</strong> <?= esc($ticket["prioridade"]) ?></li>
        <li><strong>Última Atualização:</strong> <?= esc($ticket["updated_at"]) ?></li>
    </ul>
    <p>Pode gerir os seus tickets atribuídos na secção "Tratamento de Tickets".</p>
    <p>Obrigado,</p>
    <p>A sua Equipa de Suporte</p>
</body>
</html>