<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($page_title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Aprovar Permutas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Info Box -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="callout callout-info">
                    <h5><i class="fas fa-info"></i> Informação:</h5>
                    Nesta página pode aprovar ou rejeitar os pedidos de permuta submetidos pelos professores. 
                    Após aprovação ou rejeição, será enviado um email automático aos professores envolvidos.
                </div>
            </div>
        </div>

        <!-- Permutas Pendentes -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-list"></i> Permutas Pendentes 
                            <span class="badge bg-warning text-dark ml-2"><?= count($permutas) ?></span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (empty($permutas)): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> Não existem permutas pendentes de aprovação.
                            </div>
                        <?php else: ?>
                            <?php
                            // Agrupar permutas por grupo_permuta
                            $grupos = [];
                            foreach ($permutas as $permuta) {
                                if (!empty($permuta['grupo_permuta'])) {
                                    $grupos[$permuta['grupo_permuta']][] = $permuta;
                                }
                            }
                            
                            // Mostrar botões de ação por grupo
                            if (!empty($grupos)):
                            ?>
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-layer-group"></i> Grupos de Permutas</h5>
                                    <p>Foram encontrados <?= count($grupos) ?> grupo(s) de permutas. Pode aprovar ou rejeitar todas as permutas de cada grupo de uma vez:</p>
                                    <?php foreach ($grupos as $grupoId => $permutasGrupo): ?>
                                        <div class="mb-2 p-2 border rounded" style="background-color: #f8f9fa;">
                                            <strong>Grupo com <?= count($permutasGrupo) ?> aula(s)</strong>
                                            <span class="text-muted">
                                                (<?= esc($permutasGrupo[0]['disciplina_abrev']) ?> - 
                                                <?= esc($permutasGrupo[0]['professor_autor_nome']) ?>)
                                            </span>
                                            <div class="float-right">
                                                <button type="button" 
                                                        class="btn btn-sm btn-success btn-aprovar-grupo" 
                                                        data-grupo="<?= esc($grupoId) ?>"
                                                        title="Aprovar todas as permutas deste grupo">
                                                    <i class="fas fa-check-double"></i> Aprovar Grupo
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger btn-rejeitar-grupo" 
                                                        data-grupo="<?= esc($grupoId) ?>"
                                                        title="Rejeitar todas as permutas deste grupo">
                                                    <i class="fas fa-times-circle"></i> Rejeitar Grupo
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="table-responsive">
                                <table id="permutasTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Aula</th>
                                            <th>Professor Autor</th>
                                            <th>Professor Substituto</th>
                                            <th>Data Original</th>
                                            <th>Data Permutada</th>
                                            <th>Submetido</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $corGrupo = '';
                                        $cores = ['#fff3cd', '#d1ecf1', '#d4edda', '#f8d7da', '#e2e3e5'];
                                        $gruposVistos = [];
                                        
                                        foreach ($permutas as $permuta): 
                                            // Definir cor para grupo
                                            if (!empty($permuta['grupo_permuta'])) {
                                                if (!isset($gruposVistos[$permuta['grupo_permuta']])) {
                                                    $gruposVistos[$permuta['grupo_permuta']] = $cores[count($gruposVistos) % count($cores)];
                                                }
                                                $corGrupo = $gruposVistos[$permuta['grupo_permuta']];
                                            } else {
                                                $corGrupo = '';
                                            }
                                        ?>
                                            <tr <?= !empty($permuta['grupo_permuta']) ? 'style="background-color: ' . $corGrupo . ';"' : '' ?>
                                                data-grupo="<?= esc($permuta['grupo_permuta']) ?>">
                                                <td><?= esc($permuta['id']) ?></td>
                                                <td>
                                                    <strong><?= esc($permuta['disciplina_abrev']) ?></strong><br>
                                                    <small class="text-muted">
                                                        <?= esc($permuta['turma_nome']) ?>
                                                        <?php if (!empty($permuta['grupo_permuta'])): ?>
                                                            <br><i class="fas fa-layer-group"></i> Grupo
                                                        <?php endif; ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?= esc($permuta['professor_autor_nome']) ?><br>
                                                    <small class="text-muted"><?= esc($permuta['professor_autor_email']) ?></small>
                                                </td>
                                                <td>
                                                    <?= esc($permuta['professor_substituto_nome']) ?><br>
                                                    <small class="text-muted"><?= esc($permuta['professor_substituto_email']) ?></small>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($permuta['data_aula_original'])) ?></td>
                                                <td><?= date('d/m/Y', strtotime($permuta['data_aula_permutada'])) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($permuta['created_at'])) ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="<?= base_url('permutas/ver/' . $permuta['id']) ?>" 
                                                           class="btn btn-sm btn-info" 
                                                           title="Ver Detalhes">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-success btn-aprovar" 
                                                                data-id="<?= $permuta['id'] ?>"
                                                                title="Aprovar">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger btn-rejeitar" 
                                                                data-id="<?= $permuta['id'] ?>"
                                                                title="Rejeitar">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Modal de Rejeição Individual -->
