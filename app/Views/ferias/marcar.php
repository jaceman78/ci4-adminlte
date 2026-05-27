<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1><i class="bi bi-calendar-plus"></i> Marcar Período de Férias</h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <!-- Card com Saldo -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><?= $saldo['dias_total'] ?></h3>
                            <p>Dias Atribuídos</p>
                            <div style="font-size: 0.9em; margin-top: 5px; border-top: 1px solid rgba(255,255,255,0.3); padding-top: 5px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                                    <span>Este ano:</span>
                                    <strong><?= $saldo['dias_base'] ?? 0 ?></strong>
                                </div>
                                <?php 
                                $diasAnosAnteriores = ($saldo['dias_ajuste'] ?? 0) + ($saldo['dias_extra'] ?? 0);
                                if ($diasAnosAnteriores != 0): 
                                ?>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Anos anteriores:</span>
                                    <strong style="<?= $diasAnosAnteriores < 0 ? 'color: #ffcccc;' : '' ?>">
                                        <?= $diasAnosAnteriores >= 0 ? '+' : '' ?><?= $diasAnosAnteriores ?>
                                    </strong>
                                </div>
                                <?php endif; ?>
                                <?php 
                                $diasDescontoFaltas = $saldo['dias_desconto_faltas'] ?? 0;
                                if ($diasDescontoFaltas > 0): 
                                ?>
                                <div style="display: flex; justify-content: space-between; margin-top: 3px;">
                                    <span><small><i class="fas fa-exclamation-triangle"></i> Desc. faltas:</small></span>
                                    <strong style="color: #ffcccc;">
                                        -<?= number_format($diasDescontoFaltas, 1) ?>
                                    </strong>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="icon"><i class="bi bi-calendar-check"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><?= $saldo['dias_gastos'] ?></h3>
                            <p>Dias Já Marcados</p>
                        </div>
                        <div class="icon"><i class="bi bi-calendar-x"></i></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><?= $saldo['dias_disponiveis'] ?></h3>
                            <p>Dias Disponíveis</p>
                        </div>
                        <div class="icon"><i class="bi bi-calendar-heart"></i></div>
                    </div>
                </div>
            </div>

            <?php if (!empty($pedido_pendente)): ?>
            <!-- Alerta de Pedido Pendente -->
            <div class="alert alert-warning alert-dismissible">
                <h5><i class="bi bi-exclamation-triangle"></i> Pedido Pendente</h5>
                <p>Já existe um pedido de férias pendente para este ano letivo (Pedido #<?= $pedido_pendente['id'] ?>).</p>
                <p><strong>Estado:</strong> <?= formatar_estado_ferias($pedido_pendente['estado']) ?></p>
                <p>Deve cancelar o pedido anterior antes de submeter um novo.</p>
                <hr>
                <button class="btn btn-danger" onclick="cancelarPedidoPendente(<?= $pedido_pendente['id'] ?>)">
                    <i class="bi bi-x-circle"></i> Cancelar Pedido Pendente
                </button>
                <a href="<?= base_url('ferias/meus-pedidos') ?>" class="btn btn-info">
                    <i class="bi bi-list"></i> Ver Histórico de Pedidos
                </a>
            </div>
            <?php elseif (!empty($pedido_remarcacao)): ?>
            <!-- Alerta de Remarcação -->
            <?php if ($pedido_remarcacao['estado'] === 'remarcacao_solicitada'): ?>
            <div class="alert alert-warning">
                <h5><i class="bi bi-hourglass-split"></i> Remarcação Pendente</h5>
                <p>Solicitou a remarcação do pedido #<?= $pedido_remarcacao['id'] ?>. <strong>A nova marcação só é possível após a secretaria aprovar o pedido de remarcação.</strong></p>
                <p>Acompanhe o estado na página dos seus pedidos.</p>
                <a href="<?= base_url('ferias/meus-pedidos') ?>" class="btn btn-info btn-sm">
                    <i class="bi bi-list"></i> Ver os meus pedidos
                </a>
            </div>
            <?php else: ?>
            <div class="alert alert-success">
                <h5><i class="bi bi-check-circle"></i> Remarcação Aprovada</h5>
                <p>A remarcação do pedido #<?= $pedido_remarcacao['id'] ?> foi aprovada pela secretaria.</p>
                <p><strong>Para marcar novas férias, confirme primeiro a remarcação na página dos seus pedidos.</strong> Isso cancelará a marcação anterior e libertará os dias.</p>
                <a href="<?= base_url('ferias/meus-pedidos') ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-arrow-clockwise"></i> Ir para os meus pedidos
                </a>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Formulário -->
            <?php $bloqueado = !empty($pedido_pendente) || !empty($pedido_remarcacao); ?>
            <div class="card <?= $bloqueado ? 'opacity-50' : '' ?>">
                <div class="card-header">
                    <h3 class="card-title">Selecionar Períodos</h3>
                    <?php if (!empty($pedido_pendente)): ?>
                    <span class="badge bg-danger float-end">Bloqueado - Pedido Pendente</span>
                    <?php elseif (!empty($pedido_remarcacao)): ?>
                    <span class="badge bg-warning float-end">Bloqueado - Remarcação em curso</span>
                    <?php endif; ?>
                </div>
                <div class="card-body" <?= $bloqueado ? 'style="pointer-events: none;"' : '' ?>>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Instruções:</strong> Adicione os períodos de férias que pretende. 
                        O sistema calculará automaticamente os dias úteis excluindo fins de semana e feriados.
                    </div>

                    <?php if (!empty($configuracao)): ?>
                        <?php if ($configuracao['permite_marcacao']): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-calendar-check"></i>
                                <strong>Período Permitido:</strong> 
                                As férias devem ser marcadas entre 
                                <strong><?= date('d/m/Y', strtotime($configuracao['data_inicio_permitida'])) ?></strong> 
                                e 
                                <strong><?= date('d/m/Y', strtotime($configuracao['data_fim_permitida'])) ?></strong>.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Marcação Bloqueada:</strong> 
                                <?= !empty($configuracao['mensagem_bloqueio']) 
                                    ? $configuracao['mensagem_bloqueio'] 
                                    : 'A marcação de férias está temporariamente suspensa.' ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (!empty($permite_fora_periodo) || !empty($obriga_totalidade)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Permissões Especiais:</strong>
                            <ul class="mb-0 mt-2">
                                <?php if (!empty($permite_fora_periodo)): ?>
                                    <li>✅ Pode marcar férias <strong>fora do período configurado</strong></li>
                                <?php endif; ?>
                                <?php if (empty($obriga_totalidade)): ?>
                                    <li>✅ <strong>Não é obrigado</strong> a marcar a totalidade dos dias disponíveis</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div id="periodosContainer">
                        <!-- Período 1 (inicial) -->
                        <div class="periodo-item card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-5">
                                        <label>Data Início:</label>
                                        <input type="date" class="form-control data-inicio" required
                                               <?php if (!$permite_fora_periodo && !empty($configuracao['data_inicio_permitida'])): ?>
                                               min="<?= $configuracao['data_inicio_permitida'] ?>"
                                               max="<?= $configuracao['data_fim_permitida'] ?>"
                                               <?php endif; ?>>
                                    </div>
                                    <div class="col-md-5">
                                        <label>Data Fim:</label>
                                        <input type="date" class="form-control data-fim" required
                                               <?php if (!$permite_fora_periodo && !empty($configuracao['data_inicio_permitida'])): ?>
                                               min="<?= $configuracao['data_inicio_permitida'] ?>"
                                               max="<?= $configuracao['data_fim_permitida'] ?>"
                                               <?php endif; ?>>
                                    </div>
                                    <div class="col-md-2">
                                        <label>Dias Úteis:</label>
                                        <input type="text" class="form-control dias-uteis" readonly value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary mb-3" onclick="adicionarPeriodo()">
                        <i class="bi bi-plus-circle"></i> Adicionar Período
                    </button>

                    <div class="form-group">
                        <label>Morada em período de Férias: <span class="text-danger">*</span></label>
                        <textarea id="observacoes" class="form-control" rows="3" 
                                  placeholder="Indique a morada onde estará contactável durante as férias..." required></textarea>
                        <small class="text-muted">Campo obrigatório</small>
                    </div>

                    <div class="alert alert-secondary">
                        <strong>Total de dias selecionados:</strong> 
                        <span id="totalDias" class="badge bg-primary">0</span> dias úteis
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-success" onclick="submeterPedido()" <?= $bloqueado ? 'disabled' : '' ?>>
                        <i class="bi bi-check-circle"></i> Submeter Pedido
                    </button>
                    <a href="<?= base_url('ferias') ?>" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let contadorPeriodos = 1;
const diasDisponiveis = <?= $saldo['dias_disponiveis'] ?>;
const obrigaTotalidade = <?= !empty($obriga_totalidade) ? 'true' : 'false' ?>;
const permiteForaPeriodo = <?= $permite_fora_periodo ? 'true' : 'false' ?>;
const dataMinPermitida = <?= !$permite_fora_periodo && !empty($configuracao['data_inicio_permitida']) ? '"' . $configuracao['data_inicio_permitida'] . '"' : 'null' ?>;
const dataMaxPermitida = <?= !$permite_fora_periodo && !empty($configuracao['data_fim_permitida']) ? '"' . $configuracao['data_fim_permitida'] . '"' : 'null' ?>;
let feriadosArray = [];

// Carregar feriados ao inicializar a página
$(document).ready(function() {
    carregarFeriados();
    $('.data-inicio, .data-fim').on('change', validarECalcularDias);
});

// Carregar feriados do servidor
function carregarFeriados() {
    $.get('<?= base_url('ferias/obter-feriados') ?>')
        .done(function(response) {
            if (response.success) {
                feriadosArray = response.feriados;
            }
        })
        .fail(function() {
            console.warn('Erro ao carregar feriados');
        });
}

// Verificar se data é fim de semana
function isFimDeSemana(dateString) {
    const date = new Date(dateString + 'T00:00:00');
    const dayOfWeek = date.getDay();
    return dayOfWeek === 0 || dayOfWeek === 6; // 0 = Domingo, 6 = Sábado
}

// Verificar se data é feriado
function isFeriado(dateString) {
    return feriadosArray.includes(dateString);
}

// Validar data selecionada e calcular dias úteis
function validarECalcularDias() {
    const input = $(this);
    const valor = input.val();
    
    if (!valor) {
        return;
    }
    
    // Verificar se é fim de semana
    if (isFimDeSemana(valor)) {
        Swal.fire({
            icon: 'warning',
            title: 'Fim de Semana',
            text: 'Não pode selecionar sábados ou domingos para férias.',
            confirmButtonText: 'OK'
        }).then(() => {
            input.val('');
        });
        return;
    }
    
    // Verificar se é feriado
    if (isFeriado(valor)) {
        Swal.fire({
            icon: 'warning',
            title: 'Feriado',
            text: 'Não pode selecionar um feriado para férias.',
            confirmButtonText: 'OK'
        }).then(() => {
            input.val('');
        });
        return;
    }
    
    // Se passou nas validações, calcular dias úteis
    calcularDiasUteis.call(this);
}

function adicionarPeriodo() {
    contadorPeriodos++;
    const minMaxAttrs = (!permiteForaPeriodo && dataMinPermitida) 
        ? `min="${dataMinPermitida}" max="${dataMaxPermitida}"`
        : '';
    
    const html = `
        <div class="periodo-item card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        <label>Data Início:</label>
                        <input type="date" class="form-control data-inicio" required ${minMaxAttrs}>
                    </div>
                    <div class="col-md-5">
                        <label>Data Fim:</label>
                        <input type="date" class="form-control data-fim" required ${minMaxAttrs}>
                    </div>
                    <div class="col-md-2">
                        <label>Dias Úteis:</label>
                        <input type="text" class="form-control dias-uteis" readonly value="0">
                        <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removerPeriodo(this)">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    $('#periodosContainer').append(html);
    $('.data-inicio, .data-fim').off('change').on('change', validarECalcularDias);
}

function removerPeriodo(btn) {
    $(btn).closest('.periodo-item').remove();
    calcularTotalDias();
}

function verificarOverlapDatas(periodoAtual, dataInicio, dataFim) {
    // Converter strings para objetos Date para comparação
    const inicio = new Date(dataInicio);
    const fim = new Date(dataFim);
    
    // Validar se data fim é maior que data início
    if (fim < inicio) {
        return true; // Retorna erro se a data fim for anterior à data início
    }
    
    let temOverlap = false;
    
    // Percorrer todos os períodos para verificar sobreposição
    $('.periodo-item').each(function() {
        // Ignorar o período atual que está sendo editado
        if ($(this).is(periodoAtual)) {
            return true; // continue
        }
        
        const outroInicio = $(this).find('.data-inicio').val();
        const outroFim = $(this).find('.data-fim').val();
        
        // Se o outro período não tem datas preenchidas, ignorar
        if (!outroInicio || !outroFim) {
            return true; // continue
        }
        
        const outroInicioDate = new Date(outroInicio);
        const outroFimDate = new Date(outroFim);
        
        // Verificar sobreposição: dois períodos sobrepõem-se se
        // inicio <= outroFim AND fim >= outroInicio
        if (inicio <= outroFimDate && fim >= outroInicioDate) {
            temOverlap = true;
            return false; // break
        }
    });
    
    return temOverlap;
}

function calcularDiasUteis() {
    const periodoCard = $(this).closest('.periodo-item');
    const dataInicio = periodoCard.find('.data-inicio').val();
    const dataFim = periodoCard.find('.data-fim').val();
    
    if (!dataInicio || !dataFim) {
        periodoCard.find('.dias-uteis').val('0');
        calcularTotalDias();
        return;
    }
    
    // Verificar se há overlap com outros períodos
    if (verificarOverlapDatas(periodoCard, dataInicio, dataFim)) {
        Swal.fire({
            icon: 'error',
            title: 'Datas sobrepostas!',
            text: 'Este período sobrepõe-se com outro já selecionado. Por favor, escolha datas diferentes.',
            confirmButtonText: 'OK'
        });
        // Limpar os campos do período atual
        periodoCard.find('.data-inicio').val('');
        periodoCard.find('.data-fim').val('');
        periodoCard.find('.dias-uteis').val('0');
        calcularTotalDias();
        return;
    }

    // Calcular dias úteis via AJAX
    $.post('<?= base_url('ferias/calcular-dias-uteis') ?>', {
        data_inicio: dataInicio,
        data_fim: dataFim
    })
    .done(function(response) {
        if (response.success) {
            periodoCard.find('.dias-uteis').val(response.dias_uteis);
        } else {
            Swal.fire('Erro', response.message || 'Erro ao calcular dias', 'error');
            periodoCard.find('.dias-uteis').val('0');
        }
        calcularTotalDias();
    })
    .fail(function() {
        periodoCard.find('.dias-uteis').val('0');
        calcularTotalDias();
    });
}

function calcularTotalDias() {
    let total = 0;
    $('.dias-uteis').each(function() {
        const valor = parseInt($(this).val()) || 0;
        total += valor;
    });
    $('#totalDias').text(total);
    
    // Validar se não excede dias disponíveis
    if (total > diasDisponiveis) {
        $('#totalDias').removeClass('bg-primary').addClass('bg-danger');
    } else {
        $('#totalDias').removeClass('bg-danger').addClass('bg-primary');
    }
}

function submeterPedido() {
    // Validar se há períodos
    const periodos = [];
    let valido = true;
    
    $('.periodo-item').each(function() {
        const dataInicio = $(this).find('.data-inicio').val();
        const dataFim = $(this).find('.data-fim').val();
        const diasUteis = parseInt($(this).find('.dias-uteis').val()) || 0;
        
        if (!dataInicio || !dataFim) {
            valido = false;
            return false;
        }
        
        if (diasUteis <= 0) {
            valido = false;
            Swal.fire('Erro', 'Todos os períodos devem ter pelo menos 1 dia útil', 'error');
            return false;
        }
        
        periodos.push({
            data_inicio: dataInicio,
            data_fim: dataFim,
            dias_uteis: diasUteis
        });
    });
    
    if (!valido || periodos.length === 0) {
        Swal.fire('Erro', 'Preencha pelo menos um período válido', 'error');
        return;
    }
    
    // Validar total de dias
    const totalDias = parseInt($('#totalDias').text());
    if (totalDias > diasDisponiveis) {
        Swal.fire('Erro', `O total de dias (${totalDias}) excede os dias disponíveis (${diasDisponiveis})`, 'error');
        return;
    }
    
    // Validar observações (morada obrigatória)
    const observacoes = $('#observacoes').val().trim();
    if (!observacoes) {
        Swal.fire('Erro', 'Por favor, indique a morada onde estará durante as férias', 'error');
        $('#observacoes').focus();
        return;
    }

    // ── Bloqueio para utilizadores com obrigação total ────────────
    if (obrigaTotalidade && totalDias < diasDisponiveis) {
        Swal.fire('Atenção', `Deve marcar a totalidade dos dias disponíveis (${diasDisponiveis} dias). Apenas marcou ${totalDias} dias.`, 'warning');
        return;
    }

    // ── Pedido de acumulação para utilizadores sem obrigação ──────
    if (!obrigaTotalidade && totalDias < diasDisponiveis) {
        const diasSobrantes = diasDisponiveis - totalDias;
        Swal.fire({
            icon: 'warning',
            title: 'Pedido de Acumulação de Férias',
            html: `
                <div class="text-start">
                    <p>Está a marcar <strong>${totalDias}</strong> dias, mas tem <strong>${diasDisponiveis}</strong> dias disponíveis.</p>
                    <p>Os restantes <strong class="text-danger">${diasSobrantes} dias</strong> requerem um 
                       <strong>Pedido de Acumulação de Férias</strong> (art.º 89 do ECD).</p>
                    <hr>
                    <p>Ao submeter, será gerado automaticamente o documento de pedido de acumulação que deverá 
                       <strong>imprimir, assinar e fazer upload</strong> do ficheiro assinado.</p>
                    <div class="mt-3">
                        <label class="form-label fw-bold">Motivo do pedido de acumulação: <span class="text-danger">*</span></label>
                        <textarea id="swal-motivo-acumulacao" class="form-control" rows="3" 
                                  maxlength="500" 
                                  placeholder="Indique o motivo pelo qual não pode marcar a totalidade dos dias..."></textarea>
                        <small class="text-muted">Campo obrigatório</small>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-send"></i> Submeter e Gerar Documento',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            preConfirm: () => {
                const motivo = document.getElementById('swal-motivo-acumulacao').value.trim();
                if (!motivo) {
                    Swal.showValidationMessage('O motivo é obrigatório para o pedido de acumulação');
                    return false;
                }
                return motivo;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                _enviarPedido(periodos, observacoes, result.value);
            }
        });
        return;
    }

    // Submissão normal
    _enviarPedido(periodos, observacoes, null);
}

