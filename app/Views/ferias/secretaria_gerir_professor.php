<?= $this->extend('layout/master') ?>

<?= $this->section('pageHeader') ?>
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0"><i class="fas fa-user-cog"></i> Gestão de Férias</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('ferias/secretaria') ?>">Gestão de Férias</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('ferias/atribuir') ?>">Atribuir Dias</a></li>
            <li class="breadcrumb-item active"><?= esc($professor['name']) ?></li>
        </ol>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header do Professor -->
<div class="row mb-3">
    <div class="col-12">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="mb-2"><i class="fas fa-user"></i> <?= esc($professor['name']) ?></h3>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>NIF:</strong> <?= esc($professor['NIF']) ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?= esc($professor['email']) ?></p>
                                <p class="mb-1"><strong>Telefone:</strong> <?= $professor['telefone'] ? esc($professor['telefone']) : '<span class="text-muted">Não definido</span>' ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Cód. Funcionário:</strong> <?= $professor['cod_funcionario'] ? esc($professor['cod_funcionario']) : '<span class="text-muted">Não definido</span>' ?></p>
                                <p class="mb-1"><strong>Categoria:</strong> <?= $professor['categoria'] ? '<span class="badge bg-info">' . esc($professor['categoria']) . '</span>' : '<span class="text-muted">Não definido</span>' ?></p>
                                <p class="mb-1"><strong>Escola de Serviço:</strong> <?= isset($professor['escola_nome']) && $professor['escola_nome'] ? esc($professor['escola_nome']) : '<span class="text-muted">Não definido</span>' ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <h5 class="text-muted mb-2">Ano Letivo</h5>
                        <h2 class="text-primary mb-3"><?= $ano_letivo['anoletivo'] ?>/<?= $ano_letivo['anoletivo'] + 1 ?></h2>
                        <a href="<?= base_url('ferias/atribuir') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar à Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumo de Dias -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $saldo['dias_total'] ?? 0 ?></h3>
                <p>Dias Atribuídos</p>
                <?php
                    $diasBase   = $saldo['dias_base']  ?? 0;
                    $diasAjuste = $saldo['dias_ajuste'] ?? 0;
                    $diasExtra  = $saldo['dias_extra']  ?? 0;
                    $parts = [];
                    if ($diasBase > 0)    $parts[] = $diasBase . ' base';
                    if ($diasAjuste != 0) $parts[] = ($diasAjuste > 0 ? '+' : '') . $diasAjuste . ' ano anterior';
                    if ($diasExtra != 0)  $parts[] = ($diasExtra  > 0 ? '+' : '') . $diasExtra  . ' extra';
                    if (count($parts) > 1):
                ?>
                <small class="text-white"><?= implode(' ', $parts) ?></small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $saldo['dias_gastos'] ?? 0 ?></h3>
                <p>Dias Já Marcados</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box <?= ($saldo['dias_desconto_faltas'] ?? 0) > 0 ? 'bg-danger' : 'bg-secondary' ?>">
            <div class="inner">
                <h3><?= number_format($saldo['dias_desconto_faltas'] ?? 0, 1) ?></h3>
                <p>Desconto por Faltas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box <?= ($saldo['dias_disponiveis'] ?? 0) > 0 ? 'bg-success' : (($saldo['dias_disponiveis'] ?? 0) < 0 ? 'bg-danger' : 'bg-secondary') ?>">
            <div class="inner">
                <h3><?= $saldo['dias_disponiveis'] ?? 0 ?></h3>
                <p>Dias Disponíveis</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabs de Gestão -->
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-atribuicao" data-bs-toggle="tab" href="#atribuicao" role="tab">
                            <i class="fas fa-calendar-plus"></i> Atribuição de Dias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-situacoes" data-bs-toggle="tab" href="#situacoes-especiais" role="tab">
                            <i class="fas fa-exclamation-circle"></i> Situações Especiais
                            <?php if (!empty($atribuicao['alinea'])): ?>
                                <span class="badge bg-warning"><?= strtoupper($atribuicao['alinea']) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-faltas" data-bs-toggle="tab" href="#faltas" role="tab">
                            <i class="fas fa-exclamation-triangle"></i> Faltas
                            <?php if (count($faltas) > 0): ?>
                                <span class="badge bg-danger"><?= count($faltas) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-pedidos" data-bs-toggle="tab" href="#pedidos" role="tab">
                            <i class="fas fa-file-alt"></i> Pedidos de Férias
                            <?php if (count($pedidos) > 0): ?>
                                <span class="badge bg-primary"><?= count($pedidos) ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-historico" data-bs-toggle="tab" href="#historico" role="tab">
                            <i class="fas fa-history"></i> Histórico/Logs
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="custom-tabs-content">
                    
                    <!-- TAB: Atribuição de Dias -->
                    <div class="tab-pane fade show active" id="atribuicao" role="tabpanel">
                        <h4><i class="fas fa-calendar-plus"></i> Atribuição de Dias de Férias</h4>
                        <hr>
                        
                        <?php if ($ano_anterior): ?>
                        <!-- Informação do Ano Anterior -->
                        <div class="alert <?= $atribuicao_ano_anterior ? 'alert-info' : ($dias_gozados_anterior > 0 ? 'alert-warning' : 'alert-secondary') ?>">
                            <h5><i class="fas fa-calendar"></i> Ano Letivo Anterior (<?= $ano_anterior['anoletivo'] ?>/<?= $ano_anterior['anoletivo'] + 1 ?>)</h5>
                            <?php if ($atribuicao_ano_anterior): ?>
                                <p class="mb-1"><strong>Dias Atribuídos:</strong> <?= $atribuicao_ano_anterior['dias_total'] ?> 
                                    (Base: <?= $atribuicao_ano_anterior['dias_base'] ?>, 
                                    Ajuste: <?= $atribuicao_ano_anterior['dias_ajuste'] ?>, 
                                    Extra: <?= $atribuicao_ano_anterior['dias_extra'] ?>)</p>
                                <p class="mb-0"><strong>Dias Gozados:</strong> <?= $dias_gozados_anterior ?> | 
                                    <strong>Diferença:</strong> <span class="badge bg-<?= ($atribuicao_ano_anterior['dias_total'] - $dias_gozados_anterior) >= 0 ? 'success' : 'danger' ?>">
                                        <?= $atribuicao_ano_anterior['dias_total'] - $dias_gozados_anterior ?>
                                    </span>
                                </p>
                            <?php elseif ($dias_gozados_anterior > 0): ?>
                                <p class="mb-1"><i class="fas fa-exclamation-triangle"></i> <strong>Sem registo formal de atribuição</strong></p>
                                <p class="mb-1"><strong>Dias Atribuídos Detectados:</strong> <?= $dias_atribuidos_anterior ?></p>
                                <p class="mb-1"><strong>Dias Gozados Detectados:</strong> <?= $dias_gozados_anterior ?></p>
                                <p class="mb-0 text-muted"><small>Os campos abaixo foram preenchidos automaticamente. Ajuste se necessário e guarde a atribuição.</small></p>
                            <?php else: ?>
                                <p class="mb-0">Sem registo de atribuição para o ano anterior</p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Card de Descontos por Faltas -->
                        <?php if (isset($faltas_desconto_ano_atual) && count($faltas_desconto_ano_atual) > 0): ?>
                        <div class="card card-danger card-outline">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-exclamation-triangle"></i> Descontos Aplicados ao Ano Atual (<?= $ano_letivo['anoletivo'] ?>/<?= $ano_letivo['anoletivo'] + 1 ?>)</h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Data da Falta</th>
                                            <th>Tipo de Falta</th>
                                            <th class="text-end">Dias Desconto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalDesconto = 0;
                                        foreach ($faltas_desconto_ano_atual as $falta): 
                                            $totalDesconto += $falta['dias_desconto'];
                                        ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($falta['data_falta'])) ?></td>
                                            <td>
                                                <?= \App\Models\FeriasFaltasDescontoModel::getDescricaoTipoFalta($falta['tipo_falta']) ?>
                                                <?php 
                                                $categoria = \App\Models\FeriasFaltasDescontoModel::getCategoriaFalta($falta['tipo_falta']);
                                                $badgeClass = $categoria === 'justificada' ? 'bg-warning' : 'bg-danger';
                                                ?>
                                                <br><span class="badge <?= $badgeClass ?>"><?= ucfirst($categoria) ?></span>
                                            </td>
                                            <td class="text-end"><span class="badge bg-danger"><?= number_format($falta['dias_desconto'], 1) ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-warning fw-bold">
                                            <td colspan="2" class="text-end">TOTAL DE DESCONTOS:</td>
                                            <td class="text-end"><span class="badge bg-danger"><?= number_format($totalDesconto, 1) ?> dias</span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Card de Breakdown do Saldo -->
                        <?php if ($atribuicao): ?>
                        <div class="card card-primary card-outline mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0"><i class="fas fa-calculator"></i> Cálculo do Saldo de Férias</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td><strong>Dias Base:</strong></td>
                                            <td class="text-end"><?= $saldo['dias_base'] ?? 0 ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>+ Férias por gozar do ano anterior:</strong></td>
                                            <td class="text-end"><?= ($saldo['dias_ajuste'] ?? 0) >= 0 ? '+' : '' ?><?= $saldo['dias_ajuste'] ?? 0 ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>+ Extras/Acertos:</strong></td>
                                            <td class="text-end"><?= ($saldo['dias_extra'] ?? 0) >= 0 ? '+' : '' ?><?= $saldo['dias_extra'] ?? 0 ?></td>
                                        </tr>
                                        <tr class="table-info fw-bold">
                                            <td><strong>= TOTAL ATRIBUÍDO:</strong></td>
                                            <td class="text-end"><?= $saldo['dias_total'] ?? 0 ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>- Dias Já Marcados:</strong></td>
                                            <td class="text-end text-danger">-<?= $saldo['dias_gastos'] ?? 0 ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>- Desconto por Faltas:</strong></td>
                                            <td class="text-end text-danger">-<?= number_format($saldo['dias_desconto_faltas'] ?? 0, 1) ?></td>
                                        </tr>
                                        <tr class="table-success fw-bold">
                                            <td><strong>= DIAS DISPONÍVEIS:</strong></td>
                                            <td class="text-end"><span class="badge bg-<?= ($saldo['dias_disponiveis'] ?? 0) >= 0 ? 'success' : 'danger' ?> fs-6"><?= $saldo['dias_disponiveis'] ?? 0 ?></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Formulário de Atribuição -->
                        <form id="formAtribuicao">
                            <input type="hidden" name="user_nif" value="<?= $professor['NIF'] ?>">
                            <input type="hidden" name="anoletivo_id" value="<?= $ano_letivo['id_anoletivo'] ?>">
                            <input type="hidden" id="atribuicao_id" value="<?= $atribuicao['id'] ?? '' ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-calendar-check"></i> Ano Atual (<?= $ano_letivo['anoletivo'] ?>/<?= $ano_letivo['anoletivo'] + 1 ?>)</h5>
                                    
                                    <div class="mb-3">
                                        <label for="dias_base" class="form-label">Dias Base *</label>
                                        <input type="number" class="form-control" id="dias_base" name="dias_base" 
                                               value="<?= $atribuicao['dias_base'] ?? 22 ?>" min="0" max="30" required
                                               onchange="calcularTotal()">
                                        <small class="text-muted">Normalmente 22 dias por ano</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="dias_ajuste" class="form-label">Férias por gozar do ano anterior</label>
                                        <input type="number" class="form-control" id="dias_ajuste" name="dias_ajuste" 
                                               value="<?= $atribuicao['dias_ajuste'] ?? 0 ?>" min="-30" max="30" readonly
                                               style="background-color: #e9ecef;">
                                        <small class="text-muted">Calculado automaticamente</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="dias_extra" class="form-label">Acertos/Extras</label>
                                        <input type="number" class="form-control" id="dias_extra" name="dias_extra" 
                                               value="<?= $atribuicao['dias_extra'] ?? 0 ?>" min="-30" max="30"
                                               onchange="calcularTotal()">
                                        <small class="text-muted">Ajustes manuais (positivos ou negativos)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5 class="mb-3"><i class="fas fa-calendar-minus"></i> Dados do Ano Anterior (Retroativo)</h5>
                                    
                                    <div class="mb-3">
                                        <label for="dias_atribuidos_anterior" class="form-label">Dias Atribuídos no Ano Anterior</label>
                                        <input type="number" class="form-control" id="dias_atribuidos_anterior" 
                                               value="<?= $dias_atribuidos_anterior ?? ($atribuicao_ano_anterior['dias_total'] ?? 0) ?>" min="0" max="50"
                                               onchange="calcularAjusteRetroativo()">
                                        <small class="text-muted">Total de dias que teve direito no ano anterior</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="dias_gozados_anterior" class="form-label">Dias Gozados no Ano Anterior</label>
                                        <input type="number" class="form-control" id="dias_gozados_anterior" name="dias_gozados_anterior"
                                               value="<?= $dias_gozados_anterior ?? 0 ?>" min="0" max="50"
                                               onchange="calcularAjusteRetroativo()">
                                        <small class="text-muted">Total efetivamente gozado no ano anterior</small>
                                    </div>
                                    
                                    <hr class="my-4">
                                    
                                    <h5 class="mb-3"><i class="fas fa-user-cog"></i> Permissões Especiais</h5>
                                    
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input" id="permite_marcar_fora_periodo" name="permite_marcar_fora_periodo" value="1"
                                               <?= !empty($atribuicao['permite_marcar_fora_periodo']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="permite_marcar_fora_periodo">
                                            <strong>Permitir marcar fora do período</strong>
                                            <small class="text-muted d-block">Professor pode marcar férias fora do período configurado</small>
                                        </label>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input type="checkbox" class="form-check-input" id="obriga_totalidade_dias" name="obriga_totalidade_dias" value="1"
                                               <?= isset($atribuicao['obriga_totalidade_dias']) ? ($atribuicao['obriga_totalidade_dias'] ? 'checked' : '') : 'checked' ?>>
                                        <label class="form-check-label" for="obriga_totalidade_dias">
                                            <strong>Obrigar a marcar totalidade de dias</strong>
                                            <small class="text-muted d-block">Professor deve marcar TODOS os dias disponíveis</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?= $atribuicao['observacoes'] ?? '' ?></textarea>
                            </div>
                            
                            <div class="text-end">
                                <button type="button" class="btn btn-primary btn-lg" onclick="salvarAtribuicao()">
                                    <i class="fas fa-save"></i> Guardar Atribuição
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- TAB: Faltas -->
                    <div class="tab-pane fade" id="faltas" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4><i class="fas fa-exclamation-triangle"></i> Faltas Registadas</h4>
                                <p class="text-muted mb-0">
                                    <small>A descontar em: <?= $ano_letivo['anoletivo'] ?>/<?= $ano_letivo['anoletivo'] + 1 ?>
                                    <?php if (isset($ano_seguinte) && $ano_seguinte): ?>
                                        e <?= $ano_seguinte['anoletivo'] ?>/<?= $ano_seguinte['anoletivo'] + 1 ?>
                                    <?php endif; ?>
                                    </small>
                                </p>
                            </div>
                            <button type="button" class="btn btn-primary" onclick="abrirModalFalta(null)">
                                <i class="fas fa-plus"></i> Registar Nova Falta
                            </button>
                        </div>
                        <hr>
                        
                        <?php if (count($faltas) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Data da Falta</th>
                                        <th>Tipo/Legislação</th>
                                        <th>Dias a Descontar</th>
                                        <th>Ano de Desconto</th>
                                        <th>Motivo</th>
                                        <th>Registado Por</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($faltas as $falta): ?>
                                    <tr>
                                        <td><?= date('d/m/Y', strtotime($falta['data_falta'])) ?></td>
                                        <td>
                                            <?php 
                                            $categoria = \App\Models\FeriasFaltasDescontoModel::getCategoriaFalta($falta['tipo_falta']);
                                            $badgeClass = $categoria === 'justificada' ? 'bg-warning' : 'bg-danger';
                                            ?>
                                            <span class="badge <?= $badgeClass ?>">
                                                <?= \App\Models\FeriasFaltasDescontoModel::getDescricaoTipoFalta($falta['tipo_falta']) ?>
                                            </span>
                                        </td>
                                        <td class="fw-bold"><?= number_format($falta['dias_desconto'], 1) ?> dias</td>
                                        <td><?= $falta['ano_desconto'] ?>/<?= $falta['ano_desconto'] + 1 ?></td>
                                        <td><small><?= esc($falta['motivo']) ?></small></td>
                                        <td><small><?= esc($falta['registado_por_nome'] ?? '-') ?></small></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning me-1"
                                                onclick='abrirModalFalta(<?= json_encode(['id' => $falta['id'], 'data_falta' => $falta['data_falta'], 'tipo_falta' => $falta['tipo_falta'], 'dias_desconto' => $falta['dias_desconto'], 'anoletivo_id_falta' => $falta['anoletivo_id_falta'], 'anoletivo_id_desconto' => $falta['anoletivo_id_desconto'], 'motivo' => $falta['motivo'] ?? '', 'observacoes' => $falta['observacoes'] ?? '']) ?>)'
                                                title="Editar falta">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="eliminarFalta(<?= $falta['id'] ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-danger">
                                        <td colspan="2" class="text-end"><strong>Total (soma faltas):</strong></td>
                                        <td colspan="5">
                                            <strong><?= number_format(array_sum(array_column($faltas, 'dias_desconto')), 1) ?> dias</strong>
                                            <small class="text-muted ms-2">(efectivo a descontar: <?= (int)floor(array_sum(array_column($faltas, 'dias_desconto'))) ?> dia(s))</small>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nenhuma falta registada para este professor nos anos letivos 
                            <?= $ano_letivo['anoletivo'] ?>/<?= $ano_letivo['anoletivo'] + 1 ?>
                            <?php if (isset($ano_seguinte) && $ano_seguinte): ?>
                                e <?= $ano_seguinte['anoletivo'] ?>/<?= $ano_seguinte['anoletivo'] + 1 ?>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- TAB: Pedidos de Férias -->
                    <div class="tab-pane fade" id="pedidos" role="tabpanel">
                        <h4><i class="fas fa-file-alt"></i> Pedidos de Férias</h4>
                        <hr>
                        
                        <?php if (count($pedidos) > 0): ?>
                        <?php foreach ($pedidos as $pedido): ?>
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5 class="mb-0">Pedido #<?= $pedido['id'] ?></h5>
                                        <small class="text-muted">Criado em <?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></small>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <?php
                                        $badgeClass = 'secondary';
                                        $estadoTexto = 'Desconhecido';
                                        switch ($pedido['estado']) {
                                            case 'submetido': $badgeClass = 'info'; $estadoTexto = 'Submetido'; break;
                                            case 'em_aprovacao': $badgeClass = 'warning'; $estadoTexto = 'Em Aprovação'; break;
                                            case 'aprovado': $badgeClass = 'success'; $estadoTexto = 'Aprovado'; break;
                                            case 'rejeitado': $badgeClass = 'danger'; $estadoTexto = 'Rejeitado'; break;
                                            case 'aguarda_assinatura': $badgeClass = 'primary'; $estadoTexto = 'Aguarda Assinatura'; break;
                                            case 'concluido': $badgeClass = 'dark'; $estadoTexto = 'Concluído'; break;
                                            case 'cancelado': $badgeClass = 'secondary'; $estadoTexto = 'Cancelado'; break;
                                        }
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?> fs-6"><?= $estadoTexto ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Total de Dias:</strong> <?= $pedido['total_dias'] ?> dias úteis</p>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($pedido['estado'] === 'aprovado' && $pedido['aprovado_por']): ?>
                                            <p class="mb-1"><strong>Aprovado por:</strong> <?= esc($pedido['nome_aprovador']) ?></p>
                                            <p class="mb-1"><small class="text-muted"><?= date('d/m/Y H:i', strtotime($pedido['aprovado_em'])) ?></small></p>
                                        <?php elseif ($pedido['estado'] === 'rejeitado' && $pedido['rejeitado_por']): ?>
                                            <p class="mb-1"><strong>Rejeitado por:</strong> <?= esc($pedido['nome_rejeitador']) ?></p>
                                            <p class="mb-1"><small class="text-muted"><?= date('d/m/Y H:i', strtotime($pedido['rejeitado_em'])) ?></small></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <?php if (!empty($pedido['periodos'])): ?>
                                <h6 class="mt-3 mb-2">Períodos:</h6>
                                <ul class="list-group">
                                    <?php foreach ($pedido['periodos'] as $periodo): ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?= date('d/m/Y', strtotime($periodo['data_inicio'])) ?> até <?= date('d/m/Y', strtotime($periodo['data_fim'])) ?>
                                        <span class="badge bg-primary float-end"><?= $periodo['dias_uteis'] ?> dias</span>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                <?php endif; ?>
                                
                                <?php if (!empty($pedido['motivo_rejeicao'])): ?>
                                <div class="alert alert-danger mt-3 mb-0">
                                    <strong>Motivo de Rejeição:</strong> <?= esc($pedido['motivo_rejeicao']) ?>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($pedido['requer_acumulacao'])): ?>
                                <!-- ── PAINEL DE ACUMULAÇÃO DE FÉRIAS ── -->
                                <div class="card card-outline card-warning mt-3 mb-0">
                                    <div class="card-header bg-warning text-dark py-2">
                                        <h6 class="mb-0">
                                            <i class="bi bi-file-earmark-text"></i> Pedido de Acumulação de Férias (art.º 89 ECD)
                                            <?php
                                            $ae = $pedido['acumulacao_estado'] ?? 'pendente';
                                            $aeBadge = match($ae) {
                                                'autorizada'     => '<span class="badge bg-success ms-2">Autorizada</span>',
                                                'nao_autorizada' => '<span class="badge bg-danger ms-2">Não Autorizada</span>',
                                                default          => '<span class="badge bg-secondary ms-2">Aguarda Despacho</span>',
                                            };
                                            echo $aeBadge;
                                            ?>
                                        </h6>
                                    </div>
                                    <div class="card-body py-2">
                                        <p class="mb-1">
                                            <strong>Dias sobrantes:</strong> <?= (int)($pedido['dias_sobrantes'] ?? 0) ?>
                                            &nbsp;|&nbsp;
                                            <strong>Motivo:</strong> <?= !empty($pedido['motivo_acumulacao']) ? esc($pedido['motivo_acumulacao']) : '<em class="text-muted">Não indicado</em>' ?>
                                        </p>
                                        <?php if (!empty($pedido['upload_acumulacao_em'])): ?>
                                        <p class="mb-1 small text-success">
                                            <i class="bi bi-check-circle"></i> Documento assinado recebido em <?= date('d/m/Y H:i', strtotime($pedido['upload_acumulacao_em'])) ?>
                                        </p>
                                        <?php else: ?>
                                        <p class="mb-1 small text-warning">
                                            <i class="bi bi-hourglass-split"></i> Aguarda upload do documento assinado pelo professor
                                        </p>
                                        <?php endif; ?>
                                        <?php if (!empty($pedido['acumulacao_despacho_em'])): ?>
                                        <p class="mb-1 small text-muted">
                                            Despacho registado em <?= date('d/m/Y H:i', strtotime($pedido['acumulacao_despacho_em'])) ?>
                                        </p>
                                        <?php endif; ?>
                                        <div class="d-flex gap-2 flex-wrap mt-2">
                                            <?php if (!empty($pedido['doc_acumulacao_pdf'])): ?>
                                            <a href="<?= base_url('ferias/download-acumulacao/' . $pedido['id']) ?>"
                                               class="btn btn-outline-secondary btn-sm" target="_blank">
                                                <i class="bi bi-download"></i> PDF Acumulação
                                            </a>
                                            <?php endif; ?>
                                            <?php if (!empty($pedido['doc_acumulacao_assinado'])): ?>
                                            <a href="<?= base_url('ferias/download-acumulacao-assinado/' . $pedido['id']) ?>"
                                               class="btn btn-outline-success btn-sm" target="_blank">
                                                <i class="bi bi-file-check"></i> Ver Assinado
                                            </a>
                                            <?php endif; ?>
                                            <button class="btn btn-success btn-sm"
                                                    onclick="registarDespachoAcumulacao(<?= $pedido['id'] ?>, 'autorizada')"
                                                    <?= ($ae === 'autorizada') ? 'disabled' : '' ?>>
                                                <i class="bi bi-check-circle"></i> Autorizar
                                            </button>
                                            <button class="btn btn-danger btn-sm"
                                                    onclick="registarDespachoAcumulacao(<?= $pedido['id'] ?>, 'nao_autorizada')"
                                                    <?= ($ae === 'nao_autorizada') ? 'disabled' : '' ?>>
                                                <i class="bi bi-x-circle"></i> Não Autorizar
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm"
                                                    onclick="regenerarPDFAcumulacao(<?= $pedido['id'] ?>)">
                                                <i class="bi bi-arrow-clockwise"></i> Regenerar PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nenhum pedido de férias registado para este professor no ano letivo <?= $ano_letivo['anoletivo'] ?>/<?= $ano_letivo['anoletivo'] + 1 ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- TAB: Histórico -->
                    <div class="tab-pane fade" id="historico" role="tabpanel">
                        <h4><i class="fas fa-history"></i> Histórico de Ações</h4>
                        <hr>
                        
                        <?php if (count($logs) > 0): ?>
                        <div class="timeline">
                            <?php foreach ($logs as $log): ?>
                            <div class="time-label">
                                <span class="bg-primary text-white"><?= date('d/m/Y', strtotime($log['criado_em'])) ?></span>
                            </div>
                            <div>
                                <i class="fas fa-circle bg-info"></i>
                                <div class="timeline-item">
                                    <span class="time"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($log['criado_em'])) ?></span>
                                    <h3 class="timeline-header"><?= esc($log['acao']) ?></h3>
                                    <div class="timeline-body">
                                        <?= nl2br(esc($log['detalhes'])) ?>
                                        <br><small class="text-muted">Por: <?= esc($log['nome_usuario'] ?? 'Sistema') ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <div>
                                <i class="fas fa-clock bg-gray"></i>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Nenhum registo de histórico disponível
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- TAB: Situações Especiais -->
                    <div class="tab-pane fade" id="situacoes-especiais" role="tabpanel">
                        <h4><i class="fas fa-exclamation-circle"></i> Situações Especiais</h4>
                        <hr>

                        <?php if (!empty($atribuicao['alinea'])): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-tag"></i>
                            Este professor tem a alínea <strong><?= strtoupper($atribuicao['alinea']) ?></strong> activa.
                            Os dias atribuídos são: <strong><?= $atribuicao['dias_base'] ?? 0 ?></strong>.
                        </div>
                        <?php endif; ?>

                        <!-- Marcação pela Secretaria -->
                        <div class="card card-outline card-primary mb-4">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><i class="fas fa-calendar-alt"></i> Marcação de Férias pela Secretaria</h5>
                                <button type="button" class="btn btn-light btn-sm" onclick="mostrarFormSecretaria()">
                                    <i class="fas fa-plus"></i> Adicionar Períodos
                                </button>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Para professores que não submeteram o pedido, a secretaria pode definir os períodos de férias directamente, ao abrigo da lei.
                                </p>

                                <!-- Form de adição de períodos (inicialmente oculto) -->
                                <div id="form-sec-ferias" class="d-none">
                                    <div class="card card-secondary card-outline mb-3">
                                        <div class="card-header">
                                            <h6 class="mb-0">Novo Registo de Férias</h6>
                                        </div>
                                        <div class="card-body">
                                            <div id="periodos-container">
                                                <!-- Rows adicionadas dinamicamente -->
                                            </div>
                                            <button type="button" class="btn btn-sm btn-secondary mb-3" onclick="adicionarPeriodoRow()">
                                                <i class="fas fa-plus"></i> Adicionar Período
                                            </button>

                                            <div class="mb-3">
                                                <label class="form-label">Observações / Fundamento Legal</label>
                                                <input type="text" class="form-control" id="obs-sec-ferias" placeholder="Ex: Marcação ao abrigo do art.º 241.º do EFP">
                                            </div>

                                            <div id="resumo-dias-secretaria" class="alert alert-info py-2 d-none mb-2">
                                                <i class="fas fa-calendar-check"></i> <span id="resumo-dias-secretaria-text">A calcular...</span>
                                            </div>
                                            <div class="d-flex gap-2 justify-content-end">
                                                <button type="button" class="btn btn-secondary" onclick="cancelarFormSecretaria()">
                                                    <i class="fas fa-times"></i> Cancelar
                                                </button>
                                                <button type="button" class="btn btn-primary" onclick="guardarFeriasSecretaria()">
                                                    <i class="fas fa-save"></i> Guardar Férias
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Listagem dos pedidos criados pela secretaria -->
                                <?php
                                $pedidosSecretaria = array_filter($pedidos ?? [], fn($p) => empty($p['submetido_em']));
                                ?>
                                <?php if (!empty($pedidosSecretaria)): ?>
                                <div class="table-responsive mt-2">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Pedido #</th>
                                                <th>Períodos</th>
                                                <th class="text-center">Dias</th>
                                                <th>Observações</th>
                                                <th>Criado em</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pedidosSecretaria as $ps): ?>
                                            <tr>
                                                <td><span class="badge bg-success">#<?= $ps['id'] ?></span></td>
                                                <td>
                                                    <?php if (!empty($ps['periodos'])): ?>
                                                        <?php foreach ($ps['periodos'] as $per): ?>
                                                            <span class="badge bg-light text-dark border me-1">
                                                                <?= date('d/m/Y', strtotime($per['data_inicio'])) ?> – <?= date('d/m/Y', strtotime($per['data_fim'])) ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center fw-bold"><?= $ps['total_dias'] ?></td>
                                                <td><small class="text-muted"><?= esc($ps['observacoes_secretaria'] ?? '') ?></small></td>
                                                <td><small><?= date('d/m/Y H:i', strtotime($ps['criado_em'])) ?></small></td>
                                                <td class="text-center" style="white-space:nowrap">
                                                    <button class="btn btn-sm btn-warning me-1"
                                                        onclick='editarPedidoSecretaria(<?= $ps['id'] ?>, <?= json_encode(array_map(fn($p) => ['data_inicio' => $p['data_inicio'], 'data_fim' => $p['data_fim']], $ps['periodos'] ?? [])) ?>, <?= json_encode($ps['observacoes_secretaria'] ?? '') ?>)'
                                                        title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="eliminarPedidoSecretaria(<?= $ps['id'] ?>)" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-light border mt-2">
                                    <i class="fas fa-info-circle text-muted"></i> Não existem períodos registados pela secretaria para este professor.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Alínea -->
                        <div class="card card-outline card-warning mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-tag"></i> Alínea no Mapa de Férias</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="alinea" class="form-label fw-bold">Situação especial a registar</label>
                                    <select class="form-select" id="alinea" name="alinea" onchange="alineaChanged(this.value)">
                                        <option value="" <?= empty($atribuicao['alinea']) ? 'selected' : '' ?>>— Nenhuma (professor com férias normais) —</option>
                                        <option value="a" <?= ($atribuicao['alinea'] ?? '') === 'a' ? 'selected' : '' ?>>a) Junta Médica</option>
                                        <option value="b" <?= ($atribuicao['alinea'] ?? '') === 'b' ? 'selected' : '' ?>>b) Mobilidade Especial</option>
                                        <option value="c" <?= ($atribuicao['alinea'] ?? '') === 'c' ? 'selected' : '' ?>>c) Em Mobilidade</option>
                                        <option value="d" <?= ($atribuicao['alinea'] ?? '') === 'd' ? 'selected' : '' ?>>d) Licença s/ vencimento</option>
                                        <option value="e" <?= ($atribuicao['alinea'] ?? '') === 'e' ? 'selected' : '' ?>>e) Licença ao abrigo do artigo 37.º da Lei n.º 7/2009</option>
                                        <option value="f" <?= ($atribuicao['alinea'] ?? '') === 'f' ? 'selected' : '' ?>>f) Licença ao abrigo do Art.º53 da Lei nº90/2019</option>
                                    </select>
                                    <small class="text-muted">Quando definida, o professor aparece no Mapa de Férias com a letra correspondente e não lhe são atribuídos dias de férias.</small>
                                </div>
                                <div id="aviso-alinea" class="alert alert-warning <?= empty($atribuicao['alinea']) ? 'd-none' : '' ?>">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span id="aviso-alinea-texto">
                                        <?php if (!empty($atribuicao['alinea'])): ?>
                                        Alínea activa — os dias de férias ficam registados com o valor actual.
                                        <?php else: ?>
                                        Alínea activa.
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-warning" onclick="salvarAtribuicao()">
                                        <i class="fas fa-save"></i> Guardar Alínea
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Interrupção de Férias -->
                        <?php
                        $estadosAprovados = ['aprovado', 'aguarda_assinatura', 'assinado', 'concluido'];
                        $pedidosAprovados = array_filter($pedidos ?? [], fn($p) =>
                            in_array($p['estado'], $estadosAprovados) && !empty($p['periodos'])
                        );
                        ?>
                        <div class="card card-outline card-danger mb-4">
                            <div class="card-header bg-danger text-white">
                                <h5 class="card-title mb-0"><i class="fas fa-stop-circle"></i> Interrupção de Férias</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">
                                    Registe o regresso antecipado do docente. O período de férias será truncado na data de regresso e os dias restantes ficam automaticamente disponíveis para nova marcação.
                                </p>

                                <?php if (empty($pedidosAprovados)): ?>
                                <div class="alert alert-light border">
                                    <i class="fas fa-info-circle text-muted"></i> Não existem pedidos aprovados com períodos registados.
                                </div>
                                <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Pedido</th>
                                                <th>Períodos</th>
                                                <th class="text-center">Dias</th>
                                                <th>Data de Regresso</th>
                                                <th>Motivo (opcional)</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pedidosAprovados as $pa): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-success">#<?= $pa['id'] ?></span><br>
                                                    <small class="text-muted"><?= $pa['estado'] ?></small>
                                                </td>
                                                <td>
                                                    <?php foreach ($pa['periodos'] as $per): ?>
                                                        <span class="badge bg-light text-dark border me-1">
                                                            <?= date('d/m/Y', strtotime($per['data_inicio'])) ?> – <?= date('d/m/Y', strtotime($per['data_fim'])) ?>
                                                            <small class="text-muted">(<?= $per['dias_uteis'] ?>d)</small>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td class="text-center fw-bold"><?= $pa['total_dias'] ?></td>
                                                <td style="min-width:150px">
                                                    <?php
                                                    $minDate = min(array_column($pa['periodos'], 'data_inicio'));
                                                    $maxDate = max(array_column($pa['periodos'], 'data_fim'));
                                                    ?>
                                                    <input type="date"
                                                           class="form-control form-control-sm data-regresso"
                                                           id="regresso-<?= $pa['id'] ?>"
                                                           min="<?= $minDate ?>"
                                                           max="<?= $maxDate ?>"
                                                           placeholder="dd/mm/aaaa">
                                                </td>
                                                <td style="min-width:180px">
                                                    <input type="text"
                                                           class="form-control form-control-sm"
                                                           id="motivo-<?= $pa['id'] ?>"
                                                           placeholder="Ex: convocatória urgente">
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-danger"
                                                            onclick="confirmarInterrupcao(<?= $pa['id'] ?>)"
                                                            title="Registar interrupção">
                                                        <i class="fas fa-stop-circle"></i> Interromper
                                                    </button>
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
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Calcular total de dias
function calcularTotal() {
    const diasBase = parseInt($('#dias_base').val()) || 0;
    const diasAjuste = parseInt($('#dias_ajuste').val()) || 0;
    const diasExtra = parseInt($('#dias_extra').val()) || 0;
    const total = diasBase + diasAjuste + diasExtra;
    $('#dias_total_display').val(total);
}

