<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="bi bi-door-open"></i> Alocar Salas - Sessão de Exame - <?= esc($sessao['codigo_prova']) ?> - <?= esc($sessao['nome_prova']) ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Início</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sessoes-exame') ?>">Sessões de Exame</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sessoes-exame/detalhes/' . $sessao['id']) ?>">Detalhes</a></li>
                        <li class="breadcrumb-item active">Alocar Salas</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Info da Sessão -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="bi bi-info-circle"></i> Informações da Sessão</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Exame:</strong> <?= esc($sessao['codigo_prova']) ?> - <?= esc($sessao['nome_prova']) ?></p>
                                    <p><strong>Tipo:</strong> <span class="badge bg-info"><?= esc($sessao['tipo_prova']) ?></span></p>
                                    <p><strong>Fase:</strong> <?= esc($sessao['fase']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($sessao['data_exame'])) ?></p>
                                    <p><strong>Hora:</strong> <?= date('H:i', strtotime($sessao['hora_exame'])) ?></p>
                                    <p><strong>Duração:</strong> <?= $sessao['duracao_minutos'] ?> minutos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="bi bi-bar-chart"></i> Estatísticas</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Total de Salas:</strong> <?= count($salasAlocadas) ?></p>
                            <?php if (!in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC'])): ?>
                            <p><strong>Alunos Inscritos:</strong> <?= number_format($totalAlunosInscritos, 0, ',', '.') ?></p>
                            <p><strong>Alunos Alocados:</strong> 
                                <?= number_format($totalAlunosAlocados, 0, ',', '.') ?>
                                <?php if ($totalAlunosAlocados < $totalAlunosInscritos): ?>
                                    <span class="badge bg-warning"><?= $totalAlunosInscritos - $totalAlunosAlocados ?> por alocar</span>
                                <?php elseif ($totalAlunosAlocados == $totalAlunosInscritos): ?>
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Completo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger"><i class="bi bi-exclamation-triangle"></i> Excedeu!</span>
                                <?php endif; ?>
                            </p>
                            <?php else: ?>
                            <?php 
                                $tiposEspeciais = [
                                    'Suplentes' => 'Sala de Espera para Suplentes',
                                    'Verificacao Calculadoras' => 'Verificação de Calculadoras',
                                    'Apoio TIC' => 'Apoio TIC - Provas em Computador'
                                ];
                                $descricaoEspecial = $tiposEspeciais[$sessao['tipo_prova']] ?? 'Sessão Especial';
                            ?>
                            <p class="text-info"><i class="bi bi-info-circle"></i> <strong><?= $descricaoEspecial ?></strong></p>
                            <p><small>Não aplicável contagem de alunos</small></p>
                            <?php endif; ?>
                            <?php if (!$isTipoEspecial): ?>
                            <p><strong>Vigilantes Necessários:</strong> <span class="badge bg-primary fs-6"><?= $totalVigilantesNecessarios ?></span></p>
                            <?php if ($sessao['tipo_prova'] === 'MODa'): ?>
                                <p class="text-info"><i class="bi bi-info-circle"></i> <small>MODa: 1 vigilante por sala</small></p>
                            <?php else: ?>
                                <p class="text-info"><i class="bi bi-info-circle"></i> <small>Regra: 2 vigilantes por sala</small></p>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salas Alocadas -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-door-open"></i> Salas Alocadas</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm" id="btnAdicionarSala">
                            <i class="bi bi-plus-circle"></i> Adicionar Sala
                        </button>
                        <a href="<?= base_url('sessoes-exame/detalhes/' . $sessao['id']) ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($salasAlocadas)): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Nenhuma sala alocada ainda.</strong> 
                            Clique em "Adicionar Sala" para começar a alocar salas a esta sessão.
                        </div>
                    <?php else: ?>
                        <table id="tableSalas" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sala</th>
                                    <th>Alunos</th>
                                    <th>Vigilantes Necessários</th>
                                    <th>Vigilantes Alocados</th>
                                    <th>Em Falta</th>
                                    <th>Estado</th>
                                    <th>Observações</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal Adicionar/Editar Sala -->
<div class="modal fade" id="modalSala" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalSalaTitle">Adicionar Sala</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formSala">
                <div class="modal-body">
                    <input type="hidden" id="sala_id" name="id">
                    <input type="hidden" name="sessao_exame_id" value="<?= $sessao['id'] ?>">

                    <div class="mb-3">
                        <label for="sala_select" class="form-label">Sala <span class="text-danger">*</span></label>
                        <select class="form-select" id="sala_select" name="sala_id" required>
                            <option value="">Selecione a sala...</option>
                            <?php 
                            $escolaAtual = '';
                            foreach ($salasDisponiveis as $sala): 
                                // Agrupar por escola
                                if ($escolaAtual !== $sala['escola_nome']):
                                    if ($escolaAtual !== ''): ?>
                                        </optgroup>
                                    <?php endif; 
                                    $escolaAtual = $sala['escola_nome'];
                                    ?>
                                    <optgroup label="<?= esc($escolaAtual) ?>">
                                <?php endif; ?>
                                <option value="<?= $sala['id'] ?>">
                                    <?= esc($sala['codigo_sala']) ?>
                                </option>
                            <?php endforeach; 
                            if ($escolaAtual !== ''): ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Apenas salas ainda não alocadas aparecem aqui.</small>
                    </div>

                    <div class="mb-3">
                        <?php $isTipoEspecial = in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC']); ?>
                        <label for="num_alunos_sala" class="form-label">Número de Alunos <?php if (!$isTipoEspecial): ?><span class="text-danger">*</span><?php endif; ?></label>
                        <input type="number" class="form-control" id="num_alunos_sala" name="num_alunos_sala" min="0" <?php if (!$isTipoEspecial): ?>required<?php endif; ?> <?php if ($isTipoEspecial): ?>value="0"<?php endif; ?>>
                        <?php if ($isTipoEspecial): ?>
                            <?php 
                                $mensagensEspeciais = [
                                    'Suplentes' => 'Para suplentes, deixe em 0 (sala de espera)',
                                    'Verificacao Calculadoras' => 'Para verificação de calculadoras, deixe em 0',
                                    'Apoio TIC' => 'Para apoio TIC, deixe em 0'
                                ];
                                $mensagemEspecial = $mensagensEspeciais[$sessao['tipo_prova']] ?? 'Deixe em 0';
                            ?>
                            <small class="text-muted"><i class="bi bi-info-circle"></i> <?= $mensagemEspecial ?></small>
                        <?php endif; ?>
                    </div>

                    <?php if (!$isTipoEspecial): ?>
                    <div class="mb-3">
                        <label class="form-label">Vigilantes Necessários</label>
                        <div class="alert alert-info">
                            <?php if ($sessao['tipo_prova'] === 'MODa'): ?>
                                <i class="bi bi-info-circle"></i> Para provas <strong>MODa</strong>: 1 vigilante por sala
                            <?php else: ?>
                                <i class="bi bi-info-circle"></i> Para esta prova: <strong>sempre 2 vigilantes por sala</strong>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="2" placeholder="Ex: Sala adaptada para NEE, Sala com projetor"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const sessaoExameId = <?= $sessao['id'] ?>;
    const isMODa = <?= $sessao['tipo_prova'] === 'MODa' ? 'true' : 'false' ?>;
    const isTipoEspecial = <?= in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC']) ? 'true' : 'false' ?>;
    let dataTable;

    // Inicializar DataTable
    <?php if (!empty($salasAlocadas)): ?>
    dataTable = $('#tableSalas').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('sessoes-exame-salas/getDataTable') ?>',
            type: 'POST',
            data: {
                sessao_exame_id: sessaoExameId
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2, className: 'text-center' },
            { data: 3, className: 'text-center' },
            { data: 4, className: 'text-center' },
            { data: 5, className: 'text-center' },
            { data: 6, className: 'text-center' },
            { data: 7 },
            { data: 8, orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        }
    });
    <?php endif; ?>

    // Adicionar Sala
    $('#btnAdicionarSala').on('click', function() {
        $('#formSala')[0].reset();
        $('#sala_id').val('');
        $('#modalSalaTitle').text('Adicionar Sala');
        $('#sala_select').prop('disabled', false);
        
        // Se for tipo especial (suplentes/verificação calculadoras), preencher com 0
        if (isTipoEspecial) {
            $('#num_alunos_sala').val(0);
        }
        
        $('#modalSala').modal('show');
    });

    // Editar Sala
    $(document).on('click', '.btn-editar', function() {
        const id = $(this).data('id');
        
        $.get('<?= base_url('sessoes-exame-salas/get') ?>/' + id, function(response) {
            if (response.success) {
                $('#sala_id').val(response.data.id);
                $('#num_alunos_sala').val(response.data.num_alunos_sala);
                $('#observacoes').val(response.data.observacoes);
                
                // Limpar dropdown
                $('#sala_select').empty();
                
                // Adicionar apenas a sala atual (não permitir trocar)
                const salaTexto = response.data.escola_nome + ' - ' + response.data.codigo_sala;
                $('#sala_select').append(
                    $('<option></option>')
                        .val(response.data.sala_id)
                        .text(salaTexto)
                        .prop('selected', true)
                );
                
                $('#sala_select').prop('disabled', true);
                $('#modalSalaTitle').text('Editar Sala');
                $('#modalSala').modal('show');
            }
        });
    });

    // Submeter Formulário
    $('#formSala').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#sala_id').val();
        const url = id ? '<?= base_url('sessoes-exame-salas/update') ?>/' + id : '<?= base_url('sessoes-exame-salas/store') ?>';
        
        const data = {
            sessao_exame_id: sessaoExameId,
            sala_id: $('#sala_select').val(),
            num_alunos_sala: $('#num_alunos_sala').val(),
            observacoes: $('#observacoes').val()
        };

        $.ajax({
            url: url,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: response.message,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: response?.message || 'Erro ao guardar sala.'
                });
            }
        });
    });

    // Eliminar Sala
    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Tem a certeza?',
            text: 'Esta ação irá remover a sala desta sessão. As convocatórias associadas também serão afetadas.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, remover!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('sessoes-exame-salas/delete') ?>/' + id,
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Removido!',
                                text: response.message,
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message
                            });
                        }
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
