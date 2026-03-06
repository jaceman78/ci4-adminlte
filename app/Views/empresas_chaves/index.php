<?= $this->extend('layout/master') ?>
<?= $this->section('title') ?>Gestão de Chaves de Acesso - Empresas<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="bi bi-key"></i> Gestão de Chaves de Acesso - Empresas</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Chaves de Acesso</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-lg-4 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $stats['total'] ?></h3>
                        <p>Total de Empresas</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $stats['ativas'] ?></h3>
                        <p>Chaves Ativas</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $stats['inativas'] ?></h3>
                        <p>Chaves Inativas</p>
                    </div>
                    <div class="icon">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra de Ações -->
        <div class="mb-3">
            <button type="button" class="btn btn-primary" id="btnNovaChave">
                <i class="bi bi-plus-circle"></i> Nova Chave de Acesso
            </button>
            <button type="button" class="btn btn-outline-info ms-2" id="btnVerLink">
                <i class="bi bi-link-45deg"></i> Link de Acesso Público
            </button>
        </div>

        <!-- DataTable -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Chaves de Acesso</h3>
            </div>
            <div class="card-body">
                <table id="chavesTable" class="table table-bordered table-hover table-sm nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Empresa</th>
                            <th>Chave de Acesso</th>
                            <th>Status</th>
                            <th>Total Acessos</th>
                            <th>Último Acesso</th>
                            <th>Último IP</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>
</section>

<!-- Modal Nova/Editar Chave -->
<div class="modal fade" id="modalChave" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formChave">
                <input type="hidden" id="chave_id" name="id">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalChaveTitle">Nova Chave de Acesso</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Alerta informativo -->
                    <div class="alert alert-info" id="alertChaveAuto">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Nota:</strong> A chave de acesso será gerada automaticamente de forma segura e única.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="empresa_nome" class="form-label">Nome da Empresa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="empresa_nome" name="empresa_nome" required list="empresas_list">
                                <datalist id="empresas_list"></datalist>
                                <small class="text-muted">Escolha uma empresa existente nas reparações ou digite um novo nome</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="plafond_com_iva" class="form-label">Plafond (com IVA)</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="plafond_com_iva" name="plafond_com_iva" step="0.01" min="0" placeholder="0.00">
                                    <span class="input-group-text">€</span>
                                </div>
                                <small class="text-muted">Valor máximo que a empresa pode gastar (incluindo IVA)</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="ativo" class="form-label">Status</label>
                                <select class="form-select" id="ativo" name="ativo">
                                    <option value="1" selected>Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                                <small class="text-muted">A empresa só poderá aceder se o status estiver ativo</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Notas internas sobre esta empresa (opcional)"></textarea>
                            </div>
                        </div>
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

