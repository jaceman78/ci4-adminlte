<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1><i class="bi bi-file-text"></i> Logs do Sistema de Férias</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <!-- Filtros -->
            <div class="card card-outline card-secondary">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-funnel"></i> Filtros</h3>
                </div>
                <div class="card-body">
                    <form method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="user_nif" class="form-control">
                                    <option value="">Todos os Professores</option>
                                    <?php foreach ($professores as $p): ?>
                                    <option value="<?= $p['NIF'] ?>" <?= $filtros['user_nif'] == $p['NIF'] ? 'selected' : '' ?>>
                                        <?= esc($p['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="acao" class="form-control" 
                                       placeholder="Ação" value="<?= $filtros['acao'] ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="data_inicio" class="form-control" 
                                       value="<?= $filtros['data_inicio'] ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="data_fim" class="form-control" 
                                       value="<?= $filtros['data_fim'] ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="bi bi-search"></i> Filtrar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Tabela Logs -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Data/Hora</th>
                                    <th>Utilizador</th>
                                    <th>Ação</th>
                                    <th>Detalhes</th>
                                    <th>Responsável</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><small><?= date('d/m/Y H:i:s', strtotime($log['criado_em'])) ?></small></td>
                                    <td><?= esc($log['nome_usuario'] ?? 'N/A') ?></td>
                                    <td><strong><?= esc($log['acao']) ?></strong></td>
                                    <td><small><?= esc($log['detalhes']) ?></small></td>
                                    <td><?= esc($log['nome_responsavel'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?= $pager->links() ?>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?= $this->endSection() ?>
