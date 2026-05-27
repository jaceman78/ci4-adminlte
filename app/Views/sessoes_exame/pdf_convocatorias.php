<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Convocatórias - <?= esc($sessao['codigo_prova']) ?></title>
    <style>
        @page {
            margin: 10mm 15mm 8mm 15mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.35;
            color: #000;
            orphans: 4;
            widows: 4;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header-logos {
            display: table;
            width: 100%;
            margin-bottom: 6px;
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
            padding: 8px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .info-row {
            margin: 3px 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .section-title {
            background-color: #2c3e50;
            color: white;
            padding: 5px 8px;
            margin: 12px 0 6px 0;
            font-size: 11pt;
            font-weight: bold;
            border-radius: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10pt;
            page-break-after: auto;
            page-break-inside: auto;
        }
        table.last-table {
            margin-bottom: 8px;
        }
        thead {
            display: table-header-group;
        }
        tbody {
            display: table-row-group;
        }
        table th {
            background-color: #34495e;
            color: white;
            padding: 6px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #2c3e50;
        }
        table td {
            padding: 5px 6px;
            border: 1px solid #ddd;
        }
        table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .assinatura-footer {
            page-break-inside: avoid !important;
            page-break-before: auto;
            margin-top: 12px;
            min-height: 100px;
        }
        .assinatura {
            text-align: center;
            margin-bottom: 6px;
        }
        .footer {
            padding-top: 6px;
            border-top: 1px solid #ccc;
            font-size: 7pt;
            color: #666;
            line-height: 1.15;
            text-align: center;
        }
        .importante {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 6px;
            margin: 10px 0;
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
                    'Apoio TIC' => 'Equipa de Apoio TIC',
                    'Estrutura de Apoio' => 'Estrutura de Apoio',
                    'Verificação de Materiais' => 'Verificação de Materiais'
                ];
                if (isset($subtitulosEspeciais[$sessao['tipo_prova']])):
                ?>
                    <p style="font-size: 12pt; margin: 5px 0 0 0; color: #555;"><?= esc($subtitulosEspeciais[$sessao['tipo_prova']]) ?></p>
                <?php endif; ?>
                <?php if (!empty($escolaNomePdf)): ?>
                    <p style="font-size: 9pt; margin: 4px 0 0 0; color: #d9534f; font-weight: bold; white-space: nowrap;"><?= esc($escolaNomePdf) ?></p>
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
        <?php if (!in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC', 'Estrutura de Apoio', 'Verificação de Materiais'])): ?>
        <div class="info-row">
            <span class="info-label">Prova:</span>
            <span>
                <?= esc($sessao['codigo_prova']) ?> - <?= esc($sessao['nome_prova']) ?>
                <?php if (!empty($provasRelacionadas)): ?>
                    <?php foreach ($provasRelacionadas as $provaRel): ?>
                        / <?= esc($provaRel['codigo_prova']) ?> - 
                        <?php 
                        // Abreviar nome das provas PLNM
                        $nomeAbrev = str_replace('Português Língua Não Materna', 'PLNM', $provaRel['nome_prova']);
                        echo esc($nomeAbrev);
                        ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </span>
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
            <span><strong><?= date('H', strtotime($sessao['hora_exame'])) ?>h <?= date('i', strtotime($sessao['hora_exame'])) ?>min</strong></span>
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
        <?php
        // Todos os docentes devem comparecer 45 minutos antes do início
        $minutosAntecedencia = 45;
        // 'prova': Prova Ensaio, ModA, 4º ano, 6º ano; 'exame': restantes
        $ehProva = ($sessao['fase'] === 'Prova Ensaio')
                || in_array($sessao['tipo_prova'] ?? '', ['MODa', 'ModA'])
                || in_array($sessao['ano_escolaridade'] ?? 0, [4, 6]);
        $textoEvento = $ehProva ? 'prova' : 'exame';
        
        // Local de comparecimento:
        // - Estrutura de Apoio: Para 4º ano
        // - Coordenação da Escola: Para 6º ano
        // - Secretariado de Exames: Para outros anos
        
        // Verificar se é equipa de apoio ou suplentes
        $ehEquipaEspecial = in_array($sessao['tipo_prova'], ['Verificacao Calculadoras', 'Apoio TIC', 'Estrutura de Apoio', 'Suplentes', 'Verificação de Materiais']);

        // Para SUP-MANHA e TIC-APOIO em Fase única, usar o local específico da Escola Básica de Corroios
        $usaLocalEscolaCorroiosFaseUnica = in_array($sessao['codigo_prova'] ?? '', ['SUP-MANHA', 'TIC-APOIO'])
            && (($sessao['fase'] ?? '') === 'Fase única');

        // Para equipas especiais do 4º ano, usar texto/local específico de Estrutura de Apoio
        $usaTexto4AnoEspecial = in_array($sessao['codigo_prova'] ?? '', ['SUP-MANHA', 'SUP-TARDE', 'TIC-APOIO', 'EST-APOIO'])
            && isset($temProva4AnoNoDia) && $temProva4AnoNoDia;
        
        if ($usaTexto4AnoEspecial) {
            $localComparecimento = 'Estrutura de Apoio';
        } elseif ($usaLocalEscolaCorroiosFaseUnica) {
            $localComparecimento = 'Escola Básica de Corroios, na Sala de Apoio ao Secretariado de Exames,';
        } elseif ($sessao['ano_escolaridade'] == 4) {
            $localComparecimento = 'Estrutura de Apoio';
        } elseif ($sessao['tipo_prova'] === 'MODa' || $sessao['tipo_prova'] === 'ModA') {
            $localComparecimento = 'Escola Básica de Corroios, na Sala de Apoio ao Secretariado de Exames,';
        } elseif ($ehEquipaEspecial && isset($temProva4AnoNoDia) && $temProva4AnoNoDia) {
            $localComparecimento = 'Estrutura de Apoio';
        } elseif ($sessao['ano_escolaridade'] == 6) {
            // Prova é do 6º ano
            $localComparecimento = 'Coordenação da Escola';
        } elseif ($ehEquipaEspecial && isset($temProva6AnoNoDia) && $temProva6AnoNoDia) {
            // Equipa de apoio/suplentes em dia com prova do 6º ano
            $localComparecimento = 'Coordenação da Escola';
        } else {
            // Outros casos
            $localComparecimento = 'Secretariado de Exames';
        }
        ?>
        <?php if ($sessao['ano_escolaridade'] == 4 || $usaTexto4AnoEspecial): ?>
        <strong>⚠ IMPORTANTE:</strong> Todos os docentes convocados devem comparecer na escola, junto à Estrutura de Apoio, <span class="destaque">45 minutos antes</span> do início da prova, para receberem as informações necessárias.
        <?php elseif ($usaLocalEscolaCorroiosFaseUnica): ?>
        <strong>⚠ IMPORTANTE:</strong> Todos os docentes convocados devem comparecer na Escola Básica de Corroios, na Sala de Apoio ao Secretariado de Exames, <span class="destaque">45 minutos antes</span> do início da prova, para receberem as instruções necessárias.
        <?php else: ?>
        <strong>⚠ IMPORTANTE:</strong> Todos os docentes convocados devem comparecer na <?= $localComparecimento ?> <span class="destaque"><?= $minutosAntecedencia ?> minutos antes</span> do início da <?= $textoEvento ?>, para receberem as informações necessárias.
        <?php endif; ?>
    </div>

    <?php if (!empty($vigilantes)): ?>
    <div class="section-title">VIGILANTES</div>
    <table>
        <thead>
            <tr>
                <th width="50%">Nome</th>
                <th width="10%">Sala</th>
                <th width="40%">Tomei conhecimento</th>
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
    <?php
    // Título dinâmico baseado na função dos convocados
    $titulosOutros = [
        'Apoio TIC'              => 'EQUIPA DE APOIO TIC',
        'Estrutura de Apoio'     => 'ESTRUTURA DE APOIO',
        'Verificar Calculadoras' => 'VERIFICAÇÃO DE CALCULADORAS',
        'Verificar Materiais'    => 'VERIFICAÇÃO DE MATERIAIS',
        'Júri'                   => 'JÚRI',
    ];
    $primeiraFuncao  = $outros[0]['funcao'] ?? '';
    $tituloSecao     = $titulosOutros[$primeiraFuncao] ?? strtoupper($primeiraFuncao) ?: 'OUTROS CONVOCADOS';
    ?>
    <div class="section-title"><?= esc($tituloSecao) ?></div>
    <table>
        <thead>
            <tr>
                <th width="70%">Nome</th>
                <th width="30%">Tomei conhecimento</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($outros as $o): ?>
            <tr>
                <td><?= esc($o['professor_nome']) ?></td>
                <td>&nbsp;</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <div class="assinatura-footer">
        <div class="assinatura">
            <?php
            $meses = ['', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 
                      'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
            $mes = $meses[(int)date('n')];
            ?>
            <p style="margin: 0 0 10px 0;">Corroios, <?= date('d') ?> de <?= $mes ?> de <?= date('Y') ?></p>
            <p style="margin: 0 0 3px 0;"><strong>O Diretor</strong></p>
            <p style="margin: 25px 0 1px 0;">______________________________</p>
            <p style="margin: 0;">( António de Carvalho )</p>
        </div>

        <div class="footer">
            <p style="margin: 0;">Agrupamento de Escolas João de Barros | Rua Dr. Manuel de Arriaga, 2855-098 Corroios, Portugal | Tel.: 212 559 800 / 212 559 809 | secretariadoexamesaejb@aejoaodebarros.pt | https://www.aejoaodebarros.pt/</p>
        </div>
    </div><?php // Fim assinatura-footer ?>
</body>
</html>
