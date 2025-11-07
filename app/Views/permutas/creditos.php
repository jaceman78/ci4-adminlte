<?= $this->extend('layout/master') ?>

<?= $this->section('styles') ?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($page_title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/permutas') ?>">Horário</a></li>
                    <li class="breadcrumb-item active">Créditos</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Info Box -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info-circle"></i> Sistema de Créditos de Aulas</h5>
                    <p class="mb-0">
                        Os créditos resultam de visitas de estudo e podem ser usados em permutas do tipo "Eu próprio".
                        <?php if (!empty($anoLetivoAtivo['anoletivo'])): ?>
                            <strong>Validade:</strong> Ano letivo atual (<?= esc($anoLetivoAtivo['anoletivo']) ?>-<?= esc($anoLetivoAtivo['anoletivo'] + 1) ?>)
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Resumo -->
        <div class="row mb-3">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $totalDisponiveis ?></h3>
                        <p>Créditos Disponíveis</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $totalUsados ?></h3>
                        <p>Créditos Usados</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <?php if ($userLevel >= 6): ?>
            <div class="col-lg-6">
                <div class="card card-primary card-outline">
                    <div class="card-body">
                        <button type="button" class="btn btn-primary btn-lg btn-block" data-bs-toggle="modal" data-bs-target="#modalCriarCredito">
                            <i class="fas fa-plus-circle"></i> Registar Créditos de Visita de Estudo
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Créditos Disponíveis -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Créditos Disponíveis
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($creditosDisponiveis)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Não existem créditos disponíveis.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table id="tabelaCreditosDisponiveis" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <?php if ($userLevel >= 6): ?>
                                            <th>Professor</th>
                                            <?php endif; ?>
                                            <th>Turma</th>
                                            <th>Disciplina</th>
                                            <th>Turno</th>
                                            <th>Origem (Visita)</th>
                                            <th>Data Visita</th>
                                            <th>Criado em</th>
                                            <?php if ($userLevel >= 6): ?>
                                            <th>Ações</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($creditosDisponiveis as $credito): ?>
                                            <tr>
                                                <?php if ($userLevel >= 6): ?>
                                                <td><?= esc($credito['professor_nome'] ?? $credito['professor_nif']) ?></td>
                                                <?php endif; ?>
                                                <td>
                                                    <strong><?= esc($credito['codigo_turma']) ?></strong>
                                                    <?php if (!empty($credito['turma_nome'])): ?>
                                                        <br><small class="text-muted"><?= esc($credito['turma_nome']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?= esc($credito['disciplina_abrev'] ?? '') ?></strong>
                                                    <?php if (!empty($credito['disciplina_nome'])): ?>
                                                        <br><small class="text-muted"><?= esc($credito['disciplina_nome']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= $credito['turno'] ? '<span class="badge bg-primary">' . esc($credito['turno']) . '</span>' : '<span class="text-muted">-</span>' ?>
                                                </td>
                                                <td><?= esc($credito['origem']) ?></td>
                                                <td><?= date('d/m/Y', strtotime($credito['data_visita'])) ?></td>
                                                <td><?= date('d/m/Y', strtotime($credito['created_at'])) ?></td>
                                                <?php if ($userLevel >= 6): ?>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-danger btn-cancelar-credito" 
                                                            data-id="<?= $credito['id'] ?>"
                                                            title="Cancelar crédito">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Créditos Usados -->
        <div class="row">
            <div class="col-12">
                <div class="card card-secondary collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-history"></i> Histórico - Créditos Usados
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($creditosUsados)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Nenhum crédito usado ainda.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table id="tabelaCreditosUsados" class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <?php if ($userLevel >= 6): ?>
                                            <th>Professor</th>
                                            <?php endif; ?>
                                            <th>Turma</th>
                                            <th>Disciplina</th>
                                            <th>Turno</th>
                                            <th>Origem</th>
                                            <th>Usado em</th>
                                            <th>Permuta</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($creditosUsados as $credito): ?>
                                            <tr>
                                                <?php if ($userLevel >= 6): ?>
                                                <td><?= esc($credito['professor_nome'] ?? $credito['professor_nif']) ?></td>
                                                <?php endif; ?>
                                                <td><?= esc($credito['codigo_turma']) ?></td>
                                                <td><?= esc($credito['disciplina_abrev'] ?? $credito['disciplina_nome'] ?? '') ?></td>
                                                <td><?= $credito['turno'] ? '<span class="badge bg-success">' . esc($credito['turno']) . '</span>' : '<span class="text-muted">-</span>' ?></td>
                                                <td><?= esc($credito['origem']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($credito['data_uso'])) ?></td>
                                                <td>
                                                    <?php if (!empty($credito['usado_em_permuta_id'])): ?>
                                                        <a href="<?= base_url('permutas/ver/' . $credito['usado_em_permuta_id']) ?>" 
                                                           class="btn btn-xs btn-info" target="_blank">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Modal Criar Crédito -->
<?php if ($userLevel >= 6): ?>
<div class="modal fade" id="modalCriarCredito" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Registar Créditos de Visita de Estudo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCriarCredito">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="professor_nif">Professor <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="professor_nif" name="professor_nif" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($professores as $prof): ?>
                                        <option value="<?= esc($prof['NIF']) ?>">
                                            <?= esc($prof['name']) ?> (<?= esc($prof['NIF']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="data_visita">Data da Visita <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="data_visita" name="data_visita" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo_turma">Turma <span class="text-danger">*</span></label>
                                <select class="form-control" id="codigo_turma" name="codigo_turma" required disabled>
                                    <option value="">Selecione primeiro o professor...</option>
                                </select>
                                <small class="form-text text-muted">Apenas turmas que o professor leciona</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disciplina_id">Disciplina <span class="text-danger">*</span></label>
                                <select class="form-control" id="disciplina_id" name="disciplina_id" required disabled>
                                    <option value="">Selecione primeiro a turma...</option>
                                </select>
                                <small class="form-text text-muted">Apenas disciplinas que o professor leciona nesta turma</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6" id="turno-container" style="display: none;">
                            <div class="form-group">
                                <label for="turno">Turno</label>
                                <select class="form-control" id="turno" name="turno">
                                    <option value="">Todos os turnos</option>
                                </select>
                                <small class="form-text text-muted">Deixe vazio para criar créditos para todos os turnos</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="num_aulas">Número de Aulas <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="num_aulas" name="num_aulas" 
                                       min="1" max="20" value="1" required>
                                <small class="form-text text-muted">Quantidade de créditos a criar</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="origem">Descrição da Visita <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="origem" name="origem" 
                               placeholder="Ex: Visita de estudo ao Museu Nacional" 
                               maxlength="255" required>
                    </div>

                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" 
                                  rows="3" placeholder="Informações adicionais (opcional)"></textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Nota:</strong> Serão criados <strong id="preview-num-aulas">1</strong> crédito(s) de aula.
                        Se a disciplina tiver turnos e não selecionar um turno específico, serão criados créditos para cada turno.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Criar Créditos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cancelar Crédito -->
<div class="modal fade" id="modalCancelarCredito" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Cancelar Crédito</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="credito_id_cancelar">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Atenção:</strong> Esta ação não pode ser revertida.
                </div>
                <div class="form-group">
                    <label for="motivo_cancelamento">Motivo do Cancelamento <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="motivo_cancelamento" rows="4" 
                              placeholder="Indique o motivo..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarCancelamento">
                    <i class="fas fa-times-circle"></i> Cancelar Crédito
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTables
    $('#tabelaCreditosDisponiveis').DataTable({
        'order': [[<?= $userLevel >= 6 ? 5 : 4 ?>,'desc']],
        'language': {
            'url': 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        }
    });

    $('#tabelaCreditosUsados').DataTable({
        'order': [[<?= $userLevel >= 6 ? 5 : 4 ?>,'desc']],
        'language': {
            'url': 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        }
    });

    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#modalCriarCredito')
    });

    // Preview número de aulas
    $('#num_aulas').on('input', function() {
        $('#preview-num-aulas').text($(this).val());
    });

    // Ao selecionar professor → carregar turmas
    $('#professor_nif').on('change', function() {
        var professorNif = $(this).val();
        
        $('#codigo_turma').html('<option value="">Carregando...</option>').prop('disabled', true);
        $('#disciplina_id').html('<option value="">Selecione primeiro a turma...</option>').prop('disabled', true);
        $('#turno-container').hide();

        if (!professorNif) return;

        $.ajax({
            url: '<?= base_url('permutas/getTurmasProfessor') ?>',
            type: 'POST',
            data: { professor_nif: professorNif },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.turmas) {
                    var options = '<option value="">Selecione...</option>';
                    $.each(response.turmas, function(i, turma) {
                        options += '<option value="' + turma.codigo + '">' + 
                                  turma.codigo + ' - ' + turma.nome + '</option>';
                    });
                    $('#codigo_turma').html(options).prop('disabled', false);
                } else {
                    $('#codigo_turma').html('<option value="">Nenhuma turma encontrada</option>');
                }
            },
            error: function() {
                $('#codigo_turma').html('<option value="">Erro ao carregar</option>');
            }
        });
    });

    // Ao selecionar turma → carregar disciplinas
    $('#codigo_turma').on('change', function() {
        var professorNif = $('#professor_nif').val();
        var codigoTurma = $(this).val();
        
        $('#disciplina_id').html('<option value="">Carregando...</option>').prop('disabled', true);
        $('#turno-container').hide();

        if (!professorNif || !codigoTurma) return;

        $.ajax({
            url: '<?= base_url('permutas/getDisciplinasProfessorTurma') ?>',
            type: 'POST',
            data: { 
                professor_nif: professorNif,
                codigo_turma: codigoTurma 
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.disciplinas) {
                    var options = '<option value="">Selecione...</option>';
                    $.each(response.disciplinas, function(i, disc) {
                        options += '<option value="' + disc.descritivo + '">' + 
                                  disc.abreviatura + ' - ' + disc.descritivo + '</option>';
                    });
                    $('#disciplina_id').html(options).prop('disabled', false);
                } else {
                    $('#disciplina_id').html('<option value="">Nenhuma disciplina encontrada</option>');
                }
            },
            error: function() {
                $('#disciplina_id').html('<option value="">Erro ao carregar</option>');
            }
        });
    });

    // Ao selecionar disciplina → verificar turnos
    $('#disciplina_id').on('change', function() {
        var professorNif = $('#professor_nif').val();
        var codigoTurma = $('#codigo_turma').val();
        var disciplinaId = $(this).val();

        $('#turno-container').hide();
        $('#turno').html('<option value="">Todos os turnos</option>');

        if (!professorNif || !codigoTurma || !disciplinaId) return;

        $.ajax({
            url: '<?= base_url('permutas/verificarTurnosDisciplina') ?>',
            type: 'POST',
            data: {
                professor_nif: professorNif,
                codigo_turma: codigoTurma,
                disciplina_id: disciplinaId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.tem_turnos) {
                    var options = '<option value="">Todos os turnos</option>';
                    $.each(response.turnos, function(i, t) {
                        options += '<option value="' + t.turno + '">' + t.turno + '</option>';
                    });
                    $('#turno').html(options);
                    $('#turno-container').show();
                }
            }
        });
    });

    // Submit formulário criar crédito
    $('#formCriarCredito').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        Swal.fire({
            title: 'A processar...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '<?= base_url('permutas/salvarCredito') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        html: response.message + (response.errors ? '<br><br>' + Object.values(response.errors).join('<br>') : '')
                    });
                }
            },
            error: function() {
                Swal.close();
                Swal.fire('Erro!', 'Erro ao processar pedido', 'error');
            }
        });
    });

    // Cancelar crédito
    $('.btn-cancelar-credito').on('click', function() {
        var creditoId = $(this).data('id');
        $('#credito_id_cancelar').val(creditoId);
        $('#motivo_cancelamento').val('');
        
        var modalEl = document.getElementById('modalCancelarCredito');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    $('#btnConfirmarCancelamento').on('click', function() {
        var creditoId = $('#credito_id_cancelar').val();
        var motivo = $('#motivo_cancelamento').val().trim();

        if (!motivo) {
            Swal.fire('Atenção!', 'Indique o motivo do cancelamento', 'warning');
            return;
        }

        var modalEl = document.getElementById('modalCancelarCredito');
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        $.ajax({
            url: '<?= base_url('permutas/cancelarCredito') ?>',
            type: 'POST',
            data: {
                credito_id: creditoId,
                motivo: motivo
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cancelado!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Erro ao processar pedido', 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
