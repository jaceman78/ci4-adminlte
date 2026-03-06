<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>
<?= $this->section('content') ?>
<style>
#sessaoModal .modal-header {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0"><?= esc($title) ?></h1></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Sessões de Exame</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Gestão de Sessões de Exame</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary" onclick="novaSessao()">
                        <i class="bi bi-plus-circle"></i> Nova Sessão
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="sessoesTable" class="table table-bordered table-striped table-hover nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th class="d-none">ID</th>
                            <th>Código</th>
                            <th>Exame</th>
                            <th>Fase</th>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Duração</th>
                            <th>Convocatórias</th>
                            <th>Estado</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="sessaoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sessaoModalLabel">Nova Sessão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sessaoForm">
                <div class="modal-body">
                    <input type="hidden" id="sessaoId" name="id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="exameId" class="form-label">Exame <span class="text-danger">*</span></label>
                            <select class="form-select" id="exameId" name="exame_id" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($exames as $exame): ?>
                                    <option value="<?= $exame['id'] ?>" data-tipo="<?= $exame['tipo_prova'] ?>"><?= $exame['codigo_prova'] ?> - <?= $exame['nome_prova'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fase" class="form-label">Fase <span class="text-danger">*</span></label>
                            <select class="form-select" id="fase" name="fase" required>
                                <option value="">Selecione...</option>
                                <option value="1ª Fase">1ª Fase</option>
                                <option value="2ª Fase">2ª Fase</option>
                                <option value="Prova Ensaio">Prova Ensaio</option>
                                <option value="Oral">Oral</option>
                                <option value="Época Especial">Época Especial</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="dataExame" class="form-label">Data <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="dataExame" name="data_exame" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="horaExame" class="form-label">Hora <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="horaExame" name="hora_exame" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="duracaoMinutos" class="form-label">Duração (min) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="duracaoMinutos" name="duracao_minutos" min="30" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="toleranciaMinutos" class="form-label">Tolerância (min)</label>
                            <input type="number" class="form-control" id="toleranciaMinutos" name="tolerancia_minutos" value="0" min="0">
                        </div>
                        <div class="col-md-6 mb-3" id="numAlunosContainer">
                            <label for="numAlunos" class="form-label">Nº de Alunos</label>
                            <input type="number" class="form-control" id="numAlunos" name="num_alunos" min="1">
                            <small class="text-muted">Deixe em branco para exames de suplentes</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardar"><i class="bi bi-save"></i> Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Detectar mudança de exame para ajustar interface
    $('#exameId').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const tipoProva = selectedOption.data('tipo');
        
        // Sessões especiais não requerem número de alunos
        const tiposEspeciais = ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC'];
        if (tiposEspeciais.includes(tipoProva)) {
            $('#numAlunos').val('').prop('disabled', true);
            const nomesTipos = {
                'Suplentes': 'Suplentes',
                'Verificacao Calculadoras': 'Verificação de Calculadoras',
                'Apoio TIC': 'Apoio TIC'
            };
            $('#numAlunosContainer small').html(`<span class="badge bg-info">Não aplicável para ${nomesTipos[tipoProva]}</span>`);
        } else {
            $('#numAlunos').prop('disabled', false);
            $('#numAlunosContainer small').html('Deixe em branco para exames de suplentes');
        }
    });


    var table = $('#sessoesTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '<?= base_url('sessoes-exame/getDataTable') ?>', 
            type: 'POST',
            dataSrc: function(json) {
                console.log('Dados recebidos:', json);
                if (json.data && json.data.length > 0) {
                    console.log('Primeira linha:', json.data[0]);
                }
                return json.data;
            }
        },
        columns: [
            {data: 0}, {data: 1}, {data: 2}, {data: 3}, {data: 4}, 
            {data: 5}, {data: 6}, {data: 7}, {data: 8}, {data: 9, orderable: false, searchable: false}
        ],
        columnDefs: [
            { targets: 0, visible: false, searchable: false }
        ],
        language: {url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'},
        order: [[4, 'desc']]
    });

    $('#sessaoForm').on('submit', function(e) {
        e.preventDefault();
        const sessaoId = $('#sessaoId').val();
        const url = sessaoId ? '<?= base_url('sessoes-exame/update') ?>/' + sessaoId : '<?= base_url('sessoes-exame/store') ?>';
        $('#btnGuardar').prop('disabled', true);
        $.ajax({
            url: url, type: 'POST', data: new FormData(this), processData: false, contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#sessaoModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Sucesso!', response.message, 'success');
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            complete: function() { $('#btnGuardar').prop('disabled', false); }
        });
    });
});

function novaSessao() {
    $('#sessaoForm')[0].reset();
    $('#sessaoId').val('');
    $('#sessaoModalLabel').text('Nova Sessão');
    $('#sessaoModal').modal('show');
}

function editSessao(id) {
    $.ajax({
        url: '<?= base_url('sessoes-exame/get') ?>/' + id,
        success: function(response) {
            if (response.success) {
                const s = response.data;
                $('#sessaoId').val(s.id);
                $('#exameId').val(s.exame_id);
                $('#fase').val(s.fase);
                $('#dataExame').val(s.data_exame);
                $('#horaExame').val(s.hora_exame);
                $('#duracaoMinutos').val(s.duracao_minutos);
                $('#toleranciaMinutos').val(s.tolerancia_minutos);
                $('#numAlunos').val(s.num_alunos);
                $('#observacoes').val(s.observacoes);
                $('#sessaoModalLabel').text('Editar Sessão');
                $('#sessaoModal').modal('show');
            }
        }
    });
}

function deleteSessao(id) {
    Swal.fire({
        title: 'Tem a certeza?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, eliminar!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('sessoes-exame/delete') ?>/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        $('#sessoesTable').DataTable().ajax.reload();
                        Swal.fire('Eliminado!', response.message, 'success');
                    }
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
