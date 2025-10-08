<!DOCTYPE html>
<html>
<head>
    <title>Atualização do Ticket</title>
</head>
<body>
    <p>Olá, <strong><?= esc($user["name"]) ?></strong>,</p>
    <p>O seu ticket #<?= esc($ticket["id"]) ?> foi atualizado com os seguintes detalhes:</p>
    <ul>
        <li><strong>Equipamento:</strong> <?= esc($ticket["equipamento_marca"]) ?> <?= esc($ticket["equipamento_modelo"]) ?></li>
        <li><strong>Sala:</strong> <?= esc($ticket["codigo_sala"]) ?></li>
        <li><strong>Tipo de Avaria:</strong> <?= esc($ticket["tipo_avaria_descricao"]) ?></li>
        <li><strong>Descrição:</strong> <?= esc($ticket["descricao"]) ?></li>
        <li><strong>Estado:</strong> <?= esc($ticket["estado"]) ?></li>
        <li><strong>Prioridade:</strong> <?= esc($ticket["prioridade"]) ?></li>
        <li><strong>Última Atualização:</strong> <?= esc($ticket["updated_at"]) ?></li>
    </ul>
    <p>Acompanhe o estado do seu ticket na secção "Meus Tickets" da aplicação.</p>
    <p>Obrigado,</p>
    <p>A sua Equipa de Suporte</p>
</body>
</html>
