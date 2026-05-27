<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="bi bi-hourglass-split"></i> Pedidos Pendentes</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('ferias/secretaria') ?>">Férias</a></li>
                        <li class="breadcrumb-item active">Pendentes</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong><?= count($pedidos) ?></strong> pedido(s) aguardando aprovação.
            </div>
            
            <?php if (empty($pedidos)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> Não existem pedidos pendentes no momento.
            </div>
            <?php else: ?>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="pedidosTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Professor</th>
                                    <th>Ano</th>
                                    <th>Submetido</th>
                                    <th>Períodos</th>
                                    <th>Dias</th>
                                    <th>Estado</th>
                                    <th width="150">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td><strong>#<?= $pedido['id'] ?></strong></td>
                                    <td><?= esc($pedido['nome_professor']) ?></td>
                                    <td><?= $pedido['ano'] + 1 ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($pedido['submetido_em'])) ?></td>
                                    <td>
                                        <small>
                                            <?php foreach ($pedido['periodos'] as $i => $p): ?>
                                                <?= date('d/m', strtotime($p['data_inicio'])) ?> - <?= date('d/m', strtotime($p['data_fim'])) ?>
                                                <?php if ($i < count($pedido['periodos']) - 1): ?><br><?php endif; ?>
                                            <?php endforeach; ?>
                                        </small>
                                    </td>
                                    <td><strong><?= $pedido['total_dias'] ?></strong></td>
                                    <td>
                                        <?= formatar_estado_ferias($pedido['estado']) ?>
                                        <?php if (!empty($pedido['requer_acumulacao'])): ?>
                                        <br>
                                        <?php
                                        $acumEstado = $pedido['acumulacao_estado'] ?? 'pendente';
                                        $acumBadge = match($acumEstado) {
                                            'autorizada'     => '<span class="badge bg-success" title="Acumulação autorizada">Acum. ✓</span>',
                                            'nao_autorizada' => '<span class="badge bg-danger" title="Acumulação não autorizada">Acum. ✗</span>',
                                            default          => '<span class="badge bg-warning text-dark" title="Aguarda despacho acumulação">Acum. ⏳</span>',
                                        };
                                        echo $acumBadge;
                                        ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($pedido['estado'] === 'remarcacao_solicitada'): ?>
                                        <!-- Botões para Remarcação -->
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-warning" 
                                                    onclick="aprovarRemarcacao(<?= $pedido['id'] ?>)"
                                                    title="Aprovar Remarcação">
                                                <i class="bi bi-check-circle"></i> Aprovar Remarcação
                                            </button>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="rejeitarRemarcacao(<?= $pedido['id'] ?>)"
                                                    title="Rejeitar Remarcação">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" 
                                                    onclick="verDetalhes(<?= $pedido['id'] ?>)"
                                                    title="Detalhes">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <?php else: ?>
                                        <!-- Botões para Pedido Normal -->
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-success" 
                                                    onclick="aprovar(<?= $pedido['id'] ?>)"
                                                    title="Aprovar">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" 
                                                    onclick="rejeitar(<?= $pedido['id'] ?>)"
                                                    title="Rejeitar">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" 
                                                    onclick="verDetalhes(<?= $pedido['id'] ?>)"
                                                    title="Detalhes">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php endif; ?>
            
        </div>
    </div>
</div>

<!-- Modal de Detalhes do Pedido -->
<div class="modal fade" id="modalDetalhes" tabindex="-1" aria-labelledby="modalDetalhesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalDetalhesLabel">
                    <i class="bi bi-file-text"></i> Detalhes do Pedido
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetalhesBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">A carregar...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="modalDetalhesFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#pedidosTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        order: [[3, 'asc']],
        pageLength: 25
    });
});

function aprovar(pedidoId) {
    Swal.fire({
        title: 'Aprovar Pedido?',
        text: 'O PDF será gerado automaticamente e enviado ao professor.',
        icon: 'question',
        input: 'textarea',
        inputLabel: 'Observações (opcional)',
        inputPlaceholder: 'Digite observações...',
        showCancelButton: true,
        confirmButtonText: 'Sim, Aprovar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/aprovar/') ?>' + pedidoId,
                type: 'POST',
                data: { observacoes: result.value },
                success: function(response) {
                    Swal.fire('Aprovado!', 'Pedido aprovado com sucesso', 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire('Erro', response?.messages?.error || 'Erro ao aprovar pedido', 'error');
                }
            });
        }
    });
}

