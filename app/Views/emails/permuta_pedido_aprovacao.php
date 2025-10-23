<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #e3f2fd; color: #1565c0; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; border: 2px solid #007bff; }
        .content { background-color: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; }
        .info-box { background-color: white; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; }
        .info-row { margin: 8px 0; }
        .label { font-weight: bold; color: #495057; }
        .value { color: #212529; }
        .button { display: inline-block; padding: 12px 24px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 15px 0; }
        .footer { text-align: center; padding: 15px; color: #6c757d; font-size: 12px; }
        .alert { background-color: #fff3cd; border: 1px solid #ffc107; padding: 10px; margin: 10px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Novo Pedido de Permuta</h1>
            <p>Permuta #<?= $permutaId ?></p>
        </div>
        
        <div class="content">
            <div class="alert">
                <strong>⏰ Atenção:</strong> Este pedido requer aprovação da direção.
            </div>

            <div class="info-box">
                <h3>👤 Professor Solicitante</h3>
                <div class="info-row">
                    <span class="label">Nome:</span> 
                    <span class="value"><?= isset($autor['name']) ? esc($autor['name']) : 'N/A' ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span> 
                    <span class="value"><?= isset($autor['email']) ? esc($autor['email']) : 'N/A' ?></span>
                </div>
            </div>

            <div class="info-box">
                <h3>🔄 Professor Substituto</h3>
                <div class="info-row">
                    <span class="label">Nome:</span> 
                    <span class="value"><?= isset($substituto['name']) ? esc($substituto['name']) : 'N/A' ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Email:</span> 
                    <span class="value"><?= isset($substituto['email']) ? esc($substituto['email']) : 'N/A' ?></span>
                </div>
            </div>

            <?php if (!empty($permuta)): ?>
            <div class="info-box">
                <h3>📚 Detalhes da Aula</h3>
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
                    <span class="label">Data Original:</span> 
                    <span class="value"><?= !empty($permuta['data_aula_original']) ? date('d/m/Y', strtotime($permuta['data_aula_original'])) : 'N/A' ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Data Reposição:</span> 
                    <span class="value"><?= !empty($permuta['data_aula_permutada']) ? date('d/m/Y', strtotime($permuta['data_aula_permutada'])) : 'N/A' ?></span>
                </div>
                <?php if (!empty($permuta['observacoes'])): ?>
                <div class="info-row">
                    <span class="label">Observações:</span> 
                    <span class="value"><?= nl2br(esc($permuta['observacoes'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div style="text-align: center;">
                <a href="<?= base_url('permutas/ver/' . $permutaId) ?>" class="button">
                    Ver Detalhes e Aprovar/Rejeitar
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
