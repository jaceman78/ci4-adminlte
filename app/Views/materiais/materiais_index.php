<?= $this->extend('layout/master') ?>

<?= $this->section('pageHeader') ?>
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Gestão de Materiais</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Materiais</li>
        </ol>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Lista de Materiais</h3>
        <div class="card-tools">
            <!-- Botão Adicionar Material -->
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#materialModal" onclick="clearForm()">
                <i class="fas fa-plus"></i> Adicionar Material
            </button>
            <button type="button" class="btn btn-info btn-sm" onclick="getStats()" id="statsBtn">
                <i class="fas fa-chart-bar"></i> Estatísticas
            </button>
        </div>
    </div>
    <div class="card-body">
        <table id="materiaisTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Referência</th>
                    <th>Stock Atual</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dados serão carregados via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar/Editar Material -->
<div class="modal fade" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="materialModalLabel">Adicionar/Editar Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="materialForm">
                <div class="modal-body">
                    <input type="hidden" id="materialId" name="id">
                    <div class="form-group">
                        <label for="nome">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="referencia">Referência</label>
                        <input type="text" class="form-control" id="referencia" name="referencia">
                    </div>
                    <div class="form-group">
                        <label for="stock_atual">Stock Atual</label>
                        <input type="number" class="form-control" id="stock_atual" name="stock_atual" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal para Estatísticas -->
<div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statsModalLabel">Estatísticas dos Materiais</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-boxes"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total de Materiais</span>
                                <span class="info-box-number" id="statsTotalMateriais">0</span>
                            </div>
                        </div>
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Materiais com Stock Baixo</span>
                                <span class="info-box-number" id="statsStockBaixo">0</span>
                            </div>
                        </div>
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Materiais Sem Stock</span>
                                <span class="info-box-number" id="statsSemStock">0</span>
                            </div>
                        </div>
                        <div class="info-box mb-3">
                            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-calendar-plus"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Adicionados nos Últimos 30 Dias</span>
                                <span class="info-box-number" id="statsAdicionados30Dias">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>



<?= $this->section('scripts') ?>
<!-- DataTables e Toastr JS --> 

<script>
   

    $(function () {
        // Inicialização do DataTables
       var  materiaisTable = $("#materiaisTable").DataTable({
            'responsive': true,
            'lengthChange': false,
            'autoWidth': false,
            'processing': true,
            'serverSide': true,
            'ajax': {
                'url': "<?= base_url('materiais/getDataTable') ?>",
                'type': 'POST'
            },
            'columns': [
                { 'data': 0, 'name': 'id' },
                { 'data': 1, 'name': 'nome' },
                { 'data': 2, 'name': 'referencia' },
                { 'data': 3, 'name': 'stock_atual' },
                { 'data': 4, 'name': 'actions', 'orderable': false, 'searchable': false }
            ],
            'language': {
               'url': 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
            },
            'columnDefs': [
                {
                    'targets': -1,
                    'data': null,
                    'render': function (data, type, row) {
                        return `
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-primary" onclick="editMaterial(${row[0]})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteMaterial(${row[0]})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });

        // Submissão do formulário de material
        $("#materialForm").submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: "<?= base_url('materiais/save') ?>",
                type: "POST",
                data: formData,
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.message);
                        $("#materialModal").modal("hide");
                        clearForm();
                        materiaisTable.ajax.reload();
                    } else {
                        showToast('error', "Erro: " + Object.values(response.errors).join("<br>"));
                    }
                },

            });
        });
    });

    function clearForm() {
        $("#materialForm")[0].reset();
        $("#materialId").val("");
        $("#materialModalLabel").text("Adicionar Material");
    }

    function editMaterial(id) {
        clearForm();
        $.ajax({
            url: "<?= base_url('materiais/getMaterial/') ?>" + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $("#materialId").val(response.data.id);
                    $("#nome").val(response.data.nome);
                    $("#referencia").val(response.data.referencia);
                    $("#stock_atual").val(response.data.stock_atual);
                    $("#materialModalLabel").text("Editar Material");
                    $("#materialModal").modal("show");
                } else {
                    toastr.error("Erro: " + response.error);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Erro ao carregar material para edição: " + xhr.responseText);
            }
        });
    }

    function deleteMaterial(id) {
        if (confirm("Tem certeza que deseja eliminar este material?")) {
            $.ajax({
                url: "<?= base_url('materiais/delete/') ?>" + id,
                type: "POST",
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        materiaisTable.ajax.reload();
                    } else {
                        toastr.error("Erro: " + response.message);
                    }
                },
     
            });
        }
    }

    function getStats() {
        $.ajax({
            url: "<?= base_url('materiais/getStats') ?>",
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    $("#statsTotalMateriais").text(response.data.total);
                    $("#statsStockBaixo").text(response.data.stock_baixo);
                    $("#statsSemStock").text(response.data.sem_stock);
                    $("#statsAdicionados30Dias").text(response.data.adicionados_30dias);
                    $("#statsModal").modal("show");
                } else {
                    toastr.error("Erro ao carregar estatísticas: " + response.error);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Erro ao carregar estatísticas: " + xhr.responseText);
            }
        });
    }
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>