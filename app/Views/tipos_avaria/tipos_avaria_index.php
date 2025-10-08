<?= $this->extend("layout/master") ?>

<?= $this->section("content") ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $page_title ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                        <li class="breadcrumb-item active">Tipos de Avaria</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Main row -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= $page_subtitle ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tipoAvariaModal" onclick="openCreateModal()">
                                    <i class="fas fa-plus"></i> Novo Tipo de Avaria
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="loadStatistics()">
                                    <i class="fas fa-chart-bar"></i> Estatísticas
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="tiposAvariaTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Descrição</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para Tipo de Avaria -->
<div class="modal fade" id="tipoAvariaModal" tabindex="-1" aria-labelledby="tipoAvariaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tipoAvariaModalLabel">Novo Tipo de Avaria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="tipoAvariaForm">
                <div class="modal-body">
                    <input type="hidden" id="tipo_avaria_id" name="tipo_avaria_id">
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição do Tipo de Avaria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="descricao" name="descricao" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="saveButton">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Visualizar Tipo de Avaria -->
<div class="modal fade" id="viewTipoAvariaModal" tabindex="-1" aria-labelledby="viewTipoAvariaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewTipoAvariaModalLabel">Detalhes do Tipo de Avaria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Descrição:</strong>
                    <p id="view_tipo_avaria_descricao"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section("scripts") ?>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $("#tiposAvariaTable").DataTable({
        "processing": true,
        "serverSide": false,
        "ajax": {
            "url": "<?= base_url("tipos_avaria/getDataTable") ?>",
            "type": "POST"
        },
        "columns": [
            { "data": "id" },
            { "data": "descricao" },
            {
                "data": null,
                "orderable": false,
                "render": function(data, type, row) {
                    return "<div class=\"btn-group\" role=\"group\">" +
                           "<button type=\"button\" class=\"btn btn-sm btn-info\" onclick=\"viewTipoAvaria(" + row.id + ")\" title=\"Ver\">" +
                           "<i class=\"fas fa-eye\"></i></button>" +
                           "<button type=\"button\" class=\"btn btn-sm btn-warning\" onclick=\"editTipoAvaria(" + row.id + ")\" title=\"Editar\">" +
                           "<i class=\"fas fa-edit\"></i></button>" +
                           "<button type=\"button\" class=\"btn btn-sm btn-danger\" onclick=\"deleteTipoAvaria(" + row.id + ")\" title=\"Eliminar\">" +
                           "<i class=\"fas fa-trash\"></i></button>" +
                           "</div>";
                }
            }
        ],
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json"
        },
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false
    });

    // Submissão do formulário
    $("#tipoAvariaForm").on("submit", function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var tipoAvariaId = $("#tipo_avaria_id").val();
        var url = tipoAvariaId ? 
            "<?= base_url("tipos_avaria/update") ?>/" + tipoAvariaId : 
            "<?= base_url("tipos_avaria/create") ?>";
        
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $("#tipoAvariaModal").modal("hide");
                table.ajax.reload();
                showToast("success", response.message || "Operação realizada com sucesso!");
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                if (response.messages) {
                    var errors = Object.values(response.messages).join("<br>");
                    showToast("error", errors);
                } else {
                    showToast("error", response.message || "Erro ao processar a solicitação.");
                }
            }
        });
    });
});

function openCreateModal() {
    $("#tipoAvariaModalLabel").text("Novo Tipo de Avaria");
    $("#tipoAvariaForm")[0].reset();
    $("#tipo_avaria_id").val("");
    $("#saveButton").text("Guardar");
}

function editTipoAvaria(id) {
    $.ajax({
        url: "<?= base_url("tipos_avaria/getTipoAvaria") ?>/" + id,
        type: "GET",
        success: function(data) {
            $("#tipoAvariaModalLabel").text("Editar Tipo de Avaria");
            $("#tipo_avaria_id").val(data.id);
            $("#descricao").val(data.descricao);
            $("#saveButton").text("Atualizar");
            $("#tipoAvariaModal").modal("show");
        },
        error: function(xhr) {
            var response = JSON.parse(xhr.responseText);
            showToast("error", response.message || "Erro ao carregar dados do tipo de avaria.");
        }
    });
}

function viewTipoAvaria(id) {
    $.ajax({
        url: "<?= base_url("tipos_avaria/getTipoAvaria") ?>/" + id,
        type: "GET",
        success: function(data) {
            $("#view_tipo_avaria_descricao").text(data.descricao || "Sem descrição");
            $("#viewTipoAvariaModal").modal("show");
        },
        error: function(xhr) {
            var response = JSON.parse(xhr.responseText);
            showToast("error", response.message || "Erro ao carregar dados do tipo de avaria.");
        }
    });
}

function deleteTipoAvaria(id) {
    if (confirm("Tem a certeza que deseja eliminar este tipo de avaria?")) {
        $.ajax({
            url: "<?= base_url("tipos_avaria/delete") ?>/" + id,
            type: "POST",
            success: function(response) {
                $("#tiposAvariaTable").DataTable().ajax.reload();
                showToast("success", response.message || "Tipo de avaria eliminado com sucesso!");
            },
            error: function(xhr) {
                var response = JSON.parse(xhr.responseText);
                showToast("error", response.message || "Erro ao eliminar tipo de avaria.");
            }
        });
    }
}

function loadStatistics() {
    $.ajax({
        url: "<?= base_url("tipos_avaria/getStatistics") ?>",
        type: "GET",
        success: function(data) {
            // Implementar a lógica para exibir as estatísticas dos tipos de avaria
            // Por exemplo, em um modal ou em um card na página
            showToast("info", "Total de Tipos de Avaria: " + data.total_tipos_avaria);
        },
        error: function(xhr) {
            console.error("Erro ao carregar estatísticas de tipos de avaria:", xhr);
        }
    });
}

function showToast(type, message) {
    // Implementar sistema de toast notifications
    // Pode usar Toastr.js ou similar
    if (type === "success" || type === "info") {
        alert("Sucesso: " + message);
    } else {
        alert("Erro: " + message);
    }
}
</script>
<?= $this->endSection() ?>