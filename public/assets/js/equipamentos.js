/**
 * Gestão de Equipamentos
 * Sistema completo com gestão de salas via equipamentos_sala
 */

let table;
let equipamentoToDelete = null;

$(document).ready(function() {
    initializeDataTable();
    initializeFormHandlers();
    initializeCascadingDropdowns();
    loadStatistics();
});

/**
 * Inicializar DataTable
 */
function initializeDataTable() {
    table = $('#equipamentosTable').DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": baseUrl + "equipamentos/getDataTable",
            "type": "POST"
        },
        "columns": [
            { "data": "id", "width": "50px" },
            { "data": "escola_nome" },
            { "data": "sala_nome" },
            { "data": "tipo_nome" },
            { "data": "marca_modelo" },
            { "data": "numero_serie" },
            { 
                "data": "estado",
                "render": function(data, type, row) {
                    return getEstadoBadge(data);
                }
            },
            {
                "data": null,
                "orderable": false,
                "render": function(data, type, row) {
                    let buttons = '<div class="btn-group" role="group">';
                    
                    // Botão Ver
                    buttons += '<button type="button" class="btn btn-sm btn-info" onclick="viewEquipamento(' + row.id + ')" title="Ver Detalhes">' +
                               '<i class="fas fa-eye"></i></button>';
                    
                    // Botão Editar
                    buttons += '<button type="button" class="btn btn-sm btn-warning" onclick="editEquipamento(' + row.id + ')" title="Editar Equipamento">' +
                               '<i class="fas fa-edit"></i></button>';
                    
                    // Dropdown para gestão de sala
                    buttons += '<div class="btn-group" role="group">' +
                               '<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" title="Gerir Localização">' +
                               '<i class="fas fa-map-marker-alt"></i></button>' +
                               '<ul class="dropdown-menu">';
                    
                    if (row.tem_sala) {
                        buttons += '<li><a class="dropdown-item" href="#" onclick="editarSalaEquipamento(' + row.id + ', event)"><i class="fas fa-exchange-alt"></i> Mudar Sala</a></li>';
                        buttons += '<li><a class="dropdown-item" href="#" onclick="removerSalaEquipamento(' + row.id + ', event)"><i class="fas fa-times"></i> Remover Sala</a></li>';
                    } else {
                        buttons += '<li><a class="dropdown-item" href="#" onclick="atribuirSalaEquipamento(' + row.id + ', event)"><i class="fas fa-plus"></i> Atribuir Sala</a></li>';
                    }
                    
                    buttons += '<li><hr class="dropdown-divider"></li>';
                    buttons += '<li><a class="dropdown-item" href="#" onclick="verHistorico(' + row.id + ', event)"><i class="fas fa-history"></i> Histórico</a></li>';
                    buttons += '</ul></div>';
                    
                    // Botão Eliminar
                    buttons += '<button type="button" class="btn btn-sm btn-danger" onclick="deleteEquipamento(' + row.id + ')" title="Eliminar">' +
                               '<i class="fas fa-trash"></i></button>';
                    
                    buttons += '</div>';
                    return buttons;
                }
            }
        ],
        "language": {
            "url": 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "order": [[0, 'desc']]
    });
}

/**
 * Inicializar manipuladores de formulários
 */
function initializeFormHandlers() {
    // Formulário de criar/editar equipamento
    $('#equipamentoForm').on('submit', function(e) {
        e.preventDefault();
        submitEquipamentoForm();
    });
    
    // Formulário de gerir sala
    $('#gerirSalaForm').on('submit', function(e) {
        e.preventDefault();
        submitGerirSalaForm();
    });
    
    // Botão de confirmar exclusão
    $('#confirmDeleteBtn').on('click', function() {
        if (equipamentoToDelete) {
            executeDelete();
        }
    });
}

/**
 * Inicializar dropdowns em cascata
 */
