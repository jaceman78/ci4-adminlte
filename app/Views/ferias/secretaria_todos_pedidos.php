<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="bi bi-list-ul"></i> Todos os Pedidos</h1>
                </div>
                <div class="col-sm-6">
                    <a href="<?= base_url('ferias/secretaria') ?>" class="btn btn-secondary float-sm-right">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <!-- Filtros -->
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-funnel"></i> Filtros</h3>
                </div>
                <div class="card-body">
                    <form method="get">
                        <div class="row">
                            <div class="col-md-2">
                                <select name="estado" class="form-control">
                                    <option value="">Todos os Estados</option>
                                    <option value="submetido" <?= $filtros['estado'] === 'submetido' ? 'selected' : '' ?>>Submetido</option>
                                    <option value="aprovado" <?= $filtros['estado'] === 'aprovado' ? 'selected' : '' ?>>Aprovado</option>
                                    <option value="aguarda_assinatura" <?= $filtros['estado'] === 'aguarda_assinatura' ? 'selected' : '' ?>>Aguarda Assinatura</option>
                                    <option value="rejeitado" <?= $filtros['estado'] === 'rejeitado' ? 'selected' : '' ?>>Rejeitado</option>
                                    <option value="concluido" <?= $filtros['estado'] === 'concluido' ? 'selected' : '' ?>>Concluído</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="origem" class="form-control">
                                    <option value="">Todas as Origens</option>
                                    <option value="professor" <?= ($filtros['origem'] ?? '') === 'professor' ? 'selected' : '' ?>>Pedido Professor</option>
                                    <option value="secretaria" <?= ($filtros['origem'] ?? '') === 'secretaria' ? 'selected' : '' ?>>Marcação Secretaria</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="ano" class="form-control">
                                    <option value="">Todos os Anos</option>
                                    <?php foreach ($anos as $a): ?>
                                    <option value="<?= $a['anoletivo'] ?>" <?= $filtros['ano'] == $a['anoletivo'] ? 'selected' : '' ?>>
                                        <?= $a['anoletivo'] ?>/<?= $a['anoletivo'] + 1 ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="professor" class="form-control" 
                                       placeholder="Nome do professor" value="<?= $filtros['professor'] ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php
                        $zipParams = http_build_query(array_filter([
                            'ano'       => $filtros['ano'] ?? '',
                            'professor' => $filtros['professor'] ?? '',
                        ]));
                    ?>
                    <div class="mt-2">
                        <a href="<?= base_url('ferias/download-zip-assinados') . ($zipParams ? '?' . $zipParams : '') ?>"
                           class="btn btn-success btn-sm">
                            <i class="bi bi-file-zip"></i> Download ZIP — PDFs Assinados
                            <?php if (!empty($filtros['ano'])): ?>
                                (<?= $filtros['ano'] ?>/<?= $filtros['ano'] + 1 ?>)
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Tabela -->
            <div class="card">
                <div class="card-body">
                    <table class="table table-sm" id="pedidosTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Professor</th>
                                <th>Ano</th>
                                <th>Data</th>
                                <th>Origem</th>
                                <th>Dias</th>
                                <th>Estado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                            <?php $daOrigem = empty($pedido['submetido_em']) ? 'secretaria' : 'professor'; ?>
                            <tr>
                                <td><?= $pedido['id'] ?></td>
                                <td><?= esc($pedido['nome_professor']) ?></td>
                                <td><?= $pedido['ano'] + 1 ?></td>
                                <td><?= !empty($pedido['submetido_em']) ? date('d/m/Y', strtotime($pedido['submetido_em'])) : date('d/m/Y', strtotime($pedido['criado_em'])) ?></td>
                                <td>
                                    <?php if ($daOrigem === 'secretaria'): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-building"></i> Secretaria</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-white"><i class="bi bi-person"></i> Professor</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $pedido['total_dias'] ?></td>
                                <td>
                                    <?= formatar_estado_ferias($pedido['estado']) ?>
                                    <?php if (!empty($pedido['requer_acumulacao'])): ?>
                                    <?php
                                        $acumBadge = match($pedido['acumulacao_estado'] ?? 'pendente') {
                                            'autorizada'     => '<span class="badge bg-success ms-1" title="Acumulação autorizada">Acum. ✓</span>',
                                            'nao_autorizada' => '<span class="badge bg-danger ms-1" title="Acumulação não autorizada">Acum. ✗</span>',
                                            default          => '<span class="badge bg-warning text-dark ms-1" title="Aguarda despacho acumulação">Acum. ⏳</span>',
                                        };
                                        echo $acumBadge;
                                    ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="verDetalhes(<?= $pedido['id'] ?>)" title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    
                                    <?php if (!empty($pedido['documento_pdf'])): ?>
                                    <a href="<?= base_url('ferias/download/' . $pedido['id']) ?>" class="btn btn-sm btn-primary" title="Download PDF gerado">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($pedido['documento_assinado'])): ?>
                                    <a href="<?= base_url('ferias/download-assinado/' . $pedido['id']) ?>" class="btn btn-sm btn-success" title="Download PDF assinado">
                                        <i class="bi bi-file-earmark-check"></i>
                                    </a>
                                    <?php else: ?>
                                        <?php if (in_array($pedido['estado'], ['aprovado', 'aguarda_assinatura'])): ?>
                                        <button class="btn btn-sm btn-warning" onclick="regenerarPDF(<?= $pedido['id'] ?>)" title="Regenerar PDF">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($pedido['documento_remarcacao'])): ?>
                                    <a href="<?= base_url('ferias/download-remarcacao/' . $pedido['id']) ?>" class="btn btn-sm btn-secondary" title="Download PDF Alteração de Férias" target="_blank">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="regenerarPDFRemarcacao(<?= $pedido['id'] ?>)" title="Regenerar PDF Alteração de Férias">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                    <?php if (!empty($pedido['documento_remarcacao_assinado'])): ?>
                                    <a href="<?= base_url('ferias/download-remarcacao-assinado/' . $pedido['id']) ?>" class="btn btn-sm btn-success" title="Download Alteração de Férias Assinada">
                                        <i class="bi bi-file-earmark-check-fill"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($pedido['requer_acumulacao'])): ?>
                                    <?php if (!empty($pedido['doc_acumulacao_pdf'])): ?>
                                    <a href="<?= base_url('ferias/download-acumulacao/' . $pedido['id']) ?>" class="btn btn-sm btn-outline-warning" title="Download PDF Acumulação" target="_blank">
                                        <i class="bi bi-stack"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (!empty($pedido['doc_acumulacao_assinado'])): ?>
                                    <a href="<?= base_url('ferias/download-acumulacao-assinado/' . $pedido['id']) ?>" class="btn btn-sm btn-outline-success" title="Download Acumulação Assinada" target="_blank">
                                        <i class="bi bi-stack-overflow"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (($pedido['acumulacao_estado'] ?? '') === 'pendente'): ?>
                                    <button class="btn btn-sm btn-success" onclick="despachoAcumulacao(<?= $pedido['id'] ?>, 'autorizada')" title="Autorizar acumulação">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="despachoAcumulacao(<?= $pedido['id'] ?>, 'nao_autorizada')" title="Não autorizar acumulação">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-secondary" onclick="regenerarPDFAcumulacaoTodos(<?= $pedido['id'] ?>)" title="Regenerar PDF Acumulação">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#pedidosTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json' },
        order: [[3, 'desc']],
        columnDefs: [{ targets: 4, searchable: false, orderable: false }]
    });
});