// Calcular ajuste retroativo do ano anterior
function calcularAjusteRetroativo() {
    const diasAtribuidos = parseInt($('#dias_atribuidos_anterior').val()) || 0;
    const diasGozados = parseInt($('#dias_gozados_anterior').val()) || 0;
    const diferenca = diasAtribuidos - diasGozados;
    $('#dias_ajuste').val(diferenca);
    calcularTotal();
}

// Salvar atribuição
function salvarAtribuicao() {
    const formData = {
        user_nif: $('input[name="user_nif"]').val(),
        anoletivo_id: $('input[name="anoletivo_id"]').val(),
        dias_base: $('#dias_base').val(),
        dias_ajuste: $('#dias_ajuste').val(),
        dias_extra: $('#dias_extra').val(),
        dias_gozados_anterior: $('#dias_gozados_anterior').val(),
        dias_atribuidos_anterior: $('#dias_atribuidos_anterior').val(),
        observacoes: $('#observacoes').val(),
        permite_marcar_fora_periodo: $('#permite_marcar_fora_periodo').is(':checked') ? 1 : 0,
        obriga_totalidade_dias: $('#obriga_totalidade_dias').is(':checked') ? 1 : 0,
        alinea: $('#alinea').val() || ''
    };
    
    $.ajax({
        url: '<?= base_url('ferias/salvar-atribuicao') ?>',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Atribuição guardada com sucesso',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: response.message || 'Erro ao guardar atribuição'
                });
            }
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro de comunicação com o servidor'
            });
            console.error(xhr);
        }
    });
}

