<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>Listagem de pedidos<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$userLevel = session()->get('LoggedUserData')['level'] ?? 0;
?>
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
                <button type="button" class="btn btn-outline-danger filtro-estado" data-estado="dados_invalidos">
                    Dados Inválidos <span id="count_dados_invalidos" class="badge rounded-pill bg-danger ms-1"><?= $stats['dados_invalidos'] ?></span>
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
                <table id="kitTable" class="table table-bordered table-hover nowrap" style="width:100%">
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
                <?php if ($userLevel >= 8): ?>
                <button type="button" class="btn btn-sm btn-warning me-2" id="btnEditarDetalhes" style="display:none;">
                    <i class="bi bi-pencil"></i> Editar
                </button>
                <?php endif; ?>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar" style="filter: invert(1) grayscale(100%) brightness(0);"></button>
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

<!-- Modal Confirmar Reenvio de Aviso -->
<div class="modal fade" id="resendReminderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title">Confirmar Reenvio de Aviso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Tem a certeza que pretende <strong>reenviar o aviso de levantamento</strong> ao Encarregado de Educação?</p>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-envelope-exclamation me-2"></i>
                    Será enviado um email de aviso informando que o prazo para levantamento está a terminar.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="resendReminderConfirmBtn" class="btn btn-info"><i class="bi bi-envelope-exclamation"></i> Confirmar Envio</button>
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
    
    // Carregar turmas disponíveis
    var turmasDisponiveis = [];
    $.get('<?= base_url('kit-digital-admin/get-turmas') ?>', function(response) {
        if (response.success) {
            turmasDisponiveis = response.turmas;
        }
    });
    
    // DataTable
    var table = $('#kitTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        order: [[7, 'desc']], // Ordenar por data (coluna 7) decrescente por defeito
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
                visible: false,
                orderable: true
            },
            { 
                data: 'numero_aluno',
                orderable: true
            },
            { 
                data: 'nome',
                orderable: true
            },
            { 
                data: 'turma',
                orderable: true
            },
            { 
                data: 'nif',
                orderable: true
            },
            { 
                data: 'ase',
                orderable: true
            },
            { 
                data: 'estado',
                orderable: true,
                render: function(data) {
                    var badges = {
                        'pendente': '<span class="badge text-bg-warning">Pendente</span>',
                        'dados_invalidos': '<span class="badge text-bg-danger">Dados Inválidos</span>',
                        'por levantar': '<span class="badge text-bg-info">Por Levantar</span>',
                        'terminado': '<span class="badge text-bg-dark">Terminado</span>',
                        'rejeitado': '<span class="badge text-bg-danger">Rejeitado</span>',
                        'anulado': '<span class="badge text-bg-secondary">Anulado</span>'
                    };
                    return badges[data] || ('<span class="badge text-bg-light">'+data+'</span>');
                }
            },
            { 
                data: 'created_at',
                orderable: true
            },
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
    var currentEditId = null;
    var currentEditData = null;
    
    $('#kitTable').on('click', '.view-btn', function() {
        var id = $(this).data('id');
        currentEditId = id;
        
        $.get('<?= base_url('kit-digital-admin/view') ?>/' + id, function(data) {
            currentEditData = data;
            var estadoBadges = {
                'pendente': '<span class="badge text-bg-warning">Pendente</span>',
                'dados_invalidos': '<span class="badge text-bg-danger">Dados Inválidos</span>',
                'por levantar': '<span class="badge text-bg-info">Por Levantar</span>',
                'terminado': '<span class="badge text-bg-dark">Terminado</span>',
                'rejeitado': '<span class="badge text-bg-danger">Rejeitado</span>',
                'anulado': '<span class="badge text-bg-secondary">Anulado</span>'
            };
            var estadoHtml = estadoBadges[data.estado] || data.estado;
            
            // Gerar opções do select de turmas
            var turmaOptions = turmasDisponiveis.map(t => 
                `<option value="${t}" ${data.turma===t?'selected':''}>${t}</option>`
            ).join('');
            
            var html = `
                <table class="table table-sm" id="detailsTable">
                    <tr><th>Nº Aluno:</th><td class="view-mode">${data.numero_aluno}</td><td class="edit-mode" style="display:none;"><input type="text" class="form-control form-control-sm" name="numero_aluno" value="${data.numero_aluno}"></td></tr>
                    <tr><th>Nome:</th><td class="view-mode">${data.nome}</td><td class="edit-mode" style="display:none;"><input type="text" class="form-control form-control-sm" name="nome" value="${data.nome}"></td></tr>
                    <tr><th>Turma:</th><td class="view-mode">${data.turma}</td><td class="edit-mode" style="display:none;"><select class="form-select form-select-sm" name="turma">${turmaOptions}</select></td></tr>
                    <tr><th>NIF:</th><td class="view-mode">${data.nif}</td><td class="edit-mode" style="display:none;"><input type="text" class="form-control form-control-sm" name="nif" value="${data.nif}"></td></tr>
                    <tr><th>ASE:</th><td class="view-mode">${data.ase}</td><td class="edit-mode" style="display:none;"><select class="form-select form-select-sm" name="ase"><option value="Escalão A" ${data.ase==='Escalão A'?'selected':''}>Escalão A</option><option value="Escalão B" ${data.ase==='Escalão B'?'selected':''}>Escalão B</option><option value="Escalão C" ${data.ase==='Escalão C'?'selected':''}>Escalão C</option><option value="Sem Escalão" ${data.ase==='Sem Escalão'?'selected':''}>Sem Escalão</option></select></td></tr>
                    <tr><th>Email Aluno:</th><td class="view-mode">${data.email_aluno}</td><td class="edit-mode" style="display:none;"><input type="email" class="form-control form-control-sm" name="email_aluno" value="${data.email_aluno}"></td></tr>
                    <tr><th>Email EE:</th><td class="view-mode">${data.email_ee}</td><td class="edit-mode" style="display:none;"><input type="email" class="form-control form-control-sm" name="email_ee" value="${data.email_ee}"></td></tr>
                    <tr><th>Estado:</th><td class="view-mode">${estadoHtml}</td><td class="edit-mode" style="display:none;"><div class="d-flex align-items-center gap-2">${estadoHtml} <select class="form-select form-select-sm" name="estado" style="flex: 1;"><option value="pendente" ${data.estado==='pendente'?'selected':''}>Pendente</option><option value="dados_invalidos" ${data.estado==='dados_invalidos'?'selected':''}>Dados Inválidos</option><option value="por levantar" ${data.estado==='por levantar'?'selected':''}>Por Levantar</option><option value="terminado" ${data.estado==='terminado'?'selected':''}>Terminado</option><option value="rejeitado" ${data.estado==='rejeitado'?'selected':''}>Rejeitado</option><option value="anulado" ${data.estado==='anulado'?'selected':''}>Anulado</option></select></div></td></tr>
                    <tr><th>Data:</th><td>${data.created_at}</td></tr>
                    ${data.obs ? '<tr><th>Observações:</th><td class="view-mode">'+data.obs+'</td><td class="edit-mode" style="display:none;"><textarea class="form-control form-control-sm" name="obs" rows="2">'+data.obs+'</textarea></td></tr>' : '<tr class="edit-mode" style="display:none;"><th>Observações:</th><td><textarea class="form-control form-control-sm" name="obs" rows="2"></textarea></td></tr>'}
                </table>
            `;
            $('#modalBody').html(html);
            
            <?php if ($userLevel >= 8): ?>
            // Mostrar botão editar para nível 8+
            $('#btnEditarDetalhes').show();
            <?php else: ?>
            $('#btnEditarDetalhes').hide();
            <?php endif; ?>

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
                footer += ' <button class="btn btn-info resend-reminder-btn" data-id="'+data.id+'" title="Reenviar aviso de prazo de levantamento"><i class="bi bi-envelope-exclamation"></i> Reenviar Aviso</button>';
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

    // Reenviar aviso de levantamento (abrir modal confirmação)
    $(document).on('click', '.resend-reminder-btn', function() {
        var id = $(this).data('id');
        $('#resendReminderConfirmBtn').data('id', id);
        var modal = new bootstrap.Modal(document.getElementById('resendReminderModal'));
        modal.show();
    });
    
    // Confirmar reenvio de aviso
    $('#resendReminderConfirmBtn').on('click', function(){
        var id = $(this).data('id');
        var btn = $(this);
        var resendBtn = $('.resend-reminder-btn[data-id="'+id+'"]');
        
        // Fechar modal de confirmação
        var modal = bootstrap.Modal.getInstance(document.getElementById('resendReminderModal'));
        modal.hide();
        
        // Desabilitar botão durante o envio
        resendBtn.prop('disabled', true);
        resendBtn.html('<i class="bi bi-hourglass-split"></i> A enviar...');
        
        $.ajax({
            url: '<?= base_url('kit-digital-admin/resend-reminder') ?>/' + id,
            type: 'POST',
            data: {
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSuccess(response.message || 'Aviso reenviado com sucesso!');
                } else {
                    showError(response.message || 'Erro ao reenviar aviso');
                }
                // Reabilitar botão
                resendBtn.prop('disabled', false);
                resendBtn.html('<i class="bi bi-envelope-exclamation"></i> Reenviar Aviso');
            },
            error: function(xhr) {
                console.error('Erro AJAX:', xhr);
                var errorMsg = 'Erro ao reenviar aviso';
                try {
                    var jsonResponse = JSON.parse(xhr.responseText);
                    if (jsonResponse.message) {
                        errorMsg = jsonResponse.message;
                    }
                } catch (e) {
                    errorMsg += ' (Código: ' + xhr.status + ')';
                }
                showError(errorMsg);
                // Reabilitar botão
                resendBtn.prop('disabled', false);
                resendBtn.html('<i class="bi bi-envelope-exclamation"></i> Reenviar Aviso');
            }
        });
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
    
    <?php if ($userLevel >= 8): ?>
    // Modo de edição
    var editMode = false;
    
    $('#btnEditarDetalhes').on('click', function() {
        if (!editMode) {
            // Ativar modo edição
            editMode = true;
            $('.view-mode').hide();
            $('.edit-mode').show();
            $(this).html('<i class="bi bi-x-circle"></i> Cancelar');
            $(this).removeClass('btn-warning').addClass('btn-secondary');
            
            // Adicionar botão salvar ao footer
            var saveBtn = '<button type="button" class="btn btn-success" id="btnSalvarDetalhes"><i class="bi bi-save"></i> Salvar Alterações</button>';
            $('#modalFooter').prepend(saveBtn);
        } else {
            // Cancelar edição
            editMode = false;
            $('.view-mode').show();
            $('.edit-mode').hide();
            $(this).html('<i class="bi bi-pencil"></i> Editar');
            $(this).removeClass('btn-secondary').addClass('btn-warning');
            $('#btnSalvarDetalhes').remove();
        }
    });
    
    // Salvar alterações
    $(document).on('click', '#btnSalvarDetalhes', function() {
        var formData = {
            numero_aluno: $('input[name="numero_aluno"]').val(),
            nome: $('input[name="nome"]').val(),
            turma: $('select[name="turma"]').val(),
            nif: $('input[name="nif"]').val(),
            ase: $('select[name="ase"]').val(),
            email_aluno: $('input[name="email_aluno"]').val(),
            email_ee: $('input[name="email_ee"]').val(),
            estado: $('select[name="estado"]').val(),
            obs: $('textarea[name="obs"]').val(),
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        };
        
        $.ajax({
            url: '<?= base_url('kit-digital-admin/update') ?>/' + currentEditId,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Atualizar dados na view
                    currentEditData = {...currentEditData, ...formData};
                    
                    // Atualizar HTML da view mode
                    var estadoBadges = {
                        'pendente': '<span class="badge text-bg-warning">Pendente</span>',
                        'dados_invalidos': '<span class="badge text-bg-danger">Dados Inválidos</span>',
                        'por levantar': '<span class="badge text-bg-info">Por Levantar</span>',
                        'terminado': '<span class="badge text-bg-dark">Terminado</span>',
                        'rejeitado': '<span class="badge text-bg-danger">Rejeitado</span>',
                        'anulado': '<span class="badge text-bg-secondary">Anulado</span>'
                    };
                    var estadoHtml = estadoBadges[currentEditData.estado] || currentEditData.estado;
                    
                    // Gerar opções do select de turmas
                    var turmaOptions = turmasDisponiveis.map(t => 
                        `<option value="${t}" ${formData.turma===t?'selected':''}>${t}</option>`
                    ).join('');
                    
                    // Atualizar estado nos badges
                    currentEditData.estado = formData.estado;
                    estadoHtml = estadoBadges[currentEditData.estado] || currentEditData.estado;
                    
                    var html = `
                        <table class="table table-sm" id="detailsTable">
                            <tr><th>Nº Aluno:</th><td class="view-mode">${formData.numero_aluno}</td><td class="edit-mode" style="display:none;"><input type="text" class="form-control form-control-sm" name="numero_aluno" value="${formData.numero_aluno}"></td></tr>
                            <tr><th>Nome:</th><td class="view-mode">${formData.nome}</td><td class="edit-mode" style="display:none;"><input type="text" class="form-control form-control-sm" name="nome" value="${formData.nome}"></td></tr>
                            <tr><th>Turma:</th><td class="view-mode">${formData.turma}</td><td class="edit-mode" style="display:none;"><select class="form-select form-select-sm" name="turma">${turmaOptions}</select></td></tr>
                            <tr><th>NIF:</th><td class="view-mode">${formData.nif}</td><td class="edit-mode" style="display:none;"><input type="text" class="form-control form-control-sm" name="nif" value="${formData.nif}"></td></tr>
                            <tr><th>ASE:</th><td class="view-mode">${formData.ase}</td><td class="edit-mode" style="display:none;"><select class="form-select form-select-sm" name="ase"><option value="Escalão A" ${formData.ase==='Escalão A'?'selected':''}>Escalão A</option><option value="Escalão B" ${formData.ase==='Escalão B'?'selected':''}>Escalão B</option><option value="Escalão C" ${formData.ase==='Escalão C'?'selected':''}>Escalão C</option><option value="Sem Escalão" ${formData.ase==='Sem Escalão'?'selected':''}>Sem Escalão</option></select></td></tr>
                            <tr><th>Email Aluno:</th><td class="view-mode">${formData.email_aluno}</td><td class="edit-mode" style="display:none;"><input type="email" class="form-control form-control-sm" name="email_aluno" value="${formData.email_aluno}"></td></tr>
                            <tr><th>Email EE:</th><td class="view-mode">${formData.email_ee}</td><td class="edit-mode" style="display:none;"><input type="email" class="form-control form-control-sm" name="email_ee" value="${formData.email_ee}"></td></tr>
                            <tr><th>Estado:</th><td class="view-mode">${estadoHtml}</td><td class="edit-mode" style="display:none;"><div class="d-flex align-items-center gap-2">${estadoHtml} <select class="form-select form-select-sm" name="estado" style="flex: 1;"><option value="pendente" ${formData.estado==='pendente'?'selected':''}>Pendente</option>${(currentEditData.estado==='pendente'||formData.estado==='dados_invalidos')?'<option value="dados_invalidos" ${formData.estado==="dados_invalidos"?"selected":""}>Dados Inválidos</option>':''}<option value="por levantar" ${formData.estado==='por levantar'?'selected':''}>Por Levantar</option><option value="terminado" ${formData.estado==='terminado'?'selected':''}>Terminado</option><option value="rejeitado" ${formData.estado==='rejeitado'?'selected':''}>Rejeitado</option><option value="anulado" ${formData.estado==='anulado'?'selected':''}>Anulado</option></select></div></td></tr>
                            <tr><th>Data:</th><td>${currentEditData.created_at}</td></tr>
                            ${formData.obs ? '<tr><th>Observações:</th><td class="view-mode">'+formData.obs+'</td><td class="edit-mode" style="display:none;"><textarea class="form-control form-control-sm" name="obs" rows="2">'+formData.obs+'</textarea></td></tr>' : '<tr class="edit-mode" style="display:none;"><th>Observações:</th><td><textarea class="form-control form-control-sm" name="obs" rows="2"></textarea></td></tr>'}
                        </table>
                    `;
                    $('#modalBody').html(html);
                    
                    // Desativar modo edição
                    editMode = false;
                    $('#btnEditarDetalhes').html('<i class="bi bi-pencil"></i> Editar');
                    $('#btnEditarDetalhes').removeClass('btn-secondary').addClass('btn-warning');
                    $('#btnSalvarDetalhes').remove();
                    
                    // Recarregar datatable
                    table.ajax.reload(null, false);
                    
                    // Mostrar mensagem de sucesso
                    showSuccess('Dados atualizados com sucesso!');
                } else {
                    showError('Erro ao atualizar: ' + (response.message || 'Erro desconhecido'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro AJAX:', xhr);
                console.error('Status:', status);
                console.error('Error:', error);
                console.error('Response Text:', xhr.responseText);
                
                var errorMsg = 'Erro ao salvar alterações.';
                try {
                    var jsonResponse = JSON.parse(xhr.responseText);
                    if (jsonResponse.message) {
                        errorMsg = jsonResponse.message;
                    }
                } catch (e) {
                    errorMsg += ' Código: ' + xhr.status;
                }
                showError(errorMsg);
            }
        });
    });
    
    // Remover foco antes de esconder modal (acessibilidade)
    $('#detailModal').on('hide.bs.modal', function() {
        // Remove focus de qualquer elemento dentro do modal
        $(this).find(':focus').blur();
    });
    
    // Adicionar o mesmo tratamento para outros modais
    $('#rejectModal, #cancelModal, #resendReminderModal').on('hide.bs.modal', function() {
        $(this).find(':focus').blur();
    });
    
    // Resetar modo edição ao fechar modal
    $('#detailModal').on('hidden.bs.modal', function() {
        editMode = false;
        $('#btnEditarDetalhes').html('<i class="bi bi-pencil"></i> Editar');
        $('#btnEditarDetalhes').removeClass('btn-secondary').addClass('btn-warning');
        $('#btnSalvarDetalhes').remove();
    });
    <?php endif; ?>

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
