<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <h1><i class="bi bi-calendar-plus"></i> Atribuir Dias de Férias - <?= $ano_letivo['anoletivo'] + 1 ?></h1>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Gestão de Atribuições</h3>
                    <div class="card-tools">
                        <span class="badge bg-light text-dark">Total: <?= count($professores) ?> funcionários</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold"><i class="bi bi-building"></i> Escola</label>
                            <select id="filtroEscola" class="form-select form-select-sm">
                                <option value="">Todas as escolas</option>
                                <?php foreach ($escolas as $esc): ?>
                                <option value="<?= esc($esc['nome']) ?>"><?= esc($esc['nome']) ?></option>
                                <?php endforeach; ?>
                                <option value="Sem escola">Sem escola atribuída</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="filtroSaldoPositivo">
                                <label class="form-check-label fw-semibold" for="filtroSaldoPositivo">
                                    Apenas com saldo &gt; 0
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-sm btn-outline-secondary" id="btnLimparFiltros">
                                <i class="bi bi-x-circle"></i> Limpar filtros
                            </button>
                        </div>
                    </div>
                    <table class="table table-sm table-striped" id="professorTable">
                        <thead>
                            <tr>
                                <th>Funcionário</th>
                                <th>Cód. Func.</th>
                                <th>Escola</th>
                                <th width="100">Base</th>
                                <th width="100">Ajuste</th>
                                <th width="100">Extra</th>
                                <th width="100">Total</th>
                                <th width="100">Saldo</th>
                                <th width="120">Ações</th>
                                <th>_escola</th>
                                <th>_saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($professores as $prof): ?>
                            <?php
                                $saldoVal = isset($prof['saldo']) && is_array($prof['saldo'])
                                    ? (int)$prof['saldo']['dias_disponiveis']
                                    : null;
                                $nomeEscola = $prof['nome_escola'] ?? '';
                            ?>
                            <tr data-escola="<?= esc($nomeEscola ?: 'Sem escola') ?>" data-saldo="<?= $saldoVal !== null ? $saldoVal : '' ?>">
                                <td>
                                    <?= esc($prof['name']) ?>
                                    <?php
                                        $alinea = $prof['atribuicao']['alinea'] ?? null;
                                        if ($alinea):
                                            $alineaDesc = [
                                                'a' => 'Junta Médica',
                                                'b' => 'Mobilidade Especial',
                                                'c' => 'Em Mobilidade',
                                                'd' => 'Licença s/ vencimento',
                                                'e' => 'Licença art.º 37.º Lei 7/2009',
                                                'f' => 'Licença art.º 53 Lei 90/2019',
                                            ];
                                            $desc = $alineaDesc[$alinea] ?? '';
                                    ?>
                                        <span class="badge bg-warning text-dark ms-1"
                                              title="Alínea <?= strtoupper($alinea) ?><?= $desc ? ' — ' . $desc : '' ?>">
                                            <?= strtoupper($alinea) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $prof['cod_funcionario'] ? esc($prof['cod_funcionario']) : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td>
                                    <?= $nomeEscola ? esc($nomeEscola) : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td>
                                    <?= $prof['atribuicao']['dias_base'] ?? '<span class="text-muted">-</span>' ?>
                                </td>
                                <td>
                                    <?php if (isset($prof['atribuicao']['dias_ajuste'])): ?>
                                        <span class="badge bg-<?= $prof['atribuicao']['dias_ajuste'] >= 0 ? 'success' : 'danger' ?>">
                                            <?= $prof['atribuicao']['dias_ajuste'] >= 0 ? '+' : '' ?><?= $prof['atribuicao']['dias_ajuste'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($prof['atribuicao']['dias_extra']) && $prof['atribuicao']['dias_extra'] > 0): ?>
                                        <span class="badge bg-primary">+<?= $prof['atribuicao']['dias_extra'] ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($prof['atribuicao']['dias_total'])): ?>
                                        <strong><?= $prof['atribuicao']['dias_total'] ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (isset($prof['saldo']) && is_array($prof['saldo'])): ?>
                                        <span class="badge bg-<?= $prof['saldo']['dias_disponiveis'] > 0 ? 'success' : ($prof['saldo']['dias_disponiveis'] < 0 ? 'danger' : 'secondary') ?>">
                                            <?= $prof['saldo']['dias_disponiveis'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('ferias/gerir-professor/' . $prof['NIF']) ?>" 
                                           class="btn btn-sm btn-primary"
                                           title="Gerir férias deste professor">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if (isset($prof['atribuicao']['dias_total']) && $prof['atribuicao']['dias_total'] > 0): ?>
                                            <?php 
                                            $emailEnviado = $prof['email_enviado'] ?? false;
                                            ?>
                                            <button class="btn btn-sm btn-<?= $emailEnviado ? 'success' : 'info' ?>" 
                                                    onclick='enviarEmailAtribuicao(<?= $prof['NIF'] ?>, <?= json_encode($prof['name'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>, <?= $prof['atribuicao']['id'] ?>)'
                                                    title="<?= $emailEnviado ? 'Email já enviado - Reenviar' : 'Enviar email com informação das férias' ?>"
                                                    id="btn-email-<?= $prof['NIF'] ?>">
                                                <i class="bi bi-envelope<?= $emailEnviado ? '-check' : '' ?>"></i>
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled title="Atribua dias de férias primeiro">
                                                <i class="bi bi-envelope"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <!-- Colunas ocultas para filtragem correta em todas as páginas -->
                                <td><?= esc($nomeEscola ?: 'Sem escola') ?></td>
                                <td><?= $saldoVal !== null ? $saldoVal : '' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Filtro por escola: usa column search nativo do DataTables (funciona em todas as páginas)
    // Filtro por saldo: usa ext.search para comparação numérica
    $.fn.dataTable.ext.search.push(function(settings, data) {
        if (settings.nTable.id !== 'professorTable') return true;
        if (!$('#filtroSaldoPositivo').is(':checked')) return true;
        var rowSaldo = data[10];
        return rowSaldo !== '' && parseInt(rowSaldo) > 0;
    });

    var table = $('#professorTable').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json' },
        pageLength: 25,
        order: [[0, 'asc']],
        columnDefs: [
            { targets: [9, 10], visible: false, searchable: true }
        ]
    });

    $('#filtroEscola').on('change', function() {
        var val = $(this).val();
        // Regex de match exato para não apanhar substrings
        table.column(9).search(val ? '^' + $.fn.dataTable.util.escapeRegex(val) + '$' : '', true, false).draw();
    });

    $('#filtroSaldoPositivo').on('change', function() {
        table.draw();
    });

    $('#btnLimparFiltros').on('click', function() {
        $('#filtroEscola').val('');
        $('#filtroSaldoPositivo').prop('checked', false);
        table.column(9).search('', false, false).draw();
    });
});

