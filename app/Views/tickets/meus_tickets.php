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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTicketForm">
                <div class="modal-body">
                    <!-- Loading spinner -->
                    <div id="editTicketLoading" class="text-center" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">A carregar dados do ticket...</p>
                    </div>
                    
                    <!-- Conteúdo do formulário -->
                    <div id="editTicketFormContent">
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Alterar Prioridade -->
<div class="modal fade" id="modalPrioridadeMeus" tabindex="-1" aria-labelledby="modalPrioridadeMeusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalPrioridadeMeusLabel"><i class="fas fa-flag"></i> Alterar Prioridade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nova_prioridade_meus">Selecione a nova prioridade:</label>
                    <select class="form-control" id="nova_prioridade_meus">
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                        <option value="critica">Crítica</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarPrioridadeMeus">Salvar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Estilos para badges na tabela */
    #meusTicketsTable .badge {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        font-weight: 600;
        border-radius: 0.25rem;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }
    
    /* Garantir texto branco em todos os badges de estado */
    #meusTicketsTable .text-white {
        color: #ffffff !important;
    }
    
    /* Melhorar legibilidade da coluna equipamento */
    #meusTicketsTable td:first-child {
        line-height: 1.6;
    }
    
    #meusTicketsTable td:first-child strong {
        color: #007bff;
        font-size: 0.95rem;
    }
    
    #meusTicketsTable td:first-child small {
        font-size: 0.8rem;
    }
    
    /* Cores das linhas por estado */
    #meusTicketsTable tbody tr {
        transition: background-color 0.2s ease;
    }
    
    #meusTicketsTable tbody tr:hover {
        filter: brightness(0.95);
    }
