<?= $this->extend('layout/master') ?>

<?= $this->section('pageHeader') ?>
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Gestão de Salas</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Salas</li>
        </ol>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <!-- Card para seleção de escola -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Selecionar Escola</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="escolaSelect">Escola:</label>
                            <select class="form-control" id="escolaSelect">
                                <option value="">Selecione uma escola...</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-info" onclick="loadEscolaInfo()" id="infoEscolaBtn" disabled>
                                    <i class="fas fa-info-circle"></i> Informações da Escola
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card para lista de salas (inicialmente oculto) -->
        <div class="card" id="salasCard" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">Lista de Salas - <span id="escolaNomeHeader"></span></h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()" id="novaSalaBtn">
                        <i class="fas fa-plus"></i> Nova Sala
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportCSV()" id="exportBtn">
                        <i class="fas fa-download"></i> Exportar CSV
                    </button>
                    <button type="button" class="btn btn-info btn-sm" onclick="getStats()" id="statsBtn">
                        <i class="fas fa-chart-bar"></i> Estatísticas
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="salasTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código da Sala</th>
                            <th>Escola</th>
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

<!-- Modal para Criar/Editar Sala -->
<div class="modal fade" id="salaModal" tabindex="-1" aria-labelledby="salaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salaModalLabel">Nova Sala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="salaForm">
                <div class="modal-body">
                    <input type="hidden" id="salaId" name="id">
                    <input type="hidden" id="salaEscolaId" name="escola_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="salaEscolaNome" class="form-label">Escola</label>
                                <input type="text" class="form-control" id="salaEscolaNome" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="salaCodigoSala" class="form-label">Código da Sala <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="salaCodigoSala" name="codigo_sala" placeholder="Ex: A101, B205, Lab01" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="saveSalaBtn">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Sala -->
<div class="modal fade" id="viewSalaModal" tabindex="-1" aria-labelledby="viewSalaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSalaModalLabel">Detalhes da Sala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td id="viewSalaId"></td>
                            </tr>
                            <tr>
                                <td><strong>Código da Sala:</strong></td>
                                <td id="viewSalaCodigoSala"></td>
                            </tr>
                            <tr>
                                <td><strong>Escola:</strong></td>
                                <td id="viewSalaEscolaNome"></td>
                            </tr>
                            <tr>
                                <td><strong>Data Criação:</strong></td>
                                <td id="viewSalaCreated"></td>
                            </tr>
                            <tr>
                                <td><strong>Última Atualização:</strong></td>
                                <td id="viewSalaUpdated"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="editSalaFromView()">
                    <i class="fas fa-edit"></i> Editar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Informações da Escola -->
<div class="modal fade" id="escolaInfoModal" tabindex="-1" aria-labelledby="escolaInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="escolaInfoModalLabel">Informações da Escola</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nome:</strong></td>
                                <td id="infoEscolaNome"></td>
                            </tr>
                            <tr>
                                <td><strong>Morada:</strong></td>
                                <td id="infoEscolaMorada"></td>
                            </tr>
                            <tr>
                                <td><strong>Total de Salas:</strong></td>
                                <td id="infoEscolaTotalSalas"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Estatísticas -->
<div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statsModalLabel">Estatísticas das Salas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-door-open"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total de Salas nesta Escola</span>
                                <span class="info-box-number" id="statsTotal">0</span>
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
<?= $this->section('scripts') ?>
<script>
var table;
var selectedEscolaId = null;
var selectedEscolaNome = '';

$(document).ready(function() {
    // Carregar lista de escolas
    loadEscolas();

    // Event listener para mudança de escola
    $('#escolaSelect').on('change', function() {
        var escolaId = $(this).val();
        var escolaNome = $(this).find('option:selected').text();
        
        if (escolaId) {
            selectedEscolaId = escolaId;
            selectedEscolaNome = escolaNome;
            
            // Mostrar card das salas
            $('#salasCard').show();
            $('#escolaNomeHeader').text(escolaNome);
            
            // Habilitar botões
            $('#infoEscolaBtn').prop('disabled', false);
            
            // Inicializar ou recarregar DataTable
            initializeDataTable();
        } else {
            selectedEscolaId = null;
            selectedEscolaNome = '';
            
            // Ocultar card das salas
            $('#salasCard').hide();
            
            // Desabilitar botões
            $('#infoEscolaBtn').prop('disabled', true);
            
            // Destruir DataTable se existir
            if (table) {
                table.destroy();
                table = null;
            }
        }
    });

    // Form submission
    $('#salaForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var salaId = $('#salaId').val();
        var url = salaId ? '<?= base_url('salas/update') ?>/' + salaId : '<?= base_url('salas/create') ?>';
        var method = 'POST';

        // Desabilitar botão de submit
        $('#saveSalaBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> A guardar...');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#salaModal').modal('hide');
                    if (table) {
                        table.ajax.reload();
                    }
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
                $('#saveSalaBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });
});

// Função para carregar lista de escolas
function loadEscolas() {
    $.ajax({
        url: '<?= base_url('salas/getEscolasDropdown') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var select = $('#escolaSelect');
                select.empty().append('<option value="">Selecione uma escola...</option>');
                
                $.each(response.data, function(id, nome) {
                    select.append('<option value="' + id + '">' + nome + '</option>');
                });
            } else {
                showToast('error', 'Erro ao carregar escolas');
            }
        },
        error: function() {
            showToast('error', 'Erro ao carregar escolas');
        }
    });
}