function verDetalhes(id) {
    // Buscar detalhes via AJAX
    $.ajax({
        url: '<?= base_url('ferias/detalhes') ?>/' + id,
        method: 'GET',
        dataType: 'json',
        beforeSend: function() {
            Swal.fire({
                title: 'A carregar...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(response) {
            if (response.success && response.data && response.data.pedido) {
                const p = response.data.pedido;
                const logs = response.data.logs || [];
                
                // Construir tabela de períodos
                let periodosHTML = '<div class="table-responsive mb-3">';
                periodosHTML += '<table class="table table-sm table-bordered">';
                periodosHTML += '<thead class="bg-light"><tr>';
                periodosHTML += '<th width="30">#</th>';
                periodosHTML += '<th>Data Início</th>';
                periodosHTML += '<th>Data Fim</th>';
                periodosHTML += '<th width="80">Dias Úteis</th>';
                periodosHTML += '</tr></thead><tbody>';
                
                (p.periodos || []).forEach((periodo, index) => {
                    periodosHTML += '<tr>';
                    periodosHTML += '<td class="text-center">' + (index + 1) + '</td>';
                    periodosHTML += '<td>' + periodo.data_inicio + '</td>';
                    periodosHTML += '<td>' + periodo.data_fim + '</td>';
                    periodosHTML += '<td class="text-center"><strong>' + periodo.dias_uteis + '</strong></td>';
                    periodosHTML += '</tr>';
                });
                
                periodosHTML += '</tbody>';
                periodosHTML += '<tfoot class="bg-light"><tr>';
                periodosHTML += '<th colspan="3" class="text-right">TOTAL:</th>';
                periodosHTML += '<th class="text-center">' + p.total_dias + '</th>';
                periodosHTML += '</tr></tfoot>';
                periodosHTML += '</table></div>';
                
                // Construir cronologia a partir de logs
                let cronologiaHTML = '<div class="timeline" style="max-height: 300px; overflow-y: auto;">';
                if (logs.length > 0) {
                    logs.forEach(log => {
                        const data = log.criado_em ? log.criado_em.substring(0, 16).replace('T', ' ') : '';
                        cronologiaHTML += '<div class="mb-3 pb-2" style="border-bottom: 1px solid #eee;">';
                        cronologiaHTML += '<div class="d-flex justify-content-between">';
                        cronologiaHTML += '<strong class="text-primary">' + (log.acao || '') + '</strong>';
                        cronologiaHTML += '<small class="text-muted">' + data + '</small>';
                        cronologiaHTML += '</div>';
                        if (log.detalhes) {
                            cronologiaHTML += '<div class="text-muted small mt-1">' + log.detalhes + '</div>';
                        }
                        cronologiaHTML += '</div>';
                    });
                } else {
                    cronologiaHTML += '<p class="text-muted">Sem registos de cronologia</p>';
                }
                cronologiaHTML += '</div>';
                
                // Estados com cores
                const estadoBadges = {
                    'submetido': '<span class="badge" style="background-color:#007bff;color:white;font-weight:bold;">SUBMETIDO</span>',
                    'aprovado': '<span class="badge" style="background-color:#28a745;color:white;font-weight:bold;">APROVADO</span>',
                    'rejeitado': '<span class="badge" style="background-color:#dc3545;color:white;font-weight:bold;">REJEITADO</span>',
                    'concluido': '<span class="badge" style="background-color:#6c757d;color:white;font-weight:bold;">CONCLUÍDO</span>',
                    'aguarda_assinatura': '<span class="badge" style="background-color:#ffc107;color:#000;font-weight:bold;">AGUARDA ASSINATURA</span>'
                };
                const estadoBadge = estadoBadges[p.estado] || '<span class="badge" style="background-color:#6c757d;color:white;font-weight:bold;">' + (p.estado||'').toUpperCase().replace(/_/g,' ') + '</span>';

                const anoFmt = p.ano ? (parseInt(p.ano)+1) : '—';
                const submFmt = p.submetido_em ? p.submetido_em.substring(0, 16) : '—';
                const origemBadge = p.submetido_em
                    ? '<span class="badge bg-info text-white"><i class="bi bi-person"></i> Pedido Professor</span>'
                    : '<span class="badge bg-warning text-dark"><i class="bi bi-building"></i> Marcação Secretaria</span>';
                const dataLabel = p.submetido_em ? 'Submetido em' : 'Criado em';
                const dataVal = p.submetido_em ? submFmt : (p.criado_em ? p.criado_em.substring(0, 16) : '—');
                
                // Secção acumulação
                let acumHTML = '';
                if (p.requer_acumulacao) {
                    const acumEstadoBadge = p.acumulacao_estado === 'autorizada'
                        ? '<span class="badge bg-success">Autorizada</span>'
                        : p.acumulacao_estado === 'nao_autorizada'
                            ? '<span class="badge bg-danger">Não Autorizada</span>'
                            : '<span class="badge bg-warning text-dark">Pendente</span>';
                    acumHTML = `
                        <hr>
                        <h6 class="text-warning"><i class="bi bi-stack"></i> Pedido de Acumulação de Férias</h6>
                        <p><strong>Estado:</strong> ${acumEstadoBadge}</p>
                        <p><strong>Dias sobrantes:</strong> ${p.dias_sobrantes || 0}</p>
                        ${p.motivo_acumulacao ? '<p><strong>Motivo:</strong> ' + p.motivo_acumulacao + '</p>' : ''}
                        ${p.doc_acumulacao_pdf ? '<a href="<?= base_url('ferias/download-acumulacao/') ?>'+p.id+'" class="btn btn-sm btn-outline-warning me-1" target="_blank"><i class="bi bi-stack"></i> PDF Pedido</a>' : ''}
                        ${p.doc_acumulacao_assinado ? '<a href="<?= base_url('ferias/download-acumulacao-assinado/') ?>'+p.id+'" class="btn btn-sm btn-outline-success me-1" target="_blank"><i class="bi bi-check2-circle"></i> PDF Assinado</a>' : ''}
                    `;
                }

                const html = `
                    <div class="text-left">
                        <div class="mb-3">
                            <p><strong>Professor:</strong> ${p.nome_professor || '—'}</p>
                            <p><strong>Ano Letivo:</strong> ${p.ano || '—'}/${anoFmt}</p>
                            <p><strong>Origem:</strong> ${origemBadge}</p>
                            <p><strong>Estado:</strong> ${estadoBadge}</p>
                            <p><strong>${dataLabel}:</strong> ${dataVal}</p>
                        </div>
                        <h6 class="text-primary"><i class="bi bi-calendar-range"></i> Períodos de Férias:</h6>
                        ${periodosHTML}
                        ${acumHTML}
                        <h6 class="text-primary mt-3"><i class="bi bi-clock-history"></i> Cronologia do Processo:</h6>
                        ${cronologiaHTML}
                    </div>
                `;
                
                Swal.fire({
                    title: 'Pedido #' + p.id,
                    html: html,
                    width: '700px',
                    confirmButtonText: 'Fechar',
                    customClass: { htmlContainer: 'text-left' }
                });
            } else {
                Swal.fire('Erro', 'Não foi possível carregar os detalhes', 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Erro', xhr.responseJSON?.message || 'Erro ao carregar detalhes', 'error');
        }
    });
}

function regenerarPDF(pedidoId) {
    Swal.fire({
        title: 'Regenerar PDF?',
        html: `
            <p>Tem a certeza que deseja regenerar o PDF para este pedido?</p>
            <p class="text-muted">O PDF anterior será substituído.</p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-arrow-repeat"></i> Sim, Regenerar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ffc107',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: '<?= base_url('ferias/regenerar-pdf') ?>/' + pedidoId,
                method: 'POST',
                dataType: 'json'
            }).then(response => {
                if (!response.success) {
                    throw new Error(response.message || 'Erro ao regenerar PDF');
                }
                return response;
            }).catch(error => {
                Swal.showValidationMessage(
                    `Erro: ${error.message || error.responseJSON?.message || 'Erro desconhecido'}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'PDF Regenerado!',
                text: result.value.message,
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        }
    });
}

function despachoAcumulacao(pedidoId, estado) {
    const label = estado === 'autorizada' ? 'Autorizar' : 'Não Autorizar';
    const color = estado === 'autorizada' ? '#28a745' : '#dc3545';
    Swal.fire({
        title: label + ' Acumulação?',
        html: `Confirma o despacho <strong>${estado === 'autorizada' ? 'Autorizada' : 'Não Autorizada'}</strong> para o pedido #${pedidoId}?`,
        icon: estado === 'autorizada' ? 'success' : 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirmar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: color
    }).then((result) => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '<?= base_url('ferias/registar-despacho-acumulacao/') ?>' + pedidoId,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ estado: estado }),
            success: function(r) { Swal.fire('Sucesso!', r.message, 'success').then(() => location.reload()); },
            error: function(xhr) { Swal.fire('Erro', xhr.responseJSON?.messages?.error || 'Erro', 'error'); }
        });
    });
}