function rejeitar(pedidoId) {
    Swal.fire({
        title: 'Rejeitar Pedido?',
        text: 'Esta ação não pode ser desfeita.',
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Motivo da rejeição (obrigatório)',
        inputPlaceholder: 'Digite o motivo...',
        inputValidator: (value) => {
            if (!value) {
                return 'Deve indicar o motivo da rejeição!';
            }
        },
        showCancelButton: true,
        confirmButtonText: 'Sim, Rejeitar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/rejeitar/') ?>' + pedidoId,
                type: 'POST',
                data: { motivo: result.value },
                success: function(response) {
                    Swal.fire('Rejeitado!', 'Pedido rejeitado', 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire('Erro', response?.messages?.error || 'Erro ao rejeitar pedido', 'error');
                }
            });
        }
    });
}

function verDetalhes(pedidoId) {
    // Abrir modal
    const modal = new bootstrap.Modal(document.getElementById('modalDetalhes'));
    modal.show();
    
    // Mostrar loading
    $('#modalDetalhesBody').html('<div class="text-center py-4"><div class="spinner-border" role="status"><span class="visually-hidden">A carregar...</span></div></div>');
    
    // Carregar dados via AJAX
    $.ajax({
        url: '<?= base_url('ferias/detalhes/') ?>' + pedidoId,
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                mostrarDetalhes(response.data.pedido, response.data.aprovador, response.data.rejeitador);
            } else {
                $('#modalDetalhesBody').html('<div class="alert alert-danger">Erro ao carregar detalhes</div>');
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            $('#modalDetalhesBody').html(
                '<div class="alert alert-danger">' + 
                (response?.messages?.error || 'Erro ao carregar detalhes') + 
                '</div>'
            );
        }
    });
}

function contarDiasUteisIntervalo(inicio, fim) {
    if (!inicio || !fim || fim < inicio) return 0;
    let d = new Date(inicio + 'T00:00:00');
    const f = new Date(fim + 'T00:00:00');
    let dias = 0;
    while (d <= f) {
        const dow = d.getDay();
        if (dow !== 0 && dow !== 6) dias++;
        d.setDate(d.getDate() + 1);
    }
    return dias;
}