// Função para inicializar DataTable
function initializeDataTable() {
    if (table) {
        table.destroy();
    }
    
    table = $('#salasTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('salas/getDataTable') ?>',
            type: 'POST',
            data: function (d) {
                d['X-Requested-With'] = 'XMLHttpRequest';
                d['escola_id'] = selectedEscolaId;
                return d;
            }
        },
        columns: [
            { data: 0, name: 'id', visible: false  },
            { data: 1, name: 'codigo_sala' },
            { data: 2, name: 'escola_nome' },
            { data: 3, name: 'created_at' },
            { data: 4, name: 'actions', orderable: false, searchable: false }
        ],
        language: {
             url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[1, 'asc']]
    });
}

// Função para abrir modal de criação
function openCreateModal() {
    if (!selectedEscolaId) {
        showToast('error', 'Selecione uma escola primeiro');
        return;
    }
    
    resetForm();
    $('#salaEscolaId').val(selectedEscolaId);
    $('#salaEscolaNome').val(selectedEscolaNome);
    $('#salaModalLabel').text('Nova Sala');
    $('#salaModal').modal('show');
}

// Função para editar sala
function editSala(id) {
    $.ajax({
        url: '<?= base_url('salas/getSala') ?>/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var sala = response.data;
                
                $('#salaId').val(sala.id);
                $('#salaEscolaId').val(sala.escola_id);
                $('#salaEscolaNome').val(sala.escola_nome);
                $('#salaCodigoSala').val(sala.codigo_sala);
                
                $('#salaModalLabel').text('Editar Sala');
                $('#salaModal').modal('show');
            } else {
                showToast('error', response.message || 'Erro ao carregar dados da sala');
            }
        },
        error: function() {
            showToast('error', 'Erro ao carregar dados da sala');
        }
    });
}

// Função para ver sala
function viewSala(id) {
    $.ajax({
        url: '<?= base_url('salas/getSala') ?>/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var sala = response.data;
                
                $('#viewSalaId').text(sala.id);
                $('#viewSalaCodigoSala').text(sala.codigo_sala);
                $('#viewSalaEscolaNome').text(sala.escola_nome);
                $('#viewSalaCreated').text(formatDate(sala.created_at));
                $('#viewSalaUpdated').text(sala.updated_at ? formatDate(sala.updated_at) : 'N/A');
                
                // Guardar ID para possível edição
                $('#viewSalaModal').data('sala-id', sala.id);
                
                $('#viewSalaModal').modal('show');
            } else {
                showToast('error', response.message || 'Erro ao carregar dados da sala');
            }
        },
        error: function() {
            showToast('error', 'Erro ao carregar dados da sala');
        }
    });
}

// Função para editar sala a partir do modal de visualização
function editSalaFromView() {
    var salaId = $('#viewSalaModal').data('sala-id');
    $('#viewSalaModal').modal('hide');
    
    // Pequeno delay para evitar conflito entre modais
    setTimeout(function() {
        editSala(salaId);
    }, 300);
}

// Função para eliminar sala
function deleteSala(id) {
    if (confirm('Tem a certeza que deseja eliminar esta sala?')) {
        $.ajax({
            url: '<?= base_url('salas/delete') ?>/' + id,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    if (table) {
                        table.ajax.reload();
                    }
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Erro ao eliminar sala');
            }
        });
    }
}

// Função para exportar CSV
function exportCSV() {
    if (!selectedEscolaId) {
        showToast('error', 'Selecione uma escola primeiro');
        return;
    }
    
    window.location.href = '<?= base_url('salas/exportCSV') ?>?escola_id=' + selectedEscolaId;
}

// Função para obter estatísticas
function getStats() {
    if (!selectedEscolaId) {
        showToast('error', 'Selecione uma escola primeiro');
        return;
    }
    
    $.ajax({
        url: '<?= base_url('salas/getStats') ?>?escola_id=' + selectedEscolaId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var stats = response.data;
                
                $('#statsTotal').text(stats.total);
                
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

// Função para carregar informações da escola
function loadEscolaInfo() {
    if (!selectedEscolaId) {
        showToast('error', 'Selecione uma escola primeiro');
        return;
    }
    
    $.ajax({
        url: '<?= base_url('salas/getEscolaInfo') ?>/' + selectedEscolaId,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var escola = response.data.escola;
                var stats = response.data.stats;
                
                $('#infoEscolaNome').text(escola.nome);
                $('#infoEscolaMorada').text(escola.morada || 'Não definida');
                $('#infoEscolaTotalSalas').text(stats.total);
                
                $('#escolaInfoModal').modal('show');
            } else {
                showToast('error', response.message || 'Erro ao carregar informações da escola');
            }
        },
        error: function() {
            showToast('error', 'Erro ao carregar informações da escola');
        }
    });
}

// Função para resetar formulário
function resetForm() {
    $('#salaForm')[0].reset();
    $('#salaId').val('');
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
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
