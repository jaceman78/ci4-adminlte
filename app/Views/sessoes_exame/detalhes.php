<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>Detalhes da Sessão<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detalhes da Sessão de Exame - <?= esc($sessao['codigo_prova']) ?> - <?= esc($sessao['nome_prova']) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('sessoes-exame') ?>">Sessões</a></li>
                    <li class="breadcrumb-item active">Detalhes</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title"><i class="bi bi-file-earmark-text"></i> Informações do Exame</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Código:</dt>
                            <dd class="col-sm-8"><?= esc($sessao['codigo_prova']) ?></dd>

                            <dt class="col-sm-4">Nome:</dt>
                            <dd class="col-sm-8"><?= esc($sessao['nome_prova']) ?></dd>

                            <dt class="col-sm-4">Tipo:</dt>
                            <dd class="col-sm-8"><span class="badge bg-info"><?= esc($sessao['tipo_prova']) ?></span></dd>

                            <?php if (!in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC'])): ?>
                            <dt class="col-sm-4">Ano:</dt>
                            <dd class="col-sm-8"><?= esc($sessao['ano_escolaridade']) ?>º ano</dd>
                            <?php endif; ?>

                            <dt class="col-sm-4">Fase:</dt>
                            <dd class="col-sm-8"><strong><?= esc($sessao['fase']) ?></strong></dd>

                            <dt class="col-sm-4">Data:</dt>
                            <dd class="col-sm-8"><?= date('d/m/Y', strtotime($sessao['data_exame'])) ?></dd>

                            <dt class="col-sm-4">Hora:</dt>
                            <dd class="col-sm-8"><?= date('H:i', strtotime($sessao['hora_exame'])) ?></dd>

                            <dt class="col-sm-4">Duração:</dt>
                            <dd class="col-sm-8"><?= $sessao['duracao_minutos'] ?> minutos</dd>

                            <dt class="col-sm-4">Tolerância:</dt>
                            <dd class="col-sm-8"><?= $sessao['tolerancia_minutos'] ?> minutos</dd>

                            <?php if ($sessao['num_alunos']): ?>
                            <dt class="col-sm-4">Nº Alunos:</dt>
                            <dd class="col-sm-8"><?= $sessao['num_alunos'] ?> alunos</dd>
                            <?php endif; ?>
                        </dl>
                        
                        <div class="mt-3">
                            <a href="<?= base_url('sessoes-exame/alocar-salas/' . $sessao['id']) ?>" class="btn btn-warning">
                                <i class="bi bi-door-open"></i> Alocar Salas
                            </a>
                            <?php if (!empty($convocatorias)): ?>
                            <a href="<?= base_url('sessoes-exame/gerar-pdf/' . $sessao['id']) ?>" class="btn btn-danger" target="_blank">
                                <i class="bi bi-file-pdf"></i> Gerar PDF para Afixação
                            </a>
                            <?php endif; ?>
                            <a href="<?= base_url('sessoes-exame') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title"><i class="bi bi-people"></i> Vigilância</h3>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <?php if (!in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC'])): ?>
                            <dt class="col-sm-6">Vigilantes Necessários:</dt>
                            <dd class="col-sm-6"><span class="badge bg-primary"><?= $vigilantesNecessarios ?></span></dd>

                            <dt class="col-sm-6">Vigilantes Convocados:</dt>
                            <dd class="col-sm-6">
                                <span class="badge <?= $vigilantesConvocados >= $vigilantesNecessarios ? 'bg-success' : 'bg-warning' ?>">
                                    <?= $vigilantesConvocados ?>
                                </span>
                                <?php if ($vigilantesConvocados < $vigilantesNecessarios): ?>
                                    <small class="text-danger">(Faltam <?= $vigilantesNecessarios - $vigilantesConvocados ?>)</small>
                                <?php endif; ?>
                            </dd>
                            <?php endif; ?>

                            <?php 
                                // Label para professores convocados varia conforme o tipo
                                $labelsEspeciais = [
                                    'Suplentes' => 'Suplentes Convocados',
                                    'Verificacao Calculadoras' => 'Professores para Verificação',
                                    'Apoio TIC' => 'Equipa de Apoio TIC'
                                ];
                                $labelEspecial = $labelsEspeciais[$sessao['tipo_prova']] ?? 'Suplentes Convocados';
                            ?>
                            <dt class="col-sm-6"><?= $labelEspecial ?>:</dt>
                            <dd class="col-sm-6"><span class="badge bg-info"><?= $suplentesConvocados ?></span></dd>

                            <?php if (!in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC'])): ?>
                            <dt class="col-sm-6">Total Convocados:</dt>
                            <dd class="col-sm-6"><span class="badge bg-success"><?= count($convocatorias) ?></span></dd>
                            <?php endif; ?>
                        </dl>
                        <div class="mt-3">
                            <a href="<?= base_url('convocatorias/criar/' . $sessao['id']) ?>" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Adicionar Convocatória
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-list-check"></i> Convocatórias</h3>
                <div class="card-tools">
                    <?php if (!empty($convocatorias)): ?>
                    <button type="button" class="btn btn-sm btn-success" id="btnEnviarTodas" title="Enviar convocatórias para todos">
                        <i class="bi bi-envelope-fill"></i> Enviar para Todos
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($convocatorias)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> Ainda não há convocatórias para esta sessão.
                    </div>
                <?php else: ?>
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Professor</th>
                                <th>Função</th>
                                <th>Sala</th>
                                <th>Estado</th>
                                <th>Contacto</th>
                                <th>Email Enviado</th>
                                <th width="100">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($convocatorias as $conv): ?>
                                <tr>
                                    <td><?= esc($conv['professor_nome']) ?></td>
                                    <td><span class="badge bg-primary"><?= esc($conv['funcao']) ?></span></td>
                                    <td><?= $conv['codigo_sala'] ? esc($conv['codigo_sala']) : '<em>Suplente</em>' ?></td>
                                    <td>
                                        <?php
                                        $badge = match($conv['estado_confirmacao']) {
                                            'Confirmado' => 'bg-success',
                                            'Pendente' => 'bg-warning',
                                            'Rejeitado' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= esc($conv['estado_confirmacao']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($conv['professor_email']): ?>
                                            <i class="bi bi-envelope"></i> <?= esc($conv['professor_email']) ?><br>
                                        <?php endif; ?>
                                        <?php if ($conv['professor_telefone']): ?>
                                            <i class="bi bi-telephone"></i> <?= esc($conv['professor_telefone']) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($conv['email_enviado'])): ?>
                                            <span class="badge bg-success" title="Enviado em <?= date('d/m/Y H:i', strtotime($conv['data_envio_email'])) ?>">
                                                <i class="bi bi-check-circle"></i> Sim
                                            </span>
                                            <br>
                                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($conv['data_envio_email'])) ?></small>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-x-circle"></i> Não
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-info btn-enviar-individual" 
                                                data-id="<?= $conv['id'] ?>" 
                                                data-nome="<?= esc($conv['professor_nome']) ?>"
                                                title="Enviar convocatória por email">
                                            <i class="bi bi-envelope"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Enviar convocatória individual
    $('.btn-enviar-individual').on('click', function() {
        const convocatoriaId = $(this).data('id');
        const professorNome = $(this).data('nome');
        const btn = $(this);

        Swal.fire({
            title: 'Enviar Convocatória',
            html: `Deseja enviar a convocatória por email para:<br><strong>${professorNome}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-envelope"></i> Enviar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                btn.prop('disabled', true);
                const originalHtml = btn.html();
                btn.html('<i class="bi bi-hourglass-split"></i>');

                $.ajax({
                    url: '<?= base_url('sessoes-exame/enviar-convocatoria') ?>/' + convocatoriaId,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        btn.prop('disabled', false);
                        btn.html(originalHtml);

                        if (response.success) {
                            Swal.fire({
                                title: 'Sucesso!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Recarregar a página para mostrar a atualização do status de envio
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Erro',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        btn.html(originalHtml);
                        
                        Swal.fire({
                            title: 'Erro',
                            text: 'Erro ao enviar email. Por favor, tente novamente.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });

    // Enviar para todos
    $('#btnEnviarTodas').on('click', function() {
        const btn = $(this);
        const sessaoId = <?= $sessao['id'] ?>;

        Swal.fire({
            title: 'Enviar para Todos',
            text: 'Deseja enviar a convocatória por email para todos os vigilantes desta sessão?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="bi bi-envelope-fill"></i> Enviar para Todos',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loading
                btn.prop('disabled', true);
                const originalHtml = btn.html();
                btn.html('<i class="bi bi-hourglass-split"></i> A enviar...');

                $.ajax({
                    url: '<?= base_url('sessoes-exame/enviar-convocatorias-todas') ?>/' + sessaoId,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        btn.prop('disabled', false);
                        btn.html(originalHtml);

                        if (response.success) {
                            Swal.fire({
                                title: 'Sucesso!',
                                html: `<strong>${response.message}</strong><br><br>
                                       Emails enviados: ${response.enviados}`,
                                icon: 'success',
                                confirmButtonColor: '#28a745'
                            }).then(() => {
                                // Recarregar a página para mostrar a atualização do status de envio
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Atenção',
                                html: `${response.message}<br><br>
                                       Enviados: ${response.enviados || 0}<br>
                                       Erros: ${response.erros || 0}`,
                                icon: 'warning',
                                confirmButtonColor: '#ffc107'
                            }).then(() => {
                                // Recarregar mesmo com erros para mostrar quais foram enviados
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        btn.html(originalHtml);
                        
                        Swal.fire({
                            title: 'Erro',
                            text: 'Erro ao enviar emails. Por favor, tente novamente.',
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
