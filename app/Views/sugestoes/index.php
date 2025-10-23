
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
                        <table id="sugestoesTable" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">ID</th>
                                    <th>Utilizador</th>
                                    <th>Categoria</th>
                                    <th>Título</th>
                                    <th>Prioridade</th>
                                    <th>Estado</th>
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
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes da Sugestão #<span id="detalhes-id"></span></h5>
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
<div class="modal fade" id="modalResponder" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formResponder">
                <input type="hidden" id="responder-id">
                <div class="modal-header">
                    <h5 class="modal-title">Responder Sugestão #<span id="responder-titulo"></span></h5>
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
        ajax: {
            url: '<?= base_url('sugestoes/getDataTable') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { 
                data: null,
                render: function(data) {
                    return data.user_name || 'N/A';
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
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        }
    });

    // Ver detalhes
    $('#sugestoesTable').on('click', '.btn-ver', function() {
        const id = $(this).data('id');
        const row = table.rows().data().toArray().find(r => r.id == id);
        
        $('#detalhes-id').text(row.id);
        $('#detalhes-usuario').text(row.user_name || 'N/A');
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
        
        if (row.resposta) {
            $('#detalhes-resposta').text(row.resposta);
            $('#detalhes-respondedor').text(row.respondedor_name || 'N/A');
            $('#detalhes-data-resposta').text(row.respondido_em ? new Date(row.respondido_em).toLocaleString('pt-PT') : 'N/A');
            $('#resposta-container').show();
        } else {
            $('#resposta-container').hide();
        }
        
        // Abrir modal usando Bootstrap 5
        var modalDetalhes = new bootstrap.Modal(document.getElementById('modalDetalhes'));
        modalDetalhes.show();
    });

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
});
</script>
<?= $this->endSection() ?>
