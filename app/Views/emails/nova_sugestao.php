<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Sugestão Recebida</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
        <h1 style="margin: 0; font-size: 28px;">📬 Nova Sugestão Recebida</h1>
        <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">Sistema de Gestão Escolar</p>
    </div>
    
    <div style="background: #f8f9fa; padding: 30px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 10px 10px;">
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #667eea;">
            <h2 style="margin-top: 0; color: #667eea; font-size: 20px;">
                Sugestão #<?= esc($sugestao['id']) ?>
            </h2>
            <p style="margin: 5px 0; font-size: 14px; color: #666;">
                Recebida em: <strong><?= date('d/m/Y H:i', strtotime($sugestao['created_at'])) ?></strong>
            </p>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="margin-top: 0; color: #333; font-size: 16px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                👤 Informações do Utilizador
            </h3>
            <table style="width: 100%; font-size: 14px;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 120px;"><strong>Nome:</strong></td>
                    <td style="padding: 8px 0;"><?= esc($usuario['name'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;"><strong>Email:</strong></td>
                    <td style="padding: 8px 0;"><?= esc($usuario['email'] ?? 'N/A') ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;"><strong>NIF:</strong></td>
                    <td style="padding: 8px 0;"><?= esc($sugestao['user_nif'] ?? 'N/A') ?></td>
                </tr>
            </table>
        </div>

        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="margin-top: 0; color: #333; font-size: 16px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                📋 Detalhes da Sugestão
            </h3>
            <table style="width: 100%; font-size: 14px; margin-bottom: 15px;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 120px;"><strong>Categoria:</strong></td>
                    <td style="padding: 8px 0;">
                        <span style="background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                            <?= esc($sugestao['categoria']) ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;"><strong>Prioridade:</strong></td>
                    <td style="padding: 8px 0;">
                        <?php 
                        $prioridades = [
                            'baixa' => ['cor' => '#6c757d', 'texto' => 'BAIXA'],
                            'media' => ['cor' => '#ffc107', 'texto' => 'MÉDIA'],
                            'alta' => ['cor' => '#dc3545', 'texto' => 'ALTA']
                        ];
                        $p = $prioridades[$sugestao['prioridade']] ?? ['cor' => '#6c757d', 'texto' => strtoupper($sugestao['prioridade'])];
                        ?>
                        <span style="background: <?= $p['cor'] ?>; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                            <?= $p['texto'] ?>
                        </span>
                    </td>
                </tr>
            </table>
            
            <div style="margin-top: 15px;">
                <strong style="color: #666; font-size: 14px;">Título:</strong>
                <p style="margin: 8px 0; padding: 12px; background: #f8f9fa; border-radius: 6px; font-size: 15px; font-weight: 500;">
                    <?= esc($sugestao['titulo']) ?>
                </p>
            </div>
            
            <div style="margin-top: 15px;">
                <strong style="color: #666; font-size: 14px;">Descrição:</strong>
                <p style="margin: 8px 0; padding: 15px; background: #f8f9fa; border-radius: 6px; font-size: 14px; line-height: 1.8; white-space: pre-wrap;">
<?= esc($sugestao['descricao']) ?>
                </p>
            </div>
        </div>

        <div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; margin-top: 20px;">
            <p style="margin: 0; font-size: 13px; color: #856404;">
                <strong>⚠️ Atenção:</strong> Por favor, aceda ao sistema para visualizar todos os detalhes e responder a esta sugestão.
            </p>
        </div>

        <div style="text-align: center; margin-top: 25px;">
            <a href="<?= base_url('sugestoes') ?>" 
               style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 14px; box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);">
                🔗 Aceder ao Sistema
            </a>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; text-align: center; font-size: 12px; color: #6c757d;">
            <p style="margin: 5px 0;">
                <strong>Sistema de Gestão Escolar</strong><br>
                Agrupamento de Escolas João de Barros
            </p>
            <p style="margin: 5px 0;">
                Este é um email automático, por favor não responda.
            </p>
        </div>
    </div>
</body>
</html>
