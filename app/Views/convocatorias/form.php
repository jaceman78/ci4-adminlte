<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1><i class="bi bi-people-fill"></i> Convocar Vigilantes para o Exame <?= esc($sessao['codigo_prova']) ?> - <?= esc($sessao['nome_prova']) ?></h1>
                </div>
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sessoes-exame') ?>">Sessões de Exame</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sessoes-exame/detalhes/' . $sessao['id']) ?>">Detalhes</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('sessoes-exame/alocar-salas/' . $sessao['id']) ?>">Alocar Salas</a></li>
                        <li class="breadcrumb-item active">Convocar Vigilantes</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Informações da Sessão -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-info-circle"></i> Sessão de Exame</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Exame:</strong> <?= esc($sessao['codigo_prova']) ?> - <?= esc($sessao['nome_prova']) ?></p>
                            <p><strong>Tipo:</strong> <span class="badge bg-info"><?= esc($sessao['tipo_prova']) ?></span></p>
                            <p><strong>Fase:</strong> <?= esc($sessao['fase']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data:</strong> <?= date('d/m/Y', strtotime($sessao['data_exame'])) ?></p>
                            <p><strong>Hora:</strong> <?= date('H:i', strtotime($sessao['hora_exame'])) ?></p>
                            <p><strong>Duração:</strong> <?= $sessao['duracao_minutos'] ?> minutos</p>
                        </div>
                    </div>
                    <?php if (!in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC'])): ?>
                    <?php
                    $totalVigilantesNecessarios = 0;
                    $totalVigilantesAlocados = 0;
                    foreach ($salasAlocadas as $sala) {
                        $totalVigilantesNecessarios += $sala['vigilantes_necessarios'];
                        $totalVigilantesAlocados += $sala['vigilantes_alocados'];
                    }
                    ?>
                    <div class="alert alert-info mt-3">
                        <strong>Total Vigilantes:</strong> <?= $totalVigilantesAlocados ?> / <?= $totalVigilantesNecessarios ?>
                        <?php if ($totalVigilantesAlocados < $totalVigilantesNecessarios): ?>
                            <span class="badge bg-warning ms-2">Faltam <?= $totalVigilantesNecessarios - $totalVigilantesAlocados ?> vigilantes</span>
                        <?php elseif ($totalVigilantesAlocados == $totalVigilantesNecessarios): ?>
                            <span class="badge bg-success ms-2"><i class="bi bi-check-circle"></i> Completo</span>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <?php
                    // Definir labels apropriados para cada tipo especial
                    $labelsEspeciais = [
                        'Suplentes' => [
                            'titulo' => 'Suplentes',
                            'descricao' => 'Convoque professores livremente. Não há número mínimo ou máximo.'
                        ],
                        'Verificacao Calculadoras' => [
                            'titulo' => 'Verificação de Calculadoras',
                            'descricao' => 'Convoque professores para verificar calculadoras. Sem limite de convocações.'
                        ],
                        'Apoio TIC' => [
                            'titulo' => 'Apoio TIC',
                            'descricao' => 'Convoque a equipa de apoio TIC necessária. Sem limite de convocações.'
                        ]
                    ];
                    $info = $labelsEspeciais[$sessao['tipo_prova']] ?? ['titulo' => 'Sessão Especial', 'descricao' => 'Sem limite de convocações.'];
                    ?>
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i>
                        <strong><?= esc($info['titulo']) ?>:</strong> <?= esc($info['descricao']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Layout Drag & Drop -->
            <div class="row">
                <!-- LADO ESQUERDO: Salas Alocadas -->
                <div class="col-md-7">
                    <div class="card card-success">
                        <div class="card-header">
                            <h3 class="card-title"><i class="bi bi-door-open"></i> Salas Alocadas</h3>
                            <div class="card-tools">
                                <span class="badge bg-light text-dark"><?= count($salasAlocadas) ?> salas</span>
                                <a href="<?= base_url('sessoes-exame/alocar-salas/' . $sessao['id']) ?>" class="btn btn-secondary btn-sm ms-2">
                                    <i class="bi bi-arrow-left"></i> Voltar
                                </a>
                            </div>
                        </div>
                        <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                            <?php if (empty($salasAlocadas)): ?>
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Nenhuma sala alocada ainda. <a href="<?= base_url('sessoes-exame/alocar-salas/' . $sessao['id']) ?>">Alocar salas primeiro</a>.
                                </div>
                            <?php else: ?>
                                <div id="salas-container">
                                    <?php foreach ($salasAlocadas as $sala): 
                                        $vigilantesNaSala = $convocatoriasPorSala[$sala['id']] ?? [];
                                        $vigilantesCount = count($vigilantesNaSala);
                                        $vigilantesNecessarios = $sala['vigilantes_necessarios'];
                                        
                                        // Para sessões especiais, não usar cores de status (completo/incompleto)
                                        $isTipoEspecial = in_array($sessao['tipo_prova'], ['Suplentes', 'Verificacao Calculadoras', 'Apoio TIC']);
                                        if ($isTipoEspecial) {
                                            $cardBorderColor = 'border-info';
                                            $headerBgColor = 'bg-info';
                                            $badgeBgColor = 'bg-light text-info';
                                        } else {
                                            $isCompleto = $vigilantesCount >= $vigilantesNecessarios;
                                            $isParcial = $vigilantesCount > 0 && $vigilantesCount < $vigilantesNecessarios;
                                            $cardBorderColor = $isCompleto ? 'border-success' : ($isParcial ? 'border-warning' : 'border-danger');
                                            $headerBgColor = $isCompleto ? 'bg-success' : ($isParcial ? 'bg-warning' : 'bg-danger');
                                            $badgeBgColor = $isCompleto ? 'bg-light text-success' : 'bg-light text-dark';
                                        }
                                    ?>
                                    <div class="sala-card card mb-3 <?= $cardBorderColor ?>" 
                                         data-sala-id="<?= $sala['id'] ?>" 
                                         data-vigilantes-necessarios="<?= $vigilantesNecessarios ?>">
                                        <div class="card-header <?= $headerBgColor ?> text-white">
                                            <h5 class="card-title mb-0">
                                                <i class="bi bi-door-closed"></i> <?= esc($sala['sala_nome']) ?>
                                                <?php if (!$isTipoEspecial): ?>
                                                <span class="badge <?= $badgeBgColor ?> ms-2">
                                                    <?= $vigilantesCount ?>/<?= $vigilantesNecessarios ?> vigilantes
                                                </span>
                                                <?php else: ?>
                                                <?php
                                                $labelPessoas = match($sessao['tipo_prova']) {
                                                    'Suplentes' => 'suplentes',
                                                    'Verificacao Calculadoras' => 'professores',
                                                    'Apoio TIC' => 'técnicos',
                                                    default => 'pessoas'
                                                };
                                                ?>
                                                <span class="badge bg-light text-info ms-2">
                                                    <?= $vigilantesCount ?> <?= $labelPessoas ?>
                                                </span>
                                                <?php endif; ?>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <?php if (!$isTipoEspecial): ?>
                                            <p class="mb-2">
                                                <strong>Alunos:</strong> <?= number_format($sala['num_alunos_sala'], 0, ',', '.') ?>
                                            </p>
                                            <?php else: ?>
                                            <?php
                                            $infoSalas = [
                                                'Suplentes' => 'Sala de espera de suplentes',
                                                'Verificacao Calculadoras' => 'Sala de verificação de calculadoras',
                                                'Apoio TIC' => 'Sala de apoio TIC'
                                            ];
                                            ?>
                                            <p class="mb-2 text-muted">
                                                <i class="bi bi-info-circle"></i> <?= $infoSalas[$sessao['tipo_prova']] ?? 'Sala especial' ?>
                                            </p>
                                            <?php endif; ?>
                                            
                                            <!-- Lista de vigilantes alocados -->
                                            <div class="vigilantes-lista mb-2" data-sala-id="<?= $sala['id'] ?>">
                                                <?php if (!empty($vigilantesNaSala)): ?>
                                                    <?php foreach ($vigilantesNaSala as $conv): ?>
                                                        <div class="vigilante-item alert alert-secondary d-flex justify-content-between align-items-center py-2 mb-1" 
                                                             data-convocatoria-id="<?= $conv['id'] ?>">
                                                            <span>
                                                                <i class="bi bi-person-fill"></i> <?= esc($conv['user_nome']) ?>
                                                            </span>
                                                            <button type="button" class="btn btn-sm btn-danger btn-remover-vigilante" 
                                                                    data-convocatoria-id="<?= $conv['id'] ?>"
                                                                    title="Remover">
                                                                <i class="bi bi-x-lg"></i>
                                                            </button>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <!-- Zona de drop -->
                                            <div class="drop-zone border border-2 border-dashed rounded p-3 text-center text-muted" 
                                                 data-sala-id="<?= $sala['id'] ?>">
                                                <i class="bi bi-plus-circle"></i> Arrastar professor aqui
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- LADO DIREITO: Professores Disponíveis -->
                <div class="col-md-5">
                    <div class="card card-info sticky-top" style="top: 20px;">
                        <div class="card-header">
                            <h3 class="card-title"><i class="bi bi-people"></i> Professores</h3>
                        </div>
                        <div class="card-body">
                            <!-- Pesquisa -->
                            <div class="mb-3">
                                <input type="text" id="searchProfessor" class="form-control" placeholder="🔍 Pesquisar professor...">
                            </div>

                            <!-- Filtros -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="filtroDisponiveis" checked>
                                <label class="form-check-label" for="filtroDisponiveis">
                                    Mostrar apenas disponíveis
                                </label>
                            </div>

                            <!-- Lista de Professores -->
                            <div id="professores-lista" style="max-height: 450px; overflow-y: auto;">
                                <?php foreach ($professores as $prof): 
                                    $isAlocado = in_array($prof['id'], $professoresAlocados);
                                ?>
                                    <div class="professor-item card mb-2 <?= $isAlocado ? 'bg-light' : '' ?>" 
                                         data-user-id="<?= $prof['id'] ?>"
                                         data-user-nome="<?= esc($prof['name']) ?>"
                                         data-alocado="<?= $isAlocado ? '1' : '0' ?>"
                                         draggable="<?= $isAlocado ? 'false' : 'true' ?>"
                                         style="cursor: <?= $isAlocado ? 'not-allowed' : 'grab' ?>;">
                                        <div class="card-body p-2">
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <?php if ($isAlocado): ?>
                                                        <i class="bi bi-check-circle-fill text-success"></i>
                                                    <?php else: ?>
                                                        <i class="bi bi-circle text-secondary"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong><?= esc($prof['name']) ?></strong>
                                                    <?php if (!empty($prof['grupo_id']) || isset($prof['total_vigilancias'])): ?>
                                                        <span class="text-muted">
                                                            <?php if (!empty($prof['grupo_id'])): ?>
                                                                (<?= esc($prof['grupo_id']) ?>)
                                                            <?php endif; ?>
                                                            <?php if (isset($prof['total_vigilancias'])): ?>
                                                                - (<?= (int)$prof['total_vigilancias'] ?> vigilâncias)
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($isAlocado): ?>
                                                        <br><small class="text-success">Já convocado</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <small class="text-muted">Arraste os professores para as salas</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
</div>

<style>
.sala-card {
    transition: all 0.3s ease;
}

.sala-card.drag-over {
    transform: scale(1.02);
    box-shadow: 0 0 20px rgba(0,123,255,0.5);
}

.drop-zone {
    min-height: 50px;
    transition: all 0.3s ease;
}

.drop-zone.drag-over {
    background-color: #e3f2fd;
    border-color: #2196F3 !important;
}

.professor-item[draggable="true"]:active {
    cursor: grabbing;
    opacity: 0.5;
}

.vigilante-item {
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
const sessaoExameId = <?= $sessao['id'] ?>;
let draggedElement = null;

document.addEventListener('DOMContentLoaded', function() {
    initDragAndDrop();
    initSearch();
    initFiltros();
    initRemoveButtons();
});

function initDragAndDrop() {
    // Drag start
    document.querySelectorAll('.professor-item[draggable="true"]').forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedElement = this;
            this.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        });

        item.addEventListener('dragend', function(e) {
            this.style.opacity = '1';
        });
    });

    // Drop zones
    document.querySelectorAll('.drop-zone').forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
            this.closest('.sala-card').classList.add('drag-over');
        });

        zone.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
            this.closest('.sala-card').classList.remove('drag-over');
        });

        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            this.closest('.sala-card').classList.remove('drag-over');

            if (draggedElement) {
                const userId = draggedElement.dataset.userId;
                const userNome = draggedElement.dataset.userNome;
                const salaId = this.dataset.salaId;

                adicionarVigilante(userId, userNome, salaId);
            }
        });
    });
}

