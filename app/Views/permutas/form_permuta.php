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

                <!-- Créditos Disponíveis -->
                <?php if (!empty($creditosDisponiveis)): ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h5><i class="icon fas fa-check"></i> Você tem <?= count($creditosDisponiveis) ?> crédito(s) disponível(is)!</h5>
                    <p>Pode usar um crédito de visita de estudo para repor esta aula. Selecione abaixo:</p>
                    <div class="list-group">
                        <?php foreach ($creditosDisponiveis as $credito): ?>
                        <div class="list-group-item">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" 
                                       id="credito_<?= $credito['id'] ?>" 
                                       name="usar_credito_id[]" 
                                       class="custom-control-input usar-credito" 
                                       value="<?= $credito['id'] ?>"
                                       data-data-visita="<?= $credito['data_visita'] ?>"
                                       data-origem="<?= esc($credito['origem']) ?>"
                                       data-turno="<?= esc($credito['turno'] ?? '') ?>">
                                <label class="custom-control-label" for="credito_<?= $credito['id'] ?>">
                                    <strong>Visita: <?= esc($credito['origem']) ?></strong><br>
                                    <small>Data da visita: <?= date('d/m/Y', strtotime($credito['data_visita'])) ?>
                                    <?php if ($credito['turno']): ?>
                                        | Turno: <span class="badge bg-primary"><?= esc($credito['turno']) ?></span>
                                    <?php endif; ?>
                                    </small>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <div class="list-group-item bg-light">
                            <div class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="limpar-creditos">
                                    <i class="fas fa-times"></i> Limpar Seleção (Permuta Normal)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Formulário de Permuta -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-alt"></i> Dados da Permuta</h3>
                    </div>
                    <form id="formPermuta">
                        <div class="card-body">
                            <input type="hidden" name="aula_original_id" value="<?= esc($aula['id_aula']) ?>">
                            <input type="hidden" id="dia_semana_aula" value="<?= esc($aula['dia_semana']) ?>">
                            <input type="hidden" id="turno_aula_principal" value="<?= esc($aula['turno'] ?? '') ?>">
                            
                            <!-- Data da Aula Original -->
                            <div class="form-group">
                                <label for="data_aula_original">Data em que irá faltar <span class="text-danger">*</span></label>
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
                                <small class="form-text text-muted">Selecione "Eu próprio" se for repor a aula noutra data</small>
                            </div>

                            <!-- Data da Aula Permutada -->
                            <div class="form-group">
                                <label for="data_aula_permutada">Data de Reposição <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="data_aula_permutada" name="data_aula_permutada" required>
                                <small class="form-text text-muted">
                                    <span id="data-reposicao-info">Data em que a aula será reposta</span>
                                </small>
                            </div>

            <!-- Bloco Horário para Reposição -->
            <div class="form-group" id="bloco-reposicao-container" style="display: block !important;">
                <label for="bloco_reposicao">Bloco Horário de Reposição <span class="text-danger">*</span></label>
                <select class="form-control select2" id="bloco_reposicao" name="bloco_reposicao" required disabled>
                    <option value="">Selecione primeiro a data de reposição...</option>
                </select>
                <small class="form-text text-muted">
                    <?php if (!empty($aula['hora_inicio']) && !empty($aula['hora_fim'])): ?>
                        <strong>Bloco original:</strong> <?= substr($aula['hora_inicio'], 0, 5) ?> - <?= substr($aula['hora_fim'], 0, 5) ?>
                        <br>
                    <?php endif; ?>
                    <span id="bloco_info" class="text-info"></span>
                </small>
            </div>                            <!-- Sala -->
                            <div class="form-group" id="sala-reposicao-container" style="display: block !important;">
                                <label for="sala_permutada_id">Sala para Reposição <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="sala_permutada_id" name="sala_permutada_id" required disabled>
                                    <option value="">Selecione primeiro o bloco horário...</option>
                                </select>
                                <small class="form-text text-muted">
                                    Apenas salas disponíveis no horário selecionado serão mostradas.
                                    <br><span id="sala_info" class="text-info"></span>
                                </small>
                            </div>

                            <!-- Outras aulas do mesmo dia e mesma turma -->
                            <?php if (!empty($aulasNoDia)): ?>
                            <div class="form-group">
                                <label>Incluir outras aulas do mesmo dia? <small class="text-muted">(mesma turma)</small></label>
                                <div class="border p-3 rounded">
                                    <?php foreach ($aulasNoDia as $aulaExtra): ?>
                                        <div class="custom-control custom-checkbox aula-adicional-item" data-turno="<?= esc($aulaExtra['turno'] ?? '') ?>">
                                            <input class="custom-control-input" type="checkbox" 
                                                   id="aula_<?= $aulaExtra['id_aula'] ?>" 
                                                   name="aulas_adicionais[]" 
                                                   value="<?= $aulaExtra['id_aula'] ?>"
                                                   data-turno="<?= esc($aulaExtra['turno'] ?? '') ?>">
                                            <label class="custom-control-label" for="aula_<?= $aulaExtra['id_aula'] ?>">
                                                <?php 
                                                $disciplina = !empty($aulaExtra['disciplina_nome']) ? 
                                                    esc($aulaExtra['disciplina_nome']) : 
                                                    esc($aulaExtra['disciplina_abrev']); 
                                                ?>
                                                <strong><?= $disciplina ?></strong>
                                                (<?= substr($aulaExtra['hora_inicio'], 0, 5) ?> - <?= substr($aulaExtra['hora_fim'], 0, 5) ?>)
                                                <?php if (!empty($aulaExtra['turno'])): ?>
                                                    <span class="badge bg-secondary ms-1">Turno: <?= esc($aulaExtra['turno']) ?></span>
                                                <?php endif; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div id="aulas-adicionais-info">
                                    <small class="form-text text-muted">
                                        Marque se quiser permutar várias aulas da mesma turma em conjunto
                                    </small>
                                </div>
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

    // Filtrar aulas adicionais pelo turno da aula principal
    function filtrarAulasAdicionaisPorTurnoPrincipal() {
        var turnoPrincipal = $('#turno_aula_principal').val();
        
        // Se não há turno definido, mostrar todas
        if (!turnoPrincipal) {
            $('.aula-adicional-item').show();
            return;
        }
        
        // Filtrar por turno
        $('.aula-adicional-item').each(function() {
            var turnoAula = $(this).data('turno');
            
            // Mostrar apenas se:
            // 1. A aula tem o mesmo turno da aula principal
            // 2. OU a aula não tem turno definido (para compatibilidade)
            if (turnoAula === turnoPrincipal || !turnoAula) {
                $(this).show();
            } else {
                $(this).hide();
                // Desmarcar se estava marcado
                $(this).find('input[type="checkbox"]').prop('checked', false);
            }
        });
        
        // Mostrar aviso se houver aulas filtradas
        var aulasVisiveis = $('.aula-adicional-item:visible').length;
        var aulasTotal = $('.aula-adicional-item').length;
        
        if (aulasVisiveis < aulasTotal) {
            $('#aulas-adicionais-info').html(
                '<small class="form-text text-info">' +
                '<i class="fas fa-info-circle"></i> Mostrando apenas aulas do Turno ' + turnoPrincipal + 
                ' (' + aulasVisiveis + ' de ' + aulasTotal + ' aulas)' +
                '</small>'
            );
        } else {
            $('#aulas-adicionais-info').html(
                '<small class="form-text text-muted">Marque se quiser permutar várias aulas da mesma turma em conjunto</small>'
            );
        }
    }
    
    // Aplicar filtro ao carregar a página
    filtrarAulasAdicionaisPorTurnoPrincipal();

    // Manipular seleção de créditos
    $('.usar-credito').on('change', function() {
        var creditosSelecionados = $('.usar-credito:checked');
        
        if (creditosSelecionados.length > 0) {
            // Se selecionou pelo menos um crédito
            var dataVisita = $(this).data('data-visita');
            var origem = $(this).data('origem');
            
            // Preencher data de reposição com data da visita
            $('#data_aula_permutada').val(dataVisita);
            
            // Atualizar texto explicativo
            var dataFormatada = new Date(dataVisita + 'T00:00:00').toLocaleDateString('pt-PT');
            $('#data-reposicao-info').html('<strong class="text-success">A aula foi reposta na visita de estudo em ' + dataFormatada + '</strong>');
            
            // Preencher observações
            $('#observacoes').val('Aula reposta na visita de estudo: ' + origem);
            
            // Selecionar "Eu próprio" como professor substituto
            var userNif = '<?= $userNif ?>';
            $('#professor_substituto_nif').val(userNif).trigger('change');
            
            // OCULTAR campos de bloco e sala (não são necessários para créditos)
            $('#bloco-reposicao-container').hide();
            $('#sala-reposicao-container').hide();
            $('#bloco_reposicao').prop('required', false).val('0');
            $('#sala_permutada_id').html('<option value="VE" selected>[VE] - Visita de Estudo</option>').prop('required', false);
            
            // Filtrar aulas adicionais por turno
            var creditoTurno = $(this).data('turno') || $(this).closest('.list-group-item').find('.badge').text().trim();
            filtrarAulasAdicionaisPorTurno(creditoTurno);
            
            // Verificar quantas aulas estão sendo permutadas
            var totalAulas = 1 + $('input[name="aulas_adicionais[]"]:checked').length;
            
            // Validar se há créditos suficientes (apenas se marcou um crédito)
            if ($(this).is(':checked') && creditosSelecionados.length < totalAulas) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Créditos Insuficientes!',
                    html: `Precisa selecionar <strong>${totalAulas} crédito(s)</strong> para cobrir todas as aulas.<br><br>` +
                          `Atualmente selecionados: <strong>${creditosSelecionados.length}</strong>`,
                    confirmButtonText: 'Entendi'
                });
                return;
            }
            
            // Verificar correspondência de turnos
            if (creditosSelecionados.length > 1) {
                var turnosUnicos = [];
                creditosSelecionados.each(function() {
                    var turno = $(this).data('turno');
                    if (turno && turnosUnicos.indexOf(turno) === -1) {
                        turnosUnicos.push(turno);
                    }
                });
                
                if (turnosUnicos.length > 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Atenção aos Turnos!',
                        html: 'Créditos de turnos diferentes selecionados:<br>' +
                              turnosUnicos.map(t => `• Turno ${t}`).join('<br>') +
                              '<br><br>Verifique se as aulas correspondem aos turnos dos créditos.',
                        timer: 4000
                    });
                }
            }
            
            // Mostrar mensagem de sucesso
            if ($(this).is(':checked')) {
                Swal.fire({
                    icon: 'success',
                    title: 'Crédito Selecionado!',
                    html: 'A aula será reposta na visita de estudo.<br>Pode selecionar múltiplos créditos para múltiplas aulas.',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
            
            // Validar créditos necessários
            validarCreditosNecessarios();
            
        } else {
            // Nenhum crédito selecionado - voltar ao modo normal
            $('#bloco-reposicao-container').show();
            $('#sala-reposicao-container').show();
            $('#bloco_reposicao').prop('required', true);
            $('#sala_permutada_id').html('<option value="">Selecione...</option>').prop('required', true);
            $('#data-reposicao-info').text('Data em que a aula será reposta');
            $('input[name="aulas_adicionais[]"]').closest('.custom-control').show();
            $('#creditos-info').empty();
            
            Swal.fire({
                icon: 'info',
                title: 'Créditos Desmarcados',
                text: 'Voltou ao modo de permuta normal.',
                timer: 2000,
                showConfirmButton: false
            });
        }
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

    // Função para carregar blocos horários disponíveis
    function carregarBlocosHorarios() {
        var dataReposicao = $('#data_aula_permutada').val();
        
        if (!dataReposicao) {
            $('#bloco_reposicao').prop('disabled', true)
                .html('<option value="">Selecione primeiro a data de reposição...</option>');
            $('#bloco_info').text('');
            return;
        }

        $('#bloco_info').html('<i class="fas fa-spinner fa-spin"></i> A carregar blocos...');
        
        $.ajax({
            url: '<?= base_url('permutas/getBlocosHorarios') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.blocos) {
                    var options = '<option value="">Selecione o bloco horário...</option>';
                    
                    if (response.blocos.length > 0) {
                        $.each(response.blocos, function(index, bloco) {
                            var label = bloco.designacao || 
                                       (bloco.hora_inicio.substring(0,5) + ' - ' + bloco.hora_fim.substring(0,5));
                            options += '<option value="' + bloco.id_bloco + '">' + 
                                      label + 
                                      '</option>';
                        });
                        $('#bloco_info').html('<i class="fas fa-check-circle text-success"></i> ' + 
                            response.blocos.length + ' bloco(s) disponível(is)');
                    } else {
                        options = '<option value="">Nenhum bloco disponível</option>';
                        $('#bloco_info').html('<i class="fas fa-exclamation-triangle text-warning"></i> ' +
                            'Nenhum bloco horário encontrado para este dia');
                    }
                    
                    $('#bloco_reposicao').html(options).prop('disabled', false);
                    $('#bloco_info').html('<i class="fas fa-check-circle text-success"></i> ' + 
                        response.blocos.length + ' blocos horários disponíveis');
                } else {
                    $('#bloco_reposicao').html('<option value="">Nenhum bloco disponível</option>');
                    $('#bloco_info').html('<i class="fas fa-times-circle text-danger"></i> ' + 
                        (response.message || 'Nenhum bloco horário encontrado'));
                }
            },
            error: function(xhr) {
                $('#bloco_reposicao').html('<option value="">Erro ao carregar blocos</option>');
                $('#bloco_info').html('<i class="fas fa-times-circle text-danger"></i> Erro na comunicação');
            }
        });
    }

    // Função para carregar salas livres
    function carregarSalasLivres() {
        var dataReposicao = $('#data_aula_permutada').val();
        var blocoReposicao = $('#bloco_reposicao').val();
        var aulaOriginalId = $('input[name="aula_original_id"]').val();
        
        if (!dataReposicao) {
            $('#sala_permutada_id').prop('disabled', true)
                .html('<option value="">Selecione primeiro a data de reposição...</option>');
            $('#sala_info').text('');
            return;
        }

        if (!blocoReposicao) {
            $('#sala_permutada_id').prop('disabled', true)
                .html('<option value="">Selecione primeiro o bloco horário...</option>');
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
                bloco_reposicao: blocoReposicao,
                aula_original_id: aulaOriginalId,
                aulas_adicionais: aulasAdicionais,
                professor_substituto_nif: $('#professor_substituto_nif').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var options = '<option value="">Selecione uma sala disponível...</option>';
                    
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
                            response.salas.length + ' sala(s) disponível(is) para ' + horariosText);
                        $('#sala_permutada_id').html(options).prop('disabled', false);
                    } else {
                        $('#sala_info').html('<i class="fas fa-times-circle text-danger"></i> ' +
                            'Nenhuma sala disponível neste horário. Escolha outra data ou bloco.');
                        $('#sala_permutada_id').html('<option value="">Sem salas disponíveis</option>').prop('disabled', true);
                    }
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

    // Event Listeners
    // Carregar blocos quando a data de reposição mudar
    $('#data_aula_permutada').on('change', function() {
        var dataSelecionada = $(this).val();
        if (!dataSelecionada) return;
        
        // Validar se não é domingo (0) nem sábado (6)
        var date = new Date(dataSelecionada + 'T00:00:00');
        var diaSemanaJS = date.getDay();
        
        if (diaSemanaJS === 0 || diaSemanaJS === 6) {
            var diaNome = diaSemanaJS === 0 ? 'Domingo' : 'Sábado';
            Swal.fire({
                icon: 'error',
                title: 'Data Inválida',
                html: 'Não é possível marcar reposições aos <strong>' + diaNome + 's</strong>.<br>' +
                      'Por favor, selecione uma data entre Segunda e Sexta-feira.',
                confirmButtonText: 'OK'
            });
            $(this).val('');
            $('#bloco_reposicao').html('<option value="">Selecione primeiro a data de reposição...</option>').prop('disabled', true);
            $('#sala_permutada_id').html('<option value="">Selecione primeiro o bloco horário...</option>').prop('disabled', true);
            return;
        }
        
        carregarBlocosHorarios();
        // Limpar sala e bloco selecionados
        $('#bloco_reposicao').html('<option value="">Carregando...</option>').prop('disabled', true);
        $('#sala_permutada_id').html('<option value="">Selecione primeiro o bloco horário...</option>').prop('disabled', true);
    });

    // Carregar salas quando o bloco horário mudar
    $('#bloco_reposicao').on('change', carregarSalasLivres);

    // Recarregar salas quando o professor substituto mudar
    // (importante para quando escolhe "Eu próprio" - sala original fica disponível)
    $('#professor_substituto_nif').on('change', function() {
        if ($('#data_aula_permutada').val() && $('#bloco_reposicao').val()) {
            carregarSalasLivres();
        }
    });

    // Função para filtrar aulas adicionais por turno
    function filtrarAulasAdicionaisPorTurno(turnoSelecionado) {
        if (!turnoSelecionado) {
            // Se não há turno, mostrar todas as aulas
            $('input[name="aulas_adicionais[]"]').closest('.custom-control').show();
            return;
        }
        
        // Ocultar todas as aulas primeiro
        $('input[name="aulas_adicionais[]"]').closest('.custom-control').hide().find('input').prop('checked', false);
        
        // Mostrar apenas aulas do mesmo turno
        $('input[name="aulas_adicionais[]"]').each(function() {
            var aulaText = $(this).closest('.custom-control').find('label').text();
            if (aulaText.includes('Turno: ' + turnoSelecionado) || !aulaText.includes('Turno:')) {
                $(this).closest('.custom-control').show();
            }
        });
        
        // Atualizar texto explicativo
        $('#aulas-adicionais-info').html(
            '<small class="text-info"><i class="fas fa-info-circle"></i> ' +
            'Apenas aulas do turno ' + turnoSelecionado + ' podem ser agrupadas com este crédito.</small>'
        );
    }

    // Recarregar salas quando aulas adicionais forem marcadas/desmarcadas
    $('input[name="aulas_adicionais[]"]').on('change', function() {
        if ($('#data_aula_permutada').val() && $('#bloco_reposicao').val()) {
            carregarSalasLivres();
        }
        
        // Validar créditos quando aulas adicionais mudam
        validarCreditosNecessarios();
    });
    
    // Função para validar se há créditos suficientes
    function validarCreditosNecessarios() {
        var creditosSelecionados = $('.usar-credito:checked').length;
        
        // Só validar se há créditos selecionados
        if (creditosSelecionados === 0) {
            $('#creditos-info').empty();
            return;
        }
        
        // Calcular total de aulas: 1 aula principal + aulas adicionais
        var aulasAdicionais = $('input[name="aulas_adicionais[]"]:checked').length;
        var totalAulas = 1 + aulasAdicionais;
        
        var infoDiv = $('#creditos-info');
        if (infoDiv.length === 0) {
            // Criar div de info se não existir
            $('.list-group').last().after('<div id="creditos-info" class="mt-2"></div>');
            infoDiv = $('#creditos-info');
        }
        
        if (creditosSelecionados < totalAulas) {
            var faltam = totalAulas - creditosSelecionados;
            infoDiv.html(
                '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> ' +
                `<strong>Atenção:</strong> Tem ${creditosSelecionados} crédito(s) selecionado(s), mas precisa de ${totalAulas} para cobrir:<br>` +
                `• 1 aula principal<br>` +
                `• ${aulasAdicionais} aula(s) adicional(is)<br>` +
                `<strong>Faltam ${faltam} crédito(s)!</strong></div>`
            );
        } else if (creditosSelecionados === totalAulas) {
            infoDiv.html(
                '<div class="alert alert-success"><i class="fas fa-check"></i> ' +
                `<strong>Perfeito!</strong> ${creditosSelecionados} crédito(s) cobrem ${totalAulas} aula(s):<br>` +
                `• 1 aula principal<br>` +
                `• ${aulasAdicionais} aula(s) adicional(is)</div>`
            );
        } else {
            var sobram = creditosSelecionados - totalAulas;
            infoDiv.html(
                '<div class="alert alert-info"><i class="fas fa-info-circle"></i> ' +
                `<strong>Informação:</strong> ${creditosSelecionados} crédito(s) selecionados para ${totalAulas} aula(s).<br>` +
                `Sobram ${sobram} crédito(s) não utilizados nesta permuta.</div>`
            );
        }
    }

    // Submit do formulário (continuação da validação anterior)
    $('#formPermuta').on('submit', function(e) {
        e.preventDefault();
        
        // Verificar se está usando créditos
        var creditosSelecionados = $('.usar-credito:checked');
        var usandoCreditos = creditosSelecionados.length > 0;
        
        if (usandoCreditos) {
            // Se usa créditos, garantir valores padrão e habilitar campos
            $('#bloco_reposicao').val('0').prop('required', false).prop('disabled', false);
            $('#sala_permutada_id').val('VE').prop('required', false).prop('disabled', false);
        } else {
            // Habilitar campos disabled temporariamente para enviar os valores
            $('#bloco_reposicao').prop('disabled', false);
            $('#sala_permutada_id').prop('disabled', false);
        }
        
        // Serializar o formulário
        var formData = $(this).serialize();
        
        // IMPORTANTE: Adicionar créditos selecionados (estão fora do form)
        if (usandoCreditos) {
            creditosSelecionados.each(function() {
                formData += '&usar_credito_id[]=' + encodeURIComponent($(this).val());
            });
        }
        
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
