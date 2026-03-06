<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>Reparações Externas<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$userLevel = session()->get('LoggedUserData')['level'] ?? 0;
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="bi bi-tools"></i> Reparações Externas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('kit-digital-admin') ?>">Kit Digital</a></li>
                    <li class="breadcrumb-item active">Reparações Externas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-lg-2 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="stat_total"><?= $stats['total'] ?></h3>
                        <p>Total de Reparações</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="stat_em_reparacao"><?= $stats['em_reparacao'] ?></h3>
                        <p>Em Reparação</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="stat_reparado"><?= $stats['reparado'] ?></h3>
                        <p>Reparados</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="stat_irreparavel"><?= $stats['irreparavel'] ?></h3>
                        <p>Irreparável</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3 id="stat_custo"><?= number_format($stats['custo_total'], 2, ',', '.') ?>€</h3>
                        <p>Custo Total</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Ações -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" id="btnNovaReparacao">
                <i class="bi bi-plus-circle"></i> Nova Reparação
            </button>
            <div class="btn-group ms-2" role="group">
                <button type="button" class="btn btn-outline-success" id="btnImportar">
                    <i class="bi bi-upload"></i> Importar CSV
                </button>
                <button type="button" class="btn btn-outline-info" id="btnExportar">
                    <i class="bi bi-download"></i> Exportar CSV
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btnTemplate">
                    <i class="bi bi-file-earmark-text"></i> Download Template
                </button>
            </div>
            <button type="button" class="btn btn-outline-primary ms-2" id="btnEstatisticas">
                <i class="bi bi-bar-chart"></i> Estatísticas
            </button>
        </div>

        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <label class="form-label">Filtrar por Estado:</label>
                        <select class="form-select" id="filtroEstado">
                            <option value="">Todos</option>
                            <option value="enviado">Enviado</option>
                            <option value="em_reparacao">Em Reparação</option>
                            <option value="reparado">Reparado</option>
                            <option value="irreparavel">Irreparável</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Filtrar por Tipologia:</label>
                        <select class="form-select" id="filtroTipologia">
                            <option value="">Todas</option>
                            <option value="Tipo I">Tipo I</option>
                            <option value="Tipo II">Tipo II</option>
                            <option value="Tipo III">Tipo III</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Filtrar por Avaria:</label>
                        <select class="form-select" id="filtroAvaria">
                            <option value="">Todas</option>
                            <option value="Teclado">Teclado</option>
                            <option value="Monitor">Monitor</option>
                            <option value="Bateria">Bateria</option>
                            <option value="Disco">Disco</option>
                            <option value="Sistema Operativo">Sistema Operativo</option>
                            <option value="CUCo">CUCo</option>
                            <option value="Gráfica">Gráfica</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Filtrar por Empresa:</label>
                        <select class="form-select" id="filtroEmpresa">
                            <option value="">Todas</option>
                            <?php if (!empty($empresas)): ?>
                                <?php foreach ($empresas as $empresa): ?>
                                    <option value="<?= esc($empresa['empresa_nome']) ?>"><?= esc($empresa['empresa_nome']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-secondary w-100" id="btnLimparFiltros">
                            <i class="bi bi-x-circle"></i> Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Reparações</h3>
            </div>
            <div class="card-body">
                <table id="reparacoesTable" class="table table-bordered table-hover table-sm nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Nº Série</th>
                            <th>Tipologia</th>
                            <th>Avaria</th>
                            <th>Data Envio</th>
                            <th>Empresa</th>
                            <th>Custo (€)</th>
                            <th>Estado</th>
                            <th>Dias</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<!-- Modal Nova/Editar Reparação -->
<div class="modal fade" id="modalReparacao" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formReparacao">
                <input type="hidden" id="reparacao_id" name="reparacao_id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalReparacaoTitle">Nova Reparação Externa</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Coluna 1 -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="n_serie_equipamento" class="form-label">Nº Série do Equipamento <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="n_serie_equipamento" name="n_serie_equipamento" required>
                            </div>

                            <div class="mb-3">
                                <label for="tipologia" class="form-label">Tipologia <span class="text-danger">*</span></label>
                                <select class="form-select" id="tipologia" name="tipologia" required>
                                    <option value="">Selecione...</option>
                                    <option value="Tipo I">Tipo I</option>
                                    <option value="Tipo II">Tipo II</option>
                                    <option value="Tipo III">Tipo III</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="possivel_avaria" class="form-label">Tipo de Avaria <span class="text-danger">*</span></label>
                                <select class="form-select" id="possivel_avaria" name="possivel_avaria" required>
                                    <option value="">Selecione...</option>
                                    <option value="Teclado">Teclado</option>
                                    <option value="Monitor">Monitor</option>
                                    <option value="Bateria">Bateria</option>
                                    <option value="Disco">Disco</option>
                                    <option value="Sistema Operativo">Sistema Operativo</option>
                                    <option value="CUCo">CUCo</option>
                                    <option value="Gráfica">Gráfica</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="descricao_avaria" class="form-label">Descrição da Avaria</label>
                                <textarea class="form-control" id="descricao_avaria" name="descricao_avaria" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="data_envio" class="form-label">Data de Envio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="data_envio" name="data_envio" required>
                            </div>

                            <div class="mb-3">
                                <label for="empresa_reparacao" class="form-label">Empresa de Reparação <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="empresa_reparacao" name="empresa_reparacao" list="empresas_list" placeholder="Selecione ou digite o nome da empresa" required>
                                <datalist id="empresas_list">
                                    <?php if (!empty($empresas)): ?>
                                        <?php foreach ($empresas as $empresa): ?>
                                            <option value="<?= esc($empresa['empresa_nome']) ?>">
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </datalist>
                                <small class="text-muted">Escolha da lista ou digite um novo nome</small>
                            </div>
                        </div>

                        <!-- Coluna 2 -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="n_guia" class="form-label">Nº de Guia/RMA</label>
                                <input type="text" class="form-control" id="n_guia" name="n_guia">
                            </div>

                            <div class="mb-3">
                                <label for="trabalho_efetuado" class="form-label">Trabalho Efetuado</label>
                                <textarea class="form-control" id="trabalho_efetuado" name="trabalho_efetuado" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="custo" class="form-label">Custo (€)</label>
                                <input type="number" class="form-control" id="custo" name="custo" step="0.01" min="0">
                            </div>

                            <div class="mb-3">
                                <label for="data_recepcao" class="form-label">Data de Receção</label>
                                <input type="date" class="form-control" id="data_recepcao" name="data_recepcao">
                            </div>

                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="enviado">Enviado</option>
                                    <option value="em_reparacao">Em Reparação</option>
                                    <option value="reparado">Reparado</option>
                                    <option value="irreparavel">Irreparável</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i> <strong>Nota:</strong> Os campos marcados com <span class="text-danger">*</span> são obrigatórios.
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

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detalhes da Reparação</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detalhesContent">
                <p class="text-center"><i class="bi bi-hourglass-split"></i> A carregar...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Importação CSV -->
<div class="modal fade" id="modalImportacao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="formImportacao" enctype="multipart/form-data">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Importar CSV</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Formato do Ficheiro CSV</h6>
                        <p class="mb-1"><strong>Colunas necessárias (por ordem):</strong></p>
                        <ol class="mb-0" style="font-size: 0.9em;">
                            <li>Nº Série</li>
                            <li>Tipologia (Tipo I, Tipo II ou Tipo III)</li>
                            <li>Tipo Avaria (Teclado, Monitor, Bateria, etc.)</li>
                            <li>Descrição Avaria</li>
                            <li>Data Envio (YYYY-MM-DD)</li>
                            <li>Empresa Reparação</li>
                            <li>Nº Guia</li>
                            <li>Trabalho Efetuado</li>
                            <li>Custo (número decimal, ex: 45.50)</li>
                            <li>Data Receção (YYYY-MM-DD ou vazio)</li>
                            <li>Observações</li>
                            <li>Estado (enviado, em_reparacao, reparado, etc.)</li>
                        </ol>
                        <p class="mt-2 mb-0"><small>Use o botão "Download Template" para obter um exemplo.</small></p>
                    </div>

                    <div class="mb-3">
                        <label for="csv_file" class="form-label">Selecione o ficheiro CSV:</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload"></i> Importar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Estatísticas -->
<div class="modal fade" id="modalEstatisticas" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-bar-chart"></i> Estatísticas Detalhadas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Por Tipologia</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartTipologia"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Por Tipo de Avaria</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartAvaria"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Resumo Geral</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h3 id="stat_detail_total">0</h3>
                                        <p>Total de Reparações</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h3 id="stat_detail_custo">0€</h3>
                                        <p>Custo Total</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h3 id="stat_detail_tempo">0</h3>
                                        <p>Tempo Médio (dias)</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h3 id="stat_detail_taxa">0%</h3>
                                        <p>Taxa de Sucesso</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    let dataTable;
    let chartTipologia, chartAvaria;

    // Inicializar DataTable
    function initDataTable() {
        dataTable = $('#reparacoesTable').DataTable({
            responsive: true,
            ajax: {
                url: '<?= base_url('reparacoes-externas/getData') ?>',
                dataSrc: 'data'
            },
            columns: [
                { data: 'n_serie_equipamento' },
                { data: 'tipologia' },
                { data: 'possivel_avaria' },
                { 
                    data: 'data_envio',
                    render: function(data) {
                        if (!data) return '-';
                        const date = new Date(data);
                        return date.toLocaleDateString('pt-PT');
                    }
                },
                { 
                    data: 'empresa_reparacao',
                    render: function(data) {
                        return data || '-';
                    }
                },
                { 
                    data: 'custo',
                    render: function(data) {
                        return data ? parseFloat(data).toFixed(2) + '€' : '-';
                    }
                },
                { 
                    data: 'estado',
                    render: function(data) {
                        const badges = {
                            'enviado': '<span class="badge bg-primary">Enviado</span>',
                            'em_reparacao': '<span class="badge bg-warning">Em Reparação</span>',
                            'reparado': '<span class="badge bg-success">Reparado</span>',
                            'irreparavel': '<span class="badge bg-danger">Irreparável</span>',
                            'cancelado': '<span class="badge bg-secondary">Cancelado</span>'
                        };
                        return badges[data] || data;
                    }
                },
                { 
                    data: 'dias_reparacao',
                    render: function(data) {
                        return data !== null ? data + ' dias' : '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-info btn-sm btn-view" data-id="${row.id_reparacao}" title="Ver Detalhes">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-warning btn-sm btn-edit" data-id="${row.id_reparacao}" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm btn-delete" data-id="${row.id_reparacao}" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            order: [[0, 'desc']],
            language: {
                "sEmptyTable": "Sem dados disponíveis",
                "sInfo": "Mostrando _START_ a _END_ de _TOTAL_ registos",
                "sInfoEmpty": "Mostrando 0 a 0 de 0 registos",
                "sInfoFiltered": "(filtrado de _MAX_ registos no total)",
                "sInfoPostFix": "",
                "sInfoThousands": ".",
                "sLengthMenu": "Mostrar _MENU_ registos",
                "sLoadingRecords": "A carregar...",
                "sProcessing": "A processar...",
                "sSearch": "Pesquisar:",
                "sZeroRecords": "Nenhum registo encontrado",
                "oPaginate": {
                    "sFirst": "Primeiro",
                    "sLast": "Último",
                    "sNext": "Seguinte",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending": ": ativar para ordenar a coluna de forma ascendente",
                    "sSortDescending": ": ativar para ordenar a coluna de forma descendente"
                }
            },
            responsive: true,
            pageLength: 25
        });
    }

    initDataTable();

    // Filtros
    $('#filtroEstado, #filtroTipologia, #filtroAvaria, #filtroEmpresa').on('change', function() {
        const estado = $('#filtroEstado').val();
        const tipologia = $('#filtroTipologia').val();
        const avaria = $('#filtroAvaria').val();
        const empresa = $('#filtroEmpresa').val();

        dataTable.column(6).search(estado).draw();
        dataTable.column(1).search(tipologia).draw();
        dataTable.column(2).search(avaria).draw();
        dataTable.column(4).search(empresa).draw();
    });

    $('#btnLimparFiltros').on('click', function() {
        $('#filtroEstado, #filtroTipologia, #filtroAvaria, #filtroEmpresa').val('');
        dataTable.search('').columns().search('').draw();
    });

    // Nova Reparação
    $('#btnNovaReparacao').on('click', function() {
        $('#modalReparacaoTitle').text('Nova Reparação Externa');
        $('#formReparacao')[0].reset();
        $('#reparacao_id').val('');
        $('#data_envio').val(new Date().toISOString().split('T')[0]);
        $('#modalReparacao').modal('show');
    });

    // Submeter Formulário
    $('#formReparacao').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#reparacao_id').val();
        const url = id ? 
            '<?= base_url('reparacoes-externas/update') ?>/' + id : 
            '<?= base_url('reparacoes-externas/create') ?>';

        const formData = $(this).serialize();

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Sucesso!', response.message, 'success');
                    $('#modalReparacao').modal('hide');
                    dataTable.ajax.reload();
                    atualizarEstatisticas();
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Erro!', 'Ocorreu um erro ao guardar.', 'error');
            }
        });
    });

    // Editar Reparação
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url('reparacoes-externas/getDetails') ?>/' + id,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#modalReparacaoTitle').text('Editar Reparação');
                    $('#reparacao_id').val(data.id_reparacao);
                    $('#n_serie_equipamento').val(data.n_serie_equipamento);
                    $('#tipologia').val(data.tipologia);
                    $('#possivel_avaria').val(data.possivel_avaria);
                    $('#descricao_avaria').val(data.descricao_avaria);
                    $('#data_envio').val(data.data_envio);
                    $('#empresa_reparacao').val(data.empresa_reparacao);
                    $('#n_guia').val(data.n_guia);
                    $('#trabalho_efetuado').val(data.trabalho_efetuado);
                    $('#custo').val(data.custo);
                    $('#data_recepcao').val(data.data_recepcao);
                    $('#estado').val(data.estado);
                    $('#observacoes').val(data.observacoes);
                    $('#modalReparacao').modal('show');
                }
            }
        });
    });

    // Ver Detalhes
    $(document).on('click', '.btn-view', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url('reparacoes-externas/getDetails') ?>/' + id,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    let html = `
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr><th>ID:</th><td>${data.id_reparacao}</td></tr>
                                    <tr><th>Nº Série:</th><td><strong>${data.n_serie_equipamento}</strong></td></tr>
                                    <tr><th>Tipologia:</th><td>${data.tipologia}</td></tr>
                                    <tr><th>Tipo Avaria:</th><td>${data.possivel_avaria}</td></tr>
                                    <tr><th>Descrição:</th><td>${data.descricao_avaria || '-'}</td></tr>
                                    <tr><th>Data Envio:</th><td>${new Date(data.data_envio).toLocaleDateString('pt-PT')}</td></tr>
                                    <tr><th>Empresa:</th><td>${data.empresa_reparacao || '-'}</td></tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr><th>Nº Guia:</th><td>${data.n_guia || '-'}</td></tr>
                                    <tr><th>Trabalho:</th><td>${data.trabalho_efetuado || '-'}</td></tr>
                                    <tr><th>Custo:</th><td>${data.custo ? parseFloat(data.custo).toFixed(2) + '€' : '-'}</td></tr>
                                    <tr><th>Data Receção:</th><td>${data.data_recepcao ? new Date(data.data_recepcao).toLocaleDateString('pt-PT') : '-'}</td></tr>
                                    <tr><th>Estado:</th><td>${data.estado}</td></tr>
                                    <tr><th>Observações:</th><td>${data.observacoes || '-'}</td></tr>
                                    <tr><th>Criado em:</th><td>${new Date(data.created_at).toLocaleString('pt-PT')}</td></tr>
                                </table>
                            </div>
                        </div>
                    `;
                    $('#detalhesContent').html(html);
                    $('#modalDetalhes').modal('show');
                }
            }
        });
    });

    // Eliminar
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Tem a certeza?',
            text: 'Esta ação não pode ser revertida!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('reparacoes-externas/delete') ?>/' + id,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Eliminado!', response.message, 'success');
                            dataTable.ajax.reload();
                            atualizarEstatisticas();
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    }
                });
            }
        });
    });

    // Exportar
    $('#btnExportar').on('click', function() {
        const estado = $('#filtroEstado').val();
        const tipologia = $('#filtroTipologia').val();
        let url = '<?= base_url('reparacoes-externas/export') ?>?';
        if (estado) url += 'estado=' + estado + '&';
        if (tipologia) url += 'tipologia=' + tipologia;
        window.location.href = url;
    });

    // Download Template
    $('#btnTemplate').on('click', function() {
        window.location.href = '<?= base_url('reparacoes-externas/downloadTemplate') ?>';
    });

    // Importar
    $('#btnImportar').on('click', function() {
        $('#modalImportacao').modal('show');
    });

    $('#formImportacao').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '<?= base_url('reparacoes-externas/import') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let msg = response.message;
                    if (response.resultado && response.resultado.erros.length > 0) {
                        msg += '<br><br><strong>Erros:</strong><br>';
                        response.resultado.erros.forEach(erro => {
                            msg += `Linha ${erro.linha}: ${erro.erro}<br>`;
                        });
                    }
                    Swal.fire({
                        title: 'Importação Concluída!',
                        html: msg,
                        icon: 'success'
                    });
                    $('#modalImportacao').modal('hide');
                    dataTable.ajax.reload();
                    atualizarEstatisticas();
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Ocorreu um erro ao importar o ficheiro.', 'error');
            }
        });
    });

    // Estatísticas
    $('#btnEstatisticas').on('click', function() {
        $.ajax({
            url: '<?= base_url('reparacoes-externas/getStats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const stats = response.stats;
                const porTipologia = response.por_tipologia;
                const porAvaria = response.por_avaria;

                // Atualizar resumo
                $('#stat_detail_total').text(stats.total);
                $('#stat_detail_custo').text(parseFloat(stats.custo_total).toFixed(2) + '€');
                $('#stat_detail_tempo').text(stats.tempo_medio);
                const taxaSucesso = stats.total > 0 ? ((stats.reparado / stats.total) * 100).toFixed(1) : 0;
                $('#stat_detail_taxa').text(taxaSucesso + '%');

                // Gráfico Tipologia
                const ctxTipologia = document.getElementById('chartTipologia').getContext('2d');
                if (chartTipologia) chartTipologia.destroy();
                chartTipologia = new Chart(ctxTipologia, {
                    type: 'pie',
                    data: {
                        labels: porTipologia.map(item => item.tipologia),
                        datasets: [{
                            data: porTipologia.map(item => item.total),
                            backgroundColor: ['#007bff', '#28a745', '#ffc107']
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });

                // Gráfico Avaria
                const ctxAvaria = document.getElementById('chartAvaria').getContext('2d');
                if (chartAvaria) chartAvaria.destroy();
                chartAvaria = new Chart(ctxAvaria, {
                    type: 'bar',
                    data: {
                        labels: porAvaria.map(item => item.possivel_avaria),
                        datasets: [{
                            label: 'Quantidade',
                            data: porAvaria.map(item => item.total),
                            backgroundColor: '#17a2b8'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                $('#modalEstatisticas').modal('show');
            }
        });
    });

    // Atualizar estatísticas da página
    function atualizarEstatisticas() {
        $.ajax({
            url: '<?= base_url('reparacoes-externas/getStats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const stats = response.stats;
                $('#stat_total').text(stats.total);
                $('#stat_em_reparacao').text(stats.em_reparacao);
                $('#stat_reparado').text(stats.reparado);
                $('#stat_custo').text(parseFloat(stats.custo_total).toFixed(2) + '€');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
