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
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $title ?></h1>
                </div>
                <div class="col-sm-6">
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
            <!-- Estatísticas -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3 id="totalTickets">0</h3>
                            <p>Total de Tickets</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3 id="novosTickets">0</h3>
                            <p>Tickets Novos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3 id="emResolucaoTickets">0</h3>
                            <p>Em Resolução</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-cog"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3 id="reparadosTickets">0</h3>
                            <p>Tickets Reparados</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Todos os Tickets</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#advancedStatisticsModal">
                                    <i class="fas fa-chart-pie"></i> Estatísticas Avançadas
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="exportToExcel()">
                                    <i class="fas fa-file-excel"></i> Exportar Excel
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="todosTicketsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Equipamento</th>
                                        <th>Sala</th>
                                        <th>Tipo de Avaria</th>
                                        <th>Descrição</th>
                                        <th>Estado</th>
                                        <th>Prioridade</th>
                                        <th>Criado em</th>
                                        <th>Criado por</th>
                                        <th>Atribuído a</th>
                                        <th>Aceite</th>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewTicketContent">
                <!-- Conteúdo carregado via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Ticket (Admin) -->
<div class="modal fade" id="editTicketModal" tabindex="-1" role="dialog" aria-labelledby="editTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTicketModalLabel">Editar Ticket (Admin)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTicketForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_ticket_id" name="ticket_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_equipamento_id">Equipamento</label>
                                <select class="form-control" id="edit_equipamento_id" name="equipamento_id" required>
                                    <option value="">Selecione um equipamento</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_sala_id">Sala</label>
                                <select class="form-control" id="edit_sala_id" name="sala_id" required>
                                    <option value="">Selecione uma sala</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_tipo_avaria_id">Tipo de Avaria</label>
                                <select class="form-control" id="edit_tipo_avaria_id" name="tipo_avaria_id" required>
                                    <option value="">Selecione um tipo de avaria</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_atribuido_user_id">Atribuído a</label>
                                <select class="form-control" id="edit_atribuido_user_id" name="atribuido_user_id">
                                    <option value="">Não Atribuído</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_estado">Estado</label>
                                <select class="form-control" id="edit_estado" name="estado" required>
                                    <option value="novo">Novo</option>
                                    <option value="em_resolucao">Em Resolução</option>
                                    <option value="aguarda_peca">Aguarda Peça</option>
                                    <option value="reparado">Reparado</option>
                                    <option value="anulado">Anulado</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_prioridade">Prioridade</label>
                                <select class="form-control" id="edit_prioridade" name="prioridade" required>
                                    <option value="baixa">Baixa</option>
                                    <option value="media">Média</option>
                                    <option value="alta">Alta</option>
                                    <option value="critica">Crítica</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_descricao">Descrição da Avaria</label>
                        <textarea class="form-control" id="edit_descricao" name="descricao" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Estatísticas Avançadas -->
