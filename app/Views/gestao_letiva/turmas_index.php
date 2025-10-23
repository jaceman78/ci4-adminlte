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
                        <li class="breadcrumb-item active">Turmas</li>
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
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTurma">
                            <i class="bi bi-plus-circle"></i> Nova Turma
                        </button>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalImportTurmas">
                            <i class="bi bi-upload"></i> Importar CSV
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableTurmas" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Código</th>
                                <th>Abreviatura</th>
                                <th>Descritivo</th>
                                <th>Ano</th>
                                <th>Diretor</th>
                                <th>Secretário</th>
                                <th>Escola</th>
                                <th>Ano Letivo</th>
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

<?= $this->section('styles') ?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Modal Turma -->
<div class="modal fade" id="modalTurma" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTurmaTitle">Nova Turma</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTurma">
                <input type="hidden" id="turma_id" name="id_turma">
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="codigo">Código *</label>
                                <input type="text" class="form-control" id="codigo" name="codigo" required maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="abreviatura">Abreviatura *</label>
                                <input type="text" class="form-control" id="abreviatura" name="abreviatura" required maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="ano">Ano *</label>
                                <select class="form-control" id="ano" name="ano" required>
                                    <option value="">Selecione...</option>
                                    <option value="0">Pré-escolar</option>
                                    <?php for($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?>º Ano</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="nome">Nome *</label>
                        <input type="text" class="form-control" id="nome" name="nome" required maxlength="63">
                    </div>
                    <div class="form-group">
                        <label for="descritivo">Descritivo</label>
                        <input type="text" class="form-control" id="descritivo" name="descritivo" maxlength="255">
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dir_turma_nif">Diretor de Turma (NIF)</label>
                                <select class="form-control" id="dir_turma_nif" name="dir_turma_nif">
                                    <option value="">Selecione...</option>
                                    <?php if(isset($professores) && count($professores) > 0): ?>
                                        <?php foreach($professores as $professor): ?>
                                            <option value="<?= $professor['NIF'] ?>"><?= $professor['name'] ?> (<?= $professor['NIF'] ?>)</option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Nenhum professor encontrado</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="secretario_nif">Secretário (NIF)</label>
                                <select class="form-control" id="secretario_nif" name="secretario_nif">
                                    <option value="">Selecione...</option>
                                    <?php if(isset($professores) && count($professores) > 0): ?>
                                        <?php foreach($professores as $professor): ?>
                                            <option value="<?= $professor['NIF'] ?>"><?= $professor['name'] ?> (<?= $professor['NIF'] ?>)</option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Nenhum professor encontrado</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="escola_id">Escola</label>
                                <select class="form-control" id="escola_id" name="escola_id">
                                    <option value="">Selecione...</option>
                                    <?php if(isset($escolas) && count($escolas) > 0): ?>
                                        <?php foreach($escolas as $escola): ?>
                                            <option value="<?= $escola['id'] ?>"><?= $escola['nome'] ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="" disabled>Nenhuma escola encontrada</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="num_alunos">Número de Alunos</label>
                                <input type="number" min="0" class="form-control" id="num_alunos" name="num_alunos" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="anoletivo_id">Ano Letivo *</label>
                        <select class="form-control" id="anoletivo_id" name="anoletivo_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach($anos_letivos as $ano): ?>
                                <option value="<?= $ano['id_anoletivo'] ?>" <?= $ano['status'] == 1 ? 'selected' : '' ?>>
                                    <?= $ano['anoletivo'] ?> <?= $ano['status'] == 1 ? '(Ativo)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tipologia_id">Tipologia *</label>
                        <select class="form-control" id="tipologia_id" name="tipologia_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach($tipologias as $tipologia): ?>
                                <option value="<?= $tipologia['id_tipologia'] ?>" <?= ($tipologia['id_tipologia'] == 1 ? 'selected' : '') ?>>
                                    <?= $tipologia['nome_tipologia'] ?>
                                </option>
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

<script>
$(document).ready(function() {
    // Inicializar Select2 para o campo Diretor de Turma
    $('#dir_turma_nif, #secretario_nif, #escola_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#modalTurma'),
        placeholder: 'Selecione um professor...',
        allowClear: true,
        language: {
            noResults: function() {
                return "Nenhum professor encontrado";
            },
            searching: function() {
                return "Procurando...";
            }
        }
    });

    // Inicializar DataTable
    var table = $('#tableTurmas').DataTable({
        ajax: {
            url: '<?= base_url('turmas/getDataTable') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_turma' },
            { data: 'codigo' },
            { data: 'abreviatura' },
            { data: 'descritivo', defaultContent: '' },
            { data: 'ano', render: function(data) {
                return data == 0 ? 'Pré-escolar' : data + 'º Ano';
            }},
            { data: 'nome_dt', defaultContent: '' },
            { data: 'nome_secretario', defaultContent: '' },
            { data: 'nome_escola', defaultContent: '' },
            { data: 'anoletivo' },
            { data: 'nome_tipologia' },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info btn-edit" data-id="${row.id_turma}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${row.id_turma}">
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

    // Criar/Atualizar Turma
    $('#formTurma').on('submit', function(e) {
        e.preventDefault();
        var id = $('#turma_id').val();
        var url = id ? '<?= base_url('turmas/update') ?>/' + id : '<?= base_url('turmas/create') ?>';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.success) {
                    var modal = bootstrap.Modal.getInstance(document.getElementById('modalTurma'));
                    if(modal) modal.hide();
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });

    // Editar Turma
    $('#tableTurmas').on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        
        $.get('<?= base_url('turmas/get') ?>/' + id, function(data) {
            $('#turma_id').val(data.id_turma);
            $('#codigo').val(data.codigo);
            $('#abreviatura').val(data.abreviatura);
            $('#descritivo').val(data.descritivo);
            $('#ano').val(data.ano);
            $('#nome').val(data.nome);
            $('#dir_turma_nif').val(data.dir_turma_nif).trigger('change');
            $('#secretario_nif').val(data.secretario_nif).trigger('change');
            $('#escola_id').val(data.escola_id).trigger('change');
            $('#num_alunos').val(data.num_alunos);
            $('#anoletivo_id').val(data.anoletivo_id);
            $('#tipologia_id').val(data.tipologia_id);
            $('#modalTurmaTitle').text('Editar Turma');
            var modal = new bootstrap.Modal(document.getElementById('modalTurma'));
            modal.show();
        });
    });

    // Eliminar Turma
    $('#tableTurmas').on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        $('#deleteTurmaId').val(id);
        var modalDelete = new bootstrap.Modal(document.getElementById('modalDeleteTurma'));
        modalDelete.show();
    });

    // Confirmar eliminação
    $('#btnConfirmDeleteTurma').on('click', function() {
        var id = $('#deleteTurmaId').val();
        
        $.ajax({
            url: '<?= base_url('turmas/delete') ?>/' + id,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteTurma'));
                modalDelete.hide();
                
                if(response.success) {
                    table.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                var modalDelete = bootstrap.Modal.getInstance(document.getElementById('modalDeleteTurma'));
                modalDelete.hide();
                toastr.error('Erro ao eliminar turma');
            }
        });
    });

    // Limpar form ao fechar modal
    $('#modalTurma').on('hidden.bs.modal', function() {
        $('#formTurma')[0].reset();
        $('#turma_id').val('');
        $('#dir_turma_nif').val(null).trigger('change');
        $('#secretario_nif').val(null).trigger('change');
        $('#escola_id').val(null).trigger('change');
        $('#modalTurmaTitle').text('Nova Turma');
    });
});
</script>

