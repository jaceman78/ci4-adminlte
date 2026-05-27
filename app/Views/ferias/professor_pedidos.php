<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="bi bi-clock-history"></i> Histórico de Pedidos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('ferias') ?>">Férias</a></li>
                        <li class="breadcrumb-item active">Histórico</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <?php if (empty($pedidos)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Ainda não submeteu nenhum pedido de férias.
            </div>
            <?php else: ?>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-list-ul"></i> Todos os Meus Pedidos</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="pedidosTable">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>Ano Letivo</th>
                                    <th>Data Submissão</th>
                                    <th>Períodos</th>
                                    <th>Total Dias</th>
                                    <th>Estado</th>
                                    <th style="min-width:220px">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td><strong>#<?= $pedido['id'] ?></strong></td>
                                    <td><?= $pedido['ano'] + 1 ?></td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($pedido['submetido_em'] ?? $pedido['criado_em'])) ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($pedido['periodos'])): ?>
                                            <small>
                                                <?php foreach ($pedido['periodos'] as $i => $periodo): ?>
                                                    <?= date('d/m', strtotime($periodo['data_inicio'])) ?> - <?= date('d/m', strtotime($periodo['data_fim'])) ?>
                                                    <?php if ($i < count($pedido['periodos']) - 1): ?><br><?php endif; ?>
                                                <?php endforeach; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= $pedido['total_dias'] ?></strong> dias</td>
                                    <td><?= formatar_estado_ferias($pedido['estado']) ?></td>
                                    <td>
                                        <?php if (!empty($pedido['documento_pdf']) && $pedido['estado'] !== 'cancelado'): ?>
                                        <a href="<?= base_url('ferias/download/' . $pedido['id']) ?>" 
                                           class="btn btn-sm btn-primary" title="Download PDF gerado">
                                            <i class="bi bi-file-pdf"></i>
                                        </a>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($pedido['documento_assinado']) && $pedido['estado'] !== 'cancelado'): ?>
                                        <a href="<?= base_url('ferias/download-assinado/' . $pedido['id']) ?>" 
                                           class="btn btn-sm btn-success" title="Download PDF assinado">
                                            <i class="bi bi-file-earmark-check"></i>
                                        </a>
                                        <?php elseif (!empty($pedido['documento_pdf']) && in_array($pedido['estado'], ['aprovado', 'aguarda_assinatura'])): ?>
                                        <button class="btn btn-sm btn-outline-success" onclick="uploadAssinado(<?= $pedido['id'] ?>)"
                                                title="Upload documento assinado">
                                            <i class="bi bi-upload"></i>
                                        </button>
                                        <?php endif; ?>

                                        <?php if (!empty($pedido['documento_remarcacao']) && $pedido['estado'] !== 'cancelado'): ?>
                                        <a href="<?= base_url('ferias/download-remarcacao/' . $pedido['id']) ?>"
                                           class="btn btn-sm btn-secondary" title="Download PDF Alteração de Férias" target="_blank">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                        <?php if (!empty($pedido['documento_remarcacao_assinado'])): ?>
                                        <a href="<?= base_url('ferias/download-remarcacao-assinado/' . $pedido['id']) ?>"
                                           class="btn btn-sm btn-success" title="Download Alteração Assinada">
                                            <i class="bi bi-file-earmark-check-fill"></i>
                                        </a>
                                        <?php elseif (in_array($pedido['estado'], ['remarcacao_solicitada', 'remarcacao_aprovada'])): ?>
                                        <button class="btn btn-sm btn-outline-success" onclick="uploadAlteracaoAssinada(<?= $pedido['id'] ?>)"
                                                title="Upload Alteração Assinada">
                                            <i class="bi bi-upload"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if (in_array($pedido['estado'], ['submetido', 'em_aprovacao'])): ?>
                                        <button class="btn btn-sm btn-danger" 
                                                onclick="cancelarPedido(<?= $pedido['id'] ?>)" 
                                                title="Cancelar Pedido">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                        <?php endif; ?>
                                        
                                        <?php if (in_array($pedido['estado'], ['aprovado', 'aguarda_assinatura', 'concluido'])): ?>
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="solicitarRemarcacao(<?= $pedido['id'] ?>, <?= htmlspecialchars(json_encode($pedido['observacoes_professor'] ?? ''), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($pedido['remarcacao_info'] ?? []), ENT_QUOTES) ?>)" 
                                                title="Solicitar Remarcação">
                                            <i class="bi bi-arrow-clockwise"></i>
                                        </button>
                                        <?php elseif ($pedido['estado'] === 'remarcacao_solicitada'): ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-warning fs-6" style="padding: 8px 12px;" title="Remarcação solicitada - Aguarda aprovação da secretaria">
                                                <i class="bi bi-hourglass-split"></i> Remarcação Pendente
                                            </span>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <button class="btn btn-sm btn-info" 
                                                onclick="verDetalhes(<?= $pedido['id'] ?>)" 
                                                title="Ver Detalhes">
                                            <i class="bi bi-eye"></i>
                                        </button>
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

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
const userTelefone = <?= json_encode($userTelefone ?? '') ?>;

