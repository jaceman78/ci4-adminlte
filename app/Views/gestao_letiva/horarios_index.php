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
                        <li class="breadcrumb-item active">Horários</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><?= $page_subtitle ?></h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#modalImportarCSV">
                            <i class="bi bi-file-earmark-arrow-up"></i> Importar CSV
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalHorario">
                            <i class="bi bi-plus-circle"></i> Novo Horário
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableHorarios" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Turma</th>
                                <th>Disciplina</th>
                                <th>Professor</th>
                                <th>Sala</th>
                                <th>Dia</th>
                                <th>Intervalo</th>
                                <th>Tempo</th>
                                <th>Turno</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Modal Horário -->
<div class="modal fade" id="modalHorario" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHorarioTitle">Novo Horário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formHorario">
                <input type="hidden" id="horario_id" name="id_aula">
                <div class="modal-body">
                    <div class="alert alert-warning" id="alertConflitos" style="display:none;">
                        <strong>Atenção!</strong>
                        <ul id="listaConflitos"></ul>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_nif">Professor *</label>
                                <select class="form-control" id="user_nif" name="user_nif" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach($professores as $prof): ?>
                                        <option value="<?= $prof['NIF'] ?>"><?= $prof['name'] ?> (<?= $prof['NIF'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="disciplina_id">Disciplina *</label>
                                <select class="form-control" id="disciplina_id" name="disciplina_id" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach($disciplinas as $tipologia => $discs): ?>
                                        <optgroup label="<?= $tipologia ?>">
                                            <?php foreach($discs as $disc): ?>
                                                <option value="<?= $disc['id_disciplina'] ?>"><?= $disc['abreviatura'] ?> - <?= $disc['descritivo'] ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="codigo_turma">Turma *</label>
                                <select class="form-control" id="codigo_turma" name="codigo_turma" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach($turmas as $codigo => $label): ?>
                                        <option value="<?= $codigo ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sala_id">Sala</label>
                                <select class="form-control" id="sala_id" name="sala_id">
                                    <option value="">Sem sala</option>
                                    <?php foreach($salas as $sala): ?>
                                        <option value="<?= $sala['codigo_sala'] ?>">
                                            <?= $sala['codigo_sala'] ?>
                                            <?php if(!empty($sala['descricao'])): ?>
                                                - <?= $sala['descricao'] ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dia_semana">Dia da Semana *</label>
                                <select class="form-control" id="dia_semana" name="dia_semana" required>
                                    <option value="">Selecione...</option>
                                    <?php foreach($diasSemana as $valor => $descricao): ?>
                                        <option value="<?= $valor ?>"><?= $descricao ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tempo">Tempo</label>
                                <input type="number" class="form-control" id="tempo" name="tempo" min="0" step="1" placeholder="Ex: 1">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hora_inicio">Hora Início *</label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="hora_fim">Hora Fim *</label>
                                <input type="time" class="form-control" id="hora_fim" name="hora_fim" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="intervalo">Intervalo</label>
                                <input type="text" class="form-control" id="intervalo" name="intervalo" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="turno">Turno</label>
                                <select class="form-control" id="turno" name="turno">
                                    <option value="">Sem turno</option>
                                    <option value="T1">Turno 1 (T1)</option>
                                    <option value="T2">Turno 2 (T2)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Importar CSV -->
<div class="modal fade" id="modalImportarCSV" tabindex="-1" aria-labelledby="modalImportarCSVLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formImportarCSV" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImportarCSVLabel">Importar Horários (CSV)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Instruções -->
                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Instruções para o Ficheiro CSV</h6>
                        <p class="mb-2">O ficheiro CSV deve seguir a estrutura abaixo. Use <strong>TAB</strong> ou <strong>ponto e vírgula (;)</strong> como separador entre colunas:</p>
                        <div class="bg-light p-3 rounded border">
                            <code style="display: block; white-space: pre; font-family: 'Courier New', monospace; font-size: 0.9em;">
codigo_turma	disciplina_id	user_nif	sala_id	Turno	dia	tempo	intervalo	hora_inicio	hora_fim
10A	MAT	194341402	A101	T1	2	45		08:00	08:45
10B	PORT	188869387	B202	T2	3	90		09:00	10:30</code>
                        </div>
                        <hr>
                        <h6><i class="bi bi-list-check"></i> Detalhes dos Campos:</h6>
                        <ul class="mb-0 small">
                            <li><strong>codigo_turma</strong>: Código da turma (ex: 10A, 10B, 1/2 AM)</li>
                            <li><strong>disciplina_id</strong>: ID da disciplina (ex: MAT, PORT, FQ)</li>
                            <li><strong>user_nif</strong>: NIF do professor (ex: 194341402)</li>
                            <li><strong>sala_id</strong>: Código da sala (ex: A101, B202) - <em>pode ficar vazio</em></li>
                            <li><strong>Turno</strong>: T1, T2 ou vazio - <em>pode ficar vazio</em></li>
                            <li><strong>dia</strong>: Dia da semana (2=Segunda, 3=Terça, 4=Quarta, 5=Quinta, 6=Sexta, 7=Sábado)</li>
                            <li><strong>tempo</strong>: Duração em minutos (ex: 45, 90) - <em>pode ficar vazio</em></li>
                            <li><strong>intervalo</strong>: Formato HH:MM - HH:MM - <em>gerado automaticamente, pode ficar vazio</em></li>
                            <li><strong>hora_inicio</strong>: Hora de início (formato HH:MM, ex: 08:00)</li>
                            <li><strong>hora_fim</strong>: Hora de fim (formato HH:MM, ex: 08:45)</li>
                        </ul>
                    </div>

                    <!-- Upload do ficheiro -->
                    <div class="form-group">
                        <label for="csv_file">Selecione o ficheiro CSV:</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                        <small class="form-text text-muted">Formatos aceites: .csv, .txt (separados por TAB)</small>
                    </div>

                    <!-- Área de resultados -->
                    <div id="importResults" style="display:none;">
                        <hr>
                        <h6>Resultado da Importação:</h6>
                        <div id="importResultsContent"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success" id="btnImportar">
                        <i class="bi bi-upload"></i> Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#tableHorarios').DataTable({
        ajax: {
            url: '<?= base_url('horarios/getDataTable') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_aula' },
            { data: 'turma_label' },
            { data: 'disciplina_nome' },
            { data: 'professor_nome' },
            { data: 'sala_label' },
            {
                data: 'dia_semana',
                render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return row.dia_semana_label || '';
                    }
                    return data;
                }
            },
            {
                data: 'intervalo',
                render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return data || '';
                    }
                    return row.hora_inicio || '';
                }
            },
            {
                data: 'tempo',
                render: function(data, type) {
                    if (type === 'display' || type === 'filter') {
                        return data !== null && data !== '' ? data : '—';
                    }
                    return data !== null ? data : '';
                }
            },
            {
                data: 'turno',
                render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return row.turno_label || 'Sem turno';
                    }
                    return data || '';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info btn-edit" data-id="${row.id_aula}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id_aula}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        order: [[5, 'asc'], [6, 'asc']],
        responsive: true
    });

    function atualizarIntervalo() {
        var inicio = $('#hora_inicio').val();
        var fim = $('#hora_fim').val();
        if (inicio && fim) {
            $('#intervalo').val(inicio + ' - ' + fim);
        } else {
            $('#intervalo').val('');
        }
    }

    $('#hora_inicio, #hora_fim').on('change', atualizarIntervalo);

    // Criar/Atualizar Horário
    $('#formHorario').on('submit', function(e) {
        e.preventDefault();
        var id = $('#horario_id').val();
        var url = id ? '<?= base_url('horarios/update') ?>/' + id : '<?= base_url('horarios/create') ?>';
        
        // Limpar alertas anteriores
        $('#alertConflitos').hide();
        $('#listaConflitos').empty();
        
        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalHorario'));
                    if(modal) modal.hide();
                    table.ajax.reload();
                    toastr.success(response.message);
                } else if(response.conflitos) {
                    // Mostrar conflitos
                    response.conflitos.forEach(function(conflito) {
                        $('#listaConflitos').append('<li>' + conflito + '</li>');
                    });
                    $('#alertConflitos').show();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Editar Horário
    $('#tableHorarios').on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        
        $.get('<?= base_url('horarios/get') ?>/' + id, function(data) {
            $('#horario_id').val(data.id_aula);
            $('#user_nif').val(data.user_nif);
            $('#disciplina_id').val(data.disciplina_id);
            $('#codigo_turma').val(data.codigo_turma);
            $('#sala_id').val(data.sala_id);
            $('#dia_semana').val(data.dia_semana);
            $('#tempo').val(data.tempo);
            $('#hora_inicio').val(data.hora_inicio);
            $('#hora_fim').val(data.hora_fim);
            $('#intervalo').val(data.intervalo);
            $('#turno').val(data.turno || '');
            atualizarIntervalo();
            $('#modalHorarioTitle').text('Editar Horário');
            var modal = new bootstrap.Modal(document.getElementById('modalHorario'));
            modal.show();
        });
    });

    // Eliminar Horário
    $('#tableHorarios').on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        $('#deleteHorarioId').val(id);
        var modalDelete = new bootstrap.Modal(document.getElementById('modalDeleteHorario'));
        modalDelete.show();
    });

    // Confirmar eliminação
    $('#btnConfirmDeleteHorario').on('click', function() {
        var id = $('#deleteHorarioId').val();
        
        $.ajax({
            url: '<?= base_url('horarios/delete') ?>/' + id,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteHorario'));
                modalDelete.hide();
                
                if(response.success) {
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteHorario'));
                modalDelete.hide();
                toastr.error('Erro ao eliminar horário');
            }
        });
    });

    // Limpar form ao fechar modal
    $('#modalHorario').on('hidden.bs.modal', function() {
        $('#formHorario')[0].reset();
        $('#horario_id').val('');
        $('#modalHorarioTitle').text('Novo Horário');
        $('#alertConflitos').hide();
        $('#listaConflitos').empty();
        $('#intervalo').val('');
        $('#turno').val('');
    });

    // Importar CSV
    $('#formImportarCSV').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var btnImportar = $('#btnImportar');
        
        // Desabilitar botão durante upload
        btnImportar.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Importando...');
        $('#importResults').hide();
        
        $.ajax({
            url: '<?= base_url('horarios/importarCSV') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                btnImportar.prop('disabled', false).html('<i class="bi bi-upload"></i> Importar');
                
                if(response.success) {
                    // Mostrar resultados
                    var html = '<div class="alert alert-success">';
                    html += '<strong><i class="bi bi-check-circle"></i> Importação concluída!</strong><br>';
                    html += 'Total de registos processados: ' + response.total + '<br>';
                    html += 'Registos importados com sucesso: ' + response.sucesso + '<br>';
                    
                    if(response.erros > 0) {
                        html += 'Registos com erro: ' + response.erros;
                    }
                    html += '</div>';
                    
                    // Mostrar erros se houver
                    if(response.detalhes_erros && response.detalhes_erros.length > 0) {
                        html += '<div class="alert alert-warning">';
                        html += '<strong>Detalhes dos erros:</strong><ul class="mb-0">';
                        response.detalhes_erros.forEach(function(erro) {
                            html += '<li>Linha ' + erro.linha + ': ' + erro.erro + '</li>';
                        });
                        html += '</ul></div>';
                    }
                    
                    $('#importResultsContent').html(html);
                    $('#importResults').show();
                    
                    // Recarregar tabela
                    table.ajax.reload();
                    
                    // Mostrar toast
                    toastr.success('Importação concluída! ' + response.sucesso + ' de ' + response.total + ' registos importados.');
                    
                } else {
                    $('#importResultsContent').html('<div class="alert alert-danger">' + response.message + '</div>');
                    $('#importResults').show();
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                btnImportar.prop('disabled', false).html('<i class="bi bi-upload"></i> Importar');
                var errorMsg = 'Erro ao importar ficheiro.';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#importResultsContent').html('<div class="alert alert-danger">' + errorMsg + '</div>');
                $('#importResults').show();
                toastr.error(errorMsg);
            }
        });
    });

    // Limpar form ao fechar modal de importação
    $('#modalImportarCSV').on('hidden.bs.modal', function() {
        $('#formImportarCSV')[0].reset();
        $('#importResults').hide();
        $('#importResultsContent').empty();
    });
});
</script>

<!-- Modal Confirmação de Eliminação -->
<div class="modal fade" id="modalDeleteHorario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Tem a certeza que deseja eliminar este horário?</p>
                <p class="text-muted small mb-0">Esta ação não pode ser revertida.</p>
                <input type="hidden" id="deleteHorarioId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteHorario">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


