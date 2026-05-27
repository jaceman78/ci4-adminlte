<?= $this->extend('layout/master') ?>

<?= $this->section('pageHeader') ?>
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-umbrella-beach"></i> Gestão de Férias</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Gestão de Férias</li>
        </ol>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <!-- Ações Rápidas -->
    <div class="col-md-3">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks"></i> Ações Rápidas</h3>
            </div>
            <div class="card-body p-0">
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a href="<?= base_url('ferias/atribuir') ?>" class="nav-link">
                            <i class="fas fa-user-plus"></i> Atribuir Dias de Férias
                            <?php if (($professores_sem_atribuicao ?? 0) > 0): ?>
                            <span class="badge bg-primary float-right" title="Professores sem dias atribuídos"><?= $professores_sem_atribuicao ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('ferias/pedidos-pendentes') ?>" class="nav-link">
                            <i class="fas fa-clock"></i> Pedidos Pendentes
                            <span class="badge bg-warning float-right"><?= count($pedidos_pendentes) ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('ferias/mapa-ferias') ?>" class="nav-link">
                            <i class="fas fa-calendar"></i> Calendário de Férias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= base_url('ferias/relatorios') ?>" class="nav-link">
                            <i class="fas fa-chart-line"></i> Relatórios
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Pedidos Pendentes -->
    <div class="col-md-9">
        <div class="card card-warning card-outline" id="pedidosPendentes">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock"></i> Pedidos Pendentes de Aprovação</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($pedidos_pendentes)): ?>
                    <div class="p-3 text-center text-muted">
                        <i class="fas fa-check-circle fa-3x mb-3"></i></br>
                        Não existem pedidos pendentes de aprovação.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Professor</th>
                                    <th>Dias</th>
                                    <th>Estado</th>
                                    <th>Data Submissão</th>
                                    <th width="200">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos_pendentes as $pedido): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($pedido['nome_professor']) ?></strong><br>
                                            <small class="text-muted"><?= esc($pedido['email_professor']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= $pedido['total_dias'] ?> dias</span>
                                        </td>
                                        <td>
                                            <?= formatar_estado_ferias($pedido['estado']) ?>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($pedido['submetido_em'])) ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="verDetalhes(<?= $pedido['id'] ?>)" title="Ver Detalhes">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success" onclick="aprovar(<?= $pedido['id'] ?>)" title="Aprovar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="rejeitar(<?= $pedido['id'] ?>)" title="Rejeitar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Configuração de Período -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card card-info collapsed-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-cog"></i> Configuração do Período de Marcação
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Defina o período permitido</strong> para que os professores possam marcar férias.
                            As datas fora deste intervalo serão automaticamente rejeitadas.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form id="formConfiguracao">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="data_inicio_permitida">
                                            <i class="fas fa-calendar-alt"></i> Data de Início
                                        </label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="data_inicio_permitida" 
                                               name="data_inicio_permitida" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="data_fim_permitida">
                                            <i class="fas fa-calendar-alt"></i> Data de Fim
                                        </label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="data_fim_permitida" 
                                               name="data_fim_permitida" 
                                               required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="mostrar_menu_ferias" 
                                           name="mostrar_menu_ferias" 
                                           value="1"
                                           checked>
                                    <label class="custom-control-label" for="mostrar_menu_ferias">
                                        <strong>Mostrar menu Férias na barra lateral</strong>
                                        <small class="text-muted d-block">
                                            Desative para ocultar o acesso ao módulo de férias para todos os utilizadores
                                        </small>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="permite_marcacao" 
                                           name="permite_marcacao" 
                                           value="1"
                                           checked>
                                    <label class="custom-control-label" for="permite_marcacao">
                                        <strong>Permitir marcação de férias</strong>
                                        <small class="text-muted d-block">
                                            Desative temporariamente para bloquear novas marcações
                                        </small>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group" id="msg_bloqueio_group" style="display:none;">
                                <label for="mensagem_bloqueio">
                                    <i class="fas fa-comment"></i> Mensagem de Bloqueio
                                </label>
                                <textarea class="form-control" 
                                          id="mensagem_bloqueio" 
                                          name="mensagem_bloqueio" 
                                          rows="2" 
                                          placeholder="Mensagem a mostrar aos professores quando marcação está bloqueada"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="observacoes">
                                    <i class="fas fa-sticky-note"></i> Observações
                                </label>
                                <textarea class="form-control" 
                                          id="observacoes" 
                                          name="observacoes" 
                                          rows="2" 
                                          placeholder="Notas internas sobre esta configuração"></textarea>
                            </div>

                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-save"></i> Guardar Configuração
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalhes do Pedido -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Pedido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDetalhesContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">A carregar...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Aprovação -->
<div class="modal fade" id="modalAprovar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Aprovar Pedido de Férias</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAprovar">
                <input type="hidden" id="aprovarPedidoId">
                <div class="modal-body">
                    <p>Tem a certeza que deseja aprovar este pedido?</p>
                    <div class="mb-3">
                        <label for="observacoesAprovacao" class="form-label">Observações (opcional)</label>
                        <textarea class="form-control" id="observacoesAprovacao" name="observacoes" rows="3"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Será gerado um documento PDF que o professor deverá assinar.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Aprovar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Rejeição -->