$(document).ready(function() {
    $('#pedidosTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        order: [[2, 'desc']],
        pageLength: 25
    });
});

function verDetalhes(pedidoId) {
    // Fazer chamada AJAX para buscar detalhes
    $.ajax({
        url: '<?= base_url('ferias/detalhes/') ?>' + pedidoId,
        type: 'GET',
        beforeSend: function() {
            Swal.fire({
                title: 'A carregar detalhes...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            if (response.success && response.data) {
                const pedido = response.data.pedido;
                const aprovador = response.data.aprovador;
                const rejeitador = response.data.rejeitador;
                
                // Formatar períodos
                let periodosHtml = '<table class="table table-sm table-bordered mt-2" style="font-size: 0.9rem;"><thead><tr><th>#</th><th>Data Início</th><th>Data Fim</th><th>Dias Úteis</th></tr></thead><tbody>';
                let num = 1;
                pedido.periodos.forEach(function(periodo) {
                    periodosHtml += `
                        <tr>
                            <td>${num++}</td>
                            <td>${formatarData(periodo.data_inicio)}</td>
                            <td>${formatarData(periodo.data_fim)}</td>
                            <td><strong>${periodo.dias_uteis}</strong></td>
                        </tr>
                    `;
                });
                periodosHtml += `
                    <tr class="table-info">
                        <td colspan="3" class="text-end"><strong>TOTAL:</strong></td>
                        <td><strong>${pedido.total_dias}</strong></td>
                    </tr>
                </tbody></table>`;
                
                // Montar HTML dos detalhes
                let html = `
                    <div class="text-start" style="max-height: 500px; overflow-y: auto;">
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <strong>Pedido:</strong> #${pedido.id}
                                </div>
                                <div class="col-6 text-end">
                                    <strong>Ano Letivo:</strong> ${pedido.ano}/${parseInt(pedido.ano) + 1}
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Estado:</strong> ${formatarEstado(pedido.estado)}
                        </div>
                        
                        <div class="mb-3">
                            <strong>Total de Dias:</strong> <span class="badge bg-primary">${pedido.total_dias} dias</span>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mb-2"><i class="bi bi-calendar-range"></i> Períodos de Férias</h6>
                        ${periodosHtml}
                        
                        <hr>
                        
                        <h6><i class="bi bi-clock-history"></i> Histórico</h6>
                        <ul class="list-unstyled" style="font-size: 0.9rem;">
                `;
                
                // Data de criação
                if (pedido.criado_em) {
                    html += `<li><i class="bi bi-circle-fill text-secondary" style="font-size: 0.5rem;"></i> <strong>Criado:</strong> ${formatarDataHora(pedido.criado_em)}</li>`;
                }
                
                // Data de submissão
                if (pedido.submetido_em) {
                    html += `<li><i class="bi bi-circle-fill text-info" style="font-size: 0.5rem;"></i> <strong>Submetido:</strong> ${formatarDataHora(pedido.submetido_em)}</li>`;
                }
                
                // Data de aprovação
                if (pedido.aprovado_em && aprovador) {
                    html += `<li><i class="bi bi-circle-fill text-success" style="font-size: 0.5rem;"></i> <strong>Aprovado:</strong> ${formatarDataHora(pedido.aprovado_em)} por ${aprovador.name}</li>`;
                }
                
                // Data de rejeição
                if (pedido.rejeitado_em && rejeitador) {
                    html += `<li><i class="bi bi-circle-fill text-danger" style="font-size: 0.5rem;"></i> <strong>Rejeitado:</strong> ${formatarDataHora(pedido.rejeitado_em)} por ${rejeitador.name}</li>`;
                }
                
                // Upload de documento assinado
                if (pedido.upload_assinatura_em) {
                    html += `<li><i class="bi bi-circle-fill text-success" style="font-size: 0.5rem;"></i> <strong>Documento Assinado Entregue:</strong> ${formatarDataHora(pedido.upload_assinatura_em)}</li>`;
                }
                
                html += '</ul>';
                
                // Observações do professor
                if (pedido.observacoes_professor) {
                    html += `
                        <hr>
                        <h6><i class="bi bi-chat-left-text"></i> Observações (Professor)</h6>
                        <div class="alert alert-info mb-2" style="font-size: 0.9rem;">${escapeHtml(pedido.observacoes_professor)}</div>
                    `;
                }
                
                // Observações da secretaria
                if (pedido.observacoes_secretaria) {
                    html += `
                        <hr>
                        <h6><i class="bi bi-chat-left-text"></i> Observações (Secretaria)</h6>
                        <div class="alert alert-warning mb-2" style="font-size: 0.9rem;">${escapeHtml(pedido.observacoes_secretaria)}</div>
                    `;
                }
                
                // Motivo de rejeição (se existir)
                if (pedido.motivo_rejeicao) {
                    html += `
                        <hr>
                        <h6 class="text-danger"><i class="bi bi-exclamation-triangle"></i> Motivo da Rejeição</h6>
                        <div class="alert alert-danger mb-2" style="font-size: 0.9rem;">${escapeHtml(pedido.motivo_rejeicao)}</div>
                    `;
                }
                
                // Links para documentos (não mostrar em pedidos cancelados)
                if (pedido.estado !== 'cancelado' && (pedido.documento_pdf || pedido.documento_assinado)) {
                    html += `<hr><h6><i class="bi bi-file-pdf"></i> Documentos</h6><div class="d-flex gap-2">`;
                    
                    if (pedido.documento_pdf) {
                        html += `
                            <a href="<?= base_url('ferias/download/') ?>${pedido.id}" class="btn btn-sm btn-primary" target="_blank">
                                <i class="bi bi-download"></i> PDF Gerado
                            </a>
                        `;
                    }
                    
                    if (pedido.documento_assinado) {
                        html += `
                            <a href="<?= base_url('ferias/download-assinado/') ?>${pedido.id}" class="btn btn-sm btn-success" target="_blank">
                                <i class="bi bi-download"></i> PDF Assinado
                            </a>
                        `;
                    }
                    
                    html += '</div>';
                }
                
                html += '</div>';
                
                Swal.fire({
                    title: `<i class="bi bi-info-circle"></i> Detalhes do Pedido #${pedido.id}`,
                    html: html,
                    width: '700px',
                    confirmButtonText: 'Fechar',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Não foi possível carregar os detalhes do pedido'
                });
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: response.messages?.error || response.message || 'Erro ao carregar detalhes'
            });
        }
    });
}

