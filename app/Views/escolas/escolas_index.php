<?= $this->extend('layout/master') ?>
<?= $this->section('pageHeader') ?>
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Gestão de Escolas</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Escolas</li>
        </ol>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Escolas</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
                        <i class="fas fa-plus"></i> Nova Escola
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportCSV()">
                        <i class="fas fa-download"></i> Exportar CSV
                    </button>
                    <button type="button" class="btn btn-info btn-sm" onclick="getStats()">
                        <i class="fas fa-chart-bar"></i> Estatísticas
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="escolasTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Morada</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados carregados via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Criar/Editar Escola -->
<div class="modal fade" id="escolaModal" tabindex="-1" aria-labelledby="escolaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="escolaModalLabel">Nova Escola</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="escolaForm">
                <div class="modal-body">
                    <input type="hidden" id="escolaId" name="id">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="escolaNome" class="form-label">Nome da Escola <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="escolaNome" name="nome" placeholder="Nome da escola" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="escolaMorada" class="form-label">Morada</label>
                                <textarea class="form-control" id="escolaMorada" name="morada" rows="3" placeholder="Morada completa da escola"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="saveEscolaBtn">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Escola -->
<div class="modal fade" id="viewEscolaModal" tabindex="-1" aria-labelledby="viewEscolaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEscolaModalLabel">Detalhes da Escola</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td id="viewEscolaId"></td>
                            </tr>
                            <tr>
                                <td><strong>Nome:</strong></td>
                                <td id="viewEscolaNome"></td>
                            </tr>
                            <tr>
                                <td><strong>Morada:</strong></td>
                                <td id="viewEscolaMorada"></td>
                            </tr>
                            <tr>
                                <td><strong>Data Criação:</strong></td>
                                <td id="viewEscolaCreated"></td>
                            </tr>
                            <tr>
                                <td><strong>Última Atualização:</strong></td>
                                <td id="viewEscolaUpdated"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="editEscolaFromView()">
                    <i class="fas fa-edit"></i> Editar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Estatísticas -->
<div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statsModalLabel">Estatísticas das Escolas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-school"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total de Escolas</span>
                                <span class="info-box-number" id="statsTotal">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-map-marker-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Com Morada</span>
                                <span class="info-box-number" id="statsComMorada">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Sem Morada</span>
                                <span class="info-box-number" id="statsSemMorada">0</span>
                            </div>
                        </div>
                    </div>
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
        Tem a certeza que deseja eliminar esta escola?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#escolasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('escolas/getDataTable') ?>',
            type: 'POST',
            data: function (d) {
                // Adiciona o cabeçalho X-Requested-With para que o CodeIgniter reconheça como AJAX
                d['X-Requested-With'] = 'XMLHttpRequest'; 
                return d;
            }
        },
        columns: [
            { data: 0, name: 'id' },
            { data: 1, name: 'nome' },
            { data: 2, name: 'morada', orderable: false },
            { data: 3, name: 'created_at' },
            { data: 4, name: 'actions', orderable: false, searchable: false }
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
    $('#escolaForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var escolaId = $('#escolaId').val();
        var url = escolaId ? '<?= base_url('escolas/update') ?>/' + escolaId : '<?= base_url('escolas/create') ?>';
        var method = 'POST';

        // Desabilitar botão de submit
        $('#saveEscolaBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> A guardar...');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#escolaModal').modal('hide');
                    table.ajax.reload();
                    resetForm();
                } else {
                    showToast('error', response.message);
                    if (response.errors) {
                        displayFormErrors(response.errors);
                    }
                }
            },
            error: function(xhr) {
                showToast('error', 'Erro ao processar pedido');
                console.error(xhr.responseText);
            },
            complete: function() {
                $('#saveEscolaBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });
});

// Função para abrir modal de criação
function openCreateModal() {
    resetForm();
    $('#escolaModalLabel').text('Nova Escola');
    $('#escolaModal').modal('show');
}

