<?= $this->extend('layout/master') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard Técnico</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Cards de Estatísticas -->
            <div class="row">
                <!-- Tickets Ativos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $stats['tickets_ativos'] ?></h3>
                            <p>Tickets Ativos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <a href="<?= site_url('tickets/tratamento') ?>" class="small-box-footer">
                            Ver Todos <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Tickets Urgentes -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $stats['tickets_urgentes'] ?></h3>
                            <p>Tickets Urgentes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="<?= site_url('tickets/tratamento') ?>" class="small-box-footer">
                            Ver Urgentes <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Aguardam Peça -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $stats['aguardam_peca'] ?></h3>
                            <p>Aguardam Peça</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <a href="<?= site_url('tickets/tratamento') ?>" class="small-box-footer">
                            Ver Pendentes <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Resolvidos Este Mês -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $stats['resolvidos_mes'] ?></h3>
                            <p>Resolvidos Este Mês</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="<?= site_url('tickets/meus') ?>" class="small-box-footer">
                            Ver Histórico <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Permutas (se aplicável) -->
            <?php if (!empty($permutas_stats)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exchange-alt"></i> As Minhas Permutas</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('permutas/minhas') ?>" class="btn btn-tool">
                                    Ver Todas <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3><?= $permutas_stats['pendentes'] ?></h3>
                                            <p>Pendentes</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-clock"></i></div>
                                        <a href="<?= base_url('permutas/minhas') ?>" class="small-box-footer">
                                            Ver <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3><?= $permutas_stats['aprovadas'] ?></h3>
                                            <p>Aprovadas</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                                        <a href="<?= base_url('permutas/minhas') ?>" class="small-box-footer">
                                            Ver <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3><?= $permutas_stats['rejeitadas'] ?></h3>
                                            <p>Rejeitadas</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-times-circle"></i></div>
                                        <a href="<?= base_url('permutas/minhas') ?>" class="small-box-footer">
                                            Ver <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3><?= $permutas_stats['como_substituto'] ?></h3>
                                            <p>Como Substituto</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-user-check"></i></div>
                                        <a href="<?= base_url('permutas/minhas') ?>" class="small-box-footer">
                                            Ver <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($permutas_recentes)): ?>
                            <h5 class="mt-3 mb-2">Permutas Recentes</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Aula</th>
                                            <th>Data Original</th>
                                            <th>Data Reposição</th>
                                            <th>Estado</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($permutas_recentes as $permuta): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($permuta['disciplina_abrev']) ?></strong><br>
                                                <small><?= esc($permuta['turma_nome']) ?></small>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($permuta['data_aula_original'])) ?></td>
                                            <td><?= date('d/m/Y', strtotime($permuta['data_aula_permutada'])) ?></td>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'pendente' => 'warning',
                                                    'aprovada' => 'success',
                                                    'rejeitada' => 'danger',
                                                    'cancelada' => 'secondary'
                                                ];
                                                ?>
                                                <span class="badge badge-<?= $badges[$permuta['estado']] ?? 'secondary' ?>">
                                                    <?= ucfirst($permuta['estado']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('permutas/ver/' . $permuta['id']) ?>" 
                                                   class="btn btn-xs btn-info">
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
            </div>
            <?php endif; ?>

            <!-- Métricas de Performance -->
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="info-box bg-gradient-primary">
                        <span class="info-box-icon"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tempo Médio de Resolução</span>
                            <span class="info-box-number"><?= $stats['tempo_medio_horas'] ?> horas</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?= min(100, (24 / max(1, $stats['tempo_medio_horas'])) * 100) ?>%"></div>
                            </div>
                            <span class="progress-description">
                                Últimos 30 dias
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-12">
                    <div class="info-box bg-gradient-success">
                        <span class="info-box-icon"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Taxa de Reabertura</span>
                            <span class="info-box-number"><?= $stats['taxa_reabertura'] ?>%</span>
                            <div class="progress">
                                <div class="progress-bar bg-<?= $stats['taxa_reabertura'] > 10 ? 'danger' : 'success' ?>" 
                                     style="width: <?= $stats['taxa_reabertura'] ?>%"></div>
                            </div>
                            <span class="progress-description">
                                <?= $stats['taxa_reabertura'] <= 5 ? 'Excelente!' : ($stats['taxa_reabertura'] <= 10 ? 'Bom' : 'Necessita atenção') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Coluna Esquerda: Tickets -->
                <div class="col-lg-8">
                    
                    <!-- Tickets Urgentes -->
                    <?php if (!empty($tickets_urgentes)): ?>
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Tickets Urgentes</h3>
                            <div class="card-tools">
                                <span class="badge badge-danger"><?= count($tickets_urgentes) ?></span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Equipamento</th>
                                            <th>Prioridade</th>
                                            <th>Estado</th>
                                            <th>Criado</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($tickets_urgentes, 0, 5) as $ticket): ?>
                                        <tr>
                                            <td><strong>#<?= $ticket['id'] ?></strong></td>
                                            <td>
                                                <small><?= esc($ticket['equipamento_nome'] ?? 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $ticket['prioridade'] == 'critica' ? 'danger' : 'warning' ?>">
                                                    <?= ucfirst($ticket['prioridade']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?= ucfirst(str_replace('_', ' ', $ticket['estado'])) ?></span>
                                            </td>
                                            <td><small><?= date('d/m H:i', strtotime($ticket['created_at'])) ?></small></td>
                                            <td>
                                                <a href="<?= site_url('tickets/view/' . $ticket['id']) ?>" class="btn btn-xs btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (count($tickets_urgentes) > 5): ?>
                        <div class="card-footer text-center">
                            <a href="<?= site_url('tickets/tratamento') ?>" class="btn btn-sm btn-danger">
                                Ver Todos os Urgentes (<?= count($tickets_urgentes) ?>)
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Tickets Atribuídos -->
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-wrench"></i> Meus Tickets em Resolução</h3>
                            <div class="card-tools">
                                <span class="badge badge-primary"><?= count($tickets_atribuidos) ?></span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($tickets_atribuidos)): ?>
                            <div class="text-center p-4">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="text-muted">Nenhum ticket em resolução no momento</p>
                            </div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Equipamento</th>
                                            <th>Tipo Avaria</th>
                                            <th>Prioridade</th>
                                            <th>Estado</th>
                                            <th>Criado</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets_atribuidos as $ticket): ?>
                                        <tr>
                                            <td><strong>#<?= $ticket['id'] ?></strong></td>
                                            <td>
                                                <small><?= esc($ticket['equipamento_nome'] ?? 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <small><?= esc($ticket['tipo_avaria_nome'] ?? 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                $prioridadeCores = [
                                                    'baixa' => 'success',
                                                    'media' => 'warning',
                                                    'alta' => 'orange',
                                                    'critica' => 'danger'
                                                ];
                                                $cor = $prioridadeCores[$ticket['prioridade']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $cor ?>">
                                                    <?= ucfirst($ticket['prioridade']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $estadoCores = [
                                                    'novo' => 'primary',
                                                    'em_resolucao' => 'warning',
                                                    'aguarda_peca' => 'info'
                                                ];
                                                $corEstado = $estadoCores[$ticket['estado']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $corEstado ?>">
                                                    <?= ucfirst(str_replace('_', ' ', $ticket['estado'])) ?>
                                                </span>
                                            </td>
                                            <td><small><?= date('d/m H:i', strtotime($ticket['created_at'])) ?></small></td>
                                            <td>
                                                <a href="<?= site_url('tickets/view/' . $ticket['id']) ?>" 
                                                   class="btn btn-xs btn-primary" title="Ver Ticket">
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
                        <div class="card-footer text-center">
                            <a href="<?= site_url('tickets/tratamento') ?>" class="btn btn-sm btn-primary">
                                Ver Todos os Meus Tickets
                            </a>
                        </div>
                    </div>

                    <!-- Tickets Aguardam Peça -->
                    <?php if (!empty($tickets_aguardam_peca)): ?>
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-box"></i> Tickets Aguardam Peça</h3>
                            <div class="card-tools">
                                <span class="badge badge-warning"><?= count($tickets_aguardam_peca) ?></span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Equipamento</th>
                                            <th>Descrição</th>
                                            <th>Desde</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets_aguardam_peca as $ticket): ?>
                                        <tr>
                                            <td><strong>#<?= $ticket['id'] ?></strong></td>
                                            <td><small><?= esc($ticket['equipamento_nome'] ?? 'N/A') ?></small></td>
                                            <td><small><?= esc(substr($ticket['descricao'] ?? '', 0, 50)) ?>...</small></td>
                                            <td><small><?= date('d/m/Y', strtotime($ticket['updated_at'])) ?></small></td>
                                            <td>
                                                <a href="<?= site_url('tickets/view/' . $ticket['id']) ?>" class="btn btn-xs btn-warning">
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
                    <?php endif; ?>

                    <!-- Gráfico de Evolução -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line"></i> Tickets Resolvidos - Últimos 7 Dias</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="chartResolvidos" height="100"></canvas>
                        </div>
                    </div>

                </div>

                <!-- Coluna Direita: Ações Rápidas e Info -->
                <div class="col-lg-4">
                    
                    <!-- Ações Rápidas -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-bolt"></i> Ações Rápidas</h3>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?= site_url('tickets/tratamento') ?>" class="btn btn-primary btn-block mb-2">
                                    <i class="fas fa-tasks"></i> Ver Tickets em Tratamento
                                </a>
                                <a href="<?= site_url('equipamentos') ?>" class="btn btn-info btn-block mb-2">
                                    <i class="fas fa-laptop"></i> Ver Equipamentos
                                </a>
                                <a href="<?= site_url('materiais') ?>" class="btn btn-warning btn-block mb-2">
                                    <i class="fas fa-box"></i> Registar Material
                                </a>
                                <a href="<?= site_url('tickets/meus') ?>" class="btn btn-success btn-block">
                                    <i class="fas fa-history"></i> Histórico de Tickets
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tickets por Localização -->
                    <?php if (!empty($tickets_por_localizacao)): ?>
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Tickets por Localização</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($tickets_por_localizacao as $loc): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= esc($loc['escola'] ?? 'Sem Escola') ?>
                                    <span class="badge badge-primary badge-pill"><?= $loc['total'] ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tipos de Avaria Mais Comuns -->
                    <?php if (!empty($tipos_avaria_comuns)): ?>
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-wrench"></i> Tipos de Avaria Mais Comuns</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($tipos_avaria_comuns as $tipo): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <small><?= esc($tipo['tipo_avaria'] ?? 'N/A') ?></small>
                                    <span class="badge badge-warning badge-pill"><?= $tipo['total'] ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Equipamentos Mais Problemáticos -->
                    <?php if (!empty($equipamentos_problematicos)): ?>
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-circle"></i> Equipamentos Problemáticos</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($equipamentos_problematicos as $equip): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <small><?= esc($equip['tipo_equipamento'] ?? 'N/A') ?></small>
                                    <span class="badge badge-danger badge-pill"><?= $equip['total'] ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

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
    // Gráfico de tickets resolvidos
    const ctx = document.getElementById('chartResolvidos').getContext('2d');
    const chartData = <?= json_encode($chart_data) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Tickets Resolvidos',
                data: chartData.data,
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
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