function regenerarPDFAcumulacaoTodos(pedidoId) {
    $.post('<?= base_url('ferias/regenerar-pdf-acumulacao/') ?>' + pedidoId)
        .done(function(r) { Swal.fire('Sucesso!', r.message || 'PDF regenerado', 'success').then(() => location.reload()); })
        .fail(function(xhr) { Swal.fire('Erro', xhr.responseJSON?.messages?.error || 'Erro ao regenerar PDF', 'error'); });
}

function regenerarPDFRemarcacao(pedidoId) {
    Swal.fire({
        title: 'Regenerar PDF de Alteração?',
        html: `<p>Tem a certeza que deseja regenerar o documento de <strong>Alteração de Férias</strong>?</p>
               <p class="text-muted">O PDF anterior será substituído.</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-arrow-clockwise"></i> Sim, Regenerar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#6c757d',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                url: '<?= base_url('ferias/regenerar-pdf-remarcacao') ?>/' + pedidoId,
                method: 'POST',
                dataType: 'json'
            }).then(response => {
                if (!response.success) {
                    throw new Error(response.message || 'Erro ao regenerar PDF');
                }
                return response;
            }).catch(error => {
                Swal.showValidationMessage(
                    `Erro: ${error.message || error.responseJSON?.message || 'Erro desconhecido'}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'PDF Regenerado!',
                text: result.value?.message || 'PDF de alteração regenerado com sucesso.',
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload();
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
