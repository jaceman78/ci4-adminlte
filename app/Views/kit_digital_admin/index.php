<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>Gestão de Kit Digital<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestão de Pedidos - Kit Digital</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Kit Digital</li>
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
                <button type="button" class="btn btn-outline-warning filtro-estado" data-estado="pendente">
                    Pendentes <span id="count_pendente" class="badge rounded-pill bg-warning text-dark ms-1"><?= $stats['pendente'] ?></span>
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
            <a id="exportCsvBtn" class="btn btn-sm btn-outline-primary ms-2" href="#"><i class="bi bi-download"></i> Exportar CSV</a>
        </div>

        <!-- DataTable -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Requisições</h3>
            </div>
            <div class="card-body">
                <table id="kitTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nº Aluno</th>
                            <th>Nome</th>
                            <th>Turma</th>
                            <th>NIF</th>
                            <th>ASE</th>
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
            <div class="modal-header d-flex align-items-start">
                <h5 class="modal-title flex-grow-1">Detalhes do Pedido</h5>
                <button type="button" class="btn-close" aria-label="Fechar" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <p class="text-center"><i class="bi bi-hourglass-split"></i> A carregar...</p>
            </div>
            <div class="modal-footer" id="modalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rejeição -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="rejectForm">
                <div class="modal-header bg-danger text-white d-flex align-items-start">
                    <h5 class="modal-title flex-grow-1">Rejeitar Pedido</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="motivo">Motivo da Rejeição *</label>
                        <textarea class="form-control" name="motivo" id="motivo" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Confirmar Rejeição</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Anulação -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Confirmar Anulação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Tem a certeza que pretende <strong>anular</strong> este pedido de Kit Digital?</p>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Esta ação marca o pedido como <strong>Anulado</strong>. Poderá ser necessário criar nova requisição se ainda for pertinente.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="cancelConfirmBtn" class="btn btn-warning"><i class="bi bi-slash-circle"></i> Confirmar Anulação</button>
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
    var table = $('#kitTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('kit-digital-admin/get-data') ?>',
            type: 'POST',
            data: function(d) {
                d.<?= csrf_token() ?> = '<?= csrf_hash() ?>';
                
                // Converter valor interno (com underscore) para formato BD (com espaço) antes de enviar
                var fe = window.__filtroEstado || '';
                if (fe === 'por_levantar') fe = 'por levantar';
                d.filter_estado = fe;
            }
        },
        columns: [
            { 
                data: 'id',
                visible: false
            },
            { data: 'numero_aluno' },
            { data: 'nome' },
            { data: 'turma' },
            { data: 'nif' },
            { data: 'ase' },
            { 
                data: 'estado',
                render: function(data) {
                    var badges = {
                        'pendente': '<span class="badge text-bg-warning">Pendente</span>',
                        'por levantar': '<span class="badge text-bg-info">Por Levantar</span>',
                        'terminado': '<span class="badge text-bg-dark">Terminado</span>',
                        'rejeitado': '<span class="badge text-bg-danger">Rejeitado</span>',
                        'anulado': '<span class="badge text-bg-secondary">Anulado</span>'
                    };
                    return badges[data] || ('<span class="badge text-bg-light">'+data+'</span>');
                }
            },
            { data: 'created_at' },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-info view-btn" data-id="'+row.id+'"><i class="bi bi-eye"></i></button>';
                }
            }
        ],
        language: {
            "sProcessing": "A processar...",
            "sLengthMenu": "Mostrar _MENU_ registos",
            "sZeroRecords": "Não foram encontrados resultados",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registos",
            "sInfoEmpty": "Mostrando de 0 até 0 de 0 registos",
            "sInfoFiltered": "(filtrado de _MAX_ registos no total)",
            "sInfoPostFix": "",
            "sSearch": "Procurar:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sPrevious": "Anterior",
                "sNext": "Seguinte",
                "sLast": "Último"
            }
        }
    });

    // Ver detalhes
    $('#kitTable').on('click', '.view-btn', function() {
        var id = $(this).data('id');
        $.get('<?= base_url('kit-digital-admin/view') ?>/' + id, function(data) {
            var estadoBadges = {
                'pendente': '<span class="badge text-bg-warning">Pendente</span>',
                'por levantar': '<span class="badge text-bg-info">Por Levantar</span>',
                'terminado': '<span class="badge text-bg-dark">Terminado</span>',
                'rejeitado': '<span class="badge text-bg-danger">Rejeitado</span>',
                'anulado': '<span class="badge text-bg-secondary">Anulado</span>'
            };
            var estadoHtml = estadoBadges[data.estado] || data.estado;
            var html = `
                <table class="table table-sm">
                    <tr><th>Nº Aluno:</th><td>${data.numero_aluno}</td></tr>
                    <tr><th>Nome:</th><td>${data.nome}</td></tr>
                    <tr><th>Turma:</th><td>${data.turma}</td></tr>
                    <tr><th>NIF:</th><td>${data.nif}</td></tr>
                    <tr><th>ASE:</th><td>${data.ase}</td></tr>
                    <tr><th>Email Aluno:</th><td>${data.email_aluno}</td></tr>
                    <tr><th>Email EE:</th><td>${data.email_ee}</td></tr>
                    <tr><th>Estado:</th><td>${estadoHtml}</td></tr>
                    <tr><th>Data:</th><td>${data.created_at}</td></tr>
                    ${data.obs ? '<tr><th>Observações:</th><td>'+data.obs+'</td></tr>' : ''}
                </table>
            `;
            $('#modalBody').html(html);

            // Botões de ação
            var footer = '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>';
            if (data.estado === 'pendente') {
                footer += ' <a href="<?= base_url('kit-digital-admin/approve') ?>/'+data.id+'" class="btn btn-success"><i class="bi bi-check"></i> Aprovar</a>';
                footer += ' <button class="btn btn-danger reject-btn" data-id="'+data.id+'"><i class="bi bi-x"></i> Rejeitar</button>';
            }
            if (data.estado !== 'anulado' && data.estado !== 'terminado') {
                footer += ' <button class="btn btn-warning cancel-btn" data-id="'+data.id+'"><i class="bi bi-slash-circle"></i> Anular</button>';
            }
            if (data.estado === 'por levantar') {
                footer += ' <a href="<?= base_url('kit-digital-admin/finish') ?>/'+data.id+'" class="btn btn-dark"><i class="bi bi-flag"></i> Terminar Processo</a>';
            }
            $('#modalFooter').html(footer);

            $('#detailModal').modal('show');
        });
    });

    // Modal confirmação de anulação (apenas ligar uma vez)
    $(document).on('click', '.cancel-btn', function(){
        var id = $(this).data('id');
        $('#cancelConfirmBtn').data('id', id);
        var modal = new bootstrap.Modal(document.getElementById('cancelModal'));
        modal.show();
    });
    $('#cancelConfirmBtn').on('click', function(){
        var id = $(this).data('id');
        window.location.href = '<?= base_url('kit-digital-admin/cancel') ?>/' + id;
    });

    // Rejeitar (abrir modal motivo)
    $(document).on('click', '.reject-btn', function() {
        var id = $(this).data('id');
        $('#rejectForm').attr('action', '<?= base_url('kit-digital-admin/reject') ?>/' + id);
        $('#detailModal').modal('hide');
        $('#rejectModal').modal('show');
    });

    // Filtro por estado
    window.__filtroEstado = '';
    
    $(document).on('click', '.filtro-estado', function(){
        $('.filtro-estado').removeClass('active');
        $(this).addClass('active');
        window.__filtroEstado = $(this).data('estado') || '';
        
        // Atualizar URL export (converter underscore para espaço ao enviar)
        var estadoParam = window.__filtroEstado === 'por_levantar' ? 'por levantar' : window.__filtroEstado;
        var estadoEncoded = encodeURIComponent(estadoParam);
        var url = '<?= base_url('kit-digital-admin/export') ?>' + (estadoParam ? ('?estado=' + estadoEncoded) : '');
        $('#exportCsvBtn').attr('href', url);
        
        // Recarregar DataTable com novo filtro
        table.ajax.reload(null, true);
    });

    // Inicializar export link
    var initUrl = '<?= base_url('kit-digital-admin/export') ?>';
    $('#exportCsvBtn').attr('href', initUrl);

    // Atualizar contagens (badges) periodicamente
    function refreshCounts() {
        $.get('<?= base_url('kit-digital-admin/get-stats') ?>', function(resp){
            if (!resp) return;
            $('#count_all').text(resp.total ?? 0);
            $('#count_pendente').text(resp.pendente ?? 0);
            $('#count_por_levantar').text(resp.por_levantar ?? 0);
            $('#count_terminado').text(resp.terminado ?? 0);
            $('#count_rejeitado').text(resp.rejeitado ?? 0);
            $('#count_anulado').text(resp.anulado ?? 0);
        });
    }
    refreshCounts();
    setInterval(refreshCounts, 30000); // a cada 30s
});
</script>
<?= $this->endSection() ?>