// Registar nova falta
const emailAtribuicaoEnviado = <?= !empty($email_atribuicao_enviado) ? 'true' : 'false' ?>;

// Abrir modal para registar (faltaExistente=null) ou editar (faltaExistente=objeto com dados)
function abrirModalFalta(faltaExistente) {
    $.ajax({
        url: '<?= base_url('api/anos-letivos') ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarModalFalta(response.data, faltaExistente);
            } else {
                Swal.fire({ icon: 'error', title: 'Erro', text: 'Erro ao carregar anos letivos' });
            }
        },
        error: function() {
            Swal.fire({ icon: 'error', title: 'Erro', text: 'Erro de comunicação com o servidor' });
        }
    });
}

// Mostrar modal para registar ou editar falta
function mostrarModalFalta(anos, faltaExistente) {
    const isEdit = faltaExistente && faltaExistente.id;
    const titulo = isEdit ? 'Editar Falta' : 'Registar Nova Falta';

    const opcoesAnos = anos.map(ano =>
        `<option value="${ano.id_anoletivo}" ${isEdit && ano.id_anoletivo == faltaExistente.anoletivo_id_falta ? 'selected' : ''}>
            ${ano.anoletivo}/${parseInt(ano.anoletivo) + 1}
        </option>`
    ).join('');

    const opcoesAnosDesconto = anos.map(ano =>
        `<option value="${ano.id_anoletivo}" ${
            isEdit
                ? (ano.id_anoletivo == faltaExistente.anoletivo_id_desconto ? 'selected' : '')
                : (ano.id_anoletivo == <?= $ano_letivo['id_anoletivo'] ?> ? 'selected' : '')
        }>
            ${ano.anoletivo}/${parseInt(ano.anoletivo) + 1}
        </option>`
    ).join('');

    const avisoEmail = emailAtribuicaoEnviado
        ? `<div class="alert alert-warning text-start mb-3">
               <i class="fas fa-exclamation-triangle"></i>
               <strong>Atenção:</strong> O professor já recebeu o email com a informação das férias.
               Após guardar, será necessário <strong>reenviar o email</strong> com os dados actualizados.
           </div>`
        : '';

    Swal.fire({
        title: titulo,
        html: `
            <form id="formFalta" class="text-left">
                ${avisoEmail}
                <div class="alert alert-info text-left mb-3">
                    <strong>Professor:</strong> <?= esc($professor['name']) ?><br>
                    <strong>NIF:</strong> <?= $professor['NIF'] ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="anoLetivoFalta">Ano Letivo da Falta *</label>
                            <select class="form-control" id="anoLetivoFalta" required>
                                <option value="">Selecione...</option>
                                ${opcoesAnos}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="dataFalta">Data da Falta *</label>
                            <input type="date" class="form-control" id="dataFalta" required
                                   value="${isEdit ? faltaExistente.data_falta : ''}">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="tipoFalta">Tipo de Falta (Legislação) *</label>
                    <select class="form-control" id="tipoFalta" required>
                        <option value="">Selecione...</option>
                        <optgroup label="Faltas Justificadas">
                            <option value="artigo_102" ${isEdit && faltaExistente.tipo_falta === 'artigo_102' ? 'selected' : ''}>Artigo 102.º do ECD (Justificada por participação)</option>
                            <option value="art_135_n4_ltfp" ${isEdit && faltaExistente.tipo_falta === 'art_135_n4_ltfp' ? 'selected' : ''}>Artigo 135º, nº4 de 35/2014 LTFP</option>
                            <option value="artigo_89_ecd" ${isEdit && faltaExistente.tipo_falta === 'artigo_89_ecd' ? 'selected' : ''}>Artigo 89.º do ECD — Acumulação de férias</option>
                        </optgroup>
                        <optgroup label="Faltas Injustificadas">
                            <option value="injustificada" ${isEdit && faltaExistente.tipo_falta === 'injustificada' ? 'selected' : ''}>Falta Injustificada</option>
                        </optgroup>
                    </select>
                    <small class="form-text text-muted">Base legal da falta — influencia o desconto nas férias</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="diasDesconto">Dias a Descontar *</label>
                            <input type="number" step="0.1" min="0.1" max="365" class="form-control" id="diasDesconto" required
                                   value="${isEdit ? faltaExistente.dias_desconto : '1.0'}">
                            <small class="form-text text-muted">
                                Permite décimas (ex: 0.3, 0.5, 1.5). Só desconta dias inteiros — ex: 0.5+0.8=1.3 → desconta 1 dia.
                            </small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="anoLetivoDesconto">Ano Letivo do Desconto *</label>
                            <select class="form-control" id="anoLetivoDesconto" required>
                                <option value="">Selecione...</option>
                                ${opcoesAnosDesconto}
                            </select>
                            <small class="form-text text-muted">Ano onde será descontado</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="motivo">Motivo/Descrição</label>
                    <textarea class="form-control" id="motivo" rows="2">${isEdit ? (faltaExistente.motivo || '') : ''}</textarea>
                    <small class="form-text text-muted">Descrição do motivo da falta (opcional)</small>
                </div>

                <div class="form-group">
                    <label for="observacoes">Observações</label>
                    <textarea class="form-control" id="observacoes" rows="2">${isEdit ? (faltaExistente.observacoes || '') : ''}</textarea>
                    <small class="form-text text-muted">Informações adicionais (opcional)</small>
                </div>
            </form>
        `,
        width: '700px',
        showCancelButton: true,
        confirmButtonText: `<i class="fas fa-check"></i> ${isEdit ? 'Guardar Alterações' : 'Registar'}`,
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        preConfirm: () => {
            const form = document.getElementById('formFalta');
            if (!form.checkValidity()) {
                Swal.showValidationMessage('Por favor preencha todos os campos obrigatórios');
                return false;
            }
            return {
                id: isEdit ? faltaExistente.id : null,
                user_nif: '<?= $professor['NIF'] ?>',
                anoletivo_id_falta: document.getElementById('anoLetivoFalta').value,
                data_falta: document.getElementById('dataFalta').value,
                tipo_falta: document.getElementById('tipoFalta').value,
                dias_desconto: document.getElementById('diasDesconto').value,
                anoletivo_id_desconto: document.getElementById('anoLetivoDesconto').value,
                motivo: document.getElementById('motivo').value,
                observacoes: document.getElementById('observacoes').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            guardarFalta(result.value);
        }
    });
}

// Guardar falta (criar ou atualizar)
function guardarFalta(dados) {
    const url = dados.id
        ? '<?= base_url('ferias/faltas/atualizar/') ?>' + dados.id
        : '<?= base_url('ferias/faltas/registar') ?>';

    $.ajax({
        url: url,
        type: 'POST',
        data: dados,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message || 'Operação realizada com sucesso',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else if (response.duplicate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Falta já existente nesta data',
                    text: response.message,
                    showCancelButton: true,
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Registar mesmo assim',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        guardarFalta(Object.assign({}, dados, { forcar: 1 }));
                    }
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Erro', text: response.message || 'Erro ao guardar falta' });
            }
        },
        error: function(xhr) {
            const resp = xhr.responseJSON || {};
            Swal.fire({ icon: 'error', title: 'Erro', text: resp.messages?.error || resp.message || 'Erro de comunicação com o servidor' });
            console.error(xhr);
        }
    });
}

