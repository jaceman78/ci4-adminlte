<?= $this->extend('layout/master') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
.sessao-card .card-header {
    background-color: #f8f9fa !important;
    border-bottom: 2px solid #dee2e6;
}
.sessao-card .card-header.hoje {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    border-bottom: 2px solid #1e7e34;
}
.sessao-card .card-header.hoje .btn-link {
    color: #ffffff !important;
}
.sessao-card .card-header.hoje .badge {
    background-color: rgba(255, 255, 255, 0.2) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.3);
}
.sessao-card .badge {
    font-size: 0.875rem !important;
    padding: 0.5em 0.75em !important;
    margin-left: 0.4rem !important;
}
.sessao-card .badge-title {
    font-size: 1.1rem !important;
    padding: 0.5em 0.85em !important;
    font-weight: 500 !important;
}
.sessao-card .btn-link {
    text-decoration: none !important;
    color: #495057 !important;
}
.sessao-card .btn-link:hover {
    color: #212529 !important;
}
.sessao-card .card-header.hoje .btn-link:hover {
    color: #f8f9fa !important;
}
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><i class="bi bi-clipboard-check"></i> <?= esc($title) ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('convocatorias') ?>">Convocatórias</a></li>
                    <li class="breadcrumb-item active">Marcar Presenças</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        <?php if (empty($sessoes)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Não há sessões de exame com convocatórias.
        </div>
        <?php else: ?>
        
        <!-- Filtros -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label>Filtrar por Data</label>
                        <input type="date" id="filtroData" class="form-control" placeholder="Todas as datas">
                    </div>
                    <div class="col-md-4">
                        <label>Filtrar por Tipo</label>
                        <select id="filtroTipo" class="form-select">
                            <option value="">Todos</option>
                            <option value="Exame Nacional">Exame Nacional</option>
                            <option value="Prova Final">Prova Final</option>
                            <option value="MODa">MODa</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-secondary form-control" onclick="limparFiltros()">
                            <i class="bi bi-x-circle"></i> Limpar Filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sessões com Cardboxes Expansíveis -->
        <div class="accordion" id="sessoesAccordion">
            <?php 
            $hoje = date('Y-m-d');
            foreach ($sessoes as $index => $sessao): 
                $isHoje = ($sessao['data_exame'] === $hoje);
            ?>
            <div class="card sessao-card" data-data="<?= $sessao['data_exame'] ?>" data-tipo="<?= $sessao['tipo_prova'] ?>">
                <div class="card-header <?= $isHoje ? 'hoje' : '' ?>" id="heading<?= $sessao['id'] ?>">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left collapsed" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#collapse<?= $sessao['id'] ?>" 
                                aria-expanded="false" aria-controls="collapse<?= $sessao['id'] ?>"
                                onclick="carregarConvocatorias(<?= $sessao['id'] ?>)">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-calendar-event"></i>
                                    <span class="badge badge-title bg-light text-dark">Sessão de Exame - <?= esc($sessao['codigo_prova']) ?> - <?= esc($sessao['nome_prova']) ?></span>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-secondary"><?= esc($sessao['tipo_prova']) ?></span>
                                    <span class="badge bg-info"><?= date('d/m/Y', strtotime($sessao['data_exame'])) ?> às <?= date('H:i', strtotime($sessao['hora_exame'])) ?></span>
                                    <span class="badge bg-primary"><?= esc($sessao['fase']) ?></span>
                                </div>
                            </div>
                        </button>
                    </h2>
                </div>

                <div id="collapse<?= $sessao['id'] ?>" class="collapse" aria-labelledby="heading<?= $sessao['id'] ?>" data-bs-parent="#sessoesAccordion">
                    <div class="card-body">
                        <div id="loading<?= $sessao['id'] ?>" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">A carregar...</span>
                            </div>
                        </div>
                        <div id="convocatorias<?= $sessao['id'] ?>" style="display: none;">
                            <!-- Conteúdo carregado via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    const convocatoriasCarregadas = {};

    window.carregarConvocatorias = function(sessaoId) {
        // Se já foi carregado, não carregar novamente
        if (convocatoriasCarregadas[sessaoId]) {
            return;
        }

        $.ajax({
            url: '<?= base_url('convocatorias/get-convocatorias-sessao') ?>/' + sessaoId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    renderizarConvocatorias(sessaoId, response.data);
                    convocatoriasCarregadas[sessaoId] = true;
                } else {
                    const errorMsg = response.message || 'Erro ao carregar convocatórias';
                    console.error('Erro:', errorMsg);
                    $('#loading' + sessaoId).html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro AJAX:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
                let errorMsg = 'Erro ao carregar convocatórias';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMsg = response.message;
                    }
                } catch(e) {
                    errorMsg += ' - ' + xhr.status + ': ' + xhr.statusText;
                }
                $('#loading' + sessaoId).html('<div class="alert alert-danger">' + errorMsg + '</div>');
            }
        });
    };

    function renderizarConvocatorias(sessaoId, data) {
        let html = '<form id="formPresencas' + sessaoId + '">';
        
        // Vigilantes
        if (data.vigilantes.length > 0) {
            html += '<h5 class="mt-3"><i class="bi bi-people-fill"></i> Vigilantes</h5>';
            html += '<div class="table-responsive"><table class="table table-sm table-bordered">';
            html += '<thead><tr><th width="40%">Nome</th><th width="15%">Sala</th><th width="30%">Presença</th><th width="15%">Ação</th></tr></thead><tbody>';
            
            data.vigilantes.forEach(function(conv) {
                html += '<tr>';
                html += '<td>' + conv.professor_nome + '</td>';
                html += '<td>' + (conv.codigo_sala || '-') + '</td>';
                html += '<td>';
                html += '<select class="form-select form-select-sm presenca-select" data-conv-id="' + conv.id + '" name="presenca[' + conv.id + ']">';
                html += gerarOpcoesPresenca(conv.presenca);
                html += '</select>';
                html += '</td>';
                html += '<td><button type="button" class="btn btn-sm btn-primary" onclick="salvarPresencaIndividual(' + conv.id + ')"><i class="bi bi-check"></i></button></td>';
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
        }
        
        // Suplentes
        if (data.suplentes.length > 0) {
            html += '<h5 class="mt-3"><i class="bi bi-people"></i> Suplentes</h5>';
            html += '<div class="table-responsive"><table class="table table-sm table-bordered">';
            html += '<thead><tr><th width="55%">Nome</th><th width="30%">Presença</th><th width="15%">Ação</th></tr></thead><tbody>';
            
            data.suplentes.forEach(function(conv) {
                html += '<tr>';
                html += '<td>' + conv.professor_nome + '</td>';
                html += '<td>';
                html += '<select class="form-select form-select-sm presenca-select" data-conv-id="' + conv.id + '" name="presenca[' + conv.id + ']">';
                html += gerarOpcoesPresenca(conv.presenca);
                html += '</select>';
                html += '</td>';
                html += '<td><button type="button" class="btn btn-sm btn-primary" onclick="salvarPresencaIndividual(' + conv.id + ')"><i class="bi bi-check"></i></button></td>';
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
        }
        
        // Coadjuvantes
        if (data.coadjuvantes.length > 0) {
            html += '<h5 class="mt-3"><i class="bi bi-person-badge"></i> Coadjuvantes</h5>';
            html += '<div class="table-responsive"><table class="table table-sm table-bordered">';
            html += '<thead><tr><th width="55%">Nome</th><th width="30%">Presença</th><th width="15%">Ação</th></tr></thead><tbody>';
            
            data.coadjuvantes.forEach(function(conv) {
                html += '<tr>';
                html += '<td>' + conv.professor_nome + '</td>';
                html += '<td>';
                html += '<select class="form-select form-select-sm presenca-select" data-conv-id="' + conv.id + '" name="presenca[' + conv.id + ']">';
                html += gerarOpcoesPresenca(conv.presenca);
                html += '</select>';
                html += '</td>';
                html += '<td><button type="button" class="btn btn-sm btn-primary" onclick="salvarPresencaIndividual(' + conv.id + ')"><i class="bi bi-check"></i></button></td>';
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
        }
        
        // Outros
        if (data.outros.length > 0) {
            html += '<h5 class="mt-3"><i class="bi bi-person-gear"></i> Outros</h5>';
            html += '<div class="table-responsive"><table class="table table-sm table-bordered">';
            html += '<thead><tr><th width="40%">Nome</th><th width="15%">Função</th><th width="30%">Presença</th><th width="15%">Ação</th></tr></thead><tbody>';
            
            data.outros.forEach(function(conv) {
                html += '<tr>';
                html += '<td>' + conv.professor_nome + '</td>';
                html += '<td>' + conv.funcao + '</td>';
                html += '<td>';
                html += '<select class="form-select form-select-sm presenca-select" data-conv-id="' + conv.id + '" name="presenca[' + conv.id + ']">';
                html += gerarOpcoesPresenca(conv.presenca);
                html += '</select>';
                html += '</td>';
                html += '<td><button type="button" class="btn btn-sm btn-primary" onclick="salvarPresencaIndividual(' + conv.id + ')"><i class="bi bi-check"></i></button></td>';
                html += '</tr>';
            });
            
            html += '</tbody></table></div>';
        }
        
        html += '<div class="mt-3 d-flex justify-content-between align-items-center">';
        html += '<div>';
        html += '<button type="button" class="btn btn-success" onclick="salvarTodasPresencas(' + sessaoId + ')"><i class="bi bi-save"></i> Guardar Todas as Presenças</button> ';
        html += '<a href="<?= base_url('convocatorias/gerar-pdf-faltas') ?>/' + sessaoId + '" class="btn btn-danger" target="_blank"><i class="bi bi-file-pdf"></i> Gerar PDF de Faltas</a>';
        html += '</div>';
        html += '<div>';
        html += '<a href="<?= base_url('convocatorias/pdf-presencas') ?>/' + sessaoId + '" class="btn btn-info" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Folha de Presenças</a>';
        html += '</div>';
        html += '</div>';
        
        html += '</form>';
        
        $('#loading' + sessaoId).hide();
        $('#convocatorias' + sessaoId).html(html).show();
    }

    function gerarOpcoesPresenca(presencaAtual) {
        const opcoes = ['Pendente', 'Presente', 'Falta', 'Falta Justificada'];
        let html = '';
        
        opcoes.forEach(function(opcao) {
            const selected = (presencaAtual === opcao) ? 'selected' : '';
            const color = getCorPresenca(opcao);
            html += '<option value="' + opcao + '" ' + selected + ' style="background-color: ' + color + ';">' + opcao + '</option>';
        });
        
        return html;
    }

    function getCorPresenca(presenca) {
        switch(presenca) {
            case 'Presente': return '#d4edda';
            case 'Falta': return '#f8d7da';
            case 'Falta Justificada': return '#fff3cd';
            default: return '#ffffff';
        }
    }

    window.salvarPresencaIndividual = function(convocatoriaId) {
        const select = $('select[data-conv-id="' + convocatoriaId + '"]');
        const presenca = select.val();
        
        $.ajax({
            url: '<?= base_url('convocatorias/atualizar-presenca') ?>/' + convocatoriaId,
            type: 'POST',
            data: { presenca: presenca },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Sucesso!', response.message, 'success');
                    select.css('background-color', getCorPresenca(presenca));
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Erro ao atualizar presença', 'error');
            }
        });
    };

    window.salvarTodasPresencas = function(sessaoId) {
        const form = $('#formPresencas' + sessaoId);
        const presencas = {};
        
        form.find('select.presenca-select').each(function() {
            const convId = $(this).data('conv-id');
            const presenca = $(this).val();
            presencas[convId] = presenca;
        });
        
        $.ajax({
            url: '<?= base_url('convocatorias/atualizar-presencas-sessao') ?>/' + sessaoId,
            type: 'POST',
            data: { presencas: presencas },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire('Sucesso!', response.message, 'success');
                    // Atualizar cores
                    form.find('select.presenca-select').each(function() {
                        $(this).css('background-color', getCorPresenca($(this).val()));
                    });
                } else {
                    Swal.fire('Erro!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Erro!', 'Erro ao atualizar presenças', 'error');
            }
        });
    };

    // Filtros
    $('#filtroData, #filtroTipo').on('change', function() {
        aplicarFiltros();
    });

    function aplicarFiltros() {
        const filtroData = $('#filtroData').val();
        const filtroTipo = $('#filtroTipo').val();
        
        $('.sessao-card').each(function() {
            const dataCard = $(this).data('data');
            const tipoCard = $(this).data('tipo');
            
            let mostrar = true;
            
            if (filtroData && dataCard !== filtroData) {
                mostrar = false;
            }
            
            if (filtroTipo && tipoCard !== filtroTipo) {
                mostrar = false;
            }
            
            if (mostrar) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    window.limparFiltros = function() {
        $('#filtroData').val('');
        $('#filtroTipo').val('');
        $('.sessao-card').show();
    };
});
</script>
<?= $this->endSection() ?>
