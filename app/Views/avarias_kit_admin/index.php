<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>Avarias Reportadas<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$userLevel = session()->get('LoggedUserData')['level'] ?? 0;
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestão de Avarias Reportadas - Kit Digital</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('kit-digital-admin') ?>">Kit Digital</a></li>
                    <li class="breadcrumb-item active">Avarias Reportadas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filtros por Estado -->
        <div class="mb-3">
            <div class="btn-group" role="group" aria-label="Filtros de Estado">
                <button type="button" class="btn btn-outline-secondary filtro-estado active" data-estado="">
                    Todos <span id="count_all" class="badge rounded-pill bg-secondary ms-1"><?= $stats['total'] ?></span>
                </button>
                <button type="button" class="btn btn-outline-info filtro-estado" data-estado="novo">
                    Novos <span id="count_novo" class="badge rounded-pill bg-info ms-1"><?= $stats['novo'] ?></span>
                </button>
                <button type="button" class="btn btn-outline-warning filtro-estado" data-estado="lido">
                    Lidos <span id="count_lido" class="badge rounded-pill bg-warning text-dark ms-1"><?= $stats['lido'] ?></span>
                </button>
                <button type="button" class="btn btn-outline-primary filtro-estado" data-estado="a_analisar">
                    A Analisar <span id="count_a_analisar" class="badge rounded-pill bg-primary ms-1"><?= $stats['a_analisar'] ?></span>
                </button>
                <button type="button" class="btn btn-outline-info filtro-estado" data-estado="por_levantar">
                    Por Levantar <span id="count_por_levantar" class="badge rounded-pill bg-info text-dark ms-1"><?= $stats['por_levantar'] ?></span>
                </button>
                <button type="button" class="btn btn-outline-dark filtro-estado" data-estado="terminado">
                    Terminados <span id="count_terminado" class="badge rounded-pill bg-dark ms-1"><?= $stats['terminado'] ?></span>
                </button>
                <button type="button" class="btn btn-outline-danger filtro-estado" data-estado="rejeitado">
                    Rejeitados <span id="count_rejeitado" class="badge rounded-pill bg-danger ms-1"><?= $stats['rejeitado'] ?></span>
                </button>
                <button type="button" class="btn btn-outline-secondary filtro-estado" data-estado="anulado">
                    Anulados <span id="count_anulado" class="badge rounded-pill bg-secondary ms-1"><?= $stats['anulado'] ?></span>
                </button>
            </div>
            <a id="exportCsvBtn" class="btn btn-sm btn-outline-primary ms-2" href="<?= base_url('avarias-kit-admin/export') ?>">
                <i class="bi bi-download"></i> Exportar CSV
            </a>
        </div>

        <!-- DataTable -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Avarias Reportadas</h3>
            </div>
            <div class="card-body">
                <table id="avariasTable" class="table table-bordered table-hover nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nº Aluno</th>
                            <th>Nome</th>
                            <th>Turma</th>
                            <th>NIF</th>
                            <th>Estado</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Modal Detalhes -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes da Avaria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <p class="text-center"><i class="bi bi-hourglass-split"></i> A carregar...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Atualizar Estado -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Atualizar Estado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="update_avaria_id">
                <div class="mb-3">
                    <label for="novo_estado" class="form-label">Novo Estado *</label>
                    <select class="form-select" id="novo_estado" required>
                        <option value="">Selecione...</option>
                        <option value="novo">Novo</option>
                        <option value="lido">Lido</option>
                        <option value="a analisar">A Analisar</option>
                        <option value="por levantar">Por Levantar</option>
                        <option value="terminado">Terminado</option>
                        <option value="rejeitado">Rejeitado</option>
                        <option value="anulado">Anulado</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="obs_estado" class="form-label">Observações</label>
                    <textarea class="form-control" id="obs_estado" rows="3" placeholder="Observações sobre a mudança de estado (opcional)"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmUpdate">Atualizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminação -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmar Eliminação</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem a certeza que deseja eliminar este registo de avaria?</p>
                <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> Esta ação não pode ser revertida.</p>
                <input type="hidden" id="delete_avaria_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmDelete">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inicializar filtro
    window.__filtroEstado = '';
    
    // DataTable
    var table = $('#avariasTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        order: [[6, 'desc']], // Ordenar por data decrescente
        ajax: {
            url: '<?= base_url('avarias-kit-admin/get-data') ?>',
            type: 'POST',
            data: function(d) {
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                d.estado = window.__filtroEstado || '';
            }
        },
        columns: [
            { data: 'id', visible: false },
            { data: 'numero_aluno' },
            { data: 'nome' },
            { data: 'turma' },
            { data: 'nif' },
            { 
                data: 'estado',
                render: function(data) {
                    var badges = {
                        'novo': '<span class="badge bg-info">Novo</span>',
                        'lido': '<span class="badge bg-warning text-dark">Lido</span>',
                        'a analisar': '<span class="badge bg-primary">A Analisar</span>',
                        'por levantar': '<span class="badge bg-info text-dark">Por Levantar</span>',
                        'terminado': '<span class="badge bg-dark">Terminado</span>',
                        'rejeitado': '<span class="badge bg-danger">Rejeitado</span>',
                        'anulado': '<span class="badge bg-secondary">Anulado</span>'
                    };
                    return badges[data] || '<span class="badge bg-light text-dark">'+data+'</span>';
                }
            },
            { data: 'created_at' },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-info" onclick="viewAvaria(${row.id})" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning" onclick="updateStatus(${row.id})" title="Atualizar Estado">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if ($userLevel >= 8): ?>
                            <button class="btn btn-sm btn-danger" onclick="deleteAvaria(${row.id})" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    `;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        }
    });
    
    // Filtros de estado
    $('.filtro-estado').click(function() {
        $('.filtro-estado').removeClass('active');
        $(this).addClass('active');
        
        window.__filtroEstado = $(this).data('estado');
        
        // Atualizar URL do exportar
        var estadoParam = window.__filtroEstado ? '?estado=' + window.__filtroEstado : '';
        $('#exportCsvBtn').attr('href', '<?= base_url('avarias-kit-admin/export') ?>' + estadoParam);
        
        table.ajax.reload();
        updateStats();
    });
    
    // Atualizar estatísticas
    function updateStats() {
        $.get('<?= base_url('avarias-kit-admin/get-stats') ?>', function(data) {
            $('#count_all').text(data.total || 0);
            $('#count_novo').text(data.novo || 0);
            $('#count_lido').text(data.lido || 0);
            $('#count_a_analisar').text(data.a_analisar || 0);
            $('#count_por_levantar').text(data.por_levantar || 0);
            $('#count_terminado').text(data.terminado || 0);
            $('#count_rejeitado').text(data.rejeitado || 0);
            $('#count_anulado').text(data.anulado || 0);
        });
    }
    
    // Auto-atualizar stats a cada 30 segundos
    setInterval(updateStats, 30000);
    
    // Remover foco antes de esconder modal (acessibilidade)
    $('#detailModal, #updateStatusModal, #deleteModal').on('hide.bs.modal', function() {
        // Remove focus de qualquer elemento dentro do modal
        $(this).find(':focus').blur();
    });
});

// Ver detalhes da avaria
function viewAvaria(id) {
    $('#modalBody').html('<p class="text-center"><i class="bi bi-hourglass-split"></i> A carregar...</p>');
    $('#detailModal').modal('show');
    
    $.get('<?= base_url('avarias-kit-admin/view') ?>/' + id, function(data) {
        var html = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID:</strong> ${data.id || ''}</p>
                    <p><strong>Número de Aluno:</strong> ${data.numero_aluno || ''}</p>
                    <p><strong>Nome:</strong> ${data.nome || ''}</p>
                    <p><strong>Turma:</strong> ${data.turma || ''}</p>
                    <p><strong>NIF:</strong> ${data.nif || ''}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Email Aluno:</strong> ${data.email_aluno || ''}</p>
                    <p><strong>Email EE:</strong> ${data.email_ee || ''}</p>
                    <p><strong>Estado:</strong> ${data.estado || ''}</p>
                    <p><strong>Criado em:</strong> ${data.created_at || ''}</p>
                    <p><strong>Finalizado em:</strong> ${data.finished_at || 'N/A'}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-12">
                    <h6><strong>Descrição da Avaria:</strong></h6>
                    <div class="alert alert-info">${data.avaria || 'Sem descrição'}</div>
                </div>
            </div>
            ${data.obs ? `
                <div class="row">
                    <div class="col-12">
                        <h6><strong>Observações:</strong></h6>
                        <div class="alert alert-warning">${data.obs}</div>
                    </div>
                </div>
            ` : ''}
        `;
        $('#modalBody').html(html);
    }).fail(function() {
        $('#modalBody').html('<p class="text-danger">Erro ao carregar dados.</p>');
    });
}