// Eliminar falta
function eliminarFalta(id) {
    Swal.fire({
        title: 'Confirmar Eliminação',
        text: 'Tem a certeza que deseja eliminar este registo de falta?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/faltas/eliminar') ?>/' + id,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminada!',
                            text: 'Falta eliminada com sucesso',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: response.message || 'Erro ao eliminar falta'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro de comunicação com o servidor'
                    });
                    console.error(xhr);
                }
            });
        }
    });
}

// Verificar dados obrigatórios do professor
function verificarDadosObrigatorios() {
    const codFuncionario = '<?= $professor['cod_funcionario'] ?? '' ?>';
    const categoria = '<?= $professor['categoria'] ?? '' ?>';
    const escolaServico = '<?= $professor['escola_servico'] ?? '' ?>';
    
    if (!codFuncionario || !categoria || !escolaServico) {
        // Carregar categorias e escolas via AJAX
        $.when(
            $.ajax({ url: '<?= base_url('api/categorias-professores') ?>', type: 'GET', dataType: 'json' }),
            $.ajax({ url: '<?= base_url('api/escolas') ?>', type: 'GET', dataType: 'json' })
        ).done(function(categoriasResponse, escolasResponse) {
            if (categoriasResponse[0].success && escolasResponse[0].success) {
                mostrarModalDadosObrigatorios(codFuncionario, categoria, escolaServico, categoriasResponse[0].data, escolasResponse[0].data);
            } else {
                let erroMsg = 'Erro ao carregar dados: ';
                if (!categoriasResponse[0].success) erroMsg += 'Categorias inválidas. ';
                if (!escolasResponse[0].success) erroMsg += 'Escolas inválidas.';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: erroMsg
                });
            }
        }).fail(function(xhr1, textStatus1, errorThrown1) {
            console.error('Erro ao carregar dados:', {xhr1, textStatus1, errorThrown1});
            
            let errorMessage = 'Erro de comunicação com o servidor';
            if (xhr1.responseJSON && xhr1.responseJSON.messages) {
                errorMessage += ': ' + (xhr1.responseJSON.messages.error || JSON.stringify(xhr1.responseJSON.messages));
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: errorMessage
            });
        });
    }
}

