<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Alteração de Férias</title>
    <style>
        @page {
            margin: 14mm 15mm 12mm 15mm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ── CABEÇALHO ─────────────────────────────── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .header-logo-rp {
            width: 48%;
            vertical-align: middle;
            text-align: left;
        }
        .header-logo-escola {
            width: 48%;
            vertical-align: middle;
            text-align: right;
        }
        .header-logo-rp img {
            height: 34px;
            width: auto;
        }
        .header-logo-escola img {
            height: 40px;
            width: auto;
        }
        .header-sep {
            border-bottom: 1.5px solid #000;
            margin: 6px 0 8px 0;
        }

        /* ── TÍTULO ─────────────────────────────────── */
        .titulo {
            text-align: center;
            font-size: 13pt;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 6px 0 4px 0;
        }
        .info-box {
            border: 1px solid #555;
            padding: 5px 10px;
            font-size: 7.5pt;
            color: #333;
            margin: 6px 0 8px 0;
            background: #f9f9f9;
        }

        /* ── DUAS COLUNAS: INFO SERVIÇOS | DESPACHO ─── */
        .two-col-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .two-col-table td {
            vertical-align: top;
            width: 50%;
        }
        .two-col-table td:first-child {
            padding-right: 6px;
        }
        .two-col-table td:last-child {
            padding-left: 6px;
        }
        .box-secao {
            border: 1px solid #000;
            padding: 8px;
            box-sizing: border-box;
        }
        .box-secao-titulo {
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            border-bottom: 1px solid #aaa;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }
        .box-secao-corpo {
            font-size: 8pt;
            line-height: 1.5;
        }
        .linha-assinatura {
            border-bottom: 1px solid #000;
            margin-top: 18px;
            margin-bottom: 2px;
        }
        .linha-label {
            font-size: 7.5pt;
            text-align: center;
            color: #444;
        }

        /* ── CAMPOS PRINCIPAIS ──────────────────────── */
        .campo-linha {
            margin: 5px 0;
            font-size: 9pt;
        }
        .campo-label-bold {
            font-weight: bold;
        }
        .campo-valor {
            display: inline;
            font-size: 9pt;
            border: none;
            text-decoration: none;
        }
        .check-label {
            font-size: 9pt;
        }
        .check-box {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            vertical-align: middle;
            text-align: center;
            font-size: 8pt;
            line-height: 11px;
            margin-right: 2px;
        }
        .check-box-checked {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            vertical-align: middle;
            text-align: center;
            font-size: 8pt;
            line-height: 11px;
            margin-right: 2px;
            font-weight: bold;
        }

        /* ── TABELAS DE PERÍODOS ────────────────────── */
        .periodos-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 8px 0;
            font-size: 8.5pt;
        }
        .periodos-table th {
            background: #e0e0e0;
            border: 1px solid #000;
            padding: 3px 6px;
            text-align: center;
            font-weight: bold;
        }
        .periodos-table td {
            border: 1px solid #000;
            padding: 3px 6px;
            text-align: center;
            min-height: 16px;
            height: 16px;
        }
        .secao-label {
            font-size: 9pt;
            margin: 8px 0 3px 0;
        }

        /* ── MOTIVO / DOMICÍLIO ─────────────────────── */
        .motivo-box {
            min-height: 18px;
            padding: 2px 3px;
            display: inline-block;
            min-width: 380px;
            font-size: 9pt;
        }
        .domicilio-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
            font-size: 8.5pt;
        }
        .domicilio-table td {
            padding: 3px 6px 3px 0;
            vertical-align: bottom;
        }
        .domicilio-line {
            display: inline-block;
            min-width: 180px;
            padding: 1px 3px;
        }
        .domicilio-line-sm {
            display: inline-block;
            min-width: 120px;
            padding: 1px 3px;
        }

        /* ── ASSINATURA FUNCIONÁRIO ─────────────────── */
        .assinatura-secao {
            margin-top: 20px;
            text-align: center;
        }
        .assinatura-data {
            font-size: 8.5pt;
            margin-bottom: 22px;
        }
        .assinatura-linha {
            border-bottom: 1px solid #000;
            width: 220px;
            display: inline-block;
            margin-bottom: 2px;
        }
        .assinatura-nome {
            font-size: 7.5pt;
            text-align: center;
            width: 220px;
            display: inline-block;
        }

        /* ── RODAPÉ ─────────────────────────────────── */
        .rodape {
            text-align: center;
            font-size: 6.5pt;
            color: #888;
            margin-top: 10px;
            border-top: 1px solid #ccc;
            padding-top: 4px;
        }
    </style>
