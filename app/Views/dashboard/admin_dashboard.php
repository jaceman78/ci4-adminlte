<?= $this->extend('layout/master') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard Administrador</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Estatísticas Principais -->
            <div class="row">
                <!-- Total Tickets -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= number_format($stats['total_tickets']) ?></h3>
                            <p>Total de Tickets</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <a href="<?= base_url('tickets/todos') ?>" class="small-box-footer">
                            Ver todos <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Tickets Ativos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= number_format($stats['tickets_ativos']) ?></h3>
                            <p>Tickets Ativos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <a href="<?= base_url('tickets/tratamento') ?>" class="small-box-footer">
                            Ver ativos <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Tickets Críticos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= number_format($stats['tickets_criticos']) ?></h3>
                            <p>Tickets Críticos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Requer atenção <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Tickets Resolvidos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= number_format($stats['tickets_resolvidos']) ?></h3>
                            <p>Tickets Resolvidos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Histórico <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Métricas Adicionais -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-user-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Não Atribuídos</span>
                            <span class="info-box-number"><?= number_format($stats['tickets_nao_atribuidos']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tempo Médio Resolução</span>
                            <span class="info-box-number"><?= number_format($stats['tempo_medio_resolucao'], 1) ?>h</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Taxa Resolução Hoje</span>
                            <span class="info-box-number"><?= number_format($stats['taxa_resolucao_hoje'], 1) ?>%</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Técnicos Ativos</span>
                            <span class="info-box-number"><?= number_format($stats['total_tecnicos']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row">
                <!-- Tickets por Estado -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie"></i> Tickets por Estado</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="ticketsPorEstadoChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Evolução Últimos 30 Dias -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line"></i> Evolução de Tickets (30 dias)</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="evolucaoTicketsChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance dos Técnicos -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-trophy"></i> Performance dos Técnicos (Top 10)</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Técnico</th>
                                        <th class="text-center">Total Atribuídos</th>
                                        <th class="text-center">Resolvidos</th>
                                        <th class="text-center">Em Progresso</th>
                                        <th class="text-center">Tempo Médio (h)</th>
                                        <th class="text-center">Taxa de Sucesso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($performance_tecnicos)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Nenhum técnico com tickets atribuídos</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($performance_tecnicos as $tecnico): ?>
                                            <?php 
                                            $taxa_sucesso = $tecnico['total_atribuidos'] > 0 
                                                ? round(($tecnico['resolvidos'] / $tecnico['total_atribuidos']) * 100, 1) 
                                                : 0;
                                            $cor_taxa = $taxa_sucesso >= 80 ? 'success' : ($taxa_sucesso >= 50 ? 'warning' : 'danger');
                                            ?>
                                            <tr>
                                                <td><i class="fas fa-user-cog text-primary"></i> <?= esc($tecnico['tecnico']) ?></td>
                                                <td class="text-center"><span class="badge bg-info"><?= $tecnico['total_atribuidos'] ?></span></td>
                                                <td class="text-center"><span class="badge bg-success"><?= $tecnico['resolvidos'] ?></span></td>
                                                <td class="text-center"><span class="badge bg-warning"><?= $tecnico['em_progresso'] ?></span></td>
                                                <td class="text-center"><?= number_format($tecnico['tempo_medio_horas'], 1) ?>h</td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $cor_taxa ?>"><?= number_format($taxa_sucesso, 1) ?>%</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas Críticos -->
            <?php if (!empty($tickets_criticos)): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-circle"></i> Tickets Críticos Não Atribuídos</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Equipamento</th>
                                            <th>Localização</th>
                                            <th>Tipo de Avaria</th>
                                            <th>Criado</th>
                                            <th>Criado Por</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets_criticos as $ticket): ?>
                                            <tr>
                                                <td><strong>#<?= $ticket['id'] ?></strong></td>
                                                <td><?= esc($ticket['equipamento_nome']) ?></td>
                                                <td>
                                                    <?= esc($ticket['escola_nome']) ?><br>
                                                    <small class="text-muted"><?= esc($ticket['codigo_sala']) ?></small>
                                                </td>
                                                <td><?= esc($ticket['tipo_avaria_nome']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></td>
                                                <td><?= esc($ticket['criador_nome']) ?></td>
                                                <td>
                                                    <a href="<?= base_url('tickets/view/' . $ticket['id']) ?>" class="btn btn-sm btn-info" title="Ver Detalhes">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
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
            <?php endif; ?>

            <!-- Tickets Antigos e Análises -->
            <div class="row">
                <!-- Tickets Pendentes > 48h -->
                <div class="col-md-6">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clock"></i> Tickets Pendentes +48h</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($tickets_antigos)): ?>
                                <p class="text-center text-muted">✓ Nenhum ticket pendente há mais de 48 horas</p>
                            <?php else: ?>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm">
                                        <tbody>
                                            <?php foreach ($tickets_antigos as $ticket): ?>
                                                <tr>
                                                    <td>
                                                        <strong>#<?= $ticket['id'] ?></strong><br>
                                                        <small><?= esc($ticket['equipamento_nome']) ?></small><br>
                                                        <small class="text-muted"><?= esc($ticket['escola_nome']) ?></small>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="badge bg-danger"><?= $ticket['horas_pendente'] ?>h</span><br>
                                                        <a href="<?= base_url('tickets/view/' . $ticket['id']) ?>" class="btn btn-xs btn-info mt-1">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Escolas com Mais Tickets -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-school"></i> Escolas com Mais Tickets Ativos</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($escolas_mais_tickets)): ?>
                                <p class="text-center text-muted">Nenhum ticket ativo no momento</p>
                            <?php else: ?>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm">
                                        <tbody>
                                            <?php foreach ($escolas_mais_tickets as $escola): ?>
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-school text-primary"></i>
                                                        <?= esc($escola['escola']) ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="badge bg-info"><?= $escola['total'] ?> tickets</span>
                                                        <?php if ($escola['criticos'] > 0): ?>
                                                            <span class="badge bg-danger"><?= $escola['criticos'] ?> críticos</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Análises Adicionais -->
            <div class="row">
                <!-- Tipos de Avaria Mais Frequentes -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-wrench"></i> Tipos de Avaria Mais Frequentes</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo de Avaria</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Tempo Médio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tipos_avaria_frequentes)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Sem dados disponíveis</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($tipos_avaria_frequentes as $tipo): ?>
                                            <tr>
                                                <td><?= esc($tipo['tipo_avaria']) ?></td>
                                                <td class="text-center"><span class="badge bg-info"><?= $tipo['total'] ?></span></td>
                                                <td class="text-center"><?= number_format($tipo['tempo_medio_horas'], 1) ?>h</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Equipamentos Mais Problemáticos -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-laptop-medical"></i> Equipamentos Mais Problemáticos</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo de Equipamento</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Pendentes</th>
                                        <th class="text-center">Resolvidos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($equipamentos_problematicos)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Sem dados disponíveis</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($equipamentos_problematicos as $equipamento): ?>
                                            <tr>
                                                <td><?= esc($equipamento['tipo_equipamento']) ?></td>
                                                <td class="text-center"><span class="badge bg-info"><?= $equipamento['total_tickets'] ?></span></td>
                                                <td class="text-center"><span class="badge bg-warning"><?= $equipamento['pendentes'] ?></span></td>
                                                <td class="text-center"><span class="badge bg-success"><?= $equipamento['resolvidos'] ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
$(document).ready(function() {
    
    // Gráfico de Pizza - Tickets por Estado
    const ctxEstado = document.getElementById('ticketsPorEstadoChart').getContext('2d');
    new Chart(ctxEstado, {
        type: 'pie',
        data: {
            labels: [<?php echo implode(',', array_map(function($item) { return "'" . $item['estado'] . "'"; }, $tickets_por_estado)); ?>],
            datasets: [{
                data: [<?php echo implode(',', array_column($tickets_por_estado, 'total')); ?>],
                backgroundColor: [
                    <?php 
                    foreach ($tickets_por_estado as $item) {
                        $cor = $item['cor'] ?? 'secondary';
                        $corMap = [
                            'primary' => '#007bff',
                            'warning' => '#ffc107',
                            'success' => '#28a745',
                            'danger' => '#dc3545',
                            'info' => '#17a2b8',
                            'secondary' => '#6c757d'
                        ];
                        echo "'" . ($corMap[$cor] ?? '#6c757d') . "',";
                    }
                    ?>
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });

    // Gráfico de Linha - Evolução de Tickets
    const ctxEvolucao = document.getElementById('evolucaoTicketsChart').getContext('2d');
    new Chart(ctxEvolucao, {
        type: 'line',
        data: {
            labels: [<?php echo implode(',', array_map(function($item) { return "'" . date('d/m', strtotime($item['data'])) . "'"; }, $evolucao_tickets)); ?>],
            datasets: [
                {
                    label: 'Criados',
                    data: [<?php echo implode(',', array_column($evolucao_tickets, 'criados')); ?>],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Resolvidos',
                    data: [<?php echo implode(',', array_column($evolucao_tickets, 'resolvidos')); ?>],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

});
</script>
<?= $this->endSection() ?>