// Mostrar modal de dados obrigatórios
function mostrarModalDadosObrigatorios(codFuncionario, categoriaAtual, escolaAtual, categorias, escolas) {
    const opcoesCategorias = categorias.map(cat => 
        `<option value="${cat.nome}" ${categoriaAtual === cat.nome ? 'selected' : ''}>
            ${cat.nome}
        </option>`
    ).join('');
    
    const opcoesEscolas = escolas.map(escola => 
        `<option value="${escola.id}" ${escolaAtual == escola.id ? 'selected' : ''}>
            ${escola.nome}
        </option>`
    ).join('');
    
    Swal.fire({
        title: 'Dados Obrigatórios em Falta',
        html: `
            <div class="alert alert-warning text-left mb-3">
                <i class="fas fa-exclamation-triangle"></i>
                Por favor, preencha os dados obrigatórios antes de continuar.
            </div>
            <form id="formDadosObrigatorios" class="text-left">
                <div class="form-group">
                    <label for="codFuncionario">Código de Funcionário *</label>
                    <input type="text" class="form-control" id="codFuncionario" 
                           value="${codFuncionario}" required 
                           placeholder="Ex: 123456">
                </div>
                <div class="form-group">
                    <label for="categoria">Categoria *</label>
                    <select class="form-control" id="categoria" required>
                        <option value="">Selecione...</option>
                        ${opcoesCategorias}
                    </select>
                </div>
                <div class="form-group">
                    <label for="escolaServico">Escola de Serviço *</label>
                    <select class="form-control" id="escolaServico" required>
                        <option value="">Selecione...</option>
                        ${opcoesEscolas}
                    </select>
                </div>
            </form>
        `,
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Guardar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        preConfirm: () => {
            const codFunc = $('#codFuncionario').val();
            const cat = $('#categoria').val();
            const escola = $('#escolaServico').val();
            
            if (!codFunc || !cat || !escola) {
                Swal.showValidationMessage('Todos os campos são obrigatórios');
                return false;
            }
            
            return {
                cod_funcionario: codFunc,
                categoria: cat,
                escola_servico: escola
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            guardarDadosObrigatorios(result.value);
        } else if (result.isDismissed) {
            // Redirecionar para lista de atribuição
            window.location.href = '<?= base_url('ferias/atribuir') ?>';
        }
    });
}

// Guardar dados obrigatórios do professor
function guardarDadosObrigatorios(dados) {
    $.ajax({
        url: '<?= base_url('ferias/atualizar-dados-professor/' . $professor['NIF']) ?>',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(dados),
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Dados atualizados com sucesso',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: response.message || 'Erro ao atualizar dados'
                });
            }
        },
        error: function(xhr) {
            console.error('Erro ao atualizar dados:', xhr.responseJSON);
            
            let errorMessage = 'Erro de comunicação com o servidor';
            if (xhr.responseJSON && xhr.responseJSON.messages) {
                errorMessage = xhr.responseJSON.messages.error || errorMessage;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: errorMessage
            });
        }
    });
}

