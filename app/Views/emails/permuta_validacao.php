<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333}.container{max-width:600px;margin:0 auto;padding:20px}.header{background-color:<?= $aprovado ? '#28a745' : '#dc3545' ?>;color:white;padding:20px;text-align:center}.content{background-color:#f8f9fa;padding:20px;margin:20px 0}.info-box{background-color:white;padding:15px;margin:10px 0;border-left:4px solid <?= $aprovado ? '#28a745' : '#dc3545' ?>}.button{display:inline-block;padding:12px 24px;margin:10px 5px;text-decoration:none;border-radius:4px;font-weight:bold;background-color:#17a2b8;color:white}.footer{text-align:center;padding:20px;font-size:12px;color:#666}</style></head>
<body>
    <div class="container">
        <div class="header">
            <h2>Permuta <?= $aprovado ? 'Aprovada' : 'Rejeitada' ?></h2>
        </div>
        <div class="content">
            <p>Olá <strong><?= esc($destinatario) ?></strong>,</p>
            <p>A permuta de vigilância foi <?= $aprovado ? '<strong style="color:#28a745">aprovada</strong> pelo secretariado' : '<strong style="color:#dc3545">rejeitada</strong> pelo secretariado' ?>:</p>
            <div class="info-box">
                <p><strong>Professor Original:</strong> <?= esc($permuta['nome_original']) ?></p>
                <p><strong>Professor Substituto:</strong> <?= esc($permuta['nome_substituto']) ?></p>
                <p><strong>Prova:</strong> <?= esc($permuta['codigo_prova']) ?> - <?= esc($permuta['nome_prova']) ?></p>
                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($permuta['data_exame'])) ?> às <?= date('H:i', strtotime($permuta['hora_exame'])) ?></p>
                <p><strong>Sala:</strong> <?= esc($permuta['codigo_sala']) ?></p>
                <p><strong>Validado por:</strong> <?= esc($permuta['nome_validador']) ?></p>
                <?php if (!empty($permuta['observacoes_validacao'])): ?>
                <p><strong>Observações:</strong> <?= esc($permuta['observacoes_validacao']) ?></p>
                <?php endif; ?>
            </div>
            <?php if ($aprovado): ?>
            <p style="color:#28a745;font-weight:bold">✓ A convocatória foi atualizada automaticamente. <?= esc($permuta['nome_substituto']) ?> é agora o responsável pela vigilância.</p>
            <?php else: ?>
            <p style="color:#dc3545">✗ A vigilância original mantém-se.</p>
            <?php endif; ?>
            <p style="text-align:center;margin-top:30px;"><a href="<?= base_url('dashboard') ?>" class="button">Ver Convocatórias</a></p>
        </div>
        <div class="footer"><p>Agrupamento de Escolas João de Barros</p></div>
    </div>
</body>
</html>
