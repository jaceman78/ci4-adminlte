<?= $this->extend('layout/master') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
 
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tickets para Tratamento</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#statisticsModal">
                                    <i class="fas fa-chart-bar"></i> Estatísticas
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="tratamentoTicketsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Equipamento</th>
                                        <th>Sala</th>
                                        <th>Tipo de Avaria</th>
                                        <th>Descrição</th>
                                        <th>Estado</th>
                                        <th>Prioridade</th>
                                        <th>Criado em</th>
                                        <th>Criado por</th>
                                        <th>Atribuído a</th>
                                        <th>Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dados carregados via AJAX -->
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>

<!-- Modal para Ver Ticket -->
<div class="modal fade" id="viewTicketModal" tabindex="-1" role="dialog" aria-labelledby="viewTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTicketModalLabel">Detalhes do Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewTicketContent">
                <!-- Conteúdo carregado via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Atribuir/Alterar Estado do Ticket -->
<div class="modal fade" id="assignTicketModal" tabindex="-1" role="dialog" aria-labelledby="assignTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignTicketModalLabel">Atribuir/Alterar Estado do Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="assignTicketForm">
                <div class="modal-body">
                    <input type="hidden" id="assign_ticket_id" name="ticket_id">
                    <div class="form-group">
                        <label for="atribuido_user_id">Atribuir a</label>
                        <select class="form-control" id="atribuido_user_id" name="atribuido_user_id">
                            <option value="">Nenhum (remover atribuição)</option>
                            <?php foreach ($utilizadoresTecnicos as $utilizador): ?>
                                <option value="<?= $utilizador['id'] ?>"><?= esc($utilizador['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="em_resolucao" selected>Em Resolução</option>
                            <option value="aguarda_peca">Aguarda Peça</option>
                            <option value="reparado">Reparado</option>
                            <option value="anulado">Anulado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atribuir/Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Estatísticas -->
<div class="modal fade" id="statisticsModal" tabindex="-1" role="dialog" aria-labelledby="statisticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statisticsModalLabel">Estatísticas de Tickets</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="statisticsContent">
                <!-- Conteúdo carregado via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Alterar Prioridade -->
<div class="modal fade" id="modalPrioridadeTratamento" tabindex="-1" aria-labelledby="modalPrioridadeTratamentoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPrioridadeTratamentoLabel"><i class="fas fa-flag"></i> Alterar Prioridade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nova_prioridade_tratamento">Selecione a nova prioridade:</label>
                    <select class="form-control" id="nova_prioridade_tratamento">
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                        <option value="critica">Crítica</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarPrioridadeTratamento">Salvar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#tratamentoTicketsTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?= site_url('tickets/tratamento-datatable') ?>",
            "type": "GET"
        },
        "columns": [
            { "data": 0 }, // Equipamento
            { "data": 1 }, // Sala
            { "data": 2 }, // Tipo de Avaria
            { "data": 3 }, // Descrição
            { 
                "data": 4, // Estado
                "render": function(data, type, row) {
                    var estadoStyle = '';
                    var estadoTexto = '';
                    
                    switch(data) {
                        case 'novo':
                            estadoStyle = 'background-color: #007bff; color: white;';
                            estadoTexto = 'Novo';
                            break;
                        case 'em_resolucao':
                            estadoStyle = 'background-color: #ffc107; color: #000;';
                            estadoTexto = 'Em Resolução';
                            break;
                        case 'aguarda_peca':
                            estadoStyle = 'background-color: #17a2b8; color: white;';
                            estadoTexto = 'Aguarda Peça';
                            break;
                        case 'reparado':
                            estadoStyle = 'background-color: #28a745; color: white;';
                            estadoTexto = 'Reparado';
                            break;
                        case 'anulado':
                            estadoStyle = 'background-color: #dc3545; color: white;';
                            estadoTexto = 'Anulado';
                            break;
                        default:
                            estadoStyle = 'background-color: #6c757d; color: white;';
                            estadoTexto = data;
                    }
                    
                    return '<span class="badge" style="' + estadoStyle + '">' + estadoTexto + '</span>';
                }
            },
            { 
                "data": 5, // Prioridade
                "render": function(data, type, row) {
                    var prioridadeStyle = '';
                    var prioridadeTexto = '';
                    var ticketId = row[10]; // ID do ticket (última coluna, oculta)
                    var estadoTicket = row[4]; // Estado do ticket
                    var isAdmin = <?= session()->get('level') >= 8 ? 'true' : 'false' ?>;
                    
                    switch(data) {
                        case 'baixa':
                            prioridadeStyle = 'background-color: #28a745; color: white;';
                            prioridadeTexto = 'Baixa';
                            break;
                        case 'media':
                            prioridadeStyle = 'background-color: #ffc107; color: #000;';
                            prioridadeTexto = 'Média';
                            break;
                        case 'alta':
                            prioridadeStyle = 'background-color: #fd7e14; color: white;';
                            prioridadeTexto = 'Alta';
                            break;
                        case 'critica':
                            prioridadeStyle = 'background-color: #dc3545; color: white;';
                            prioridadeTexto = 'Crítica';
                            break;
                        default:
                            prioridadeStyle = 'background-color: #6c757d; color: white;';
                            prioridadeTexto = data;
                    }
                    
                    var cursor = (isAdmin && estadoTicket !== 'reparado') ? 'cursor: pointer;' : '';
                    var opacity = (estadoTicket === 'reparado') ? 'opacity: 0.7;' : '';
                    var title = (isAdmin && estadoTicket !== 'reparado') 
                        ? 'Clique para alterar a prioridade' 
                        : (estadoTicket === 'reparado' ? 'Não é possível alterar prioridade de ticket reparado' : '');
                    
                    return '<span class="badge badge-prioridade-tratamento" style="' + prioridadeStyle + ' ' + cursor + ' ' + opacity + '" ' +
                           'data-ticket-id="' + ticketId + '" ' +
                           'data-prioridade="' + data + '" ' +
                           'data-estado="' + estadoTicket + '" ' +
                           'title="' + title + '">' + prioridadeTexto + '</span>';
                }
            },
            { "data": 6 }, // Criado em
            { "data": 7 }, // Criado por
            { "data": 8 }, // Atribuído a
            { 
                "data": 9, // Opções
                "orderable": false
            },
            { 
                "data": 10, // ID do ticket (coluna oculta)
                "visible": false,
                "searchable": false
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json"
        },
        "responsive": true,
        "autoWidth": false
    });

    // Ver Ticket
    $(document).on('click', '.view-ticket', function() {
        var ticketId = $(this).data('id');
        loadTicketDetails(ticketId);
    });

    // Atribuir/Alterar Estado do Ticket
    $(document).on('click', '.assign-ticket', function() {
        var ticketId = $(this).data('id');
        $('#assign_ticket_id').val(ticketId);
        var assignModal = new bootstrap.Modal(document.getElementById('assignTicketModal'));
        assignModal.show();
    });

    // Submeter formulário de atribuição
    $('#assignTicketForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= site_url("tickets/assignTicket") ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('button[type="submit"]', '#assignTicketForm').prop('disabled', true).text('Atribuindo...');
            },
            success: function(response) {
                // respondUpdated retorna com status HTTP 200, response tem a mensagem
                toastr.success(response.message || 'Ticket atribuído/atualizado com sucesso!');
                var assignModal = bootstrap.Modal.getInstance(document.getElementById('assignTicketModal'));
                assignModal.hide();
                table.ajax.reload();
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                if (response.messages && response.messages.error) {
                    if (typeof response.messages.error === 'object') {
                        $.each(response.messages.error, function(field, message) {
                            toastr.error(message);
                        });
                    } else {
                        toastr.error(response.messages.error);
                    }
                } else {
                    toastr.error('Erro interno do servidor.');
                }
            },
            complete: function() {
                $('button[type="submit"]', '#assignTicketForm').prop('disabled', false).text('Atribuir/Atualizar');
            }
        });
    });

    // Carregar estatísticas
    $('#statisticsModal').on('show.bs.modal', function() {
        loadStatistics();
    });

    function loadTicketDetails(ticketId) {
        $.ajax({
            url: '<?= site_url("tickets/get/") ?>' + ticketId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 200 && response.data) {
                    var ticket = response.data;
                    var content = '<div class="row">';
                    content += '<div class="col-md-6"><strong>ID:</strong> #' + ticket.id + '</div>';
                    content += '<div class="col-md-6"><strong>Estado:</strong> <span class="badge badge-primary">' + ticket.estado + '</span></div>';
                    content += '<div class="col-md-6"><strong>Prioridade:</strong> <span class="badge badge-warning">' + ticket.prioridade + '</span></div>';
                    content += '<div class="col-md-6"><strong>Equipamento:</strong> ' + ticket.equipamento_marca + ' ' + ticket.equipamento_modelo + '</div>';
                    content += '<div class="col-md-6"><strong>Sala:</strong> ' + ticket.codigo_sala + '</div>';
                    content += '<div class="col-md-6"><strong>Tipo de Avaria:</strong> ' + ticket.tipo_avaria_descricao + '</div>';
                    content += '<div class="col-md-6"><strong>Criado por:</strong> ' + ticket.user_nome + '</div>';
                    content += '<div class="col-md-6"><strong>Atribuído a:</strong> ' + (ticket.atribuido_user_nome || 'Não Atribuído') + '</div>';
                    content += '<div class="col-md-6"><strong>Criado em:</strong> ' + ticket.created_at + '</div>';
                    content += '<div class="col-md-6"><strong>Atualizado em:</strong> ' + ticket.updated_at + '</div>';
                    content += '<div class="col-12 mt-3"><strong>Descrição:</strong><br>' + ticket.descricao + '</div>';
                    content += '</div>';
                    
                    $('#viewTicketContent').html(content);
                    var viewModal = new bootstrap.Modal(document.getElementById('viewTicketModal'));
                    viewModal.show();
                } else {
                    toastr.error('Erro ao carregar detalhes do ticket.');
                }
            },
            error: function() {
                toastr.error('Erro ao carregar detalhes do ticket.');
            }
        });
    }

    function loadStatistics() {
        $.ajax({
            url: '<?= site_url("tickets/statistics") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 200 && response.data) {
                    var stats = response.data;
                    var content = '<div class="row">';
                    content += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-info"><i class="fas fa-ticket-alt"></i></span><div class="info-box-content"><span class="info-box-text">Total</span><span class="info-box-number">' + stats.total + '</span></div></div></div>';
                    content += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-primary"><i class="fas fa-plus"></i></span><div class="info-box-content"><span class="info-box-text">Novos</span><span class="info-box-number">' + stats.novo + '</span></div></div></div>';
                    content += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-warning"><i class="fas fa-cog"></i></span><div class="info-box-content"><span class="info-box-text">Em Resolução</span><span class="info-box-number">' + stats.em_resolucao + '</span></div></div></div>';
                    content += '<div class="col-md-3"><div class="info-box"><span class="info-box-icon bg-success"><i class="fas fa-check"></i></span><div class="info-box-content"><span class="info-box-text">Reparados</span><span class="info-box-number">' + stats.reparado + '</span></div></div></div>';
                    content += '</div>';
                    
                    $('#statisticsContent').html(content);
                } else {
                    $('#statisticsContent').html('<p>Erro ao carregar estatísticas.</p>');
                }
            },
            error: function() {
                $('#statisticsContent').html('<p>Erro ao carregar estatísticas.</p>');
            }
        });
    }
    
    // Variável global para armazenar o ticket ID atual
    var currentTicketIdPrioridadeTratamento = null;
    
    // Alterar Prioridade (apenas admins e tickets não reparados)
    $(document).on('click', '.badge-prioridade-tratamento', function() {
        var isAdmin = <?= session()->get('level') >= 8 ? 'true' : 'false' ?>;
        
        if (!isAdmin) {
            toastr.warning('Apenas administradores podem alterar a prioridade.');
            return;
        }
        
        var estadoTicket = $(this).data('estado');
        
        if (estadoTicket === 'reparado') {
            toastr.warning('Não é possível alterar a prioridade de um ticket já reparado.');
            return;
        }
        
        currentTicketIdPrioridadeTratamento = $(this).data('ticket-id');
        var prioridadeAtual = $(this).data('prioridade');
        
        $('#nova_prioridade_tratamento').val(prioridadeAtual);
        
        const modal = new bootstrap.Modal(document.getElementById('modalPrioridadeTratamento'));
        modal.show();
    });
    
    $('#btnSalvarPrioridadeTratamento').on('click', function() {
        const novaPrioridade = $('#nova_prioridade_tratamento').val();
        
        if (!currentTicketIdPrioridadeTratamento) {
            toastr.error('Erro: ID do ticket não encontrado.');
            return;
        }
        
        $.ajax({
            url: '<?= site_url("tickets/updatePrioridade") ?>',
            type: 'POST',
            data: {
                ticket_id: currentTicketIdPrioridadeTratamento,
                prioridade: novaPrioridade
            },
            dataType: 'json',
            beforeSend: function() {
                $('#btnSalvarPrioridadeTratamento').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
            },
            success: function(response) {
                console.log('Success response:', response);
                
                if (response.success) {
                    const modalEl = document.getElementById('modalPrioridadeTratamento');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    
                    toastr.success(response.message || 'Prioridade atualizada com sucesso.');
                    
                    // Recarregar tabela
                    table.ajax.reload(null, false);
                    
                    currentTicketIdPrioridadeTratamento = null;
                } else {
                    toastr.error(response.message || 'Erro ao atualizar prioridade.');
                }
            },
            error: function(xhr) {
                console.log('Error response:', xhr);
                
                const response = xhr.responseJSON;
                let errorMsg = 'Erro ao atualizar prioridade.';
                
                if (response) {
                    if (response.message) {
                        errorMsg = response.message;
                    } else if (response.messages && typeof response.messages === 'object') {
                        errorMsg = Object.values(response.messages).join('<br>');
                    } else if (response.messages) {
                        errorMsg = response.messages;
                    }
                }
                
                toastr.error(errorMsg);
            },
            complete: function() {
                $('#btnSalvarPrioridadeTratamento').prop('disabled', false).html('Salvar');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
