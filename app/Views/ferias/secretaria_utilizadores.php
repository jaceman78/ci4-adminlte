<?= $this->extend('layout/master') ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><i class="bi bi-people"></i> Gestão de Utilizadores</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('ferias/secretaria') ?>">Férias</a></li>
                        <li class="breadcrumb-item active">Utilizadores</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                Esta página permite editar apenas dados básicos dos utilizadores ativos do sistema.
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Utilizadores Ativos</h3>
                    <div class="card-tools">
                        <span class="badge bg-primary"><?= count($utilizadores) ?> utilizadores</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="utilizadoresTable">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>NIF</th>
                                    <th>Cód. Funcionário</th>
                                    <th>Telefone</th>
                                    <th>Grupo</th>
                                    <th>Categoria</th>
                                    <th>Escola</th>
                                    <th>Mapa Férias</th>
                                    <th width="80">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($utilizadores as $user): ?>
                                <?php
                                    $grupoLabels = [
                                        'geral'                => '<span class="badge bg-secondary">Geral</span>',
                                        'direcao'              => '<span class="badge bg-primary text-white">Direção</span>',
                                        'tecnico_superior'     => '<span class="badge bg-info text-white">Téc. Superior</span>',
                                    ];
                                    $grupoLabel = $grupoLabels[$user['grupo_mapa_ferias'] ?? 'geral'] ?? '<span class="badge bg-secondary">Geral</span>';
                                ?>
                                <tr>
                                    <td><?= esc($user['name']) ?></td>
                                    <td><?= $user['NIF'] ?></td>
                                    <td>
                                        <?= $user['cod_funcionario'] ? esc($user['cod_funcionario']) : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td>
                                        <?= $user['telefone'] ? esc($user['telefone']) : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td>
                                        <?= $user['grupo_id'] ? $user['grupo_id'] : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td>
                                        <?= $user['categoria'] ? esc($user['categoria']) : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td>
                                        <?= $user['escola_nome'] ? esc($user['escola_nome']) : '<span class="text-muted">-</span>' ?>
                                    </td>
                                    <td><?= $grupoLabel ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" 
                                                onclick='editarUtilizador(<?= json_encode($user, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                            <i class="bi bi-pencil"></i>
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
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#utilizadoresTable').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        pageLength: 25,
        order: [[0, 'asc']]
    });
});

function editarUtilizador(user) {
    const categorias = <?= json_encode($categorias) ?>;
    const escolas = <?= json_encode($escolas) ?>;
    
    // Construir options de categorias
    let categoriasOptions = '<option value="">Selecione...</option>';
    categorias.forEach(function(cat) {
        const selected = cat === user.categoria ? 'selected' : '';
        categoriasOptions += `<option  value="${cat}" ${selected}>${cat}</option>`;
    });
    
    // Construir options de escolas
    let escolasOptions = '<option value="">Selecione...</option>';
    escolas.forEach(function(escola) {
        const selected = escola.id == user.escola_servico ? 'selected' : '';
        escolasOptions += `<option value="${escola.id}" ${selected}>${escola.nome}</option>`;
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
                
                <div class="form-group mb-3">
                    <label><strong>Escola de Serviço:</strong></label>
                    <select id="escola_servico" class="form-control">
                        ${escolasOptions}
                    </select>
                    <small class="text-muted">Escola onde leciona</small>
                </div>
                <div class="form-group mb-3">
                    <label><strong>Grupo no Mapa de Férias:</strong></label>
                    <select id="grupo_mapa_ferias" class="form-control">
                        <option value="geral" ${user.grupo_mapa_ferias === 'geral' || !user.grupo_mapa_ferias ? 'selected' : ''}>Geral (mapa padrão)</option>
                        <option value="direcao" ${user.grupo_mapa_ferias === 'direcao' ? 'selected' : ''}>Direção (férias pelo Conselho Geral)</option>
                        <option value="tecnico_superior" ${user.grupo_mapa_ferias === 'tecnico_superior' ? 'selected' : ''}>Técnico Superior (mapa próprio)</option>
                    </select>
                    <small class="text-muted">Define em que mapa de férias este docente aparece</small>
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
                categoria: document.getElementById('categoria').value,
                escola_servico: document.getElementById('escola_servico').value,
                grupo_mapa_ferias: document.getElementById('grupo_mapa_ferias').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('ferias/atualizar-utilizador') ?>',
                type: 'POST',
                data: result.value,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: response.message || 'Utilizador atualizado com sucesso',
                        confirmButtonText: 'OK'
                    }).then(() => location.reload());
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
</script>
<?= $this->endSection() ?>
