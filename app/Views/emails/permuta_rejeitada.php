<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8d7da; color: #721c24; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; border: 2px solid #dc3545; }
        .content { background-color: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; }
        .info-box { background-color: white; padding: 15px; margin: 10px 0; border-left: 4px solid #dc3545; }
        .info-row { margin: 8px 0; }
        .label { font-weight: bold; color: #495057; }
        .value { color: #212529; }
        .button { display: inline-block; padding: 12px 24px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
        .footer { text-align: center; padding: 15px; color: #6c757d; font-size: 12px; }
        .alert-danger { background-color: #f8d7da; border: 1px solid #dc3545; padding: 15px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Permuta Rejeitada</h1>
            <p>Permuta #<?= $permutaId ?></p>
        </div>
        
        <div class="content">
            <p>Informamos que a sua permuta foi rejeitada pela direção.</p>

            <div class="alert-danger">
                <strong>Motivo da Rejeição:</strong><br>
                <?= nl2br(esc($permuta['motivo_rejeicao'])) ?>
            </div>

            <div class="info-box">
                <h3>📚 Detalhes da Permuta</h3>
                <div class="info-row">
                    <span class="label">Disciplina:</span> 
                    <span class="value">
                        <?php if (!empty($permuta['disciplina_nome'])): ?>
                            <?= esc($permuta['disciplina_abrev']) ?> - <?= esc($permuta['disciplina_nome']) ?>
                        <?php else: ?>
                            <?= esc($permuta['disciplina_id'] ?? 'N/A') ?>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Turma:</span> 
                    <span class="value">
                        <?php if (!empty($permuta['turma_nome'])): ?>
                            <?= esc($permuta['turma_nome']) ?> (<?= esc($permuta['ano'] ?? '') ?>º ano)
                        <?php else: ?>
                            <?= esc($permuta['codigo_turma'] ?? 'N/A') ?>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="label">Data da Aula:</span> 
                    <span class="value"><?= !empty($permuta['data_aula_original']) ? date('d/m/Y', strtotime($permuta['data_aula_original'])) : 'N/A' ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Professor Substituto:</span> 
                    <span class="value"><?= esc($permuta['professor_substituto_nome']) ?></span>
                </div>
            </div>

            <p>Se tiver dúvidas, por favor contacte a direção.</p>

            <div style="text-align: center;">
                <a href="<?= base_url('permutas/ver/' . $permutaId) ?>" class="button">
                    Ver Detalhes Completos
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p>Sistema de Gestão Escolar - Agrupamento de Escolas João de Barros</p>
            <p>Este é um email automático, por favor não responda.</p>
        </div>
    </div>
</body>
</html>
