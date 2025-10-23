<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
 
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                        <li class="breadcrumb-item active">Tipos de Equipamento</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= $page_subtitle ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tipoEquipamentoModal" onclick="openCreateModal()">
                                    <i class="fas fa-plus"></i> Novo Tipo de Equipamento
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="loadStatistics()">
                                    <i class="fas fa-chart-bar"></i> Estatísticas
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="tiposEquipamentoTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Descrição</th>
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
        </div>
    </section>
</div>

<!-- Modal para Tipo de Equipamento -->
<div class="modal fade" id="tipoEquipamentoModal" tabindex="-1" aria-labelledby="tipoEquipamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tipoEquipamentoModalLabel">Novo Tipo de Equipamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tipoEquipamentoForm">
                <div class="modal-body">
                    <input type="hidden" id="tipo_equipamento_id" name="tipo_equipamento_id">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Tipo de Equipamento <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="saveButton">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Visualizar Tipo de Equipamento -->
<div class="modal fade" id="viewTipoEquipamentoModal" tabindex="-1" aria-labelledby="viewTipoEquipamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTipoEquipamentoModalLabel">Detalhes do Tipo de Equipamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Nome:</strong>
                    <p id="view_tipo_nome"></p>
                </div>
                <div class="mb-3">
                    <strong>Descrição:</strong>
                    <p id="view_tipo_descricao"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Danger Modal de Confirmação -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminação</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Tem a certeza que deseja eliminar este tipo de equipamento?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#tiposEquipamentoTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?= base_url('tipos_equipamentos/getDataTable') ?>",
            "type": "POST"
        },
        "columns": [
            { "data": "nome" },
            { "data": "descricao" },
            {
                "data": null,
                "orderable": false,
                "render": function(data, type, row) {
                    return '<div class="btn-group" role="group">' +
                           '<button type="button" class="btn btn-sm btn-info" onclick="viewTipoEquipamento(' + row.id + ')" title="Ver">' +
                           '<i class="fas fa-eye"></i></button>' +
                           '<button type="button" class="btn btn-sm btn-warning" onclick="editTipoEquipamento(' + row.id + ')" title="Editar">' +
                           '<i class="fas fa-edit"></i></button>' +
                           '<button type="button" class="btn btn-sm btn-danger" onclick="deleteTipoEquipamento(' + row.id + ')" title="Eliminar">' +
                           '<i class="fas fa-trash"></i></button>' +
                           '</div>';
                }
            }
        ],
        "language": {
            "url": 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });

    // Submissão do formulário
    $('#tipoEquipamentoForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var tipoEquipamentoId = $('#tipo_equipamento_id').val();
        var url = tipoEquipamentoId ? 
            '<?= base_url('tipos_equipamentos/update') ?>/' + tipoEquipamentoId : 
            '<?= base_url('tipos_equipamentos/create') ?>';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#tipoEquipamentoModal').modal('hide');
                table.ajax.reload();
                showToast('success', response.message || 'Operação realizada com sucesso!');
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                if (response.messages) {
                    var errors = Object.values(response.messages).join('<br>');
                    showToast('error', errors);
                } else {
                    showToast('error', response.message || 'Erro ao processar a solicitação.');
                }
            }
        });
    });
});

function openCreateModal() {
    $('#tipoEquipamentoModalLabel').text('Novo Tipo de Equipamento');
    $('#tipoEquipamentoForm')[0].reset();
    $('#tipo_equipamento_id').val('');
    $('#saveButton').text('Guardar');
}

function editTipoEquipamento(id) {
    $.ajax({
        url: '<?= base_url('tipos_equipamentos/getTipoEquipamento') ?>/' + id,
        type: 'GET',
        success: function(data) {
            $('#tipoEquipamentoModalLabel').text('Editar Tipo de Equipamento');
            $('#tipo_equipamento_id').val(data.id);
            $('#nome').val(data.nome);
            $('#descricao').val(data.descricao);
            $('#saveButton').text('Atualizar');
            $('#tipoEquipamentoModal').modal('show');
        },
        error: function(xhr) {
            var response = JSON.parse(xhr.responseText);
            showToast('error', response.message || 'Erro ao carregar dados do tipo de equipamento.');
        }
    });
}

function viewTipoEquipamento(id) {
    $.ajax({
        url: '<?= base_url('tipos_equipamentos/getTipoEquipamento') ?>/' + id,
        type: 'GET',
        success: function(data) {
            $('#view_tipo_nome').text(data.nome);
            $('#view_tipo_descricao').text(data.descricao || 'Sem descrição');
            $('#viewTipoEquipamentoModal').modal('show');
        },
        error: function(xhr) {
            var response = JSON.parse(xhr.responseText);
            showToast('error', response.message || 'Erro ao carregar dados do tipo de equipamento.');
        }
    });
}

let tipoEquipamentoToDelete = null;

function deleteTipoEquipamento(id) {
    tipoEquipamentoToDelete = id;
    $('#confirmDeleteModal').modal('show');
}

$('#confirmDeleteBtn').on('click', function() {
    if (tipoEquipamentoToDelete) {
        $.ajax({
            url: '<?= base_url('tipos_equipamentos/delete') ?>/' + tipoEquipamentoToDelete,
            type: 'POST',
            success: function(response) {
                $('#tiposEquipamentoTable').DataTable().ajax.reload();
                showToast('success', response.message || 'Tipo de equipamento eliminado com sucesso!');
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                showToast('error', response.message || 'Erro ao eliminar tipo de equipamento.');
            },
            complete: function() {
                $('#confirmDeleteModal').modal('hide');
                tipoEquipamentoToDelete = null;
            }
        });
    }
});

function loadStatistics() {
    $.ajax({
        url: '<?= base_url('tipos_equipamentos/getStatistics') ?>',
        type: 'GET',
        success: function(data) {
            // Implementar a lógica para exibir as estatísticas dos tipos de equipamento
            // Por exemplo, em um modal ou em um card na página
            showToast('info', 'Total de Tipos de Equipamento: ' + data.total_tipos);
        },
        error: function(xhr) {
            console.error('Erro ao carregar estatísticas de tipos de equipamento:', xhr);
        }
    });
}

function showToast(type, message) {
    if (type === 'success') {
        toastr.success(message);
    } else if (type === 'info') {
        toastr.info(message);
    } else if (type === 'warning') {
        toastr.warning(message);
    } else {
        toastr.error(message);
    }
}
</script>
<?= $this->endSection() ?>