// Inicializar ao carregar
$(document).ready(function() {
    calcularTotal();
    calcularAjusteRetroativo();
    verificarDadosObrigatorios();
    // Inicializar estado da alínea sem recarregar
    alineaChanged($('#alinea').val(), false);
});

// ── Alínea ──────────────────────────────────────────────
function alineaChanged(val, warn) {
    if (warn === undefined) warn = true;
    const temAlinea = val !== '';
    // Mostrar/ocultar aviso
    $('#aviso-alinea').toggleClass('d-none', !temAlinea);
    const campos = ['#dias_base', '#dias_extra'];
    if (temAlinea) {
        if (warn) {
            // Guardar valores atuais para as duas opções
            const diasBaseAtual  = $('#dias_base').val();
            const diasExtraAtual = $('#dias_extra').val();
            Swal.fire({
                icon: 'question',
                title: 'Alínea Activa',
                html: 'Como pretende guardar os dias de férias?',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-times-circle"></i> Definir como 0',
                cancelButtonText:  '<i class="fas fa-lock"></i> Manter dias actuais (' + diasBaseAtual + ')',
                confirmButtonColor: '#dc3545',
                cancelButtonColor:  '#6c757d',
                reverseButtons: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Opção: Definir como 0
                    campos.forEach(id => { $(id).val(0).prop('readonly', true); });
                    $('#aviso-alinea-texto').html('Alínea activa — os dias de férias serão definidos como <strong>0</strong> ao guardar.');
                } else {
                    // Opção: Manter dias actuais (também clicando em X / fora)
                    campos.forEach(id => { $(id).prop('readonly', true); });
                    $('#aviso-alinea-texto').html('Alínea activa — os dias de férias ficam registados com o valor actual (<strong>' + diasBaseAtual + '</strong>).');
                }
                $('#dias_atribuidos_anterior, #dias_gozados_anterior').prop('disabled', true);
                calcularTotal();
            });
        } else {
            // Carga inicial: só bloquear edição, manter valores do BD
            campos.forEach(id => { $(id).prop('readonly', true); });
            $('#dias_atribuidos_anterior, #dias_gozados_anterior').prop('disabled', true);
        }
    } else {
        campos.forEach(id => { $(id).prop('readonly', false); });
        $('#dias_atribuidos_anterior, #dias_gozados_anterior').prop('disabled', false);
        $('#aviso-alinea-texto').text('Alínea activa.');
    }
    calcularTotal();
}

