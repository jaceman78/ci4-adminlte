<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização da sua Sugestão</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">

    <?php
    $estadoConfig = [
        'implementada' => [
            'cor'    => '#28a745',
            'texto'  => 'IMPLEMENTADA',
            'icone'  => '✅',
            'titulo' => 'Sugestão Implementada!',
        ],
        'rejeitada' => [
            'cor'    => '#dc3545',
            'texto'  => 'REJEITADA',
            'icone'  => '❌',
            'titulo' => 'Sugestão Rejeitada',
        ],
        'em_analise' => [
            'cor'    => '#17a2b8',
            'texto'  => 'EM ANÁLISE',
            'icone'  => '🔍',
            'titulo' => 'Sugestão em Análise',
        ],
    ];
    $cfg = $estadoConfig[$estado] ?? [
        'cor'    => '#6c757d',
        'texto'  => strtoupper($estado),
        'icone'  => '📋',
        'titulo' => 'Atualização da Sugestão',
    ];
    ?>

    <!-- Header -->
    <div style="background: <?= $cfg['cor'] ?>; color: white; padding: 30px; border-radius: 10px 10px 0 0; text-align: center;">
        <h1 style="margin: 0; font-size: 26px;"><?= $cfg['icone'] ?> <?= $cfg['titulo'] ?></h1>
        <p style="margin: 10px 0 0 0; font-size: 14px; opacity: 0.9;">Sistema de Gestão Escolar</p>
    </div>

    <!-- Body -->
    <div style="background: #f8f9fa; padding: 30px; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 10px 10px;">

        <!-- Saudação -->
        <p style="font-size: 15px; margin-top: 0;">
            Olá, <strong><?= esc($autor['name'] ?? 'Utilizador') ?></strong>,
        </p>
        <p style="font-size: 14px; color: #555;">
            A sua sugestão foi atualizada. Consulte os detalhes abaixo.
        </p>

        <!-- Referência da Sugestão -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid <?= $cfg['cor'] ?>;">
            <h2 style="margin-top: 0; color: <?= $cfg['cor'] ?>; font-size: 18px;">
                Sugestão #<?= esc($sugestao['id']) ?>
            </h2>
            <p style="margin: 5px 0; font-size: 13px; color: #666;">
                Submetida em: <strong><?= date('d/m/Y H:i', strtotime($sugestao['created_at'])) ?></strong>
            </p>
        </div>

        <!-- Detalhes da sugestão -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="margin-top: 0; color: #333; font-size: 16px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                📋 Detalhes da Sugestão
            </h3>
            <table style="width: 100%; font-size: 14px; margin-bottom: 15px;">
                <tr>
                    <td style="padding: 8px 0; color: #666; width: 110px;"><strong>Categoria:</strong></td>
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
                            'alta'  => ['cor' => '#dc3545', 'texto' => 'ALTA'],
                        ];
                        $p = $prioridades[$sugestao['prioridade']] ?? ['cor' => '#6c757d', 'texto' => strtoupper($sugestao['prioridade'])];
                        ?>
                        <span style="background: <?= $p['cor'] ?>; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                            <?= $p['texto'] ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #666;"><strong>Novo Estado:</strong></td>
                    <td style="padding: 8px 0;">
                        <span style="background: <?= $cfg['cor'] ?>; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                            <?= $cfg['texto'] ?>
                        </span>
                    </td>
                </tr>
            </table>

            <div>
                <strong style="color: #666; font-size: 14px;">Título:</strong>
                <p style="margin: 8px 0; padding: 12px; background: #f8f9fa; border-radius: 6px; font-size: 15px; font-weight: 500;">
                    <?= esc($sugestao['titulo']) ?>
                </p>
            </div>
        </div>

        <!-- Resposta do admin -->
        <div style="background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid <?= $cfg['cor'] ?>;">
            <h3 style="margin-top: 0; color: #333; font-size: 16px; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                💬 Resposta da Administração
            </h3>
            <p style="margin: 0; font-size: 14px; line-height: 1.8; white-space: pre-wrap; color: #444;">
<?= esc($resposta) ?>
            </p>
            <?php if (!empty($adminNome)): ?>
            <p style="margin: 15px 0 0 0; font-size: 12px; color: #999; text-align: right;">
                — <?= esc($adminNome) ?>, <?= date('d/m/Y \à\s H:i') ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- CTA -->
        <div style="text-align: center; margin-top: 25px;">
            <a href="<?= base_url('sugestoes') ?>"
               style="display: inline-block; background: <?= $cfg['cor'] ?>; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold; font-size: 14px;">
                🔗 Aceder ao Sistema
            </a>
        </div>

        <!-- Footer -->
        <p style="margin-top: 30px; font-size: 12px; color: #999; text-align: center; border-top: 1px solid #dee2e6; padding-top: 15px;">
            Este é um email automático. Por favor, não responda diretamente a esta mensagem.<br>
            AE João de Barros — Sistema de Gestão Escolar
        </p>
    </div>

</body>
</html>