// Funções auxiliares
function formatarData(data) {
    const d = new Date(data + 'T00:00:00');
    return d.toLocaleDateString('pt-PT');
}

function formatarDataHora(dataHora) {
    const d = new Date(dataHora);
    return d.toLocaleDateString('pt-PT') + ' às ' + d.toLocaleTimeString('pt-PT', {hour: '2-digit', minute: '2-digit'});
}

function formatarEstado(estado) {
    const estados = {
        'por_preencher': '<span class="badge bg-secondary">Por Preencher</span>',
        'submetido': '<span class="badge bg-info">Submetido</span>',
        'em_aprovacao': '<span class="badge bg-warning">Em Aprovação</span>',
        'aprovado': '<span class="badge bg-success">Aprovado</span>',
        'rejeitado': '<span class="badge bg-danger">Rejeitado</span>',
        'cancelado': '<span class="badge bg-dark">Cancelado</span>',
        'aguarda_assinatura': '<span class="badge bg-primary">Aguarda Assinatura</span>',
        'concluido': '<span class="badge bg-success">Concluído</span>',
        'remarcacao_solicitada': '<span class="badge bg-warning"><i class="bi bi-clock"></i> Remarcação Solicitada</span>'
    };
    return estados[estado] || estado;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/\n/g, '<br>');
}