<div class="modal fade" id="modalRejeitar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Rejeitar Pedido de Férias</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formRejeitar">
                <input type="hidden" id="rejeitarPedidoId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="motivoRejeicao" class="form-label">Motivo da Rejeição <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motivoRejeicao" name="motivo" rows="4" required></textarea>
                        <div class="form-text">Por favor, indique o motivo da rejeição para que o professor possa corrigir.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Rejeitar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function verDetalhes(pedidoId) {
    $('#modalDetalhes').modal('show');
    $('#modalDetalhesContent').html('<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">A carregar...</span></div></div>');
    
    // Redirecionar para página de detalhes ou carregar via AJAX
    window.location.href = '<?= base_url('ferias/pedidos-pendentes') ?>?pedido=' + pedidoId;
}

function aprovar(pedidoId) {
    $('#aprovarPedidoId').val(pedidoId);
    $('#modalAprovar').modal('show');
}

function rejeitar(pedidoId) {
    $('#rejeitarPedidoId').val(pedidoId);
    $('#modalRejeitar').modal('show');
}

$('#formAprovar').on('submit', function(e) {
    e.preventDefault();
    
    const pedidoId = $('#aprovarPedidoId').val();
    const observacoes = $('#observacoesAprovacao').val();
    
    $.ajax({
        url: '<?= base_url('ferias/aprovar') ?>/' + pedidoId,
        type: 'POST',
        data: { observacoes },
        success: function(response) {
            $('#modalAprovar').modal('hide');
            showToast('success', response.message || 'Pedido aprovado com sucesso');
            setTimeout(() => location.reload(), 1500);
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            showToast('error', response.message || 'Erro ao aprovar pedido');
        }
    });
});

$('#formRejeitar').on('submit', function(e) {
    e.preventDefault();
    
    const pedidoId = $('#rejeitarPedidoId').val();
    const motivo = $('#motivoRejeicao').val();
    
    if (!motivo.trim()) {
        showToast('error', 'Por favor, indique o motivo da rejeição');
        return;
    }
    
    $.ajax({
        url: '<?= base_url('ferias/rejeitar') ?>/' + pedidoId,
        type: 'POST',
        data: { motivo },
        success: function(response) {
            $('#modalRejeitar').modal('hide');
            showToast('success', response.message || 'Pedido rejeitado');
            setTimeout(() => location.reload(), 1500);
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            showToast('error', response.message || 'Erro ao rejeitar pedido');
        }
    });
});

function showToast(type, message) {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type,
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

// ============================================================
// CONFIGURAÇÃO DE PERÍODO DE MARCAÇÃO
// ============================================================

// Carregar configuração ao abrir o card
$(document).ready(function() {
    carregarConfiguracao();

    // Mostrar/esconder campo de mensagem de bloqueio
    $('#permite_marcacao').on('change', function() {
        if ($(this).is(':checked')) {
            $('#msg_bloqueio_group').slideUp();
        } else {
            $('#msg_bloqueio_group').slideDown();
        }
    });
});

function carregarConfiguracao() {
    $.ajax({
        url: '<?= base_url('ferias/get-configuracao') ?>',
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                const config = response.data;
                $('#data_inicio_permitida').val(config.data_inicio_permitida);
                $('#data_fim_permitida').val(config.data_fim_permitida);
                $('#mostrar_menu_ferias').prop('checked', config.mostrar_menu_ferias !== false);
                // Mostrar/esconder campo de mensagem de bloqueio
                if (!config.permite_marcacao) {
                    $('#msg_bloqueio_group').show();
                }
            }
        },
        error: function(xhr) {
            console.error('Erro ao carregar configuração:', xhr);
        }
    });
}

$('#formConfiguracao').on('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        data_inicio_permitida: $('#data_inicio_permitida').val(),
        data_fim_permitida: $('#data_fim_permitida').val(),
        mostrar_menu_ferias: $('#mostrar_menu_ferias').is(':checked') ? 1 : 0,
        permite_marcacao: $('#permite_marcacao').is(':checked') ? 1 : 0,
        mensagem_bloqueio: $('#mensagem_bloqueio').val(),
        observacoes: $('#observacoes').val()
    };

    // Validar datas
    const dataInicio = new Date(formData.data_inicio_permitida);
    const dataFim = new Date(formData.data_fim_permitida);

    if (dataFim < dataInicio) {
        showToast('error', 'A data de fim deve ser posterior à data de início');
        return;
    }

    // Validar mensagem de bloqueio se marcação estiver desativada
    if (!formData.permite_marcacao && !formData.mensagem_bloqueio.trim()) {
        showToast('warning', 'Recomenda-se definir uma mensagem de bloqueio');
    }

    $.ajax({
        url: '<?= base_url('ferias/salvar-configuracao') ?>',
        type: 'POST',
        data: formData,
        success: function(response) {
            showToast('success', response.message || 'Configuração guardada com sucesso');
            setTimeout(() => {
                // Recarregar configuração para confirmar
                carregarConfiguracao();
            }, 1000);
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            const errorMsg = response.messages?.error || response.message || 'Erro ao guardar configuração';
            showToast('error', errorMsg);
        }
    });
});
</script>
<?= $this->endSection() ?>