function adicionarVigilante(userId, userNome, salaId) {
    const data = {
        user_id: parseInt(userId),
        sessao_exame_sala_id: parseInt(salaId),
        sessao_exame_id: sessaoExameId
    };

    $.ajax({
        url: '<?= base_url('convocatorias/adicionar-vigilante') ?>',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(data),
        success: function(response) {
            if (response.success) {
                // Recarregar página para atualizar tudo
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
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
            const response = xhr.responseJSON;
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: response?.message || 'Erro ao adicionar vigilante.'
            });
        }
    });
}

function initRemoveButtons() {
    document.querySelectorAll('.btn-remover-vigilante').forEach(btn => {
        btn.addEventListener('click', function() {
            const convocatoriaId = this.dataset.convocatoriaId;
            
            Swal.fire({
                title: 'Remover vigilante?',
                text: 'Tem a certeza que pretende remover este vigilante?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, remover!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    removerVigilante(convocatoriaId);
                }
            });
        });
    });
}

function removerVigilante(convocatoriaId) {
    $.ajax({
        url: '<?= base_url('convocatorias/remover-vigilante') ?>/' + convocatoriaId,
        type: 'POST',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Removido!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
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
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: 'Erro ao remover vigilante.'
            });
        }
    });
}

function initSearch() {
    const searchInput = document.getElementById('searchProfessor');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        document.querySelectorAll('.professor-item').forEach(item => {
            const nome = item.dataset.userNome.toLowerCase();
            if (nome.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
}

function initFiltros() {
    const filtroCheck = document.getElementById('filtroDisponiveis');
    filtroCheck.addEventListener('change', function() {
        const mostrarDisponiveis = this.checked;
        
        document.querySelectorAll('.professor-item').forEach(item => {
            const isAlocado = item.dataset.alocado === '1';
            
            if (mostrarDisponiveis && isAlocado) {
                item.style.display = 'none';
            } else {
                item.style.display = '';
            }
        });
    });

    // Aplicar filtro inicial
    filtroCheck.dispatchEvent(new Event('change'));
}
</script>

<?= $this->endSection() ?>