function uploadAssinado(pedidoId) {
    Swal.fire({
        title: 'Upload Documento Assinado',
        html: `
            <p>Selecione o documento assinado (PDF, JPG ou PNG):</p>
            <input type="file" id="fileInput" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const file = document.getElementById('fileInput').files[0];
            if (!file) {
                Swal.showValidationMessage('Selecione um ficheiro');
                return false;
            }
            return file;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const csrfToken = document.cookie.split('; ').find(r => r.startsWith('csrf_cookie_name='))?.split('=')[1] ?? '';
            const formData = new FormData();
            formData.append('documento_assinado', result.value);
            formData.append('<?= csrf_token() ?>', csrfToken);

            $.ajax({
                url: '<?= base_url('ferias/upload-documento/') ?>' + pedidoId,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire('Sucesso!', 'Documento enviado com sucesso', 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Erro', xhr.responseJSON?.messages?.error || 'Erro ao enviar documento', 'error');
                }
            });
        }
    });
}

function uploadAlteracaoAssinada(pedidoId) {
    Swal.fire({
        title: 'Upload Alteração de Férias Assinada',
        html: `
            <p>Selecione o documento de Alteração de Férias assinado (PDF, JPG ou PNG):</p>
            <input type="file" id="fileInputAlt" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const file = document.getElementById('fileInputAlt').files[0];
            if (!file) {
                Swal.showValidationMessage('Selecione um ficheiro');
                return false;
            }
            return file;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const csrfToken = document.cookie.split('; ').find(r => r.startsWith('csrf_cookie_name='))?.split('=')[1] ?? '';
            const formData = new FormData();
            formData.append('documento_remarcacao_assinado', result.value);
            formData.append('<?= csrf_token() ?>', csrfToken);

            $.ajax({
                url: '<?= base_url('ferias/upload-remarcacao-assinado/') ?>' + pedidoId,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire('Sucesso!', 'Documento de Alteração enviado com sucesso', 'success')
                        .then(() => location.reload());
                },
                error: function(xhr) {
                    Swal.fire('Erro', xhr.responseJSON?.messages?.error || 'Erro ao enviar documento', 'error');
                }
            });
        }
    });
}

