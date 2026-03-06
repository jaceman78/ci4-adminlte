<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #17a2b8; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f8f9fa; padding: 20px; margin: 20px 0; }
        .info-box { background-color: white; padding: 15px; margin: 10px 0; border-left: 4px solid #17a2b8; }
        .button { display: inline-block; padding: 12px 24px; margin: 10px 5px; text-decoration: none; border-radius: 4px; font-weight: bold; }
        .button-success { background-color: #28a745; color: white; }
        .button-danger { background-color: #dc3545; color: white; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Pedido de Permuta de Vigilância</h2>
        </div>
        
        <div class="content">
            <p>Olá <strong><?= esc($permuta['nome_substituto']) ?></strong>,</p>
            
            <p><strong><?= esc($permuta['nome_original']) ?></strong> solicitou que o substitua numa vigilância de exame:</p>
            
            <div class="info-box">
                <p><strong>Prova:</strong> <?= esc($permuta['codigo_prova']) ?> - <?= esc($permuta['nome_prova']) ?></p>
                <p><strong>Fase:</strong> <?= esc($permuta['fase']) ?></p>
                <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($permuta['data_exame'])) ?></p>
                <p><strong>Hora:</strong> <?= date('H:i', strtotime($permuta['hora_exame'])) ?></p>
                <p><strong>Sala:</strong> <?= esc($permuta['codigo_sala']) ?></p>
                <p><strong>Função:</strong> <?= esc($permuta['funcao']) ?></p>
            </div>
            
            <div class="info-box">
                <p><strong>Motivo:</strong></p>
                <p><?= nl2br(esc($permuta['motivo'])) ?></p>
            </div>
            
            <p style="text-align: center; margin-top: 30px;">
                <a href="<?= base_url('dashboard') ?>" class="button button-success">Ver Pedido no Sistema</a>
            </p>
            
            <p style="font-size: 14px; color: #666; margin-top: 20px;">
                <strong>Nota:</strong> Aceda ao sistema para aceitar ou recusar este pedido. 
                Após a sua resposta, o pedido será enviado para validação do secretariado.
            </p>
        </div>
        
        <div class="footer">
            <p>Agrupamento de Escolas João de Barros</p>
            <p>Sistema de Gestão Escolar</p>
        </div>
    </div>
</body>
</html>
