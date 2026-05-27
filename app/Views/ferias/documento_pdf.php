<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Licença para Férias</title>
    <style>
        @page {
            margin: 12mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            line-height: 1.2;
            color: #000;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
        }
        .header-logo img {
            max-width: 160px;
            height: auto;
            margin: 0 auto 5px auto;
        }
        .header-escola {
            font-size: 7pt;
            margin: 3px 0;
            line-height: 1.2;
            color: #333;
        }
        .titulo {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0 8px 0;
        }
        .campo {
            margin: 4px 0;
            font-size: 8.5pt;
            display: flex;
            align-items: flex-start;
        }
        .campo-label {
            display: inline-block;
            width: 90px;
            font-weight: normal;
            flex-shrink: 0;
        }
        .campo-valor {
            display: inline-block;
            border: 1px solid #000;
            min-width: 450px;
            padding: 2px 6px;
            flex-grow: 1;
            min-height: 14px;
        }
        .campo-pequeno {
            border: 1px solid #000;
            display: inline-block;
            min-width: 35px;
            text-align: center;
            padding: 2px 6px;
            margin-left: 8px;
            font-weight: bold;
        }
        .calculo-linha {
            margin: 3px 0 3px 30px;
            font-size: 8pt;
        }
        .calculo-label {
            display: inline-block;
            width: 470px;
        }
        .assinatura-coordenador {
            text-align: right;
            margin: 10px 50px 12px 0;
            font-size: 8pt;
        }
        .assinatura-linha {
            border-bottom: 1px solid #000;
            width: 250px;
            margin-left: auto;
            padding-top: 2px;
            margin-bottom: 3px;
        }
        .secao-preenchimento {
            border: 1px solid #000;
            padding: 10px;
            margin: 12px 0;
            min-height: 100px;
        }
        .secao-titulo {
            font-weight: bold;
            margin-bottom: 8px;
        }
        .campo-texto {
            border: 1px solid #000;
            min-height: 40px;
            margin: 8px 0;
            padding: 5px;
        }
        .despacho {
            border: 1px solid #000;
            padding: 10px;
            min-height: 70px;
            margin: 10px 0;
        }
        .notas-rodape {
            font-size: 7pt;
            margin: 8px 30px;
            line-height: 1.3;
        }
        .rodape-sistema {
            text-align: center;
            font-size: 6.5pt;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid #ccc;
        }
        .data-linha {
            margin: 10px 0;
            font-size: 8pt;
        }
        table.periodos {
            width: 90%;
            margin: 10px auto;
            border-collapse: collapse;
            font-size: 8pt;
        }
        table.periodos th,
        table.periodos td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: left;
        }
        table.periodos th {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        table.periodos td.center {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- CABEÇALHO -->
    <div class="header">
        <div class="header-logo">
            <img src="<?= $logoPath ?? FCPATH . 'ME_logo2024_horizontal_pdf.png' ?>" alt="Ministério da Educação">
        </div>
        <div class="header-escola">
            <strong>171268 - Agrupamento de Escolas João de Barros</strong><br>
            <strong>460050 - Escola Secundária João de Barros</strong>, 2855 - 713 Corroios - NMec 212589180 Tel 212531167 - Cont. 600078422
        </div>
    </div>

    <!-- TÍTULO -->
    <div class="titulo">
        Licença para Férias / Ano <?= $pedido['ano'] + 1 ?>
    </div>
    <?php
    $numRemarc = (int)($pedido['numero_remarcacao'] ?? 0);
    if ($numRemarc === 0):
    ?>
    <div style="text-align: center; font-size: 8pt; font-weight: bold; margin: -4px 0 8px 0; color: #333;">
        1ª Via
    </div>
    <?php else: ?>
    <div style="text-align: center; font-size: 8pt; font-weight: bold; margin: -4px 0 8px 0; color: #333;">
        <?= $numRemarc ?>º Pedido de Remarcação
    </div>
    <?php endif; ?>

    <!-- DADOS DO PROFESSOR -->
    <div class="campo">
        <span class="campo-label">Nome :</span>
        <span class="campo-valor"><?= esc($pedido['nome_professor']) ?></span>
    </div>

    <div class="campo">
        <span class="campo-label">Nº Func. :</span>
        <span class="campo-valor"><?= esc($pedido['cod_funcionario']) ?></span>
    </div>

    <div class="campo">
        <span class="campo-label">Categoria :</span>
        <span class="campo-valor"><?= esc($pedido['categoria']) ?></span>
    </div>

    <div class="campo">
        <span class="campo-label">Escola :</span>
        <span class="campo-valor">Escola Secundária João de Barros, Corroios, Seixal</span>
    </div>

    <!-- CÁLCULO DE DIAS -->
    <div class="calculo-linha" style="margin-top: 8px;">
        <span class="calculo-label">Nº de dias de licença para férias a que teve direito no ano anterior :</span>
        <span class="campo-pequeno"><?= $pedido['dias_ano_anterior'] ?? 0 ?></span>
    </div>

    <div class="calculo-linha">
        <span class="calculo-label">Nº de dias de licença para férias gozadas no ano anterior :</span>
        <span class="campo-pequeno"><?= $pedido['dias_gozados_anterior'] ?? 0 ?></span>
    </div>

    <div class="calculo-linha" style="margin-top: 6px;">
        <span class="calculo-label">Dias de licença para férias a que tem direito (a) :</span>
        <span class="campo-pequeno"><?= $pedido['dias_base'] + $pedido['dias_extra'] ?><?= ($pedido['dias_ajuste'] > 0) ? '¹' : '' ?></span>
    </div>

    <div class="calculo-linha">
        <span class="calculo-label">Faltas Justificadas por participação :</span>
        <span class="campo-pequeno"><?= floor($pedido['faltas_justificadas'] ?? 0) ?></span>
    </div>
    <?php if (!empty($pedido['faltas_justificadas_lista'])): ?>
    <div style="margin-left: 50px; font-size: 7pt; color: #555;">
        <?php foreach ($pedido['faltas_justificadas_lista'] as $falta): ?>
            <?= rtrim(rtrim(number_format((float)$falta['dias_desconto'], 1, ',', ''), '0'), ',') ?>d - <?= date('d/m/Y', strtotime($falta['data_falta'])) ?> 
            (<?= \App\Models\FeriasFaltasDescontoModel::getReferenciaLegal($falta['tipo_falta']) ?>)<br>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="calculo-linha">
        <span class="calculo-label">Faltas Injustificadas :</span>
        <span class="campo-pequeno"><?= floor($pedido['faltas_injustificadas'] ?? 0) ?></span>
    </div>
    <?php if (!empty($pedido['faltas_injustificadas_lista'])): ?>
    <div style="margin-left: 50px; font-size: 7pt; color: #555;">
        <?php foreach ($pedido['faltas_injustificadas_lista'] as $falta): ?>
            <?= rtrim(rtrim(number_format((float)$falta['dias_desconto'], 1, ',', ''), '0'), ',') ?>d - <?= date('d/m/Y', strtotime($falta['data_falta'])) ?><br>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="calculo-linha">
        <span class="calculo-label">Total de faltas a descontar :</span>
        <span class="campo-pequeno"><?= floor(($pedido['faltas_justificadas'] ?? 0) + ($pedido['faltas_injustificadas'] ?? 0)) ?></span>
    </div>

    <div class="calculo-linha" style="margin-top: 6px;">
        <span class="calculo-label">Nº de dias de licença para férias a conceder :</span>
        <span class="campo-pequeno"><?= $pedido['dias_concedidos'] ?? $pedido['total_dias'] ?></span>
    </div>

    <!-- ASSINATURA COORDENADOR -->
    <div class="assinatura-coordenador">
        <div>P/ A Coordenador(a) Técnico(a) / CSAE</div>
        <?php
        $assinaturaPath = WRITEPATH . 'private/Assinatura_Andreia_Ferreira.png';
        if (file_exists($assinaturaPath)):
            $assinaturaBase64 = base64_encode(file_get_contents($assinaturaPath));
        ?>
        <div style="margin-top: 4px;">
            <img src="data:image/png;base64,<?= $assinaturaBase64 ?>" alt="Assinatura" style="max-height: 55px; max-width: 220px;">
        </div>
        <?php endif; ?>
        <div class="assinatura-linha"></div>
    </div>

    <!-- PERÍODOS PRETENDIDOS -->
    <div style="margin: 12px 0 8px 0; font-weight: bold; font-size: 8.5pt;">
        Período de férias pretendido :
    </div>
    
    <table class="periodos">
        <thead>
            <tr>
                <th width="40">#</th>
                <th>Data de Início</th>
                <th>Data de Fim</th>
                <th width="100">Dias Úteis</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $num = 1;
            foreach ($pedido['periodos'] as $periodo): 
            ?>
                <tr>
                    <td class="center"><?= $num++ ?></td>
                    <td class="center"><?= date('d/m/Y', strtotime($periodo['data_inicio'])) ?></td>
                    <td class="center"><?= date('d/m/Y', strtotime($periodo['data_fim'])) ?></td>
                    <td class="center"><strong><?= $periodo['dias_uteis'] ?></strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align: right;">TOTAL:</th>
                <th class="center"><?= $pedido['total_dias'] ?></th>
            </tr>
        </tfoot>
    </table>

    <div style="margin: 8px 0; font-weight: bold; font-size: 8.5pt;">
        Residência durante o período de férias :
    </div>
    <div class="campo-texto">
        <?= !empty($pedido['residencia_ferias']) ? nl2br(esc($pedido['residencia_ferias'])) : '' ?>
    </div>

    <div class="data-linha">
        Data ____ / ____ / ______
    </div>

    <div style="margin-top: 6px; font-size: 8pt;">
        Assinatura do(a) trabalhador(a) : _____________________________________________
    </div>

    <!-- DESPACHO -->
    <div class="despacho">
        <div style="font-weight: bold; margin-bottom: 6px; font-size: 8.5pt;">Despacho :</div>
        <?php if (!empty($pedido['observacoes_secretaria'])): ?>
        <div style="margin-top: 6px; font-size: 8pt;">
            <?= nl2br(esc($pedido['observacoes_secretaria'])) ?>
        </div>
        <?php endif; ?>
        <div style="margin-top: 35px; font-size: 8pt;">
            Data ____ / ____ / ______     ____________________________________________
        </div>
    </div>

    <!-- NOTAS DE RODAPÉ -->
    <div class="notas-rodape">
        <div>(a) Férias vencidas no ano + acumuladas devidamente autorizadas, antes do desconto de faltas.</div>
        <?php if (($pedido['dias_ajuste'] ?? 0) > 0): ?>
        <div>¹1 dia de férias acumuladas de acordo com o art.º 89 do ECD</div>
        <?php endif; ?>
    </div>

    <!-- RODAPÉ SISTEMA -->
    <div class="rodape-sistema">
        <div>Processado por computador pelo software Sistema de Gestão Escolar • Copyright© - Agrupamento de Escolas</div>
        <div style="margin-top: 3px; font-size: 6pt; color: #666;">
            Licenciado para <?= esc($pedido['escola_nome'] ?? 'Escola') ?> | Documento #<?= $pedido['id'] ?> | Gerado em <?= date('d/m/Y H:i') ?>
        </div>
    </div>
</body>
</html>