function cancelarPedido(pedidoId) {
    Swal.fire({
        title: 'Cancelar Pedido?',
        html: `
            <p>Tem a certeza que deseja cancelar este pedido?</p>
            <ul class="text-start">
                <li>O pedido será <strong>cancelado</strong></li>
                <li>Os dias ficarão novamente <strong>disponíveis</strong></li>
                <li>Poderá submeter um novo pedido</li>
            </ul>
            <p class="mt-3"><strong>Deseja continuar?</strong></p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, cancelar pedido',
        cancelButtonText: 'Não',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/cancelar/') ?>' + pedidoId,
                type: 'POST',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pedido Cancelado',
                        text: response.message || 'Pedido cancelado com sucesso. Os dias foram devolvidos ao seu saldo.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.messages?.error || response.message || 'Erro ao cancelar pedido'
                    });
                }
            });
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

function atualizarContadorRemarcacao() {
    let total = 0;
    for (let i = 0; i < 3; i++) {
        const ini = document.getElementById('swal-inicio-' + i)?.value;
        const fim = document.getElementById('swal-fim-' + i)?.value;
        if (ini && fim && fim >= ini) total += contarDiasUteisIntervalo(ini, fim);
    }
    const counter = document.getElementById('swal-dias-counter');
    if (!counter) return;
    const target = parseInt(counter.dataset.target || '0');
    counter.textContent = total;
    if (target > 0) {
        counter.className = 'fw-bold ' + (total === target ? 'text-success' : total > target ? 'text-danger' : 'text-warning');
    }
}

function solicitarRemarcacao(pedidoId, moradaAtual, remarcacaoInfo) {
    moradaAtual    = moradaAtual    || '';
    remarcacaoInfo = remarcacaoInfo || {};
    const totalDiasOriginal = remarcacaoInfo.total_dias_original || 0;
    const permiteFora       = remarcacaoInfo.permite_fora_periodo !== undefined ? !!remarcacaoInfo.permite_fora_periodo : true;
    const obrigaTotalidade  = remarcacaoInfo.obriga_totalidade    !== undefined ? !!remarcacaoInfo.obriga_totalidade    : false;
    const dataInicioPeriodo = remarcacaoInfo.data_inicio_permitida || null;
    const dataFimPeriodo    = remarcacaoInfo.data_fim_permitida    || null;
    const telefoneFaltante  = !userTelefone;

    function formatarDataPT(iso) {
        if (!iso) return '';
        const parts = iso.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    const minDate = (!permiteFora && dataInicioPeriodo) ? ' min="' + dataInicioPeriodo + '"' : '';
    const maxDate = (!permiteFora && dataFimPeriodo)    ? ' max="' + dataFimPeriodo    + '"' : '';

    let infoPeriodo = '';
    if (dataInicioPeriodo && dataFimPeriodo) {
        infoPeriodo = permiteFora
            ? '<br><small class="text-success"><i class="bi bi-unlock"></i> Pode marcar fora do período (' + formatarDataPT(dataInicioPeriodo) + ' a ' + formatarDataPT(dataFimPeriodo) + ')</small>'
            : '<br><small><i class="bi bi-lock"></i> Período permitido: <strong>' + formatarDataPT(dataInicioPeriodo) + '</strong> a <strong>' + formatarDataPT(dataFimPeriodo) + '</strong></small>';
    }

    const infoContador = totalDiasOriginal > 0
        ? '<div class="alert alert-info text-start py-2 mb-3"><i class="bi bi-calendar-check"></i> <strong>Dias a propor:</strong> <span id="swal-dias-counter" class="fw-bold" data-target="' + totalDiasOriginal + '">0</span> / <strong>' + totalDiasOriginal + '</strong> dias úteis' + infoPeriodo + '</div>'
        : '';

    const campoTelefone = telefoneFaltante ? `
        <div class="text-start mt-3">
            <label class="form-label fw-bold">Telemóvel/Telefone: <span class="text-danger">*</span></label>
            <input type="text" id="swal-telefone" class="swal2-input" style="margin:0;width:100%;" maxlength="20" placeholder="Ex: 912 345 678">
        </div>` : '';

    Swal.fire({
        title: 'Solicitar Alteração de Férias',
        html: `
            ${infoContador}
            <div class="alert alert-warning text-start py-2 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Atenção:</strong> Este pedido deve ser formulado com antecedência mínima de <strong>10 dias úteis</strong>.
                Em caso de urgência devidamente justificada, poderá ser dispensada a antecedência.
            </div>
            <p class="text-start mb-3">Para gerar o documento oficial, preencha os dados abaixo:</p>
            <div class="text-start mb-3">
                <label class="form-label fw-bold">Motivo da alteração: <span class="text-danger">*</span></label>
                <input type="text" id="swal-motivo" class="swal2-input" style="margin:0;width:100%;" maxlength="500" placeholder="Ex: Motivos familiares">
            </div>
            <div class="text-start mb-3">
                <label class="form-label fw-bold">Morada durante as férias: <span class="text-danger">*</span></label>
                <input type="text" id="swal-morada" class="swal2-input" style="margin:0;width:100%;" maxlength="300" placeholder="Rua, localidade...">
            </div>
            <div class="text-start mb-2">
                <label class="form-label fw-bold">Novos períodos propostos: <span class="text-danger">*</span></label>
                <small class="text-muted d-block mb-2">Indique até 3 períodos (pelo menos 1 obrigatório)</small>
                <div class="d-flex align-items-center gap-1 mb-1">
                    <small class="text-nowrap">1.</small>
                    <input type="date" id="swal-inicio-0" class="swal2-input" style="margin:0;flex:1;"${minDate}${maxDate} oninput="atualizarContadorRemarcacao()">
                    <small class="text-nowrap px-1">a</small>
                    <input type="date" id="swal-fim-0" class="swal2-input" style="margin:0;flex:1;"${minDate}${maxDate} oninput="atualizarContadorRemarcacao()">
                </div>
                <div class="d-flex align-items-center gap-1 mb-1">
                    <small class="text-nowrap">2.</small>
                    <input type="date" id="swal-inicio-1" class="swal2-input" style="margin:0;flex:1;"${minDate}${maxDate} oninput="atualizarContadorRemarcacao()">
                    <small class="text-nowrap px-1">a</small>
                    <input type="date" id="swal-fim-1" class="swal2-input" style="margin:0;flex:1;"${minDate}${maxDate} oninput="atualizarContadorRemarcacao()">
                </div>
                <div class="d-flex align-items-center gap-1">
                    <small class="text-nowrap">3.</small>
                    <input type="date" id="swal-inicio-2" class="swal2-input" style="margin:0;flex:1;"${minDate}${maxDate} oninput="atualizarContadorRemarcacao()">
                    <small class="text-nowrap px-1">a</small>
                    <input type="date" id="swal-fim-2" class="swal2-input" style="margin:0;flex:1;"${minDate}${maxDate} oninput="atualizarContadorRemarcacao()">
                </div>
            </div>
            ${campoTelefone}
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Solicitar Alteração',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        didOpen: () => {
            document.getElementById('swal-morada').value = moradaAtual;
            atualizarContadorRemarcacao();
        },
        preConfirm: () => {
            const motivo = document.getElementById('swal-motivo').value.trim();
            if (!motivo) {
                Swal.showValidationMessage('O motivo da alteração é obrigatório');
                return false;
            }
            const morada = document.getElementById('swal-morada').value.trim();
            if (!morada) {
                Swal.showValidationMessage('A morada durante as férias é obrigatória');
                return false;
            }
            const inicio0 = document.getElementById('swal-inicio-0').value;
            const fim0    = document.getElementById('swal-fim-0').value;
            if (!inicio0 || !fim0) {
                Swal.showValidationMessage('O primeiro período de datas é obrigatório');
                return false;
            }
            const periodos = [];
            let totalDias = 0;
            for (let i = 0; i < 3; i++) {
                const ini = document.getElementById('swal-inicio-' + i)?.value;
                const fim = document.getElementById('swal-fim-' + i)?.value;
                if (ini && fim) {
                    if (fim < ini) {
                        Swal.showValidationMessage('Data de fim deve ser posterior à de início no período ' + (i + 1));
                        return false;
                    }
                    if (!permiteFora && dataInicioPeriodo && dataFimPeriodo) {
                        if (ini < dataInicioPeriodo || fim > dataFimPeriodo) {
                            Swal.showValidationMessage('As datas do período ' + (i + 1) + ' estão fora do período permitido (' + formatarDataPT(dataInicioPeriodo) + ' a ' + formatarDataPT(dataFimPeriodo) + ')');
                            return false;
                        }
                    }
                    totalDias += contarDiasUteisIntervalo(ini, fim);
                    periodos.push({ data_inicio: ini, data_fim: fim });
                }
            }
            if (obrigaTotalidade && totalDiasOriginal > 0 && totalDias !== totalDiasOriginal) {
                Swal.showValidationMessage('Deve propor exatamente ' + totalDiasOriginal + ' dias úteis (propôs ' + totalDias + ')');
                return false;
            }
            const data = {
                motivo_remarcacao: motivo,
                morada:            morada,
                datas_propostas:   JSON.stringify(periodos)
            };
            if (telefoneFaltante) {
                const tel = document.getElementById('swal-telefone')?.value?.trim();
                if (!tel) {
                    Swal.showValidationMessage('O telemóvel/telefone é obrigatório para o documento');
                    return false;
                }
                data.telefone = tel;
            }
            return data;
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            $.ajax({
                url: '<?= base_url('ferias/remarcar/') ?>' + pedidoId,
                type: 'POST',
                data: result.value,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Remarcação Solicitada',
                        html: (response.message || 'Pedido de remarcação enviado. Aguarde aprovação da secretaria.') +
                              (response.pdf_gerado ? '<br><small class="text-success"><i class="bi bi-file-pdf"></i> Documento de alteração gerado com sucesso.</small>' : ''),
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.messages?.error || response.message || 'Erro ao solicitar remarcação'
                    });
                }
            });
        }
    });
}

function confirmarRemarcacao(pedidoId) {
    Swal.fire({
        title: 'Aplicar Remarcação?',
        html: `
            <p>A remarcação foi <strong>aprovada pela secretaria</strong>.</p>
            <p>Ao confirmar, as datas atuais do pedido serão substituídas pelas <strong>novas datas propostas</strong>.</p>
            <p class="mt-3"><strong>Deseja aplicar a remarcação?</strong></p>
        `,
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Sim, aplicar',
        cancelButtonText: 'Agora não',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/confirmar-remarcacao/') ?>' + pedidoId,
                type: 'POST',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Remarcação Efetivada',
                        text: response.message || 'As novas datas estão agora ativas.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.messages?.error || response.message || 'Erro ao aplicar remarcação'
                    });
                }
            });
        }
    });
}
</script>

<?= $this->endSection() ?>