// Função para editar escola
function editEscola(id) {
    $.ajax({
        url: '<?= base_url('escolas/getEscola') ?>/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var escola = response.data;
                
                $('#escolaId').val(escola.id);
                $('#escolaNome').val(escola.nome);
                $('#escolaMorada').val(escola.morada);
                
                $('#escolaModalLabel').text('Editar Escola');
                $('#escolaModal').modal('show');
            } else {
                showToast('error', response.message || 'Erro ao carregar dados da escola');
            }
        },
        error: function() {
            showToast('error', 'Erro ao carregar dados da escola');
        }
    });
}

// Função para ver escola
function viewEscola(id) {
    $.ajax({
        url: '<?= base_url('escolas/getEscola') ?>/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var escola = response.data;
                
                $('#viewEscolaId').text(escola.id);
                $('#viewEscolaNome').text(escola.nome);
                $('#viewEscolaMorada').text(escola.morada || 'Não definida');
                $('#viewEscolaCreated').text(formatDate(escola.created_at));
                $('#viewEscolaUpdated').text(escola.updated_at ? formatDate(escola.updated_at) : 'N/A');
                
                // Guardar ID para possível edição
                $('#viewEscolaModal').data('escola-id', escola.id);
                
                $('#viewEscolaModal').modal('show');
            } else {
                showToast('error', response.message || 'Erro ao carregar dados da escola');
            }
        },
        error: function() {
            showToast('error', 'Erro ao carregar dados da escola');
        }
    });
}

// Função para editar escola a partir do modal de visualização
function editEscolaFromView() {
    var escolaId = $('#viewEscolaModal').data('escola-id');
    $('#viewEscolaModal').modal('hide');
    
    // Pequeno delay para evitar conflito entre modais
    setTimeout(function() {
        editEscola(escolaId);
    }, 300);
}

// Variável para guardar ID da escola a eliminar
let escolaToDelete = null;

// Função para eliminar escola
function deleteEscola(id) {
    escolaToDelete = id;
    $('#confirmDeleteModal').modal('show');
}

$('#confirmDeleteBtn').on('click', function() {
    if (escolaToDelete) {
        $.ajax({
            url: '<?= base_url('escolas/delete') ?>/' + escolaToDelete,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#escolasTable').DataTable().ajax.reload();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Erro ao eliminar escola');
            },
            complete: function() {
                $('#confirmDeleteModal').modal('hide');
                escolaToDelete = null;
            }
        });
    }
});

// Função para exportar CSV
function exportCSV() {
    window.location.href = '<?= base_url('escolas/exportCSV') ?>';
}

// Função para obter estatísticas
function getStats() {
    $.ajax({
        url: '<?= base_url('escolas/getStats') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var stats = response.data;
                
                $('#statsTotal').text(stats.total);
                $('#statsComMorada').text(stats.com_morada);
                $('#statsSemMorada').text(stats.sem_morada);
                
                $('#statsModal').modal('show');
            } else {
                showToast('error', response.message || 'Erro ao carregar estatísticas');
            }
        },
        error: function() {
            showToast('error', 'Erro ao carregar estatísticas');
        }
    });
}

// Função para resetar formulário
function resetForm() {
    $('#escolaForm')[0].reset();
    $('#escolaId').val('');
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

// Função para mostrar erros do formulário
function displayFormErrors(errors) {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    
    for (var field in errors) {
        var input = $('[name="' + field + '"]');
        input.addClass('is-invalid');
        input.siblings('.invalid-feedback').text(errors[field]);
    }
}

// Função para mostrar toasts
function showToast(type, message) {
    var bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    var icon = type === 'success' ? 'fas fa-check' : 'fas fa-exclamation-triangle';
    
    var toast = `
        <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="${icon}"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Criar container de toasts se não existir
    if ($('#toastContainer').length === 0) {
        $('body').append('<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }
    
    var $toast = $(toast);
    $('#toastContainer').append($toast);
    
    var bsToast = new bootstrap.Toast($toast[0]);
    bsToast.show();
    
    // Remover toast após ser escondido
    $toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

// Função para formatar data
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    var date = new Date(dateString);
    return date.toLocaleDateString('pt-PT') + ' ' + date.toLocaleTimeString('pt-PT', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Função para abrir modal de equipamentos
function openEquipamentoModal() {
    var modal = new bootstrap.Modal(document.getElementById('equipamentoModal'));
    modal.show();
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>