<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>Detalhes - <?= $equipamento['n_serie'] ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="bi bi-pc-display-horizontal"></i> Detalhes do Equipamento</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('inutilizados-kitdigital') ?>">Equipamentos Inutilizados</a></li>
                    <li class="breadcrumb-item active">Detalhes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-info-circle"></i> Informações do Equipamento
                        </h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">ID:</dt>
                            <dd class="col-sm-8"><?= esc($equipamento['id']) ?></dd>

                            <dt class="col-sm-4">Número de Série:</dt>
                            <dd class="col-sm-8"><strong><?= esc($equipamento['n_serie']) ?></strong></dd>

                            <dt class="col-sm-4">Marca:</dt>
                            <dd class="col-sm-8"><?= esc($equipamento['marca']) ?></dd>

                            <dt class="col-sm-4">Modelo:</dt>
                            <dd class="col-sm-8"><?= $equipamento['modelo'] ? esc($equipamento['modelo']) : '<em class="text-muted">Não especificado</em>' ?></dd>

                            <dt class="col-sm-4">Estado:</dt>
                            <dd class="col-sm-8">
                                <?php
                                $badges = [
                                    'ativo' => '<span class="badge bg-success">Ativo</span>',
                                    'esgotado' => '<span class="badge bg-warning">Esgotado</span>',
                                    'descartado' => '<span class="badge bg-secondary">Descartado</span>'
                                ];
                                echo $badges[$equipamento['estado']] ?? $equipamento['estado'];
                                ?>
                            </dd>

                            <dt class="col-sm-4">Data de Registo:</dt>
                            <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($equipamento['created_at'])) ?></dd>

                            <?php if ($equipamento['updated_at']): ?>
                            <dt class="col-sm-4">Última Atualização:</dt>
                            <dd class="col-sm-8"><?= date('d/m/Y H:i', strtotime($equipamento['updated_at'])) ?></dd>
                            <?php endif; ?>
                        </dl>

                        <hr>

                        <h5 class="mb-3"><i class="bi bi-cpu"></i> Componentes</h5>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-memory fs-4 me-2"></i>
                                    <div>
                                        <strong>RAM</strong><br>
                                        <span class="<?= $equipamento['ram'] ? 'text-success' : 'text-danger' ?>">
                                            <?= $equipamento['ram'] ? 'Disponível' : 'Já utilizado' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-device-hdd fs-4 me-2"></i>
                                    <div>
                                        <strong>Disco</strong><br>
                                        <span class="<?= $equipamento['disco'] ? 'text-success' : 'text-danger' ?>">
                                            <?= $equipamento['disco'] ? 'Disponível' : 'Já utilizado' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-keyboard fs-4 me-2"></i>
                                    <div>
                                        <strong>Teclado</strong><br>
                                        <span class="<?= $equipamento['teclado'] ? 'text-success' : 'text-danger' ?>">
                                            <?= $equipamento['teclado'] ? 'Disponível' : 'Já utilizado' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-display fs-4 me-2"></i>
                                    <div>
                                        <strong>Ecrã</strong><br>
                                        <span class="<?= $equipamento['ecra'] ? 'text-success' : 'text-danger' ?>">
                                            <?= $equipamento['ecra'] ? 'Disponível' : 'Já utilizado' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-battery-charging fs-4 me-2"></i>
                                    <div>
                                        <strong>Bateria</strong><br>
                                        <span class="<?= $equipamento['bateria'] ? 'text-success' : 'text-danger' ?>">
                                            <?= $equipamento['bateria'] ? 'Disponível' : 'Já utilizado' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-box fs-4 me-2"></i>
                                    <div>
                                        <strong>Caixa</strong><br>
                                        <span class="<?= $equipamento['caixa'] ? 'text-success' : 'text-danger' ?>">
                                            <?= $equipamento['caixa'] ? 'Disponível' : 'Já utilizado' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($equipamento['outros']): ?>
                        <div class="alert alert-info">
                            <strong><i class="bi bi-plus-circle"></i> Outros Componentes:</strong><br>
                            <?= nl2br(esc($equipamento['outros'])) ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($equipamento['observacoes']): ?>
                        <div class="alert alert-secondary">
                            <strong><i class="bi bi-chat-left-text"></i> Observações:</strong><br>
                            <?= nl2br(esc($equipamento['observacoes'])) ?>
                        </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <a href="<?= base_url('inutilizados-kitdigital') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar à Listagem
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-qr-code"></i> QR Code
                        </h3>
                    </div>
                    <div class="card-body text-center">
                        <img src="<?= base_url('inutilizados-kitdigital/getQRCode/' . $equipamento['id']) ?>" 
                             alt="QR Code" 
                             class="img-fluid border p-2 mb-3"
                             style="max-width: 250px;">
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="verQRCodeAmpliado()">
                                <i class="bi bi-eye"></i> Ver QR Code
                            </button>
                            <a href="<?= base_url('inutilizados-kitdigital/getQRCode/' . $equipamento['id']) ?>" 
                               class="btn btn-success" 
                               download="qrcode_<?= $equipamento['n_serie'] ?>.png">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-lightning"></i> Ações Rápidas
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" onclick="editarEquipamento(<?= $equipamento['id'] ?>)">
                                <i class="bi bi-pencil"></i> Editar
                            </button>
                            <button class="btn btn-outline-danger" onclick="eliminarEquipamento(<?= $equipamento['id'] ?>)">
                                <i class="bi bi-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal: QR Code Ampliado -->
<div class="modal fade" id="modalQRCode" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-qr-code"></i> QR Code - <?= esc($equipamento['n_serie']) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="<?= base_url('inutilizados-kitdigital/getQRCode/' . $equipamento['id']) ?>" 
                     alt="QR Code" 
                     class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="<?= base_url('inutilizados-kitdigital/getQRCode/' . $equipamento['id']) ?>" 
                   class="btn btn-success" 
                   download="qrcode_<?= $equipamento['n_serie'] ?>.png">
                    <i class="bi bi-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function verQRCodeAmpliado() {
    $('#modalQRCode').modal('show');
}

function editarEquipamento(id) {
    window.location.href = '<?= base_url('inutilizados-kitdigital') ?>?edit=' + id;
}

function eliminarEquipamento(id) {
    Swal.fire({
        title: 'Tem a certeza?',
        text: "Esta ação não pode ser revertida!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('inutilizados-kitdigital/delete') ?>/' + id,
                method: 'POST',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Eliminado!', response.message, 'success').then(() => {
                            window.location.href = '<?= base_url('inutilizados-kitdigital') ?>';
                        });
                    } else {
                        Swal.fire('Erro!', response.message, 'error');
                    }
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
