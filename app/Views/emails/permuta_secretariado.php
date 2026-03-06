<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333}.container{max-width:600px;margin:0 auto;padding:20px}.header{background-color:#ffc107;color:#333;padding:20px;text-align:center}.content{background-color:#f8f9fa;padding:20px;margin:20px 0}.info-box{background-color:white;padding:15px;margin:10px 0;border-left:4px solid #ffc107}.button{display:inline-block;padding:12px 24px;margin:10px 5px;text-decoration:none;border-radius:4px;font-weight:bold;background-color:#ffc107;color:#333}.footer{text-align:center;padding:20px;font-size:12px;color:#666}</style></head>
<body>
    <div class="container">
        <div class="header">
            <h2>⚠ Permuta Pendente Validação</h2>
        </div>
        <div class="content">
            <p>Uma permuta de vigilância foi aceite e aguarda validação do secretariado:</p>
            <div class="info-box">
                <p><strong>Professor Original:</strong> <?= esc($permuta['nome_original']) ?></p>
                <p><strong>Professor Substituto:</strong> <?= esc($permuta['nome_substituto']) ?></p>
                <p><strong>Prova:</strong> <?= esc($permuta['codigo_prova']) ?> - <?= esc($permuta['nome_prova']) ?></p>
                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($permuta['data_exame'])) ?> às <?= date('H:i', strtotime($permuta['hora_exame'])) ?></p>
                <p><strong>Sala:</strong> <?= esc($permuta['codigo_sala']) ?></p>
                <p><strong>Motivo:</strong> <?= esc($permuta['motivo']) ?></p>
            </div>
            <p style="text-align:center;margin-top:30px;"><a href="<?= base_url('permutas-vigilancia/pendentes-validacao') ?>" class="button">Validar Permuta</a></p>
        </div>
        <div class="footer"><p>Agrupamento de Escolas João de Barros</p></div>
    </div>
</body>
</html>
