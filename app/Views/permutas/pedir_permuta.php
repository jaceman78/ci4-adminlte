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
                        <li class="breadcrumb-item"><a href="<?= base_url('permutas') ?>">Meu Horário</a></li>
                        <li class="breadcrumb-item active">Pedir Permuta</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <!-- Informação da Aula Atual -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="bi bi-book"></i> Aula a Permutar</h3>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-4">Disciplina:</dt>
                                <dd class="col-sm-8"><?= esc($aula['disciplina_nome'] ?? 'N/A') ?></dd>

                                <dt class="col-sm-4">Turma:</dt>
                                <dd class="col-sm-8">
                                    <?= esc($aula['codigo_turma']) ?> - <?= esc($aula['turma_nome']) ?> (<?= esc($aula['turma_ano']) ?>º ano)
                                </dd>

                                <dt class="col-sm-4">Sala:</dt>
                                <dd class="col-sm-8"><?= esc($aula['sala_id'] ?? 'N/A') ?></dd>

                                <dt class="col-sm-4">Dia:</dt>
                                <dd class="col-sm-8">
                                    <?php
                                    $dias = [2 => 'Segunda-feira', 3 => 'Terça-feira', 4 => 'Quarta-feira', 
                                             5 => 'Quinta-feira', 6 => 'Sexta-feira', 7 => 'Sábado'];
                                    echo $dias[$aula['dia_semana']] ?? 'N/A';
                                    ?>
                                </dd>

                                <dt class="col-sm-4">Horário:</dt>
                                <dd class="col-sm-8">
                                    <?= substr($aula['hora_inicio'], 0, 5) ?> - <?= substr($aula['hora_fim'], 0, 5) ?>
                                </dd>

                                <?php if ($aula['turno']): ?>
                                <dt class="col-sm-4">Turno:</dt>
                                <dd class="col-sm-8">
                                    <span class="badge <?= $aula['turno'] == 'T1' ? 'bg-info' : 'bg-warning' ?>">
                                        <?= esc($aula['turno']) ?>
                                    </span>
                                </dd>
                                <?php endif; ?>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <!-- Formulário para solicitar permuta -->
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="bi bi-arrow-left-right"></i> Solicitar Permuta</h3>
                        </div>
                        <form id="formPermuta">
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> 
                                    <strong>Em desenvolvimento:</strong> Funcionalidade de permuta será implementada em breve.
                                </div>

                                <div class="form-group">
                                    <label>Professor com quem deseja permutar:</label>
                                    <select class="form-control" disabled>
                                        <option>-- Selecione um professor --</option>
                                    </select>
                                    <small class="form-text text-muted">
                                        Será possível selecionar professores que lecionam a mesma disciplina.
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label>Motivo da permuta:</label>
                                    <textarea class="form-control" rows="3" disabled 
                                              placeholder="Descreva o motivo da solicitação..."></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="<?= base_url('permutas') ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Voltar ao Horário
                                </a>
                                <button type="submit" class="btn btn-success" disabled>
                                    <i class="bi bi-send"></i> Enviar Solicitação
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= $this->endSection() ?>
