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
                    <li class="breadcrumb-item active">As Minhas Permutas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        
        <!-- Estatísticas -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?= $stats['pendentes'] ?></h3>
                        <p>Pendentes</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= $stats['aprovadas'] ?></h3>
                        <p>Aprovadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3><?= $stats['rejeitadas'] ?></h3>
                        <p>Rejeitadas</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= $stats['como_substituto'] ?></h3>
                        <p>Como Substituto</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listagem de Permutas -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-list"></i> Listagem de Permutas</h3>
                <div class="card-tools">
                    <a href="<?= base_url('/permutas') ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-calendar-alt"></i> Ver Meu Horário
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($permutas)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Ainda não tem permutas registadas.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table id="tabelaPermutas" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Aula</th>
                                    <th>Data Original</th>
                                    <th>Data Reposição</th>
                                    <th>Professor Substituto</th>
                                    <th>Estado</th>
                                    <th>Criado</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($permutas as $permuta): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($permuta['disciplina_abrev']) ?></strong><br>
                                            <small class="text-muted">
                                                <?= esc($permuta['turma_nome']) ?> | 
                                                <?php 
                                                $dias = ['', '', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                                                echo $dias[$permuta['dia_semana'] ?? 0];
                                                ?> 
                                                <?= substr($permuta['hora_inicio'] ?? '', 0, 5) ?>-<?= substr($permuta['hora_fim'] ?? '', 0, 5) ?>
                                            </small>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($permuta['data_aula_original'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($permuta['data_aula_permutada'])) ?></td>
                                        <td>
                                            <?= esc($permuta['professor_substituto_nome']) ?>
                                            <?php if ($permuta['professor_substituto_nif'] == $userNif): ?>
                                                <span class="badge bg-info text-dark">Eu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badges = [
                                                'pendente' => 'badge bg-warning text-dark',
                                                'aprovada' => 'badge bg-success text-dark',
                                                'rejeitada' => 'badge bg-danger text-white',
                                                'cancelada' => 'badge bg-secondary text-white'
                                            ];
                                            $badgeClass = $badges[$permuta['estado']] ?? 'badge bg-secondary text-white';
                                            ?>
                                            <span class="<?= $badgeClass ?>">
                                                <?= ucfirst($permuta['estado']) ?>
                                            </span>
                                            <?php if (!empty($permuta['grupo_permuta'])): ?>
                                                <br><small class="text-muted"><i class="fas fa-layer-group"></i> Grupo</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d/m/Y H:i', strtotime($permuta['created_at'])) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('permutas/ver/' . $permuta['id']) ?>" 
                                               class="btn btn-sm btn-info" 
                                               title="Ver detalhes">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($permuta['estado'] == 'pendente' && $permuta['professor_autor_nif'] == $userNif): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger btn-cancelar" 
                                                        data-id="<?= $permuta['id'] ?>"
                                                        title="Cancelar">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php endif; ?>
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
</section>

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
    $('#tabelaPermutas').DataTable({
        'responsive': true,
        'lengthChange': true,
        'autoWidth': false,
        'order': [[0, 'desc']],
        'language': {
            'url': 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        'columnDefs': [
            {
                'targets': -1,
                'orderable': false,
                'searchable': false
            }
        ]
    });

    // Cancelar permuta
    $('.btn-cancelar').on('click', function() {
        var permutaId = $(this).data('id');
        
        Swal.fire({
            title: 'Cancelar Permuta?',
            text: 'Tem a certeza que deseja cancelar esta permuta?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, cancelar',
            cancelButtonText: 'Não'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'A processar...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '<?= base_url('permutas/cancelar/') ?>' + permutaId,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cancelada!',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: 'Erro ao processar pedido: ' + xhr.statusText
                        });
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
