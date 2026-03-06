<?= $this->extend('layout/master') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard Administrador</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Estatísticas Principais -->
            <div class="row">
                <!-- Total Tickets -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= number_format($stats['total_tickets']) ?></h3>
                            <p>Total de Tickets</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <a href="<?= base_url('tickets/todos') ?>" class="small-box-footer">
                            Ver todos <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Tickets Ativos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= number_format($stats['tickets_ativos']) ?></h3>
                            <p>Tickets Ativos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <a href="<?= base_url('tickets/tratamento') ?>" class="small-box-footer">
                            Ver ativos <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Tickets Críticos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?= number_format($stats['tickets_criticos']) ?></h3>
                            <p>Tickets Críticos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Requer atenção <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <!-- Tickets Resolvidos -->
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= number_format($stats['tickets_resolvidos']) ?></h3>
                            <p>Tickets Resolvidos</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="#" class="small-box-footer">
                            Histórico <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Métricas Adicionais -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-user-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Não Atribuídos</span>
                            <span class="info-box-number"><?= number_format($stats['tickets_nao_atribuidos']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Tempo Médio Resolução</span>
                            <span class="info-box-number"><?= number_format($stats['tempo_medio_resolucao'], 1) ?>h</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-percentage"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Taxa Resolução Hoje</span>
                            <span class="info-box-number"><?= number_format($stats['taxa_resolucao_hoje'], 1) ?>%</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Técnicos Ativos</span>
                            <span class="info-box-number"><?= number_format($stats['total_tecnicos']) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row">
                <!-- Tickets por Estado -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-pie"></i> Tickets por Estado</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="ticketsPorEstadoChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Evolução Últimos 30 Dias -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-chart-line"></i> Evolução de Tickets (30 dias)</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="evolucaoTicketsChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance dos Técnicos -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-trophy"></i> Performance dos Técnicos (Top 10)</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Técnico</th>
                                        <th class="text-center">Total Atribuídos</th>
                                        <th class="text-center">Resolvidos</th>
                                        <th class="text-center">Em Progresso</th>
                                        <th class="text-center">Tempo Médio (h)</th>
                                        <th class="text-center">Taxa de Sucesso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($performance_tecnicos)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Nenhum técnico com tickets atribuídos</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($performance_tecnicos as $tecnico): ?>
                                            <?php 
                                            $taxa_sucesso = $tecnico['total_atribuidos'] > 0 
                                                ? round(($tecnico['resolvidos'] / $tecnico['total_atribuidos']) * 100, 1) 
                                                : 0;
                                            $cor_taxa = $taxa_sucesso >= 80 ? 'success' : ($taxa_sucesso >= 50 ? 'warning' : 'danger');
                                            ?>
                                            <tr>
                                                <td><i class="fas fa-user-cog text-primary"></i> <?= esc($tecnico['tecnico']) ?></td>
                                                <td class="text-center"><span class="badge bg-info"><?= $tecnico['total_atribuidos'] ?></span></td>
                                                <td class="text-center"><span class="badge bg-success"><?= $tecnico['resolvidos'] ?></span></td>
                                                <td class="text-center"><span class="badge bg-warning"><?= $tecnico['em_progresso'] ?></span></td>
                                                <td class="text-center"><?= number_format($tecnico['tempo_medio_horas'], 1) ?>h</td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $cor_taxa ?>"><?= number_format($taxa_sucesso, 1) ?>%</span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas Críticos -->
            <?php if (!empty($tickets_criticos)): ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-danger">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-exclamation-circle"></i> Tickets Críticos Não Atribuídos</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Equipamento</th>
                                            <th>Localização</th>
                                            <th>Tipo de Avaria</th>
                                            <th>Criado</th>
                                            <th>Criado Por</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tickets_criticos as $ticket): ?>
                                            <tr>
                                                <td><strong>#<?= $ticket['id'] ?></strong></td>
                                                <td><?= esc($ticket['equipamento_nome']) ?></td>
                                                <td>
                                                    <?= esc($ticket['escola_nome']) ?><br>
                                                    <small class="text-muted"><?= esc($ticket['codigo_sala']) ?></small>
                                                </td>
                                                <td><?= esc($ticket['tipo_avaria_nome']) ?></td>
                                                <td><?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></td>
                                                <td><?= esc($ticket['criador_nome']) ?></td>
                                                <td>
                                                    <a href="<?= base_url('tickets/view/' . $ticket['id']) ?>" class="btn btn-sm btn-info" title="Ver Detalhes">
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
                </div>
            </div>
            <?php endif; ?>

            <!-- Tickets Antigos e Análises -->
            <div class="row">
                <!-- Tickets Pendentes > 48h -->
                <div class="col-md-6">
                    <div class="card card-warning">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-clock"></i> Tickets Pendentes +48h</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($tickets_antigos)): ?>
                                <p class="text-center text-muted">✓ Nenhum ticket pendente há mais de 48 horas</p>
                            <?php else: ?>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm">
                                        <tbody>
                                            <?php foreach ($tickets_antigos as $ticket): ?>
                                                <tr>
                                                    <td>
                                                        <strong>#<?= $ticket['id'] ?></strong><br>
                                                        <small><?= esc($ticket['equipamento_nome']) ?></small><br>
                                                        <small class="text-muted"><?= esc($ticket['escola_nome']) ?></small>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="badge bg-danger"><?= $ticket['horas_pendente'] ?>h</span><br>
                                                        <a href="<?= base_url('tickets/view/' . $ticket['id']) ?>" class="btn btn-xs btn-info mt-1">
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

                <!-- Escolas com Mais Tickets -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-school"></i> Escolas com Mais Tickets Ativos</h3>
                        </div>
                        <div class="card-body">
                            <?php if (empty($escolas_mais_tickets)): ?>
                                <p class="text-center text-muted">Nenhum ticket ativo no momento</p>
                            <?php else: ?>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-sm">
                                        <tbody>
                                            <?php foreach ($escolas_mais_tickets as $escola): ?>
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-school text-primary"></i>
                                                        <?= esc($escola['escola']) ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <span class="badge bg-info"><?= $escola['total'] ?> tickets</span>
                                                        <?php if ($escola['criticos'] > 0): ?>
                                                            <span class="badge bg-danger"><?= $escola['criticos'] ?> críticos</span>
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
            </div>

            <!-- Análises Adicionais -->
            <div class="row">
                <!-- Tipos de Avaria Mais Frequentes -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-wrench"></i> Tipos de Avaria Mais Frequentes</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo de Avaria</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Tempo Médio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tipos_avaria_frequentes)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Sem dados disponíveis</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($tipos_avaria_frequentes as $tipo): ?>
                                            <tr>
                                                <td><?= esc($tipo['tipo_avaria']) ?></td>
                                                <td class="text-center"><span class="badge bg-info"><?= $tipo['total'] ?></span></td>
                                                <td class="text-center"><?= number_format($tipo['tempo_medio_horas'], 1) ?>h</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Equipamentos Mais Problemáticos -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-laptop-medical"></i> Equipamentos Mais Problemáticos</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead>
                                    <tr>
                                        <th>Tipo de Equipamento</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Pendentes</th>
                                        <th class="text-center">Resolvidos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($equipamentos_problematicos)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Sem dados disponíveis</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($equipamentos_problematicos as $equipamento): ?>
                                            <tr>
                                                <td><?= esc($equipamento['tipo_equipamento']) ?></td>
                                                <td class="text-center"><span class="badge bg-info"><?= $equipamento['total_tickets'] ?></span></td>
                                                <td class="text-center"><span class="badge bg-warning"><?= $equipamento['pendentes'] ?></span></td>
                                                <td class="text-center"><span class="badge bg-success"><?= $equipamento['resolvidos'] ?></span></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

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
                                                <span class="badge badge-success" style="padding: 0.4rem 0.8rem; font-size: 0.875rem;">
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
                                                <a href="<?= base_url('convocatorias') ?>" class="btn btn-xs btn-warning" title="Pedir Permuta">
                                                    <i class="fas fa-exchange-alt"></i> Pedir Permuta
                                                </a>
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
<div class="modal fade" id="modalDetalhesConvocatoria" tabindex="-1" aria-labelledby="modalDetalhesConvocatoriaLabel" aria-hidden="false">
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

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ========================================
// ADMIN DASHBOARD - SCRIPTS
// Dashboard para utilizadores nível 8
// ========================================

