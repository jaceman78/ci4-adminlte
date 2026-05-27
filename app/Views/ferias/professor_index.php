<?= $this->extend('layout/master') ?>

<?= $this->section('pageHeader') ?>
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-umbrella-beach"></i> Minhas Férias</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Férias</li>
        </ol>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php if (isset($sem_atribuicao) && $sem_atribuicao): ?>
    <!-- Sem Atribuição -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-info">
                <h5><i class="icon fas fa-info"></i> Informação!</h5>
                Ainda não tem dias de férias atribuídos para o ano letivo <?= $ano_letivo['anoletivo'] ?>/<?= $ano_letivo['anoletivo'] + 1 ?>.
                Por favor, contacte a secretaria.
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Saldo de Férias -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= $saldo['dias_total'] ?? 0 ?></h3>
                    <p>Dias Atribuídos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?= $saldo['dias_gastos'] ?? 0 ?></h3>
                    <p>Dias Marcados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?= $saldo['dias_disponiveis'] ?? 0 ?></h3>
                    <p>Dias Disponíveis</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-plus"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3><?= $saldo['dias_ajuste'] ?? 0 ?></h3>
                    <p>Ajuste Ano Anterior</p>
                </div>
                <div class="icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerta de Remarcação Pendente -->
    <?php 
    $remarcacaoPendente = false;
    if (!empty($pedidos)) {
        foreach ($pedidos as $p) {
            if ($p['estado'] === 'remarcacao_solicitada') {
                $remarcacaoPendente = $p;
                break;
            }
        }
    }
    ?>
    <?php if ($remarcacaoPendente): ?>
    <div class="row">
        <div class="col-12">
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h5><i class="fas fa-hourglass-half"></i> Remarcação Pendente</h5>
                <p class="mb-2">
                    O seu pedido de remarcação (#<?= $remarcacaoPendente['id'] ?>) está a aguardar aprovação da secretaria.
                    Será notificado assim que houver uma decisão.
                </p>
                <hr>
                <p class="mb-0">
                    <strong>Solicitado em:</strong> <?= date('d/m/Y H:i', strtotime($remarcacaoPendente['criado_em'])) ?><br>
                    <strong>Dias:</strong> <?= $remarcacaoPendente['total_dias'] ?> dias de férias
                </p>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Explicação do Cálculo de Atribuição -->
    <div class="row">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-calculator"></i> Cálculo Detalhado da Atribuição de Férias</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <td><strong>Nº de dias de licença para férias a que teve direito no ano anterior:</strong></td>
                                <td class="text-end">
                                    <?php if ($dias_atribuidos_anterior > 0): ?>
                                        <span class="badge bg-info fs-6"><?= $dias_atribuidos_anterior ?> dias</span>
                                        <?php if ($ano_anterior): ?>
                                            <br><small class="text-muted">(Ano <?= $ano_anterior['anoletivo'] ?>/<?= $ano_anterior['anoletivo'] + 1 ?>)</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sem informação</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Nº de dias de licença para férias gozadas no ano anterior:</strong></td>
                                <td class="text-end">
                                    <?php if ($dias_gozados_anterior > 0): ?>
                                        <span class="badge bg-warning fs-6"><?= $dias_gozados_anterior ?> dias</span>
                                        <?php if ($ano_anterior): ?>
                                            <br><small class="text-muted">(Ano <?= $ano_anterior['anoletivo'] ?>/<?= $ano_anterior['anoletivo'] + 1 ?>)</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">0 dias</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if (!empty($faltas_desconto)): ?>
                                <?php 
                                $faltasJustificadas = [];
                                $faltasInjustificadas = [];
                                foreach ($faltas_desconto as $falta) {
                                    if (\App\Models\FeriasFaltasDescontoModel::getCategoriaFalta($falta['tipo_falta']) === 'justificada') {
                                        $faltasJustificadas[] = $falta;
                                    } else {
                                        $faltasInjustificadas[] = $falta;
                                    }
                                }
                                ?>
                                <tr>
                                    <td><strong>Faltas Justificadas por participação:</strong></td>
                                    <td>
                                        <?php if (!empty($faltasJustificadas)): ?>
                                            <table class="table table-sm table-borderless mb-0">
                                                <?php foreach ($faltasJustificadas as $falta): ?>
                                                    <tr>
                                                        <td>
                                                            <?= \App\Models\FeriasFaltasDescontoModel::getDescricaoTipoFalta($falta['tipo_falta']) ?>
                                                            <br><small class="text-muted">
                                                                Data da falta: <?= date('d/m/Y', strtotime($falta['data_falta'])) ?>
                                                            </small>
                                                            <?php if (!empty($falta['motivo'])): ?>
                                                                <br><small class="text-muted">Motivo: <?= esc($falta['motivo']) ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <span class="badge bg-warning"><?= number_format($falta['dias_desconto'], 1) ?> dias</span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </table>
                                        <?php else: ?>
                                            <span class="badge bg-success">Sem faltas justificadas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Faltas Injustificadas:</strong></td>
                                    <td>
                                        <?php if (!empty($faltasInjustificadas)): ?>
                                            <table class="table table-sm table-borderless mb-0">
                                                <?php foreach ($faltasInjustificadas as $falta): ?>
                                                    <tr>
                                                        <td>
                                                            <?= \App\Models\FeriasFaltasDescontoModel::getDescricaoTipoFalta($falta['tipo_falta']) ?>
                                                            <br><small class="text-muted">
                                                                Data da falta: <?= date('d/m/Y', strtotime($falta['data_falta'])) ?>
                                                            </small>
                                                            <?php if (!empty($falta['motivo'])): ?>
                                                                <br><small class="text-muted">Motivo: <?= esc($falta['motivo']) ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="text-end">
                                                            <span class="badge bg-danger"><?= number_format($falta['dias_desconto'], 1) ?> dias</span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </table>
                                        <?php else: ?>
                                            <span class="badge bg-success">Sem faltas injustificadas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td><strong>Faltas Justificadas por participação:</strong></td>
                                    <td class="text-end"><span class="badge bg-success">Sem faltas justificadas</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Faltas Injustificadas:</strong></td>
                                    <td class="text-end"><span class="badge bg-success">Sem faltas injustificadas</span></td>
                                </tr>
                            <?php endif; ?>
                            <tr class="table-warning">
                                <td><strong>Total de faltas a descontar:</strong></td>
                                <td class="text-end">
                                    <?php if (($saldo['dias_desconto_faltas'] ?? 0) > 0): ?>
                                        <span class="badge bg-danger fs-6"><?= number_format($saldo['dias_desconto_faltas'], 1) ?> dias</span>
                                    <?php else: ?>
                                        <span class="badge bg-success fs-6">0 dias</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>Dias Base (ano atual):</strong></td>
                                <td class="text-end">
                                    <span class="badge bg-primary fs-6"><?= $saldo['dias_base'] ?? 0 ?> dias</span>
                                </td>
                            </tr>
                            <tr class="table-primary">
                                <td><strong>Ajuste do ano anterior:</strong></td>
                                <td class="text-end">
                                    <?php 
                                    $ajuste = $saldo['dias_ajuste'] ?? 0;
                                    $ajusteCalc = $dias_atribuidos_anterior - $dias_gozados_anterior;
                                    ?>
                                    <span class="badge bg-<?= $ajuste >= 0 ? 'success' : 'danger' ?> fs-6">
                                        <?= $ajuste >= 0 ? '+' : '' ?><?= $ajuste ?> dias
                                    </span>
                                    <?php if ($dias_atribuidos_anterior > 0 || $dias_gozados_anterior > 0): ?>
                                        <br><small class="text-muted">(<?= $dias_atribuidos_anterior ?> atribuídos - <?= $dias_gozados_anterior ?> gozados = <?= $ajusteCalc ?>)</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php if (($saldo['dias_extra'] ?? 0) != 0): ?>
                            <tr class="table-primary">
                                <td><strong>Acertos/Extras:</strong></td>
                                <td class="text-end">
                                    <span class="badge bg-<?= ($saldo['dias_extra'] ?? 0) >= 0 ? 'info' : 'warning' ?> fs-6">
                                        <?= ($saldo['dias_extra'] ?? 0) >= 0 ? '+' : '' ?><?= $saldo['dias_extra'] ?? 0 ?> dias
                                    </span>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr class="table-success" style="font-size: 1.1em;">
                                <td><strong>Nº de dias de licença para férias a conceder (Total Disponível):</strong></td>
                                <td class="text-end">
                                    <span class="badge bg-success fs-5"><?= $saldo['dias_disponiveis'] ?? 0 ?> dias</span>
                                    <?php if (($saldo['dias_total'] ?? 0) > 0): ?>
                                        <br><small class="text-muted">
                                            (<?= $saldo['dias_base'] ?? 0 ?> 
                                            <?= ($saldo['dias_ajuste'] ?? 0) >= 0 ? '+' : '' ?><?= $saldo['dias_ajuste'] ?? 0 ?> 
                                            <?= ($saldo['dias_extra'] ?? 0) >= 0 ? '+' : '' ?><?= $saldo['dias_extra'] ?? 0 ?>
                                            <?= ($saldo['dias_gastos'] ?? 0) > 0 ? ' - ' . ($saldo['dias_gastos'] ?? 0) . ' marcados' : '' ?>
                                            <?= ($saldo['dias_desconto_faltas'] ?? 0) > 0 ? ' - ' . number_format($saldo['dias_desconto_faltas'], 1) . ' faltas' : '' ?>
                                            )
                                        </small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalhes da Atribuição -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title"><i class="fas fa-info-circle"></i> Detalhes da Atribuição</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-6">Ano Letivo:</dt>
                        <dd class="col-sm-6"><?= $ano_letivo['anoletivo'] ?>/<?= $ano_letivo['anoletivo'] + 1 ?></dd>

                        <dt class="col-sm-6">Dias Base:</dt>
                        <dd class="col-sm-6"><?= $saldo['dias_base'] ?? 0 ?> dias</dd>

                        <dt class="col-sm-6">Ajuste Ano Anterior:</dt>
                        <dd class="col-sm-6">
                            <?php if (($saldo['dias_ajuste'] ?? 0) > 0): ?>
                                <span class="badge bg-success">+<?= $saldo['dias_ajuste'] ?> dias</span>
                            <?php elseif (($saldo['dias_ajuste'] ?? 0) < 0): ?>
                                <span class="badge bg-danger"><?= $saldo['dias_ajuste'] ?> dias</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">0 dias</span>
                            <?php endif; ?>
                        </dd>

                        <dt class="col-sm-6">Acertos:</dt>
                        <dd class="col-sm-6"><?= $saldo['dias_extra'] ?? 0 ?> dias</dd>

                        <dt class="col-sm-6"><strong>Total:</strong></dt>
                        <dd class="col-sm-6"><strong><?= $saldo['dias_total'] ?? 0 ?> dias</strong></dd>
                    </dl>

                    <?php if (!empty($atribuicao['observacoes'])): ?>
                        <div class="alert alert-info mt-3">
                            <strong>Observações:</strong><br>
                            <?= nl2br(esc($atribuicao['observacoes'])) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (($saldo['dias_disponiveis'] ?? 0) > 0): ?>
                        <a href="<?= base_url('ferias/marcar') ?>" class="btn btn-primary btn-block mt-3">
                            <i class="fas fa-plus"></i> Marcar Período de Férias
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Meus Pedidos -->
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-list"></i> Meus Pedidos</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($pedidos)): ?>
                        <p class="text-muted">Ainda não fez nenhum pedido de férias.</p>
                    <?php else: ?>
                        <div class="timeline timeline-inverse">
                            <?php foreach ($pedidos as $pedido): ?>
                                <div>
                                    <i class="fas fa-calendar bg-<?= 
                                        $pedido['estado'] == 'concluido' ? 'success' :
                                        ($pedido['estado'] == 'rejeitado' ? 'danger' :
                                        ($pedido['estado'] == 'remarcacao_solicitada' ? 'warning' :
                                        ($pedido['estado'] == 'aprovado' || $pedido['estado'] == 'aguarda_assinatura' ? 'success' : 'info')))
                                    ?>"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="far fa-clock"></i> <?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></span>
                                        <h3 class="timeline-header">
                                            <?= formatar_estado_ferias($pedido['estado']) ?>
                                        </h3>
                                        <div class="timeline-body">
                                            <strong><?= $pedido['total_dias'] ?> dias</strong> de férias solicitados
                                            
                                            <?php if ($pedido['estado'] == 'remarcacao_solicitada'): ?>
                                                <div class="alert alert-warning mt-2 mb-0">
                                                    <i class="fas fa-hourglass-half"></i> <strong>Remarcação pendente:</strong>
                                                    A secretaria está a avaliar o seu pedido de remarcação.
                                                    Será notificado quando houver uma decisão.
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($pedido['estado'] == 'rejeitado' && !empty($pedido['observacoes_secretaria'])): ?>
                                                <div class="alert alert-danger mt-2 mb-0">
                                                    <strong>Motivo da rejeição:</strong><br>
                                                    <?= nl2br(esc($pedido['observacoes_secretaria'])) ?>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($pedido['estado'] == 'aguarda_assinatura'): ?>
                                                <div class="alert alert-warning mt-2 mb-0">
                                                    <strong>Ação necessária:</strong> Faça o download do documento, assine e faça o upload.
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="timeline-footer">
                                            <?php if (!empty($pedido['documento_pdf']) && $pedido['estado'] !== 'cancelado'): ?>
                                                <a href="<?= base_url('ferias/download/' . $pedido['id']) ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-download"></i> Download PDF
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($pedido['estado'] == 'aguarda_assinatura'): ?>
                                                <button class="btn btn-success btn-sm" onclick="uploadDocumento(<?= $pedido['id'] ?>)">
                                                    <i class="fas fa-upload"></i> Upload Assinado
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div>
                                <i class="far fa-clock bg-gray"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal de Upload de Documento -->
<div class="modal fade" id="modalUploadDocumento" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload de Documento Assinado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formUploadDocumento" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="uploadPedidoId" name="pedido_id">
                    <input type="hidden" name="<?= csrf_token() ?>" id="csrfUpload" value="">
                    <div class="mb-3">
                        <label for="documentoAssinado" class="form-label">Selecione o documento assinado (PDF)</label>
                        <input type="file" class="form-control" id="documentoAssinado" name="documento_assinado" accept=".pdf" required>
                        <div class="form-text">Apenas ficheiros PDF são aceites</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload"></i> Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function uploadDocumento(pedidoId) {
    $('#uploadPedidoId').val(pedidoId);
    $('#modalUploadDocumento').modal('show');
}

$('#formUploadDocumento').on('submit', function(e) {
    e.preventDefault();
    
    const pedidoId = $('#uploadPedidoId').val();
    // Atualizar token CSRF com valor atual do cookie
    const csrfToken = document.cookie.split('; ').find(r => r.startsWith('csrf_cookie_name='))?.split('=')[1] ?? '';
    $('#csrfUpload').val(csrfToken);
    const formData = new FormData(this);
    
    $.ajax({
        url: '<?= base_url('ferias/upload-documento') ?>/' + pedidoId,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            $('#modalUploadDocumento').modal('hide');
            showToast('success', response.message || 'Documento enviado com sucesso');
            setTimeout(() => location.reload(), 1500);
        },
        error: function(xhr) {
            const response = xhr.responseJSON || {};
            showToast('error', response.message || 'Erro ao enviar documento');
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
</script>
<?= $this->endSection() ?>