<!-- Modal Ver Detalhes -->
<div class="modal fade" id="modalVerChave" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detalhes da Chave de Acesso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="200"><strong>ID:</strong></td>
                        <td id="ver_id"></td>
                    </tr>
                    <tr>
                        <td><strong>Empresa:</strong></td>
                        <td id="ver_empresa"></td>
                    </tr>
                    <tr>
                        <td><strong>Chave de Acesso:</strong></td>
                        <td>
                            <div class="input-group">
                                <input type="text" class="form-control" id="ver_chave" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="copiarChave()">
                                    <i class="bi bi-clipboard"></i> Copiar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Plafond (com IVA):</strong></td>
                        <td id="ver_plafond"></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td id="ver_status"></td>
                    </tr>
                    <tr>
                        <td><strong>Total de Acessos:</strong></td>
                        <td id="ver_total_acessos"></td>
                    </tr>
                    <tr>
                        <td><strong>Último Acesso:</strong></td>
                        <td id="ver_ultimo_acesso"></td>
                    </tr>
                    <tr>
                        <td><strong>Último IP:</strong></td>
                        <td id="ver_ip"></td>
                    </tr>
                    <tr>
                        <td><strong>Observações:</strong></td>
                        <td id="ver_observacoes"></td>
                    </tr>
                    <tr>
                        <td><strong>Criado em:</strong></td>
                        <td id="ver_created"></td>
                    </tr>
                </table>
                
                <div class="alert alert-info mt-3">
                    <h6><i class="bi bi-link"></i> Link de Acesso:</h6>
                    <div class="input-group">
                        <input type="text" class="form-control" id="ver_link" readonly>
                        <button class="btn btn-primary" type="button" onclick="copiarLink()">
                            <i class="bi bi-clipboard"></i> Copiar Link
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Link Público -->
<div class="modal fade" id="modalLinkPublico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Link de Acesso Público</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>As empresas podem aceder ao sistema através do seguinte link:</p>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="link_publico" readonly value="<?= base_url('empresa/login') ?>">
                    <button class="btn btn-primary" type="button" onclick="copiarLinkPublico()">
                        <i class="bi bi-clipboard"></i> Copiar
                    </button>
                </div>
                <p class="text-muted small">Cada empresa deve usar a sua chave de acesso única para entrar no sistema.</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    const table = $('#chavesTable').DataTable({
        ajax: {
            url: '<?= base_url('empresas-chaves/datatable') ?>',
            type: 'POST',
            error: function(xhr) {
                console.error('Erro ao carregar dados:', xhr);
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
        order: [[0, 'desc']],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        responsive: true,
        pageLength: 25
    });

    // Carregar empresas existentes
    function carregarEmpresas() {
        $.get('<?= base_url('empresas-chaves/empresas-reparacoes') ?>', function(response) {
            if (response.success) {
                const datalist = $('#empresas_list');
                datalist.empty();
                response.data.forEach(empresa => {
                    datalist.append(`<option value="${empresa.empresa_reparacao}">`);
                });
            }
        });
    }

    // Nova Chave
    $('#btnNovaChave').click(function() {
        $('#formChave')[0].reset();
        $('#chave_id').val('');
        $('#modalChaveTitle').text('Nova Chave de Acesso');
        $('#alertChaveAuto').show(); // Mostrar alerta de chave automática
        carregarEmpresas();
        $('#modalChave').modal('show');
    });

    // Ver Link Público
    $('#btnVerLink').click(function() {
        $('#modalLinkPublico').modal('show');
    });

    // Guardar Chave
    $('#formChave').submit(function(e) {
        e.preventDefault();
        const id = $('#chave_id').val();
        const url = id ? `<?= base_url('empresas-chaves/update') ?>/${id}` : '<?= base_url('empresas-chaves/create') ?>';
        
        $.ajax({
            url: url,
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#modalChave').modal('hide');
                    Swal.fire('Sucesso!', response.message, 'success');
                    
                    // Se for criação, mostrar a chave gerada
                    if (response.data && response.data.chave_acesso) {
                        Swal.fire({
                            title: 'Chave Gerada com Sucesso!',
                            html: `
                                <p><strong>Chave de Acesso:</strong></p>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="${response.data.chave_acesso}" id="chave_gerada" readonly>
                                    <button class="btn btn-primary" onclick="copiarChaveGerada()">
                                        <i class="bi bi-clipboard"></i> Copiar
                                    </button>
                                </div>
                                <p class="text-danger small"><i class="bi bi-exclamation-triangle"></i> Guarde esta chave! Ela será necessária para o acesso da empresa.</p>
                            `,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    }
                    
                    table.ajax.reload();
                } else {
                    Swal.fire('Erro', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Erro', 'Não foi possível guardar', 'error');
            }
        });
    });

    // Ver Detalhes
    $(document).on('click', '.btn-view', function() {
        const id = $(this).data('id');
        
        $.get(`<?= base_url('empresas-chaves/show') ?>/${id}`, function(response) {
            if (response.success) {
                const data = response.data;
                $('#ver_id').text(data.id);
                $('#ver_empresa').text(data.empresa_nome);
                $('#ver_chave').val(data.chave_acesso);
                $('#ver_plafond').text(data.plafond_com_iva ? parseFloat(data.plafond_com_iva).toFixed(2) + ' €' : 'Não definido');
                $('#ver_status').html(data.ativo == 1 ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-danger">Inativo</span>');
                $('#ver_total_acessos').text(data.total_acessos || 0);
                $('#ver_ultimo_acesso').text(data.ultimo_acesso ? new Date(data.ultimo_acesso).toLocaleString('pt-PT') : 'Nunca');
                $('#ver_ip').text(data.ip_ultimo_acesso || '-');
                $('#ver_observacoes').text(data.observacoes || '-');
                $('#ver_created').text(data.created_at ? new Date(data.created_at).toLocaleString('pt-PT') : '-');
                $('#ver_link').val(`<?= base_url('empresa/login') ?>?chave=${data.chave_acesso}`);
                
                $('#modalVerChave').modal('show');
            }
        });
    });

    // Editar
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        $.get(`<?= base_url('empresas-chaves/show') ?>/${id}`, function(response) {
            if (response.success) {
                const data = response.data;
                $('#chave_id').val(data.id);
                $('#empresa_nome').val(data.empresa_nome);
                $('#plafond_com_iva').val(data.plafond_com_iva);
                $('#ativo').val(data.ativo);
                $('#observacoes').val(data.observacoes);
                $('#modalChaveTitle').text('Editar Chave de Acesso');
                $('#alertChaveAuto').hide(); // Esconder alerta em modo edição
                carregarEmpresas();
                $('#modalChave').modal('show');
            }
        });
    });

    // Regenerar Chave
    $(document).on('click', '.btn-regenerate', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Regenerar Chave?',
            text: 'A chave atual será invalidada e uma nova será gerada. A empresa terá que usar a nova chave!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, regenerar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`<?= base_url('empresas-chaves/regenerate') ?>/${id}`, function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Chave Regenerada!',
                            html: `
                                <p><strong>Nova Chave:</strong></p>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" value="${response.data.chave_acesso}" id="chave_gerada" readonly>
                                    <button class="btn btn-primary" onclick="copiarChaveGerada()">
                                        <i class="bi bi-clipboard"></i> Copiar
                                    </button>
                                </div>
                                <p class="text-danger small"><i class="bi bi-exclamation-triangle"></i> Envie esta nova chave para a empresa!</p>
                            `,
                            icon: 'success'
                        });
                        table.ajax.reload();
                    } else {
                        Swal.fire('Erro', response.message, 'error');
                    }
                });
            }
        });
    });

    // Eliminar
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Eliminar Chave?',
            text: 'A empresa não poderá mais aceder ao sistema!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, eliminar!',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('empresas-chaves/delete') ?>/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Eliminado!', response.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('Erro', response.message, 'error');
                        }
                    }
                });
            }
        });
    });
});