</head>
<body>

<?php
/* ── Auxiliares de categoria / vínculo ─────────────────────── */
$tipoCategoria = $categoriaType  ?? 'Docente';  // Docente | AO | AT | TS
$tipoVinculo   = $vinculoType    ?? 'Contratado'; // Quadro | Destacado | Contratado

function checkBox(bool $checked): string {
    return $checked
        ? '<span class="check-box-checked">X</span>'
        : '<span class="check-box">&nbsp;</span>';
}

/* ── Datas formatadas ──────────────────────────────────────── */
$dataPedido = !empty($pedido['submetido_em'])
    ? date('d/m/Y', strtotime($pedido['submetido_em']))
    : date('d/m/Y');

$mesNomes = ['','janeiro','fevereiro','março','abril','maio','junho',
             'julho','agosto','setembro','outubro','novembro','dezembro'];
$mesAtual = (int)date('m');
$diaAtual = date('d');
$anoAtual = date('Y');
$dataExtenso = "Corroios, {$diaAtual} de {$mesNomes[$mesAtual]} de {$anoAtual}";

/* ── Períodos do pedido (pré-preenchidos) ──────────────────── */
$periodos = $pedido['periodos'] ?? [];

/* ── Períodos propostos na remarcação ──────────────────────── */
$periodosPropostos = [];
if (!empty($pedido['datas_remarcacao_propostas'])) {
    $periodosPropostos = json_decode($pedido['datas_remarcacao_propostas'], true) ?? [];
}

/* ── Dados domicílio / motivo ──────────────────────────────── */
$motivo     = esc($pedido['motivo_remarcacao']       ?? '');
$morada     = esc($pedido['observacoes_professor']   ?? '');
$telefone   = esc($professor['telefone']             ?? '');
$nome       = esc($pedido['nome_professor']        ?? ($professor['name'] ?? ''));
$categoria  = esc($professor['categoria']          ?? '');
?>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- CABEÇALHO                                                   -->
<!-- ═══════════════════════════════════════════════════════════ -->
<table class="header-table">
    <tr>
        <td class="header-logo-rp">
            <img src="<?= $logoRP ?>" alt="República Portuguesa / Ministério da Educação">
        </td>
        <td class="header-logo-escola">
            <img src="<?= $logoEscola ?>" alt="Agrupamento de Escolas João de Barros">
        </td>
    </tr>
</table>
<div class="header-sep"></div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- TÍTULO                                                      -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="titulo">ALTERAÇÃO DE FÉRIAS</div>

<div class="info-box">
    <strong>Nota:</strong> Este pedido deve ser formulado com antecedência mínima de 10 dias úteis. Em caso de urgência devidamente justificada, poderá ser dispensada a antecedência.
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- INFORMAÇÃO DOS SERVIÇOS ADMINISTRATIVOS  |  DESPACHO       -->
<!-- ═══════════════════════════════════════════════════════════ -->
<table class="two-col-table">
    <tr>
        <td>
            <div class="box-secao">
                <div class="box-secao-titulo">Informação dos Serviços Administrativos</div>
                <div class="box-secao-corpo">
                    De acordo com o legalmente autorizado.<br>
                    À consideração superior.
                    <div style="margin-top: 14px;">
                        <div class="linha-assinatura"></div>
                        <div class="linha-label">O Responsável</div>
                        <div style="margin-top:5px; font-size:8pt;">Data: ______ / ______ / __________</div>
                    </div>
                </div>
            </div>
        </td>
        <td>
            <div class="box-secao">
                <div class="box-secao-titulo">Despacho</div>
                <div class="box-secao-corpo">
                    <?= checkBox(false) ?> <span class="check-label">Autorizado</span>
                    &nbsp;&nbsp;&nbsp;
                    <?= checkBox(false) ?> <span class="check-label">Não autorizado</span>
                    <div style="margin-top: 14px;">
                        <div class="linha-assinatura"></div>
                        <div class="linha-label">O Diretor</div>
                        <div style="margin-top:5px; font-size:8pt;">Data: ______ / ______ / __________</div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</table>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- DADOS DO FUNCIONÁRIO                                        -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="campo-linha">
    <span class="campo-label-bold">Nome:</span>
    <span class="campo-valor"><?= $nome ?></span>