function initializeCascadingDropdowns() {
    // Modal de criar/editar equipamento
    $('#escola_id').change(function() {
        const escolaId = $(this).val();
        const salaSelect = $('#sala_id');
        
        salaSelect.html('<option value="">Carregando...</option>').prop('disabled', true);
        
        if (escolaId) {
            $.get(baseUrl + 'salas/getByEscola/' + escolaId, function(data) {
                salaSelect.html('<option value="">Selecione uma sala</option>');
                data.forEach(function(sala) {
                    salaSelect.append(`<option value="${sala.id}">${sala.codigo_sala}</option>`);
                });
                salaSelect.prop('disabled', false);
                
                // Mostrar seção de motivo se tiver sala selecionada
                if (data.length > 0) {
                    $('#motivo_section').show();
                }
            }).fail(function() {
                salaSelect.html('<option value="">Erro ao carregar salas</option>');
                showToast('error', 'Erro ao carregar salas');
            });
        } else {
            salaSelect.html('<option value="">Selecione primeiro uma escola</option>');
            $('#motivo_section').hide();
        }
    });
    
    // Modal de gerir sala
    $('#gerir_escola_id').change(function() {
        const escolaId = $(this).val();
        const salaSelect = $('#gerir_sala_id');
        
        salaSelect.html('<option value="">Carregando...</option>').prop('disabled', true);
        
        if (escolaId) {
            $.get(baseUrl + 'salas/getByEscola/' + escolaId, function(data) {
                salaSelect.html('<option value="">Selecione uma sala</option>');
                data.forEach(function(sala) {
                    salaSelect.append(`<option value="${sala.id}">${sala.codigo_sala}</option>`);
                });
                salaSelect.prop('disabled', false);
            }).fail(function() {
                salaSelect.html('<option value="">Erro ao carregar salas</option>');
                showToast('error', 'Erro ao carregar salas');
            });
        } else {
            salaSelect.html('<option value="">Selecione primeiro uma escola</option>');
        }
    });
}

/**
 * Abrir modal para criar novo equipamento
 */
function openCreateModal() {
    $('#equipamentoModalLabel').text('Novo Equipamento');
    $('#equipamentoForm')[0].reset();
    $('#equipamento_id').val('');
    $('#sala_id').html('<option value="">Selecione primeiro uma escola</option>').prop('disabled', true);
    $('#motivo_section').hide();
    $('#saveButton').text('Guardar');
    $('#equipamentoModal').modal('show');
}

/**
 * Editar equipamento
 */
function editEquipamento(id) {
    $.get(baseUrl + 'equipamentos/getEquipamentoCompleto/' + id, function(data) {
        $('#equipamentoModalLabel').text('Editar Equipamento');
        $('#equipamento_id').val(data.id);
        $('#tipo_id').val(data.tipo_id);
        $('#marca').val(data.marca);
        $('#modelo').val(data.modelo);
        $('#numero_serie').val(data.numero_serie);
        $('#estado').val(data.estado);
        $('#data_aquisicao').val(data.data_aquisicao);
        $('#observacoes').val(data.observacoes);
        
        // Se tem sala, carregar escola e sala
        if (data.escola_id) {
            $('#escola_id').val(data.escola_id).trigger('change');
            
            // Aguardar o carregamento das salas e selecionar
            setTimeout(function() {
                $('#sala_id').val(data.sala_atual_id);
            }, 500);
            
            $('#motivo_section').show();
        }
        
        $('#saveButton').text('Atualizar');
        $('#equipamentoModal').modal('show');
    }).fail(function(xhr) {
        showToast('error', 'Erro ao carregar dados do equipamento');
    });
}

/**
 * Ver detalhes do equipamento
 */
function viewEquipamento(id) {
    $.get(baseUrl + 'equipamentos/getEquipamentoCompleto/' + id, function(data) {
        $('#view_sala').text(data.sala_atual ? data.sala_atual.codigo_sala + ' (' + data.sala_atual.escola_nome + ')' : 'Sem atribuição');
        $('#view_tipo').text(data.tipo_nome || '');
        $('#view_marca').text(data.marca || '');
        $('#view_modelo').text(data.modelo || '');
        $('#view_numero_serie').text(data.numero_serie || '');
        $('#view_estado').html(getEstadoBadge(data.estado));
        $('#view_data_aquisicao').text(data.data_aquisicao || '');
        $('#view_observacoes').text(data.observacoes || '');
        $('#viewEquipamentoModal').modal('show');
    }).fail(function() {
        showToast('error', 'Erro ao carregar dados do equipamento');
    });
}