<div class="modal fade" id="advancedStatisticsModal" tabindex="-1" role="dialog" aria-labelledby="advancedStatisticsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="advancedStatisticsModalLabel">Estatísticas Avançadas de Tickets</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="advancedStatisticsContent">
                <!-- Conteúdo carregado via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Carregar estatísticas básicas
    loadBasicStatistics();

    // Inicializar DataTable
    var table = $('#todosTicketsTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?= site_url('tickets/todos-datatable') ?>",
            "type": "GET"
        },
        "columns": [
            { "data": 0 }, // ID
            { "data": 1 }, // Equipamento
            { "data": 2 }, // Sala
            { "data": 3 }, // Tipo de Avaria
            { "data": 4 }, // Descrição
            { 
                "data": 5, // Estado
                "render": function(data, type, row) {
                    var badgeClass = '';
                    switch(data) {
                        case 'novo':
                            badgeClass = 'badge-primary';
                            break;
                        case 'em_resolucao':
                            badgeClass = 'badge-warning';
                            break;
                        case 'aguarda_peca':
                            badgeClass = 'badge-info';
                            break;
                        case 'reparado':
                            badgeClass = 'badge-success';
                            break;
                        case 'anulado':
                            badgeClass = 'badge-danger';
                            break;
                        default:
                            badgeClass = 'badge-secondary';
                    }
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            { 
                "data": 6, // Prioridade
                "render": function(data, type, row) {
                    var badgeClass = '';
                    switch(data) {
                        case 'baixa':
                            badgeClass = 'badge-success';
                            break;
                        case 'media':
                            badgeClass = 'badge-warning';
                            break;
                        case 'alta':
                            badgeClass = 'badge-danger';
                            break;
                        case 'critica':
                            badgeClass = 'badge-dark';
                            break;
                        default:
                            badgeClass = 'badge-secondary';
                    }
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            { "data": 7 }, // Criado em
            { "data": 8 }, // Criado por
            { "data": 9 }, // Atribuído a
            { 
                "data": 10, // Aceite
                "render": function(data, type, row) {
                    return data === 'Sim' ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>';
                }
            },
            { 
                "data": 11, // Opções
                "orderable": false
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese.json"
        },
        "responsive": true,
        "autoWidth": false,
        "order": [[0, "desc"]] // Ordenar por ID decrescente
    });

    // Ver Ticket
    $(document).on('click', '.view-ticket', function() {
        var ticketId = $(this).data('id');
        loadTicketDetails(ticketId);
    });

    // Editar Ticket
    $(document).on('click', '.edit-ticket', function() {
        var ticketId = $(this).data('id');
        loadTicketForEdit(ticketId);
    });

    // Apagar Ticket
    $(document).on('click', '.delete-ticket', function() {
        var ticketId = $(this).data('id');
        if (confirm('Tem certeza de que deseja apagar este ticket? Esta ação não pode ser desfeita.')) {
            deleteTicket(ticketId);
        }
    });

    // Submeter formulário de edição
    $('#editTicketForm').on('submit', function(e) {
        e.preventDefault();
        var ticketId = $('#edit_ticket_id').val();
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= site_url("tickets/update/") ?>' + ticketId,
            type: 'PUT',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('button[type="submit"]', '#editTicketForm').prop('disabled', true).text('Atualizando...');
            },
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.messages.success || 'Ticket atualizado com sucesso!');
                    $('#editTicketModal').modal('hide');
                    table.ajax.reload();
                    loadBasicStatistics();
                } else {
                    toastr.error('Erro ao atualizar ticket.');
                }
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
                $('button[type="submit"]', '#editTicketForm').prop('disabled', false).text('Atualizar Ticket');
            }
        });
    });

    // Carregar estatísticas avançadas
    $('#advancedStatisticsModal').on('show.bs.modal', function() {
        loadAdvancedStatistics();
    });

    function loadBasicStatistics() {
        $.ajax({
            url: '<?= site_url("tickets/statistics") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 200 && response.data) {
                    var stats = response.data;
                    $('#totalTickets').text(stats.total || 0);
                    $('#novosTickets').text(stats.novo || 0);
                    $('#emResolucaoTickets').text(stats.em_resolucao || 0);
                    $('#reparadosTickets').text(stats.reparado || 0);
                }
            },
            error: function() {
                console.log('Erro ao carregar estatísticas básicas.');
            }
        });
    }

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
                    content += '<div class="col-md-6"><strong>Aceite:</strong> ' + (ticket.ticket_aceite ? '<span class="badge badge-success">Sim</span>' : '<span class="badge badge-secondary">Não</span>') + '</div>';
                    content += '<div class="col-md-6"><strong>Equipamento:</strong> ' + ticket.equipamento_marca + ' ' + ticket.equipamento_modelo + '</div>';
                    content += '<div class="col-md-6"><strong>Sala:</strong> ' + ticket.codigo_sala + '</div>';
                    content += '<div class="col-md-6"><strong>Tipo de Avaria:</strong> ' + ticket.tipo_avaria_descricao + '</div>';
                    content += '<div class="col-md-6"><strong>Criado por:</strong> ' + ticket.user_nome + ' (' + ticket.user_email + ')</div>';
                    content += '<div class="col-md-6"><strong>Atribuído a:</strong> ' + (ticket.atribuido_user_nome || 'Não Atribuído') + '</div>';
                    content += '<div class="col-md-6"><strong>Criado em:</strong> ' + ticket.created_at + '</div>';
                    content += '<div class="col-md-6"><strong>Atualizado em:</strong> ' + ticket.updated_at + '</div>';
                    content += '<div class="col-12 mt-3"><strong>Descrição:</strong><br>' + ticket.descricao + '</div>';
                    content += '</div>';
                    
                    $('#viewTicketContent').html(content);
                    $('#viewTicketModal').modal('show');
                } else {
                    toastr.error('Erro ao carregar detalhes do ticket.');
                }
            },
            error: function() {
                toastr.error('Erro ao carregar detalhes do ticket.');
            }
        });
    }

    function loadTicketForEdit(ticketId) {
        $.ajax({
            url: '<?= site_url("tickets/get/") ?>' + ticketId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 200 && response.data) {
                    var ticket = response.data;
                    $('#edit_ticket_id').val(ticket.id);
                    $('#edit_equipamento_id').val(ticket.equipamento_id);
                    $('#edit_sala_id').val(ticket.sala_id);
                    $('#edit_tipo_avaria_id').val(ticket.tipo_avaria_id);
                    $('#edit_atribuido_user_id').val(ticket.atribuido_user_id || '');
                    $('#edit_estado').val(ticket.estado);
                    $('#edit_prioridade').val(ticket.prioridade);
                    $('#edit_descricao').val(ticket.descricao);
                    
                    // Carregar opções dos selects
                    loadSelectOptions();
                    
                    $('#editTicketModal').modal('show');
                } else {
                    toastr.error('Erro ao carregar dados do ticket.');
                }
            },
            error: function() {
                toastr.error('Erro ao carregar dados do ticket.');
            }
        });
    }

    function deleteTicket(ticketId) {
        $.ajax({
            url: '<?= site_url("tickets/delete/") ?>' + ticketId,
            type: 'DELETE',
            dataType: 'json',
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.messages.success || 'Ticket apagado com sucesso!');
                    table.ajax.reload();
                    loadBasicStatistics();
                } else {
                    toastr.error('Erro ao apagar ticket.');
                }
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                if (response.messages && response.messages.error) {
                    toastr.error(response.messages.error);
                } else {
                    toastr.error('Erro interno do servidor.');
                }
            }
        });
    }

    function loadSelectOptions() {
        // Carregar equipamentos
        $.get('<?= site_url("equipamentos/all") ?>', function(data) {
            $('#edit_equipamento_id').empty().append('<option value="">Selecione um equipamento</option>');
            $.each(data, function(key, value) {
                $('#edit_equipamento_id').append('<option value="' + value.id + '">' + value.marca + ' ' + value.modelo + '</option>');
            });
        });

        // Carregar salas
        $.get('<?= site_url("salas/all") ?>', function(data) {
            $('#edit_sala_id').empty().append('<option value="">Selecione uma sala</option>');
            $.each(data, function(key, value) {
                $('#edit_sala_id').append('<option value="' + value.id + '">' + value.codigo_sala + '</option>');
            });
        });

        // Carregar tipos de avaria
        $.get('<?= site_url("tipos-avaria/all") ?>', function(data) {
            $('#edit_tipo_avaria_id').empty().append('<option value="">Selecione um tipo de avaria</option>');
            $.each(data, function(key, value) {
                $('#edit_tipo_avaria_id').append('<option value="' + value.id + '">' + value.descricao + '</option>');
            });
        });

        // Carregar utilizadores técnicos
        $.get('<?= site_url("users/technicians") ?>', function(data) {
            $('#edit_atribuido_user_id').empty().append('<option value="">Não Atribuído</option>');
            $.each(data, function(key, value) {
                $('#edit_atribuido_user_id').append('<option value="' + value.id + '">' + value.name + '</option>');
            });
        });
    }

    function loadAdvancedStatistics() {
        $.ajax({
            url: '<?= site_url("tickets/advanced-statistics") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 200 && response.data) {
                    var stats = response.data;
                    var content = '<div class="row">';
                    
                    // Estatísticas por Estado
                    content += '<div class="col-md-6"><h5>Por Estado</h5><canvas id="estadoChart" width="400" height="200"></canvas></div>';
                    
                    // Estatísticas por Prioridade
                    content += '<div class="col-md-6"><h5>Por Prioridade</h5><canvas id="prioridadeChart" width="400" height="200"></canvas></div>';
                    
                    // Estatísticas por Tipo de Avaria
                    content += '<div class="col-md-6"><h5>Por Tipo de Avaria</h5><canvas id="tipoAvariaChart" width="400" height="200"></canvas></div>';
                    
                    // Estatísticas por Utilizador
                    content += '<div class="col-md-6"><h5>Por Utilizador Criador</h5><canvas id="utilizadorChart" width="400" height="200"></canvas></div>';
                    
                    content += '</div>';
                    
                    $('#advancedStatisticsContent').html(content);
                    
                    // Renderizar gráficos (assumindo que Chart.js está carregado)
                    // Esta parte requer a biblioteca Chart.js
                    
                } else {
                    $('#advancedStatisticsContent').html('<p>Erro ao carregar estatísticas avançadas.</p>');
                }
            },
            error: function() {
                $('#advancedStatisticsContent').html('<p>Erro ao carregar estatísticas avançadas.</p>');
            }
        });
    }

    function exportToExcel() {
        window.location.href = '<?= site_url("tickets/export-excel") ?>';
    }
});
</script>
<?= $this->endSection() ?>
