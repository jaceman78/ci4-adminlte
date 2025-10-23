<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grupo de Permutas Aprovado</title>
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
            background: #28a745;
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
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .permuta-item {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .permuta-item h3 {
            margin-top: 0;
            color: #28a745;
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
        .badge-success {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✓ Grupo de Permutas Aprovado</h1>
        </div>

        <div class="alert-success">
            <strong>Olá, <?= esc($nomeProfessor) ?>!</strong><br>
            O grupo de permutas com <strong><?= $totalPermutas ?> permuta(s)</strong> foi aprovado pela Direção.
        </div>

        <p><strong>ID do Grupo:</strong> <?= esc($grupoId) ?></p>
        <p><strong>Total de Permutas:</strong> <?= $totalPermutas ?></p>

        <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">

        <h2 style="color: #28a745;">Detalhes das Permutas Aprovadas:</h2>

        <?php foreach ($permutas as $index => $permuta): ?>
            <div class="permuta-item">
                <h3>Permuta #<?= $permuta['id'] ?? 'N/A' ?> <span class="badge badge-success">APROVADA</span></h3>
                
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

        <div class="footer">
            <p>Este é um email automático do Sistema de Gestão Escolar.<br>
            Por favor, não responda a este email.</p>
            <p><small>Agrupamento de Escolas João de Barros</small></p>
        </div>
    </div>
</body>
</html>