/**
 * Submeter formulário de equipamento
 */
function submitEquipamentoForm() {
    const formData = new FormData($('#equipamentoForm')[0]);
    const equipamentoId = $('#equipamento_id').val();
    const url = equipamentoId ? 
        baseUrl + 'equipamentos/update/' + equipamentoId : 
        baseUrl + 'equipamentos/createWithSala';
    
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
            handleAjaxError(xhr);
        }
    });
}

/**
 * Atribuir sala a equipamento
 */
function atribuirSalaEquipamento(id, event) {
    event.preventDefault();
    
    $.get(baseUrl + 'equipamentos/getEquipamento/' + id, function(data) {
        $('#gerirSalaModalLabel').text('Atribuir Sala');
        $('#gerir_equipamento_id').val(id);
        $('#gerir_action').val('atribuir');
        $('#gerir_equipamento_info').text(`${data.marca || ''} ${data.modelo || ''} ${data.numero_serie ? '(SN: ' + data.numero_serie + ')' : ''}`);
        
        $('#sala_atual_info').hide();
        $('#nova_localizacao_section').show();
        $('#remover_sala_section').hide();
        
        $('#gerir_escola_id').val('').prop('required', true);
        $('#gerir_sala_id').html('<option value="">Selecione primeiro uma escola</option>').prop('disabled', true).prop('required', true);
        $('#gerir_motivo').val('').prop('required', true);
        
        $('#gerirSalaBtn').text('Atribuir Sala').removeClass('btn-danger').addClass('btn-primary');
        $('#gerirSalaModal').modal('show');
    });
}

/**
 * Editar/Mover sala do equipamento
 */
function editarSalaEquipamento(id, event) {
    event.preventDefault();
    
    $.get(baseUrl + 'equipamentos/getEquipamentoCompleto/' + id, function(data) {
        $('#gerirSalaModalLabel').text('Mudar Sala');
        $('#gerir_equipamento_id').val(id);
        $('#gerir_action').val('editar');
        $('#gerir_equipamento_info').text(`${data.marca || ''} ${data.modelo || ''} ${data.numero_serie ? '(SN: ' + data.numero_serie + ')' : ''}`);
        
        if (data.sala_atual) {
            $('#gerir_sala_atual').text(data.sala_atual.codigo_sala + ' (' + data.sala_atual.escola_nome + ')');
            $('#sala_atual_info').show();
        }
        
        $('#nova_localizacao_section').show();
        $('#remover_sala_section').hide();
        
        $('#gerir_escola_id').val('').prop('required', true);
        $('#gerir_sala_id').html('<option value="">Selecione primeiro uma escola</option>').prop('disabled', true).prop('required', true);
        $('#gerir_motivo').val('').prop('required', true);
        
        $('#gerirSalaBtn').text('Mover para Nova Sala').removeClass('btn-danger').addClass('btn-primary');
        $('#gerirSalaModal').modal('show');
    });
}

/**
 * Remover sala do equipamento
 */
function removerSalaEquipamento(id, event) {
    event.preventDefault();
    
    $.get(baseUrl + 'equipamentos/getEquipamentoCompleto/' + id, function(data) {
        $('#gerirSalaModalLabel').text('Remover de Sala');
        $('#gerir_equipamento_id').val(id);
        $('#gerir_action').val('remover');
        $('#gerir_equipamento_info').text(`${data.marca || ''} ${data.modelo || ''} ${data.numero_serie ? '(SN: ' + data.numero_serie + ')' : ''}`);
        
        if (data.sala_atual) {
            $('#gerir_sala_atual').text(data.sala_atual.codigo_sala + ' (' + data.sala_atual.escola_nome + ')');
            $('#sala_atual_info').show();
        }
        
        $('#nova_localizacao_section').hide();
        $('#remover_sala_section').show();
        
        $('#gerir_motivo_remocao').val('').prop('required', true);
        
        $('#gerirSalaBtn').text('Confirmar Remoção').removeClass('btn-primary').addClass('btn-danger');
        $('#gerirSalaModal').modal('show');
    });
}

