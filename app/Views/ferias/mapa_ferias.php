<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">

    <div class="no-print content-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center py-2">
                <h1 class="mb-0"><i class="bi bi-calendar-range"></i> Mapa de F&eacute;rias &mdash; Ver&atilde;o <?= $ano_civil ?></h1>
                <div>
                    <a href="<?= base_url('ferias/relatorios') ?>" class="btn btn-secondary me-1">
                        <i class="bi bi-arrow-left"></i> Relat&oacute;rios
                    </a>
                    <button class="btn btn-outline-secondary me-1" onclick="alterarTituloMapa()" title="Alterar o t&iacute;tulo do relat&oacute;rio">
                        <i class="bi bi-pencil"></i> Alterar T&iacute;tulo
                    </button>
                    <button class="btn btn-primary" onclick="imprimirMapa()">
                        <i class="bi bi-printer"></i> Imprimir / PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid px-2">

            <!-- Filtros: Ano + Grupo -->
            <div class="card card-outline card-secondary mb-3 no-print">
                <div class="card-body py-2">
                    <form method="get" class="row g-2 align-items-center">
                        <div class="col-auto">
                            <label class="form-label mb-0 fw-semibold">Ano Letivo:</label>
                        </div>
                        <div class="col-auto">
                            <select name="ano" class="form-select form-select-sm" onchange="this.form.submit()">
                                <?php
                                $anosLetivos = (new \App\Models\AnoLetivoModel())->orderBy('anoletivo', 'DESC')->findAll();
                                foreach ($anosLetivos as $al):
                                ?>
                                <option value="<?= $al['anoletivo'] ?>" <?= $al['anoletivo'] == ($ano_letivo['anoletivo'] ?? '') ? 'selected' : '' ?>>
                                    <?= $al['anoletivo'] ?>/<?= $al['anoletivo'] + 1 ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-auto ms-3">
                            <label class="form-label mb-0 fw-semibold">Grupo:</label>
                        </div>
                        <div class="col-auto">
                            <select name="grupo" class="form-select form-select-sm" onchange="this.form.submit()">
                                <?php foreach ($grupos_mapa as $val => $label): ?>
                                <option value="<?= $val ?>" <?= $grupo_ativo === $val ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            // Definir sempre (usado na tabela e no modal)
            $titulos_grupo = [
                'geral'            => 'Pessoal Docente',
                'tecnico_superior' => 'Técnicos Superiores',
                'direcao'          => 'Direção',
            ];
            $titulo_grupo   = $titulos_grupo[$grupo_ativo] ?? 'Pessoal Docente';
            $ano_civil      = ($ano_letivo['anoletivo'] ?? date('Y')) + 1;
            $titulo_default = 'MAPA DE FÉRIAS ' . $ano_civil . ' — ' . $titulo_grupo;
            ?>

            <?php if (empty($ferias_organizadas)): ?>
            <div class="alert alert-warning no-print">
                N&atilde;o existem f&eacute;rias aprovadas para o per&iacute;odo de ver&atilde;o do ano letivo
                <?= $ano_letivo['anoletivo'] ?>/<?= $ano_letivo['anoletivo'] + 1 ?>.
            </div>
            <?php else: ?>

            <?php
            $semanasPorMes = [];
            foreach ($semanas as $s) {
                $semanasPorMes[$s['mes']][] = $s;
            }
            $totalCols = 2 + count($semanas);
            ?>

            <div id="mapa-print-area">
            <table class="mapa-table">
                <thead>
                    <?php // $titulo_default já definido acima ?>
                    <tr>
                        <td id="mapa-titulo-cell" colspan="<?= $totalCols ?>" class="cell-titulo">
                            <?= esc($titulo_default) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="cell-inst-esq">
                            MINIST&Eacute;RIO DA EDUCA&Ccedil;&Atilde;O, CI&Ecirc;NCIA E INOVA&Ccedil;&Atilde;O<br>
                            AGRUPAMENTO DE ESCOLAS JO&Atilde;O DE BARROS
                        </td>
                        <td colspan="<?= count($semanas) ?>" class="cell-inst-dir">
                            <table style="width:100%;border:none">
                                <tr>
                                    <td class="lbl">RESPONS&Aacute;VEL:</td>
                                    <td class="campo-assin"></td>
                                    <td class="lbl">HOMOLOGA&Ccedil;&Atilde;O:</td>
                                    <td class="campo-assin"></td>
                                    <td class="lbl">DATA HOMOLOGA&Ccedil;&Atilde;O:</td>
                                    <td class="campo-assin" style="min-width:55px"></td>
                                    <td class="lbl">ASSINATURA:</td>
                                    <td style="min-width:80px; border-bottom:1px solid #000; font-size:6pt;color:#bbb;text-align:right"><?= date('d/m/Y') ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="3" class="th-nome">NOME</th>
                        <th rowspan="3" class="th-dias">DIAS<br>DE<br>F&Eacute;RIAS</th>
                        <?php foreach ($semanasPorMes as $mes => $smeses): ?>
                        <th colspan="<?= count($smeses) ?>" class="th-mes"><?= $smeses[0]['mes_nome'] ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($semanas as $s): ?>
                        <th class="th-snum"><?= $s['numero'] ?></th>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <?php foreach ($semanas as $s): ?>
                        <th class="th-slabel"><?= $s['label'] ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                <?php foreach ($ferias_organizadas as $escola => $professores): ?>
                <tbody>
                    <tr class="tr-escola">
                        <td colspan="<?= $totalCols ?>"><?= esc($escola) ?></td>
                    </tr>
                    <?php foreach ($professores as $prof): ?>
                    <?php
                    $ranges = array_map(fn($p) => ['i' => $p['data_inicio'], 'f' => $p['data_fim']], $prof['periodos']);
                    $alinea = $prof['alinea'] ?? null;
                    ?>
                    <tr class="tr-prof">
                        <td class="td-nome">
                            <?= esc($prof['nome_professor']) ?>
                            <?php if ($alinea): ?><sup class="td-alinea"><?= esc($alinea) ?>)</sup><?php endif; ?>
                        </td>
                        <td class="td-dias"><?= ($alinea && ($prof['total_dias'] ?? 0) == 0) ? '—' : $prof['total_dias'] ?></td>
                        <?php foreach ($semanas as $s):
                            $wS = $s['segunda_str'];
                            $wE = $s['sexta_str'];
                            $emF = false; $dI = null; $dF = null;
                            foreach ($ranges as $r) {
                                if ($r['i'] <= $wE && $r['f'] >= $wS) {
                                    $emF = true;
                                    if ($r['i'] >= $wS) $dI = (int)(new \DateTime($r['i']))->format('j');
                                    if ($r['f'] <= $wE) $dF = (int)(new \DateTime($r['f']))->format('j');
                                    break;
                                }
                            }
                        ?>
                        <?php if (!$emF): ?>
                            <td class="td-s"></td>
                        <?php else: ?>
                            <td class="td-s td-f">
                                <?php if ($dI !== null && $dF !== null): ?>
                                    <span class="si">&lt;<?= $dI ?></span><span class="sf"><?= $dF ?>&gt;</span>
                                <?php elseif ($dI !== null): ?>
                                    <span class="si">&lt;<?= $dI ?></span>
                                <?php elseif ($dF !== null): ?>
                                    <span class="sf"><?= $dF ?>&gt;</span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php endforeach; ?>

                <?php
                // Collect which alineas are actually used
                $usedAlineas = [];
                foreach ($ferias_organizadas as $profs) {
                    foreach ($profs as $p) {
                        if (!empty($p['alinea'])) $usedAlineas[$p['alinea']] = true;
                    }
                }
                ksort($usedAlineas);
                $alineaTextos = [
                    'a' => 'Junta Médica',
                    'b' => 'Mobilidade Especial',
                    'c' => 'Em Mobilidade',
                    'd' => 'Licença s/ vencimento',
                    'e' => 'Licença ao abrigo do artigo 37.º da Lei n.º 7/2009',
                    'f' => 'Licença ao abrigo do Art.º53 da Lei nº90/2019',
                ];
                ?>
                <?php if (!empty($usedAlineas)): ?>
                <tbody>
                    <tr class="tr-legenda">
                        <td colspan="<?= $totalCols ?>" class="td-legenda">
                            <?php foreach ($usedAlineas as $letra => $_): ?>
                                <span class="legenda-item"><sup><?= $letra ?>)</sup> <?= $alineaTextos[$letra] ?></span>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </tbody>
                <?php endif; ?>
            </table>
            </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<style>
