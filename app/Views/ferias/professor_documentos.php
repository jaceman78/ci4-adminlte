<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="bi bi-file-earmark-pdf"></i> Meus Documentos</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('ferias') ?>">Férias</a></li>
                        <li class="breadcrumb-item active">Documentos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <?php if (empty($pedidos)): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Ainda não possui documentos de férias aprovados.
            </div>
            <?php else: ?>
            
            <div class="row">
                <?php foreach ($pedidos as $pedido): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="bi bi-file-pdf"></i> 
                                Pedido #<?= $pedido['id'] ?> - <?= $pedido['ano'] + 1 ?>
                            </h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Estado:</strong> <?= formatar_estado_ferias($pedido['estado']) ?></p>
                            <p><strong><?= !empty($pedido['aprovado_em']) ? 'Aprovado em' : 'Submetido em' ?>:</strong>
                                <?= !empty($pedido['aprovado_em'])
                                    ? date('d/m/Y H:i', strtotime($pedido['aprovado_em']))
                                    : (!empty($pedido['submetido_em']) ? date('d/m/Y H:i', strtotime($pedido['submetido_em'])) : '—')
                                ?>
                            </p>
                            <p><strong>Total Dias:</strong> <?= $pedido['total_dias'] ?> dias úteis</p>
                            
                            <hr>

                            <?php if (!empty($pedido['documento_remarcacao'])): ?>
                            <?php // Documento principal: Alteração de Férias ?>
                            <p class="small text-muted mb-1"><strong><i class="bi bi-file-earmark-text"></i> Documento de Alteração de Férias</strong></p>
                            <div class="btn-group d-flex gap-2 mb-2">
                                <a href="<?= base_url('ferias/download-remarcacao/' . $pedido['id']) ?>"
                                   class="btn btn-secondary flex-fill" target="_blank">
                                    <i class="bi bi-download"></i> Download Alteração
                                </a>
                                <?php if (empty($pedido['documento_remarcacao_assinado'])): ?>
                                <button class="btn btn-outline-success flex-fill"
                                        onclick="uploadAlteracaoAssinada(<?= $pedido['id'] ?>)">
                                    <i class="bi bi-upload"></i> Upload Assinado
                                </button>
                                <?php else: ?>
                                <a href="<?= base_url('ferias/download-remarcacao-assinado/' . $pedido['id']) ?>"
                                   class="btn btn-success flex-fill">
                                    <i class="bi bi-check-circle"></i> Ver Assinado
                                </a>
                                <?php endif; ?>
                            </div>
                            <p class="small text-muted mb-1 mt-3"><strong><i class="bi bi-file-pdf"></i> Documento Original de Férias</strong></p>
                            <a href="<?= base_url('ferias/download/' . $pedido['id']) ?>"
                               class="btn btn-outline-primary btn-sm w-100 mb-2" target="_blank">
                                <i class="bi bi-download"></i> Download Original
                            </a>
                            <?php else: ?>
                            <?php // Documento principal: Pedido de Férias original ?>
                            <div class="btn-group d-flex gap-2 mb-2">
                                <a href="<?= base_url('ferias/download/' . $pedido['id']) ?>" 
                                   class="btn btn-primary flex-fill">
                                    <i class="bi bi-download"></i> Download PDF
                                </a>
                                
                                <?php if ($pedido['documento_assinado']): ?>
                                <a href="<?= base_url('ferias/download-assinado/' . $pedido['id']) ?>" 
                                   class="btn btn-success flex-fill">
                                    <i class="bi bi-check-circle"></i> Ver Assinado
                                </a>
                                <?php endif; ?>
                                
                                <button class="btn <?= $pedido['documento_assinado'] ? 'btn-outline-success' : 'btn-success' ?> flex-fill" 
                                        onclick="uploadAssinado(<?= $pedido['id'] ?>)"
                                        title="<?= $pedido['documento_assinado'] ? 'Substituir documento assinado' : 'Carregar documento assinado' ?>">
                                    <i class="bi bi-upload"></i> <?= $pedido['documento_assinado'] ? 'Re-upload' : 'Upload Assinado' ?>
                                </button>
                            </div>
                            
                            <?php if (in_array($pedido['estado'], ['aprovado', 'aguarda_assinatura', 'concluido'])): ?>
                            <button class="btn btn-warning btn-sm w-100" 
                                    onclick="solicitarRemarcacao(<?= $pedido['id'] ?>, <?= htmlspecialchars(json_encode($pedido['observacoes_professor'] ?? ''), ENT_QUOTES) ?>, <?= htmlspecialchars(json_encode($pedido['remarcacao_info'] ?? []), ENT_QUOTES) ?>)">
                                <i class="bi bi-arrow-clockwise"></i> Solicitar Remarcação
                            </button>
                            <?php endif; ?>
                            <?php endif; ?>

                            <?php if (!empty($pedido['requer_acumulacao'])): ?>
                            <!-- ── PEDIDO DE ACUMULAÇÃO DE FÉRIAS ── -->
                            <hr>
                            <p class="small fw-bold mb-1">
                                <i class="bi bi-file-earmark-text text-warning"></i> Pedido de Acumulação (art.º 89 ECD)
                                <?php
                                $badgeAcum = match($pedido['acumulacao_estado'] ?? 'pendente') {
                                    'autorizada'     => '<span class="badge bg-success">Autorizada</span>',
                                    'nao_autorizada' => '<span class="badge bg-danger">Não Autorizada</span>',
                                    default          => '<span class="badge bg-warning text-dark">Pendente</span>',
                                };
                                echo $badgeAcum;
                                ?>
                            </p>
                            <p class="small text-muted mb-2">
                                <strong><?= $pedido['dias_sobrantes'] ?? 0 ?></strong> dias sobrantes
                                <?php if (!empty($pedido['motivo_acumulacao'])): ?>
                                — <em><?= esc($pedido['motivo_acumulacao']) ?></em>
                                <?php endif; ?>
                            </p>
                            <div class="d-flex gap-2 flex-wrap">
                                <?php if (!empty($pedido['doc_acumulacao_pdf'])): ?>
                                <a href="<?= base_url('ferias/download-acumulacao/' . $pedido['id']) ?>"
                                   class="btn btn-outline-secondary btn-sm flex-fill" target="_blank">
                                    <i class="bi bi-download"></i> Download Acumulação
                                </a>
                                <?php endif; ?>
                                <?php if (!empty($pedido['doc_acumulacao_assinado'])): ?>
                                <a href="<?= base_url('ferias/download-acumulacao-assinado/' . $pedido['id']) ?>"
                                   class="btn btn-success btn-sm flex-fill" target="_blank">
                                    <i class="bi bi-check-circle"></i> Ver Assinado
                                </a>
                                <?php endif; ?>
                                <button class="btn <?= !empty($pedido['doc_acumulacao_assinado']) ? 'btn-outline-success' : 'btn-success' ?> btn-sm flex-fill"
                                        onclick="uploadAcumulacaoAssinada(<?= $pedido['id'] ?>)">
                                    <i class="bi bi-upload"></i>
                                    <?= !empty($pedido['doc_acumulacao_assinado']) ? 'Re-upload Assinado' : 'Upload Assinado' ?>
                                </button>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php endif; ?>
            
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
const userTelefone = <?= json_encode($userTelefone ?? '') ?>;

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

function uploadAcumulacaoAssinada(pedidoId) {
    Swal.fire({
        title: 'Upload Pedido de Acumulação Assinado',
        html: `
            <p>Selecione o documento de Pedido de Acumulação assinado (PDF, JPG ou PNG):</p>
            <input type="file" id="fileInputAcum" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const file = document.getElementById('fileInputAcum').files[0];
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
            formData.append('doc_acumulacao_assinado', result.value);
            formData.append('<?= csrf_token() ?>', csrfToken);

            $.ajax({
                url: '<?= base_url('ferias/upload-acumulacao-assinado/') ?>' + pedidoId,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire('Sucesso!', 'Documento de acumulação enviado com sucesso', 'success')
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
</script>

<?= $this->endSection() ?>