/**
 * Submeter formulário de gerir sala
 */
function submitGerirSalaForm() {
    const action = $('#gerir_action').val();
    const equipamentoId = $('#gerir_equipamento_id').val();
    let url, data;
    
    switch(action) {
        case 'atribuir':
            url = baseUrl + 'equipamentos/atribuirSala';
            data = {
                equipamento_id: equipamentoId,
                sala_id: $('#gerir_sala_id').val(),
                motivo_movimentacao: $('#gerir_motivo').val()
            };
            break;
            
        case 'editar':
            url = baseUrl + 'equipamentos/editarSala';
            data = {
                equipamento_id: equipamentoId,
                sala_id: $('#gerir_sala_id').val(),
                motivo_movimentacao: $('#gerir_motivo').val()
            };
            break;
            
        case 'remover':
            url = baseUrl + 'equipamentos/removerSala';
            data = {
                equipamento_id: equipamentoId,
                motivo_movimentacao: $('#gerir_motivo_remocao').val()
            };
            break;
    }
    
    $.post(url, data, function(response) {
        // Se houver aviso sobre tickets em reparação
        if (response.warning) {
            // Fechar modal de gestão de sala primeiro
            $('#gerirSalaModal').modal('hide');
            
            // Aguardar animação de fechamento antes de abrir a nova modal
            setTimeout(function() {
                // Preencher dados na modal
                $('#tickets_count_text').text(response.tickets_count);
                $('#sala_atual_text').text(response.sala_atual);
                $('#sala_nova_text').text(response.sala_nova);
                
                // Guardar dados para reenvio
                $('#confirmMudancaTicketsBtn').data('url', url);
                $('#confirmMudancaTicketsBtn').data('data', data);
                
                // Mostrar modal de confirmação
                $('#confirmMudancaTicketsModal').modal('show');
            }, 300);
            
            return;
        }
        
        // Sucesso normal
        $('#gerirSalaModal').modal('hide');
        table.ajax.reload();
        showToast('success', response.message);
    }).fail(function(xhr) {
        handleAjaxError(xhr);
    });
}

// Handler para confirmar mudança de sala com tickets
$(document).on('click', '#confirmMudancaTicketsBtn', function() {
    const btn = $(this);
    const url = btn.data('url');
    const data = btn.data('data');
    
    // Adicionar flag para forçar mudança
    data.forcar_mudanca = 'true';
    
    // Desabilitar botão
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
    
    $.post(url, data, function(response) {
        $('#confirmMudancaTicketsModal').modal('hide');
        table.ajax.reload();
        showToast('success', response.message);
        
        // Resetar botão
        btn.prop('disabled', false).html('<i class="fas fa-check"></i> Continuar com Mudança');
    }).fail(function(xhr) {
        handleAjaxError(xhr);
        
        // Resetar botão
        btn.prop('disabled', false).html('<i class="fas fa-check"></i> Continuar com Mudança');
    });
});

// Handler para quando a modal de confirmação de tickets é fechada sem confirmar
$('#confirmMudancaTicketsModal').on('hidden.bs.modal', function() {
    // Se o botão ainda não foi usado (não está disabled), significa que foi cancelado
    const btn = $('#confirmMudancaTicketsBtn');
    if (!btn.prop('disabled')) {
        // Reabrir a modal de gestão de sala para o usuário poder fazer outra ação
        setTimeout(function() {
            $('#gerirSalaModal').modal('show');
        }, 200);
    }
});

/**
 * Ver histórico de movimentações
 */
