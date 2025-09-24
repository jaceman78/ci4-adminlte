<?= $this->extend('layout/master') ?>

<?= $this->section('pageHeader') ?>
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Gestão de Utilizadores</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Utilizadores</li>
        </ol>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lista de Utilizadores</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-primary btn-sm" onclick="openCreateModal()">
                        <i class="fas fa-plus"></i> Novo Utilizador
                    </button>
                    <button type="button" class="btn btn-success btn-sm" onclick="exportCSV()">
                        <i class="fas fa-download"></i> Exportar CSV
                    </button>
                </div>
            </div>
            <div class="card-body">
                <table id="usersTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>NIF</th>
                            <th>Nível</th>
                            <th>Status</th>
                            <th>Data Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Dados carregados via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Criar/Editar Utilizador -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Novo Utilizador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userName" class="form-label">Nome</label>
                                <input type="text" class="form-control" id="userName" name="name" placeholder="Nome completo">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="userEmail" name="email" placeholder="email@exemplo.com" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userNIF" class="form-label">NIF</label>
                                <input type="number" class="form-control" id="userNIF" name="NIF" placeholder="123456789">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="userOAuthId" class="form-label">OAuth ID</label>
                                <input type="text" class="form-control" id="userOAuthId" name="oauth_id" placeholder="OAuth ID">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="userGrupoId" class="form-label">Grupo ID</label>
                                <input type="number" class="form-control" id="userGrupoId" name="grupo_id" placeholder="1">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="userLevel" class="form-label">Nível</label>
                                <select class="form-select" id="userLevel" name="level">
                                    <option value="0">Utilizador</option>
                                    <option value="1">Moderador</option>
                                    <option value="2">Administrador</option>
                                    <option value="3">Super Admin</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="userStatus" class="form-label">Status</label>
                                <select class="form-select" id="userStatus" name="status">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="userProfileImg" class="form-label">Imagem de Perfil</label>
                                <input type="file" class="form-control" id="userProfileImg" name="profile_image" accept="image/*">
                                <div class="form-text">Formatos aceites: JPG, PNG, GIF. Tamanho máximo: 2MB</div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div id="imagePreview" class="mt-2" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="saveUserBtn">
                        <i class="fas fa-save"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Ver Utilizador -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewUserModalLabel">Detalhes do Utilizador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <img id="viewUserImg" src="" alt="Foto de Perfil" class="img-fluid rounded-circle mb-3" style="max-width: 120px;">
                    </div>
                    <div class="col-md-9">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID:</strong></td>
                                <td id="viewUserId"></td>
                            </tr>
                            <tr>
                                <td><strong>Nome:</strong></td>
                                <td id="viewUserName"></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td id="viewUserEmail"></td>
                            </tr>
                            <tr>
                                <td><strong>NIF:</strong></td>
                                <td id="viewUserNIF"></td>
                            </tr>
                            <tr>
                                <td><strong>OAuth ID:</strong></td>
                                <td id="viewUserOAuthId"></td>
                            </tr>
                            <tr>
                                <td><strong>Grupo ID:</strong></td>
                                <td id="viewUserGrupoId"></td>
                            </tr>
                            <tr>
                                <td><strong>Nível:</strong></td>
                                <td id="viewUserLevel"></td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td id="viewUserStatus"></td>
                            </tr>
                            <tr>
                                <td><strong>Data Criação:</strong></td>
                                <td id="viewUserCreated"></td>
                            </tr>
                            <tr>
                                <td><strong>Última Atualização:</strong></td>
                                <td id="viewUserUpdated"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Inicializar DataTable
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('users/getDataTable') ?>',
            type: 'POST',
                        data: function (d) {
                // Adiciona o cabeçalho X-Requested-With para que o CodeIgniter reconheça como AJAX
                d['X-Requested-With'] = 'XMLHttpRequest'; 
                return d;
            }
        },
        columns: [
            { data: 0, name: 'id' },
            { data: 1, name: 'profile_img', orderable: false, searchable: false },
            { data: 2, name: 'name' },
            { data: 3, name: 'email' },
            { data: 4, name: 'NIF' },
            { data: 5, name: 'level' },
            { data: 6, name: 'status' },
            { data: 7, name: 'created_at' },
            { data: 8, name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json'
        },
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']]
    });

    // Form submission
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var userId = $('#userId').val();
        var url = userId ? '<?= base_url('users/update') ?>/' + userId : '<?= base_url('users/create') ?>';
        var method = 'POST';

        // Desabilitar botão de submit
        $('#saveUserBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> A guardar...');

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#userModal').modal('hide');
                    table.ajax.reload();
                    resetForm();
                } else {
                    showToast('error', response.message);
                    if (response.errors) {
                        displayFormErrors(response.errors);
                    }
                }
            },
            error: function(xhr) {
                showToast('error', 'Erro ao processar pedido');
                console.error(xhr.responseText);
            },
            complete: function() {
                $('#saveUserBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
            }
        });
    });

    // Preview de imagem
    $('#userProfileImg').on('change', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImg').attr('src', e.target.result);
                $('#imagePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });
});

