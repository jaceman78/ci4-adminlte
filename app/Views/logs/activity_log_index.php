<?php
if ((session()->get('LoggedUserData')['level'] ?? 0) != 9) {
    // Redireciona ou mostra mensagem de acesso negado
    echo '<div class="alert alert-danger mt-4">Acesso negado.</div>';
    exit;
}
?>


<?= $this->extend("layout/master") ?>

<?= $this->section("pageHeader") ?>
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0"><?= $title ?></h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <?php foreach ($breadcrumb as $item): ?>
                <?php if (!empty($item["url"])): ?>
                    <li class="breadcrumb-item"><a href="<?= $item["url"] ?>"><?= $item["name"] ?></a></li>
                <?php else: ?>
                    <li class="breadcrumb-item active"><?= $item["name"] ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section("content") ?>
<div class="row">
    <div class="col-12">
        <!-- Card de Filtros -->
        <div class="card card-outline card-primary mb-3">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-filter"></i>
                    Filtros de Pesquisa
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filterUser">Utilizador</label>
                                <select class="form-control select2" id="filterUser" name="filter_user_id">
                                    <option value="">Todos os utilizadores</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="filterModulo">Módulo</label>
                                <select class="form-control" id="filterModulo" name="filter_modulo">
                                    <option value="">Todos os módulos</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="filterAcao">Ação</label>
                                <select class="form-control" id="filterAcao" name="filter_acao">
                                    <option value="">Todas as ações</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="filterDataInicio">Data Início</label>
                                <input type="date" class="form-control" id="filterDataInicio" name="filter_data_inicio">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="filterDataFim">Data Fim</label>
                                <input type="date" class="form-control" id="filterDataFim" name="filter_data_fim">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="button" class="btn btn-primary btn-block" onclick="applyFilters()">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="clearFilters()">
                                <i class="fas fa-times"></i> Limpar Filtros
                            </button>
                            <button type="button" class="btn btn-info btn-sm" onclick="showStats()">
                                <i class="fas fa-chart-bar"></i> Estatísticas
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="exportLogs()">
                                <i class="fas fa-download"></i> Exportar CSV
                            </button>
                            <?php if (session()->get("level") >= 9): ?>
                            <button type="button" class="btn btn-warning btn-sm" onclick="showCleanLogsModal()">
                                <i class="fas fa-broom"></i> Limpar Logs Antigos
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card Principal -->
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Logs de Atividade
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" onclick="refreshTable()">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="logsTable" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="5%">ID</th>
                                <th width="15%">Utilizador</th>
                                <th width="10%">Módulo</th>
                                <th width="10%">Ação</th>
                                <th width="35%">Descrição</th>
                                <th width="10%">IP</th>
                                <th width="10%">Data/Hora</th>
                                <th width="5%">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dados carregados via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Visualização de Log -->
<div class="modal fade" id="viewLogModal" tabindex="-1" aria-labelledby="viewLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="viewLogModalLabel">
                    <i class="fas fa-eye"></i> Detalhes do Log
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>Informações Básicas</strong></h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td id="viewLogId"></td>
                            </tr>
                            <tr>
                                <td><strong>Utilizador:</strong></td>
                                <td id="viewLogUser"></td>
                            </tr>
                            <tr>
                                <td><strong>Módulo:</strong></td>
                                <td id="viewLogModulo"></td>
                            </tr>
                            <tr>
                                <td><strong>Ação:</strong></td>
                                <td id="viewLogAcao"></td>
                            </tr>
                            <tr>
                                <td><strong>Registo ID:</strong></td>
                                <td id="viewLogRegistroId"></td>
                            </tr>
                            <tr>
                                <td><strong>Data/Hora:</strong></td>
                                <td id="viewLogDataHora"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><strong>Informações Técnicas</strong></h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>IP Address:</strong></td>
                                <td id="viewLogIp"></td>
                            </tr>
                            <tr>
                                <td><strong>User Agent:</strong></td>
                                <td id="viewLogUserAgent" style="word-break: break-all;"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><strong>Descrição</strong></h6>
                        <div class="alert alert-info" id="viewLogDescricao"></div>
                    </div>
                </div>

                <!-- Dados Anteriores -->
                <div class="row mt-3" id="dadosAnterioresSection" style="display: none;">
                    <div class="col-12">
                        <h6><strong>Dados Anteriores</strong></h6>
                        <pre id="viewLogDadosAnteriores" class="bg-light p-3" style="max-height: 200px; overflow-y: auto;"></pre>
                    </div>
                </div>

                <!-- Dados Novos -->
                <div class="row mt-3" id="dadosNovosSection" style="display: none;">
                    <div class="col-12">
                        <h6><strong>Dados Novos</strong></h6>
                        <pre id="viewLogDadosNovos" class="bg-light p-3" style="max-height: 200px; overflow-y: auto;"></pre>
                    </div>
                </div>

                <!-- Detalhes Adicionais -->
                <div class="row mt-3" id="detalhesSection" style="display: none;">
                    <div class="col-12">
                        <h6><strong>Detalhes Adicionais</strong></h6>
                        <pre id="viewLogDetalhes" class="bg-light p-3" style="max-height: 200px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Estatísticas -->
