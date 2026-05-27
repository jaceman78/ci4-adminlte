
<?= $this->extend('layout/master') ?>


<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($page_title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Sugestões</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><?= esc($page_subtitle) ?></h3>
                    </div>
                    <div class="card-body">
                        <table id="sugestoesTable" class="table table-bordered table-striped table-hover nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Utilizador</th>
                                    <th>Categoria</th>
                                    <th>Título</th>
                                    <th>Prioridade</th>
                                    <th>Estado</th>
                                    <th>Anexos</th>
                                    <th>Data</th>
                                    <th style="width: 150px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Ver Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1" aria-labelledby="modalDetalhesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalhesLabel">Detalhes da Sugestão #<span id="detalhes-id"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Utilizador:</strong><br>
                        <span id="detalhes-usuario"></span>
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong><br>
                        <span id="detalhes-email"></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Categoria:</strong><br>
                        <span id="detalhes-categoria"></span>
                    </div>
                    <div class="col-md-4">
                        <strong>Prioridade:</strong><br>
                        <span id="detalhes-prioridade"></span>
                    </div>
                    <div class="col-md-4">
                        <strong>Estado:</strong><br>
                        <span id="detalhes-estado"></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Título:</strong><br>
                        <span id="detalhes-titulo"></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12">
                        <strong>Descrição:</strong><br>
                        <div id="detalhes-descricao" style="white-space: pre-wrap;"></div>
                    </div>
                </div>
                <div id="anexos-container" style="display: none;">
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Anexos:</strong>
                            <div id="detalhes-anexos" class="mt-2"></div>
                        </div>
                    </div>
                </div>
                <div id="resposta-container" style="display: none;">
                    <hr>
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Resposta:</strong><br>
                            <div id="detalhes-resposta" class="alert alert-info"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <small><strong>Respondido por:</strong> <span id="detalhes-respondedor"></span></small>
                        </div>
                        <div class="col-md-6">
                            <small><strong>Data:</strong> <span id="detalhes-data-resposta"></span></small>
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

<!-- Modal Responder -->
<div class="modal fade" id="modalResponder" tabindex="-1" aria-labelledby="modalResponderLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formResponder">
                <input type="hidden" id="responder-id">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalResponderLabel">Responder Sugestão #<span id="responder-titulo"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Estado *</label>
                        <select class="form-control" id="responder-estado" required>
                            <option value="em_analise">Em Análise</option>
                            <option value="implementada">Implementada</option>
                            <option value="rejeitada">Rejeitada</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Resposta *</label>
                        <textarea class="form-control" id="responder-resposta" rows="5" required placeholder="Digite sua resposta..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Enviar Resposta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // DataTable
    const table = $('#sugestoesTable').DataTable({
        responsive: true,
        ajax: {
            url: '<?= base_url('sugestoes/getDataTable') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { 
                data: null,
                render: function(data) {
                    return data.user_nome || 'N/A';
                }
            },
            { data: 'categoria' },
            { 
                data: 'titulo',
                render: function(data) {
                    return data.length > 50 ? data.substr(0, 50) + '...' : data;
                }
            },
            { 
                data: 'prioridade',
                render: function(data) {
                    const badges = {
                        'baixa': 'bg-secondary text-white',
                        'media': 'bg-warning text-dark',
                        'alta': 'bg-danger text-white'
                    };
                    return `<span class="badge ${badges[data]}">${data.toUpperCase()}</span>`;
                }
            },
            { 
                data: 'estado',
                render: function(data) {
                    const badges = {
                        'pendente': 'bg-secondary text-white',
                        'em_analise': 'bg-info text-white',
                        'implementada': 'bg-success text-white',
                        'rejeitada': 'bg-danger text-white'
                    };
                    const labels = {
                        'pendente': 'Pendente',
                        'em_analise': 'Em Análise',
                        'implementada': 'Implementada',
                        'rejeitada': 'Rejeitada'
                    };
                    return `<span class="badge ${badges[data]}">${labels[data]}</span>`;
                }
            },
            { 
                data: 'num_anexos',
                orderable: false,
                render: function(data, type, row) {
                    if (data > 0) {
                        return `<span class="badge bg-primary btn-ver-anexos" style="cursor: pointer;" data-id="${row.id}" title="Clique para ver ${data} anexo(s)">
                                    <i class="fas fa-paperclip"></i> ${data}
                                </span>`;
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    if (!data) return 'N/A';
                    const date = new Date(data);
                    return date.toLocaleDateString('pt-PT') + ' ' + date.toLocaleTimeString('pt-PT', {hour: '2-digit', minute:'2-digit'});
                }
            },
            { 
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info btn-ver" data-id="${row.id}" title="Ver Detalhes">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary btn-responder" data-id="${row.id}" title="Responder">
                            <i class="fas fa-reply"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-excluir" data-id="${row.id}" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            "sEmptyTable": "Sem dados disponíveis na tabela",
            "sInfo": "A mostrar _START_ até _END_ de _TOTAL_ registos",
            "sInfoEmpty": "A mostrar 0 até 0 de 0 registos",
            "sInfoFiltered": "(filtrado de _MAX_ registos no total)",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "Mostrar _MENU_ registos",
            "sLoadingRecords": "A carregar...",
            "sProcessing": "A processar...",
            "sSearch": "Pesquisar:",
            "sZeroRecords": "Não foram encontrados resultados",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sPrevious": "Anterior",
                "sNext": "Seguinte",
                "sLast": "Último"
            },
            "oAria": {
                "sSortAscending": ": ativar para ordenar a coluna de forma ascendente",
                "sSortDescending": ": ativar para ordenar a coluna de forma descendente"
            }
        }
    });

    // Ver detalhes
    $('#sugestoesTable').on('click', '.btn-ver', function() {
        const id = $(this).data('id');
        const row = table.rows().data().toArray().find(r => r.id == id);
        mostrarDetalhes(row);
    });

    // Ver anexos - abre modal de detalhes
    $('#sugestoesTable').on('click', '.btn-ver-anexos', function() {
        const id = $(this).data('id');
        const row = table.rows().data().toArray().find(r => r.id == id);
        mostrarDetalhes(row);
    });

    // Função para mostrar detalhes da sugestão
    function mostrarDetalhes(row) {
        $('#detalhes-id').text(row.id);
        $('#detalhes-usuario').text(row.user_nome || 'N/A');
        $('#detalhes-email').text(row.user_email || 'N/A');
        $('#detalhes-categoria').text(row.categoria);
        
        const prioridadeBadges = {
            'baixa': 'bg-secondary text-white',
            'media': 'bg-warning text-dark',
            'alta': 'bg-danger text-white'
        };
        $('#detalhes-prioridade').html(`<span class="badge ${prioridadeBadges[row.prioridade]}">${row.prioridade.toUpperCase()}</span>`);
        
        const estadoBadges = {
            'pendente': 'bg-secondary text-white',
            'em_analise': 'bg-info text-white',
            'implementada': 'bg-success text-white',
            'rejeitada': 'bg-danger text-white'
        };
        const estadoLabels = {
            'pendente': 'Pendente',
            'em_analise': 'Em Análise',
            'implementada': 'Implementada',
            'rejeitada': 'Rejeitada'
        };
        $('#detalhes-estado').html(`<span class="badge ${estadoBadges[row.estado]}">${estadoLabels[row.estado]}</span>`);
        
        $('#detalhes-titulo').text(row.titulo);
        $('#detalhes-descricao').text(row.descricao);
        
        // Carregar anexos
        if (row.num_anexos > 0) {
            $.ajax({
                url: '<?= base_url('sugestoes/anexos') ?>/' + row.id,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let anexosHtml = '<div class="list-group">';
                        response.data.forEach(function(anexo) {
                            const iconClass = getFileIcon(anexo.tipo_mime);
                            const fileSize = formatBytes(anexo.tamanho);
                            anexosHtml += `
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="${iconClass} me-2"></i>
                                        <span>${anexo.nome_original}</span>
                                        <small class="text-muted ms-2">(${fileSize})</small>
                                    </div>
                                    <div>
                                        <a href="<?= base_url('sugestoes/anexo/download') ?>/${anexo.id}" 
                                           class="btn btn-sm btn-info me-1" 
                                           title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger btn-excluir-anexo" 
                                                data-id="${anexo.id}" 
                                                data-sugestao-id="${row.id}"
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        anexosHtml += '</div>';
                        $('#detalhes-anexos').html(anexosHtml);
                        $('#anexos-container').show();
                    } else {
                        $('#anexos-container').hide();
                    }
                },
                error: function() {
                    $('#anexos-container').hide();
                }
            });
        } else {
            $('#anexos-container').hide();
        }
        
        if (row.resposta) {
            $('#detalhes-resposta').text(row.resposta);
            $('#detalhes-respondedor').text(row.respondedor_nome || 'N/A');
            $('#detalhes-data-resposta').text(row.respondido_em ? new Date(row.respondido_em).toLocaleString('pt-PT') : 'N/A');
            $('#resposta-container').show();
        } else {
            $('#resposta-container').hide();
        }
        
        // Abrir modal usando Bootstrap 5
        var modalDetalhes = new bootstrap.Modal(document.getElementById('modalDetalhes'));
        modalDetalhes.show();
    }

    // Responder
    $('#sugestoesTable').on('click', '.btn-responder', function() {
        const id = $(this).data('id');
        $('#responder-id').val(id);
        $('#responder-titulo').text(id);
        $('#responder-estado').val('em_analise');
        $('#responder-resposta').val('');
        
        // Abrir modal usando Bootstrap 5
        var modalResponder = new bootstrap.Modal(document.getElementById('modalResponder'));
        modalResponder.show();
    });

    // Submit resposta
    $('#formResponder').on('submit', function(e) {
        e.preventDefault();
        const id = $('#responder-id').val();
        const resposta = $('#responder-resposta').val();
        const estado = $('#responder-estado').val();

        $.ajax({
            url: '<?= base_url('sugestoes/responder') ?>/' + id,
            method: 'POST',
            data: { resposta: resposta, estado: estado },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var modalEl = document.getElementById('modalResponder');
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    
                    if (modal) {
                        // Adicionar listener para quando o modal estiver completamente fechado
                        modalEl.addEventListener('hidden.bs.modal', function onModalHidden() {
                            // Remover o listener após executar
                            modalEl.removeEventListener('hidden.bs.modal', onModalHidden);
                            
                            // Mostrar alerta e recarregar tabela após modal fechado
                            Swal.fire('Sucesso!', response.message, 'success');
                            table.ajax.reload();
                        }, { once: true });
                        
                        // Fechar modal
                        modal.hide();
                    } else {
                        // Fallback se não houver instância do modal
                        Swal.fire('Sucesso!', response.message, 'success');
                        table.ajax.reload();
                    }
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Erro ao enviar resposta', 'error');
            }
        });
    });

    // Excluir
    $('#sugestoesTable').on('click', '.btn-excluir', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Tem certeza?',
            text: "Esta ação não pode ser revertida!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('sugestoes/excluir') ?>/' + id,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Excluída!', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro!', 'Erro ao excluir sugestão', 'error');
                    }
                });
            }
        });
    });

    // Excluir anexo
    $(document).on('click', '.btn-excluir-anexo', function() {
        const anexoId = $(this).data('id');
        const sugestaoId = $(this).data('sugestao-id');
        
        Swal.fire({
            title: 'Excluir anexo?',
            text: "Esta ação não pode ser revertida!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('sugestoes/anexo/delete') ?>/' + anexoId,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Excluído!', response.message, 'success');
                            // Recarregar anexos
                            $.ajax({
                                url: '<?= base_url('sugestoes/anexos') ?>/' + sugestaoId,
                                method: 'GET',
                                dataType: 'json',
                                success: function(resp) {
                                    if (resp.success && resp.data.length > 0) {
                                        let anexosHtml = '<div class="list-group">';
                                        resp.data.forEach(function(anexo) {
                                            const iconClass = getFileIcon(anexo.tipo_mime);
                                            const fileSize = formatBytes(anexo.tamanho);
                                            anexosHtml += `
                                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="${iconClass} me-2"></i>
                                                        <span>${anexo.nome_original}</span>
                                                        <small class="text-muted ms-2">(${fileSize})</small>
                                                    </div>
                                                    <div>
                                                        <a href="<?= base_url('sugestoes/anexo/download') ?>/${anexo.id}" 
                                                           class="btn btn-sm btn-info me-1" 
                                                           title="Download">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-danger btn-excluir-anexo" 
                                                                data-id="${anexo.id}" 
                                                                data-sugestao-id="${sugestaoId}"
                                                                title="Excluir">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            `;
                                        });
                                        anexosHtml += '</div>';
                                        $('#detalhes-anexos').html(anexosHtml);
                                        $('#anexos-container').show();
                                    } else {
                                        $('#anexos-container').hide();
                                    }
                                    // Recarregar tabela para atualizar contador
                                    table.ajax.reload(null, false);
                                }
                            });
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro!', 'Erro ao excluir anexo', 'error');
                    }
                });
            }
        });
    });

    // Função para obter ícone do arquivo baseado no MIME type
    function getFileIcon(mimeType) {
        if (!mimeType) return 'fas fa-file';
        
        if (mimeType.startsWith('image/')) return 'fas fa-file-image text-primary';
        if (mimeType.includes('pdf')) return 'fas fa-file-pdf text-danger';
        if (mimeType.includes('word') || mimeType.includes('document')) return 'fas fa-file-word text-primary';
        if (mimeType.includes('excel') || mimeType.includes('spreadsheet')) return 'fas fa-file-excel text-success';
        if (mimeType.includes('text')) return 'fas fa-file-alt text-secondary';
        
        return 'fas fa-file';
    }

    // Função para formatar bytes
    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
});
</script>
<?= $this->endSection() ?>