#mapa-print-area { overflow-x: auto; font-family: Arial, Helvetica, sans-serif; }

.mapa-table { border-collapse: collapse; width: 100%; font-size: 8pt; }
.mapa-table th, .mapa-table td { border: 1px solid #000; padding: 2px 3px; vertical-align: middle; }

.cell-titulo {
    text-align: center; font-weight: bold; font-size: 11pt;
    padding: 6px; background: #fff;
}
.cell-inst-esq {
    font-size: 7.5pt; font-weight: bold; line-height: 1.6; padding: 4px 8px;
}
.cell-inst-dir { font-size: 7pt; padding: 3px 6px; }
.cell-inst-dir table td { border: none !important; padding: 1px 3px; white-space: nowrap; }
.lbl { font-weight: bold; }
.campo-assin { border-bottom: 1px solid #000 !important; min-width: 60px; }

.th-nome {
    width: 55mm; text-align: center; font-weight: bold; font-size: 7.5pt;
    background: #e8e8e8;
}
.th-dias {
    width: 10mm; text-align: center; font-weight: bold; font-size: 6.5pt;
    line-height: 1.3; background: #e8e8e8;
}
.th-mes {
    text-align: center; font-weight: bold; font-size: 8.5pt;
    background: #c8c8c8; border-bottom: 2px solid #000 !important; padding: 4px 2px;
}
.th-snum  { text-align: center; font-size: 7pt; font-weight: bold; background: #e0e0e0; padding: 2px; width: 12mm; }
.th-slabel{ text-align: center; font-size: 6pt; background: #f0f0f0; padding: 2px; width: 12mm; }

.tr-escola td {
    background: #c8c8c8; font-weight: bold; font-size: 8.5pt;
    padding: 3px 6px;
    border-top: 2px solid #000 !important;
    border-bottom: 2px solid #000 !important;
}
.td-nome { font-size: 7.5pt; padding: 2px 5px; }
.td-dias { text-align: center; font-size: 8pt; font-weight: bold; }
.td-alinea { font-size: 7pt; font-weight: bold; color: #333; }

.tr-legenda td {
    border-top: 2px solid #000 !important;
    background: #f8f8f8;
    padding: 4px 8px;
}
.td-legenda { font-size: 7pt; }
.legenda-item { display: inline-block; margin-right: 14px; white-space: nowrap; }

.td-s  { text-align: center; padding: 1px; width: 12mm; height: 13px; }
.td-f  { background-color: #999 !important; }
.si    { font-size: 7pt; font-weight: bold; margin-right: 1px; }
.sf    { font-size: 7pt; font-weight: bold; margin-left:  1px; }

.tr-prof:nth-child(even) td              { background-color: #f5f5f5; }
.tr-prof:nth-child(even) .td-f           { background-color: #999 !important; }

@media print {
    @page { size: A4 landscape; margin: 8mm; }
}
</style>

<!-- Área de impressão isolada (fora do layout) -->
<div id="janela-impressao" style="display:none"></div>

<script>
function imprimirMapa() {
    var mapaOriginal = document.getElementById('mapa-print-area');
    if (!mapaOriginal) { window.print(); return; }

    // Recolher os estilos da página actual
    var estilos = '';
    Array.from(document.styleSheets).forEach(function(ss) {
        try {
            if (ss.cssRules) {
                Array.from(ss.cssRules).forEach(function(rule) {
                    estilos += rule.cssText + '\n';
                });
            }
        } catch(e) {}
    });

    var win = window.open('', '_blank', 'width=1200,height=800');
    win.document.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mapa de Férias <?= $ano_civil ?></title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 0; }
        ${estilos}
        @page { size: A4 landscape; margin: 8mm; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
        .mapa-table { font-size: 7pt; }
        .td-nome    { font-size: 6.5pt; }
        .th-nome    { width: 50mm; }
        .th-dias    { width: 9mm; }
        .td-s, .th-snum, .th-slabel { width: 11mm; min-width: 11mm; }
        thead { display: table-header-group; }
        .td-f  { background-color: #999 !important; }
        .tr-prof:nth-child(even) .td-f { background-color: #999 !important; }
        .th-mes { background: #c8c8c8 !important; }
        .tr-escola td { background: #c8c8c8 !important; }
        .th-nome, .th-dias, .th-snum, .th-slabel { background: #e8e8e8 !important; }
        .tr-legenda td { background: #f8f8f8 !important; border-top: 2px solid #000 !important; padding: 4px 8px; }
        .td-legenda { font-size: 6.5pt; }
        .legenda-item { display: inline-block; margin-right: 12px; white-space: nowrap; }
    </style>
</head>
<body>
    ${mapaOriginal.outerHTML}
</body>
</html>`);
    win.document.close();
    win.focus();
    win.onload = function() {
        win.print();
        win.close();
    };
    // fallback se onload já disparou
    setTimeout(function() {
        if (!win.closed) {
            win.print();
            win.close();
        }
    }, 800);
}
</script>

<!-- Modal: Alterar Título do Mapa -->
<div class="modal fade" id="modalAlterarTitulo" tabindex="-1" aria-labelledby="modalAlterarTituloLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAlterarTituloLabel">
                    <i class="bi bi-pencil"></i> Alterar T&iacute;tulo do Mapa
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="inputTituloMapa" class="form-label fw-semibold">T&iacute;tulo do relat&oacute;rio:</label>
                    <input type="text" class="form-control form-control-lg" id="inputTituloMapa" maxlength="250"
                           placeholder="Ex: 1&ordf; ADENDA AO MAPA DE F&Eacute;RIAS 2026 &mdash; Pessoal Docente">
                </div>
                <div class="alert alert-info py-2">
                    <small>
                        <strong>Sugest&otilde;es:</strong><br>
                        &bull; <a href="#" class="sugestao-titulo">MAPA DE F&Eacute;RIAS <?= $ano_civil ?> &mdash; <?= esc($titulo_grupo) ?></a><br>
                        &bull; <a href="#" class="sugestao-titulo">1&ordf; ADENDA AO MAPA DE F&Eacute;RIAS <?= $ano_civil ?> &mdash; <?= esc($titulo_grupo) ?></a><br>
                        &bull; <a href="#" class="sugestao-titulo">2&ordf; ADENDA AO MAPA DE F&Eacute;RIAS <?= $ano_civil ?> &mdash; <?= esc($titulo_grupo) ?></a><br>
                        &bull; <a href="#" class="sugestao-titulo">3&ordf; ADENDA AO MAPA DE F&Eacute;RIAS <?= $ano_civil ?> &mdash; <?= esc($titulo_grupo) ?></a>
                    </small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnAplicarTitulo">
                    <i class="bi bi-check-lg"></i> Aplicar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var tituloAtual = <?= json_encode($titulo_default) ?>;

    window.alterarTituloMapa = function() {
        var cell = document.getElementById('mapa-titulo-cell');
        if (cell) {
            tituloAtual = cell.innerText.trim();
        }
        document.getElementById('inputTituloMapa').value = tituloAtual;
        var modal = new bootstrap.Modal(document.getElementById('modalAlterarTitulo'));
        modal.show();
    };

    document.getElementById('btnAplicarTitulo').addEventListener('click', function() {
        var novoTitulo = document.getElementById('inputTituloMapa').value.trim();
        if (!novoTitulo) {
            document.getElementById('inputTituloMapa').classList.add('is-invalid');
            return;
        }
        document.getElementById('inputTituloMapa').classList.remove('is-invalid');
        var cell = document.getElementById('mapa-titulo-cell');
        if (cell) {
            cell.innerText = novoTitulo;
            tituloAtual = novoTitulo;
        }
        bootstrap.Modal.getInstance(document.getElementById('modalAlterarTitulo')).hide();
    });

    // Sugestões clicáveis
    document.querySelectorAll('.sugestao-titulo').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('inputTituloMapa').value = this.innerText.trim();
            document.getElementById('inputTituloMapa').classList.remove('is-invalid');
        });
    });

    // Enter no campo confirma
    document.getElementById('inputTituloMapa').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('btnAplicarTitulo').click();
        }
    });
}());
</script>

<?= $this->endSection() ?>
