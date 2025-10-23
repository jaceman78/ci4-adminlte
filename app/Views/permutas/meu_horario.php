<?= $this->extend('layout/master') ?>
<?= $this->section('pageHeader') ?>
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $page_title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item active">Meu Horário</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info do Professor -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h5><i class="bi bi-person-badge"></i> <?= esc($userName) ?></h5>
                        <p class="mb-0">NIF: <?= $userNif ? esc($userNif) : '<em>Não associado</em>' ?></p>
                    </div>
                </div>
            </div>

            <?php if ($semNif ?? false): ?>
                <!-- Mensagem quando não tem NIF -->
                <div class="alert alert-warning">
                    <h5><i class="bi bi-exclamation-triangle"></i> Sem Horário Disponível</h5>
                    <p>O seu utilizador não tem um NIF associado. Para visualizar o seu horário, é necessário que um administrador associe o seu NIF ao seu perfil.</p>
                    <p class="mb-0">Entre em contacto com a administração para resolver esta situação.</p>
                </div>
            <?php else: ?>
            
            <!-- Horário Semanal -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-calendar-week"></i> <?= $page_subtitle ?></h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" id="horarioTable">
                            <thead class="table-primary">
                                <tr>
                                    <th style="width: 120px;" class="text-center">Hora</th>
                                    <?php foreach ($diasSemana as $diaNome => $diaNum): ?>
                                        <th class="text-center">
                                            <?php 
                                            $diasPT = [
                                                'Segunda_Feira' => 'Segunda',
                                                'Terca_Feira' => 'Terça',
                                                'Quarta_Feira' => 'Quarta',
                                                'Quinta_Feira' => 'Quinta',
                                                'Sexta_Feira' => 'Sexta',
                                                'Sabado' => 'Sábado'
                                            ];
                                            echo $diasPT[$diaNome] ?? $diaNome;
                                            ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Obter todos os blocos únicos (usar o primeiro dia como referência)
                                $primeiroBloco = reset($blocosPorDia);
                                if ($primeiroBloco):
                                    foreach ($primeiroBloco as $bloco):
                                        $horaInicioBloco = substr($bloco['hora_inicio'], 0, 5);
                                        $horaFimBloco = substr($bloco['hora_fim'], 0, 5);
                                ?>
                                <tr>
                                    <td class="text-center align-middle bg-light">
                                        <strong><?= $horaInicioBloco ?></strong><br>
                                        <small><?= $horaFimBloco ?></small><br>
                                        <small class="text-muted"><?= esc($bloco['designacao']) ?></small>
                                    </td>
                                    <?php foreach ($diasSemana as $diaNome => $diaNum): ?>
                                        <td class="text-center align-middle" style="min-width: 150px;">
                                            <?php 
                                            // Verificar se há aula do professor neste horário/dia
                                            $aulaEncontrada = null;
                                            if (isset($horarioGrid[$diaNome])) {
                                                foreach ($horarioGrid[$diaNome] as $aula) {
                                                    // Verificar se a aula está dentro do bloco
                                                    if ($aula['hora_inicio'] <= $horaInicioBloco && $aula['hora_fim'] >= $horaFimBloco) {
                                                        $aulaEncontrada = $aula;
                                                        break;
                                                    }
                                                    // Ou se o bloco está dentro da aula
                                                    if ($horaInicioBloco >= $aula['hora_inicio'] && $horaFimBloco <= $aula['hora_fim']) {
                                                        $aulaEncontrada = $aula;
                                                        break;
                                                    }
                                                    // Ou se há sobreposição
                                                    if (($horaInicioBloco >= $aula['hora_inicio'] && $horaInicioBloco < $aula['hora_fim']) ||
                                                        ($horaFimBloco > $aula['hora_inicio'] && $horaFimBloco <= $aula['hora_fim'])) {
                                                        $aulaEncontrada = $aula;
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            if ($aulaEncontrada):
                                                $turnoClass = '';
                                                if ($aulaEncontrada['turno'] == 'T1') {
                                                    $turnoClass = 'bg-info bg-opacity-25';
                                                } elseif ($aulaEncontrada['turno'] == 'T2') {
                                                    $turnoClass = 'bg-warning bg-opacity-25';
                                                }
                                            ?>
                                                <div class="card mb-0 <?= $turnoClass ?>" style="cursor: pointer;" 
                                                     onclick="mostrarDetalhes(<?= $aulaEncontrada['id_aula'] ?>, '<?= esc($aulaEncontrada['disciplina']) ?>', '<?= esc($aulaEncontrada['turma']) ?>', '<?= esc($aulaEncontrada['sala']) ?>', '<?= $aulaEncontrada['hora_inicio'] ?>', '<?= $aulaEncontrada['hora_fim'] ?>', '<?= $aulaEncontrada['turno'] ?>')">
                                                    <div class="card-body p-2">
                                                        <strong class="d-block"><?= esc($aulaEncontrada['disciplina']) ?></strong>
                                                        <small class="d-block text-muted"><?= esc($aulaEncontrada['turma']) ?></small>
                                                        <small class="d-block">
                                                            <i class="bi bi-geo-alt"></i> <?= esc($aulaEncontrada['sala']) ?>
                                                        </small>
                                                        <?php if ($aulaEncontrada['turno']): ?>
                                                            <span class="badge badge-sm bg-secondary"><?= esc($aulaEncontrada['turno']) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php 
                                    endforeach;
                                endif;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <small>
                                <span class="badge bg-info">T1</span> Turno 1 &nbsp;
                                <span class="badge bg-warning">T2</span> Turno 2
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">Clique numa aula para ver detalhes ou pedir permuta</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php endif; ?>

<!-- Modal Detalhes da Aula -->
<div class="modal fade" id="modalDetalhesAula" tabindex="-1" aria-labelledby="modalDetalhesAulaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalhesAulaLabel">Detalhes da Aula</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Disciplina:</dt>
                    <dd class="col-sm-8" id="detalhe_disciplina"></dd>

                    <dt class="col-sm-4">Turma:</dt>
                    <dd class="col-sm-8" id="detalhe_turma"></dd>

                    <dt class="col-sm-4">Sala:</dt>
                    <dd class="col-sm-8" id="detalhe_sala"></dd>

                    <dt class="col-sm-4">Horário:</dt>
                    <dd class="col-sm-8" id="detalhe_horario"></dd>

                    <dt class="col-sm-4">Turno:</dt>
                    <dd class="col-sm-8" id="detalhe_turno"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="#" id="btnPedirPermuta" class="btn btn-primary">
                    <i class="bi bi-arrow-left-right"></i> Pedir Permuta
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
#horarioTable tbody td {
    vertical-align: middle;
    height: 80px;
}

.card[onclick] {
    transition: all 0.2s ease;
}

.card[onclick]:hover {
    transform: translateY(-2px);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function mostrarDetalhes(idAula, disciplina, turma, sala, horaInicio, horaFim, turno) {
    $('#detalhe_disciplina').text(disciplina);
    $('#detalhe_turma').text(turma);
    $('#detalhe_sala').text(sala);
    $('#detalhe_horario').text(horaInicio + ' - ' + horaFim);
    $('#detalhe_turno').text(turno ? turno : 'Sem turno');
    
    // Atualizar link do botão de pedir permuta
    $('#btnPedirPermuta').attr('href', '<?= base_url('permutas/pedir') ?>/' + idAula);
    
    // Mostrar modal
    var modal = new bootstrap.Modal(document.getElementById('modalDetalhesAula'));
    modal.show();
}

// Destacar aulas ao passar o mouse
$(document).ready(function() {
    $('.card[onclick]').hover(
        function() {
            $(this).addClass('shadow-sm border-primary');
        },
        function() {
            $(this).removeClass('shadow-sm border-primary');
        }
    );
});
</script>
<?= $this->endSection() ?>
