<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Permutas Aprovadas - <?= date('d/m/Y') ?></title>
    <style>
        @page {
            margin: 10mm 10mm 25mm 10mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
            padding-bottom: 20mm;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header-logos {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .logo-left, .logo-right {
            display: table-cell;
            vertical-align: middle;
            width: 25%;
        }
        .logo-center {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            width: 50%;
        }
        .logo-left img {
            height: 45px;
        }
        .logo-right img {
            height: 40px;
            float: right;
        }
        .header h1 {
            font-size: 16pt;
            margin: 5px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-box {
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            padding: 8px 10px;
            margin: 10px 0;
            border-radius: 3px;
            font-size: 8.5pt;
        }
        .info-row {
            margin: 3px 0;
            display: inline-block;
            margin-right: 20px;
        }
        .info-label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8pt;
        }
        table th {
            background-color: #2c3e50;
            color: white;
            padding: 6px 4px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #1a242f;
            font-size: 8pt;
        }
        table td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #f0f0f0;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 7pt;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7pt;
            font-weight: bold;
            border-radius: 3px;
            color: white;
        }
        .badge-success {
            background-color: #28a745;
        }
        .total-box {
            margin-top: 10px;
            text-align: right;
            font-weight: bold;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-logos">
            <div class="logo-left">
                <?php 
                $logoEsjb = FCPATH . 'esjb_logo_pdf.png';
                if (file_exists($logoEsjb)):
                ?>
                    <img src="<?= $logoEsjb ?>" alt="Logo ESJB" style="height: 45px;">
                <?php else: ?>
                    <p style="font-weight: bold; margin: 0; font-size: 9pt;">Agrupamento de Escolas<br>João de Barros</p>
                <?php endif; ?>
            </div>
            <div class="logo-center">
                <h1><?php 
                    // Usar nome da escola se filtrado, senão mostrar genérico
                    if (!empty($permutas) && !empty($permutas[0]['nome_escola'])) {
                        echo esc($permutas[0]['nome_escola']);
                    } else {
                        echo 'Agrupamento de Escolas João de Barros';
                    }
                ?></h1>
                <p style="font-size: 11pt; margin: 5px 0 0 0; color: #2c3e50; font-weight: bold;">PERMUTAS APROVADAS</p>
            </div>
            <div class="logo-right">
                <?php 
                $logoRp = FCPATH . 'RP_Edu_pdf.png';
                if (file_exists($logoRp)):
                ?>
                    <img src="<?= $logoRp ?>" alt="República Portuguesa" style="height: 40px; float: right;">
                <?php else: ?>
                    <p style="font-weight: bold; margin: 0; font-size: 8pt; text-align: right;">República Portuguesa<br>Educação</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Período:</span>
            <?php if ($filtroDataInicio && $filtroDataFim): ?>
                <?= date('d/m/Y', strtotime($filtroDataInicio)) ?> a <?= date('d/m/Y', strtotime($filtroDataFim)) ?>
            <?php elseif ($filtroDataInicio): ?>
                A partir de <?= date('d/m/Y', strtotime($filtroDataInicio)) ?>
            <?php elseif ($filtroDataFim): ?>
                Até <?= date('d/m/Y', strtotime($filtroDataFim)) ?>
            <?php else: ?>
                Sem filtro de data
            <?php endif; ?>
        </div>
        <?php if ($filtroEscola): ?>
            <div class="info-row">
                <span class="info-label">Escola:</span>
                <?= esc($permutas[0]['nome_escola'] ?? 'Selecionada') ?>
            </div>
        <?php endif; ?>
        <?php if ($filtroPavilhao): ?>
            <div class="info-row">
                <span class="info-label">Pavilhão:</span>
                <?= esc($filtroPavilhao) ?>xxx
            </div>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Data Aula</th>
                <th style="width: 20%;">Prof. Autor</th>
                <th style="width: 20%;">Prof. Substituto</th>
                <th style="width: 12%;">Disciplina</th>
                <th style="width: 8%;">Turma</th>
                <th style="width: 12%;">Sala</th>
                <th style="width: 18%;">Horário Reposição</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($permutas as $permuta): ?>
            <tr>
                <td class="text-center">
                    <?= date('d/m/Y', strtotime($permuta['data_aula_permutada'])) ?>
                </td>
                <td><?= esc($permuta['professor_autor_nome']) ?></td>
                <td><?= esc($permuta['professor_substituto_nome']) ?></td>
                <td><?= esc($permuta['disciplina_abrev'] ?? $permuta['disciplina_id']) ?></td>
                <td class="text-center"><?= esc($permuta['codigo_turma']) ?></td>
                <td class="text-center">
                    <?php if (!empty($permuta['codigo_sala'])): ?>
                        <?= esc($permuta['codigo_sala']) ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (!empty($permuta['hora_inicio']) && !empty($permuta['hora_fim'])): ?>
                        <?= substr($permuta['hora_inicio'], 0, 5) ?> - <?= substr($permuta['hora_fim'], 0, 5) ?>
                    <?php else: ?>
                        Mesmo horário
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total-box">
        Total de Permutas: <?= $totalPermutas ?>
    </div>

    <div style="margin-top: 40px; text-align: center;">
        <p style="margin-bottom: 50px; font-size: 9pt;"><strong>O Diretor,</strong></p>
        <div style="border-top: 1px solid #000; width: 250px; margin: 0 auto;"></div>
        <p style="margin-top: 5px; font-size: 9pt;">António Carvalho</p>
    </div>

    <div class="footer">
        Documento gerado automaticamente pelo Sistema de Gestão Escolar - ESJB<br>
        <?= date('d/m/Y H:i:s') ?>
    </div>
</body>
</html>