</div>

<div class="campo-linha" style="margin-top:6px;">
    <span class="campo-label-bold">Categoria:</span>
    <?= $categoria ?: '—' ?>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- PERÍODOS ANTERIORES (pré-preenchidos)                       -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="secao-label">
    Solicito que as férias anteriormente marcadas para o(s) período(s) de:
</div>
<table class="periodos-table">
    <thead>
        <tr>
            <th style="width:40%">De</th>
            <th style="width:40%">A</th>
            <th style="width:20%">Nº de dias</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($periodos)): ?>
            <?php foreach ($periodos as $p): ?>
            <tr>
                <td><?= !empty($p['data_inicio']) ? date('d/m/Y', strtotime($p['data_inicio'])) : '' ?></td>
                <td><?= !empty($p['data_fim'])    ? date('d/m/Y', strtotime($p['data_fim']))    : '' ?></td>
                <td><?= $p['dias_uteis'] ?? '' ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <?php endif; ?>
        <?php
        // Garantir mínimo 3 linhas
        $linhasExistentes = max(count($periodos), 0);
        for ($i = $linhasExistentes; $i < 3; $i++):
        ?>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <?php endfor; ?>
    </tbody>
</table>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- NOVOS PERÍODOS (pré-preenchidos com datas propostas)       -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="secao-label">
    Seja(m) alterado(s) para: <em style="font-size:7.5pt;font-weight:normal;">(datas propostas — sujeitas a aprovação)</em>
</div>
<table class="periodos-table">
    <thead>
        <tr>
            <th style="width:40%">De</th>
            <th style="width:40%">A</th>
            <th style="width:20%">Nº de dias</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($periodosPropostos)): ?>
            <?php foreach ($periodosPropostos as $pp): ?>
            <tr>
                <td><?= !empty($pp['data_inicio']) ? date('d/m/Y', strtotime($pp['data_inicio'])) : '&nbsp;' ?></td>
                <td><?= !empty($pp['data_fim'])    ? date('d/m/Y', strtotime($pp['data_fim']))    : '&nbsp;' ?></td>
                <td><?= (!empty($pp['data_inicio']) && !empty($pp['data_fim'])) ? calcular_dias_uteis($pp['data_inicio'], $pp['data_fim']) : '&nbsp;' ?></td>
            </tr>
            <?php endforeach; ?>
            <?php for ($j = count($periodosPropostos); $j < 3; $j++): ?>
                <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <?php endfor; ?>
        <?php else: ?>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- MOTIVO                                                      -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="campo-linha" style="margin-top:4px;">
    <span class="campo-label-bold">Por motivo de:</span>
    <span class="campo-valor"><?= $motivo ?></span>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- DOMICÍLIO                                                   -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="campo-linha" style="margin-top:8px;">
    <span class="campo-label-bold">Domicílio no(s) período(s) de ausência(s):</span>
</div>
<div class="campo-linha">
    <span class="campo-label-bold">Morada:</span>
    <span class="campo-valor" style="white-space: pre-wrap;"><?= nl2br($morada) ?></span>
</div>
<div class="campo-linha" style="margin-top:4px;">
    <span class="campo-label-bold">Telefone/Telemo&oacute;vel:</span>
    <span class="campo-valor"><?= $telefone ?></span>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- ASSINATURA DO FUNCIONÁRIO                                   -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="assinatura-secao">
    <div class="assinatura-data"><?= $dataExtenso ?></div>
    <table style="width: 100%; border-collapse: collapse; margin: 0;">
        <tr>
            <td style="text-align: center; padding: 0;">
                <table style="width: 220px; border-collapse: collapse; margin: 0 auto;">
                    <tr>
                        <td style="border-bottom: 1px solid #000; height: 22px; width: 220px; padding: 0;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-size: 7.5pt; color: #444; padding-top: 3px;">O(A) Funcionário(a)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- RODAPÉ                                                      -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div class="rodape">
    Documento gerado automaticamente pelo sistema de gestão de férias — Agrupamento de Escolas João de Barros
    &nbsp;|&nbsp; Pedido #<?= $pedido['id'] ?> &nbsp;|&nbsp; <?= date('d/m/Y H:i') ?>
</div>

</body>
</html>
