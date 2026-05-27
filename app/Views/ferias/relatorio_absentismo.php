<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-8">
                <h1><i class="bi bi-clipboard2-pulse"></i> Relatório de Absentismo &mdash; <?= $ano_letivo['anoletivo'] ?>/<?= $ano_letivo['anoletivo'] + 1 ?></h1>
            </div>
            <div class="col-sm-4 text-end">
                <a href="<?= base_url('ferias/relatorios') ?>" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Voltar a Relatórios
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">

    <!-- Filtro Ano Letivo -->
    <div class="card card-outline card-secondary mb-3">
        <div class="card-body py-2">
            <form method="get" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 fw-semibold">Ano Letivo:</label>
                </div>
                <div class="col-auto">
                    <select name="ano_letivo_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <?php foreach ($anos_letivos as $al): ?>
                            <option value="<?= $al['id_anoletivo'] ?>" <?= $al['id_anoletivo'] == $ano_letivo_id ? 'selected' : '' ?>>
                                <?= $al['anoletivo'] ?>/<?= $al['anoletivo'] + 1 ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Cards de resumo -->
    <div class="row mb-3">
        <div class="col-md-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3><?= $total_faltas ?></h3>
                    <p>Total de Faltas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= number_format($total_dias, 1) ?></h3>
                    <p>Dias Descontados</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= $total_professores ?></h3>
                    <p>Professores Afetados</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3><?= $total_injustificadas ?></h3>
                    <p>Faltas Injustificadas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico por mês -->
    <div class="card card-outline card-primary mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="bi bi-bar-chart-line"></i> Absentismo por Mês</h3>
        </div>
        <div class="card-body">
            <?php if (empty($faltas_por_mes)): ?>
                <p class="text-muted text-center py-3">Sem dados para este ano letivo.</p>
            <?php else: ?>
                <canvas id="chartMes" style="max-height: 280px;"></canvas>
            <?php endif; ?>
        </div>
    </div>

    <!-- Heat Map por dia -->
    <div class="card card-outline card-warning mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="bi bi-calendar-heat"></i> Heat Map — Dias com Mais Faltas</h3>
            <div class="card-tools">
                <span class="text-muted small me-2">Intensidade:</span>
                <span class="badge hm-cell" style="background:#eee;color:#666;">0</span>
                <span class="badge hm-cell" style="background:#ffeda0;color:#333;">baixo</span>
                <span class="badge hm-cell" style="background:#feb24c;color:#333;">médio</span>
                <span class="badge hm-cell" style="background:#f03b20;color:#fff;">alto</span>
                <span class="badge hm-cell" style="background:#8b0000;color:#fff;">máx</span>
            </div>
        </div>
        <div class="card-body pb-2" style="overflow-x:auto;">
            <?php
            $nomeMeses  = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                           'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            // Ordem do ano letivo: Set … Ago
            $ordemMeses = [9, 10, 11, 12, 1, 2, 3, 4, 5, 6, 7, 8];

            function hmColor($val, $max) {
                if ($val <= 0)         return '#eeeeee';
                $ratio = $val / $max;
                if ($ratio <= 0.25)    return '#ffeda0';
                if ($ratio <= 0.50)    return '#feb24c';
                if ($ratio <= 0.75)    return '#f03b20';
                return '#8b0000';
            }
            function hmTextColor($val, $max) {
                if ($val <= 0) return '#aaa';
                $ratio = $val / $max;
                return $ratio >= 0.5 ? '#fff' : '#333';
            }
            ?>
            <table class="table table-bordered table-sm heatmap-table mb-0" style="min-width:700px;">
                <thead>
                    <tr>
                        <th style="width:90px;">Mês</th>
                        <?php for ($d = 1; $d <= 31; $d++): ?>
                            <th class="text-center" style="width:28px;font-size:.7rem;padding:2px;"><?= $d ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($ordemMeses as $mes):
                    $diasDoMes = cal_days_in_month(CAL_GREGORIAN, $mes, $mes >= 9 ? $ano_letivo['anoletivo'] : $ano_letivo['anoletivo'] + 1);
                    $totalMes  = array_sum(array_column($heatmap[$mes] ?? [], 'val'));
                ?>
                    <tr>
                        <td class="fw-semibold" style="font-size:.8rem;white-space:nowrap;">
                            <?= $nomeMeses[$mes] ?>
                            <?php if ($totalMes > 0): ?>
                                <br><small class="text-danger"><?= number_format($totalMes, 1) ?>d</small>
                            <?php endif; ?>
                        </td>
                        <?php for ($d = 1; $d <= 31; $d++):
                            if ($d > $diasDoMes):
                        ?>
                            <td style="background:#f8f8f8;"></td>
                        <?php else:
                            $cell = $heatmap[$mes][$d] ?? null;
                            $val  = $cell ? $cell['val'] : 0;
                            $bg  = hmColor($val, $max_heatmap);
                            $fg  = hmTextColor($val, $max_heatmap);
                            $dataCelula = $cell ? $cell['date'] : sprintf('%04d-%02d-%02d', ($mes >= 9 ? $ano_letivo['anoletivo'] : $ano_letivo['anoletivo'] + 1), $mes, $d);
                        ?>
                            <td class="text-center p-0 <?= $val > 0 ? 'hm-cell-falta' : '' ?>"
                                style="background:<?= $bg ?>;color:<?= $fg ?>;font-size:.65rem;height:22px;vertical-align:middle;<?= $val > 0 ? 'cursor:pointer;' : '' ?>"
                                <?= $val > 0 ? 'data-date="' . $dataCelula . '" data-anoletivo="' . $ano_letivo_id . '" data-dias="' . number_format($val, 1) . '"' : '' ?>>
                                <?= $val > 0 ? number_format($val, 0) : '' ?>
                            </td>
                        <?php endif; endfor; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabela por professor -->
    <div class="card card-outline card-info mb-3">
        <div class="card-header">
            <h3 class="card-title"><i class="bi bi-table"></i> Detalhe por Professor</h3>
        </div>
        <div class="card-body">
            <?php if (empty($faltas_por_professor)): ?>
                <p class="text-muted text-center py-3">Sem registos de faltas para este ano letivo.</p>
            <?php else: ?>
            <table id="tblProfessores" class="table table-bordered table-striped table-sm">
                <thead>
                    <tr>
                        <th>Professor</th>
                        <th>Escola</th>
                        <th class="text-center">Total Faltas</th>
                        <th class="text-center">Dias Descontados</th>
                        <th class="text-center">Justificadas</th>
                        <th class="text-center">Injustificadas</th>
                        <th class="text-center">Última Falta</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($faltas_por_professor as $row): ?>
                    <tr>
                        <td><?= esc($row['nome_professor']) ?></td>
                        <td><?= esc($row['escola_nome'] ?? '—') ?></td>
                        <td class="text-center"><?= $row['total_faltas'] ?></td>
                        <td class="text-center">
                            <span class="badge bg-<?= $row['total_dias_desconto'] >= 5 ? 'danger' : ($row['total_dias_desconto'] >= 2 ? 'warning text-dark' : 'secondary') ?>">
                                <?= number_format($row['total_dias_desconto'], 1) ?>
                            </span>
                        </td>
                        <td class="text-center text-success"><?= $row['faltas_justificadas'] ?></td>
                        <td class="text-center <?= $row['faltas_injustificadas'] > 0 ? 'text-danger fw-bold' : '' ?>">
                            <?= $row['faltas_injustificadas'] ?>
                        </td>
                        <td class="text-center"><?= $row['ultima_falta'] ? date('d/m/Y', strtotime($row['ultima_falta'])) : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
