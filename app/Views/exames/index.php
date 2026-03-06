<?= $this->extend('layout/master') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Exames/Provas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Gestão de Exames e Provas Oficiais</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" onclick="novoExame()">
                        <i class="bi bi-plus-circle"></i> Novo Exame
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="examesTable" class="table table-bordered table-striped table-hover nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th class="d-none">ID</th>
                            <th>Código</th>
                            <th>Nome da Prova</th>
                            <th>Tipo</th>
                            <th>Ano Escolaridade</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Criar/Editar Exame -->
<div class="modal fade" id="exameModal" tabindex="-1" aria-labelledby="exameModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exameModalLabel">Novo Exame</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="exameForm">
                <div class="modal-body">
                    <input type="hidden" id="exameId" name="id">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="codigoProva" class="form-label">Código da Prova <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="codigoProva" name="codigo_prova" placeholder="Ex: 639" required>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nomeProva" class="form-label">Nome da Prova <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nomeProva" name="nome_prova" placeholder="Ex: Português" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipoProva" class="form-label">Tipo de Prova <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipoProva" name="tipo_prova" required>
                                    <option value="">Selecione...</option>
                                    <option value="Exame Nacional">Exame Nacional</option>
                                    <option value="Prova Final">Prova Final</option>
                                    <option value="MODa">MODa</option>
                                    <option value="Apoio TIC">Apoio TIC</option>
                                    <option value="Verificacao Calculadoras">Verificação Calculadoras</option>
                                    <option value="Suplentes">Suplentes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="anoEscolaridade" class="form-label">Ano de Escolaridade <span class="text-danger">*</span></label>
                                <select class="form-select" id="anoEscolaridade" name="ano_escolaridade" required>
                                    <option value="">Selecione...</option>
                                    <option value="4">4º ano</option>
                                    <option value="6">6º ano</option>
                                    <option value="9">9º ano</option>
                                    <option value="11">11º ano</option>
                                    <option value="12">12º ano</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ativo" class="form-label">Estado</label>
                                <select class="form-select" id="ativo" name="ativo">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#examesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('exames/getDataTable') ?>',
            type: 'POST',
            data: function (d) {
                d['X-Requested-With'] = 'XMLHttpRequest'; 
                return d;
            }
        },
        columns: [
            { data: 0, name: 'id' },
            { data: 1, name: 'codigo_prova' },
            { data: 2, name: 'nome_prova' },
            { data: 3, name: 'tipo_prova' },
            { data: 4, name: 'ano_escolaridade' },
            { data: 5, name: 'ativo' },
            { data: 6, name: 'actions', orderable: false, searchable: false }
        ],
        columnDefs: [
            { targets: 0, visible: false, searchable: false }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']]
    });

    // Form submission
    $('#exameForm').on('submit', function(e) {
        e.preventDefault();
        
        const exameId = $('#exameId').val();
        const url = exameId ? '<?= base_url('exames/update') ?>/' + exameId : '<?= base_url('exames/store') ?>';
        const formData = new FormData(this);

        $('#btnGuardar').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Guardando...');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#exameModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Sucesso!', response.message, 'success');
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Ocorreu um erro ao guardar', 'error');
            },
            complete: function() {
                $('#btnGuardar').prop('disabled', false).html('<i class="bi bi-save"></i> Guardar');
            }
        });
    });

    // Reset modal on close
    $('#exameModal').on('hidden.bs.modal', function() {
        $('#exameForm')[0].reset();
        $('#exameId').val('');
        $('#exameModalLabel').text('Novo Exame');
    });
});

function novoExame() {
    $('#exameForm')[0].reset();
    $('#exameId').val('');
    $('#exameModalLabel').text('Novo Exame');
    $('#exameModal').modal('show');
}

function editExame(id) {
    $.ajax({
        url: '<?= base_url('exames/get') ?>/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const exame = response.data;
                $('#exameId').val(exame.id);
                $('#codigoProva').val(exame.codigo_prova);
                $('#nomeProva').val(exame.nome_prova);
                $('#tipoProva').val(exame.tipo_prova);
                $('#anoEscolaridade').val(exame.ano_escolaridade);
                $('#ativo').val(exame.ativo);
                $('#exameModalLabel').text('Editar Exame');
                $('#exameModal').modal('show');
            }
        }
    });
}

function deleteExame(id) {
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
                url: '<?= base_url('exames/delete') ?>/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        $('#examesTable').DataTable().ajax.reload();
                        Swal.fire('Eliminado!', response.message, 'success');
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