function _enviarPedido(periodos, observacoes, motivoAcumulacao) {
    const dados = {
        periodos: periodos,
        observacoes: observacoes
    };
    if (motivoAcumulacao) {
        dados.motivo_acumulacao = motivoAcumulacao;
    }
    
    $.ajax({
        url: '<?= base_url('ferias/submeter') ?>',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(dados),
        success: function(response) {
            if (response.requer_acumulacao) {
                Swal.fire({
                    icon: 'success',
                    title: 'Pedido Submetido!',
                    html: `
                        <p>O pedido de férias foi submetido com sucesso.</p>
                        <div class="alert alert-warning text-start mt-3">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <strong>Documento de Acumulação gerado!</strong><br>
                            O documento <em>Pedido de Acumulação de Férias</em> foi gerado automaticamente.
                            <br><br>
                            Aceda a <strong>Meus Documentos</strong> para descarregar, assinar e fazer upload do documento assinado.
                        </div>
                    `,
                    confirmButtonText: 'Ver Documentos',
                    showCancelButton: true,
                    cancelButtonText: 'Ir para Dashboard'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= base_url('ferias/meus-documentos') ?>';
                    } else {
                        window.location.href = '<?= base_url('ferias') ?>';
                    }
                });
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Pedido submetido com sucesso',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '<?= base_url('ferias') ?>';
                });
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            let errorMsg = 'Erro ao submeter pedido';
            let isValidationError = false;
            
            if (xhr.status === 400 && response?.messages?.error) {
                errorMsg = response.messages.error;
                isValidationError = true;
                console.info('[INFO] Validação:', errorMsg);
            } else if (response?.messages?.error) {
                errorMsg = response.messages.error;
                console.error('[ERRO] Erro no servidor:', response);
            } else if (response?.message) {
                errorMsg = response.message;
                console.error('[ERRO] Erro no servidor:', response);
            } else if (xhr.responseText) {
                errorMsg = 'Erro no servidor. Veja o console do navegador para detalhes.';
                console.error('[ERRO] Resposta do servidor:', xhr.responseText);
            }
            
            Swal.fire({
                icon: isValidationError ? 'warning' : 'error',
                title: isValidationError ? 'Atenção' : 'Erro',
                html: errorMsg,
                confirmButtonText: 'OK'
            });
        }
    });
}

function cancelarPedidoPendente(pedidoId) {
    Swal.fire({
        title: 'Cancelar Pedido Pendente?',
        html: `
            <p>Tem a certeza que deseja cancelar o pedido pendente?</p>
            <ul class="text-start">
                <li>O pedido será <strong>cancelado</strong></li>
                <li>Os dias ficarão novamente <strong>disponíveis</strong></li>
                <li>Poderá submeter um novo pedido de imediato</li>
            </ul>
            <p class="mt-3"><strong>Deseja continuar?</strong></p>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sim, cancelar pedido',
        cancelButtonText: 'Não',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/cancelar/') ?>' + pedidoId,
                type: 'POST',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pedido Cancelado',
                        text: response.message || 'Pedido cancelado com sucesso. Pode agora marcar novas férias.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.messages?.error || response.message || 'Erro ao cancelar pedido'
                    });
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