<?php if (!empty($faltas_por_mes)): ?>
(function () {
    const nomeMeses = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    const rawData   = <?= json_encode($faltas_por_mes) ?>;

    // Ordenar por mês letivo: Set(9) … Ago(8)
    const ordemLetiva = [9,10,11,12,1,2,3,4,5,6,7,8];
    const byMes = {};
    rawData.forEach(r => { byMes[parseInt(r.mes)] = r; });

    const labels     = [];
    const diasData   = [];
    const faltasData = [];
    const profData   = [];

    ordemLetiva.forEach(m => {
        labels.push(nomeMeses[m]);
        if (byMes[m]) {
            diasData.push(parseFloat(byMes[m].total_dias));
            faltasData.push(parseInt(byMes[m].total_faltas));
            profData.push(parseInt(byMes[m].professores_afetados));
        } else {
            diasData.push(0);
            faltasData.push(0);
            profData.push(0);
        }
    });

    new Chart(document.getElementById('chartMes'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Dias Descontados',
                    data: diasData,
                    backgroundColor: 'rgba(220,53,69,0.75)',
                    borderColor: '#dc3545',
                    borderWidth: 1,
                    yAxisID: 'yDias'
                },
                {
                    label: 'Professores Afetados',
                    data: profData,
                    type: 'line',
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13,110,253,0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    fill: false,
                    tension: 0.3,
                    yAxisID: 'yProf'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { position: 'top' } },
            scales: {
                yDias: {
                    type: 'linear', position: 'left',
                    title: { display: true, text: 'Dias descontados' },
                    beginAtZero: true
                },
                yProf: {
                    type: 'linear', position: 'right',
                    title: { display: true, text: 'Professores afetados' },
                    beginAtZero: true,
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
})();
<?php endif; ?>

// DataTable
$(function () {
    if ($.fn.DataTable && $('#tblProfessores').length) {
        $('#tblProfessores').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json' },
            pageLength: 25,
            order: [[3, 'desc']]
        });
    }
    // Popover AJAX nas células do heatmap
    var _popoverAtivo = null;
    var _ajaxCache   = {};

    $(document).on('mouseenter', '.hm-cell-falta', function () {
        var $cell      = $(this);
        var data       = $cell.data('date');
        var anoLetivo  = $cell.data('anoletivo');
        var dias       = $cell.data('dias');
        var cacheKey   = data + '_' + anoLetivo;

        function mostrarPopover(conteudo) {
            if (_popoverAtivo) {
                _popoverAtivo.dispose();
                _popoverAtivo = null;
            }
            var partes = data.split('-');
            var dataFmt = partes[2] + '/' + partes[1] + '/' + partes[0];
            var pop = new bootstrap.Popover($cell[0], {
                trigger:   'manual',
                html:      true,
                sanitize:  false,
                placement: 'top',
                title:     '<i class="bi bi-calendar-x text-danger"></i> ' + dataFmt + ' <small class="text-muted">(' + dias + ' dias)</small>',
                content:   conteudo,
                container: 'body'
            });
            pop.show();
            _popoverAtivo = pop;
        }

        if (_ajaxCache[cacheKey] !== undefined) {
            mostrarPopover(_ajaxCache[cacheKey]);
            return;
        }

        $.ajax({
            url: '<?= base_url('ferias/faltas-por-dia') ?>',
            method: 'GET',
            data: { data: data, ano_letivo_id: anoLetivo },
            dataType: 'json',
            success: function (resp) {
                var html = '';
                if (resp.success && resp.faltas.length > 0) {
                    var tipoLabel = {
                        'injustificada':    '<span class="badge bg-danger">Injust.</span>',
                        'artigo_102':       '<span class="badge bg-warning text-dark">Art.102</span>',
                        'art_135_n4_ltfp':  '<span class="badge bg-secondary">Art.135</span>',
                        'artigo_89_ecd':    '<span class="badge bg-info text-white">Art.89</span>'
                    };
                    html += '<ul class="list-unstyled mb-0" style="min-width:180px;max-width:280px;">';
                    resp.faltas.forEach(function (f) {
                        var badge = tipoLabel[f.tipo_falta] || '<span class="badge bg-secondary">' + f.tipo_falta + '</span>';
                        var url = '<?= base_url('ferias/gerir-professor') ?>/' + f.user_nif;
                        html += '<li class="py-1" style="border-bottom:1px solid #eee;">';
                        html += '<div class="d-flex justify-content-between align-items-center gap-2">';
                        html += '<a href="' + url + '" target="_blank" style="font-size:.82rem;text-decoration:none;color:inherit;" class="text-primary-hover">' + f.nome + '</a>';
                        html += '<span class="ms-1">' + badge + '</span>';
                        html += '</div>';
                        if (f.motivo) {
                            html += '<div class="text-muted" style="font-size:.72rem;">' + f.motivo + '</div>';
                        }
                        html += '</li>';
                    });
                    html += '</ul>';
                } else {
                    html = '<span class="text-muted">Sem detalhes disponíveis</span>';
                }
                _ajaxCache[cacheKey] = html;
                mostrarPopover(html);
            }
        });
    });

    $(document).on('mouseleave', '.hm-cell-falta', function () {
        var $cell = $(this);
        setTimeout(function () {
            if (_popoverAtivo && !$('.popover:hover').length) {
                _popoverAtivo.dispose();
                _popoverAtivo = null;
            }
        }, 200);
    });

    $(document).on('mouseleave', '.popover', function () {
        if (_popoverAtivo) {
            _popoverAtivo.dispose();
            _popoverAtivo = null;
        }
    });
});
</script>
<?= $this->endSection() ?>
