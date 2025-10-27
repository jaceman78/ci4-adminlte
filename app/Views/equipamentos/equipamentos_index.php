<?= $this->extend('layout/master') ?>
<?= $this->section('pageHeader') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
 
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                        <li class="breadcrumb-item active">Equipamentos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-laptop"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Equipamentos</span>
                            <span class="info-box-number" id="total-equipamentos">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ativos</span>
                            <span class="info-box-number" id="equipamentos-ativos">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Fora de Serviço</span>
                            <span class="info-box-number" id="equipamentos-fora-servico">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Por Atribuir</span>
                            <span class="info-box-number" id="equipamentos-por-atribuir">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main row -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= $page_subtitle ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#equipamentoModal" onclick="openCreateModal()">
                                    <i class="fas fa-plus"></i> Novo Equipamento
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="loadStatistics(true)">
                                    <i class="fas fa-chart-bar"></i> Estatísticas
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="equipamentosTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Escola</th>
                                        <th>Sala</th>
                                        <th>Tipo</th>
                                        <th>Marca/Modelo</th>
                                        <th>Número de Série</th>
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
        </div>
    </section>
</div>

<!-- Modal para Equipamento -->
<div class="modal fade" id="equipamentoModal" tabindex="-1" aria-labelledby="equipamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipamentoModalLabel">Novo Equipamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="equipamentoForm">
                <div class="modal-body">
                    <input type="hidden" id="equipamento_id" name="equipamento_id">
                    
                    <!-- Seção de Localização -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Localização</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="escola_id" class="form-label">Escola</label>
                                        <select class="form-select" id="escola_id" name="escola_id">
                                            <option value="">Sem atribuição</option>
                                            <?php foreach ($escolas as $escola): ?>
                                                <option value="<?= $escola['id'] ?>"><?= $escola['nome'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Deixe vazio se o equipamento não tem sala atribuída</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sala_id" class="form-label">Sala</label>
                                        <select class="form-select" id="sala_id" name="sala_id" disabled>
                                            <option value="">Selecione primeiro uma escola</option>
                                        </select>
                                        <small class="text-muted">Selecione a escola primeiro</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="motivo_section" style="display:none;">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="motivo_movimentacao" class="form-label">Motivo da Atribuição/Movimentação</label>
                                        <textarea class="form-control" id="motivo_movimentacao" name="motivo_movimentacao" rows="2" placeholder="Ex: Novo equipamento, Transferência, Substituição..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção de Dados do Equipamento -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-laptop"></i> Dados do Equipamento</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tipo_id" class="form-label">Tipo de Equipamento <span class="text-danger">*</span></label>
                                        <select class="form-select" id="tipo_id" name="tipo_id" required>
                                            <option value="">Selecione um tipo</option>
                                            <?php foreach ($tipos_equipamento as $tipo): ?>
                                                <option value="<?= $tipo['id'] ?>"><?= $tipo['nome'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                        <select class="form-select" id="estado" name="estado" required>
                                            <option value="ativo">Ativo</option>
                                            <option value="fora_servico">Fora de Serviço</option>
                                            <option value="por_atribuir">Por Atribuir</option>
                                            <option value="abate">Abate</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="marca" class="form-label">Marca</label>
                                        <input type="text" class="form-control" id="marca" name="marca">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="modelo" class="form-label">Modelo</label>
                                        <input type="text" class="form-control" id="modelo" name="modelo">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="numero_serie" class="form-label">Número de Série</label>
                                        <input type="text" class="form-control" id="numero_serie" name="numero_serie">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="observacoes" class="form-label">Observações</label>
                                        <textarea class="form-control" id="observacoes" name="observacoes" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
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

<!-- Modal para Visualizar Equipamento -->
<div class="modal fade" id="viewEquipamentoModal" tabindex="-1" aria-labelledby="viewEquipamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEquipamentoModalLabel">Detalhes do Equipamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Sala:</strong>
                        <p id="view_sala"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Tipo:</strong>
                        <p id="view_tipo"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Marca:</strong>
                        <p id="view_marca"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Modelo:</strong>
                        <p id="view_modelo"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Número de Série:</strong>
                        <p id="view_numero_serie"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Estado:</strong>
                        <p id="view_estado"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Observações:</strong>
                        <p id="view_observacoes"></p>
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
        Tem a certeza que deseja eliminar este equipamento?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmação Mudança com Tickets -->
<div class="modal fade" id="confirmMudancaTicketsModal" tabindex="-1" aria-labelledby="confirmMudancaTicketsLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-warning">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="confirmMudancaTicketsLabel">
            <i class="fas fa-exclamation-triangle"></i> Equipamento com Tickets em Reparação
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Este equipamento tem <strong id="tickets_count_text">0</strong> ticket(s) em reparação.
        </div>
        <p><strong>Sala Atual dos Tickets:</strong> <span id="sala_atual_text" class="badge bg-primary"></span></p>
        <p><strong>Nova Sala:</strong> <span id="sala_nova_text" class="badge bg-success"></span></p>
        <hr>
        <p class="mb-0">Ao continuar, todos os tickets em aberto serão automaticamente atualizados para a nova sala.</p>
        <p class="mt-2 text-muted"><small><i class="fas fa-lightbulb"></i> Esta ação garante que os tickets permanecem sincronizados com a localização do equipamento.</small></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning" id="confirmMudancaTicketsBtn">
            <i class="fas fa-check"></i> Continuar com Mudança
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Gestão de Sala -->
<div class="modal fade" id="gerirSalaModal" tabindex="-1" aria-labelledby="gerirSalaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="gerirSalaModalLabel"><i class="fas fa-map-marker-alt"></i> Gerir Localização</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="gerirSalaForm">
                <div class="modal-body">
                    <input type="hidden" id="gerir_equipamento_id" name="equipamento_id">
                    <input type="hidden" id="gerir_action" name="action">
                    
                    <div class="alert alert-info">
                        <strong>Equipamento:</strong> <span id="gerir_equipamento_info"></span>
                    </div>
                    
                    <div id="sala_atual_info" class="alert alert-warning" style="display:none;">
                        <strong>Sala Atual:</strong> <span id="gerir_sala_atual"></span>
                    </div>
                    
                    <div id="nova_localizacao_section">
                        <div class="mb-3">
                            <label for="gerir_escola_id" class="form-label">Escola <span class="text-danger">*</span></label>
                            <select class="form-select" id="gerir_escola_id" name="escola_id" required>
                                <option value="">Selecione uma escola</option>
                                <?php foreach ($escolas as $escola): ?>
                                    <option value="<?= $escola['id'] ?>"><?= $escola['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gerir_sala_id" class="form-label">Sala <span class="text-danger">*</span></label>
                            <select class="form-select" id="gerir_sala_id" name="sala_id" required disabled>
                                <option value="">Selecione primeiro uma escola</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gerir_motivo" class="form-label">Motivo <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="gerir_motivo" name="motivo_movimentacao" rows="3" required placeholder="Ex: Transferência, Avaria, Substituição..."></textarea>
                        </div>
                    </div>
                    
                    <div id="remover_sala_section" style="display:none;">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Tem certeza que deseja remover este equipamento da sala atual?
                        </div>
                        <div class="mb-3">
                            <label for="gerir_motivo_remocao" class="form-label">Motivo da Remoção <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="gerir_motivo_remocao" name="motivo_movimentacao" rows="3" placeholder="Ex: Equipamento para reparação, Equipamento obsoleto..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="gerirSalaBtn">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Estatísticas -->
<div class="modal fade" id="estatisticasModal" tabindex="-1" aria-labelledby="estatisticasModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="estatisticasModalLabel"><i class="fas fa-chart-bar"></i> Estatísticas dos Equipamentos</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="estatisticasModalBody">
        <!-- Conteúdo das estatísticas será preenchido por JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<?= $this->section('scripts') ?>
<script>
// Definir baseUrl global
const baseUrl = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/equipamentos.js') ?>"></script>
<script>
// Script legado removido - agora usa arquivo externo equipamentos.js
// Manter apenas configurações específicas se necessário
/*
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#equipamentosTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?= base_url('equipamentos/getDataTable') ?>",
            "type": "POST"
        },
        "columns": [
            { "data": "sala_nome" },
            { "data": "tipo_nome" },
            { "data": "marca" },
            { "data": "modelo" },
            { "data": "numero_serie" },
            { 
                "data": "estado",
                "render": function(data, type, row) {
                    var badgeClass = '';
                    var text = '';
                    switch(data) {
                        case 'ativo':
                            badgeClass = 'bg-success';
                            text = 'Ativo';
                            break;
                        case 'inativo':
                            badgeClass = 'bg-secondary';
                            text = 'Inativo';
                            break;
                        case 'pendente':
                            badgeClass = 'bg-warning';
                            text = 'Pendente';
                            break;
                        default:
                            badgeClass = 'bg-light';
                            text = data;
                    }
                    return '<span class="badge ' + badgeClass + '">' + text + '</span>';
                }
            },
            { "data": "observacoes" },
            {
                "data": null,
                "orderable": false,
                "render": function(data, type, row) {
                    return '<div class="btn-group" role="group">' +
                           '<button type="button" class="btn btn-sm btn-info" onclick="viewEquipamento(' + row.id + ')" title="Ver">' +
                           '<i class="fas fa-eye"></i></button>' +
                           '<button type="button" class="btn btn-sm btn-warning" onclick="editEquipamento(' + row.id + ')" title="Editar">' +
                           '<i class="fas fa-edit"></i></button>' +
                           '<button type="button" class="btn btn-sm btn-danger" onclick="deleteEquipamento(' + row.id + ')" title="Eliminar">' +
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

    // Carregar estatísticas ao inicializar
    loadStatistics();

    // Submissão do formulário
    $('#equipamentoForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var equipamentoId = $('#equipamento_id').val();
        var url = equipamentoId ? 
            '<?= base_url('equipamentos/update') ?>/' + equipamentoId : 
            '<?= base_url('equipamentos/create') ?>';
        
        // Debug: mostrar dados do formulário
        console.log('Estado antes de enviar:', $('#estado').val());
        console.log('FormData completo:');
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#equipamentoModal').modal('hide');
                table.ajax.reload();
                loadStatistics();
                showToast('success', response.message || 'Operação realizada com sucesso!');
            },
            error: function(xhr) {
                console.log('Erro na resposta:', xhr.responseText);
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

let equipamentoToDelete = null;

function deleteEquipamento(id) {
    equipamentoToDelete = id;
    $('#confirmDeleteModal').modal('show');
}

$('#confirmDeleteBtn').on('click', function() {
    if (equipamentoToDelete) {
        $.ajax({
            url: '<?= base_url('equipamentos/delete') ?>/' + equipamentoToDelete,
            type: 'POST',
            success: function(response) {
                $('#equipamentosTable').DataTable().ajax.reload();
                loadStatistics();
                showToast('success', response.message || 'Equipamento eliminado com sucesso!');
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                showToast('error', response.message || 'Erro ao eliminar equipamento.');
            },
            complete: function() {
                $('#confirmDeleteModal').modal('hide');
                equipamentoToDelete = null;
            }
        });
    }
});

function openCreateModal() {
    $('#equipamentoModalLabel').text('Novo Equipamento');
    $('#equipamentoForm')[0].reset();
    $('#equipamento_id').val('');
    $('#saveButton').text('Guardar');
}

function editEquipamento(id) {
    $.ajax({
        url: '<?= base_url('equipamentos/getEquipamento') ?>/' + id,
        type: 'GET',
        success: function(data) {
            console.log('Dados do equipamento:', data);
            console.log('Estado recebido:', data.estado);
            
            $('#equipamentoModalLabel').text('Editar Equipamento');
            $('#equipamento_id').val(data.id);
            $('#sala_id').val(data.sala_id);
            $('#tipo_id').val(data.tipo_id);
            $('#marca').val(data.marca);
            $('#modelo').val(data.modelo);
            $('#numero_serie').val(data.numero_serie);
            $('#estado').val(data.estado);
            
            console.log('Estado selecionado no select:', $('#estado').val());
            
            $('#observacoes').val(data.observacoes);
            $('#saveButton').text('Atualizar');
            $('#equipamentoModal').modal('show');
        },
        error: function(xhr) {
            var response = JSON.parse(xhr.responseText);
            showToast('error', response.message || 'Erro ao carregar dados do equipamento.');
        }
    });
}

function viewEquipamento(id) {
    $.get('<?= base_url('equipamentos/getEquipamento') ?>/' + id, function(data) {
        $('#view_sala').text(data.sala_nome ?? '');
        $('#view_tipo').text(data.tipo_nome ?? '');
        $('#view_marca').text(data.marca ?? '');
        $('#view_modelo').text(data.modelo ?? '');
        $('#view_numero_serie').text(data.numero_serie ?? '');
        $('#view_estado').text(data.estado ?? '');
        $('#view_observacoes').text(data.observacoes ?? '');
        $('#viewEquipamentoModal').modal('show');
    });
}

function confirmDelete(id) {
    $('#confirmDeleteBtn').off('click').on('click', function() {
        $.ajax({
            url: '<?= base_url('equipamentos/delete') ?>/' + id,
            type: 'POST',
            success: function(response) {
                $('#equipamentosTable').DataTable().ajax.reload();
                loadStatistics();
                showToast('success', response.message || 'Equipamento eliminado com sucesso!');
                $('#confirmDeleteModal').modal('hide');
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                showToast('error', response.message || 'Erro ao eliminar equipamento.');
            }
        });
    });
    $('#confirmDeleteModal').modal('show');
}

function loadStatistics(showModal = false) {
    $.ajax({
        url: '<?= base_url('equipamentos/getStatistics') ?>',
        type: 'GET',
        success: function(data) {
            // Atualiza info-boxes (mantém)
            $('#total-equipamentos').text(data.total_equipamentos);
            $('#equipamentos-ativos').text(0);
            $('#equipamentos-fora-servico').text(0);
            $('#equipamentos-por-atribuir').text(0);
            data.por_estado.forEach(function(item) {
                switch(item.estado) {
                    case 'ativo':
                        $('#equipamentos-ativos').text(item.total);
                        break;
                    case 'fora_servico':
                        $('#equipamentos-fora-servico').text(item.total);
                        break;
                    case 'por_atribuir':
                        $('#equipamentos-por-atribuir').text(item.total);
                        break;
                }
            });

            // Preenche a modal com estatísticas detalhadas
            let html = '<h6>Por Estado</h6><ul>';
            data.por_estado.forEach(function(item) {
                html += '<li>' + item.estado + ': <strong>' + item.total + '</strong></li>';
            });
            html += '</ul>';

            html += '<h6>Por Tipo</h6><ul>';
            data.por_tipo.forEach(function(item) {
                html += '<li>Tipo ID ' + item.tipo_id + ': <strong>' + item.total + '</strong></li>';
            });
            html += '</ul>';

            $('#estatisticasModalBody').html(html);
            
            // Só abre a modal se for explicitamente solicitado (quando clicar no botão)
            if (showModal) {
                $('#estatisticasModal').modal('show');
            }
        },
        error: function(xhr) {
            console.error('Erro ao carregar estatísticas:', xhr);
        }
    });
}

function getEstadoBadge(estado) {
    var badgeClass = '';
    var text = '';
    switch(estado) {
        case 'ativo':
            badgeClass = 'bg-success';
            text = 'Ativo';
            break;
        case 'fora_servico':
            badgeClass = 'bg-warning';
            text = 'Fora de Serviço';
            break;
        case 'abate':
            badgeClass = 'bg-danger';
            text = 'Abate';
            break;
        case 'por_atribuir':
            badgeClass = 'bg-secondary';
            text = 'Por Atribuir';
            break;
        default:
            badgeClass = 'bg-light';
            text = estado;
    }
    return '<span class="badge ' + badgeClass + '">' + text + '</span>';
}

function showToast(type, message) {
    // Implementar sistema de toast notifications
    // Pode usar Toastr.js ou similar
    if (type === 'success') {
        alert('Sucesso: ' + message);
    } else {
        alert('Erro: ' + message);
    }
}
*/
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
