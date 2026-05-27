<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="bi bi-graph-up-arrow"></i> Relatórios de Férias - <?= $ano_letivo['anoletivo'] + 1 ?></h1>
                </div>
                <div class="col-sm-6">
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <!-- Card Mapa de Férias -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-2"><i class="bi bi-calendar-range"></i> Mapa de Férias</h5>
                                    <p class="mb-0 text-muted">
                                        Visualize todos os períodos de férias aprovados num calendário visual, 
                                        organizados por escola e professor. Ideal para identificar períodos de maior ausência.
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="<?= base_url('ferias/mapa-ferias') ?>" class="btn btn-primary btn-lg">
                                        <i class="bi bi-calendar-range"></i> Ver Mapa de Férias
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Relatório de Absentismo -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card card-danger card-outline">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-2"><i class="bi bi-clipboard2-pulse"></i> Relatório de Absentismo</h5>
                                    <p class="mb-0 text-muted">
                                        Analise as faltas que descontam em férias por professor, com heat map das datas
                                        com maior incidência ao longo do ano letivo.
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <a href="<?= base_url('ferias/relatorio-absentismo') ?>" class="btn btn-danger btn-lg">
                                        <i class="bi bi-clipboard2-pulse"></i> Ver Absentismo
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relatório de Faltas (3 tipos) -->
            <?php
                $tipoLabels = [
                    'artigo_102'      => 'Art. 102.º ECD',
                    'art_135_n4_ltfp' => 'Art. 135.º nº4 LTFP',
                    'artigo_89_ecd'   => 'Art. 89.º ECD',
                    'injustificada'   => 'Injustificada',
                ];
                $tipoColors = [
                    'artigo_102'      => 'secondary',
                    'art_135_n4_ltfp' => 'secondary',
                    'artigo_89_ecd'   => 'secondary',
                    'injustificada'   => 'danger',
                ];
                $anoAtualLabel    = $ano_letivo['anoletivo'] . '/' . ($ano_letivo['anoletivo'] + 1);
                $anoAnteriorLabel = $ano_anterior
                    ? ($ano_anterior['anoletivo'] . '/' . ($ano_anterior['anoletivo'] + 1))
                    : (($ano_letivo['anoletivo'] - 1) . '/' . $ano_letivo['anoletivo']);
                $anoProximoLabel  = ($ano_letivo['anoletivo'] + 1) . '/' . ($ano_letivo['anoletivo'] + 2);
                // Labels com intervalo de datas para os separadores
                $anoAnteriorInicio = $ano_anterior ? $ano_anterior['anoletivo'] : ($ano_letivo['anoletivo'] - 1);
                $tabAnteriorLabel  = '01/09/' . $anoAnteriorInicio . ' a 31/08/' . ($anoAnteriorInicio + 1);
                $tabAtualLabel     = '01/09/' . $ano_letivo['anoletivo'] . ' a 31/08/' . ($ano_letivo['anoletivo'] + 1);
                $tabProximoLabel   = '01/09/' . ($ano_letivo['anoletivo'] + 1) . ' a 31/08/' . ($ano_letivo['anoletivo'] + 2);
            ?>
            <div class="card mb-3">
                <div class="card-header" style="cursor:pointer" data-bs-toggle="collapse" data-bs-target="#faltasCardCollapse" aria-expanded="false" aria-controls="faltasCardCollapse">
                    <h3 class="card-title"><i class="bi bi-file-earmark-text"></i> Relatório de Faltas</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" tabindex="-1">
                            <i class="bi bi-chevron-down"></i>
                        </button>
                    </div>
                </div>
                <div class="collapse" id="faltasCardCollapse">
                <div class="card-body">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" id="faltasTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab-anterior-btn" data-bs-toggle="tab"
                                data-bs-target="#tab-anterior" type="button" role="tab">
                                <i class="bi bi-calendar-range"></i>
                                <?= $tabAnteriorLabel ?>
                                <span class="badge bg-warning text-dark ms-1"><?= count($faltas_anterior_para_atual) ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-atual-btn" data-bs-toggle="tab"
                                data-bs-target="#tab-atual" type="button" role="tab">
                                <i class="bi bi-calendar-range"></i>
                                <?= $tabAtualLabel ?>
                                <span class="badge bg-info text-white ms-1"><?= count($faltas_atual_para_atual) ?></span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab-proximo-btn" data-bs-toggle="tab"
                                data-bs-target="#tab-proximo" type="button" role="tab">
                                <i class="bi bi-calendar-range"></i>
                                <?= $tabProximoLabel ?>
                                <span class="badge bg-secondary text-white ms-1"><?= count($faltas_atual_para_proximo) ?></span>
                            </button>
                        </li>
                    </ul>

                    <!-- Tab content -->
                    <div class="tab-content pt-3" id="faltasTabsContent">

                        <!-- TAB 1: Ano anterior → Este ano -->
                        <div class="tab-pane fade show active" id="tab-anterior" role="tabpanel">
                            <p class="text-muted small mb-2">Faltas ocorridas no ano letivo <?= $anoAnteriorLabel ?> cujo desconto é aplicado neste ano letivo (<?= $anoAtualLabel ?>).</p>
                            <table class="table table-sm table-bordered" id="faltasTable1">
                                <thead class="table-warning">
                                    <tr>
                                        <th>Cód. Func.</th>
                                        <th>Professor</th>
                                        <th>NIF</th>
                                        <th>Data da Falta</th>
                                        <th>Tipo</th>
                                        <th>Dias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($faltas_anterior_para_atual as $f): ?>
                                    <tr>
                                        <td><?= esc($f['cod_funcionario'] ?? '—') ?></td>
                                        <td><?= esc($f['nome_professor']) ?></td>
                                        <td><?= esc($f['NIF']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($f['data_falta'])) ?></td>
                                        <td><span class="badge bg-<?= $tipoColors[$f['tipo_falta']] ?? 'secondary' ?>"><?= $tipoLabels[$f['tipo_falta']] ?? esc($f['tipo_falta']) ?></span></td>
                                        <td class="text-center"><?= $f['dias_desconto'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="5" class="text-end">Total:</td>
                                        <td><?= array_sum(array_column($faltas_anterior_para_atual, 'dias_desconto')) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- TAB 2: Este ano → Este ano -->
                        <div class="tab-pane fade" id="tab-atual" role="tabpanel">
                            <p class="text-muted small mb-2">Faltas ocorridas neste ano letivo (<?= $anoAtualLabel ?>) cujo desconto é aplicado neste mesmo ano letivo.</p>
                            <table class="table table-sm table-bordered" id="faltasTable2">
                                <thead class="table-info">
                                    <tr>
                                        <th>Cód. Func.</th>
                                        <th>Professor</th>
                                        <th>NIF</th>
                                        <th>Data da Falta</th>
                                        <th>Tipo</th>
                                        <th>Dias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($faltas_atual_para_atual as $f): ?>
                                    <tr>
                                        <td><?= esc($f['cod_funcionario'] ?? '—') ?></td>
                                        <td><?= esc($f['nome_professor']) ?></td>
                                        <td><?= esc($f['NIF']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($f['data_falta'])) ?></td>
                                        <td><span class="badge bg-<?= $tipoColors[$f['tipo_falta']] ?? 'secondary' ?>"><?= $tipoLabels[$f['tipo_falta']] ?? esc($f['tipo_falta']) ?></span></td>
                                        <td class="text-center"><?= $f['dias_desconto'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="5" class="text-end">Total:</td>
                                        <td><?= array_sum(array_column($faltas_atual_para_atual, 'dias_desconto')) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- TAB 3: Este ano → Próximo ano -->
                        <div class="tab-pane fade" id="tab-proximo" role="tabpanel">
                            <p class="text-muted small mb-2">Faltas ocorridas neste ano letivo (<?= $anoAtualLabel ?>) cujo desconto será aplicado num ano letivo seguinte.</p>
                            <table class="table table-sm table-bordered" id="faltasTable3">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Cód. Func.</th>
                                        <th>Professor</th>
                                        <th>NIF</th>
                                        <th>Data da Falta</th>
                                        <th>Tipo</th>
                                        <th>Dias</th>
                                        <th>Ano Desconto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($faltas_atual_para_proximo as $f): ?>
                                    <tr>
                                        <td><?= esc($f['cod_funcionario'] ?? '—') ?></td>
                                        <td><?= esc($f['nome_professor']) ?></td>
                                        <td><?= esc($f['NIF']) ?></td>
                                        <td><?= date('d/m/Y', strtotime($f['data_falta'])) ?></td>
                                        <td><span class="badge bg-<?= $tipoColors[$f['tipo_falta']] ?? 'secondary' ?>"><?= $tipoLabels[$f['tipo_falta']] ?? esc($f['tipo_falta']) ?></span></td>
                                        <td class="text-center"><?= $f['dias_desconto'] ?></td>
                                        <td><?= ($f['ano_desconto'] + 1) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="fw-bold">
                                        <td colspan="5" class="text-end">Total:</td>
                                        <td><?= array_sum(array_column($faltas_atual_para_proximo, 'dias_desconto')) ?></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div><!-- /tab-content -->
                </div>
                </div><!-- /collapse -->
            </div>

            <!-- Cards Estatísticas -->
            <div class="row mb-3">
                <div class="col-md-3 col-6">
                    <div class="info-box shadow-none border">
                        <span class="info-box-icon bg-info"><i class="bi bi-people"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Professores com Atribuição</span>
                            <span class="info-box-number"><?= $stats['total_professores_com_atribuicao'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-box shadow-none border">
                        <span class="info-box-icon bg-warning"><i class="bi bi-hourglass-split"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pedidos Pendentes</span>
                            <span class="info-box-number"><?= $stats['pedidos_pendentes'] ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="info-box shadow-none border">
                        <span class="info-box-icon bg-secondary"><i class="bi bi-tag"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Professores com Alínea</span>
                            <span class="info-box-number"><?= $stats['professores_com_alinea'] ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Relatório por Professor -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-table"></i> Relatório por Professor</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-success" onclick="exportarExcel()">
                            <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-sm" id="relatorioTable">
                        <thead>
                            <tr>
                                <th>Professor</th>
                                <th>NIF</th>
                                <th>Atribuídos</th>
                                <th>Gozados</th>
                                <th title="Dias descontados por faltas registadas neste ano letivo">Descontos</th>
                                <th>Disponíveis</th>
                                <th>%</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($relatorio_professores as $item): ?>
                            <tr>
                                <td><?= esc($item['nome_professor']) ?></td>
                                <td><?= $item['NIF'] ?></td>
                                <td><?= $item['dias_total'] ?></td>
                                <td><?= $item['dias_gozados'] ?></td>
                                <td>
                                    <?php if ($item['dias_faltas'] > 0): ?>
                                        <span class="badge bg-warning text-dark" title="Faltas a descontar neste ano letivo">
                                            -<?= $item['dias_faltas'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $dispFloor = (int) $item['dias_disponiveis']; ?>
                                    <?php $cor = $dispFloor < 0 ? 'text-danger fw-bold' : ($dispFloor == 0 ? 'text-warning' : ''); ?>
                                    <span class="<?= $cor ?>"><?= $dispFloor ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $percentagem = $item['dias_total'] > 0 
                                        ? min(100, round((($item['dias_gozados'] + $item['dias_faltas']) / $item['dias_total']) * 100))
                                        : 0;
                                    ?>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: <?= $percentagem ?>%">
                                            <?= $percentagem ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<!-- DataTables Buttons -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script>
(function() {
    const dtLang = { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json' };
    const dtButtons = (title) => ([
        {
            extend: 'excelHtml5',
            text: '<i class="bi bi-file-earmark-excel"></i> Excel',
            className: 'btn btn-sm btn-success me-1',
            title: title
        },
        {
            extend: 'pdfHtml5',
            text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
            className: 'btn btn-sm btn-danger me-1',
            title: title,
            orientation: 'portrait',
            pageSize: 'A4',
            customize: function(doc) {
                doc.defaultStyle.fontSize = 8;
                doc.styles.tableHeader = { bold: true, fontSize: 8, fillColor: '#eeeeee' };
                doc.styles.title = { fontSize: 12, bold: true, margin: [0, 0, 0, 8] };
            }
        },
        {
            extend: 'print',
            text: '<i class="bi bi-printer"></i> Imprimir',
            className: 'btn btn-sm btn-secondary',
            title: title
        }
    ]);

    // Inicializar quando a tab ficar visível (evita re-init em tabs ocultas)
    function initTable(id, title, colDefs) {
        if ($.fn.DataTable.isDataTable('#' + id)) return;
        $('#' + id).DataTable({
            language: dtLang,
            pageLength: 25,
            order: [[0, 'asc']],
            dom: '<"d-flex justify-content-between align-items-center mb-2"Bf>rtip',
            buttons: dtButtons(title),
            columnDefs: colDefs || []
        });
    }

    // Tabela principal de relatório
    if (document.getElementById('relatorioTable')) {
        $('#relatorioTable').DataTable({
            language: dtLang,
            pageLength: 50
        });
    }

    initTable('faltasTable1', 'Faltas Ano Anterior → Este Ano');

    $('#tab-atual-btn').one('shown.bs.tab', function() {
        initTable('faltasTable2', 'Faltas Este Ano → Este Ano');
    });
    $('#tab-proximo-btn').one('shown.bs.tab', function() {
        initTable('faltasTable3', 'Faltas Este Ano → Próximo Ano');
    });
})();
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