<div class="modal fade" id="statsModal" tabindex="-1" aria-labelledby="statsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title" id="statsModalLabel">
                    <i class="fas fa-chart-bar"></i> Estatísticas dos Logs
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div id="statsContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p>Carregando estatísticas...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Limpeza de Logs -->
<?php if (session()->get("level") >= 9): ?>
<div class="modal fade" id="cleanLogsModal" tabindex="-1" aria-labelledby="cleanLogsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="cleanLogsModalLabel">
                    <i class="fas fa-broom"></i> Limpar Logs Antigos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <form id="cleanLogsForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atenção!</strong> Esta ação irá eliminar permanentemente todos os logs com mais de X dias. Esta operação não pode ser desfeita.
                    </div>
                    <div class="form-group">
                        <label for="cleanLogsDays">Eliminar logs com mais de quantos dias?</label>
                        <select class="form-control" id="cleanLogsDays" name="days" required>
                            <option value="30">30 dias</option>
                            <option value="60">60 dias</option>
                            <option value="90" selected>90 dias</option>
                            <option value="180">180 dias</option>
                            <option value="365">1 ano</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-broom"></i> Limpar Logs
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
<?= $this->section("scripts") ?>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $("#logsTable").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url("logs/getDataTable") ?>",
            type: "POST",
            data: function(d) {
                // Adicionar filtros aos dados enviados
                d.filter_user_id = $("#filterUser").val();
                d.filter_modulo = $("#filterModulo").val();
                d.filter_acao = $("#filterAcao").val();
                d.filter_data_inicio = $("#filterDataInicio").val();
                d.filter_data_fim = $("#filterDataFim").val();
                
                // Adicionar cabeçalho AJAX
                d["X-Requested-With"] = "XMLHttpRequest";
                return d;
            }
        },
        columns: [
            { data: 0, name: "id" },
            { data: 1, name: "user_name", orderable: false },
            { data: 2, name: "modulo" },
            { data: 3, name: "acao" },
            { data: 4, name: "descricao", orderable: false },
            { data: 5, name: "ip_address", orderable: false },
            { data: 6, name: "criado_em" },
            { data: 7, name: "actions", orderable: false, searchable: false }
        ],
        order: [[6, "desc"]], // Ordenar por data/hora decrescente
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        responsive: true,
        autoWidth: false,
        dom: "Bfrtip",
        buttons: [
            {
                extend: "pageLength",
                text: "<i class=\"fas fa-list\"></i> Mostrar",
                className: "btn btn-secondary btn-sm"
            },
            {
                extend: "colvis",
                text: "<i class=\"fas fa-columns\"></i> Colunas",
                className: "btn btn-secondary btn-sm"
            }
        ]
    });

    // Carregar dados para filtros
    loadFilterData();

    // Inicializar Select2 para utilizadores
    $("#filterUser").select2({
        placeholder: "Selecione um utilizador",
        allowClear: true,
        width: "100%"
    });

    // Form de limpeza de logs
    <?php if (session()->get("level") >= 9): ?>
    $("#cleanLogsForm").on("submit", function(e) {
        e.preventDefault();
        
        if (!confirm("Tem a certeza que deseja eliminar os logs antigos? Esta ação não pode ser desfeita.")) {
            return;
        }
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: "<?= base_url("logs/cleanOldLogs") ?>",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    showToast("success", response.message);
                    $("#cleanLogsModal").modal("hide");
                    table.ajax.reload();
                } else {
                    showToast("error", response.message);
                }
            },
            error: function(xhr) {
                showToast("error", "Erro ao processar pedido");
                console.error(xhr.responseText);
            }
        });
    });
    <?php endif; ?>
});