// ── Marcação pela Secretaria ─────────────────────────────
let periodoRowCount = 0;

function mostrarFormSecretaria() {
    $('#form-sec-ferias').removeClass('d-none');
    $('#periodos-container').empty();
    periodoRowCount = 0;
    adicionarPeriodoRow();
}

function cancelarFormSecretaria() {
    $('#form-sec-ferias').addClass('d-none');
    $('#periodos-container').empty();
}

function adicionarPeriodoRow() {
    periodoRowCount++;
    const idx = periodoRowCount;
    const row = `
        <div class="row g-2 mb-2 periodo-row" id="prow-${idx}">
            <div class="col-md-5">
                <label class="form-label form-label-sm">Data Início</label>
                <input type="date" class="form-control form-control-sm periodo-inicio" placeholder="Início"
                       onchange="calcularDiasContainer('#periodos-container', '#resumo-dias-secretaria', '#resumo-dias-secretaria-text')" required>
            </div>
            <div class="col-md-5">
                <label class="form-label form-label-sm">Data Fim</label>
                <input type="date" class="form-control form-control-sm periodo-fim" placeholder="Fim"
                       onchange="calcularDiasContainer('#periodos-container', '#resumo-dias-secretaria', '#resumo-dias-secretaria-text')" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removerPeriodoRow(${idx})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`;
    $('#periodos-container').append(row);
}

function removerPeriodoRow(idx) {
    $(`#prow-${idx}`).remove();
    calcularDiasContainer('#periodos-container', '#resumo-dias-secretaria', '#resumo-dias-secretaria-text');
}