</style>
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
                "data": 4 // Estado (badge já vem formatado do servidor)
            },
            { 
                "data": 5, // Prioridade
                "render": function(data, type, row) {
                    var badgeStyle = '';
                    var prioridadeTexto = '';
                    var ticketId = row[9]; // ID do ticket
                    var estadoCodigo = row[10]; // Código do estado (texto)
                    var estadoFinal = row[11]; // Flag se é estado final
                    var isAdmin = <?= session()->get('level') >= 8 ? 'true' : 'false' ?>;
                    
                    switch(data) {
                        case 'baixa':
                            badgeStyle = 'background-color: #28a745; color: white;';
                            prioridadeTexto = 'Baixa';
                            break;
                        case 'media':
                            badgeStyle = 'background-color: #ffc107; color: #000;';
                            prioridadeTexto = 'Média';
                            break;
                        case 'alta':
                            badgeStyle = 'background-color: #fd7e14; color: white;';
                            prioridadeTexto = 'Alta';
                            break;
                        case 'urgente':
                        case 'critica':
                            badgeStyle = 'background-color: #dc3545; color: white;';
                            prioridadeTexto = 'Crítica';
                            break;
                        default:
                            badgeStyle = 'background-color: #6c757d; color: white;';
                            prioridadeTexto = data;
                    }
                    
                    var cursor = (isAdmin && !estadoFinal) ? 'cursor: pointer;' : '';
                    var opacity = (estadoFinal) ? 'opacity: 0.7;' : '';
                    var title = (isAdmin && !estadoFinal) 
                        ? 'Clique para alterar a prioridade' 
                        : (estadoFinal ? 'Não é possível alterar prioridade de ticket finalizado' : '');
                    
                    return '<span class="badge badge-prioridade-meus" style="' + badgeStyle + ' ' + cursor + ' ' + opacity + '" ' +
                           'data-ticket-id="' + ticketId + '" ' +
                           'data-prioridade="' + data + '" ' +
                           'data-estado="' + estadoCodigo + '" ' +
                           'data-estado-final="' + estadoFinal + '" ' +
                           'title="' + title + '">' + prioridadeTexto + '</span>';
                }
            },
            { "data": 6 }, // Criado em
            { "data": 7 }, // Atualizado em
            { 
                "data": 8, // Opções
                "orderable": false
            },
            { 
                "data": 9, // ID do ticket (coluna oculta)
                "visible": false,
                "searchable": false
            },
            { 
                "data": 10, // Código do estado (coluna oculta)
                "visible": false,
                "searchable": false
            },
            { 
                "data": 11, // Flag estado final (coluna oculta)
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
        var formData = new FormData(this);
        
        $.ajax({
            url: '<?= site_url("tickets/update/") ?>' + ticketId,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                $('button[type="submit"]', '#editTicketForm').prop('disabled', true).text('Atualizando...');
            },
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.message || 'Ticket atualizado com sucesso!');
                    $('#editTicketModal').modal('hide');
                    table.ajax.reload();
                } else {
                    toastr.error('Erro ao atualizar ticket.');
                }
            },
            error: function(xhr) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.messages && response.messages.error) {
                        if (typeof response.messages.error === 'object') {
                            $.each(response.messages.error, function(field, message) {
                                toastr.error(message);
                            });
                        } else {
                            toastr.error(response.messages.error);
                        }
                    } else if (response.message) {
                        toastr.error(response.message);
                    } else {
                        toastr.error('Erro interno do servidor.');
                    }
                } catch(e) {
                    toastr.error('Erro ao processar resposta do servidor.');
                }
            },
            complete: function() {
                $('button[type="submit"]', '#editTicketForm').prop('disabled', false).text('Atualizar Ticket');
            }
        });
    });

    function loadTicketForEdit(ticketId) {
        // Mostrar loading e abrir modal
        $('#editTicketLoading').show();
        $('#editTicketFormContent').hide();
        $('#editTicketModal').modal('show');
        
        // Carregar dados do ticket
        $.ajax({
            url: '<?= site_url("tickets/get/") ?>' + ticketId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 200 && response.data) {
                    var ticket = response.data;
                    
                    // Preencher campos simples
                    $('#edit_ticket_id').val(ticket.id);
                    $('#edit_descricao').val(ticket.descricao);
                    
                    // Carregar selects e depois definir valores
                    loadSelectOptions(ticket).done(function() {
                        $('#editTicketLoading').hide();
                        $('#editTicketFormContent').show();
                    });
                } else {
                    toastr.error('Erro ao carregar dados do ticket.');
                    $('#editTicketModal').modal('hide');
                }
            },
            error: function(xhr) {
                console.error('Erro ao carregar ticket:', xhr);
                try {
                    var response = JSON.parse(xhr.responseText);
                    toastr.error(response.message || 'Erro ao carregar dados do ticket.');
                } catch (e) {
                    toastr.error('Erro ao carregar dados do ticket.');
                }
                $('#editTicketModal').modal('hide');
            }
        });
    }

    function deleteTicket(ticketId) {
        $.ajax({
            url: '<?= site_url("tickets/delete/") ?>' + ticketId,
            type: 'DELETE',
            dataType: 'json',
            success: function(response) {
                // respondDeleted retorna com status HTTP 200, response tem a mensagem
                toastr.success(response.message || 'Ticket apagado com sucesso!');
                table.ajax.reload();
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

    function loadSelectOptions(ticket) {
        var ajaxCalls = [];
        
        // Carregar equipamentos
        ajaxCalls.push(
            $.ajax({
                url: '<?= site_url("equipamentos/all") ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#edit_equipamento_id').empty().append('<option value="">Selecione um equipamento</option>');
                    $.each(data, function(key, value) {
                        var equipamentoLabel = (value.tipo_nome || 'N/A') + ' - ' + (value.marca || '') + ' ' + (value.modelo || '');
                        if (value.numero_serie) {
                            equipamentoLabel += ' (S/N: ' + value.numero_serie + ')';
                        }
                        $('#edit_equipamento_id').append('<option value="' + value.id + '">' + equipamentoLabel + '</option>');
                    });
                    if (ticket) $('#edit_equipamento_id').val(ticket.equipamento_id);
                }
            })
        );

        // Carregar salas
        ajaxCalls.push(
            $.ajax({
                url: '<?= site_url("salas/all") ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#edit_sala_id').empty().append('<option value="">Selecione uma sala</option>');
                    $.each(data, function(key, value) {
                        var salaLabel = value.codigo_sala;
                        if (value.escola_nome) {
                            salaLabel += ' (' + value.escola_nome + ')';
                        }
                        $('#edit_sala_id').append('<option value="' + value.id + '">' + salaLabel + '</option>');
                    });
                    if (ticket) $('#edit_sala_id').val(ticket.sala_id);
                }
            })
        );

        // Carregar tipos de avaria
        ajaxCalls.push(
            $.ajax({
                url: '<?= site_url("tipos-avaria/all") ?>',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#edit_tipo_avaria_id').empty().append('<option value="">Selecione um tipo de avaria</option>');
                    $.each(data, function(key, value) {
                        $('#edit_tipo_avaria_id').append('<option value="' + value.id + '">' + value.descricao + '</option>');
                    });
                    if (ticket) $('#edit_tipo_avaria_id').val(ticket.tipo_avaria_id);
                }
            })
        );

        // Retornar promise que aguarda todos os AJAX
        return $.when.apply($, ajaxCalls);
    }
    
    // Variável global para armazenar o ticket ID atual
    var currentTicketIdPrioridadeMeus = null;
    
    // Alterar Prioridade (apenas admins e tickets não finalizados)
    $(document).on('click', '.badge-prioridade-meus', function() {
        var isAdmin = <?= session()->get('level') >= 8 ? 'true' : 'false' ?>;
        
        if (!isAdmin) {
            toastr.warning('Apenas administradores podem alterar a prioridade.');
            return;
        }
        
        var estadoFinal = $(this).data('estado-final');
        
        if (estadoFinal) {
            toastr.warning('Não é possível alterar a prioridade de um ticket finalizado.');
            return;
        }
        
        currentTicketIdPrioridadeMeus = $(this).data('ticket-id');
        var prioridadeAtual = $(this).data('prioridade');
        
        $('#nova_prioridade_meus').val(prioridadeAtual);
        
        const modal = new bootstrap.Modal(document.getElementById('modalPrioridadeMeus'));
        modal.show();
    });
    
    $('#btnSalvarPrioridadeMeus').on('click', function() {
        const novaPrioridade = $('#nova_prioridade_meus').val();
        
        if (!currentTicketIdPrioridadeMeus) {
            toastr.error('Erro: ID do ticket não encontrado.');
            return;
        }
        
        $.ajax({
            url: '<?= site_url("tickets/updatePrioridade") ?>',
            type: 'POST',
            data: {
                ticket_id: currentTicketIdPrioridadeMeus,
                prioridade: novaPrioridade
            },
            dataType: 'json',
            beforeSend: function() {
                $('#btnSalvarPrioridadeMeus').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');
            },
            success: function(response) {
                console.log('Success response:', response);
                
                if (response.success) {
                    const modalEl = document.getElementById('modalPrioridadeMeus');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                    
                    toastr.success(response.message || 'Prioridade atualizada com sucesso.');
                    
                    // Recarregar tabela
                    meusTicketsTable.ajax.reload(null, false);
                    
                    currentTicketIdPrioridadeMeus = null;
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
                $('#btnSalvarPrioridadeMeus').prop('disabled', false).html('Salvar');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
