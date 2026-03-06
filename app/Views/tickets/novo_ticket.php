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

                                <?php 
                                $userLevel = session()->get('LoggedUserData')['level'] ?? 0;
                                if ($userLevel >= 8): 
                                ?>
                                <div class="form-group">
                                    <button type="button" class="btn btn-info btn-sm" id="btnGerarQRCode" disabled>
                                        <i class="fas fa-qrcode"></i> Gerar QR Code para esta Localização
                                    </button>
                                    <small class="form-text text-muted">Selecione escola e sala para gerar o QR code.</small>
                                </div>
                                <?php endif; ?>
                                
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
                                    <textarea class="form-control" id="descricao" name="descricao" rows="5" placeholder="Descreva detalhadamente a avaria..." required minlength="10"></textarea>
                                    <small class="form-text text-muted">Mínimo de 10 caracteres. <span id="charCount" class="text-info">(0/10)</span></small>
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

<!-- Modal QR Code -->
<div class="modal fade" id="qrcodeModal" tabindex="-1" role="dialog" aria-labelledby="qrcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="qrcodeModalLabel"><i class="fas fa-qrcode"></i> QR Code - Novo Ticket</h5>
                <button type="button" class="close text-white ml-auto" data-dismiss="modal" aria-label="Fechar" style="opacity: 1; margin: 0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <h5 class="text-primary mb-3">Para reportar anomalias/avarias use este QRcode</h5>
                <p class="mb-3"><strong id="qrcodeLocation"></strong></p>
                <div id="qrcodeContainer" class="mb-3"></div>
                <p class="text-muted small">Escaneie este código para abrir o formulário com a escola e sala pré-selecionadas.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btnImprimirQRCode">
                    <i class="fas fa-print"></i> Imprimir
                </button>
                <button type="button" class="btn btn-info" id="btnDownloadQRCode">
                    <i class="fas fa-download"></i> Download
                </button>
            </div>
        </div>
    </div>
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
                                <label for="marca">Marca <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="marca" name="marca" required>
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
                                <label for="estado">Estado</label>
                                <select class="form-control" id="estado" name="estado">
                                    <option value="ativo">Ativo</option>
                                    <option value="inativo">Inativo</option>
                                    <option value="pendente">Pendente</option>
                                </select>
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
<!-- Biblioteca QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
$(document).ready(function() {
    // Variáveis globais para QR Code
    let qrcodeInstance = null;
    let currentQRUrl = '';
    let pendingSalaId = null; // Para preencher após carregar salas
    
    // Verificar se há parâmetros na URL para preencher escola e sala
    const urlParams = new URLSearchParams(window.location.search);
    const escolaIdParam = urlParams.get('escola');
    const salaIdParam = urlParams.get('sala');
    
    if (escolaIdParam && salaIdParam) {
        // Guardar sala para preencher depois
        pendingSalaId = salaIdParam;
        
        // Preencher escola (isso vai disparar o carregamento das salas)
        $('#escola_id').val(escolaIdParam);
        
        // Disparar change manualmente
        setTimeout(function() {
            $('#escola_id').trigger('change');
        }, 100);
    }
    
    // Habilitar/desabilitar botão de gerar QR code
    function checkQRCodeButton() {
        const escolaId = $('#escola_id').val();
        const salaId = $('#sala_id').val();
        
        if (escolaId && salaId) {
            $('#btnGerarQRCode').prop('disabled', false);
        } else {
            $('#btnGerarQRCode').prop('disabled', true);
        }
    }
    
    // Gerar QR Code
    $('#btnGerarQRCode').on('click', function() {
        const escolaId = $('#escola_id').val();
        const salaId = $('#sala_id').val();
        const escolaNome = $('#escola_id option:selected').text();
        const salaNome = $('#sala_id option:selected').text();
        
        if (!escolaId || !salaId) {
            toastr.warning('Selecione escola e sala primeiro!');
            return;
        }
        
        // Criar URL com parâmetros
        currentQRUrl = '<?= site_url("tickets/novo") ?>?escola=' + escolaId + '&sala=' + salaId;
        
        // Limpar container anterior
        $('#qrcodeContainer').empty();
        
        // Gerar novo QR Code maior para impressão (400x400)
        qrcodeInstance = new QRCode(document.getElementById('qrcodeContainer'), {
            text: currentQRUrl,
            width: 400,
            height: 400,
            colorDark: '#000000',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
        
        // Atualizar texto da localização
        $('#qrcodeLocation').text(escolaNome + ' - ' + salaNome);
        
        // Guardar informações para impressão
        window.qrcodePrintData = {
            escola: escolaNome,
            sala: salaNome,
            url: currentQRUrl
        };
        
        // Abrir modal (Bootstrap 4 / AdminLTE)
        $('#qrcodeModal').modal('show');
    });
    
    // Download QR Code
    $('#btnDownloadQRCode').on('click', function() {
        const canvas = $('#qrcodeContainer canvas')[0];
        if (canvas) {
            const url = canvas.toDataURL('image/png');
            const link = document.createElement('a');
            link.download = 'qrcode-ticket-' + Date.now() + '.png';
            link.href = url;
            link.click();
            toastr.success('QR Code baixado com sucesso!');
        }
    });
    
    // Imprimir QR Code em formato A4
    $('#btnImprimirQRCode').on('click', function() {
        const canvas = $('#qrcodeContainer canvas')[0];
        if (!canvas || !window.qrcodePrintData) {
            toastr.error('Erro ao preparar impressão!');
            return;
        }
        
        const qrDataUrl = canvas.toDataURL('image/png');
        const printData = window.qrcodePrintData;
        
        // Criar janela de impressão com layout A4
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>QR Code - Reportar Avarias</title>
                <style>
                    @page {
                        size: A4 portrait;
                        margin: 0;
                    }
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    html, body {
                        width: 100%;
                        height: 100%;
                        margin: 0;
                        padding: 0;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        text-align: center;
                        display: flex;
                        flex-direction: column;
                        justify-content: center;
                        align-items: center;
                        padding: 2cm;
                    }
                    .container {
                        max-width: 17cm;
                        margin: 0 auto;
                    }
                    h1 {
                        color: #17a2b8;
                        font-size: 26pt;
                        margin-bottom: 12px;
                        font-weight: bold;
                    }
                    h2 {
                        color: #333;
                        font-size: 18pt;
                        margin: 8px 0;
                    }
                    .qrcode {
                        margin: 15px auto;
                        display: block;
                        width: 380px;
                        height: 380px;
                    }
                    .instructions {
                        font-size: 12pt;
                        color: #555;
                        margin-top: 25px;
                        text-align: left;
                        line-height: 1.6;
                    }
                    .instructions p {
                        margin: 4px 0;
                    }
                    .instructions strong {
                        display: block;
                        text-align: center;
                        margin-bottom: 8px;
                        font-size: 13pt;
                    }
                    @media print {
                        html, body {
                            width: 210mm;
                            height: 297mm;
                        }
                        body {
                            print-color-adjust: exact;
                            -webkit-print-color-adjust: exact;
                        }
                        @page {
                            margin: 0;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <h1>Para reportar anomalias/avarias use este QRcode</h1>
                    <h2>${printData.escola}</h2>
                    <h2>${printData.sala}</h2>
                    <img src="${qrDataUrl}" alt="QR Code" class="qrcode">
                    <div class="instructions">
                        <p><strong>Como usar:</strong></p>
                        <p>1. Abra a câmera do seu telemóvel e aponte para o QR Code</p>
                        <p>2. Clique na notificação que aparecer para abrir o link</p>
                        <p>3. No formulário, selecione o equipamento com avaria</p>
                        <p>4. Escolha o tipo de avaria e descreva o problema</p>
                        <p>5. Submeta o ticket para ser processado pela equipa técnica</p>
                    </div>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        
        // Aguardar carregamento da imagem e depois imprimir
        setTimeout(function() {
            printWindow.focus();
            printWindow.print();
        }, 500);
    });
    
    // Limpar QR Code ao fechar modal
    $('#qrcodeModal').on('hidden.bs.modal', function() {
        $('#qrcodeContainer').empty();
        qrcodeInstance = null;
    });
    
    // Remover foco ANTES do modal começar a fechar para evitar erro aria-hidden
    $('#qrcodeModal').on('hide.bs.modal', function() {
        // Remover foco do elemento ativo primeiro
        if (document.activeElement && document.activeElement !== document.body) {
            document.activeElement.blur();
        }
        
        // Remover tabindex temporariamente para prevenir foco
        $(this).attr('tabindex', '-1');
        
        // Garantir que nenhum elemento dentro do modal tenha foco
        $(this).find('*').each(function() {
            if (this === document.activeElement) {
                this.blur();
            }
        });
    });
    
    // Garantir que os botões de fechar funcionem
    $('#qrcodeModal .close, #qrcodeModal [data-dismiss="modal"]').off('click').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#qrcodeModal').modal('hide');
    });
    
    // Remover foco de equipamentos
    $('#equipamentoModal').on('hide.bs.modal', function() {
        if (document.activeElement && document.activeElement !== document.body) {
            document.activeElement.blur();
        }
        $(this).attr('tabindex', '-1');
        $(this).find('*').each(function() {
            if (this === document.activeElement) {
                this.blur();
            }
        });
    });
    
    // Contador de caracteres para a descrição
    $('#descricao').on('input', function() {
        var length = $(this).val().length;
        var $charCount = $('#charCount');
        
        if (!$charCount.length) {
            // Se não existe, criar o span
            $(this).next('.form-text').append(' <span id="charCount" class="text-info"></span>');
            $charCount = $('#charCount');
        }
        
        $charCount.text('(' + length + '/10)');
        
        if (length < 10) {
            $charCount.removeClass('text-success').addClass('text-danger');
        } else {
            $charCount.removeClass('text-danger').addClass('text-success');
        }
    });
    
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
                        
                        // Se existe uma sala pendente para preencher (via QR code)
                        if (pendingSalaId) {
                            $salaSelect.val(pendingSalaId).trigger('change');
                            toastr.info('Escola e sala pré-selecionadas pelo QR Code!');
                            pendingSalaId = null; // Limpar após usar
                        }
                    } else {
                        $salaSelect.append('<option value="">Nenhuma sala disponível</option>');
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Erro ao carregar salas.');
                    $salaSelect.html('<option value="">Erro ao carregar</option>');
                }
            });
        } else {
            // Desabilitar select de salas
            $salaSelect.prop('disabled', true).html('<option value="">Selecione primeiro uma escola</option>');
        }
        
        // Verificar botão QR Code
        checkQRCodeButton();
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
        
        // Verificar botão QR Code
        checkQRCodeButton();
    });
    
    // Abrir modal para adicionar novo equipamento
    $('#btnNovoEquipamento').on('click', function() {
        var salaId = $('#sala_id').val();
        if (salaId) {
            $('#modal_sala_id').val(salaId);
            $('#equipamentoForm')[0].reset();
            // Bootstrap 4 / AdminLTE modal
            $('#equipamentoModal').modal('show');
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
                
                // Bootstrap 4 / AdminLTE modal - fechar
                $('#equipamentoModal').modal('hide');
                
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
        
        // Validar descrição antes de enviar
        var descricao = $('#descricao').val().trim();
        if (descricao.length < 10) {
            toastr.warning('A descrição da avaria deve ter pelo menos 10 caracteres.', 'Campo Obrigatório');
            $('#descricao').focus();
            return false;
        }
        
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
                                // Traduzir nomes dos campos para português
                                var fieldNames = {
                                    'equipamento_id': 'Equipamento',
                                    'sala_id': 'Sala',
                                    'tipo_avaria_id': 'Tipo de Avaria',
                                    'descricao': 'Descrição'
                                };
                                var fieldLabel = fieldNames[field] || field;
                                errors.push('<strong>' + fieldLabel + ':</strong> ' + message);
                            });
                            toastr.error(errors.join('<br>'), 'Erros de Validação', {timeOut: 5000});
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