<!-- Modal Importar Turmas -->
<div class="modal fade" id="modalImportTurmas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Importar Turmas via CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formImportarTurmas" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Estrutura do ficheiro CSV (ponto e vírgula ;):</strong><br>
                        <code>Codigo;Abreviatura;Descritivo;Ano;NumAlunos;Secretario;Escola;DirTurma</code>
                        <ul class="mt-2 mb-0">
                            <li>Ano: número entre 0 e 12 (0 = Pré-escolar)</li>
                            <li>NumAlunos: número inteiro ≥ 0 (default 0)</li>
                            <li>Secretario e DirTurma: NIF dos utilizadores (pode ficar vazio)</li>
                            <li>Escola: ID da escola (numérico)</li>
                            <li>Outros campos ficam NULL ou são preenchidos automaticamente</li>
                            <li>Ano Letivo é preenchido automaticamente com o ano ativo; Tipologia por defeito = 1 (Regular)</li>
                        </ul>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-8">
                            <label for="csv_file_turmas" class="form-label">Selecionar Ficheiro CSV *</label>
                            <input type="file" class="form-control" id="csv_file_turmas" name="csv_file" accept=".csv" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="skip_duplicates_turmas" name="skip_duplicates">
                                <label class="form-check-label" for="skip_duplicates_turmas">Ignorar duplicados</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="download_errors_turmas" name="download_errors" checked>
                                <label class="form-check-label" for="download_errors_turmas">Gerar ficheiro de erros</label>
                            </div>
                        </div>
                    </div>
                    <div class="progress mt-3 d-none" id="progressImportTurmas">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div class="mt-3" id="importTurmasResult" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Handler de importação de turmas
$('#formImportarTurmas').on('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $('#progressImportTurmas').removeClass('d-none');
    $('#progressImportTurmas .progress-bar').css('width', '10%');

    $.ajax({
        url: '<?= base_url('turmas/importar') ?>',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener('progress', function(evt) {
                if (evt.lengthComputable) {
                    var percent = Math.round((evt.loaded / evt.total) * 100);
                    $('#progressImportTurmas .progress-bar').css('width', percent + '%');
                }
            }, false);
            return xhr;
        },
        success: function(resp) {
            $('#progressImportTurmas .progress-bar').css('width', '100%');
            var $res = $('#importTurmasResult');
            if (resp.success) {
                var html = `<div class="alert alert-success">
                    <strong>${resp.message}</strong><br>
                    Importadas: ${resp.imported || 0} | Ignoradas: ${resp.skipped || 0} | Erros: ${resp.errors || 0}
                </div>`;
                if (resp.error_file) {
                    html += `<a class="btn btn-outline-secondary btn-sm" href="${resp.error_file}" target="_blank">Descarregar linhas com erro</a>`;
                }
                $res.html(html).show();
                toastr.success(resp.message);
                // Atualizar tabela
                $('#tableTurmas').DataTable().ajax.reload();
            } else {
                $res.html(`<div class="alert alert-danger">${resp.message || 'Falha ao importar'}</div>`).show();
                toastr.error(resp.message || 'Falha ao importar');
            }
        },
        error: function() {
            $('#importTurmasResult').html('<div class="alert alert-danger">Erro na comunicação com o servidor.</div>').show();
            toastr.error('Erro na comunicação com o servidor');
        },
        complete: function() {
            setTimeout(function(){
                $('#progressImportTurmas').addClass('d-none');
                $('#progressImportTurmas .progress-bar').css('width', '0%');
            }, 500);
        }
    });
});
</script>

<!-- Modal Confirmação de Eliminação -->
<div class="modal fade" id="modalDeleteTurma" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Tem a certeza que deseja eliminar esta turma?</p>
                <p class="text-muted small mb-0">Esta ação não pode ser revertida.</p>
                <input type="hidden" id="deleteTurmaId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="btnConfirmDeleteTurma">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