function mostrarDetalhes(pedido, aprovador, rejeitador) {
    // Helper para formatar data
    function formatarData(data) {
        if (!data) return '-';
        const d = new Date(data);
        return d.toLocaleDateString('pt-PT');
    }
    
    function formatarDataHora(data) {
        if (!data) return '-';
        const d = new Date(data);
        return d.toLocaleDateString('pt-PT') + ' ' + d.toLocaleTimeString('pt-PT', {hour: '2-digit', minute: '2-digit'});
    }
    
    // Construir HTML com os detalhes
    let periodosHtml = '';
    pedido.periodos.forEach(function(p, idx) {
        periodosHtml += `
            <tr>
                <td><strong>${idx + 1}º Período</strong></td>
                <td>${formatarData(p.data_inicio)} a ${formatarData(p.data_fim)}</td>
                <td class="text-center"><span class="badge bg-primary">${p.dias_uteis} dias</span></td>
            </tr>
        `;
    });
    
    // Formatar estado com badge apropriado
    let estadoBadge = '';
    switch(pedido.estado) {
        case 'submetido':
            estadoBadge = '<span class="badge bg-warning">Submetido</span>';
            break;
        case 'em_aprovacao':
            estadoBadge = '<span class="badge bg-info">Em Aprovação</span>';
            break;
        case 'aprovado':
            estadoBadge = '<span class="badge bg-success">Aprovado</span>';
            break;
        case 'rejeitado':
            estadoBadge = '<span class="badge bg-danger">Rejeitado</span>';
            break;
        case 'remarcacao_solicitada':
            estadoBadge = '<span class="badge bg-warning"><i class="fas fa-clock"></i> Remarcação Solicitada</span>';
            break;
        default:
            estadoBadge = `<span class="badge bg-secondary">${pedido.estado}</span>`;
    }
    
    // Formatar ano letivo
    const anoLetivo = pedido.ano ? `${pedido.ano}/${parseInt(pedido.ano) + 1}` : '-';
    
    // Períodos propostos (remarcação)
    let periodosPropostosHtml = '';
    if (pedido.datas_remarcacao_propostas) {
        let propostos = [];
        try {
            propostos = typeof pedido.datas_remarcacao_propostas === 'string'
                ? JSON.parse(pedido.datas_remarcacao_propostas)
                : pedido.datas_remarcacao_propostas;
        } catch(e) {}
        if (propostos && propostos.length > 0) {
            function formatDataStr(iso) {
                if (!iso) return '-';
                const parts = iso.split('-');
                return parts[2] + '/' + parts[1] + '/' + parts[0];
            }
            let rows = ''; let totalProposto = 0;
            propostos.forEach(function(p, idx) {
                const dias = contarDiasUteisIntervalo(p.data_inicio, p.data_fim);
                totalProposto += dias;
                rows += '<tr><td><strong>' + (idx+1) + 'º Período</strong></td><td>' + formatDataStr(p.data_inicio) + ' a ' + formatDataStr(p.data_fim) + '</td><td class="text-center"><span class="badge bg-warning text-dark">' + dias + ' dias úteis</span></td></tr>';
            });
            const motivoHtml = pedido.motivo_remarcacao
                ? '<div class="alert alert-light border py-2 mb-2"><strong>Motivo:</strong> ' + pedido.motivo_remarcacao + '</div>'
                : '';
            periodosPropostosHtml = '<hr><h6 class="mb-2 text-warning"><i class="bi bi-arrow-clockwise"></i> Novos Períodos Propostos</h6>' + motivoHtml + '<table class="table table-bordered table-sm"><thead class="table-warning"><tr><th width="30%">Período</th><th width="50%">Datas Propostas</th><th width="20%" class="text-center">Dias Úteis</th></tr></thead><tbody>' + rows + '</tbody><tfoot class="table-warning"><tr><td colspan="2" class="text-end"><strong>Total proposto:</strong></td><td class="text-center"><strong><span class="badge bg-warning text-dark">' + totalProposto + ' dias</span></strong></td></tr></tfoot></table>';
        }
    }
    
    const html = `
        <div class="row mb-3">
            <div class="col-md-6">
                <p><strong>Pedido #${pedido.id}</strong></p>
                <p><strong>Funcionário:</strong> ${pedido.nome_professor || '-'}</p>
                <p><strong>Email:</strong> ${pedido.email_professor || '-'}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Ano Letivo:</strong> ${anoLetivo}</p>
                <p><strong>Estado:</strong> ${estadoBadge}</p>
                <p><strong>Submetido em:</strong> ${formatarDataHora(pedido.submetido_em)}</p>
            </div>
        </div>
        
        <hr>
        
        <h6 class="mb-3"><i class="bi bi-calendar-range"></i> Períodos de Férias</h6>
        <table class="table table-bordered table-sm">
            <thead class="table-light">
                <tr>
                    <th width="30%">Período</th>
                    <th width="50%">Datas</th>
                    <th width="20%" class="text-center">Dias Úteis</th>
                </tr>
            </thead>
            <tbody>
                ${periodosHtml}
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="2" class="text-end"><strong>Total de dias solicitados:</strong></td>
                    <td class="text-center"><strong><span class="badge bg-success">${pedido.total_dias} dias</span></strong></td>
                </tr>
            </tfoot>
        </table>
        
        ${periodosPropostosHtml}
        
        ${pedido.observacoes_professor ? `
        <hr>
        <h6 class="mb-2"><i class="bi bi-chat-left-text"></i> Observações do Professor</h6>
        <div class="alert alert-info mb-0">
            ${pedido.observacoes_professor}
        </div>
        ` : ''}
        
        ${pedido.observacoes_secretaria ? `
        <hr>
        <h6 class="mb-2"><i class="bi bi-chat-left-text"></i> Observações da Secretaria</h6>
        <div class="alert alert-warning mb-0">
            ${pedido.observacoes_secretaria}
        </div>
        ` : ''}
        
        ${aprovador ? `
        <hr>
        <p><strong><i class="bi bi-check-circle"></i> Aprovado por:</strong> ${aprovador.name} em ${formatarDataHora(pedido.aprovado_em)}</p>
        ` : ''}
        
        ${rejeitador ? `
        <hr>
        <p><strong><i class="bi bi-x-circle"></i> Rejeitado por:</strong> ${rejeitador.name} em ${formatarDataHora(pedido.rejeitado_em)}</p>
        ${pedido.motivo_rejeicao ? `<p><strong>Motivo:</strong> ${pedido.motivo_rejeicao}</p>` : ''}
        ` : ''}
    `;
    
    $('#modalDetalhesBody').html(html);
    
    // Adicionar botões de ação no footer conforme o estado
    let footerButtons = '';
    
    if (pedido.estado === 'remarcacao_solicitada') {
        footerButtons = `
            <button type="button" class="btn btn-danger" onclick="rejeitarRemarcacaoDaModal(${pedido.id})">
                <i class="bi bi-x-lg"></i> Rejeitar Remarcação
            </button>
            <button type="button" class="btn btn-warning" onclick="aprovarRemarcacaoDaModal(${pedido.id})">
                <i class="bi bi-check-lg"></i> Aprovar Remarcação
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        `;
    } else if (pedido.estado === 'submetido' || pedido.estado === 'em_aprovacao') {
        footerButtons = `
            <button type="button" class="btn btn-danger" onclick="rejeitarDaModal(${pedido.id})">
                <i class="bi bi-x-lg"></i> Rejeitar
            </button>
            <button type="button" class="btn btn-success" onclick="aprovarDaModal(${pedido.id})">
                <i class="bi bi-check-lg"></i> Aprovar
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        `;
    } else {
        footerButtons = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        `;
    }
    
    $('#modalDetalhesFooter').html(footerButtons);
}

function aprovarDaModal(pedidoId) {
    // Fechar modal de detalhes
    bootstrap.Modal.getInstance(document.getElementById('modalDetalhes')).hide();
    
    // Chamar função de aprovação existente
    aprovar(pedidoId);
}

function rejeitarDaModal(pedidoId) {
    // Fechar modal de detalhes
    bootstrap.Modal.getInstance(document.getElementById('modalDetalhes')).hide();
    
    // Chamar função de rejeição existente
    rejeitar(pedidoId);
}

function aprovarRemarcacaoDaModal(pedidoId) {
    // Fechar modal de detalhes
    bootstrap.Modal.getInstance(document.getElementById('modalDetalhes')).hide();
    
    // Chamar função de aprovação de remarcação existente
    aprovarRemarcacao(pedidoId);
}

function rejeitarRemarcacaoDaModal(pedidoId) {
    // Fechar modal de detalhes
    bootstrap.Modal.getInstance(document.getElementById('modalDetalhes')).hide();
    
    // Chamar função de rejeição de remarcação existente
    rejeitarRemarcacao(pedidoId);
}

// ============================================================
// FUNÇÕES PARA REMARCAÇÕES
// ============================================================

function aprovarRemarcacao(pedidoId) {
    Swal.fire({
        title: 'Aprovar Remarcação?',
        html: `
            <p>As <strong>novas datas propostas</strong> serão aplicadas imediatamente ao pedido.</p>
            <p>O professor <strong>não necessita de tomar nenhuma ação adicional</strong>.</p>
            <p class="mt-3"><strong>Deseja confirmar a aprovação?</strong></p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sim, Aprovar',
        confirmButtonColor: '#28a745',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/aprovar-remarcacao/') ?>' + pedidoId,
                type: 'POST',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Remarcação Aprovada!',
                        text: response.message || 'As novas datas foram aplicadas ao pedido.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response?.messages?.error || response?.message || 'Erro ao aprovar remarcação'
                    });
                }
            });
        }
    });
}

function rejeitarRemarcacao(pedidoId) {
    Swal.fire({
        title: 'Rejeitar Remarcação?',
        html: `
            <p>Ao rejeitar esta remarcação:</p>
            <ul class="text-start">
                <li>O pedido de férias <strong>permanecerá aprovado</strong></li>
                <li>As férias ficam <strong>marcadas como estavam</strong></li>
                <li>O motivo ficará registado no <strong>histórico do pedido</strong></li>
            </ul>
        `,
        icon: 'warning',
        input: 'textarea',
        inputLabel: 'Motivo da rejeição (obrigatório)',
        inputPlaceholder: 'Digite o motivo da rejeição da remarcação...',
        inputValidator: (value) => {
            if (!value || value.trim() === '') {
                return 'Por favor, indique o motivo da rejeição!';
            }
        },
        showCancelButton: true,
        confirmButtonText: 'Sim, Rejeitar Remarcação',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/rejeitar-remarcacao/') ?>' + pedidoId,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ motivo: result.value }),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Remarcação Rejeitada!',
                        text: response.message || 'O pedido foi restaurado ao estado anterior.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response?.messages?.error || response?.message || 'Erro ao rejeitar remarcação'
                    });
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