// Função para abrir modal de criação
function openCreateModal() {
    resetForm();
    $('#userModalLabel').text('Novo Utilizador');
    $('#userModal').modal('show');
}

// Função para editar utilizador
function editUser(id) {
    $.ajax({
        url: '<?= base_url('users/getUser') ?>/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var user = response.data;
                
                $('#userId').val(user.id);
                $('#userName').val(user.name);
                $('#userEmail').val(user.email);
                $('#userNIF').val(user.NIF);
                $('#userOAuthId').val(user.oauth_id);
                $('#userGrupoId').val(user.grupo_id);
                $('#userLevel').val(user.level);
                $('#userStatus').val(user.status);
                
                $('#userModalLabel').text('Editar Utilizador');
                $('#userModal').modal('show');
            } else {
                showToast('error', response.message || 'Erro ao carregar dados do utilizador');
            }
        },
        error: function() {
            showToast('error', 'Erro ao carregar dados do utilizador');
        }
    });
}

// Função para ver utilizador
function viewUser(id) {
    $.ajax({
        url: '<?= base_url('users/getUser') ?>/' + id,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                var user = response.data;
                
                $('#viewUserId').text(user.id);
                $('#viewUserName').text(user.name || 'N/A');
                $('#viewUserEmail').text(user.email);
                $('#viewUserNIF').text(user.NIF || 'N/A');
                $('#viewUserOAuthId').text(user.oauth_id || 'N/A');
                $('#viewUserGrupoId').text(user.grupo_id || 'N/A');
                
                // Nível
                var levels = ['Utilizador', 'Moderador', 'Administrador', 'Super Admin'];
                $('#viewUserLevel').text(levels[user.level] || 'Desconhecido');
                
                // Status
                var statusBadge = user.status == 1 
                    ? '<span class="badge bg-success">Ativo</span>' 
                    : '<span class="badge bg-danger">Inativo</span>';
                $('#viewUserStatus').html(statusBadge);
                
                // Imagem
                var imgSrc;
                if (user.profile_img && user.profile_img.startsWith("http" )) {
                    imgSrc = user.profile_img;
                } else if (user.profile_img && user.profile_img !== "default.png") {
                    imgSrc = '<?= base_url("uploads/profiles/") ?>' + user.profile_img;
                } else {
                    imgSrc = '<?= base_url("assets/img/default.png") ?>';
                }
                $('#viewUserImg').attr('src', imgSrc);
                
                // Datas
                $('#viewUserCreated').text(formatDate(user.created_at));
                $('#viewUserUpdated').text(user.updated_at ? formatDate(user.updated_at) : 'N/A');
                
                $('#viewUserModal').modal('show');
            } else {
                showToast('error', response.message || 'Erro ao carregar dados do utilizador');
            }
        },
        error: function() {
            showToast('error', 'Erro ao carregar dados do utilizador');
        }
    });
}

// Função para eliminar utilizador
function deleteUser(id) {
    if (confirm('Tem a certeza que deseja eliminar este utilizador?')) {
        $.ajax({
            url: '<?= base_url('users/delete') ?>/' + id,
            type: 'POST',
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    $('#usersTable').DataTable().ajax.reload();
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'Erro ao eliminar utilizador');
            }
        });
    }
}

// Função para exportar CSV
function exportCSV() {
    window.location.href = '<?= base_url('users/exportCSV') ?>';
}

// Função para resetar formulário
function resetForm() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#imagePreview').hide();
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
}

// Função para mostrar erros do formulário
function displayFormErrors(errors) {
    $('.form-control').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    
    for (var field in errors) {
        var input = $('[name="' + field + '"]');
        input.addClass('is-invalid');
        input.siblings('.invalid-feedback').text(errors[field]);
    }
}

// Função para mostrar toasts
function showToast(type, message) {
    var bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
    var icon = type === 'success' ? 'fas fa-check' : 'fas fa-exclamation-triangle';
    
    var toast = `
        <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="${icon}"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    // Criar container de toasts se não existir
    if ($('#toastContainer').length === 0) {
        $('body').append('<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }
    
    var $toast = $(toast);
    $('#toastContainer').append($toast);
    
    var bsToast = new bootstrap.Toast($toast[0]);
    bsToast.show();
    
    // Remover toast após ser escondido
    $toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

// Função para formatar data
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    var date = new Date(dateString);
    return date.toLocaleDateString('pt-PT') + ' ' + date.toLocaleTimeString('pt-PT', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

</script>
<?= $this->endSection() ?>
    
<?= $this->endSection() ?>

