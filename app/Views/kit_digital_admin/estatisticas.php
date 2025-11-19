<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>Estatísticas Kit Digital<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Estatísticas - Kit Digital</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('kit-digital-admin') ?>">Kit Digital</a></li>
                    <li class="breadcrumb-item active">Estatísticas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Por Ano -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pedidos por Ano de Escolaridade</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Ano</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($porAno)): ?>
                                    <?php foreach ($porAno as $row): ?>
                                        <tr>
                                            <td><?= esc($row['ano']) ?>º Ano</td>
                                            <td class="text-right"><strong><?= $row['total'] ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center text-muted">Sem dados</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Por Estado -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Pedidos por Estado</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Estado</th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($porEstado)): ?>
                                    <?php foreach ($porEstado as $row): ?>
                                        <tr>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'pendente' => '<span class="badge text-bg-warning">Pendente</span>',
                                                    'aprovado' => '<span class="badge text-bg-success">Aprovado</span>',
                                                    'por levantar' => '<span class="badge text-bg-info">Por Levantar</span>',
                                                    'rejeitado' => '<span class="badge text-bg-danger">Rejeitado</span>',
                                                    'anulado' => '<span class="badge text-bg-secondary">Anulado</span>',
                                                    'terminado' => '<span class="badge text-bg-dark">Terminado</span>'
                                                ];
                                                echo $badges[$row['estado']] ?? esc($row['estado']);
                                                ?>
                                            </td>
                                            <td class="text-right"><strong><?= $row['total'] ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="2" class="text-center text-muted">Sem dados</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
