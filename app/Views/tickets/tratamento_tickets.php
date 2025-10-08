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

<!-- Modal para Atribuir/Alterar Estado do Ticket -->
<div class="modal fade" id="assignTicketModal" tabindex="-1" role="dialog" aria-labelledby="assignTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignTicketModalLabel">Atribuir/Alterar Estado do Ticket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="assignTicketForm">
                <div class="modal-body">
                    <input type="hidden" id="assign_ticket_id" name="ticket_id">
                    <div class="form-group">
                        <label for="atribuido_user_id">Atribuir a</label>
                        <select class="form-control" id="atribuido_user_id" name="atribuido_user_id" required>
                            <option value="">Selecione um utilizador</option>
                            <?php foreach ($utilizadoresTecnicos as $utilizador): ?>
                                <option value="<?= $utilizador['id'] ?>"><?= esc($utilizador['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="novo">Novo</option>
                            <option value="em_resolucao">Em Resolução</option>
                            <option value="aguarda_peca">Aguarda Peça</option>
                            <option value="reparado">Reparado</option>
                            <option value="anulado">Anulado</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="statisticsContent">
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
                "data": 5, // Prioridade
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
            { "data": 6 }, // Criado em
            { "data": 7 }, // Criado por
            { "data": 8 }, // Atribuído a
            { 
                "data": 9, // Opções
                "orderable": false
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese.json"
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
        $('#assignTicketModal').modal('show');
    });

    // Submeter formulário de atribuição
    $('#assignTicketForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= site_url("tickets/assign") ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('button[type="submit"]', '#assignTicketForm').prop('disabled', true).text('Atribuindo...');
            },
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.messages.success || 'Ticket atribuído/atualizado com sucesso!');
                    $('#assignTicketModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error('Erro ao atribuir/atualizar ticket.');
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
});
</script>
<?= $this->endSection() ?>