$(document).ready(function() {
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
    
    // Gráfico de Pizza - Tickets por Estado
    const ticketsPorEstadoEl = document.getElementById('ticketsPorEstadoChart');
    if (ticketsPorEstadoEl) {
        try {
            const ctxEstado = ticketsPorEstadoEl.getContext('2d');
            if (!ctxEstado) return;
            new Chart(ctxEstado, {
            type: 'pie',
        data: {
            labels: [<?php echo implode(',', array_map(function($item) { return "'" . $item['estado'] . "'"; }, $tickets_por_estado)); ?>],
            datasets: [{
                data: [<?php echo implode(',', array_column($tickets_por_estado, 'total')); ?>],
                backgroundColor: [
                    <?php 
                    foreach ($tickets_por_estado as $item) {
                        $cor = $item['cor'] ?? 'secondary';
                        $corMap = [
                            'primary' => '#007bff',
                            'warning' => '#ffc107',
                            'success' => '#28a745',
                            'danger' => '#dc3545',
                            'info' => '#17a2b8',
                            'secondary' => '#6c757d'
                        ];
                        echo "'" . ($corMap[$cor] ?? '#6c757d') . "',";
                    }
                    ?>
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
            });
            }
        } catch (error) {
            // Gráfico não pôde ser criado - ignorar silenciosamente
        }
    }

    // Gráfico de Linha - Evolução de Tickets
    const evolucaoTicketsEl = document.getElementById('evolucaoTicketsChart');
    if (evolucaoTicketsEl) {
        try {
            const ctxEvolucao = evolucaoTicketsEl.getContext('2d');
            if (!ctxEvolucao) return;
            new Chart(ctxEvolucao, {
        type: 'line',
        data: {
            labels: [<?php echo implode(',', array_map(function($item) { return "'" . date('d/m', strtotime($item['data'])) . "'"; }, $evolucao_tickets)); ?>],
            datasets: [
                {
                    label: 'Criados',
                    data: [<?php echo implode(',', array_column($evolucao_tickets, 'criados')); ?>],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Resolvidos',
                    data: [<?php echo implode(',', array_column($evolucao_tickets, 'resolvidos')); ?>],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
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
/* Estilos para badges de estado de permuta */
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

/* Estilos para linhas de convocatórias */
.table-secondary {
    background-color: #e9ecef !important;
}

.table-success {
    background-color: #d4edda !important;
}
</style>

<?= $this->endSection() ?>