function editarAtribuicao(userId, nif, nome, atribuicao, codFuncionario, categoria, telefone, grupoId, atribuicaoAnoAnterior, diasGozadosAnterior, idAtribuicaoAnoAnterior) {
    // Validar se funcionário tem cod_funcionario e categoria preenchidos
    if (!codFuncionario || codFuncionario === '' || codFuncionario === null) {
        Swal.fire({
            icon: 'warning',
            title: 'Dados Incompletos',
            html: `
                <p>O funcionário <strong>${nome}</strong> não tem o <strong>Código de Funcionário</strong> preenchido.</p>
                <p>Por favor, preencha este campo antes de atribuir férias.</p>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-pencil"></i> Adicionar agora os dados em falta',
            cancelButtonText: '<i class="bi bi-x-lg"></i> Fechar',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Criar objeto user para passar para a modal
                const user = {
                    id: userId,
                    NIF: nif,
                    name: nome,
                    cod_funcionario: codFuncionario || '',
                    telefone: telefone || '',
                    grupo_id: grupoId || '',
                    categoria: categoria || ''
                };
                editarUtilizador(user);
            }
        });
        return;
    }
    
    if (!categoria || categoria === '' || categoria === null) {
        Swal.fire({
            icon: 'warning',
            title: 'Dados Incompletos',
            html: `
                <p>O funcionário <strong>${nome}</strong> não tem a <strong>Categoria</strong> preenchida.</p>
                <p>Por favor, preencha este campo antes de atribuir férias.</p>
            `,
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-pencil"></i> Adicionar agora os dados em falta',
            cancelButtonText: '<i class="bi bi-x-lg"></i> Fechar',
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Criar objeto user para passar para a modal
                const user = {
                    id: userId,
                    NIF: nif,
                    name: nome,
                    cod_funcionario: codFuncionario || '',
                    telefone: telefone || '',
                    grupo_id: grupoId || '',
                    categoria: categoria || ''
                };
                editarUtilizador(user);
            }
        });
        return;
    }
    
    // atribuicao pode ser null se funcionário não tem atribuição ainda
    const attr = atribuicao || {};
    const diasBase = attr.dias_base || 22;
    const diasAjuste = attr.dias_ajuste || 0;
    const diasExtra = attr.dias_extra || 0;
    const diasGozadosAnt = attr.dias_gozados_anterior || diasGozadosAnterior || 0;
    const obs = attr.observacoes || '';
    
    // Dados do ano anterior
    const anoAnt = atribuicaoAnoAnterior || {};
    const diasAnoAnterior = anoAnt.dias_total || 0;
    const diasBaseAnoAnterior = anoAnt.dias_base || 0;
    const diasAjusteAnoAnterior = anoAnt.dias_ajuste || 0;
    const diasExtraAnoAnterior = anoAnt.dias_extra || 0;
    
    // Informação do ano anterior (apenas se já existir registo)
    let infoAnoAnterior = '';
    if (diasAnoAnterior > 0) {
        infoAnoAnterior = `
            <div class="alert alert-success text-start mb-3">
                <strong><i class="bi bi-check-circle"></i> Registo do Ano Anterior Encontrado:</strong><br>
                Dias Base: <strong>${diasBaseAnoAnterior}</strong> | 
                Ajuste: <strong>${diasAjusteAnoAnterior}</strong> | 
                Extra: <strong>${diasExtraAnoAnterior}</strong> | 
                <strong>Total: ${diasAnoAnterior}</strong><br>
                Dias gozados: <strong>${diasGozadosAnterior}</strong> | 
                Diferença: <strong>${diasAnoAnterior - diasGozadosAnterior}</strong>
            </div>
        `;
    } else {
        infoAnoAnterior = `
            <div class="alert alert-warning text-start mb-3">
                <strong><i class="bi bi-exclamation-triangle"></i> Sem Registo do Ano Anterior</strong><br>
                Preencha os campos abaixo para criar um registo retroativo.
            </div>
        `;
    }
    
    Swal.fire({
        title: 'Atribuir Férias',
        html: `
            <div class="text-start mb-3">
                <strong>Funcionário:</strong> ${nome}<br>
                <strong>NIF:</strong> ${nif}<br>
                <strong>Cód. Funcionário:</strong> ${codFuncionario || '<span class="text-danger">Não definido</span>'}
            </div>
            ${infoAnoAnterior}
            <div class="form-group text-start mb-2">
                <label>Dias Base:</label>
                <input type="number" id="dias_base" class="form-control" value="${diasBase}" min="0" max="30">
            </div>
            <hr style="border-top: 2px solid #dee2e6; margin: 15px 0;">
            <h6 class="text-start mb-2"><i class="bi bi-calendar2-minus"></i> Dados do Ano Anterior (Retroativo)</h6>
            <div class="form-group text-start mb-2">
                <label>Dias Atribuídos no Ano Anterior:</label>
                <input type="number" id="dias_atribuidos_ano_anterior" class="form-control" value="${diasAnoAnterior}" min="0" max="50" 
                       onchange="calcularAjusteRetroativo()">
                <small class="text-muted">Total de dias que teve direito no ano anterior (deixe 0 se não aplicável)</small>
            </div>
            <div class="form-group text-start mb-2">
                <label>Dias Gozados no Ano Anterior:</label>
                <input type="number" id="dias_gozados_anterior" class="form-control" value="${diasGozadosAnt}" min="0" max="50" 
                       onchange="calcularAjusteRetroativo()">
                <small class="text-muted">Total de dias efetivamente gozados no ano anterior</small>
            </div>
            <hr style="border-top: 2px solid #dee2e6; margin: 15px 0;">
            <h6 class="text-start mb-2"><i class="bi bi-calendar-check"></i> Atribuição do Ano Atual</h6>
            <div class="form-group text-start mb-2">
                <label>Ajuste (dias ano anterior):</label>
                <input type="number" id="dias_ajuste" class="form-control" value="${diasAjuste}" min="-30" max="30" readonly style="background-color: #e9ecef;">
                <small class="text-muted">Calculado automaticamente: Dias ano anterior - Dias gozados</small>
            </div>
            <div class="form-group text-start mb-2">
                <label>Acertos:</label>
                <input type="number" id="dias_extra" class="form-control" value="${diasExtra}" min="-30" max="30">
            </div>
            
            <hr>
            <h6 class="text-primary mb-2"><i class="fas fa-user-cog"></i> Permissões Especiais</h6>
            <div class="form-check mb-2">
                <input type="checkbox" class="form-check-input" id="permite_marcar_fora_periodo" value="1">
                <label class="form-check-label" for="permite_marcar_fora_periodo">
                    <strong>Permitir marcar fora do período</strong>
                    <small class="text-muted d-block">Professor pode marcar férias fora do período configurado</small>
                </label>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="obriga_totalidade_dias" value="1" checked>
                <label class="form-check-label" for="obriga_totalidade_dias">
                    <strong>Obrigar a marcar totalidade de dias</strong>
                    <small class="text-muted d-block">Professor deve marcar TODOS os dias disponíveis</small>
                </label>
            </div>
            
            <div class="form-group text-start mb-2">
                <label>Observações:</label>
                <textarea id="observacoes" class="form-control" rows="2">${obs}</textarea>
            </div>
        `,
        width: 650,
        showCancelButton: true,
        confirmButtonText: 'Salvar',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            // Calcular ajuste inicial
            calcularAjusteRetroativo();
        },
        preConfirm: () => {
            return {
                user_nif: nif,
                id_atribuicao_ano_anterior: idAtribuicaoAnoAnterior || 0,
                dias_atribuidos_ano_anterior: document.getElementById('dias_atribuidos_ano_anterior').value,
                dias_base: document.getElementById('dias_base').value,
                dias_ajuste: document.getElementById('dias_ajuste').value,
                dias_gozados_anterior: document.getElementById('dias_gozados_anterior').value,
                dias_extra: document.getElementById('dias_extra').value,
                observacoes: document.getElementById('observacoes').value,
                permite_marcar_fora_periodo: document.getElementById('permite_marcar_fora_periodo').checked ? 1 : 0,
                obriga_totalidade_dias: document.getElementById('obriga_totalidade_dias').checked ? 1 : 0
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('ferias/salvar-atribuicao') ?>', result.value)
                .done(function(response) {
                    Swal.fire('Sucesso!', response.message || 'Atribuição salva com sucesso', 'success')
                        .then(() => location.reload());
                })
                .fail(function(xhr) {
                    console.error('Erro:', xhr.responseText);
                    const response = xhr.responseJSON;
                    const errorMsg = response?.messages?.error || response?.message || 'Erro ao salvar atribuição';
                    Swal.fire('Erro!', errorMsg, 'error');
                });
        }
    });
}

function calcularAjusteRetroativo() {
    const diasAtribuidosAnoAnterior = parseInt(document.getElementById('dias_atribuidos_ano_anterior').value) || 0;
    const diasGozados = parseInt(document.getElementById('dias_gozados_anterior').value) || 0;
    const ajuste = diasAtribuidosAnoAnterior - diasGozados;
    document.getElementById('dias_ajuste').value = ajuste;
}

function editarUtilizador(user) {
    const categorias = <?= json_encode($categorias) ?>;
    
    // Construir options de categorias
    let categoriasOptions = '<option value="">Selecione...</option>';
    categorias.forEach(function(cat) {
        const selected = cat === user.categoria ? 'selected' : '';
        categoriasOptions += `<option value="${cat}" ${selected}>${cat}</option>`;
    });
    
    Swal.fire({
        title: 'Editar Utilizador',
        html: `
            <div class="text-start">
                <div class="form-group mb-3">
                    <label><strong>NIF:</strong></label>
                    <input type="text" class="form-control" value="${user.NIF || ''}" disabled>
                </div>
                
                <div class="form-group mb-3">
                    <label><strong>Nome:</strong> <span class="text-danger">*</span></label>
                    <input type="text" id="nome" class="form-control" value="${user.name || ''}" required>
                </div>
                
                <div class="form-group mb-3">
                    <label><strong>Código de Funcionário:</strong></label>
                    <input type="text" id="cod_funcionario" class="form-control" value="${user.cod_funcionario || ''}" 
                           placeholder="Ex: F001">
                </div>
                
                <div class="form-group mb-3">
                    <label><strong>Telefone:</strong></label>
                    <input type="text" id="telefone" class="form-control" value="${user.telefone || ''}" 
                           placeholder="Ex: 912345678">
                </div>
                
                <div class="form-group mb-3">
                    <label><strong>Grupo ID:</strong></label>
                    <input type="number" id="grupo_id" class="form-control" value="${user.grupo_id || ''}" 
                           placeholder="ID do grupo">
                    <small class="text-muted">Deixe vazio se não aplicável</small>
                </div>
                
                <div class="form-group mb-3">
                    <label><strong>Categoria:</strong></label>
                    <select id="categoria" class="form-control">
                        ${categoriasOptions}
                    </select>
                </div>
            </div>
        `,
        width: 600,
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-check-lg"></i> Salvar',
        cancelButtonText: '<i class="bi bi-x-lg"></i> Cancelar',
        preConfirm: () => {
            const nome = document.getElementById('nome').value;
            
            if (!nome || nome.trim() === '') {
                Swal.showValidationMessage('Nome é obrigatório');
                return false;
            }
            
            return {
                user_id: user.id,
                nome: nome,
                cod_funcionario: document.getElementById('cod_funcionario').value,
                telefone: document.getElementById('telefone').value,
                grupo_id: document.getElementById('grupo_id').value,
                categoria: document.getElementById('categoria').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/atualizar-utilizador') ?>',
                type: 'POST',
                data: result.value,
                success: function(response) {
                    console.log('Utilizador atualizado:', response);
                    if (response.data) {
                        console.log('Cod Funcionario:', response.data.cod_funcionario);
                        console.log('Categoria:', response.data.categoria);
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Utilizador atualizado com sucesso. Abrindo atribuição de férias...',
                        timer: 1500,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        // Recarregar a página para obter os dados atualizados
                        window.location.reload(true);
                    });
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    const errorMsg = response?.messages?.error || response?.message || 'Erro ao atualizar utilizador';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: errorMsg,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

function enviarEmailAtribuicao(nif, nome, atribuicaoId) {
    Swal.fire({
        title: 'Enviar Email?',
        html: `
            <p>Deseja enviar um email com a informação da atribuição de férias para:</p>
            <p><strong>${nome}</strong></p>
            <p class="text-muted">O funcionário receberá um email com os detalhes dos dias atribuídos e um link para o sistema.</p>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="bi bi-envelope"></i> Enviar Email',
        cancelButtonText: '<i class="bi bi-x-lg"></i> Cancelar',
        confirmButtonColor: '#17a2b8',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Enviando email...',
                html: 'Por favor, aguarde.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: '<?= base_url('ferias/enviar-email-atribuicao/') ?>' + atribuicaoId,
                type: 'POST',
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Email Enviado!',
                        text: response.message || 'Email enviado com sucesso',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Atualizar botão para mostrar que foi enviado
                        const btn = $('#btn-email-' + nif);
                        btn.removeClass('btn-info').addClass('btn-success');
                        btn.attr('title', 'Email já enviado - Reenviar');
                        btn.find('i').removeClass('bi-envelope').addClass('bi-envelope-check');
                    });
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    const errorMsg = response?.messages?.error || response?.message || 'Erro ao enviar email';
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: errorMsg,
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
