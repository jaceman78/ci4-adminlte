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
                            <h3 class="card-title">Os Meus Tickets</h3>
                            <div class="card-tools">
                                <a href="<?= site_url('tickets/novo') ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Novo Ticket
                                </a>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="meusTicketsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Equipamento</th>
                                        <th>Sala</th>
                                        <th>Tipo de Avaria</th>
                                        <th>Descrição</th>
                                        <th>Estado</th>
                                        <th>Prioridade</th>
                                        <th>Criado em</th>
                                        <th>Atualizado em</th>
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

<!-- Modal para Editar Ticket -->
<div class="modal fade" id="editTicketModal" tabindex="-1" role="dialog" aria-labelledby="editTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTicketModalLabel">Editar Ticket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTicketForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_ticket_id" name="ticket_id">
                    <div class="form-group">
                        <label for="edit_equipamento_id">Equipamento</label>
                        <select class="form-control" id="edit_equipamento_id" name="equipamento_id" required>
                            <option value="">Selecione um equipamento</option>
                            <!-- Opções carregadas via AJAX -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_sala_id">Sala</label>
                        <select class="form-control" id="edit_sala_id" name="sala_id" required>
                            <option value="">Selecione uma sala</option>
                            <!-- Opções carregadas via AJAX -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_tipo_avaria_id">Tipo de Avaria</label>
                        <select class="form-control" id="edit_tipo_avaria_id" name="tipo_avaria_id" required>
                            <option value="">Selecione um tipo de avaria</option>
                            <!-- Opções carregadas via AJAX -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_descricao">Descrição da Avaria</label>
                        <textarea class="form-control" id="edit_descricao" name="descricao" rows="5" required></textarea>
                        <small class="form-text text-muted">Mínimo de 10 caracteres.</small>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#meusTicketsTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?= site_url('tickets/meus-datatable') ?>",
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
            { "data": 7 }, // Atualizado em
            { 
                "data": 8, // Opções
                "orderable": false
            }
        ],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Portuguese.json"
        },
        "responsive": true,
        "autoWidth": false
    });

    // Editar Ticket
    $(document).on('click', '.edit-ticket', function() {
        var ticketId = $(this).data('id');
        loadTicketForEdit(ticketId);
    });

    // Apagar Ticket
    $(document).on('click', '.delete-ticket', function() {
        var ticketId = $(this).data('id');
        if (confirm('Tem certeza de que deseja apagar este ticket?')) {
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
                    $('#edit_descricao').val(ticket.descricao);
                    
                    // Carregar opções dos selects se necessário
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
    }
});
</script>
<?= $this->endSection() ?>
