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
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Bem-vindo, <?= esc($user['name']) ?></h3>
                        </div>
                        <div class="card-body">
                            
                            <!-- Cards de Estatísticas -->
                            <div class="row">
                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3><?= $stats['tickets_ativos'] ?></h3>
                                            <p>Meus Tickets Ativos</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <a href="<?= site_url('tickets/meus') ?>" class="small-box-footer">
                                            Ver Tickets <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3><?= $stats['tickets_pendentes'] ?></h3>
                                            <p>Tickets Pendentes</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <a href="<?= site_url('tickets/meus') ?>" class="small-box-footer">
                                            Ver Detalhes <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>

                                <div class="col-lg-4 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3><?= $stats['tickets_resolvidos'] ?></h3>
                                            <p>Tickets Resolvidos</p>
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

                            <!-- Ações Rápidas -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <div class="card card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-bolt"></i> Ações Rápidas</h3>
                                        </div>
                                        <div class="card-body">
                                            <a href="<?= site_url('tickets/novo') ?>" class="btn btn-primary btn-block mb-2">
                                                <i class="fas fa-plus"></i> Criar Ticket de Avaria
                                            </a>
                                            <a href="<?= site_url('tickets/meus') ?>" class="btn btn-info btn-block mb-2">
                                                <i class="fas fa-list"></i> Ver Meus Tickets
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Meus Tickets Recentes -->
                                <div class="col-md-6">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h3 class="card-title"><i class="fas fa-ticket-alt"></i> Tickets Recentes</h3>
                                        </div>
                                        <div class="card-body p-0">
                                            <?php if (empty($tickets)): ?>
                                            <div class="text-center p-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Nenhum ticket criado ainda</p>
                                            </div>
                                            <?php else: ?>
                                            <ul class="list-group list-group-flush">
                                                <?php foreach ($tickets as $ticket): ?>
                                                <li class="list-group-item">
                                                    <a href="<?= site_url('tickets/view/' . $ticket['id']) ?>">
                                                        <strong>#<?= $ticket['id'] ?></strong> - <?= esc(substr($ticket['descricao'], 0, 40)) ?>...
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">
                                                        Estado: <span class="badge badge-<?= $ticket['estado'] == 'novo' ? 'primary' : 'warning' ?>">
                                                            <?= ucfirst($ticket['estado']) ?>
                                                        </span>
                                                    </small>
                                                </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
<?= $this->endSection() ?>
