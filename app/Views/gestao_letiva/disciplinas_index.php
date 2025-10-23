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
                        <li class="breadcrumb-item active">Disciplinas</li>
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
                        <button type="button" class="btn btn-success btn-sm me-1" data-bs-toggle="modal" data-bs-target="#modalImportar">
                            <i class="bi bi-file-earmark-arrow-up"></i> Importar CSV
                        </button>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalDisciplina">
                            <i class="bi bi-plus-circle"></i> Nova Disciplina
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableDisciplinas" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Abreviatura</th>
                                <th>Descritivo</th>
                                <th>Tipologia</th>
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
<!-- Modal Disciplina -->
<div class="modal fade" id="modalDisciplina" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDisciplinaTitle">Nova Disciplina</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formDisciplina">
                <input type="hidden" id="is_editing" name="is_editing" value="0">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="disciplina_id">ID da Disciplina *</label>
                        <input type="text" class="form-control" id="disciplina_id" name="id_disciplina" required maxlength="50" placeholder="Ex: MAT9, PORT10, ING8">
                        <small class="form-text text-muted">ID alfanumérico único (será usado na importação)</small>
                    </div>
                    <div class="form-group">
                        <label for="abreviatura">Abreviatura da Disciplina *</label>
                        <input type="text" class="form-control" id="abreviatura" name="abreviatura" required maxlength="255" placeholder="Ex: MAT, PORT, ING">
                    </div>
                    <div class="form-group">
                        <label for="descritivo">Descritivo (Nome Completo)</label>
                        <textarea class="form-control" id="descritivo" name="descritivo" rows="3" placeholder="Nome completo e descrição da disciplina (opcional)"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="tipologia_id">Tipologia *</label>
                        <select class="form-control" id="tipologia_id" name="tipologia_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach($tipologias as $tipologia): ?>
                                <option value="<?= $tipologia['id_tipologia'] ?>"><?= $tipologia['nome_tipologia'] ?></option>
                            <?php endforeach; ?>
                        </select>
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
<div class="modal fade" id="modalImportar" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importar Disciplinas via CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formImportar" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Formato do Ficheiro CSV</h6>
                        <ul class="mb-0">
                            <li><strong>Extensão:</strong> .csv</li>
                            <li><strong>Separador:</strong> Ponto e vírgula (;)</li>
                            <li><strong>Encoding:</strong> UTF-8 ou Windows-1252</li>
                            <li><strong>Colunas obrigatórias (ordem fixa):</strong>
                                <ol class="mt-2">
                                    <li><code>Codigo</code> - ID alfanumérico único (ex: MAT9, PORT10)</li>
                                    <li><code>Abreviatura</code> - Abreviatura da disciplina (ex: MAT, PORT)</li>
                                    <li><code>Descritivo</code> - Descrição completa (opcional)</li>
                                    <li><code>Tipologia</code> - ID da tipologia:
                                        <ul class="mt-1">
                                            <?php foreach($tipologias as $tip): ?>
                                                <li><strong><?= $tip['id_tipologia'] ?></strong> = <?= esc($tip['nome_tipologia']) ?></li>
                                            <?php endforeach; ?>
                                            <li><strong>0</strong> = Inativo (usará primeira tipologia disponível)</li>
                                        </ul>
                                    </li>
                                </ol>
                            </li>
                            <li><strong>Primeira linha:</strong> Cabeçalho (será ignorado)</li>
                        </ul>
                        <p class="mt-2 mb-0"><strong>Exemplo:</strong><br>
                        <code>Codigo;Abreviatura;Descritivo;Tipologia<br>
                        MAT9;MAT;Matemática 9º Ano;1<br>
                        PORT10;PORT;Português 10º Ano;2</code></p>
                    </div>
                    
                    <div class="form-group">
                        <label for="csv_file">Selecionar Ficheiro CSV *</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                    </div>
                    
                    <div class="form-check mt-3">
                        <input type="checkbox" class="form-check-input" id="skip_duplicates" name="skip_duplicates" checked>
                        <label class="form-check-label" for="skip_duplicates">
                            Ignorar registos duplicados (recomendado)
                        </label>
                    </div>
                    
                    <div id="import_progress" class="mt-3" style="display:none;">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                        </div>
                        <p class="text-center mt-2 mb-0" id="import_status">A processar...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
    var table = $('#tableDisciplinas').DataTable({
        ajax: {
            url: '<?= base_url('disciplinas/getDataTable') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_disciplina' },
            { data: 'abreviatura' },
            { data: 'descritivo', render: function(data) {
                return data ? data : '<span class="text-muted">-</span>';
            }},
            { data: 'nome_tipologia' },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info btn-edit" data-id="${row.id_disciplina}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id_disciplina}">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        responsive: true
    });

    // Criar/Atualizar Disciplina
    $('#formDisciplina').on('submit', function(e) {
        e.preventDefault();
        var id = $('#disciplina_id').val();
        var url = id ? '<?= base_url('disciplinas/update') ?>/' + id : '<?= base_url('disciplinas/create') ?>';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalDisciplina'));
                    if(modal) modal.hide();
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Editar Disciplina
    $('#tableDisciplinas').on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        
        $.get('<?= base_url('disciplinas/get') ?>/' + id, function(data) {
            $('#is_editing').val('1');
            $('#disciplina_id').val(data.id_disciplina).prop('readonly', true); // ID não editável
            $('#abreviatura').val(data.abreviatura);
            $('#descritivo').val(data.descritivo || '');
            $('#tipologia_id').val(data.tipologia_id);
            $('#modalDisciplinaTitle').text('Editar Disciplina');
            var modal = new bootstrap.Modal(document.getElementById('modalDisciplina'));
            modal.show();
        });
    });

    // Eliminar Disciplina
    $('#tableDisciplinas').on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        $('#deleteDisciplinaId').val(id);
        var modalDelete = new bootstrap.Modal(document.getElementById('modalDeleteDisciplina'));
        modalDelete.show();
    });

    // Confirmar eliminação
    $('#btnConfirmDeleteDisciplina').on('click', function() {
        var id = $('#deleteDisciplinaId').val();
        
        $.ajax({
            url: '<?= base_url('disciplinas/delete') ?>/' + id,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteDisciplina'));
                modalDelete.hide();
                
                if(response.success) {
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteDisciplina'));
                modalDelete.hide();
                toastr.error('Erro ao eliminar disciplina');
            }
        });
    });

    // Limpar form ao fechar modal
    $('#modalDisciplina').on('hidden.bs.modal', function() {
        $('#formDisciplina')[0].reset();
        $('#is_editing').val('0');
        $('#disciplina_id').val('').prop('readonly', false); // Permitir edição ao criar novo
        $('#modalDisciplinaTitle').text('Nova Disciplina');
    });

    // Importar CSV
    $('#formImportar').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        
        $('#btnImportar').prop('disabled', true);
        $('#import_progress').show();
        $('.progress-bar').css('width', '0%').text('0%');
        $('#import_status').text('A processar ficheiro...');
        
        $.ajax({
            url: '<?= base_url('disciplinas/importar') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        $('.progress-bar').css('width', percentComplete + '%').text(percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                $('#import_progress').hide();
                $('#btnImportar').prop('disabled', false);
                
                if(response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalImportar'));
                    if(modal) modal.hide();
                    table.ajax.reload();
                    
                    var msg = response.message + '<br>';
                    if(response.imported > 0) msg += '<strong>Importados:</strong> ' + response.imported + '<br>';
                    if(response.skipped > 0) msg += '<strong>Ignorados (duplicados):</strong> ' + response.skipped + '<br>';
                    if(response.errors > 0) msg += '<strong>Erros:</strong> ' + response.errors;
                    
                    toastr.success(msg, 'Importação Concluída');
                } else {
                    toastr.error(response.message, 'Erro na Importação');
                }
            },
            error: function(xhr) {
                $('#import_progress').hide();
                $('#btnImportar').prop('disabled', false);
                toastr.error('Erro ao processar o ficheiro. Verifique o formato.', 'Erro');
            }
        });
    });

    // Limpar form de importação ao fechar modal
    $('#modalImportar').on('hidden.bs.modal', function() {
        $('#formImportar')[0].reset();
        $('#import_progress').hide();
        $('.progress-bar').css('width', '0%');
    });
});
</script>

<!-- Modal Confirmação de Eliminação -->
<div class="modal fade" id="modalDeleteDisciplina" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Tem a certeza que deseja eliminar esta disciplina?</p>
                <p class="text-muted small mb-0">Esta ação não pode ser revertida.</p>
                <input type="hidden" id="deleteDisciplinaId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteDisciplina">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