// Funções de copiar
function copiarChave() {
    const chave = document.getElementById('ver_chave');
    chave.select();
    document.execCommand('copy');
    Swal.fire({
        icon: 'success',
        title: 'Copiado!',
        text: 'Chave copiada para a área de transferência',
        timer: 2000,
        showConfirmButton: false
    });
}

function copiarLink() {
    const link = document.getElementById('ver_link');
    link.select();
    document.execCommand('copy');
    Swal.fire({
        icon: 'success',
        title: 'Copiado!',
        text: 'Link copiado para a área de transferência',
        timer: 2000,
        showConfirmButton: false
    });
}

function copiarLinkPublico() {
    const link = document.getElementById('link_publico');
    link.select();
    document.execCommand('copy');
    Swal.fire({
        icon: 'success',
        title: 'Copiado!',
        text: 'Link copiado para a área de transferência',
        timer: 2000,
        showConfirmButton: false
    });
}

function copiarChaveGerada() {
    const chave = document.getElementById('chave_gerada');
    chave.select();
    document.execCommand('copy');
    Swal.fire({
        icon: 'success',
        title: 'Copiado!',
        text: 'Chave copiada para a área de transferência',
        timer: 2000,
        showConfirmButton: false
    });
}
</script>
<?= $this->endSection() ?>
