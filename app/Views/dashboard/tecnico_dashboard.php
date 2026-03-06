<?= $this->extend('layout/master') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item active">Dashboard Técnico</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Cards de Estatísticas -->
            <div class="row">
                <!-- Tickets Ativos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $stats['tickets_ativos'] ?></h3>
                            <p>Tickets Ativos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <a href="<?= site_url('tickets/tratamento') ?>" class="small-box-footer">
                            Ver Todos <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Tickets Urgentes -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= $stats['tickets_urgentes'] ?></h3>
                            <p>Tickets Urgentes</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="<?= site_url('tickets/tratamento') ?>" class="small-box-footer">
                            Ver Urgentes <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Aguardam Peça -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $stats['aguardam_peca'] ?></h3>
                            <p>Aguardam Peça</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <a href="<?= site_url('tickets/tratamento') ?>" class="small-box-footer">
                            Ver Pendentes <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Resolvidos Este Mês -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $stats['resolvidos_mes'] ?></h3>
                            <p>Resolvidos Este Mês</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="<?= site_url('tickets/meus') ?>" class="small-box-footer">
                            Ver Histórico <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Permutas (se aplicável) -->
            <?php if (!empty($permutas_stats)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exchange-alt"></i> As Minhas Permutas</h3>
                            <div class="card-tools">
                                <a href="<?= base_url('permutas/minhas') ?>" class="btn btn-tool">
                                    Ver Todas <i class="fas fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-warning">
                                        <div class="inner">
                                            <h3><?= $permutas_stats['pendentes'] ?></h3>
                                            <p>Pendentes</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-clock"></i></div>
                                        <a href="<?= base_url('permutas/minhas') ?>" class="small-box-footer">
                                            Ver <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3><?= $permutas_stats['aprovadas'] ?></h3>
                                            <p>Aprovadas</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-check-circle"></i></div>
                                        <a href="<?= base_url('permutas/minhas') ?>" class="small-box-footer">
                                            Ver <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-danger">
                                        <div class="inner">
                                            <h3><?= $permutas_stats['rejeitadas'] ?></h3>
                                            <p>Rejeitadas</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-times-circle"></i></div>
                                        <a href="<?= base_url('permutas/minhas') ?>" class="small-box-footer">
                                            Ver <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3><?= $permutas_stats['como_substituto'] ?></h3>
                                            <p>Como Substituto</p>
                                        </div>
                                        <div class="icon"><i class="fas fa-user-check"></i></div>
                                        <a href="<?= base_url('permutas/minhas') ?>" class="small-box-footer">
                                            Ver <i class="fas fa-arrow-circle-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($permutas_recentes)): ?>
                            <h5 class="mt-3 mb-2">Permutas Recentes</h5>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Aula</th>
                                            <th>Data Original</th>
                                            <th>Data Reposição</th>
                                            <th>Estado</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($permutas_recentes as $permuta): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($permuta['disciplina_abrev']) ?></strong><br>
                                                <small><?= esc($permuta['turma_nome']) ?></small>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($permuta['data_aula_original'])) ?></td>
                                            <td><?= date('d/m/Y', strtotime($permuta['data_aula_permutada'])) ?></td>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'pendente' => 'warning',
                                                    'aprovada' => 'success',
                                                    'rejeitada' => 'danger',
                                                    'cancelada' => 'secondary'
                                                ];
                                                ?>
                                                <span class="badge badge-<?= $badges[$permuta['estado']] ?? 'secondary' ?>">
                                                    <?= ucfirst($permuta['estado']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('permutas/ver/' . $permuta['id']) ?>" 
                                                   class="btn btn-xs btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
            <?php endif; ?>

            <!-- Pedidos de Permuta para Aceitar/Recusar -->
            <?php if (!empty($permutas_pendentes_substituto)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Pedidos de Permuta Pendentes - Requer Sua Resposta</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Solicitante</th>
                                        <th>Data</th>
                                        <th>Hora</th>
                                        <th>Prova</th>
                                        <th>Sala</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($permutas_pendentes_substituto as $permuta): ?>
                                    <tr>
                                        <td><strong><?= esc($permuta['nome_original']) ?></strong></td>
                                        <td><?= date('d/m/Y', strtotime($permuta['data_exame'])) ?></td>
                                        <td><strong><?= date('H:i', strtotime($permuta['hora_exame'])) ?></strong></td>
                                        <td><?= esc($permuta['codigo_prova']) ?></td>
                                        <td><?= esc($permuta['codigo_sala'] ?? 'N/A') ?></td>
                                        <td>
                                            <button type="button" class="btn btn-xs btn-success btn-aceitar-permuta" 
                                                    data-permuta-id="<?= $permuta['id'] ?>"
                                                    data-prova="<?= esc($permuta['codigo_prova']) ?>">
                                                <i class="fas fa-check"></i> Aceitar
                                            </button>
                                            <button type="button" class="btn btn-xs btn-danger btn-recusar-permuta" 
                                                    data-permuta-id="<?= $permuta['id'] ?>"
                                                    data-prova="<?= esc($permuta['codigo_prova']) ?>">
                                                <i class="fas fa-times"></i> Recusar
                                            </button>
                                            <button type="button" class="btn btn-xs btn-info btn-ver-detalhes-permuta" 
                                                    title="Ver detalhes"
                                                    data-solicitante="<?= esc($permuta['nome_original']) ?>"
                                                    data-data="<?= date('d/m/Y', strtotime($permuta['data_exame'])) ?>"
                                                    data-hora="<?= date('H:i', strtotime($permuta['hora_exame'])) ?>"
                                                    data-prova="<?= esc($permuta['codigo_prova']) ?>"
                                                    data-sala="<?= esc($permuta['codigo_sala'] ?? 'N/A') ?>"
                                                    data-estado="Aguarda a sua resposta"
                                                    data-permuta-id="<?= $permuta['id'] ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Convocatórias para Exames -->
            <?php if (!empty($convocatorias)): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clipboard-list"></i> Convocatórias para Exames</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Hora</th>
                                        <th>Prova</th>
                                        <th>Fase</th>
                                        <th>Sala</th>
                                        <th>Função</th>
                                        <th>Presença</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $hoje = date('Y-m-d');
                                    foreach ($convocatorias as $conv): 
                                        $dataExame = date('Y-m-d', strtotime($conv['data_exame']));
                                        $isPassado = ($dataExame < $hoje);
                                        $isHoje = ($dataExame === $hoje);
                                        $rowClass = $isPassado ? 'table-secondary' : ($isHoje ? 'table-success' : '');
                                    ?>
                                    <tr class="<?= $rowClass ?>">
                                        <td><?= date('d/m/Y', strtotime($conv['data_exame'])) ?></td>
                                        <td><strong><?= date('H:i', strtotime($conv['hora_exame'])) ?></strong></td>
                                        <td><?= esc($conv['codigo_prova']) ?> - <?= esc($conv['nome_prova']) ?></td>
                                        <td><?= esc($conv['fase']) ?></td>
                                        <td><?= esc($conv['codigo_sala'] ?? 'N/A') ?></td>
                                        <td><span class="badge badge-light border text-dark"><?= esc($conv['funcao']) ?></span></td>
                                        <td>
                                            <?php if ($isPassado): ?>
                                                <?php 
                                                $presencaBadge = [
                                                    'Presente' => 'badge-success',
                                                    'Falta' => 'badge-danger',
                                                    'Falta Justificada' => 'badge-warning',
                                                    'Pendente' => 'badge-secondary'
                                                ];
                                                $presencaLabel = $conv['presenca'] ?? 'Pendente';
                                                $badgeClass = $presencaBadge[$presencaLabel] ?? 'badge-secondary';
                                                ?>
                                                <span class="badge <?= $badgeClass ?>"><?= $presencaLabel ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-light">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($conv['estado_confirmacao']) && $conv['estado_confirmacao'] === 'Confirmado'): ?>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Confirmado
                                                </span>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-xs btn-success btn-confirmar-presenca" 
                                                        data-convocatoria-id="<?= $conv['id'] ?>"
                                                        data-prova="<?= esc($conv['codigo_prova']) ?>"
                                                        data-nome-prova="<?= esc($conv['nome_prova']) ?>"
                                                        data-data="<?= date('d/m/Y', strtotime($conv['data_exame'])) ?>"
                                                        data-hora="<?= date('H:i', strtotime($conv['hora_exame'])) ?>"
                                                        title="Confirmar Presença">
                                                    <i class="fas fa-check-circle"></i> Confirmar Presença
                                                </button>
                                            <?php endif; ?>
                                            <?php if (!empty($conv['permuta_id'])): ?>
                                                <?php
                                                $estadoBadge = [
                                                    'PENDENTE' => 'badge-warning',
                                                    'ACEITE_SUBSTITUTO' => 'badge-info',
                                                    'VALIDADO_SECRETARIADO' => 'badge-success',
                                                    'REJEITADO_SECRETARIADO' => 'badge-danger',
                                                    'RECUSADO_SUBSTITUTO' => 'badge-danger',
                                                    'CANCELADO' => 'badge-secondary'
                                                ];
                                                $estadoTexto = [
                                                    'PENDENTE' => 'Permuta Pendente',
                                                    'ACEITE_SUBSTITUTO' => 'Aceite pelo Substituto',
                                                    'VALIDADO_SECRETARIADO' => 'Permuta Aprovada',
                                                    'REJEITADO_SECRETARIADO' => 'Permuta Rejeitada',
                                                    'RECUSADO_SUBSTITUTO' => 'Recusada pelo Substituto',
                                                    'CANCELADO' => 'Permuta Cancelada'
                                                ];
                                                $badgeClass = $estadoBadge[$conv['permuta_estado']] ?? 'badge-secondary';
                                                $estadoLabel = $estadoTexto[$conv['permuta_estado']] ?? $conv['permuta_estado'];
                                                ?>
                                                <span class="badge <?= $badgeClass ?>" style="<?= $conv['permuta_estado'] == 'PENDENTE' ? 'color: #856404;' : '' ?>">
                                                    <i class="fas fa-exchange-alt"></i> <?= $estadoLabel ?>
                                                </span>
                                                <button type="button" class="btn btn-xs btn-outline-primary btn-ver-detalhes-convocatoria" 
                                                        title="Ver detalhes"
                                                        data-data="<?= date('d/m/Y', strtotime($conv['data_exame'])) ?>"
                                                        data-hora="<?= date('H:i', strtotime($conv['hora_exame'])) ?>"
                                                        data-prova="<?= esc($conv['codigo_prova']) ?>"
                                                        data-nome-prova="<?= esc($conv['nome_prova']) ?>"
                                                        data-fase="<?= esc($conv['fase']) ?>"
                                                        data-sala="<?= esc($conv['codigo_sala'] ?? 'N/A') ?>"
                                                        data-funcao="<?= esc($conv['funcao']) ?>"
                                                        data-estado="<?= $estadoLabel ?>"
                                                        data-estado-class="<?= $badgeClass ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            <?php else: ?>
                                                <?php
                                                // Verificar se faltam pelo menos 24 horas para o exame
                                                $dataHoraExame = new DateTime($conv['data_exame'] . ' ' . $conv['hora_exame']);
                                                $agora = new DateTime();
                                                $diferencaHoras = ($dataHoraExame->getTimestamp() - $agora->getTimestamp()) / 3600;
                                                $podePermuta = $diferencaHoras >= 24;
                                                ?>
                                                <?php if ($podePermuta): ?>
                                                <button type="button" class="btn btn-xs btn-warning btn-pedir-permuta" 
                                                        data-convocatoria-id="<?= $conv['id'] ?>"
                                                        data-prova="<?= esc($conv['codigo_prova']) ?>"
                                                        data-data="<?= date('d/m/Y', strtotime($conv['data_exame'])) ?>"
                                                        data-hora="<?= date('H:i', strtotime($conv['hora_exame'])) ?>"
                                                        data-sala="<?= esc($conv['codigo_sala'] ?? 'N/A') ?>"
                                                        data-sessao-id="<?= $conv['sessao_exame_id'] ?>">
                                                    <i class="fas fa-exchange-alt"></i> Pedir Permuta
                                                </button>
                                                <?php else: ?>
                                                <span class="badge badge-secondary" title="Prazo expirado (menos de 24h)">
                                                    <i class="fas fa-clock"></i> Prazo expirado
                                                </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">
                <!-- Coluna Esquerda: Tickets -->
                <div class="col-lg-8">
                    
                    <!-- Tickets Urgentes -->
                    <?php if (!empty($tickets_urgentes)): ?>
                    <div class="card card-danger card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-triangle"></i> Tickets Urgentes</h3>
                            <div class="card-tools">
                                <span class="badge badge-danger"><?= count($tickets_urgentes) ?></span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Equipamento</th>
                                            <th>Prioridade</th>
                                            <th>Estado</th>
                                            <th>Criado</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($tickets_urgentes, 0, 5) as $ticket): ?>
                                        <tr>
                                            <td><strong>#<?= $ticket['id'] ?></strong></td>
                                            <td>
                                                <small><?= esc($ticket['equipamento_nome'] ?? 'N/A') ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $ticket['prioridade'] == 'critica' ? 'danger' : 'warning' ?>">
                                                    <?= ucfirst($ticket['prioridade']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-info"><?= ucfirst(str_replace('_', ' ', $ticket['estado'])) ?></span>
                                            </td>
                                            <td><small><?= date('d/m H:i', strtotime($ticket['created_at'])) ?></small></td>
                                            <td>
                                                <a href="<?= site_url('tickets/view/' . $ticket['id']) ?>" class="btn btn-xs btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (count($tickets_urgentes) > 5): ?>
                        <div class="card-footer text-center">
                            <a href="<?= site_url('tickets/tratamento') ?>" class="btn btn-sm btn-danger">
                                Ver Todos os Urgentes (<?= count($tickets_urgentes) ?>)
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Tickets Aguardam Peça -->
                    <?php if (!empty($tickets_aguardam_peca)): ?>
                    <div class="card card-warning card-outline">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-box"></i> Tickets Aguardam Peça</h3>
                            <div class="card-tools">
                                <span class="badge badge-warning"><?= count($tickets_aguardam_peca) ?></span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Equipamento</th>
                                            <th>Descrição</th>
                                            <th>Desde</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets_aguardam_peca as $ticket): ?>
                                        <tr>
                                            <td><strong>#<?= $ticket['id'] ?></strong></td>
                                            <td><small><?= esc($ticket['equipamento_nome'] ?? 'N/A') ?></small></td>
                                            <td><small><?= esc(substr($ticket['descricao'] ?? '', 0, 50)) ?>...</small></td>
                                            <td><small><?= date('d/m/Y', strtotime($ticket['updated_at'])) ?></small></td>
                                            <td>
                                                <a href="<?= site_url('tickets/view/' . $ticket['id']) ?>" class="btn btn-xs btn-warning">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>

                <!-- Coluna Direita: Info -->
                <div class="col-lg-4">
                    
                    <!-- Tickets por Localização -->
                    <?php if (!empty($tickets_por_localizacao)): ?>
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Tickets por Localização</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($tickets_por_localizacao as $loc): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= esc($loc['escola'] ?? 'Sem Escola') ?>
                                    <span class="badge badge-primary badge-pill"><?= $loc['total'] ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tipos de Avaria Mais Comuns -->
                    <?php if (!empty($tipos_avaria_comuns)): ?>
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-wrench"></i> Tipos de Avaria Mais Comuns</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($tipos_avaria_comuns as $tipo): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <small><?= esc($tipo['tipo_avaria'] ?? 'N/A') ?></small>
                                    <span class="badge badge-warning badge-pill"><?= $tipo['total'] ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Equipamentos Mais Problemáticos -->
                    <?php if (!empty($equipamentos_problematicos)): ?>
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-circle"></i> Equipamentos Problemáticos</h3>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <?php foreach ($equipamentos_problematicos as $equip): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <small><?= esc($equip['tipo_equipamento'] ?? 'N/A') ?></small>
                                    <span class="badge badge-danger badge-pill"><?= $equip['total'] ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal Ver Detalhes de Permuta Pendente -->
<div class="modal fade" id="modalDetalhesPermuta" tabindex="-1" aria-labelledby="modalDetalhesPermutaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalDetalhesPermutaLabel"><i class="fas fa-exchange-alt"></i> Detalhes do Pedido de Permuta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar" style="filter: invert(1) grayscale(100%) brightness(0);"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle"></i> <strong>Este pedido aguarda a sua resposta</strong></h6>
                    <p class="mb-0">Por favor, aceite ou recuse este pedido de permuta.</p>
                </div>
                
                <table class="table table-sm table-bordered">
                    <tr>
                        <th style="width: 40%;">Solicitante:</th>
                        <td id="detalhe_solicitante"></td>
                    </tr>
                    <tr>
                        <th>Data do Exame:</th>
                        <td id="detalhe_data"></td>
                    </tr>
                    <tr>
                        <th>Hora:</th>
                        <td id="detalhe_hora"></td>
                    </tr>
                    <tr>
                        <th>Prova:</th>
                        <td id="detalhe_prova"></td>
                    </tr>
                    <tr>
                        <th>Sala:</th>
                        <td id="detalhe_sala"></td>
                    </tr>
                    <tr>
                        <th>Estado:</th>
                        <td><span class="badge badge-warning" style="color: #856404;"><i class="fas fa-clock"></i> Aguarda a sua resposta</span></td>
                    </tr>
                </table>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> <strong>Atenção:</strong> Se aceitar esta permuta, ficará convocado para esta vigilância no lugar do professor solicitante.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" id="btnRecusarPermutaModal">
                    <i class="fas fa-times"></i> Recusar
                </button>
                <button type="button" class="btn btn-success" id="btnAceitarPermutaModal">
                    <i class="fas fa-check"></i> Aceitar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ver Detalhes de Convocatória -->
<div class="modal fade" id="modalDetalhesConvocatoria" tabindex="-1" aria-labelledby="modalDetalhesConvocatoriaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="modalDetalhesConvocatoriaLabel"><i class="fas fa-clipboard-list"></i> Detalhes da Convocatória</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <tr>
                        <th style="width: 40%;">Data do Exame:</th>
                        <td id="conv_detalhe_data"></td>
                    </tr>
                    <tr>
                        <th>Hora:</th>
                        <td id="conv_detalhe_hora"></td>
                    </tr>
                    <tr>
                        <th>Prova:</th>
                        <td id="conv_detalhe_prova"></td>
                    </tr>
                    <tr>
                        <th>Fase:</th>
                        <td id="conv_detalhe_fase"></td>
                    </tr>
                    <tr>
                        <th>Sala:</th>
                        <td id="conv_detalhe_sala"></td>
                    </tr>
                    <tr>
                        <th>Função:</th>
                        <td id="conv_detalhe_funcao"></td>
                    </tr>
                    <tr>
                        <th>Estado da Permuta:</th>
                        <td><span id="conv_detalhe_estado_badge"></span></td>
                    </tr>
                </table>
                
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle"></i> <strong>Informação:</strong> Esta convocatória tem uma permuta associada. O estado da permuta é apresentado acima.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Pedir Permuta de Vigilância -->
<div class="modal fade" id="modalPedirPermuta" tabindex="-1" aria-labelledby="modalPedirPermutaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="modalPedirPermutaLabel"><i class="fas fa-exchange-alt"></i> Pedir Permuta de Vigilância</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar" style="filter: invert(1) grayscale(100%) brightness(0);"></button>
            </div>
            <form id="formPedirPermuta">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Convocatória:</strong><br>
                        <span id="infoProva"></span><br>
                        <span id="infoData"></span><br>
                        <span id="infoSala"></span>
                    </div>
                    
                    <input type="hidden" id="convocatoria_id" name="convocatoria_id">
                    <input type="hidden" id="sessao_exame_id" name="sessao_exame_id">
                    
                    <div class="form-group">
                        <label for="user_substituto_id">Professor Substituto <span class="text-danger">*</span></label>
                        <select class="form-control" id="user_substituto_id" name="user_substituto_id" required>
                            <option value="">A carregar professores...</option>
                        </select>
                        <small class="form-text text-muted">Selecione o professor que poderá substituí-lo</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="motivo">Motivo da Permuta <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="4" required 
                                  placeholder="Descreva o motivo do pedido de permuta (mínimo 10 caracteres)"></textarea>
                        <small class="form-text text-muted">Mínimo 10 caracteres</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-paper-plane"></i> Enviar Pedido
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ========================================
// TÉCNICO DASHBOARD - SCRIPTS
// Dashboard para utilizadores níveis 5-7
// ========================================

$(document).ready(function() {
    // Abrir modal de pedir permuta
    $('.btn-pedir-permuta').on('click', function() {
        const convocatoriaId = $(this).data('convocatoria-id');
        const sessaoId = $(this).data('sessao-id');
        const prova = $(this).data('prova');
        const data = $(this).data('data');
        const hora = $(this).data('hora');
        const sala = $(this).data('sala');
        
        // Preencher informações
        $('#convocatoria_id').val(convocatoriaId);
        $('#sessao_exame_id').val(sessaoId);
        $('#infoProva').text('Prova: ' + prova);
        $('#infoData').text('Data: ' + data + ' às ' + hora);
        $('#infoSala').text('Sala: ' + sala);
        
        // Limpar campos
        $('#motivo').val('');
        $('#user_substituto_id').html('<option value="">A carregar...</option>');
        
        // Carregar professores disponíveis
        $.ajax({
            url: '<?= base_url('permutas-vigilancia/professores-disponiveis') ?>/' + sessaoId,
            method: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.professores) {
                    let options = '<option value="">Selecione um professor</option>';
                    response.professores.forEach(function(prof) {
                        options += '<option value="' + prof.id + '">' + prof.name + '</option>';
                    });
                    $('#user_substituto_id').html(options);
                } else {
                    $('#user_substituto_id').html('<option value="">Nenhum professor disponível</option>');
                }
            },
            error: function() {
                $('#user_substituto_id').html('<option value="">Erro ao carregar professores</option>');
            }
        });
        
        const modalElement = document.getElementById('modalPedirPermuta');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    });
    
    // Submeter pedido de permuta
    $('#formPedirPermuta').on('submit', function(e) {
        e.preventDefault();
        
        const motivo = $('#motivo').val().trim();
        if (motivo.length < 10) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'O motivo deve ter no mínimo 10 caracteres'
            });
            return;
        }
        
        const substitutoId = $('#user_substituto_id').val();
        if (!substitutoId) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Selecione um professor substituto'
            });
            return;
        }
        
        const formData = {
            convocatoria_id: $('#convocatoria_id').val(),
            user_substituto_id: substitutoId,
            motivo: motivo
        };
        
        $.ajax({
            url: '<?= base_url('permutas-vigilancia/criar') ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                const modalElement = document.getElementById('modalPedirPermuta');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                    setTimeout(function() {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: response.message || 'Pedido de permuta criado com sucesso',
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message || 'Erro ao criar pedido de permuta'
                            });
                        }
                    }, 300);
                }
            },
            error: function(xhr) {
                const modalElement = document.getElementById('modalPedirPermuta');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                    setTimeout(function() {
                        let errorMsg = 'Erro ao processar pedido';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: errorMsg
                        });
                    }, 300);
                }
            }
        });
    });
    
    // Garantir que os botões de fechar a modal funcionam (Bootstrap 5)
    $('#modalPedirPermuta .btn-close, #modalPedirPermuta [data-bs-dismiss="modal"]').on('click', function() {
        const modalElement = document.getElementById('modalPedirPermuta');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) modal.hide();
    });
    
    // Abrir modal de detalhes de permuta pendente
    $('.btn-ver-detalhes-permuta').on('click', function() {
        const solicitante = $(this).data('solicitante');
        const data = $(this).data('data');
        const hora = $(this).data('hora');
        const prova = $(this).data('prova');
        const sala = $(this).data('sala');
        const permutaId = $(this).data('permuta-id');
        
        // Preencher modal
        $('#detalhe_solicitante').text(solicitante);
        $('#detalhe_data').text(data);
        $('#detalhe_hora').text(hora);
        $('#detalhe_prova').text(prova);
        $('#detalhe_sala').text(sala);
        
        // Guardar ID da permuta nos botões da modal
        $('#btnAceitarPermutaModal').data('permuta-id', permutaId);
        $('#btnRecusarPermutaModal').data('permuta-id', permutaId);
        
        const modalElement = document.getElementById('modalDetalhesPermuta');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    });
    
    // Abrir modal de detalhes de convocatória
    $('.btn-ver-detalhes-convocatoria').on('click', function() {
        const data = $(this).data('data');
        const hora = $(this).data('hora');
        const prova = $(this).data('prova');
        const nomeProva = $(this).data('nome-prova');
        const fase = $(this).data('fase');
        const sala = $(this).data('sala');
        const funcao = $(this).data('funcao');
        const estado = $(this).data('estado');
        const estadoClass = $(this).data('estado-class');
        
        // Preencher modal
        $('#conv_detalhe_data').text(data);
        $('#conv_detalhe_hora').text(hora);
        $('#conv_detalhe_prova').text(prova + ' - ' + nomeProva);
        $('#conv_detalhe_fase').text(fase);
        $('#conv_detalhe_sala').text(sala);
        $('#conv_detalhe_funcao').html('<span class="badge badge-light border text-dark">' + funcao + '</span>');
        
        // Estado da permuta com badge colorido
        let estadoHtml = '';
        if (estadoClass === 'badge-warning') {
            // Para warning, usar texto escuro para melhor contraste
            estadoHtml = '<span class="badge ' + estadoClass + '" style="color: #856404;"><i class="fas fa-exchange-alt"></i> ' + estado + '</span>';
        } else {
            // Para outros estados, usar cores padrão
            estadoHtml = '<span class="badge ' + estadoClass + '"><i class="fas fa-exchange-alt"></i> ' + estado + '</span>';
        }
        $('#conv_detalhe_estado_badge').html(estadoHtml);
        
        const modalElement = document.getElementById('modalDetalhesConvocatoria');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    });
    
    // Aceitar permuta a partir da modal de detalhes
    $('#btnAceitarPermutaModal').on('click', function() {
        const permutaId = $(this).data('permuta-id');
        
        Swal.fire({
            title: 'Aceitar Permuta?',
            text: 'Tem a certeza que pretende aceitar esta permuta? Ficará convocado para esta vigilância.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, aceitar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('permutas-vigilancia/aceitar') ?>/' + permutaId,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        const modalElement = document.getElementById('modalDetalhesPermuta');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                            setTimeout(function() {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Sucesso!',
                                        text: response.message || 'Permuta aceite com sucesso',
                                        confirmButtonText: 'OK'
                                    }).then(function() {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erro',
                                        text: response.message || 'Erro ao aceitar permuta'
                                    });
                                }
                            }, 300);
                        }
                    },
                    error: function(xhr) {
                        const modalElement = document.getElementById('modalDetalhesPermuta');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                            setTimeout(function() {
                                let errorMsg = 'Erro ao processar pedido';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: errorMsg
                                });
                            }, 300);
                        }
                    }
                });
            }
        });
    });
    
    // Recusar permuta a partir da modal de detalhes
    $('#btnRecusarPermutaModal').on('click', function() {
        const permutaId = $(this).data('permuta-id');
        
        Swal.fire({
            title: 'Recusar Permuta?',
            text: 'Tem a certeza que pretende recusar esta permuta?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, recusar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('permutas-vigilancia/recusar') ?>/' + permutaId,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        const modalElement = document.getElementById('modalDetalhesPermuta');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                            setTimeout(function() {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Sucesso!',
                                        text: response.message || 'Permuta recusada com sucesso',
                                        confirmButtonText: 'OK'
                                    }).then(function() {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erro',
                                        text: response.message || 'Erro ao recusar permuta'
                                    });
                                }
                            }, 300);
                        }
                    },
                    error: function(xhr) {
                        const modalElement = document.getElementById('modalDetalhesPermuta');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                            setTimeout(function() {
                                let errorMsg = 'Erro ao processar pedido';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erro',
                                    text: errorMsg
                                });
                            }, 300);
                        }
                    }
                });
            }
        });
    });

    // Gráfico de tickets resolvidos - verificar se o elemento existe
    const chartElement = document.getElementById('chartResolvidos');
    if (chartElement) {
        try {
            const ctx = chartElement.getContext('2d');
            if (!ctx) return;
            
            const chartData = <?= json_encode($chart_data) ?>;
            
            if (!chartData || !chartData.labels || !chartData.data) return;
            
            new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Tickets Resolvidos',
                    data: chartData.data,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
            });
        } catch (error) {
            // Gráfico não pôde ser criado - ignorar silenciosamente
        }
    }

    // Aceitar permuta
    $('.btn-aceitar-permuta').on('click', function() {
        const permutaId = $(this).data('permuta-id');
        const prova = $(this).data('prova');
        
        Swal.fire({
            title: 'Aceitar Permuta?',
            html: `Confirma que aceita substituir na vigilância da prova <strong>${prova}</strong>?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, aceitar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('permutas-vigilancia/aceitar') ?>/' + permutaId,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Aceite!',
                                text: response.message || 'Permuta aceite com sucesso',
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message || 'Erro ao aceitar permuta'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Erro ao processar pedido';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    });

    // Recusar permuta
    $('.btn-recusar-permuta').on('click', function() {
        const permutaId = $(this).data('permuta-id');
        const prova = $(this).data('prova');
        
        Swal.fire({
            title: 'Recusar Permuta?',
            html: `Tem a certeza que pretende recusar a substituição na prova <strong>${prova}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, recusar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('permutas-vigilancia/recusar') ?>/' + permutaId,
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Recusado',
                                text: response.message || 'Permuta recusada',
                                confirmButtonText: 'OK'
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message || 'Erro ao recusar permuta'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Erro ao processar pedido';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    });

    // Confirmar Presença
    $('.btn-confirmar-presenca').on('click', function() {
        const convocatoriaId = $(this).data('convocatoria-id');
        const prova = $(this).data('prova');
        const nomeProva = $(this).data('nome-prova');
        const data = $(this).data('data');
        const hora = $(this).data('hora');

        Swal.fire({
            title: 'Confirmar Presença',
            html: `<p>Confirmar presença para:</p>
                   <p><strong>${prova} - ${nomeProva}</strong></p>
                   <p>${data} às ${hora}</p>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-check"></i> Confirmar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('convocatorias/confirmar-presenca') ?>',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ convocatoria_id: convocatoriaId }),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Confirmado!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Erro ao confirmar presença';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: errorMsg
                        });
                    }
                });
            }
        });
    });
});
</script>

<style>
/* Estilos para badges de estado de permuta e linhas de convocatórias */
.badge-info {
    background-color: #138496 !important;
    color: #ffffff !important;
    font-weight: 500;
}

.badge-success {
    background-color: #28a745;
    color: #fff !important;
}

.badge-danger {
    background-color: #dc3545;
    color: #fff !important;
}

.badge-secondary {
    background-color: #6c757d;
    color: #fff !important;
}

.badge-warning {
    background-color: #ffc107;
    color: #856404 !important;
}

.table-secondary {
    background-color: #e9ecef !important;
}

.table-success {
    background-color: #d4edda !important;
}
</style>

<?= $this->endSection() ?>
