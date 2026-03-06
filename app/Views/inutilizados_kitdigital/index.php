<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>Equipamentos Inutilizados<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$userLevel = session()->get('LoggedUserData')['level'] ?? 0;
?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="bi bi-pc-display-horizontal"></i> Equipamentos Inutilizados</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('kit-digital-admin') ?>">Kit Digital</a></li>
                    <li class="breadcrumb-item active">Equipamentos Inutilizados</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="stat_total"><?= $stats['total'] ?></h3>
                        <p>Total de Equipamentos</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-pc-display-horizontal"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="stat_ativos"><?= $stats['ativos'] ?></h3>
                        <p>Ativos</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="stat_esgotados"><?= $stats['esgotados'] ?></h3>
                        <p>Esgotados</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-x-octagon"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3 id="stat_descartados"><?= $stats['descartados'] ?></h3>
                        <p>Descartados</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-trash"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards de Componentes Disponíveis -->
        <div class="row mb-4">
            <div class="col-12">
                <h5><i class="bi bi-cpu"></i> Componentes Disponíveis</h5>
            </div>
            <div class="col-lg-2 col-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="bi bi-memory"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">RAM</span>
                        <span class="info-box-number" id="stat_ram"><?= $stats['componentes']['ram'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="bi bi-device-hdd"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Disco</span>
                        <span class="info-box-number" id="stat_disco"><?= $stats['componentes']['disco'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="bi bi-keyboard"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Teclado</span>
                        <span class="info-box-number" id="stat_teclado"><?= $stats['componentes']['teclado'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="bi bi-display"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ecrã</span>
                        <span class="info-box-number" id="stat_ecra"><?= $stats['componentes']['ecra'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="bi bi-battery-charging"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Bateria</span>
                        <span class="info-box-number" id="stat_bateria"><?= $stats['componentes']['bateria'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary"><i class="bi bi-box"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Caixa</span>
                        <span class="info-box-number" id="stat_caixa"><?= $stats['componentes']['caixa'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Ações -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" id="btnNovoEquipamento">
                <i class="bi bi-plus-circle"></i> Novo Equipamento
            </button>
            <button type="button" class="btn btn-outline-info ms-2" id="btnAtualizarStats">
                <i class="bi bi-arrow-clockwise"></i> Atualizar Estatísticas
            </button>
        </div>

        <!-- Tabela de Equipamentos -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="bi bi-table"></i> Listagem de Equipamentos Inutilizados</h3>
            </div>
            <div class="card-body">
                <table id="tabelaEquipamentos" class="table table-bordered table-striped table-hover nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>N/Série</th>
                            <th>Marca</th>
                            <th>Modelo</th>
                            <th>Componentes Disponíveis</th>
                            <th>Estado</th>
                            <th>Data Registo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Preenchido via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<!-- Modal: Novo/Editar Equipamento -->
<div class="modal fade" id="modalEquipamento" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEquipamentoTitle">Novo Equipamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEquipamento">
                <div class="modal-body">
                    <input type="hidden" id="equipamento_id" name="id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="n_serie" class="form-label">Número de Série *</label>
                            <input type="text" class="form-control" id="n_serie" name="n_serie" required>
                        </div>
                        <div class="col-md-6">
                            <label for="marca" class="form-label">Marca *</label>
                            <input type="text" class="form-control" id="marca" name="marca" required>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="modelo" name="modelo">
                        </div>
                        <div class="col-md-6">
                            <label for="estado" class="form-label">Estado *</label>
                            <select class="form-select" id="estado" name="estado" required>
                                <option value="ativo">Ativo</option>
                                <option value="esgotado">Esgotado</option>
                                <option value="descartado">Descartado</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="border-bottom pb-2"><i class="bi bi-cpu"></i> Componentes Disponíveis</h6>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ram" name="ram" value="1" checked>
                                    <label class="form-check-label" for="ram">
                                        <i class="bi bi-memory"></i> RAM
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="disco" name="disco" value="1" checked>
                                    <label class="form-check-label" for="disco">
                                        <i class="bi bi-device-hdd"></i> Disco
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="teclado" name="teclado" value="1" checked>
                                    <label class="form-check-label" for="teclado">
                                        <i class="bi bi-keyboard"></i> Teclado
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="ecra" name="ecra" value="1" checked>
                                    <label class="form-check-label" for="ecra">
                                        <i class="bi bi-display"></i> Ecrã
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="bateria" name="bateria" value="1" checked>
                                    <label class="form-check-label" for="bateria">
                                        <i class="bi bi-battery-charging"></i> Bateria
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="caixa" name="caixa" value="1" checked>
                                    <label class="form-check-label" for="caixa">
                                        <i class="bi bi-box"></i> Caixa
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="outros" class="form-label">Outros Componentes</label>
                        <textarea class="form-control" id="outros" name="outros" rows="2" placeholder="Descreva outros componentes disponíveis..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Observações adicionais..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSalvarEquipamento">
                        <i class="bi bi-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Ver QR Code -->
<div class="modal fade" id="modalQRCode" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-qr-code"></i> QR Code do Equipamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="qrcodeContent">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">A carregar...</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="#" id="btnDownloadQR" class="btn btn-primary" target="_blank">
                    <i class="bi bi-download"></i> Download
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let tabela;
    let modoEdicao = false;

    // Inicializar DataTable
    tabela = $('#tabelaEquipamentos').DataTable({
        ajax: {
            url: '<?= base_url('inutilizados-kitdigital/getData') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { data: 'n_serie' },
            { data: 'marca' },
            { data: 'modelo' },
            { 
                data: null,
                render: function(data, type, row) {
                    let componentes = [];
                    if (row.ram == 1) componentes.push('<span class="badge bg-primary"><i class="bi bi-memory"></i> RAM</span>');
                    if (row.disco == 1) componentes.push('<span class="badge bg-primary"><i class="bi bi-device-hdd"></i> Disco</span>');
                    if (row.teclado == 1) componentes.push('<span class="badge bg-primary"><i class="bi bi-keyboard"></i> Teclado</span>');
                    if (row.ecra == 1) componentes.push('<span class="badge bg-primary"><i class="bi bi-display"></i> Ecrã</span>');
                    if (row.bateria == 1) componentes.push('<span class="badge bg-primary"><i class="bi bi-battery-charging"></i> Bateria</span>');
                    if (row.caixa == 1) componentes.push('<span class="badge bg-primary"><i class="bi bi-box"></i> Caixa</span>');
                    
                    return componentes.length > 0 ? componentes.join(' ') : '<span class="text-muted">Nenhum</span>';
                }
            },
            { 
                data: 'estado',
                render: function(data) {
                    const badges = {
                        'ativo': '<span class="badge bg-success">Ativo</span>',
                        'esgotado': '<span class="badge bg-warning">Esgotado</span>',
                        'descartado': '<span class="badge bg-secondary">Descartado</span>'
                    };
                    return badges[data] || data;
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    if (!data) return '-';
                    return new Date(data).toLocaleDateString('pt-PT');
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return `
                        <div class="btn-group" role="group">
                            <a href="<?= base_url('inutilizados-kitdigital/view') ?>/${row.id}" class="btn btn-sm btn-success" title="Ver Detalhes">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button class="btn btn-sm btn-info btn-view-qr" data-id="${row.id}" title="Ver QR Code">
                                <i class="bi bi-qr-code"></i>
                            </button>
                            <button class="btn btn-sm btn-primary btn-editar" data-id="${row.id}" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id}" title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ],
        order: [[0, 'desc']],
        language: {
            url: '<?= base_url('assets/datatables/pt-PT.json') ?>'
        },
        responsive: true,
        pageLength: 25
    });

    // Novo Equipamento
    $('#btnNovoEquipamento').click(function() {
        modoEdicao = false;
        $('#modalEquipamentoTitle').text('Novo Equipamento');
        $('#formEquipamento')[0].reset();
        $('#equipamento_id').val('');
        
        // Marcar todos os componentes por defeito
        $('input[type="checkbox"]', '#formEquipamento').prop('checked', true);
        
        $('#modalEquipamento').modal('show');
    });

    // Editar Equipamento
    $(document).on('click', '.btn-editar', function() {
        const id = $(this).data('id');
        modoEdicao = true;
        
        $.ajax({
            url: '<?= base_url('inutilizados-kitdigital/getDetails') ?>/' + id,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    $('#modalEquipamentoTitle').text('Editar Equipamento');
                    $('#equipamento_id').val(data.id);
                    $('#n_serie').val(data.n_serie);
                    $('#marca').val(data.marca);
                    $('#modelo').val(data.modelo);
                    $('#estado').val(data.estado);
                    $('#ram').prop('checked', data.ram == 1);
                    $('#disco').prop('checked', data.disco == 1);
                    $('#teclado').prop('checked', data.teclado == 1);
                    $('#ecra').prop('checked', data.ecra == 1);
                    $('#bateria').prop('checked', data.bateria == 1);
                    $('#caixa').prop('checked', data.caixa == 1);
                    $('#outros').val(data.outros);
                    $('#observacoes').val(data.observacoes);
                    
                    $('#modalEquipamento').modal('show');
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            }
        });
    });

    // Salvar Equipamento
    $('#formEquipamento').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const id = $('#equipamento_id').val();
        const url = id ? '<?= base_url('inutilizados-kitdigital/update') ?>/' + id : '<?= base_url('inutilizados-kitdigital/create') ?>';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire('Sucesso!', response.message, 'success');
                    $('#modalEquipamento').modal('hide');
                    tabela.ajax.reload();
                    atualizarEstatisticas();
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Ocorreu um erro ao guardar o equipamento.', 'error');
            }
        });
    });

    // Ver QR Code
    $(document).on('click', '.btn-view-qr', function() {
        const id = $(this).data('id');
        
        $('#qrcodeContent').html('<div class="spinner-border" role="status"><span class="visually-hidden">A carregar...</span></div>');
        $('#btnDownloadQR').attr('href', '<?= base_url('inutilizados-kitdigital/getQRCode') ?>/' + id);
        $('#btnDownloadQR').attr('download', 'qrcode_equipamento_' + id + '.png');
        $('#modalQRCode').modal('show');
        
        // Carregar informações do equipamento e gerar QR Code dinamicamente
        $.ajax({
            url: '<?= base_url('inutilizados-kitdigital/getDetails') ?>/' + id,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    const qrCodeUrl = '<?= base_url('inutilizados-kitdigital/getQRCode') ?>/' + id;
                    
                    let html = '<div class="mb-3">';
                    html += '<h6>' + data.n_serie + '</h6>';
                    html += '<p class="text-muted mb-2">' + data.marca + (data.modelo ? ' - ' + data.modelo : '') + '</p>';
                    html += '</div>';
                    html += '<img src="' + qrCodeUrl + '" alt="QR Code" class="img-fluid" style="max-width: 300px;">';
                    
                    $('#qrcodeContent').html(html);
                }
            },
            error: function() {
                $('#qrcodeContent').html('<p class="text-danger">Erro ao carregar QR Code</p>');
            }
        });
    });

    // Eliminar Equipamento
    $(document).on('click', '.btn-eliminar', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Tem a certeza?',
            text: "Esta ação não pode ser revertida!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, eliminar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('inutilizados-kitdigital/delete') ?>/' + id,
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Eliminado!', response.message, 'success');
                            tabela.ajax.reload();
                            atualizarEstatisticas();
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    }
                });
            }
        });
    });

    // Atualizar Estatísticas
    $('#btnAtualizarStats').click(function() {
        atualizarEstatisticas();
    });

    function atualizarEstatisticas() {
        $.ajax({
            url: '<?= base_url('inutilizados-kitdigital/getStats') ?>',
            method: 'GET',
            success: function(response) {
                if (response.stats) {
                    $('#stat_total').text(response.stats.total);
                    $('#stat_ativos').text(response.stats.ativos);
                    $('#stat_esgotados').text(response.stats.esgotados);
                    $('#stat_descartados').text(response.stats.descartados);
                    $('#stat_ram').text(response.stats.componentes.ram);
                    $('#stat_disco').text(response.stats.componentes.disco);
                    $('#stat_teclado').text(response.stats.componentes.teclado);
                    $('#stat_ecra').text(response.stats.componentes.ecra);
                    $('#stat_bateria').text(response.stats.componentes.bateria);
                    $('#stat_caixa').text(response.stats.componentes.caixa);
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
