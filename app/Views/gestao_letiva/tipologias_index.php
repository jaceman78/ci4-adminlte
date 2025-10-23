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
                        <li class="breadcrumb-item active">Tipologias</li>
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
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTipologia">
                            <i class="bi bi-plus-circle"></i> Nova Tipologia
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableTipologias" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
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
<!-- Modal Tipologia -->
<div class="modal fade" id="modalTipologia" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTipologiaTitle">Nova Tipologia</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTipologia">
                <input type="hidden" id="tipologia_id" name="id_tipologia">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nome_tipologia">Nome da Tipologia *</label>
                        <input type="text" class="form-control" id="nome_tipologia" name="nome_tipologia" required maxlength="100">
                    </div>
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
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

<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#tableTipologias').DataTable({
        ajax: {
            url: '<?= base_url('tipologias/getDataTable') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_tipologia' },
            { data: 'nome_tipologia' },
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
                    return `
                        <button class="btn btn-sm btn-info btn-edit" data-id="${row.id_tipologia}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id_tipologia}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        responsive: true
    });

    // Criar/Atualizar Tipologia
    $('#formTipologia').on('submit', function(e) {
        e.preventDefault();
        var id = $('#tipologia_id').val();
        var url = id ? '<?= base_url('tipologias/update') ?>/' + id : '<?= base_url('tipologias/create') ?>';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalTipologia'));
                    if(modal) modal.hide();
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Editar Tipologia
    $('#tableTipologias').on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        
        $.get('<?= base_url('tipologias/get') ?>/' + id, function(data) {
            $('#tipologia_id').val(data.id_tipologia);
            $('#nome_tipologia').val(data.nome_tipologia);
            $('#status').val(data.status);
            $('#modalTipologiaTitle').text('Editar Tipologia');
            var modal = new bootstrap.Modal(document.getElementById('modalTipologia'));
            modal.show();
        });
    });

    // Eliminar Tipologia
    $('#tableTipologias').on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        $('#deleteTipologiaId').val(id);
        var modalDelete = new bootstrap.Modal(document.getElementById('modalDeleteTipologia'));
        modalDelete.show();
    });

    // Confirmar eliminação
    $('#btnConfirmDeleteTipologia').on('click', function() {
        var id = $('#deleteTipologiaId').val();
        
        $.ajax({
            url: '<?= base_url('tipologias/delete') ?>/' + id,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteTipologia'));
                modalDelete.hide();
                
                if(response.success) {
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteTipologia'));
                modalDelete.hide();
                toastr.error('Erro ao eliminar tipologia');
            }
        });
    });

    // Limpar form ao fechar modal
    $('#modalTipologia').on('hidden.bs.modal', function() {
        $('#formTipologia')[0].reset();
        $('#tipologia_id').val('');
        $('#modalTipologiaTitle').text('Nova Tipologia');
    });
});
</script>

<!-- Modal Confirmação de Eliminação -->
<div class="modal fade" id="modalDeleteTipologia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Tem a certeza que deseja eliminar esta tipologia?</p>
                <p class="text-muted small mb-0">Esta ação não pode ser revertida.</p>
                <input type="hidden" id="deleteTipologiaId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteTipologia">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


