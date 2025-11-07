<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($page_title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/permutas/minhas') ?>">As Minhas Permutas</a></li>
                    <li class="breadcrumb-item active">Detalhes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                
                <!-- Badge de Estado -->
                <div class="text-center mb-3">
                    <?php
                    $badges = [
                        'pendente' => ['class' => 'warning', 'icon' => 'clock', 'style' => 'color: #000;'],
                        'aprovada' => ['class' => 'success', 'icon' => 'check-circle', 'style' => 'color: #fff; background-color: #28a745;'],
                        'rejeitada' => ['class' => 'danger', 'icon' => 'times-circle', 'style' => 'color: #fff;'],
                        'cancelada' => ['class' => 'secondary', 'icon' => 'ban', 'style' => 'color: #fff;']
                    ];
                    $estado = $badges[$permuta['estado']] ?? ['class' => 'secondary', 'icon' => 'question', 'style' => 'color: #fff;'];
                    ?>
                    <h2>
                        <span class="badge badge-<?= $estado['class'] ?> badge-lg" style="<?= $estado['style'] ?>">
                            <i class="fas fa-<?= $estado['icon'] ?>"></i> 
                            Estado: <?= ucfirst($permuta['estado']) ?>
                        </span>
                    </h2>
                </div>

                <!-- Informações da Aula Original -->
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title text-white"><i class="fas fa-book"></i> Aula Original</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Disciplina:</strong><br>
                                    <?php if (!empty($permuta['disciplina_abrev']) && !empty($permuta['disciplina_nome'])): ?>
                                        <?= esc($permuta['disciplina_abrev']) ?> - <?= esc($permuta['disciplina_nome']) ?>
                                    <?php else: ?>
                                        <?= esc($permuta['disciplina_id'] ?? 'N/A') ?>
                                    <?php endif; ?>
                                </p>
                                <p><strong>Turma:</strong><br>
                                    <?php if (!empty($permuta['turma_nome'])): ?>
                                        <?= esc($permuta['codigo_turma']) ?> - <?= esc($permuta['turma_nome']) ?> 
                                        <?php if (!empty($permuta['ano'])): ?>
                                            (<?= esc($permuta['ano']) ?>º ano)
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?= esc($permuta['codigo_turma'] ?? 'N/A') ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Dia da Semana:</strong><br>
                                    <?php 
                                    $dias = ['', '', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
                                    echo $dias[$permuta['dia_semana'] ?? 0];
                                    ?>
                                </p>
                                <p><strong>Horário:</strong><br><?= substr($permuta['hora_inicio'], 0, 5) ?> - <?= substr($permuta['hora_fim'], 0, 5) ?></p>
                                <p><strong>Sala Original:</strong><br><?= esc($permuta['sala_original_codigo'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detalhes da Permuta -->
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title text-white"><i class="fas fa-exchange-alt"></i> Detalhes da Permuta</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Data da Aula a Permutar:</strong><br>
                                    <?php if (!empty($permuta['data_aula_original'])): ?>
                                        <span class="badge badge-warning" style="font-size: 0.95rem; color: #000;">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            <?= date('d/m/Y', strtotime($permuta['data_aula_original'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Não definida</span>
                                    <?php endif; ?>
                                </p>
                                <p><strong>Data de Reposição:</strong><br>
                                    <?php if (!empty($permuta['data_aula_permutada'])): ?>
                                        <span class="badge badge-success" style="font-size: 0.95rem; color: #000;">
                                            <i class="far fa-calendar-check mr-1"></i>
                                            <?= date('d/m/Y', strtotime($permuta['data_aula_permutada'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-exclamation-circle"></i> Não definida</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Horário de Reposição:</strong><br>
                                    <?php if (!empty($permuta['bloco_designacao'])): ?>
                                        <span class="badge badge-primary" style="font-size: 0.95rem; color: #fff; background-color: #007bff;">
                                            <i class="far fa-clock mr-1"></i>
                                            <?= esc($permuta['bloco_designacao']) ?>: 
                                            <?= substr($permuta['bloco_hora_inicio'], 0, 5) ?> - <?= substr($permuta['bloco_hora_fim'], 0, 5) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary" style="font-size: 0.95rem; color: #fff;">
                                            <i class="far fa-clock mr-1"></i>
                                            Mesmo horário: <?= substr($permuta['hora_inicio'], 0, 5) ?> - <?= substr($permuta['hora_fim'], 0, 5) ?>
                                        </span>
                                    <?php endif; ?>
                                </p>
                                <p><strong>Sala para Reposição:</strong><br>
                                    <?php if (!empty($permuta['sala_permutada_codigo'])): ?>
                                        <span class="badge badge-info" style="font-size: 0.95rem; color: #000;">
                                            <i class="fas fa-door-open mr-1"></i>
                                            <?= esc($permuta['sala_permutada_codigo']) ?>
                                        </span>
                                    <?php elseif (!empty($permuta['sala_original_codigo'])): ?>
                                        <span class="badge badge-secondary" style="font-size: 0.95rem; color: #000;">
                                            <i class="fas fa-door-open mr-1"></i>
                                            <?= esc($permuta['sala_original_codigo']) ?> (mesma sala)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Não definida</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <?php if (!empty($permutasGrupo) && count($permutasGrupo) > 1): ?>
                            <hr>
                            <div class="alert alert-info">
                                <h5><i class="fas fa-layer-group"></i> Grupo de Permutas: <?= esc($permuta['grupo_permuta']) ?></h5>
                                <p class="mb-2">Esta permuta faz parte de um grupo com <strong><?= count($permutasGrupo) ?> aulas</strong>:</p>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered bg-white">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Disciplina</th>
                                                <th>Horário Original</th>
                                                <th>Horário Reposição</th>
                                                <th>Sala Reposição</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($permutasGrupo as $pGrupo): ?>
                                                <tr <?= $pGrupo['id'] == $permuta['id'] ? 'class="table-primary"' : '' ?>>
                                                    <td>
                                                        <strong><?= esc($pGrupo['disciplina_abrev'] ?? $pGrupo['disciplina_id']) ?></strong>
                                                        <?= $pGrupo['id'] == $permuta['id'] ? '<span class="badge badge-primary badge-sm ml-1">Esta</span>' : '' ?>
                                                    </td>
                                                    <td>
                                                        <i class="far fa-clock"></i> 
                                                        <?= substr($pGrupo['hora_inicio'], 0, 5) ?> - <?= substr($pGrupo['hora_fim'], 0, 5) ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($pGrupo['bloco_designacao'])): ?>
                                                            <i class="far fa-clock"></i> 
                                                            <?= esc($pGrupo['bloco_designacao']) ?>: 
                                                            <?= substr($pGrupo['bloco_hora_inicio'], 0, 5) ?> - <?= substr($pGrupo['bloco_hora_fim'], 0, 5) ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Mesmo horário</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($pGrupo['sala_permutada_codigo'])): ?>
                                                            <i class="fas fa-door-open"></i> <?= esc($pGrupo['sala_permutada_codigo']) ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Mesma sala</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Professor Autor:</strong><br>
                                    <i class="fas fa-user"></i> <?= esc($permuta['professor_autor_nome']) ?>
                                    <?php if ($permuta['professor_autor_nif'] == $userNif): ?>
                                        <span class="badge badge-primary" style="color: #000; background-color: #e3f2fd; border: 1px solid #2196F3;">Eu</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Professor Substituto:</strong><br>
                                    <i class="fas fa-user-check"></i> <?= esc($permuta['professor_substituto_nome']) ?>
                                    <?php if ($permuta['professor_substituto_nif'] == $userNif): ?>
                                        <span class="badge badge-primary" style="color: #000; background-color: #e3f2fd; border: 1px solid #2196F3;">Eu</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <?php if (!empty($permuta['observacoes'])): ?>
                            <hr>
                            <p><strong>Observações/Justificação:</strong></p>
                            <div class="alert alert-light">
                                <?= nl2br(esc($permuta['observacoes'])) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($permuta['estado'] == 'aprovada' || $permuta['estado'] == 'rejeitada'): ?>
                            <hr>
                            <p><strong>Aprovado/Rejeitado por:</strong> <?= esc($permuta['aprovador_nome'] ?? 'N/A') ?></p>
                            <p><strong>Data:</strong> <?= $permuta['data_aprovacao'] ? date('d/m/Y H:i', strtotime($permuta['data_aprovacao'])) : 'N/A' ?></p>
                            
                            <?php if (!empty($permuta['motivo_rejeicao'])): ?>
                                <div class="alert alert-danger">
                                    <strong><i class="fas fa-exclamation-triangle"></i> Motivo da Rejeição:</strong><br>
                                    <?= nl2br(esc($permuta['motivo_rejeicao'])) ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('/permutas/minhas') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        
                        <div>
                            <?php if ($isAdmin && $permuta['estado'] == 'pendente'): ?>
                                <button type="button" class="btn btn-success mr-2" id="btnAprovar">
                                    <i class="fas fa-check"></i> Aprovar
                                </button>
                                <button type="button" class="btn btn-danger" id="btnRejeitar">
                                    <i class="fas fa-times"></i> Rejeitar
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($permuta['estado'] == 'pendente' && $permuta['professor_autor_nif'] == $userNif): ?>
                                <button type="button" class="btn btn-danger" id="btnCancelar">
                                    <i class="fas fa-ban"></i> Cancelar Permuta
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Cancelar permuta
    $('#btnCancelar').on('click', function() {
        Swal.fire({
            title: 'Cancelar Permuta?',
            text: 'Tem a certeza que deseja cancelar esta permuta?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, cancelar',
            cancelButtonText: 'Não'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                
                $.ajax({
                    url: '<?= base_url('permutas/cancelar/' . $permuta['id']) ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cancelada!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Erro!', 'Erro ao processar pedido', 'error');
                    }
                });
            }
        });
    });

    // Aprovar permuta (admin)
    $('#btnAprovar').on('click', function() {
        Swal.fire({
            title: 'Aprovar Permuta?',
            text: 'Confirma a aprovação desta permuta?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, aprovar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                
                $.ajax({
                    url: '<?= base_url('permutas/aprovar/' . $permuta['id']) ?>',
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Aprovada!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Erro!', 'Erro ao processar pedido', 'error');
                    }
                });
            }
        });
    });

    // Rejeitar permuta (admin)
    $('#btnRejeitar').on('click', function() {
        Swal.fire({
            title: 'Rejeitar Permuta',
            input: 'textarea',
            inputLabel: 'Motivo da rejeição',
            inputPlaceholder: 'Descreva o motivo...',
            inputAttributes: {
                'aria-label': 'Motivo da rejeição'
            },
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Rejeitar',
            cancelButtonText: 'Cancelar',
            inputValidator: (value) => {
                if (!value) {
                    return 'Por favor indique o motivo da rejeição'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.showLoading();
                
                $.ajax({
                    url: '<?= base_url('permutas/rejeitar') ?>',
                    type: 'POST',
                    data: {
                        permuta_id: <?= $permuta['id'] ?>,
                        motivo: result.value
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Rejeitada!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Erro!', 'Erro ao processar pedido', 'error');
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
