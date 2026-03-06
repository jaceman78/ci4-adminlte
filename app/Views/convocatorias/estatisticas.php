<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="fas fa-chart-bar"></i> Estatísticas de Vigilâncias</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('convocatorias') ?>">Convocatórias</a></li>
                    <li class="breadcrumb-item active">Estatísticas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        <!-- Card de Estatísticas -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> Estatísticas de Vigilâncias</h3>
            </div>
            <div class="card-body">
                <!-- Estatísticas Gerais -->
                <div class="row mb-4">
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-info">
                            <span class="info-box-icon"><i class="fas fa-clipboard-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Convocatórias</span>
                                <span class="info-box-number"><?= number_format($estatisticas_gerais['total_convocatorias'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-success">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Professores</span>
                                <span class="info-box-number"><?= number_format($estatisticas_gerais['total_professores'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-warning">
                            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Horas</span>
                                <span class="info-box-number"><?= number_format(($estatisticas_gerais['total_minutos_geral'] ?? 0) / 60, 1) ?>h</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box bg-primary">
                            <span class="info-box-icon"><i class="fas fa-user-clock"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Suplências</span>
                                <span class="info-box-number"><?= number_format($estatisticas_gerais['total_suplencias_geral'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Presenças</span>
                                <span class="info-box-number"><?= number_format($estatisticas_gerais['total_presencas_geral'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Faltas</span>
                                <span class="info-box-number"><?= number_format($estatisticas_gerais['total_faltas_geral'] ?? 0) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-percent"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Taxa de Presença</span>
                                <span class="info-box-number"><?= number_format($estatisticas_gerais['taxa_presenca'] ?? 0, 1) ?>%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Tabela de Estatísticas por Professor -->
                <h5 class="mb-3 text-dark"><i class="fas fa-user-graduate text-primary"></i> Estatísticas por Professor</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tabelaEstatisticas">
                        <thead class="table-light">
                            <tr>
                                <th class="text-dark">Professor</th>
                                <th class="text-center text-dark">Vigilâncias</th>
                                <th class="text-center text-dark">Suplências</th>
                                <th class="text-center text-dark">Tempo Total</th>
                                <th class="text-center text-dark">Presenças</th>
                                <th class="text-center text-dark">Faltas</th>
                                <th class="text-center text-dark">Faltas Just.</th>
                                <th class="text-center text-dark">Confirmados</th>
                                <th class="text-center text-dark">Taxa Presença</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($estatisticas_professores)): ?>
                                <?php foreach ($estatisticas_professores as $stat): 
                                    $totalComPresenca = ($stat['total_presencas'] ?? 0) + ($stat['total_faltas'] ?? 0) + ($stat['total_faltas_justificadas'] ?? 0);
                                    $taxaPresenca = $totalComPresenca > 0 
                                        ? round((($stat['total_presencas'] ?? 0) / $totalComPresenca) * 100, 1) 
                                        : 0;
                                    $horas = floor(($stat['total_minutos'] ?? 0) / 60);
                                    $minutos = ($stat['total_minutos'] ?? 0) % 60;
                                ?>
                                <tr>
                                    <td><i class="fas fa-user text-primary"></i> <strong><?= esc($stat['nome_professor']) ?></strong></td>
                                    <td class="text-center"><span class="badge bg-primary text-white"><?= number_format($stat['total_vigilancias'] ?? 0) ?></span></td>
                                    <td class="text-center"><span class="badge bg-info text-white"><?= number_format($stat['total_suplencias'] ?? 0) ?></span></td>
                                    <td class="text-center"><strong class="text-dark"><?= $horas ?>h <?= $minutos ?>m</strong></td>
                                    <td class="text-center"><span class="badge bg-success text-white"><?= number_format($stat['total_presencas'] ?? 0) ?></span></td>
                                    <td class="text-center"><span class="badge bg-danger text-white"><?= number_format($stat['total_faltas'] ?? 0) ?></span></td>
                                    <td class="text-center"><span class="badge bg-warning text-dark"><?= number_format($stat['total_faltas_justificadas'] ?? 0) ?></span></td>
                                    <td class="text-center"><span class="badge bg-secondary text-white"><?= number_format($stat['total_confirmados'] ?? 0) ?></span></td>
                                    <td class="text-center">
                                        <span class="badge <?= $taxaPresenca >= 80 ? 'bg-success text-white' : ($taxaPresenca >= 60 ? 'bg-warning text-dark' : 'bg-danger text-white') ?>">
                                            <?= number_format($taxaPresenca, 1) ?>%
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <i class="fas fa-info-circle text-info"></i> <span class="text-muted">Sem dados de vigilâncias registados</span>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Garantir cores visíveis nos badges */
.badge.bg-primary {
    background-color: #007bff !important;
    color: #fff !important;
}

.badge.bg-info {
    background-color: #17a2b8 !important;
    color: #fff !important;
}

.badge.bg-success {
    background-color: #28a745 !important;
    color: #fff !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
    color: #fff !important;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
    color: #fff !important;
}

/* Garantir texto escuro em células da tabela */
.table td, .table th {
    color: #212529 !important;
}

/* Thead com fundo claro e texto escuro */
.table-light th {
    background-color: #f8f9fa !important;
    color: #212529 !important;
}

/* DataTables controls */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    color: #212529 !important;
}

.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
    color: #212529 !important;
    border: 1px solid #ced4da !important;
}

/* Ícones coloridos */
.text-primary {
    color: #007bff !important;
}

.text-dark {
    color: #212529 !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    if ($('#tabelaEstatisticas').length) {
        // Verificar se há dados na tabela antes de inicializar DataTable
        const temDados = $('#tabelaEstatisticas tbody tr').length > 0 && 
                        !$('#tabelaEstatisticas tbody tr td[colspan]').length;
        
        if (temDados) {
            $('#tabelaEstatisticas').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
                },
                pageLength: 10,
                order: [[1, 'desc']], // Ordenar por vigilâncias (coluna 1) descendente
                columnDefs: [
                    { targets: [1, 2, 3, 4, 5, 6, 7, 8], className: 'text-center' }
                ],
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            });
        }
    }
});
</script>
<?= $this->endSection() ?>
