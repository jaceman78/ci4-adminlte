<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório de Faltas - Sessão <?= esc($sessao['codigo_prova']) ?></title>
    <style>
        @page {
            margin: 15mm 10mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .logo-container {
            margin-bottom: 10px;
        }
        .logo-left {
            float: left;
            width: 30%;
        }
        .logo-right {
            float: right;
            width: 30%;
        }
        .logo-left img, .logo-right img {
            max-height: 50px;
        }
        .header-title {
            clear: both;
            padding-top: 10px;
        }
        .header h1 {
            font-size: 14pt;
            margin: 5px 0;
        }
        .header h2 {
            font-size: 12pt;
            margin: 5px 0;
            color: #666;
        }
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .info-box strong {
            color: #000;
        }
        .date-highlight {
            background-color: #fff3cd;
            border: 2px solid #856404;
            padding: 8px 15px;
            margin: 10px 0;
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
            color: #dc3545;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table th {
            background-color: #343a40;
            color: white;
            padding: 8px;
            text-align: left;
            border: 1px solid #000;
            font-weight: bold;
        }
        table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .falta {
            background-color: #f8d7da !important;
        }
        .falta-justificada {
            background-color: #fff3cd !important;
        }
        .statistics {
            margin: 20px 0;
            padding: 10px;
            background-color: #e9ecef;
            border-left: 4px solid #007bff;
        }
        .statistics h3 {
            margin: 0 0 10px 0;
            font-size: 11pt;
        }
        .stat-item {
            display: inline-block;
            margin-right: 20px;
            padding: 5px 10px;
            background-color: white;
            border-radius: 3px;
        }
        .signature {
            margin-top: 40px;
            page-break-inside: avoid;
            text-align: center;
        }
        .signature-line {
            margin-top: 40px;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 5px;
            width: 50%;
            margin-left: auto;
            margin-right: auto;
        }
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #666;
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
        }
        .no-faltas {
            text-align: center;
            padding: 30px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 12pt;
        }
    </style>
</head>
<body>
    <!-- Header com Logos -->
    <div class="header">
        <div class="logo-container">
            <?php
            // Usar caminho absoluto para as imagens
            $logoLeft = ($fcpath ?? FCPATH) . 'esjb_logo_pdf.png';
            $logoRight = ($fcpath ?? FCPATH) . 'RP_Edu_pdf.png';
            ?>
            <?php if (file_exists($logoLeft)): ?>
            <div class="logo-left">
                <img src="<?= $logoLeft ?>" alt="ESJB Logo">
            </div>
            <?php endif; ?>
            <?php if (file_exists($logoRight)): ?>
            <div class="logo-right">
                <img src="<?= $logoRight ?>" alt="RP Logo">
            </div>
            <?php endif; ?>
        </div>
        <div class="header-title">
            <h2>Relatório de Faltas - Vigilância de Exames</h2>
        </div>
    </div>

    <!-- Data em Destaque -->
    <div class="date-highlight">
        <?= date('d/m/Y', strtotime($sessao['data_exame'])) ?> às <?= date('H:i', strtotime($sessao['hora_exame'])) ?>
    </div>

    <!-- Informações da Sessão -->
    <div class="info-box">
        <strong>Código da Prova:</strong> <?= esc($sessao['codigo_prova']) ?><br>
        <strong>Nome da Prova:</strong> <?= esc($sessao['nome_prova']) ?><br>
        <strong>Tipo:</strong> <?= esc($sessao['tipo_prova']) ?><br>
        <strong>Fase:</strong> <?= esc($sessao['fase']) ?>
        <?php if (!empty($sessao['duracao'])): ?>
        <br><strong>Duração:</strong> <?= esc($sessao['duracao']) ?> minutos
        <?php endif; ?>
    </div>

    <!-- Estatísticas -->
    <div class="statistics">
        <h3>Estatísticas de Presença</h3>
        <div class="stat-item"><strong>Total:</strong> <?= $estatisticas['total'] ?></div>
        <div class="stat-item"><strong>Presentes:</strong> <?= $estatisticas['presentes'] ?></div>
        <div class="stat-item"><strong>Faltas:</strong> <?= $estatisticas['faltas'] ?></div>
        <div class="stat-item"><strong>Faltas Justificadas:</strong> <?= $estatisticas['faltas_justificadas'] ?></div>
        <div class="stat-item"><strong>Pendentes:</strong> <?= $estatisticas['pendentes'] ?></div>
    </div>

    <?php if (empty($faltas)): ?>
    <!-- Sem Faltas -->
    <div class="no-faltas">
        <strong>✓ Não há faltas registadas para esta sessão de exame.</strong>
    </div>
    <?php else: ?>
    <!-- Lista de Faltas -->
    <h3>Lista de Faltas</h3>
    <table>
        <thead>
            <tr>
                <th width="50%">Nome do Professor</th>
                <th width="25%">Função</th>
                <th width="25%">Tipo de Falta</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($faltas as $falta): ?>
            <tr class="<?= $falta['presenca'] == 'Falta' ? 'falta' : 'falta-justificada' ?>">
                <td><?= esc($falta['professor_nome']) ?></td>
                <td><?= esc($falta['funcao']) ?><?= !empty($falta['codigo_sala']) ? ' - Sala ' . esc($falta['codigo_sala']) : '' ?></td>
                <td><strong><?= esc($falta['presenca']) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Assinatura do Diretor -->
    <div class="signature">
        <?php
        $meses = ['', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 
                  'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
        $mes = $meses[(int)date('n')];
        ?>
        <p>Corroios, <?= date('d') ?> de <?= $mes ?> de <?= date('Y') ?></p>
        <p style="margin-top: 30px;">O Diretor</p>
        <div class="signature-line">
            (António de Carvalho)
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Agrupamento de Escolas João de Barros | Rua Dr. Manuel de Arriaga, 2855-098 Corroios, Portugal</p>
        <p>Tel.: 212 559 800 / 212 559 809 | secretaria@aejoaodebarros.pt | https://www.aejoaodebarros.pt/</p>
    </div>
</body>
</html>
