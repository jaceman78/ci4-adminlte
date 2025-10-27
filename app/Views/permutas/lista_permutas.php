<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>

<!-- Content Header -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= esc($page_title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('/permutas') ?>">Horário & Permutas</a></li>
                    <li class="breadcrumb-item active">Lista de Permutas</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">

        <!-- Filtros e Estatísticas -->
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Permutas Futuras</span>
                        <span class="info-box-number"><?= count($permutasFuturas) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-secondary"><i class="fas fa-history"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Permutas Passadas</span>
                        <span class="info-box-number"><?= count($permutasPassadas) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pendentes</span>
                        <span class="info-box-number">
                            <?= count(array_filter($permutasFuturas, fn($p) => $p['estado'] === 'pendente')) + 
                                count(array_filter($permutasPassadas, fn($p) => $p['estado'] === 'pendente')) ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Aprovadas</span>
                        <span class="info-box-number">
                            <?= count(array_filter($permutasFuturas, fn($p) => $p['estado'] === 'aprovada')) + 
                                count(array_filter($permutasPassadas, fn($p) => $p['estado'] === 'aprovada')) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permutas Futuras -->
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title text-white"><i class="fas fa-calendar-alt"></i> Permutas Futuras / Em Aberto</h3>
            </div>
            <div class="card-body">
                <?php if (empty($permutasFuturas)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Não existem permutas futuras registadas.
                    </div>
                <?php else: ?>
                    <table id="tabelaPermutasFuturas" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data Aula</th>
                                <th>Disciplina</th>
                                <th>Turma</th>
                                <th>Professor Autor</th>
                                <th>Professor Substituto</th>
                                <th>Estado</th>
                                <th>Data Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permutasFuturas as $permuta): ?>
                                <tr>
                                    <td><?= $permuta['id'] ?></td>
                                    <td>
                                        <?php if (!empty($permuta['data_aula_original'])): ?>
                                            <span class="badge badge-info" style="color: #000; font-size: 0.9rem;">
                                                <?= date('d/m/Y', strtotime($permuta['data_aula_original'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= esc($permuta['disciplina_abrev'] ?? 'N/A') ?></strong><br>
                                        <small class="text-muted"><?= esc($permuta['disciplina_nome'] ?? '') ?></small>
                                    </td>
                                    <td><?= esc($permuta['codigo_turma'] ?? 'N/A') ?></td>
                                    <td>
                                        <i class="fas fa-user"></i> <?= esc($permuta['professor_autor_nome']) ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-user-check"></i> <?= esc($permuta['professor_substituto_nome']) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeColors = [
                                            'pendente' => ['class' => 'warning', 'style' => 'color: #000; background-color: #ffc107;'],
                                            'aprovada' => ['class' => 'success', 'style' => 'color: #000; background-color: #d4edda;'],
                                            'rejeitada' => ['class' => 'danger', 'style' => 'color: #000; background-color: #f8d7da;'],
                                            'cancelada' => ['class' => 'secondary', 'style' => 'color: #000; background-color: #e2e3e5;']
                                        ];
                                        $badge = $badgeColors[$permuta['estado']] ?? ['class' => 'secondary', 'style' => 'color: #000;'];
                                        ?>
                                        <span class="badge badge-<?= $badge['class'] ?>" style="<?= $badge['style'] ?>">
                                            <?= ucfirst($permuta['estado']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($permuta['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('permutas/ver/' . $permuta['id']) ?>" 
                                           class="btn btn-sm btn-info" 
                                           title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Permutas Passadas -->
        <div class="card">
            <div class="card-header bg-secondary">
                <h3 class="card-title text-white"><i class="fas fa-history"></i> Permutas Passadas / Concluídas</h3>
            </div>
            <div class="card-body">
                <?php if (empty($permutasPassadas)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Não existem permutas passadas registadas.
                    </div>
                <?php else: ?>
                    <table id="tabelaPermutasPassadas" class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data Aula</th>
                                <th>Disciplina</th>
                                <th>Turma</th>
                                <th>Professor Autor</th>
                                <th>Professor Substituto</th>
                                <th>Estado</th>
                                <th>Data Criação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permutasPassadas as $permuta): ?>
                                <tr>
                                    <td><?= $permuta['id'] ?></td>
                                    <td>
                                        <?php if (!empty($permuta['data_aula_original'])): ?>
                                            <span class="badge badge-secondary" style="color: #000; font-size: 0.9rem;">
                                                <?= date('d/m/Y', strtotime($permuta['data_aula_original'])) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= esc($permuta['disciplina_abrev'] ?? 'N/A') ?></strong><br>
                                        <small class="text-muted"><?= esc($permuta['disciplina_nome'] ?? '') ?></small>
                                    </td>
                                    <td><?= esc($permuta['codigo_turma'] ?? 'N/A') ?></td>
                                    <td>
                                        <i class="fas fa-user"></i> <?= esc($permuta['professor_autor_nome']) ?>
                                    </td>
                                    <td>
                                        <i class="fas fa-user-check"></i> <?= esc($permuta['professor_substituto_nome']) ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badgeColors = [
                                            'pendente' => ['class' => 'warning', 'style' => 'color: #000; background-color: #ffc107;'],
                                            'aprovada' => ['class' => 'success', 'style' => 'color: #000; background-color: #d4edda;'],
                                            'rejeitada' => ['class' => 'danger', 'style' => 'color: #000; background-color: #f8d7da;'],
                                            'cancelada' => ['class' => 'secondary', 'style' => 'color: #000; background-color: #e2e3e5;']
                                        ];
                                        $badge = $badgeColors[$permuta['estado']] ?? ['class' => 'secondary', 'style' => 'color: #000;'];
                                        ?>
                                        <span class="badge badge-<?= $badge['class'] ?>" style="<?= $badge['style'] ?>">
                                            <?= ucfirst($permuta['estado']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($permuta['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('permutas/ver/' . $permuta['id']) ?>" 
                                           class="btn btn-sm btn-secondary" 
                                           title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function() {
    // Configuração DataTable para Permutas Futuras
    $('#tabelaPermutasFuturas').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json"
        },
        "order": [[1, "asc"]], // Ordenar por data da aula
        "pageLength": 25,
        "responsive": true,
        "columnDefs": [
            { "orderable": false, "targets": [8] } // Coluna Ações não ordenável
        ]
    });

    // Configuração DataTable para Permutas Passadas
    $('#tabelaPermutasPassadas').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json"
        },
        "order": [[1, "desc"]], // Ordenar por data da aula (mais recente primeiro)
        "pageLength": 25,
        "responsive": true,
        "columnDefs": [
            { "orderable": false, "targets": [8] } // Coluna Ações não ordenável
        ]
    });
});
</script>
<?= $this->endSection() ?>