function verHistorico(id, event) {
    event.preventDefault();
    
    $.get(baseUrl + 'equipamentos/getHistorico/' + id, function(historico) {
        let html = '<div class="timeline">';
        
        if (historico.length === 0) {
            html += '<p class="text-muted">Sem histórico de movimentações</p>';
        } else {
            historico.forEach(function(item) {
                const dataEntrada = new Date(item.data_entrada).toLocaleString('pt-PT');
                const dataSaida = item.data_saida ? new Date(item.data_saida).toLocaleString('pt-PT') : 'Atual';
                
                html += '<div class="mb-3 border-left pl-3">';
                html += '<h6>' + item.codigo_sala + '</h6>';
                html += '<p class="mb-1"><strong>Entrada:</strong> ' + dataEntrada + '</p>';
                html += '<p class="mb-1"><strong>Saída:</strong> ' + dataSaida + '</p>';
                if (item.motivo_movimentacao) {
                    html += '<p class="mb-1"><strong>Motivo:</strong> ' + item.motivo_movimentacao + '</p>';
                }
                if (item.movimentado_por) {
                    html += '<p class="mb-0 text-muted"><small>Por: ' + item.movimentado_por + '</small></p>';
                }
                html += '</div>';
            });
        }
        
        html += '</div>';
        
        // Usar a modal de estatísticas para mostrar o histórico
        $('#estatisticasModalLabel').text('Histórico de Movimentações');
        $('#estatisticasModalBody').html(html);
        $('#estatisticasModal').modal('show');
    }).fail(function() {
        showToast('error', 'Erro ao carregar histórico');
    });
}

/**
 * Confirmar exclusão de equipamento
 */
function deleteEquipamento(id) {
    equipamentoToDelete = id;
    $('#confirmDeleteModal').modal('show');
}

/**
 * Executar exclusão
 */
function executeDelete() {
    $.post(baseUrl + 'equipamentos/delete/' + equipamentoToDelete, function(response) {
        table.ajax.reload();
        loadStatistics();
        showToast('success', response.message || 'Equipamento eliminado com sucesso!');
    }).fail(function(xhr) {
        handleAjaxError(xhr);
    }).always(function() {
        $('#confirmDeleteModal').modal('hide');
        equipamentoToDelete = null;
    });
}

/**
 * Carregar estatísticas
 */
function loadStatistics(showModal = false) {
    $.get(baseUrl + 'equipamentos/getStatistics', function(data) {
        // Atualizar info-boxes
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
        
        // Preencher modal de estatísticas
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
        
        if (showModal) {
            $('#estatisticasModal').modal('show');
        }
    }).fail(function() {
        console.error('Erro ao carregar estatísticas');
    });
}

/**
 * Get badge HTML for estado
 */
function getEstadoBadge(estado) {
    let badgeClass = '';
    let text = '';
    
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
            badgeClass = 'bg-light text-dark';
            text = estado;
    }
    
    return '<span class="badge ' + badgeClass + '">' + text + '</span>';
}

/**
 * Handle AJAX errors
 */
function handleAjaxError(xhr) {
    try {
        const response = JSON.parse(xhr.responseText);
        if (response.messages) {
            const errors = Object.values(response.messages).join('<br>');
            showToast('error', errors);
        } else {
            showToast('error', response.message || 'Erro ao processar a solicitação.');
        }
    } catch(e) {
        showToast('error', 'Erro ao processar a solicitação.');
    }
}

/**
 * Show toast notification
 * Usa o sistema global de toasts (toast-notifications.js)
 */
function showToast(type, message) {
    // Usar sistema global se disponível
    if (window.toast && window.toast.show) {
        window.toast.show(type, message);
    } else {
        // Fallback para toastr direto
        switch(type) {
            case 'success':
                toastr.success(message, 'Sucesso!');
                break;
            case 'error':
                toastr.error(message, 'Erro!');
                break;
            case 'warning':
                toastr.warning(message, 'Atenção!');
                break;
            case 'info':
                toastr.info(message, 'Informação');
                break;
            default:
                toastr.info(message);
        }
    }
}
