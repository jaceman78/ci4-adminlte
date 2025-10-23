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
                        <li class="breadcrumb-item"><a href="<?= site_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('tickets/meus') ?>">Meus Tickets</a></li>
                        <li class="breadcrumb-item active">Ticket #<?= $ticket['id'] ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- Coluna Principal -->
                <div class="col-md-8">
                    <!-- Informações do Ticket -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-ticket-alt"></i> Informações do Ticket</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-hashtag"></i> Número:</strong> #<?= esc($ticket['id']) ?></p>
                                    <p><strong><i class="fas fa-calendar-plus"></i> Criado em:</strong> <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></p>
                                    <p><strong><i class="fas fa-calendar-check"></i> Atualizado em:</strong> <?= date('d/m/Y H:i', strtotime($ticket['updated_at'])) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-user"></i> Criado por:</strong> <?= esc($ticket['user_nome']) ?></p>
                                    <p><strong><i class="fas fa-envelope"></i> Email:</strong> <?= esc($ticket['user_email']) ?></p>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <h5><i class="fas fa-info-circle"></i> Estado e Prioridade</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <strong>Estado:</strong> 
                                        <?= getEstadoBadge($ticket['estado'], true) ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>
                                        <strong>Prioridade:</strong> 
                                        <?php
                                        $prioridadeStyle = '';
                                        $prioridadeTexto = '';
                                        switch($ticket['prioridade']) {
                                            case 'baixa':
                                                $prioridadeStyle = 'background-color: #28a745; color: white;';
                                                $prioridadeTexto = 'Baixa';
                                                break;
                                            case 'media':
                                                $prioridadeStyle = 'background-color: #ffc107; color: #000;';
                                                $prioridadeTexto = 'Média';
                                                break;
                                            case 'alta':
                                                $prioridadeStyle = 'background-color: #fd7e14; color: white;';
                                                $prioridadeTexto = 'Alta';
                                                break;
                                            case 'urgente':
                                            case 'critica':
                                                $prioridadeStyle = 'background-color: #dc3545; color: white;';
                                                $prioridadeTexto = 'Urgente';
                                                break;
                                            default:
                                                $prioridadeStyle = 'background-color: #6c757d; color: white;';
                                                $prioridadeTexto = $ticket['prioridade'];
                                        }
                                        ?>
                                        <span class="badge badge-prioridade" style="<?= $prioridadeStyle ?> font-size: 1rem; padding: 0.5rem 1rem; <?= !isEstadoFinal($ticket['estado']) ? 'cursor: pointer;' : 'opacity: 0.7;' ?>" 
                                              data-prioridade="<?= $ticket['prioridade'] ?>"
                                              data-estado="<?= $ticket['estado'] ?>"
                                              title="<?= !isEstadoFinal($ticket['estado']) ? 'Clique para alterar a prioridade' : 'Não é possível alterar prioridade de ticket finalizado' ?>">
                                            <?= $prioridadeTexto ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <?php if (!empty($ticket['atribuido_user_nome'])): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-user-tag"></i> <strong>Atribuído a:</strong> <?= esc($ticket['atribuido_user_nome']) ?> 
                                (<?= esc($ticket['atribuido_user_email']) ?>)
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Descrição do Problema -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-file-alt"></i> Descrição do Problema</h3>
                        </div>
                        <div class="card-body">
                            <p style="white-space: pre-wrap; font-size: 1.1rem;"><?= esc($ticket['descricao']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Coluna Lateral -->
                <div class="col-md-4">
                    <!-- Localização -->
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fas fa-map-marker-alt"></i> Localização</h3>
                        </div>
                        <div class="card-body">
                            <p><strong><i class="fas fa-school"></i> Escola:</strong><br>
                            <?= esc($ticket['escola_nome']) ?></p>
                            
                            <p><strong><i class="fas fa-door-open"></i> Sala:</strong><br>
                            <?= esc($ticket['sala_nome']) ?></p>
                        </div>
                    </div>

                    <!-- Equipamento -->
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title"><i class="fas fa-laptop"></i> Equipamento</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($ticket['equipamento_tipo'])): ?>
                            <p><strong>Tipo:</strong><br>
                            <span class="badge bg-primary text-white" style="font-size: 0.95rem;"><?= esc($ticket['equipamento_tipo']) ?></span></p>
                            <?php endif; ?>
                            
                            <p><strong>Marca/Modelo:</strong><br>
                            <?= esc($ticket['equipamento_marca']) ?> <?= esc($ticket['equipamento_modelo']) ?></p>
                            
                            <?php if (!empty($ticket['equipamento_nserie'])): ?>
                            <p><strong>Nº Série:</strong><br>
                            <code><?= esc($ticket['equipamento_nserie']) ?></code></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Tipo de Avaria -->
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h3 class="card-title"><i class="fas fa-wrench"></i> Tipo de Avaria</h3>
                        </div>
                        <div class="card-body">
                            <h5><?= esc($ticket['tipo_avaria_nome']) ?></h5>
                        </div>
                    </div>

                    <!-- Atribuir Técnico (apenas para nível 8+) -->
                    <?php if (session()->get('level') >= 8 && !isEstadoFinal($ticket['estado'])): ?>
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title"><i class="fas fa-user-cog"></i> Atribuir Técnico</h3>
                        </div>
                        <div class="card-body">
                            <select class="form-control" id="atribuir_tecnico">
                                <option value="">Selecione um técnico</option>
                                <?php foreach ($tecnicos as $tecnico): ?>
                                <option value="<?= $tecnico['id'] ?>" <?= ($ticket['atribuido_user_id'] == $tecnico['id']) ? 'selected' : '' ?>>
                                    <?= esc($tecnico['name']) ?> (Nível <?= $tecnico['level'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <button class="btn btn-primary btn-block mt-2" id="btnAtribuirTecnico">
                                <i class="fas fa-user-check"></i> Atribuir
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Ações -->
                    <div class="card">
                        <div class="card-header bg-secondary">
                            <h3 class="card-title"><i class="fas fa-cogs"></i> Ações</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($ticket['estado'] == 'novo' && $ticket['user_id'] == session()->get('user_id')): ?>
                            <a href="<?= site_url('tickets/meus') ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-edit"></i> Editar Ticket
                            </a>
                            <?php endif; ?>
                            
                            <?php 
                            // Mostrar botões de aceitar/rejeitar se:
                            // 1. Ticket foi atribuído ao usuário atual
                            // 2. Ticket ainda não foi aceite
                            // 3. Ticket não está finalizado
                            if ($ticket['atribuido_user_id'] == session()->get('user_id') 
                                && !$ticket['ticket_aceite'] 
                                && !isEstadoFinal($ticket['estado'])): 
                            ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> Este ticket foi-lhe atribuído. Por favor, aceite ou rejeite.
                            </div>
                            <button class="btn btn-success btn-block mb-2" id="btnAceitarTicket">
                                <i class="fas fa-check"></i> Aceitar Ticket
                            </button>
                            <button class="btn btn-danger btn-block mb-2" id="btnRejeitarTicket">
                                <i class="fas fa-times"></i> Rejeitar Ticket
                            </button>
                            <?php endif; ?>
                            
                            <?php if (session()->get('level') >= 5 && !isEstadoFinal($ticket['estado'])): ?>
                            <button class="btn btn-success btn-block" id="btnResolverTicket">
                                <i class="fas fa-check-circle"></i> Resolver Ticket
                            </button>
                            <?php endif; ?>
                            
                            <?php if (session()->get('level') >= 8 && isEstadoFinal($ticket['estado'])): ?>
                            <button class="btn btn-warning btn-block" id="btnReabrirTicket">
                                <i class="fas fa-redo-alt"></i> Reabrir Ticket
                            </button>
                            <?php endif; ?>
                            
                            <a href="<?= site_url('tickets/meus') ?>" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Voltar aos Meus Tickets
                            </a>
                            
                            <?php if (session()->get('level') >= 5): ?>
                            <a href="<?= site_url('tickets/tratamento') ?>" class="btn btn-info btn-block">
                                <i class="fas fa-tasks"></i> Ir para Tratamento
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Alterar Prioridade -->
<div class="modal fade" id="modalPrioridade" tabindex="-1" aria-labelledby="modalPrioridadeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPrioridadeLabel">Alterar Prioridade</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="nova_prioridade">Nova Prioridade:</label>
                    <select class="form-control" id="nova_prioridade">
                        <option value="baixa">Baixa</option>
                        <option value="media">Média</option>
                        <option value="alta">Alta</option>
                        <option value="critica">Crítica</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnSalvarPrioridade">Salvar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Resolver Ticket -->
<div class="modal fade" id="modalResolverTicket" tabindex="-1" aria-labelledby="modalResolverLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalResolverLabel"><i class="fas fa-check-circle"></i> Resolver Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formResolverTicket">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Preencha as informações sobre a resolução do ticket.
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao_resolucao">Descrição da Resolução <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="descricao_resolucao" name="descricao" rows="5" required
                                  placeholder="Descreva o que foi feito para resolver o problema..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="tempo_gasto">Tempo Gasto (minutos)</label>
                        <input type="number" class="form-control" id="tempo_gasto" name="tempo_gasto_min" min="0"
                               placeholder="Ex: 30">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnConfirmarResolucao">
                        <i class="fas fa-check"></i> Confirmar Resolução
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reabrir Ticket -->
<div class="modal fade" id="modalReabrirTicket" tabindex="-1" aria-labelledby="modalReabrirLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="modalReabrirLabel"><i class="fas fa-redo-alt"></i> Reabrir Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formReabrirTicket">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Este ticket será reaberto e o técnico atribuído será notificado por email.
                    </div>
                    
                    <div class="form-group">
                        <label for="motivo_reabertura">Motivo da Reabertura <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motivo_reabertura" name="motivo" rows="4" required
                                  placeholder="Indique o motivo pelo qual este ticket precisa ser reaberto..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-redo-alt"></i> Confirmar Reabertura
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Rejeitar Ticket -->
<div class="modal fade" id="modalRejeitarTicket" tabindex="-1" aria-labelledby="modalRejeitarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalRejeitarLabel"><i class="fas fa-times-circle"></i> Rejeitar Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formRejeitarTicket">
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Ao rejeitar este ticket, ele voltará a ficar sem técnico atribuído.
                    </div>
                    
                    <div class="form-group">
                        <label for="motivo_rejeicao">Motivo da Rejeição <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="motivo_rejeicao" name="motivo" rows="4" required
                                  placeholder="Por favor, indique o motivo da rejeição..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Confirmar Rejeição
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const ticketId = <?= $ticket['id'] ?>;
    const estadoFinal = <?= isEstadoFinal($ticket['estado']) ? 'true' : 'false' ?>;
    
    // Alterar Prioridade (bloqueia se ticket estiver finalizado)
    $('.badge-prioridade').on('click', function() {
        if (estadoFinal) {
            toastr.warning('Não é possível alterar a prioridade de um ticket finalizado.');
            return;
        }
        
        const prioridadeAtual = $(this).data('prioridade');
        $('#nova_prioridade').val(prioridadeAtual);
        const modal = new bootstrap.Modal(document.getElementById('modalPrioridade'));
        modal.show();
    });
    
    $('#btnSalvarPrioridade').on('click', function() {
        const novaPrioridade = $('#nova_prioridade').val();
        
        $.ajax({
            url: '<?= site_url("tickets/updatePrioridade") ?>',
            type: 'POST',
            data: {
                ticket_id: ticketId,
                prioridade: novaPrioridade
            },
            dataType: 'json',
            beforeSend: function() {
                $('#btnSalvarPrioridade').prop('disabled', true).text('Salvando...');
            },
            success: function(response) {
                console.log('Success response:', response);
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalPrioridade'));
                modal.hide();
                toastr.success('Prioridade atualizada com sucesso!');
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                console.log('Error response:', xhr);
                const response = xhr.responseJSON;
                if (response && response.messages) {
                    toastr.error(response.messages);
                } else {
                    toastr.error('Erro ao atualizar prioridade.');
                }
            },
            complete: function() {
                $('#btnSalvarPrioridade').prop('disabled', false).text('Salvar');
            }
        });
    });
    
    // Atribuir Técnico
    $('#btnAtribuirTecnico').on('click', function() {
        const tecnicoId = $('#atribuir_tecnico').val();
        
        if (!tecnicoId) {
            toastr.warning('Selecione um técnico.');
            return;
        }
        
        $.ajax({
            url: '<?= site_url("tickets/assignTicket") ?>',
            type: 'POST',
            data: {
                ticket_id: ticketId,
                atribuido_user_id: tecnicoId,
                estado: 'em_resolucao'
            },
            dataType: 'json',
            success: function(response) {
                toastr.success('Técnico atribuído com sucesso!');
                setTimeout(() => location.reload(), 1000);
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.messages && response.messages.error) {
                    toastr.error(response.messages.error);
                } else {
                    toastr.error('Erro ao atribuir técnico.');
                }
            }
        });
    });
    
    // Resolver Ticket
    $('#btnResolverTicket').on('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalResolverTicket'));
        modal.show();
    });
    
    $('#formResolverTicket').on('submit', function(e) {
        e.preventDefault();
        
        const descricao = $('#descricao_resolucao').val();
        const tempoGasto = $('#tempo_gasto').val();
        
        if (!descricao.trim()) {
            toastr.warning('Preencha a descrição da resolução.');
            return;
        }
        
        $('#btnConfirmarResolucao').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        
        $.ajax({
            url: '<?= site_url("tickets/resolverTicket") ?>',
            type: 'POST',
            data: {
                ticket_id: ticketId,
                descricao: descricao,
                tempo_gasto_min: tempoGasto
            },
            dataType: 'json',
            success: function(response) {
                toastr.success('Ticket resolvido com sucesso!');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                if (response && response.messages) {
                    toastr.error(response.messages);
                } else {
                    toastr.error('Erro ao resolver ticket.');
                }
                $('#btnConfirmarResolucao').prop('disabled', false).html('<i class="fas fa-check"></i> Confirmar Resolução');
            }
        });
    });
    
    // Reabrir Ticket
    $('#btnReabrirTicket').on('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalReabrirTicket'));
        modal.show();
    });
    
    $('#formReabrirTicket').on('submit', function(e) {
        e.preventDefault();
        
        const motivo = $('#motivo_reabertura').val();
        
        if (!motivo.trim()) {
            toastr.warning('Por favor, indique o motivo da reabertura.');
            return;
        }
        
        const btnSubmit = $(this).find('button[type="submit"]');
        btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Reabrindo...');
        
        $.ajax({
            url: '<?= site_url("tickets/reabrirTicket") ?>',
            type: 'POST',
            data: {
                ticket_id: ticketId,
                motivo: motivo
            },
            dataType: 'json',
            success: function(response) {
                const modalEl = document.getElementById('modalReabrirTicket');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                
                toastr.success(response.message || 'Ticket reaberto com sucesso!');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMsg = 'Erro ao reabrir ticket.';
                
                if (response) {
                    if (response.message) {
                        errorMsg = response.message;
                    } else if (response.messages && typeof response.messages === 'object') {
                        errorMsg = Object.values(response.messages).join('<br>');
                    } else if (response.messages) {
                        errorMsg = response.messages;
                    }
                }
                
                toastr.error(errorMsg);
                btnSubmit.prop('disabled', false).html('<i class="fas fa-redo-alt"></i> Confirmar Reabertura');
            }
        });
    });
    
    // Aceitar Ticket
    $('#btnAceitarTicket').on('click', function() {
        if (!confirm('Tem certeza que deseja aceitar este ticket?')) {
            return;
        }
        
        const btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Aceitando...');
        
        $.ajax({
            url: '<?= site_url("tickets/aceitarTicket") ?>',
            type: 'POST',
            data: {
                ticket_id: ticketId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message || 'Ticket aceite com sucesso!');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(response.message || 'Erro ao aceitar ticket.');
                    btn.prop('disabled', false).html('<i class="fas fa-check"></i> Aceitar Ticket');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMsg = 'Erro ao aceitar ticket.';
                
                if (response && response.message) {
                    errorMsg = response.message;
                } else if (response && response.messages) {
                    errorMsg = typeof response.messages === 'object' 
                        ? Object.values(response.messages).join('<br>') 
                        : response.messages;
                }
                
                toastr.error(errorMsg);
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Aceitar Ticket');
            }
        });
    });
    
    // Rejeitar Ticket
    $('#btnRejeitarTicket').on('click', function() {
        const modal = new bootstrap.Modal(document.getElementById('modalRejeitarTicket'));
        modal.show();
    });
    
    $('#formRejeitarTicket').on('submit', function(e) {
        e.preventDefault();
        
        const motivo = $('#motivo_rejeicao').val();
        
        if (!motivo.trim()) {
            toastr.warning('Por favor, indique o motivo da rejeição.');
            return;
        }
        
        const btnSubmit = $(this).find('button[type="submit"]');
        btnSubmit.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Rejeitando...');
        
        $.ajax({
            url: '<?= site_url("tickets/rejeitarTicket") ?>',
            type: 'POST',
            data: {
                ticket_id: ticketId,
                motivo: motivo
            },
            dataType: 'json',
            success: function(response) {
                const modalEl = document.getElementById('modalRejeitarTicket');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                
                toastr.success(response.message || 'Ticket rejeitado com sucesso!');
                setTimeout(() => location.reload(), 1500);
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMsg = 'Erro ao rejeitar ticket.';
                
                if (response) {
                    if (response.message) {
                        errorMsg = response.message;
                    } else if (response.messages && typeof response.messages === 'object') {
                        errorMsg = Object.values(response.messages).join('<br>');
                    } else if (response.messages) {
                        errorMsg = response.messages;
                    }
                }
                
                toastr.error(errorMsg);
                btnSubmit.prop('disabled', false).html('<i class="fas fa-times"></i> Confirmar Rejeição');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
