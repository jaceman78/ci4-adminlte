<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo de Permutas Rejeitado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            background: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
            margin: -20px -20px 20px -20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .alert-danger {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .motivo-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .motivo-box h3 {
            margin-top: 0;
            color: #856404;
        }
        .permuta-item {
            background: #f8f9fa;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .permuta-item h3 {
            margin-top: 0;
            color: #dc3545;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            min-width: 180px;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #777;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✗ Grupo de Permutas Rejeitado</h1>
        </div>

        <div class="alert-danger">
            <strong>Olá, <?= esc($nomeProfessor) ?>!</strong><br>
            Infelizmente, o grupo de permutas com <strong><?= $totalPermutas ?> permuta(s)</strong> foi rejeitado pela Direção.
        </div>

        <p><strong>ID do Grupo:</strong> <?= esc($grupoId) ?></p>
        <p><strong>Total de Permutas:</strong> <?= $totalPermutas ?></p>

        <?php if (!empty($motivo)): ?>
            <div class="motivo-box">
                <h3>📋 Motivo da Rejeição:</h3>
                <p style="margin: 0;"><?= nl2br(esc($motivo)) ?></p>
            </div>
        <?php endif; ?>

        <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">

        <h2 style="color: #dc3545;">Detalhes das Permutas Rejeitadas:</h2>

        <?php foreach ($permutas as $index => $permuta): ?>
            <div class="permuta-item">
                <h3>Permuta #<?= $permuta['id'] ?? 'N/A' ?> <span class="badge badge-danger">REJEITADA</span></h3>
                
                <div class="info-row">
                    <div class="info-label">Professor Autor:</div>
                    <div class="info-value"><?= esc($permuta['professor_autor_nome'] ?? 'N/A') ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Professor Substituto:</div>
                    <div class="info-value"><?= esc($permuta['professor_substituto_nome'] ?? 'N/A') ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Turma:</div>
                    <div class="info-value"><?= esc($permuta['codigo_turma'] ?? 'N/A') ?></div>
                </div>

                <div class="info-row">
                    <div class="info-label">Disciplina:</div>
                    <div class="info-value">
                        <?php if (isset($permuta['disciplina_nome']) && $permuta['disciplina_nome']): ?>
                            <?= esc($permuta['disciplina_nome']) ?>
                        <?php elseif (isset($permuta['disciplina_abrev']) && $permuta['disciplina_abrev']): ?>
                            <?= esc($permuta['disciplina_abrev']) ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Aula Original:</div>
                    <div class="info-value">
                        <?php if (isset($permuta['data_aula_original']) && $permuta['data_aula_original']): ?>
                            <?= date('d/m/Y', strtotime($permuta['data_aula_original'])) ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                        (<?= esc($permuta['hora_inicio'] ?? '') ?> - <?= esc($permuta['hora_fim'] ?? '') ?>)
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-label">Data de Reposição:</div>
                    <div class="info-value">
                        <?php if (isset($permuta['data_aula_permutada']) && $permuta['data_aula_permutada']): ?>
                            <?= date('d/m/Y', strtotime($permuta['data_aula_permutada'])) ?>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($permuta['observacoes'])): ?>
                    <div class="info-row">
                        <div class="info-label">Observações:</div>
                        <div class="info-value"><?= esc($permuta['observacoes']) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <div style="background-color: #e7f3ff; border-left: 4px solid #2196F3; padding: 15px; margin-top: 20px; border-radius: 4px;">
            <p style="margin: 0;"><strong>💡 Sugestão:</strong> Caso pretenda submeter um novo pedido de permuta, por favor contacte a Direção para esclarecer os motivos da rejeição.</p>
        </div>

        <div class="footer">
            <p>Este é um email automático do Sistema de Gestão Escolar.<br>
            Por favor, não responda a este email.</p>
            <p><small>Agrupamento de Escolas João de Barros</small></p>
        </div>
    </div>
</body>
</html>
