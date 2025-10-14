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
                        <li class="breadcrumb-item"><a href="<?= site_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active"><?= $title ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">Criar Novo Ticket de Avaria</h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->
                        <form id="novoTicketForm">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="escola_id">Escola <span class="text-danger">*</span></label>
                                    <select class="form-control" id="escola_id" name="escola_id" required>
                                        <option value="">Selecione uma escola</option>
                                        <?php foreach ($escolas as $escola): ?>
                                            <option value="<?= $escola['id'] ?>"><?= esc($escola['nome']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Primeiro selecione a escola.</small>
                                </div>

                                <div class="form-group">
                                    <label for="sala_id">Sala <span class="text-danger">*</span></label>
                                    <select class="form-control" id="sala_id" name="sala_id" required disabled>
                                        <option value="">Selecione primeiro uma escola</option>
                                    </select>
                                    <small class="form-text text-muted">Selecione a sala para carregar os equipamentos.</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="equipamento_id">Equipamento <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <select class="form-control" id="equipamento_id" name="equipamento_id" required disabled>
                                            <option value="">Selecione primeiro uma sala</option>
                                        </select>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" id="btnNovoEquipamento" disabled title="Adicionar novo equipamento">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Se o equipamento não estiver listado, clique no botão "+" para adicionar.</small>
                                </div>

                                <div class="form-group">
                                    <label for="tipo_avaria_id">Tipo de Avaria <span class="text-danger">*</span></label>
                                    <select class="form-control" id="tipo_avaria_id" name="tipo_avaria_id" required>
                                        <option value="">Selecione um tipo de avaria</option>
                                        <?php foreach ($tiposAvaria as $tipoAvaria): ?>
                                            <option value="<?= $tipoAvaria['id'] ?>"><?= esc($tipoAvaria['descricao']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="descricao">Descrição da Avaria <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="descricao" name="descricao" rows="5" placeholder="Descreva detalhadamente a avaria..." required></textarea>
                                    <small class="form-text text-muted">Mínimo de 10 caracteres.</small>
                                </div>
                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Criar Ticket</button>
                                <a href="<?= site_url('/dashboard') ?>" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para adicionar novo equipamento -->
<div class="modal fade" id="equipamentoModal" tabindex="-1" aria-labelledby="equipamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="equipamentoModalLabel">Novo Equipamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="equipamentoForm">
                <div class="modal-body">
                    <input type="hidden" id="modal_sala_id" name="sala_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_id">Tipo de Equipamento <span class="text-danger">*</span></label>
                                <select class="form-control" id="tipo_id" name="tipo_id" required>
                                    <option value="">Selecione um tipo</option>
                                    <?php foreach ($tipos_equipamento as $tipo): ?>
                                        <option value="<?= $tipo['id'] ?>"><?= esc($tipo['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="marca">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modelo">Modelo</label>
                                <input type="text" class="form-control" id="modelo" name="modelo">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero_serie">Número de Série</label>
                                <input type="text" class="form-control" id="numero_serie" name="numero_serie">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="estado">Estado <span class="text-danger">*</span></label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                    <option value="pendente">Pendente</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="data_aquisicao">Data de Aquisição</label>
                                <input type="date" class="form-control" id="data_aquisicao" name="data_aquisicao">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="saveEquipamentoBtn">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Quando a escola é selecionada, carregar as salas dessa escola
    $('#escola_id').on('change', function() {
        var escolaId = $(this).val();
        var $salaSelect = $('#sala_id');
        var $equipamentoSelect = $('#equipamento_id');
        var $btnNovoEquipamento = $('#btnNovoEquipamento');
        
        // Resetar campos dependentes
        $equipamentoSelect.prop('disabled', true).html('<option value="">Selecione primeiro uma sala</option>');
        $btnNovoEquipamento.prop('disabled', true);
        
        if (escolaId) {
            // Habilitar select de salas
            $salaSelect.prop('disabled', false);
            
            // Carregar salas da escola
            $.ajax({
                url: '<?= site_url("salas/getByEscola") ?>/' + escolaId,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $salaSelect.html('<option value="">Carregando...</option>');
                },
                success: function(response) {
                    $salaSelect.html('<option value="">Selecione uma sala</option>');
                    
                    if (response && response.length > 0) {
                        $.each(response, function(index, sala) {
                            $salaSelect.append(
                                $('<option>', {
                                    value: sala.id,
                                    text: sala.codigo_sala
                                })
                            );
                        });
                    } else {
                        $salaSelect.append('<option value="">Nenhuma sala disponível</option>');
                    }
                },
                error: function() {
                    toastr.error('Erro ao carregar salas.');
                    $salaSelect.html('<option value="">Erro ao carregar</option>');
                }
            });
        } else {
            // Desabilitar select de salas
            $salaSelect.prop('disabled', true).html('<option value="">Selecione primeiro uma escola</option>');
        }
    });

    // Quando a sala é selecionada, carregar os equipamentos dessa sala
    $('#sala_id').on('change', function() {
        var salaId = $(this).val();
        var $equipamentoSelect = $('#equipamento_id');
        var $btnNovoEquipamento = $('#btnNovoEquipamento');
        
        if (salaId) {
            // Habilitar o select e o botão
            $equipamentoSelect.prop('disabled', false);
            $btnNovoEquipamento.prop('disabled', false);
            
            // Carregar equipamentos da sala
            $.ajax({
                url: '<?= site_url("equipamentos/getBySala") ?>/' + salaId,
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $equipamentoSelect.html('<option value="">Carregando...</option>');
                },
                success: function(response) {
                    $equipamentoSelect.html('<option value="">Selecione um equipamento</option>');
                    
                    if (response && response.length > 0) {
                        $.each(response, function(index, equipamento) {
                            var label = equipamento.tipo_nome || 'Equipamento';
                            if (equipamento.marca) label += ' - ' + equipamento.marca;
                            if (equipamento.modelo) label += ' ' + equipamento.modelo;
                            if (equipamento.numero_serie) label += ' (SN: ' + equipamento.numero_serie + ')';
                            
                            $equipamentoSelect.append(
                                $('<option>', {
                                    value: equipamento.id,
                                    text: label
                                })
                            );
                        });
                    } else {
                        $equipamentoSelect.append('<option value="">Nenhum equipamento disponível</option>');
                    }
                },
                error: function() {
                    toastr.error('Erro ao carregar equipamentos.');
                    $equipamentoSelect.html('<option value="">Erro ao carregar</option>');
                }
            });
        } else {
            // Desabilitar select e botão
            $equipamentoSelect.prop('disabled', true).html('<option value="">Selecione primeiro uma sala</option>');
            $btnNovoEquipamento.prop('disabled', true);
        }
    });
    
    // Abrir modal para adicionar novo equipamento
    $('#btnNovoEquipamento').on('click', function() {
        var salaId = $('#sala_id').val();
        if (salaId) {
            $('#modal_sala_id').val(salaId);
            $('#equipamentoForm')[0].reset();
            // Bootstrap 5 modal API
            var equipamentoModal = new bootstrap.Modal(document.getElementById('equipamentoModal'));
            equipamentoModal.show();
        }
    });
    
    // Salvar novo equipamento
    $('#equipamentoForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
        $.ajax({
            url: '<?= site_url("equipamentos/create") ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $('#saveEquipamentoBtn').prop('disabled', true).text('Guardando...');
            },
            success: function(response) {
                toastr.success('Equipamento criado com sucesso!');
                
                // Bootstrap 5 modal API
                var equipamentoModal = bootstrap.Modal.getInstance(document.getElementById('equipamentoModal'));
                equipamentoModal.hide();
                
                // Guardar o ID do novo equipamento para seleção automática
                var newEquipamentoId = response.id || response.data?.id;
                
                // Recarregar a lista de equipamentos da sala
                var salaId = $('#sala_id').val();
                var $equipamentoSelect = $('#equipamento_id');
                
                $.ajax({
                    url: '<?= site_url("equipamentos/getBySala") ?>/' + salaId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(equipamentos) {
                        $equipamentoSelect.html('<option value="">Selecione um equipamento</option>');
                        
                        if (equipamentos && equipamentos.length > 0) {
                            $.each(equipamentos, function(index, equipamento) {
                                var label = equipamento.tipo_nome || 'Equipamento';
                                if (equipamento.marca) label += ' - ' + equipamento.marca;
                                if (equipamento.modelo) label += ' ' + equipamento.modelo;
                                if (equipamento.numero_serie) label += ' (SN: ' + equipamento.numero_serie + ')';
                                
                                $equipamentoSelect.append(
                                    $('<option>', {
                                        value: equipamento.id,
                                        text: label
                                    })
                                );
                            });
                            
                            // Selecionar automaticamente o novo equipamento
                            if (newEquipamentoId) {
                                $equipamentoSelect.val(newEquipamentoId);
                            }
                        }
                    }
                });
            },
            error: function(xhr) {
                var response = xhr.responseJSON;
                if (response && response.messages) {
                    if (typeof response.messages === 'object') {
                        $.each(response.messages, function(field, message) {
                            toastr.error(message);
                        });
                    } else {
                        toastr.error(response.messages);
                    }
                } else {
                    toastr.error('Erro ao criar equipamento.');
                }
            },
            complete: function() {
                $('#saveEquipamentoBtn').prop('disabled', false).text('Guardar');
            }
        });
    });
    
    // Submeter formulário de ticket
    $('#novoTicketForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        console.log('Dados do formulário:', formData);
        
        $.ajax({
            url: '<?= site_url("tickets/create") ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                console.log('Enviando requisição...');
                $('button[type="submit"]').prop('disabled', true).text('Criando...');
            },
            success: function(response, textStatus, xhr) {
                console.log('Sucesso!', response);
                console.log('Status HTTP:', xhr.status);
                
                // Se chegou aqui no success, o ticket foi criado com sucesso (status 201)
                toastr.success(response.message || 'Ticket criado com sucesso!');
                
                if (response.warning) {
                    toastr.warning('Atenção: ' + response.message);
                }
                
                $('#novoTicketForm')[0].reset();
                
                setTimeout(function() {
                    window.location.href = '<?= site_url("tickets/meus") ?>';
                }, 2000);
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error('Erro na requisição:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    textStatus: textStatus,
                    errorThrown: errorThrown,
                    response: xhr.responseText
                });
                
                try {
                    var response = JSON.parse(xhr.responseText);
                    
                    // CodeIgniter envia erros em response.messages.error ou response.message
                    if (response.messages && response.messages.error) {
                        if (typeof response.messages.error === 'object') {
                            // Erros de validação múltiplos
                            var errors = [];
                            $.each(response.messages.error, function(field, message) {
                                errors.push(field + ': ' + message);
                            });
                            toastr.error(errors.join('<br>'), 'Erros de Validação');
                        } else {
                            toastr.error(response.messages.error, 'Erro');
                        }
                    } else if (response.message) {
                        toastr.error(response.message, 'Erro');
                    } else {
                        toastr.error('Não foi possível criar o ticket. Por favor, tente novamente.', 'Erro ' + xhr.status);
                    }
                } catch (e) {
                    console.error('Erro ao parsear resposta JSON:', e);
                    
                    if (xhr.status === 0) {
                        toastr.error('Sem conexão com o servidor. Verifique sua internet.', 'Erro de Conexão');
                    } else if (xhr.status === 500) {
                        toastr.error('Erro interno do servidor. Contacte o administrador.', 'Erro 500');
                    } else if (xhr.status === 401 || xhr.status === 403) {
                        toastr.error('Sem permissão. Por favor, faça login novamente.', 'Erro ' + xhr.status);
                        setTimeout(function() {
                            window.location.href = '<?= site_url("login") ?>';
                        }, 2000);
                    } else {
                        toastr.error('Erro desconhecido. Status: ' + xhr.status, 'Erro');
                    }
                }
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).text('Criar Ticket');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