<div class="modal fade" id="modalRejeitar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Rejeitar Permuta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="permutaIdRejeitar">
                <div class="form-group">
                    <label for="motivoRejeicao">Motivo da Rejeição *</label>
                    <textarea class="form-control" id="motivoRejeicao" rows="4" 
                              placeholder="Indique o motivo da rejeição..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarRejeicao">
                    <i class="fas fa-times"></i> Rejeitar Permuta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Rejeição de Grupo -->
<div class="modal fade" id="modalRejeitarGrupo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Rejeitar Grupo de Permutas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="grupoRejeitar">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Atenção:</strong> Todas as permutas deste grupo serão rejeitadas com o mesmo motivo.
                </div>
                <div class="form-group">
                    <label for="motivoRejeicaoGrupo">Motivo da Rejeição *</label>
                    <textarea class="form-control" id="motivoRejeicaoGrupo" rows="4" 
                              placeholder="Indique o motivo da rejeição..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarRejeicaoGrupo">
                    <i class="fas fa-times-circle"></i> Rejeitar Todas
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#permutasTable').DataTable({
        'paging': true,
        'lengthChange': true,
        'searching': true,
        'ordering': true,
        'info': true,
        'autoWidth': false,
        'responsive': true,
        'order': [[0, 'desc']],
        'language': {
            'url': 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        'columnDefs': [
            { 'targets': -1, 'orderable': false, 'searchable': false }
        ]
    });

    // Aprovar permuta
    $('.btn-aprovar').on('click', function() {
        var permutaId = $(this).data('id');
        
        Swal.fire({
            title: 'Aprovar Permuta?',
            text: "Tem a certeza que pretende aprovar esta permuta? Será enviado um email aos professores envolvidos.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, aprovar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('permutas/aprovar/') ?>' + permutaId,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Aprovada!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro!', 'Erro ao processar pedido', 'error');
                    }
                });
            }
        });
    });

    // Abrir modal de rejeição
    $('.btn-rejeitar').on('click', function() {
        var permutaId = $(this).data('id');
        $('#permutaIdRejeitar').val(permutaId);
        $('#motivoRejeicao').val('');
        
        // Bootstrap 5 - usar API nativa
        var modalEl = document.getElementById('modalRejeitar');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    // Confirmar rejeição individual
    $('#btnConfirmarRejeicao').on('click', function() {
        var permutaId = $('#permutaIdRejeitar').val();
        var motivo = $('#motivoRejeicao').val().trim();

        if (!motivo) {
            Swal.fire('Atenção!', 'Por favor indique o motivo da rejeição', 'warning');
            return;
        }

        // Fechar modal usando Bootstrap 5
        var modalEl = document.getElementById('modalRejeitar');
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }

        $.ajax({
            url: '<?= base_url('permutas/rejeitar') ?>',
            type: 'POST',
            data: {
                permuta_id: permutaId,
                motivo: motivo
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Rejeitada!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Erro ao processar pedido', 'error');
            }
        });
    });

    // ========================================
    // AÇÕES EM GRUPO
    // ========================================

    // Aprovar grupo de permutas
    $('.btn-aprovar-grupo').on('click', function() {
        var grupoId = $(this).data('grupo');
        
        Swal.fire({
            title: 'Aprovar Grupo de Permutas?',
            text: "Todas as permutas deste grupo serão aprovadas. Será enviado um email aos professores envolvidos.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, aprovar todas!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('permutas/aprovarGrupo') ?>',
                    type: 'POST',
                    data: { grupo_id: grupoId },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Aprovadas!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Erro!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Erro!', 'Erro ao processar pedido', 'error');
                    }
                });
            }
        });
    });

    // Abrir modal de rejeição de grupo
    $('.btn-rejeitar-grupo').on('click', function() {
        var grupoId = $(this).data('grupo');
        $('#grupoRejeitar').val(grupoId);
        $('#motivoRejeicaoGrupo').val('');
        
        // Bootstrap 5 - usar API nativa
        var modalEl = document.getElementById('modalRejeitarGrupo');
        var modal = new bootstrap.Modal(modalEl);
        modal.show();
    });

    // Confirmar rejeição de grupo
    $('#btnConfirmarRejeicaoGrupo').on('click', function() {
        var grupoId = $('#grupoRejeitar').val();
        var motivo = $('#motivoRejeicaoGrupo').val().trim();

        if (!motivo) {
            Swal.fire('Atenção!', 'Por favor indique o motivo da rejeição', 'warning');
            return;
        }

        // Fechar modal usando Bootstrap 5
        var modalEl = document.getElementById('modalRejeitarGrupo');
        var modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }

        $.ajax({
            url: '<?= base_url('permutas/rejeitarGrupo') ?>',
            type: 'POST',
            data: {
                grupo_id: grupoId,
                motivo: motivo
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Rejeitadas!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Erro ao processar pedido', 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
