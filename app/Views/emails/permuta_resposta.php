<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333}.container{max-width:600px;margin:0 auto;padding:20px}.header{background-color:<?= $aceite ? '#28a745' : '#dc3545' ?>;color:white;padding:20px;text-align:center}.content{background-color:#f8f9fa;padding:20px;margin:20px 0}.info-box{background-color:white;padding:15px;margin:10px 0;border-left:4px solid <?= $aceite ? '#28a745' : '#dc3545' ?>}.button{display:inline-block;padding:12px 24px;margin:10px 5px;text-decoration:none;border-radius:4px;font-weight:bold;background-color:#17a2b8;color:white}.footer{text-align:center;padding:20px;font-size:12px;color:#666}</style></head>
<body>
    <div class="container">
        <div class="header">
            <h2>Permuta <?= $aceite ? 'Aceite' : 'Recusada' ?></h2>
        </div>
        <div class="content">
            <p>Olá <strong><?= esc($permuta['nome_original']) ?></strong>,</p>
            <p><strong><?= esc($permuta['nome_substituto']) ?></strong> <?= $aceite ? '<strong style="color:#28a745">aceitou</strong>' : '<strong style="color:#dc3545">recusou</strong>' ?> o seu pedido de permuta:</p>
            <div class="info-box">
                <p><strong>Prova:</strong> <?= esc($permuta['codigo_prova']) ?> - <?= esc($permuta['nome_prova']) ?></p>
                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($permuta['data_exame'])) ?> às <?= date('H:i', strtotime($permuta['hora_exame'])) ?></p>
                <p><strong>Sala:</strong> <?= esc($permuta['codigo_sala']) ?></p>
            </div>
            <?php if ($aceite): ?>
            <p style="color:#28a745;font-weight:bold">✓ O pedido será agora analisado pelo secretariado para validação final.</p>
            <?php else: ?>
            <p style="color:#dc3545">✗ Pode criar um novo pedido de permuta com outro professor se necessário.</p>
            <?php endif; ?>
            <p style="text-align:center;margin-top:30px;"><a href="<?= base_url('dashboard') ?>" class="button">Ver no Sistema</a></p>
        </div>
        <div class="footer"><p>Agrupamento de Escolas João de Barros</p></div>
    </div>
</body>
</html>
