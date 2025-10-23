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
                        <li class="breadcrumb-item active">Blocos Horários</li>
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
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalBloco">
                            <i class="bi bi-plus-circle"></i> Novo Bloco
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableBlocos" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Dia da Semana</th>
                                <th>Designação</th>
                                <th>Hora Início</th>
                                <th>Hora Fim</th>
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
<!-- Modal Bloco -->
<div class="modal fade" id="modalBloco" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBlocoTitle">Novo Bloco Horário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBloco">
                <input type="hidden" id="bloco_id" name="id_bloco">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="dia_semana">Dia da Semana *</label>
                        <select class="form-control" id="dia_semana" name="dia_semana" required>
                            <option value="">Selecione...</option>
                            <option value="Segunda_Feira">Segunda-Feira</option>
                            <option value="Terca_Feira">Terça-Feira</option>
                            <option value="Quarta_Feira">Quarta-Feira</option>
                            <option value="Quinta_Feira">Quinta-Feira</option>
                            <option value="Sexta_Feira">Sexta-Feira</option>
                            <option value="Sabado">Sábado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="designacao">Designação *</label>
                        <input type="text" class="form-control" id="designacao" name="designacao" required maxlength="50">
                        <small class="form-text text-muted">Ex: 1º Bloco, 2º Bloco, etc.</small>
                    </div>
                    <div class="form-group">
                        <label for="hora_inicio">Hora Início *</label>
                        <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                    </div>
                    <div class="form-group">
                        <label for="hora_fim">Hora Fim *</label>
                        <input type="time" class="form-control" id="hora_fim" name="hora_fim" required>
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
    var table = $('#tableBlocos').DataTable({
        ajax: {
            url: '<?= base_url('blocos/getDataTable') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_bloco' },
            { data: 'dia_semana_formatado' },
            { data: 'designacao' },
            { data: 'hora_inicio' },
            { data: 'hora_fim' },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info btn-edit" data-id="${row.id_bloco}">`
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id_bloco}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        order: [[1, 'asc'], [3, 'asc']],
        responsive: true
    });

    // Criar/Atualizar Bloco
    $('#formBloco').on('submit', function(e) {
        e.preventDefault();
        var id = $('#bloco_id').val();
        var url = id ? '<?= base_url('blocos/update') ?>/' + id : '<?= base_url('blocos/create') ?>';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalBloco'));
                    if(modal) modal.hide();
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Editar Bloco
    $('#tableBlocos').on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        
        $.get('<?= base_url('blocos/get') ?>/' + id, function(data) {
            $('#bloco_id').val(data.id_bloco);
            $('#dia_semana').val(data.dia_semana);
            $('#designacao').val(data.designacao);
            $('#hora_inicio').val(data.hora_inicio);
            $('#hora_fim').val(data.hora_fim);
            $('#modalBlocoTitle').text('Editar Bloco Horário');
            var modal = new bootstrap.Modal(document.getElementById('modalBloco'));
            modal.show();
        });
    });

    // Eliminar Bloco
    $('#tableBlocos').on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        $('#deleteBlocoId').val(id);
        var modalDelete = new bootstrap.Modal(document.getElementById('modalDeleteBloco'));
        modalDelete.show();
    });

    // Confirmar eliminação
    $('#btnConfirmDeleteBloco').on('click', function() {
        var id = $('#deleteBlocoId').val();
        
        $.ajax({
            url: '<?= base_url('blocos/delete') ?>/' + id,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteBloco'));
                modalDelete.hide();
                
                if(response.success) {
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteBloco'));
                modalDelete.hide();
                toastr.error('Erro ao eliminar bloco horário');
            }
        });
    });

    // Limpar form ao fechar modal
    $('#modalBloco').on('hidden.bs.modal', function() {
        $('#formBloco')[0].reset();
        $('#bloco_id').val('');
        $('#modalBlocoTitle').text('Novo Bloco Horário');
    });
});
</script>

<!-- Modal Confirmação de Eliminação -->
<div class="modal fade" id="modalDeleteBloco" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Tem a certeza que deseja eliminar este bloco horário?</p>
                <p class="text-muted small mb-0">Esta ação não pode ser revertida.</p>
                <input type="hidden" id="deleteBlocoId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteBloco">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


