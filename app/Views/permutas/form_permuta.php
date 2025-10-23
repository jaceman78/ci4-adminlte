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
                    <li class="breadcrumb-item"><a href="<?= base_url('/permutas') ?>">Meu Horário</a></li>
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
            <div class="col-md-8 offset-md-2">
                <!-- Card com informações da aula -->
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-exchange-alt"></i> Informações da Aula</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Disciplina:</strong> 
                                    <?php if (!empty($aula['disciplina_abrev']) && !empty($aula['disciplina_nome'])): ?>
                                        <?= esc($aula['disciplina_abrev']) ?> - <?= esc($aula['disciplina_nome']) ?>
                                    <?php else: ?>
                                        <?= esc($aula['disciplina_id'] ?? 'N/A') ?>
                                    <?php endif; ?>
                                </p>
                                <p><strong>Turma:</strong> 
                                    <?php if (!empty($aula['turma_nome'])): ?>
                                        <?= esc($aula['turma_codigo']) ?> - <?= esc($aula['turma_nome']) ?>
                                    <?php else: ?>
                                        <?= esc($aula['codigo_turma'] ?? 'N/A') ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Dia da Semana:</strong> 
                                    <?php 
                                    $dias = ['', '', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
                                    echo $dias[$aula['dia_semana']];
                                    ?>
                                </p>
                                <p><strong>Horário:</strong> <?= substr($aula['hora_inicio'], 0, 5) ?> - <?= substr($aula['hora_fim'], 0, 5) ?></p>
                                <p><strong>Sala:</strong> <?= esc($aula['sala_id'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulário de Permuta -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Dados da Permuta</h3>
                    </div>
                    <form id="formPermuta">
                        <div class="card-body">
                            <input type="hidden" name="aula_original_id" value="<?= esc($aula['id_aula']) ?>">
                            <input type="hidden" id="dia_semana_aula" value="<?= esc($aula['dia_semana']) ?>">
                            
                            <!-- Data da Aula Original -->
                            <div class="form-group">
                                <label for="data_aula_original">Data da Aula a Permutar <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="data_aula_original" name="data_aula_original" required>
                                <small class="form-text text-muted">
                                    Data específica em que não pode dar a aula 
                                    <?php 
                                    $dias = ['', '', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
                                    echo '(apenas ' . $dias[$aula['dia_semana']] . ')';
                                    ?>
                                </small>
                            </div>

                            <!-- Professor Substituto -->
                            <div class="form-group">
                                <label for="professor_substituto_nif">Professor Substituto <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="professor_substituto_nif" name="professor_substituto_nif" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach ($professores as $prof): ?>
                                        <option value="<?= esc($prof['NIF']) ?>" 
                                                <?= $prof['NIF'] == $userNif ? 'selected' : '' ?>>
                                            <?= esc($prof['name']) ?> <?= $prof['NIF'] == $userNif ? '(Eu próprio)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Pode selecionar-se a si próprio se for repor a aula noutra data</small>
                            </div>

                            <!-- Data da Aula Permutada -->
                            <div class="form-group">
                                <label for="data_aula_permutada">Data de Reposição <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="data_aula_permutada" name="data_aula_permutada" required>
                                <small class="form-text text-muted">Data em que a aula será reposta</small>
                            </div>

                            <!-- Sala -->
                            <div class="form-group">
                                <label for="sala_permutada_id">Sala para Reposição</label>
                                <select class="form-control select2" id="sala_permutada_id" name="sala_permutada_id" disabled>
                                    <option value="">Selecione primeiro a data de reposição...</option>
                                </select>
                                <small class="form-text text-muted">
                                    <span id="sala_info" class="text-info"></span>
                                </small>
                            </div>

                            <!-- Outras aulas do mesmo dia e mesma turma -->
                            <?php if (!empty($aulasNoDia)): ?>
                            <div class="form-group">
                                <label>Incluir outras aulas do mesmo dia? <small class="text-muted">(mesma turma)</small></label>
                                <div class="border p-3 rounded">
                                    <?php foreach ($aulasNoDia as $aulaExtra): ?>
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" 
                                                   id="aula_<?= $aulaExtra['id_aula'] ?>" 
                                                   name="aulas_adicionais[]" 
                                                   value="<?= $aulaExtra['id_aula'] ?>">
                                            <label class="custom-control-label" for="aula_<?= $aulaExtra['id_aula'] ?>">
                                                <?php 
                                                $disciplina = !empty($aulaExtra['disciplina_nome']) ? 
                                                    esc($aulaExtra['disciplina_nome']) : 
                                                    esc($aulaExtra['disciplina_abrev']); 
                                                ?>
                                                <strong><?= $disciplina ?></strong>
                                                (<?= substr($aulaExtra['hora_inicio'], 0, 5) ?> - <?= substr($aulaExtra['hora_fim'], 0, 5) ?>)
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="form-text text-muted">
                                    Marque se quiser permutar várias aulas da mesma turma em conjunto
                                </small>
                            </div>
                            <?php endif; ?>

                            <!-- Observações -->
                            <div class="form-group">
                                <label for="observacoes">Observações/Justificação</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3" 
                                          placeholder="Descreva o motivo da permuta..."></textarea>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-paper-plane"></i> Solicitar Permuta
                            </button>
                            <a href="<?= base_url('/permutas') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Inicializar Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Definir data mínima como hoje
    var today = new Date().toISOString().split('T')[0];
    $('#data_aula_original').attr('min', today);
    $('#data_aula_permutada').attr('min', today);

    // Obter dia da semana da aula original (2=Segunda, 3=Terça, ..., 7=Sábado)
    var diaSemanaAula = parseInt($('#dia_semana_aula').val());
    
    // Validar data da aula original - só permitir o mesmo dia da semana
    $('#data_aula_original').on('change', function() {
        var dataSelecionada = $(this).val();
        if (!dataSelecionada) return;
        
        // Calcular dia da semana da data selecionada
        // JavaScript: 0=Domingo, 1=Segunda, 2=Terça, ..., 6=Sábado
        // Nosso sistema: 2=Segunda, 3=Terça, ..., 7=Sábado
        var date = new Date(dataSelecionada + 'T00:00:00');
        var diaSemanaJS = date.getDay(); // 0-6
        var diaSemanaConvertido = diaSemanaJS === 0 ? 1 : diaSemanaJS + 1; // Converter para nosso formato
        
        if (diaSemanaConvertido !== diaSemanaAula) {
            var diasNomes = ['', 'Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
            Swal.fire({
                icon: 'error',
                title: 'Data Inválida',
                html: 'A data selecionada é <strong>' + diasNomes[diaSemanaConvertido] + '</strong>.<br>' +
                      'Por favor, selecione uma data que seja <strong>' + diasNomes[diaSemanaAula] + '</strong>.',
                confirmButtonText: 'OK'
            });
            $(this).val('');
        }
    });

    // Validar antes de submeter
    $('#formPermuta').on('submit', function(e) {
        var dataSelecionada = $('#data_aula_original').val();
        if (dataSelecionada) {
            var date = new Date(dataSelecionada + 'T00:00:00');
            var diaSemanaJS = date.getDay();
            var diaSemanaConvertido = diaSemanaJS === 0 ? 1 : diaSemanaJS + 1;
            
            if (diaSemanaConvertido !== diaSemanaAula) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Data Inválida',
                    text: 'A data da aula a permutar deve ser do mesmo dia da semana da aula original.',
                    confirmButtonText: 'OK'
                });
                return false;
            }
        }
    });

    // Função para carregar salas livres
    function carregarSalasLivres() {
        var dataReposicao = $('#data_aula_permutada').val();
        var aulaOriginalId = $('input[name="aula_original_id"]').val();
        
        if (!dataReposicao) {
            $('#sala_permutada_id').prop('disabled', true)
                .html('<option value="">Selecione primeiro a data de reposição...</option>');
            $('#sala_info').text('');
            return;
        }

        // Coletar IDs das aulas adicionais selecionadas
        var aulasAdicionais = [];
        $('input[name="aulas_adicionais[]"]:checked').each(function() {
            aulasAdicionais.push($(this).val());
        });

        // Mostrar loading
        $('#sala_permutada_id').prop('disabled', true)
            .html('<option value="">A carregar salas livres...</option>');
        $('#sala_info').html('<i class="fas fa-spinner fa-spin"></i> A verificar disponibilidade...');

        $.ajax({
            url: '<?= base_url('permutas/getSalasLivres') ?>',
            type: 'POST',
            data: {
                data_reposicao: dataReposicao,
                aula_original_id: aulaOriginalId,
                aulas_adicionais: aulasAdicionais
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var options = '<option value="">Mesma sala original</option>';
                    
                    if (response.salas && response.salas.length > 0) {
                        $.each(response.salas, function(index, sala) {
                            var descricao = sala.descricao ? ' - ' + sala.descricao : '';
                            var capacidade = sala.capacidade ? ' (Cap: ' + sala.capacidade + ')' : '';
                            options += '<option value="' + sala.codigo_sala + '">' + 
                                      sala.codigo_sala + descricao + capacidade + '</option>';
                        });
                        
                        var horariosText = response.total_horarios > 1 ? 
                            response.total_horarios + ' horários' : '1 horário';
                        $('#sala_info').html('<i class="fas fa-check-circle text-success"></i> ' + 
                            response.salas.length + ' sala(s) livre(s) encontrada(s) para ' + horariosText);
                    } else {
                        $('#sala_info').html('<i class="fas fa-exclamation-triangle text-warning"></i> ' +
                            'Nenhuma sala livre encontrada. Use a sala original.');
                    }
                    
                    $('#sala_permutada_id').html(options).prop('disabled', false);
                } else {
                    $('#sala_permutada_id').html('<option value="">Erro ao carregar salas</option>');
                    $('#sala_info').html('<i class="fas fa-times-circle text-danger"></i> ' + 
                        (response.message || 'Erro ao carregar salas'));
                }
            },
            error: function(xhr) {
                $('#sala_permutada_id').html('<option value="">Erro ao carregar salas</option>');
                $('#sala_info').html('<i class="fas fa-times-circle text-danger"></i> Erro na comunicação com o servidor');
            }
        });
    }

    // Carregar salas quando a data de reposição mudar
    $('#data_aula_permutada').on('change', carregarSalasLivres);

    // Recarregar salas quando aulas adicionais forem marcadas/desmarcadas
    $('input[name="aulas_adicionais[]"]').on('change', function() {
        if ($('#data_aula_permutada').val()) {
            carregarSalasLivres();
        }
    });

    // Submit do formulário (continuação da validação anterior)
    $('#formPermuta').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        Swal.fire({
            title: 'A submeter...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '<?= base_url('permutas/salvar') ?>',
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
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = response.redirect;
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: response.message,
                        html: response.errors ? '<ul>' + Object.values(response.errors).map(e => '<li>' + e + '</li>').join('') + '</ul>' : response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: 'Erro ao processar pedido: ' + xhr.statusText
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
