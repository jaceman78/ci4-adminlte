<?= $this->extend('layout/master') ?>
<?= $this->section('pageHeader') ?>
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $page_title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item active">Anos Letivos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $page_subtitle ?></h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAnoLetivo">
                            <i class="bi bi-plus-circle"></i> Novo Ano Letivo
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableAnosLetivos" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ano Letivo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Modal Ano Letivo -->
<div class="modal fade" id="modalAnoLetivo" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAnoLetivoTitle">Novo Ano Letivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAnoLetivo">
                <input type="hidden" id="anoletivo_id" name="id_anoletivo">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="anoletivo">Ano Letivo *</label>
                        <input type="number" class="form-control" id="anoletivo" name="anoletivo" required min="2000" max="2100">
                        <small class="form-text text-muted">Ex: 2024 para o ano letivo 2024/2025</small>
                    </div>
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                        <small class="form-text text-muted">Apenas um ano letivo pode estar ativo de cada vez</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação para Ativar Ano Letivo -->
<div class="modal fade" id="confirmAtivarModal" tabindex="-1" aria-labelledby="confirmAtivarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-warning">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="confirmAtivarModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar Ativação
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Tem a certeza que deseja ativar este ano letivo?</strong></p>
                <p class="text-muted">O ano letivo atualmente ativo será desativado.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="confirmAtivarBtn">
                    <i class="bi bi-check-circle"></i> Confirmar Ativação
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#tableAnosLetivos').DataTable({
        ajax: {
            url: '<?= base_url('anos-letivos/getDataTable') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_anoletivo' },
            { 
                data: 'anoletivo',
                render: function(data) {
                    return data + '/' + (parseInt(data) + 1);
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    return data == 1 
                        ? '<span class="badge bg-success text-white">Ativo</span>'
                        : '<span class="badge bg-secondary text-white">Inativo</span>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    var buttons = `
                        <button class="btn btn-sm btn-info btn-edit" data-id="${row.id_anoletivo}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id_anoletivo}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                    
                    if(row.status == 0) {
                        buttons += `
                            <button class="btn btn-sm btn-success btn-ativar" data-id="${row.id_anoletivo}">
                                <i class="bi bi-check-circle"></i> Ativar
                            </button>
                        `;
                    }
                    
                    return buttons;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        order: [[1, 'desc']],
        responsive: true
    });

    // Criar/Atualizar Ano Letivo
    $('#formAnoLetivo').on('submit', function(e) {
        e.preventDefault();
        var id = $('#anoletivo_id').val();
        var url = id ? '<?= base_url('anos-letivos/update') ?>/' + id : '<?= base_url('anos-letivos/create') ?>';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalAnoLetivo'));
                    if(modal) modal.hide();
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Editar Ano Letivo
    $('#tableAnosLetivos').on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        
        $.get('<?= base_url('anos-letivos/get') ?>/' + id, function(data) {
            $('#anoletivo_id').val(data.id_anoletivo);
            $('#anoletivo').val(data.anoletivo);
            $('#status').val(data.status);
            $('#modalAnoLetivoTitle').text('Editar Ano Letivo');
            var modal = new bootstrap.Modal(document.getElementById('modalAnoLetivo'));
            modal.show();
        });
    });

    // Eliminar Ano Letivo
    $('#tableAnosLetivos').on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        $('#deleteAnoLetivoId').val(id);
        var modalDelete = new bootstrap.Modal(document.getElementById('modalDeleteAnoLetivo'));
        modalDelete.show();
    });

    // Confirmar eliminação
    $('#btnConfirmDeleteAnoLetivo').on('click', function() {
        var id = $('#deleteAnoLetivoId').val();
        
        $.ajax({
            url: '<?= base_url('anos-letivos/delete') ?>/' + id,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteAnoLetivo'));
                modalDelete.hide();
                
                if(response.success) {
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteAnoLetivo'));
                modalDelete.hide();
                toastr.error('Erro ao eliminar ano letivo');
            }
        });
    });

    // Variável para guardar ID do ano letivo a ativar
    let anoLetivoToActivate = null;

    // Ativar Ano Letivo
    $('#tableAnosLetivos').on('click', '.btn-ativar', function() {
        anoLetivoToActivate = $(this).data('id');
        var modal = new bootstrap.Modal(document.getElementById('confirmAtivarModal'));
        modal.show();
    });

    // Confirmar ativação
    $('#confirmAtivarBtn').on('click', function() {
        if (anoLetivoToActivate) {
            $.ajax({
                url: '<?= base_url('anos-letivos/ativar') ?>/' + anoLetivoToActivate,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        table.ajax.reload();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Erro ao ativar ano letivo');
                },
                complete: function() {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('confirmAtivarModal'));
                    if(modal) modal.hide();
                    anoLetivoToActivate = null;
                }
            });
        }
    });

    // Limpar form ao fechar modal
    $('#modalAnoLetivo').on('hidden.bs.modal', function() {
        $('#formAnoLetivo')[0].reset();
        $('#anoletivo_id').val('');
        $('#modalAnoLetivoTitle').text('Novo Ano Letivo');
    });
});
</script>

<!-- Modal Confirmação de Eliminação -->
<div class="modal fade" id="modalDeleteAnoLetivo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Tem a certeza que deseja eliminar este ano letivo?</p>
                <p class="text-muted small mb-0">Esta ação não pode ser revertida.</p>
                <input type="hidden" id="deleteAnoLetivoId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteAnoLetivo">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