// Carregar dados para filtros
function loadFilterData() {
    $.ajax({
        url: "<?= base_url("logs/getFilterData") ?>",
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.success) {
                // Carregar utilizadores
                var userSelect = $("#filterUser");
                userSelect.empty().append("<option value=\"\">Todos os utilizadores</option>");
                $.each(response.data.users, function(index, user) {
                    userSelect.append("<option value=\"" + user.id + "\">" + user.name + " (" + user.email + ")</option>");
                });

                // Carregar módulos
                var moduloSelect = $("#filterModulo");
                moduloSelect.empty().append("<option value=\"\">Todos os módulos</option>");
                $.each(response.data.modules, function(index, modulo) {
                    moduloSelect.append("<option value=\"" + modulo + "\">" + modulo.charAt(0).toUpperCase() + modulo.slice(1) + "</option>");
                });

                // Carregar ações
                var acaoSelect = $("#filterAcao");
                acaoSelect.empty().append("<option value=\"\">Todas as ações</option>");
                $.each(response.data.actions, function(index, acao) {
                    acaoSelect.append("<option value=\"" + acao + "\">" + acao.charAt(0).toUpperCase() + acao.slice(1) + "</option>");
                });
            }
        },
        error: function(xhr) {
            console.error("Erro ao carregar dados dos filtros:", xhr.responseText);
        }
    });
}

// Aplicar filtros
function applyFilters() {
    $("#logsTable").DataTable().ajax.reload();
}

// Limpar filtros
function clearFilters() {
    $("#filterForm")[0].reset();
    $("#filterUser").val(null).trigger("change");
    $("#logsTable").DataTable().ajax.reload();
}

// Atualizar tabela
function refreshTable() {
    $("#logsTable").DataTable().ajax.reload();
    showToast("info", "Tabela atualizada");
}

// Visualizar log
function viewLog(id) {
    $.ajax({
        url: "<?= base_url("logs/getLog") ?>/" + id,
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.success) {
                var log = response.data;
                
                // Preencher dados básicos
                $("#viewLogId").text(log.id);
$("#viewLogUser").html(log.user_name ? 
    "<strong>" + log.user_name + "</strong> <small>" + (log.user_email || "") + "</small>" : 
    "<span class=\"text-muted\">Sistema</span>"
);
                $("#viewLogModulo").html("<span class=\"badge bg-primary\">" + log.modulo.charAt(0).toUpperCase() + log.modulo.slice(1) + "</span>");
                $("#viewLogAcao").html("<span class=\"badge bg-info\">" + log.acao.charAt(0).toUpperCase() + log.acao.slice(1) + "</span>");
                $("#viewLogRegistroId").text(log.registro_id || "N/A");
                $("#viewLogDataHora").text(new Date(log.criado_em).toLocaleString("pt-PT"));
                $("#viewLogIp").text(log.ip_address || "N/A");
                $("#viewLogUserAgent").text(log.user_agent || "N/A");
                $("#viewLogDescricao").text(log.descricao);

                // Mostrar/ocultar seções condicionalmente
                if (log.dados_anteriores) {
                    $("#viewLogDadosAnteriores").text(JSON.stringify(log.dados_anteriores, null, 2));
                    $("#dadosAnterioresSection").show();
                } else {
                    $("#dadosAnterioresSection").hide();
                }

                if (log.dados_novos) {
                    $("#viewLogDadosNovos").text(JSON.stringify(log.dados_novos, null, 2));
                    $("#dadosNovosSection").show();
                } else {
                    $("#dadosNovosSection").hide();
                }

                if (log.detalhes) {
                    $("#viewLogDetalhes").text(JSON.stringify(log.detalhes, null, 2));
                    $("#detalhesSection").show();
                } else {
                    $("#detalhesSection").hide();
                }

                $("#viewLogModal").modal("show");
            } else {
                showToast("error", response.message || "Erro ao carregar log");
            }
        },
        error: function(xhr) {
            showToast("error", "Erro ao processar pedido");
            console.error(xhr.responseText);
        }
    });
}

// Eliminar log
function deleteLog(id) {
    if (!confirm("Tem a certeza que deseja eliminar este log? Esta ação não pode ser desfeita.")) {
        return;
    }

    $.ajax({
        url: "<?= base_url("logs/delete") ?>/" + id,
        type: "POST",
        dataType: "json",
        success: function(response) {
            if (response.success) {
                showToast("success", response.message);
                $("#logsTable").DataTable().ajax.reload();
            } else {
                showToast("error", response.message);
            }
        },
        error: function(xhr) {
            showToast("error", "Erro ao processar pedido");
            console.error(xhr.responseText);
        }
    });
}