// Atualizar estado
function updateStatus(id) {
    $('#update_avaria_id').val(id);
    $('#novo_estado').val('');
    $('#obs_estado').val('');
    $('#updateStatusModal').modal('show');
}

$('#btnConfirmUpdate').click(function() {
    var id = $('#update_avaria_id').val();
    var estado = $('#novo_estado').val();
    var obs = $('#obs_estado').val();
    
    if (!estado) {
        toastr.error('Por favor, selecione um estado.', 'Erro!');
        return;
    }
    
    $.ajax({
        url: '<?= base_url('avarias-kit-admin/update-status') ?>/' + id,
        type: 'POST',
        data: {
            estado: estado,
            obs: obs,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message, 'Sucesso!');
                $('#updateStatusModal').modal('hide');
                $('#avariasTable').DataTable().ajax.reload();
                // Atualizar stats
                $.get('<?= base_url('avarias-kit-admin/get-stats') ?>', function(data) {
                    $('#count_all').text(data.total || 0);
                    $('#count_pendente').text(data.pendente || 0);
                    $('#count_a_analisar').text(data.a_analisar || 0);
                    $('#count_por_levantar').text(data.por_levantar || 0);
                    $('#count_terminado').text(data.terminado || 0);
                    $('#count_rejeitado').text(data.rejeitado || 0);
                    $('#count_anulado').text(data.anulado || 0);
                });
            } else {
                toastr.error(response.message, 'Erro!');
            }
        },
        error: function() {
            toastr.error('Erro ao atualizar estado.', 'Erro!');
        }
    });
});

// Eliminar avaria
function deleteAvaria(id) {
    $('#delete_avaria_id').val(id);
    $('#deleteModal').modal('show');
}

$('#btnConfirmDelete').click(function() {
    var id = $('#delete_avaria_id').val();
    
    $.ajax({
        url: '<?= base_url('avarias-kit-admin/delete') ?>/' + id,
        type: 'POST',
        data: {
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message, 'Sucesso!');
                $('#deleteModal').modal('hide');
                $('#avariasTable').DataTable().ajax.reload();
            } else {
                toastr.error(response.message, 'Erro!');
            }
        },
        error: function() {
            toastr.error('Erro ao eliminar avaria.', 'Erro!');
        }
    });
});
</script>
<?= $this->endSection() ?>
