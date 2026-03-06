<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Convocatórias - <?= esc($sessao['codigo_prova']) ?></title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header-logos {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .logo-left, .logo-right {
            display: table-cell;
            vertical-align: middle;
            width: 30%;
        }
        .logo-center {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            width: 40%;
        }
        .logo-left img {
            height: 60px;
        }
        .logo-right img {
            height: 55px;
            float: right;
        }
        .header h1 {
            font-size: 18pt;
            margin: 10px 0 5px 0;
            font-weight: bold;
            text-transform: uppercase;
        }
        .info-box {
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            padding: 12px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .info-row {
            margin: 5px 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .section-title {
            background-color: #2c3e50;
            color: white;
            padding: 8px 12px;
            margin: 20px 0 10px 0;
            font-size: 13pt;
            font-weight: bold;
            border-radius: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10pt;
        }
        table th {
            background-color: #34495e;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2c3e50;
        }
        table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-size: 9pt;
            color: #666;
        }
        .importante {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 15px 0;
            font-size: 10pt;
        }
        .destaque {
            font-weight: bold;
            color: #d9534f;
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
                    <img src="<?= $logoEsjb ?>" alt="Logo ESJB" style="height: 50px;">
                <?php else: ?>
                    <p style="font-weight: bold; margin: 0; font-size: 10pt;">Agrupamento de Escolas<br>João de Barros</p>
                <?php endif; ?>
            </div>
            <div class="logo-center">
                <h1>Convocatória de Vigilância</h1>
                <?php 
                // Adicionar subtítulo para sessões especiais
                $subtitulosEspeciais = [
                    'Suplentes' => 'Professores Suplentes',
                    'Verificacao Calculadoras' => 'Verificação de Calculadoras',
                    'Apoio TIC' => 'Equipa de Apoio TIC'
                ];
                if (isset($subtitulosEspeciais[$sessao['tipo_prova']])):
                ?>
                    <p style="font-size: 12pt; margin: 5px 0 0 0; color: #555;"><?= esc($subtitulosEspeciais[$sessao['tipo_prova']]) ?></p>
                <?php endif; ?>
            </div>
            <div class="logo-right">
                <?php 
                $logoRp = FCPATH . 'RP_Edu_pdf.png';
                if (file_exists($logoRp)):
                ?>
                    <img src="<?= $logoRp ?>" alt="República Portuguesa" style="height: 45px; float: right;">
                <?php else: ?>
                    <p style="font-weight: bold; margin: 0; font-size: 8pt; text-align: right;">República Portuguesa<br>Educação, Ciência e Inovação</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="info-box">
        <?php if (!in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC'])): ?>
        <h2 style="margin: 0 0 10px 0; font-size: 14pt; color: #2c3e50;">Informações do Exame</h2>
        <div class="info-row">
            <span class="info-label">Prova:</span>
            <span><?= esc($sessao['codigo_prova']) ?> - <?= esc($sessao['nome_prova']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Tipo de Prova:</span>
            <span><?= esc($sessao['tipo_prova']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Fase:</span>
            <span><strong><?= esc($sessao['fase']) ?></strong></span>
        </div>
        <?php else: ?>
        <h2 style="margin: 0 0 10px 0; font-size: 14pt; color: #2c3e50;">Informações da Sessão</h2>
        <?php endif; ?>
        <div class="info-row">
            <span class="info-label">Data:</span>
            <span style="font-size: 16pt; font-weight: bold; color: #d9534f; background-color: #fff3cd; padding: 4px 10px; border-radius: 3px;"><?= date('d/m/Y', strtotime($sessao['data_exame'])) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Hora de Início:</span>
            <span><strong><?= date('H:i', strtotime($sessao['hora_exame'])) ?>h</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Duração:</span>
            <span><?= $sessao['duracao_minutos'] ?> minutos</span>
        </div>
        <?php if ($sessao['tolerancia_minutos'] > 0): ?>
        <div class="info-row">
            <span class="info-label">Tolerância:</span>
            <span><?= $sessao['tolerancia_minutos'] ?> minutos</span>
        </div>
        <?php endif; ?>
    </div>

    <div class="importante">
        <strong>⚠ IMPORTANTE:</strong> Todos os docentes convocados devem comparecer com <span class="destaque">30 minutos de antecedência</span> ao horário de início do exame para receber as instruções necessárias.
    </div>

    <?php if (!empty($vigilantes)): ?>
    <div class="section-title">VIGILANTES</div>
    <table>
        <thead>
            <tr>
                <th width="50%">Nome</th>
                <th width="25%">Sala</th>
                <th width="25%">Tomei conhecimento</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vigilantes as $v): ?>
            <tr>
                <td><?= esc($v['professor_nome']) ?></td>
                <td><?= !empty($v['codigo_sala']) ? esc($v['codigo_sala']) : '-' ?></td>
                <td>&nbsp;</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if (!empty($suplentes)): ?>
    <div class="section-title">SUPLENTES</div>
    <table>
        <thead>
            <tr>
                <th width="70%">Nome</th>
                <th width="30%">Tomei conhecimento</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($suplentes as $s): ?>
            <tr>
                <td><?= esc($s['professor_nome']) ?></td>
                <td>&nbsp;</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if (!empty($coadjuvantes)): ?>
    <div class="section-title">COADJUVANTES</div>
    <table>
        <thead>
            <tr>
                <th width="70%">Nome</th>
                <th width="30%">Tomei conhecimento</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($coadjuvantes as $c): ?>
            <tr>
                <td><?= esc($c['professor_nome']) ?></td>
                <td>&nbsp;</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <?php if (!empty($outros)): ?>
    <div class="section-title">OUTROS CONVOCADOS</div>
    <table>
        <thead>
            <tr>
                <th width="45%">Nome</th>
                <th width="30%">Função</th>
                <th width="25%">Tomei conhecimento</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($outros as $o): ?>
            <tr>
                <td><?= esc($o['professor_nome']) ?></td>
                <td><?= esc($o['funcao']) ?></td>
                <td>&nbsp;</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 50px; margin-bottom: 30px;">
        <?php
        $meses = ['', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 
                  'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
        $mes = $meses[(int)date('n')];
        ?>
        <p>Corroios, <?= date('d') ?> de <?= $mes ?> de <?= date('Y') ?></p>
        <p style="margin-top: 30px;"><strong>O Diretor</strong></p>
        <p style="margin-top: 50px;">______________________________</p>
        <p>( António de Carvalho )</p>
    </div>

    <div class="footer">
        <p>Agrupamento de Escolas João de Barros | Rua Dr. Manuel de Arriaga, 2855-098 Corroios, Portugal</p>
        <p>Tel.: 212 559 800 / 212 559 809 | secretaria@aejoaodebarros.pt | https://www.aejoaodebarros.pt/</p>
    </div>
</body>
</html>
