<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= esc($empresa['empresa_nome']) ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .small-box {
            border-radius: 15px;
            padding: 20px;
            color: white;
            position: relative;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .small-box:hover {
            transform: translateY(-5px);
        }
        
        .small-box h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        .small-box p {
            margin: 10px 0 0 0;
            font-size: 1rem;
        }
        
        .small-box .icon {
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            font-size: 4rem;
            opacity: 0.3;
        }
        
        .bg-info { background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%); }
        .bg-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .bg-success { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .bg-danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .bg-secondary { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        .bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        
        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            font-weight: 600;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .badge {
            padding: 0.5em 0.8em;
            font-weight: 600;
        }
        
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
        }
        
        .modal-header {
            border-radius: 15px 15px 0 0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-tools me-2"></i>
                Portal Empresa - Reparações
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text text-white me-3">
                    <i class="bi bi-building me-2"></i>
                    <strong><?= esc($empresa['empresa_nome']) ?></strong>
                </span>
                <a href="<?= base_url('empresa/logout') ?>" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $stats['total'] ?></h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3><?= $stats['enviado'] ?></h3>
                        <p>Enviado</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-send"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $stats['em_reparacao'] ?></h3>
                        <p>Em Reparação</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $stats['reparado'] ?></h3>
                        <p>Reparado</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $stats['irreparavel'] ?></h3>
                        <p>Irreparável</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-4 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3><?= number_format($stats['custo_total'], 0, ',', '.') ?>€</h3>
                        <p>Custo Total</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Plafond -->
        <?php if ($plafond > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-wallet2 me-2"></i>
                            Gestão de Plafond
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="p-3">
                                    <h6 class="text-muted mb-2">Plafond Total (com IVA)</h6>
                                    <h2 class="mb-0 text-primary"><?= number_format($plafond, 2, ',', '.') ?>€</h2>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border-start border-end">
                                    <h6 class="text-muted mb-2">Total Gasto</h6>
                                    <h2 class="mb-0 text-danger"><?= number_format($gasto, 2, ',', '.') ?>€</h2>
                                    <small class="text-muted"><?= $plafond > 0 ? number_format(($gasto / $plafond) * 100, 1) : 0 ?>% utilizado</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3">
                                    <h6 class="text-muted mb-2">Disponível</h6>
                                    <h2 class="mb-0 <?= $disponivel > 0 ? 'text-success' : 'text-warning' ?>"><?= number_format($disponivel, 2, ',', '.') ?>€</h2>
                                    <?php if ($disponivel < 0): ?>
                                        <small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Plafond excedido</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php if ($plafond > 0): ?>
                        <div class="progress mt-3" style="height: 25px;">
                            <?php 
                                $percentagem = min(($gasto / $plafond) * 100, 100);
                                $corBarra = $percentagem < 70 ? 'bg-success' : ($percentagem < 90 ? 'bg-warning' : 'bg-danger');
                            ?>
                            <div class="progress-bar <?= $corBarra ?>" role="progressbar" style="width: <?= $percentagem ?>%;" aria-valuenow="<?= $percentagem ?>" aria-valuemin="0" aria-valuemax="100">
                                <?= number_format($percentagem, 1) ?>%
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- DataTable -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Minhas Reparações
                </h5>
            </div>
            <div class="card-body">
                <table id="reparacoesTable" class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Nº Série</th>
                            <th>Tipologia</th>
                            <th>Avaria</th>
                            <th>Data Envio</th>
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

    <!-- Modal Editar Reparação -->
    <div class="modal fade" id="modalEditarReparacao" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEditarReparacao">
                    <input type="hidden" id="reparacao_id">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil me-2"></i>
                            Editar Reparação
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nº Série</label>
                                <input type="text" class="form-control" id="edit_n_serie" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipologia</label>
                                <input type="text" class="form-control" id="edit_tipologia" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_descricao_avaria" class="form-label">Descrição da Avaria</label>
                            <textarea class="form-control" id="edit_descricao_avaria" name="descricao_avaria" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_trabalho_efetuado" class="form-label">Trabalho Efetuado</label>
                            <textarea class="form-control" id="edit_trabalho_efetuado" name="trabalho_efetuado" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_custo" class="form-label">Custo (€)</label>
                                <input type="number" class="form-control" id="edit_custo" name="custo" step="0.01" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_estado" class="form-label">Estado</label>
                                <select class="form-select" id="edit_estado" name="estado">
                                    <option value="enviado">Enviado</option>
                                    <option value="em_reparacao">Em Reparação</option>
                                    <option value="reparado">Reparado</option>
                                    <option value="irreparavel">Irreparável</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="edit_observacoes" name="observacoes" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Guardar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ver Detalhes -->
    <div class="modal fade" id="modalVerReparacao" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-eye me-2"></i>
                        Detalhes da Reparação
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nº Série:</strong> <span id="ver_n_serie"></span></p>
                            <p><strong>Tipologia:</strong> <span id="ver_tipologia"></span></p>
                            <p><strong>Avaria:</strong> <span id="ver_possivel_avaria"></span></p>
                            <p><strong>Estado:</strong> <span id="ver_estado"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data Envio:</strong> <span id="ver_data_envio"></span></p>
                            <p><strong>Custo:</strong> <span id="ver_custo"></span></p>
                            <p><strong>Dias de Reparação:</strong> <span id="ver_dias"></span></p>
                        </div>
                    </div>
                    <hr>
                    <p><strong>Descrição da Avaria:</strong></p>
                    <p id="ver_descricao_avaria" class="text-muted"></p>
                    
                    <p><strong>Trabalho Efetuado:</strong></p>
                    <p id="ver_trabalho_efetuado" class="text-muted"></p>
                    
                    <p><strong>Observações:</strong></p>
                    <p id="ver_observacoes" class="text-muted"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        // Inicializar DataTable
        const table = $('#reparacoesTable').DataTable({
            ajax: {
                url: '<?= base_url('empresa/datatable') ?>',
                type: 'POST',
                error: function(xhr) {
                    Swal.fire('Erro', 'Não foi possível carregar os dados', 'error');
                }
            },
            columns: [
                { data: 0 },
                { data: 1 },
                { data: 2 },
                { data: 3 },
                { data: 4 },
                { data: 5 },
                { data: 6 },
                { data: 7, orderable: false }
            ],
            order: [[3, 'desc']],
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
            },
            responsive: true,
            pageLength: 25
        });

        // Ver Detalhes
        $(document).on('click', '.btn-view', function() {
            const id = $(this).data('id');
            
            $.get(`<?= base_url('empresa/reparacao') ?>/${id}`, function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#ver_n_serie').text(data.n_serie_equipamento);
                    $('#ver_tipologia').text(data.tipologia);
                    $('#ver_possivel_avaria').text(data.possivel_avaria);
                    $('#ver_estado').html(getEstadoBadge(data.estado));
                    $('#ver_data_envio').text(data.data_envio ? new Date(data.data_envio).toLocaleDateString('pt-PT') : '-');
                    $('#ver_custo').text(data.custo ? parseFloat(data.custo).toFixed(2) + '€' : '-');
                    $('#ver_dias').text(data.dias_reparacao ? data.dias_reparacao + ' dias' : '-');
                    $('#ver_descricao_avaria').text(data.descricao_avaria || 'Sem descrição');
                    $('#ver_trabalho_efetuado').text(data.trabalho_efetuado || 'Não especificado');
                    $('#ver_observacoes').text(data.observacoes || 'Sem observações');
                    
                    $('#modalVerReparacao').modal('show');
                }
            });
        });

        // Editar
        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            
            $.get(`<?= base_url('empresa/reparacao') ?>/${id}`, function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#reparacao_id').val(data.id_reparacao);
                    $('#edit_n_serie').val(data.n_serie_equipamento);
                    $('#edit_tipologia').val(data.tipologia);
                    $('#edit_descricao_avaria').val(data.descricao_avaria);
                    $('#edit_trabalho_efetuado').val(data.trabalho_efetuado);
                    $('#edit_custo').val(data.custo);
                    $('#edit_estado').val(data.estado);
                    $('#edit_observacoes').val(data.observacoes);
                    
                    $('#modalEditarReparacao').modal('show');
                }
            });
        });

        // Guardar Edição
        $('#formEditarReparacao').submit(function(e) {
            e.preventDefault();
            const id = $('#reparacao_id').val();
            
            $.ajax({
                url: `<?= base_url('empresa/atualizar') ?>/${id}`,
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#modalEditarReparacao').modal('hide');
                        Swal.fire('Sucesso!', response.message, 'success');
                        table.ajax.reload();
                    } else {
                        Swal.fire('Erro', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Erro', 'Não foi possível atualizar', 'error');
                }
            });
        });

        function getEstadoBadge(estado) {
            const badges = {
                'enviado': '<span class="badge bg-primary">Enviado</span>',
                'em_reparacao': '<span class="badge bg-warning">Em Reparação</span>',
                'reparado': '<span class="badge bg-success">Reparado</span>',
                'irreparavel': '<span class="badge bg-danger">Irreparável</span>',
                'cancelado': '<span class="badge bg-secondary">Cancelado</span>'
            };
            return badges[estado] || estado;
        }
    });
    </script>
</body>
</html>
