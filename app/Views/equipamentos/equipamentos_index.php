<?= $this->extend('layout/master') ?>
<?= $this->section('pageHeader') ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
 
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                        <li class="breadcrumb-item active">Equipamentos</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info boxes -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box">
                        <span class="info-box-icon bg-info elevation-1"><i class="fas fa-laptop"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Equipamentos</span>
                            <span class="info-box-number" id="total-equipamentos">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Ativos</span>
                            <span class="info-box-number" id="equipamentos-ativos">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Fora de Serviço</span>
                            <span class="info-box-number" id="equipamentos-fora-servico">0</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Por Atribuir</span>
                            <span class="info-box-number" id="equipamentos-por-atribuir">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main row -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><?= $page_subtitle ?></h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#equipamentoModal" onclick="openCreateModal()">
                                    <i class="fas fa-plus"></i> Novo Equipamento
                                </button>
                                <button type="button" class="btn btn-info btn-sm" onclick="loadStatistics(true)">
                                    <i class="fas fa-chart-bar"></i> Estatísticas
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filtros -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="filtro_escola" class="form-label">Filtrar por Escola:</label>
                                    <select class="form-select" id="filtro_escola">
                                        <option value="">Todas as Escolas</option>
                                        <?php if (!empty($escolas)): ?>
                                            <?php foreach ($escolas as $escola): ?>
                                                <option value="<?= $escola['id'] ?>"><?= esc($escola['nome']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="filtro_sala" class="form-label">Filtrar por Sala:</label>
                                    <select class="form-select" id="filtro_sala" disabled>
                                        <option value="">Selecione primeiro uma escola</option>
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-secondary" id="btnLimparFiltros">
                                        <i class="fas fa-times"></i> Limpar Filtros
                                    </button>
                                </div>
                            </div>
                            
                            <table id="equipamentosTable" class="table table-bordered table-striped nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Escola</th>
                                        <th>Sala</th>
                                        <th>Tipo</th>
                                        <th>Marca/Modelo</th>
                                        <th>Número de Série</th>
                                        <th>Estado</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para Equipamento -->
<div class="modal fade" id="equipamentoModal" tabindex="-1" aria-labelledby="equipamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipamentoModalLabel">Novo Equipamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="equipamentoForm">
                <div class="modal-body">
                    <input type="hidden" id="equipamento_id" name="equipamento_id">
                    
                    <!-- Seção de Localização -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-map-marker-alt"></i> Localização</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="escola_id" class="form-label">Escola</label>
                                        <select class="form-select" id="escola_id" name="escola_id">
                                            <option value="">Sem atribuição</option>
                                            <?php foreach ($escolas as $escola): ?>
                                                <option value="<?= $escola['id'] ?>"><?= $escola['nome'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-muted">Deixe vazio se o equipamento não tem sala atribuída</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sala_id" class="form-label">Sala</label>
                                        <select class="form-select" id="sala_id" name="sala_id" disabled>
                                            <option value="">Selecione primeiro uma escola</option>
                                        </select>
                                        <small class="text-muted">Selecione a escola primeiro</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="motivo_section" style="display:none;">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="motivo_movimentacao" class="form-label">Motivo da Atribuição/Movimentação</label>
                                        <textarea class="form-control" id="motivo_movimentacao" name="motivo_movimentacao" rows="2" placeholder="Ex: Novo equipamento, Transferência, Substituição..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção de Dados do Equipamento -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-laptop"></i> Dados do Equipamento</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tipo_id" class="form-label">Tipo de Equipamento <span class="text-danger">*</span></label>
                                        <select class="form-select" id="tipo_id" name="tipo_id" required>
                                            <option value="">Selecione um tipo</option>
                                            <?php foreach ($tipos_equipamento as $tipo): ?>
                                                <option value="<?= $tipo['id'] ?>"><?= $tipo['nome'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                        <select class="form-select" id="estado" name="estado" required>
                                            <option value="ativo">Ativo</option>
                                            <option value="fora_servico">Fora de Serviço</option>
                                            <option value="por_atribuir">Por Atribuir</option>
                                            <option value="abate">Abate</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="marca" class="form-label">Marca</label>
                                        <input type="text" class="form-control" id="marca" name="marca">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="modelo" class="form-label">Modelo</label>
                                        <input type="text" class="form-control" id="modelo" name="modelo">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="numero_serie" class="form-label">Número de Série</label>
                                        <input type="text" class="form-control" id="numero_serie" name="numero_serie">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="observacoes" class="form-label">Observações</label>
                                        <textarea class="form-control" id="observacoes" name="observacoes" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="saveButton">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Visualizar Equipamento -->
<div class="modal fade" id="viewEquipamentoModal" tabindex="-1" aria-labelledby="viewEquipamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewEquipamentoModalLabel">Detalhes do Equipamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Sala:</strong>
                        <p id="view_sala"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Tipo:</strong>
                        <p id="view_tipo"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Marca:</strong>
                        <p id="view_marca"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Modelo:</strong>
                        <p id="view_modelo"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Número de Série:</strong>
                        <p id="view_numero_serie"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Estado:</strong>
                        <p id="view_estado"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Observações:</strong>
                        <p id="view_observacoes"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Danger Modal de Confirmação -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminação</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        Tem a certeza que deseja eliminar este equipamento?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmação Mudança com Tickets -->
<div class="modal fade" id="confirmMudancaTicketsModal" tabindex="-1" aria-labelledby="confirmMudancaTicketsLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-warning">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="confirmMudancaTicketsLabel">
            <i class="fas fa-exclamation-triangle"></i> Equipamento com Tickets em Reparação
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar" style="filter: invert(1) grayscale(100%) brightness(0);"></button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Este equipamento tem <strong id="tickets_count_text">0</strong> ticket(s) em reparação.
        </div>
        <p><strong>Sala Atual dos Tickets:</strong> <span id="sala_atual_text" class="badge bg-primary"></span></p>
        <p><strong>Nova Sala:</strong> <span id="sala_nova_text" class="badge bg-success"></span></p>
        <hr>
        <p class="mb-0">Ao continuar, todos os tickets em aberto serão automaticamente atualizados para a nova sala.</p>
        <p class="mt-2 text-muted"><small><i class="fas fa-lightbulb"></i> Esta ação garante que os tickets permanecem sincronizados com a localização do equipamento.</small></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-warning" id="confirmMudancaTicketsBtn">
            <i class="fas fa-check"></i> Continuar com Mudança
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Gestão de Sala -->
<div class="modal fade" id="gerirSalaModal" tabindex="-1" aria-labelledby="gerirSalaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="gerirSalaModalLabel"><i class="fas fa-map-marker-alt"></i> Gerir Localização</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="gerirSalaForm">
                <div class="modal-body">
                    <input type="hidden" id="gerir_equipamento_id" name="equipamento_id">
                    <input type="hidden" id="gerir_action" name="action">
                    
                    <div class="alert alert-info">
                        <strong>Equipamento:</strong> <span id="gerir_equipamento_info"></span>
                    </div>
                    
                    <div id="sala_atual_info" class="alert alert-warning" style="display:none;">
                        <strong>Sala Atual:</strong> <span id="gerir_sala_atual"></span>
                    </div>
                    
                    <div id="nova_localizacao_section">
                        <div class="mb-3">
                            <label for="gerir_escola_id" class="form-label">Escola <span class="text-danger">*</span></label>
                            <select class="form-select" id="gerir_escola_id" name="escola_id" required>
                                <option value="">Selecione uma escola</option>
                                <?php foreach ($escolas as $escola): ?>
                                    <option value="<?= $escola['id'] ?>"><?= $escola['nome'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gerir_sala_id" class="form-label">Sala <span class="text-danger">*</span></label>
                            <select class="form-select" id="gerir_sala_id" name="sala_id" required disabled>
                                <option value="">Selecione primeiro uma escola</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gerir_motivo" class="form-label">Motivo <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="gerir_motivo" name="motivo_movimentacao" rows="3" required placeholder="Ex: Transferência, Avaria, Substituição..."></textarea>
                        </div>
                    </div>
                    
                    <div id="remover_sala_section" style="display:none;">
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> Tem certeza que deseja remover este equipamento da sala atual?
                        </div>
                        <div class="mb-3">
                            <label for="gerir_motivo_remocao" class="form-label">Motivo da Remoção <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="gerir_motivo_remocao" name="motivo_movimentacao" rows="3" placeholder="Ex: Equipamento para reparação, Equipamento obsoleto..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="gerirSalaBtn">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Estatísticas -->
<div class="modal fade" id="estatisticasModal" tabindex="-1" aria-labelledby="estatisticasModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="estatisticasModalLabel"><i class="fas fa-chart-bar"></i> Estatísticas dos Equipamentos</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="estatisticasModalBody">
        <!-- Conteúdo das estatísticas será preenchido por JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Definir baseUrl global
const baseUrl = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/equipamentos.js') ?>"></script>
<?= $this->endSection() ?>