// Mostrar estatísticas
function showStats() {
    $("#statsModal").modal("show");
    
    $.ajax({
        url: "<?= base_url("logs/getStats") ?>",
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.success) {
                var stats = response.data;
                var html = "<div class=\"row\">";
                
                // Estatísticas gerais
                html += "<div class=\"col-md-6\">";
                html += "<h6><strong>Estatísticas Gerais</strong></h6>";
                html += "<table class=\"table table-sm\">";
                html += "<tr><td>Total de Logs:</td><td><strong>" + stats.total_logs + "</strong></td></tr>";
                html += "<tr><td>Logs Hoje:</td><td><strong>" + stats.logs_today + "</strong></td></tr>";
                html += "<tr><td>Logs Esta Semana:</td><td><strong>" + stats.logs_this_week + "</strong></td></tr>";
                html += "<tr><td>Logs Este Mês:</td><td><strong>" + stats.logs_this_month + "</strong></td></tr>";
                html += "</table>";
                html += "</div>";

                // Por módulo
                html += "<div class=\"col-md-6\">";
                html += "<h6><strong>Por Módulo</strong></h6>";
                html += "<table class=\"table table-sm\">";
                $.each(stats.by_module, function(modulo, count) {
                    html += "<tr><td>" + modulo.charAt(0).toUpperCase() + modulo.slice(1) + ":</td><td><strong>" + count + "</strong></td></tr>";
                });
                html += "</table>";
                html += "</div>";

                html += "</div><div class=\"row mt-3\">";

                // Por ação
                html += "<div class=\"col-md-6\">";
                html += "<h6><strong>Por Ação</strong></h6>";
                html += "<table class=\"table table-sm\">";
                $.each(stats.by_action, function(acao, count) {
                    html += "<tr><td>" + acao.charAt(0).toUpperCase() + acao.slice(1) + ":</td><td><strong>" + count + "</strong></td></tr>";
                });
                html += "</table>";
                html += "</div>";

                // Utilizadores mais ativos
                html += "<div class=\"col-md-6\">";
                html += "<h6><strong>Utilizadores Mais Ativos</strong></h6>";
                html += "<table class=\"table table-sm\">";
                $.each(stats.top_users, function(index, user) {
                    html += "<tr><td>" + (user.user_name || "Sistema") + ":</td><td><strong>" + user.count + "</strong></td></tr>";
                });
                html += "</table>";
                html += "</div>";

                html += "</div>";

                $("#statsContent").html(html);
            } else {
                $("#statsContent").html("<div class=\"alert alert-danger\">Erro ao carregar estatísticas</div>");
            }
        },
        error: function(xhr) {
            $("#statsContent").html("<div class=\"alert alert-danger\">Erro ao processar pedido</div>");
            console.error(xhr.responseText);
        }
    });
}

// Exportar logs
function exportLogs() {
    var params = new URLSearchParams();
    
    // Adicionar filtros ativos
    if ($("#filterUser").val()) params.append("user_id", $("#filterUser").val());
    if ($("#filterModulo").val()) params.append("modulo", $("#filterModulo").val());
    if ($("#filterAcao").val()) params.append("acao", $("#filterAcao").val());
    if ($("#filterDataInicio").val()) params.append("data_inicio", $("#filterDataInicio").val());
    if ($("#filterDataFim").val()) params.append("data_fim", $("#filterDataFim").val());
    
    var url = "<?= base_url("logs/exportCSV") ?>";
    if (params.toString()) {
        url += "?" + params.toString();
    }
    
    window.open(url, "_blank");
    showToast("info", "Exportação iniciada");
}

// Mostrar modal de limpeza de logs
<?php if (session()->get("level") >= 9): ?>
function showCleanLogsModal() {
    $("#cleanLogsModal").modal("show");
}
<?php endif; ?>

// Função para mostrar toasts
function showToast(type, message) {
    // Implementar conforme o seu sistema de toasts
    // Esta é uma implementação básica
    var alertClass = "alert-info";
    switch(type) {
        case "success": alertClass = "alert-success"; break;
        case "error": alertClass = "alert-danger"; break;
        case "warning": alertClass = "alert-warning"; break;
    }
    
    var toast = $("<div class=\"alert " + alertClass + " alert-dismissible fade show\" role=\"alert\">" +
                  message +
                  "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">" +
                  "<span aria-hidden=\"true\">&times;</span>" +
                  "</button>" +
                  "</div>");
    
    $("body").append(toast);
    
    setTimeout(function() {
        toast.alert("close");
    }, 5000);
}
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>