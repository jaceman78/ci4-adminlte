<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><?= esc($title) ?></h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Convocatórias</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        <!-- Card de Permutas -->
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title"><i class="fas fa-exchange-alt"></i> Pedidos de Permutas de Vigilâncias</h3>
            </div>
            <div class="card-body">
                <?php if (empty($permutas)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Não existem pedidos de permutas registados.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($permutas as $permuta): 
                            // Definir cor do badge baseado no estado
                            $estadoBadge = [
                                'PENDENTE' => 'badge-warning',
                                'ACEITE_SUBSTITUTO' => 'badge-info',
                                'VALIDADO_SECRETARIADO' => 'badge-success',
                                'REJEITADO_SECRETARIADO' => 'badge-danger',
                                'RECUSADO_SUBSTITUTO' => 'badge-danger',
                                'CANCELADO' => 'badge-secondary'
                            ];
                            $badgeClass = $estadoBadge[$permuta['estado']] ?? 'badge-secondary';
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card mb-3 shadow-sm">
                                <div class="card-header bg-light border-bottom">
                                    <h5 class="card-title mb-0 text-dark">
                                        <i class="fas fa-calendar-alt"></i> 
                                        <?= date('d/m/Y', strtotime($permuta['data_exame'])) ?> às <?= substr($permuta['hora_exame'], 0, 5) ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="text-primary">
                                        <i class="fas fa-file-alt"></i> <?= esc($permuta['codigo_prova']) ?> - <?= esc($permuta['nome_prova']) ?>
                                    </h6>
                                    <p class="mb-2">
                                        <strong>Fase:</strong> <span class="badge badge-light border text-dark"><?= esc($permuta['fase']) ?></span><br>
                                        <strong>Sala:</strong> <?= $permuta['codigo_sala'] ? esc($permuta['codigo_sala']) : '<em>Não atribuída</em>' ?><br>
                                        <strong>Função:</strong> <?= esc($permuta['funcao']) ?>
                                    </p>
                                    
                                    <hr>
                                    
                                    <div class="small">
                                        <p class="mb-1"><strong>Professor Convocado:</strong><br>
                                            <i class="fas fa-user"></i> <?= esc($permuta['nome_original']) ?>
                                        </p>
                                        <p class="mb-1"><strong>Professor Substituto:</strong><br>
                                            <i class="fas fa-user"></i> <?= esc($permuta['nome_substituto']) ?>
                                        </p>
                                    </div>
                                    
                                    <hr>
                                    
                                    <!-- Ciclo de Aprovações -->
                                    <div class="timeline-sm">
                                        <div class="timeline-item <?= in_array($permuta['estado'], ['PENDENTE', 'ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO']) ? 'active' : 'inactive' ?>">
                                            <i class="fas fa-paper-plane"></i>
                                            <span class="timeline-text">Pedido criado</span>
                                            <small class="text-muted d-block"><?= date('d/m/Y H:i', strtotime($permuta['criado_em'])) ?></small>
                                        </div>
                                        
                                        <div class="timeline-item <?= in_array($permuta['estado'], ['ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO']) ? 'active' : (in_array($permuta['estado'], ['RECUSADO_SUBSTITUTO', 'REJEITADO_SECRETARIADO']) ? 'inactive text-danger' : 'pending') ?>">
                                            <i class="fas fa-user-check"></i>
                                            <span class="timeline-text">
                                                <?php if ($permuta['substituto_aceitou'] === 1): ?>
                                                    Aceite pelo substituto
                                                <?php elseif ($permuta['substituto_aceitou'] === 0): ?>
                                                    <span class="text-danger">Recusado pelo substituto</span>
                                                <?php else: ?>
                                                    Aguarda resposta do substituto
                                                <?php endif; ?>
                                            </span>
                                            <?php if ($permuta['data_resposta_substituto']): ?>
                                                <small class="text-muted d-block"><?= date('d/m/Y H:i', strtotime($permuta['data_resposta_substituto'])) ?></small>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="timeline-item <?= $permuta['estado'] == 'VALIDADO_SECRETARIADO' ? 'active' : ($permuta['validado_secretariado'] === 0 ? 'inactive text-danger' : 'pending') ?>">
                                            <i class="fas fa-check-circle"></i>
                                            <span class="timeline-text">
                                                <?php if ($permuta['estado'] == 'VALIDADO_SECRETARIADO' || $permuta['validado_secretariado'] == 1): ?>
                                                    Validado pelo secretariado
                                                    <?php if (!empty($permuta['nome_validador'])): ?>
                                                        <small class="d-block">por <?= esc($permuta['nome_validador']) ?></small>
                                                    <?php endif; ?>
                                                <?php elseif ($permuta['validado_secretariado'] === 0 || $permuta['estado'] == 'REJEITADO_SECRETARIADO'): ?>
                                                    <span class="text-danger">Rejeitado pelo secretariado</span>
                                                <?php else: ?>
                                                    Aguarda validação do secretariado
                                                <?php endif; ?>
                                            </span>
                                            <?php if (!empty($permuta['data_validacao_secretariado'])): ?>
                                                <small class="text-muted d-block"><?= date('d/m/Y H:i', strtotime($permuta['data_validacao_secretariado'])) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($permuta['motivo']): ?>
                                        <div class="alert alert-light mt-3 mb-0">
                                            <strong>Motivo:</strong><br>
                                            <small><?= nl2br(esc($permuta['motivo'])) ?></small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($permuta['observacoes_validacao']): ?>
                                        <div class="alert alert-warning mt-2 mb-0">
                                            <strong>Observações da validação:</strong><br>
                                            <small><?= nl2br(esc($permuta['observacoes_validacao'])) ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <?php
                                    $estadoTexto = [
                                        'PENDENTE' => 'Pendente',
                                        'ACEITE_SUBSTITUTO' => 'Aceite pelo Substituto',
                                        'VALIDADO_SECRETARIADO' => 'Aprovado',
                                        'REJEITADO_SECRETARIADO' => 'Rejeitado pelo Secretariado',
                                        'RECUSADO_SUBSTITUTO' => 'Recusado pelo Substituto',
                                        'CANCELADO' => 'Cancelado'
                                    ];
                                    
                                    // Garantir que sempre há texto
                                    $estadoAtual = $permuta['estado'] ?? 'DESCONHECIDO';
                                    $textoEstado = $estadoTexto[$estadoAtual] ?? ucfirst(strtolower(str_replace('_', ' ', $estadoAtual)));
                                    
                                    // Fallback final se ainda estiver vazio
                                    if (empty($textoEstado)) {
                                        $textoEstado = 'Estado Desconhecido';
                                    }
                                    ?>
                                    
                                    <span class="badge <?= $badgeClass ?> badge-lg" style="<?= $estadoAtual == 'PENDENTE' ? 'color: #856404;' : '' ?>">
                                        <?= $textoEstado ?>
                                    </span>
                                    
                                    <?php 
                                    $userLevel = session()->get('level') ?? (session()->get('LoggedUserData')['level'] ?? 0);
                                    $userId = session()->get('id') ?? (session()->get('LoggedUserData')['id'] ?? null);
                                    $podeAnular = ($permuta['user_original_id'] == $userId) || ($userLevel >= 4 && $userLevel <= 9);
                                    $podeValidar = ($userLevel == 4 || $userLevel == 8 || $userLevel == 9) && $permuta['estado'] == 'ACEITE_SUBSTITUTO';
                                    ?>
                                    
                                    <div class="btn-group">
                                        <?php if ($podeValidar): ?>
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="validarPermuta(<?= $permuta['id'] ?>, '<?= esc($permuta['codigo_prova']) ?>', <?= $permuta['convocatoria_id'] ?>)">
                                                <i class="fas fa-check-circle"></i> Validar Permuta
                                            </button>
                                        <?php endif; ?>
                                        
                                        <?php if ($podeAnular && in_array($permuta['estado'], ['PENDENTE', 'RECUSADO_SUBSTITUTO', 'REJEITADO_SECRETARIADO', 'ACEITE_SUBSTITUTO'])): ?>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="anularPermuta(<?= $permuta['id'] ?>, '<?= esc($permuta['codigo_prova']) ?>')">
                                                <i class="fas fa-times-circle"></i> Anular Pedido
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('styles') ?>
<style>
.timeline-sm {
    position: relative;
    padding-left: 30px;
    margin-top: 15px;
}

.timeline-item {
    position: relative;
    padding-bottom: 15px;
    padding-left: 15px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -22px;
    top: 8px;
    width: 2px;
    height: calc(100% + 5px);
    background: #dee2e6;
}

.timeline-item:last-child:before {
    display: none;
}

.timeline-item i {
    position: absolute;
    left: -30px;
    top: 0;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #dee2e6;
    text-align: center;
    font-size: 8px;
    line-height: 14px;
    color: #6c757d;
}

.timeline-item.active i {
    background: #28a745;
    border-color: #28a745;
    color: #fff;
}

.timeline-item.active:before {
    background: #28a745;
}

.timeline-item.pending i {
    background: #ffc107;
    border-color: #ffc107;
    color: #fff;
}

.timeline-item.inactive i {
    background: #6c757d;
    border-color: #6c757d;
    color: #fff;
}

.timeline-text {
    font-weight: 500;
    font-size: 0.9rem;
}

.badge-lg {
    font-size: 0.95rem;
    padding: 0.5rem 0.75rem;
}

/* Melhorar contraste dos badges */
.badge-warning {
    background-color: #ffc107;
    color: #856404 !important;
}

.badge-info {
    background-color: #17a2b8;
    color: #fff !important;
}

.badge-success {
    background-color: #28a745;
    color: #fff !important;
}

.badge-danger {
    background-color: #dc3545;
    color: #fff !important;
}

.badge-secondary {
    background-color: #6c757d;
    color: #fff !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function validarPermuta(permutaId, codigoProva, convocatoriaId) {
    Swal.fire({
        title: 'Validar Permuta de Vigilância?',
        html: `
            <p>Ao validar esta permuta para <strong>${codigoProva}</strong>, o professor substituto ficará oficialmente convocado.</p>
            <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> A convocatória será atualizada automaticamente.</p>
        `,
        icon: 'question',
        input: 'textarea',
        inputLabel: 'Observações (opcional)',
        inputPlaceholder: 'Digite observações sobre a validação...',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-check"></i> Sim, validar permuta',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            // Validação opcional - pode deixar vazio
            return null;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const observacoes = result.value || '';
            
            $.ajax({
                url: '<?= base_url('permutas-vigilancia/validar') ?>/' + permutaId,
                method: 'POST',
                data: {
                    observacoes: observacoes,
                    convocatoria_id: convocatoriaId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Permuta Validada!',
                            html: response.message || 'Permuta validada e convocatória atualizada com sucesso',
                            confirmButtonText: 'OK'
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message || 'Erro ao validar permuta'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Erro ao processar pedido';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: errorMsg
                    });
                }
            });
        }
    });
}

function anularPermuta(permutaId, codigoProva) {
    Swal.fire({
        title: 'Anular Pedido de Permuta?',
        html: `Tem a certeza que pretende anular o pedido de permuta para <strong>${codigoProva}</strong>?<br><br>Esta ação não pode ser revertida.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, anular!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('permutas-vigilancia/cancelar') ?>/' + permutaId,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Anulado!',
                            text: response.message || 'Pedido de permuta anulado com sucesso',
                            confirmButtonText: 'OK'
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message || 'Erro ao anular pedido de permuta'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Erro ao processar pedido';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: errorMsg
                    });
                }
            });
        }
    });
}

// Inicializar DataTable para estatísticas
$(document).ready(function() {
    // Código apenas para permutas
});
</script>
<?= $this->endSection() ?>
