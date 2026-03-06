<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>QR Code - <?= $equipamento['n_serie'] ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="bi bi-qr-code"></i> QR Code do Equipamento</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('inutilizados-kitdigital') ?>">Equipamentos Inutilizados</a></li>
                    <li class="breadcrumb-item active">QR Code</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-pc-display-horizontal"></i> 
                            <?= esc($equipamento['n_serie']) ?>
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <h5><?= esc($equipamento['marca']) ?> 
                                <?= $equipamento['modelo'] ? '- ' . esc($equipamento['modelo']) : '' ?>
                            </h5>
                            <p class="text-muted">ID: <?= $equipamento['id'] ?></p>
                        </div>

                        <?php if ($equipamento['qr_code']): ?>
                            <div class="mb-4">
                                <img src="<?= $qrcode_path ?>" alt="QR Code" class="img-fluid border p-3" style="max-width: 400px;">
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> QR Code não disponível
                            </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <h6 class="text-start mb-3"><i class="bi bi-cpu"></i> Componentes Disponíveis:</h6>
                            <div class="row text-start">
                                <div class="col-md-6 mb-2">
                                    <i class="bi bi-memory"></i> RAM: 
                                    <strong class="<?= $equipamento['ram'] ? 'text-success' : 'text-danger' ?>">
                                        <?= $equipamento['ram'] ? 'Disponível' : 'Utilizado' ?>
                                    </strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="bi bi-device-hdd"></i> Disco: 
                                    <strong class="<?= $equipamento['disco'] ? 'text-success' : 'text-danger' ?>">
                                        <?= $equipamento['disco'] ? 'Disponível' : 'Utilizado' ?>
                                    </strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="bi bi-keyboard"></i> Teclado: 
                                    <strong class="<?= $equipamento['teclado'] ? 'text-success' : 'text-danger' ?>">
                                        <?= $equipamento['teclado'] ? 'Disponível' : 'Utilizado' ?>
                                    </strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="bi bi-display"></i> Ecrã: 
                                    <strong class="<?= $equipamento['ecra'] ? 'text-success' : 'text-danger' ?>">
                                        <?= $equipamento['ecra'] ? 'Disponível' : 'Utilizado' ?>
                                    </strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="bi bi-battery-charging"></i> Bateria: 
                                    <strong class="<?= $equipamento['bateria'] ? 'text-success' : 'text-danger' ?>">
                                        <?= $equipamento['bateria'] ? 'Disponível' : 'Utilizado' ?>
                                    </strong>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <i class="bi bi-box"></i> Caixa: 
                                    <strong class="<?= $equipamento['caixa'] ? 'text-success' : 'text-danger' ?>">
                                        <?= $equipamento['caixa'] ? 'Disponível' : 'Utilizado' ?>
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <?php if ($equipamento['outros']): ?>
                            <div class="alert alert-info text-start">
                                <strong>Outros Componentes:</strong><br>
                                <?= nl2br(esc($equipamento['outros'])) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($equipamento['observacoes']): ?>
                            <div class="alert alert-secondary text-start">
                                <strong>Observações:</strong><br>
                                <?= nl2br(esc($equipamento['observacoes'])) ?>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <a href="<?= base_url('inutilizados-kitdigital') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <?php if ($equipamento['qr_code']): ?>
                                <a href="<?= base_url('inutilizados-kitdigital/downloadQRCode/' . $equipamento['id']) ?>" 
                                   class="btn btn-primary" target="_blank">
                                    <i class="bi bi-download"></i> Download QR Code
                                </a>
                                <button onclick="window.print()" class="btn btn-info">
                                    <i class="bi bi-printer"></i> Imprimir
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
@media print {
    .content-header,
    .btn,
    .breadcrumb,
    nav,
    aside,
    footer {
        display: none !important;
    }
    
    .card {
        border: none;
        box-shadow: none;
    }
    
    img {
        max-width: 100% !important;
    }
}
</style>
<?= $this->endSection() ?>