function guardarFeriasSecretaria() {
    const periodos = [];
    $('.periodo-row').each(function() {
        const inicio = $(this).find('.periodo-inicio').val();
        const fim    = $(this).find('.periodo-fim').val();
        if (inicio && fim) periodos.push({ data_inicio: inicio, data_fim: fim });
    });

    if (periodos.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Atenção', text: 'Adicione pelo menos um período.' });
        return;
    }

    const observacoes = $('#obs-sec-ferias').val();
    const nif = <?= $professor['NIF'] ?>;

    $.ajax({
        url: '<?= base_url('ferias/marcar-secretaria/') ?>' + nif,
        type: 'POST',
        data: { periodos: periodos, observacoes: observacoes },
        dataType: 'json',
        success: function(r) {
            if (r.success) {
                Swal.fire({ icon: 'success', title: 'Sucesso!', text: r.message, timer: 2000, showConfirmButton: false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Erro', text: r.message || 'Erro ao guardar' });
            }
        },
        error: function(xhr) {
            const msg = xhr.responseJSON?.message || 'Erro de comunicação';
            Swal.fire({ icon: 'error', title: 'Erro', text: msg });
        }
    });
}

function eliminarPedidoSecretaria(id) {
    Swal.fire({
        icon: 'warning',
        title: 'Eliminar registo?',
        text: 'Esta operação é irreversível.',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sim, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '<?= base_url('ferias/eliminar-periodo-secretaria/') ?>' + id,
            type: 'POST',
            dataType: 'json',
            success: function(r) {
                if (r.success) {
                    Swal.fire({ icon: 'success', title: 'Eliminado!', timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro', text: r.message });
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', title: 'Erro', text: xhr.responseJSON?.message || 'Erro de comunicação' });
            }
        });
    });
}

function confirmarInterrupcao(pedidoId) {
    var dataRegresso = $('#regresso-' + pedidoId).val();
    var motivo       = $('#motivo-' + pedidoId).val();

    if (!dataRegresso) {
        Swal.fire({ icon: 'warning', title: 'Data obrigatória', text: 'Indique a data de regresso antes de continuar.' });
        return;
    }

    // Formatar data para exibição PT
    var partes = dataRegresso.split('-');
    var dataFmt = partes[2] + '/' + partes[1] + '/' + partes[0];

    Swal.fire({
        icon: 'warning',
        title: 'Confirmar interrupção?',
        html: '<p>Vai registar o regresso a <strong>' + dataFmt + '</strong>.</p>' +
              '<p>Os dias de férias após essa data serão devolvidos ao saldo do docente.</p>' +
              '<p><em>Esta operação não pode ser desfeita.</em></p>',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Confirmar interrupção',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '<?= base_url('ferias/interromper-ferias/') ?>' + pedidoId,
            type: 'POST',
            data: { data_regresso: dataRegresso, motivo: motivo },
            dataType: 'json',
            success: function(r) {
                if (r.success) {
                    Swal.fire({ icon: 'success', title: 'Férias interrompidas!', text: r.message, timer: 2500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro', text: r.message });
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', title: 'Erro', text: xhr.responseJSON?.message || 'Erro de comunicação' });
            }
        });
    });
}


// ─── Dias úteis: calcular total para um container de períodos ──────────────
function calcularDiasContainer(containerSel, wrapperSel, textSel) {
    const pairs = [];
    $(`${containerSel} .periodo-row`).each(function() {
        const inicio = $(this).find('.periodo-inicio').val();
        const fim    = $(this).find('.periodo-fim').val();
        if (inicio && fim && inicio <= fim) {
            pairs.push({ data_inicio: inicio, data_fim: fim });
        }
    });

    if (pairs.length === 0) {
        $(wrapperSel).addClass('d-none');
        return;
    }

    $(wrapperSel).removeClass('d-none');
    $(textSel).html('<i class="fas fa-spinner fa-spin"></i> A calcular...');

    const calls = pairs.map(p =>
        $.post('<?= base_url('ferias/calcular-dias') ?>', p).catch(() => ({ success: false, dias_uteis: 0 }))
    );

    Promise.all(calls).then(results => {
        const total = results.reduce((s, r) => s + (r && r.success ? parseInt(r.dias_uteis) : 0), 0);
        $(textSel).html(`Total seleccionado: <strong>${total} dia(s) útil(eis)</strong>`);
    }).catch(() => {
        $(textSel).html('<span class="text-danger">Erro ao calcular</span>');
    });
}

// ─── Editar pedido de férias criado pela secretaria ────────────────────────
let editPeriodoRowCount = 0;

function adicionarEditPeriodoRow(inicio, fim) {
    editPeriodoRowCount++;
    const idx = editPeriodoRowCount;
    const row = `
        <div class="row g-2 mb-2 periodo-row" id="edit-prow-${idx}">
            <div class="col-md-5">
                <label class="form-label form-label-sm">Data Início</label>
                <input type="date" class="form-control form-control-sm periodo-inicio" value="${inicio || ''}"
                       onchange="calcularDiasContainer('#edit-periodos-container','#resumo-dias-edit','#resumo-dias-edit-text')" required>
            </div>
            <div class="col-md-5">
                <label class="form-label form-label-sm">Data Fim</label>
                <input type="date" class="form-control form-control-sm periodo-fim" value="${fim || ''}"
                       onchange="calcularDiasContainer('#edit-periodos-container','#resumo-dias-edit','#resumo-dias-edit-text')" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removerEditPeriodoRow(${idx})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>`;
    $('#edit-periodos-container').append(row);
    calcularDiasContainer('#edit-periodos-container', '#resumo-dias-edit', '#resumo-dias-edit-text');
}

function removerEditPeriodoRow(idx) {
    $(`#edit-prow-${idx}`).remove();
    calcularDiasContainer('#edit-periodos-container', '#resumo-dias-edit', '#resumo-dias-edit-text');
}

function editarPedidoSecretaria(pedidoId, periodos, observacoes) {
    editPeriodoRowCount = 0;

    Swal.fire({
        title: 'Editar Registo de Férias',
        html: `
            <div class="text-start">
                <div id="edit-periodos-container"></div>
                <button type="button" class="btn btn-sm btn-secondary mb-2" onclick="adicionarEditPeriodoRow('','')">
                    <i class="fas fa-plus"></i> Adicionar Período
                </button>
                <div id="resumo-dias-edit" class="alert alert-info py-2 d-none mb-3">
                    <i class="fas fa-calendar-check"></i> <span id="resumo-dias-edit-text"></span>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Observações / Fundamento Legal</label>
                    <input type="text" class="form-control" id="edit-obs-sec-ferias"
                           value="${observacoes || ''}"
                           placeholder="Ex: Marcação ao abrigo do art.º 241.º do EFP">
                </div>
            </div>
        `,
        width: '700px',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-save"></i> Guardar Alterações',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        didOpen: () => {
            periodos.forEach(p => adicionarEditPeriodoRow(p.data_inicio, p.data_fim));
        },
        preConfirm: () => {
            const rows = [];
            $('#edit-periodos-container .periodo-row').each(function() {
                const inicio = $(this).find('.periodo-inicio').val();
                const fim    = $(this).find('.periodo-fim').val();
                if (inicio && fim) rows.push({ data_inicio: inicio, data_fim: fim });
            });
            if (rows.length === 0) {
                Swal.showValidationMessage('Adicione pelo menos um período.');
                return false;
            }
            return {
                periodos: rows,
                observacoes: document.getElementById('edit-obs-sec-ferias').value
            };
        }
    }).then(result => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '<?= base_url('ferias/editar-periodo-secretaria/') ?>' + pedidoId,
            type: 'POST',
            data: result.value,
            dataType: 'json',
            success: function(r) {
                if (r.success) {
                    Swal.fire({ icon: 'success', title: 'Sucesso!', text: r.message, timer: 2000, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro', text: r.message || 'Erro ao guardar' });
                }
            },
            error: function(xhr) {
                Swal.fire({ icon: 'error', title: 'Erro', text: xhr.responseJSON?.message || 'Erro de comunicação' });
            }
        });
    });
}

// ── Despacho de Acumulação de Férias ─────────────────────────────────
function registarDespachoAcumulacao(pedidoId, estado) {
    const label = estado === 'autorizada' ? 'Autorizar' : 'Não Autorizar';
    const icon  = estado === 'autorizada' ? 'success' : 'warning';
    Swal.fire({
        title: `${label} Acumulação?`,
        html: `Confirma que pretende registar o despacho "<strong>${estado === 'autorizada' ? 'Autorizada' : 'Não Autorizada'}</strong>" para o Pedido de Acumulação de Férias #${pedidoId}?`,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: `Confirmar — ${label}`,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: estado === 'autorizada' ? '#28a745' : '#dc3545',
    }).then((result) => {
        if (!result.isConfirmed) return;
        $.ajax({
            url: '<?= base_url('ferias/registar-despacho-acumulacao/') ?>' + pedidoId,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ estado: estado }),
            success: function(response) {
                Swal.fire('Sucesso!', response.message, 'success').then(() => location.reload());
            },
            error: function(xhr) {
                Swal.fire('Erro', xhr.responseJSON?.messages?.error || 'Erro ao registar despacho', 'error');
            }
        });
    });
}

function regenerarPDFAcumulacao(pedidoId) {
    $.post('<?= base_url('ferias/regenerar-pdf-acumulacao/') ?>' + pedidoId)
        .done(function(r) {
            Swal.fire('Sucesso!', r.message || 'PDF regenerado', 'success').then(() => location.reload());
        })
        .fail(function(xhr) {
            Swal.fire('Erro', xhr.responseJSON?.messages?.error || 'Erro ao regenerar PDF', 'error');
        });
}
</script>
<?= $this->endSection() ?>
