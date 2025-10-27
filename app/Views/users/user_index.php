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
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalImportarUsers">
                        <i class="bi bi-file-earmark-arrow-up"></i> Importar CSV
                    </button>
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
                            <th>Telefone</th>
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
                                <label for="userTelefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="userTelefone" name="telefone" placeholder="+351 912 345 678" maxlength="20">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
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
                                    <option value="1">Professor</option>
                                    <option value="2">Professor CP</option>
                                    <option value="3">Moderador</option>
                                    <option value="5">Técnico</option>
                                    <option value="6">Direção</option>
                                    <option value="8">Administrador</option>
                                    <option value="9">Super Admin</option>
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
                                    <option value="2">Pendente</option>
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
                                <td><strong>Telefone:</strong></td>
                                <td id="viewUserTelefone"></td>
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

<!-- Modal Importar Utilizadores CSV -->
<div class="modal fade" id="modalImportarUsers" tabindex="-1" aria-labelledby="modalImportarUsersTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImportarUsersTitle">Importar Utilizadores - CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formImportarUsers" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle"></i> Formato do Ficheiro CSV</h6>
                        <ul class="mb-0">
                            <li><strong>Extensão:</strong> .csv</li>
                            <li><strong>Separador:</strong> Ponto e vírgula (;)</li>
                            <li><strong>Encoding:</strong> UTF-8 ou Windows-1252</li>
                            <li><strong>Colunas obrigatórias (ordem fixa):</strong>
                                <ol class="mt-2">
                                    <li><code>Name</code> - Nome do utilizador (opcional)</li>
                                    <li><code>Email</code> - Email válido (obrigatório)</li>
                                    <li><code>NIF</code> - Número de contribuinte <span class="badge bg-danger">OBRIGATÓRIO</span></li>
                                    <li><code>Telefone</code> - Número de telefone (opcional)</li>
                                    <li><code>grupo_id</code> - ID do grupo (opcional, deixar vazio se não aplicável)</li>
                                </ol>
                            </li>
                            <li><strong>Primeira linha:</strong> Cabeçalho (será ignorado)</li>
                            <li class="text-danger mt-2"><strong>IMPORTANTE:</strong> Linhas sem NIF serão ignoradas e incluídas no relatório de erros</li>
                        </ul>
                        <p class="mt-2 mb-0"><strong>Exemplo:</strong><br>
                        <code>Name;Email;NIF;Telefone;grupo_id<br>
                        João Silva;joao.silva@exemplo.pt;123456789;912345678;1<br>
                        Maria Santos;maria.santos@exemplo.pt;987654321;913456789;</code></p>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="csv_file_users">Selecionar Ficheiro CSV *</label>
                        <input type="file" class="form-control" id="csv_file_users" name="csv_file" accept=".csv" required>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="skip_duplicates_users" name="skip_duplicates" checked>
                        <label class="form-check-label" for="skip_duplicates_users">
                            Ignorar emails duplicados (não atualizar registos existentes)
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="download_errors" name="download_errors" checked>
                        <label class="form-check-label" for="download_errors">
                            Gerar ficheiro com linhas rejeitadas (download automático)
                        </label>
                    </div>
                    
                    <!-- Progress bar -->
                    <div class="progress" id="importProgressUsers" style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                             style="width: 0%;" id="importProgressBarUsers">0%</div>
                    </div>
                    
                    <!-- Resultado da importação -->
                    <div id="importResultUsers" class="mt-3" style="display: none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnImportarUsers">
                        <i class="bi bi-upload"></i> Importar
                    </button>
                </div>
            </form>
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
            { data: 4, name: 'telefone' },
            { data: 5, name: 'NIF' },
            { data: 6, name: 'level' },
            { data: 7, name: 'status' },
            { data: 8, name: 'created_at' },
            { data: 9, name: 'actions', orderable: false, searchable: false }
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
        
        // Desabilitar botão de submit
        $('#saveUserBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> A guardar...');
        
        var userId = $('#userId').val();
        var url = userId ? '<?= base_url('users/update') ?>/' + userId : '<?= base_url('users/create') ?>';
        var fileInput = document.getElementById('userProfileImg');
        
        // Verificar se há ficheiro para upload
        if (fileInput.files.length > 0) {
            // Fazer upload da imagem primeiro
            var uploadFormData = new FormData();
            uploadFormData.append('profile_image', fileInput.files[0]);
            
            $.ajax({
                url: '<?= base_url('users/uploadProfileImage') ?>',
                type: 'POST',
                data: uploadFormData,
                processData: false,
                contentType: false,
                success: function(uploadResponse) {
                    if (uploadResponse.success) {
                        // Upload bem-sucedido, agora submeter o formulário com o nome do ficheiro
                        submitUserForm(url, uploadResponse.filename);
                    } else {
                        showToast('error', uploadResponse.message || 'Erro ao fazer upload da imagem');
                        $('#saveUserBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
                    }
                },
                error: function() {
                    showToast('error', 'Erro ao fazer upload da imagem');
                    $('#saveUserBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Guardar');
                }
            });
        } else {
            // Sem ficheiro, submeter formulário normalmente
            submitUserForm(url, null);
        }
    });

    // Função auxiliar para submeter o formulário
    function submitUserForm(url, profileImgFilename) {
        var formData = new FormData(document.getElementById('userForm'));
        
        // Se houver imagem, adicionar o nome do ficheiro
        if (profileImgFilename) {
            formData.append('profile_img', profileImgFilename);
        }
        
        // Remover o campo file input do FormData (já foi feito upload)
        formData.delete('profile_image');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    var userModal = bootstrap.Modal.getInstance(document.getElementById('userModal'));
                    if (userModal) userModal.hide();
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
    }

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
    var userModal = new bootstrap.Modal(document.getElementById('userModal'));
    userModal.show();
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
                $('#userTelefone').val(user.telefone);
                $('#userNIF').val(user.NIF);
                $('#userOAuthId').val(user.oauth_id);
                $('#userGrupoId').val(user.grupo_id);
                $('#userLevel').val(user.level);
                $('#userStatus').val(user.status);
                
                $('#userModalLabel').text('Editar Utilizador');
                var userModal = new bootstrap.Modal(document.getElementById('userModal'));
                userModal.show();
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
                $('#viewUserTelefone').text(user.telefone || 'N/A');
                $('#viewUserNIF').text(user.NIF || 'N/A');
                $('#viewUserOAuthId').text(user.oauth_id || 'N/A');
                $('#viewUserGrupoId').text(user.grupo_id || 'N/A');
                
                // Nível
                var levels = {
                    0: 'Utilizador',
                    1: 'Professor',
                    2: 'Professor CP',
                    3: 'Moderador',
                    5: 'Técnico',
                    6: 'Direção',
                    8: 'Administrador',
                    9: 'Super Admin'
                };
                $('#viewUserLevel').text(levels[user.level] || 'Desconhecido');
                
                // Status
                if(user.status == null || user.status == 2) // Pendente se nulo
                    {
                       statusBadge = '<span class="badge bg-warning text-dark">Pendente</span>';
                    }
                    else if(user.status === 1) // Ativo
                    {
                       statusBadge = '<span class="badge bg-success">Ativo</span>';
                    }
                    else if(user.status === 0) // Inativo
                    {
                       statusBadge = '<span class="badge bg-danger">Inativo</span>';
                    }
           
                    
                $('#viewUserStatus').html(statusBadge);
                
                // Imagem
                var imgSrc;
                if (user.profile_img && user.profile_img.startsWith("http" )) {
                    imgSrc = user.profile_img;
                } else if (user.profile_img && user.profile_img !== "default.png") {
                    imgSrc = '<?= base_url("writable/uploads/profiles/") ?>' + user.profile_img;
                } else {
                    imgSrc = '<?= base_url("assets/img/default.png") ?>';
                }
                $('#viewUserImg').attr('src', imgSrc);
                
                // Datas
                $('#viewUserCreated').text(formatDate(user.created_at));
                $('#viewUserUpdated').text(user.updated_at ? formatDate(user.updated_at) : 'N/A');
                
                // Bootstrap 5 - usar Modal API nativa
                var viewModal = new bootstrap.Modal(document.getElementById('viewUserModal'));
                viewModal.show();
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

// Handler para importação de utilizadores CSV
$('#formImportarUsers').on('submit', function(e) {
    e.preventDefault();
    
    var formData = new FormData(this);
    var downloadErrors = $('#download_errors').is(':checked');
    
    // Mostrar progress bar
    $('#importProgressUsers').show();
    $('#importProgressBarUsers').css('width', '0%').text('0%');
    $('#importResultUsers').hide();
    $('#btnImportarUsers').prop('disabled', true);
    
    $.ajax({
        url: '<?= base_url('users/importar') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function(evt) {
                if (evt.lengthComputable) {
                    var percentComplete = (evt.loaded / evt.total) * 100;
                    $('#importProgressBarUsers').css('width', percentComplete + '%')
                        .text(Math.round(percentComplete) + '%');
                }
            }, false);
            return xhr;
        },
        success: function(response) {
            $('#importProgressBarUsers').css('width', '100%').text('100%');
            $('#btnImportarUsers').prop('disabled', false);
            
            if(response.success) {
                var resultHtml = '<div class="alert alert-success">';
                resultHtml += '<h6><i class="bi bi-check-circle"></i> Importação Concluída!</h6>';
                resultHtml += '<ul class="mb-0">';
                resultHtml += '<li><strong>Importados:</strong> ' + response.imported + '</li>';
                resultHtml += '<li><strong>Ignorados (duplicados):</strong> ' + response.skipped + '</li>';
                resultHtml += '<li><strong>Erros (sem NIF ou inválidos):</strong> ' + response.errors + '</li>';
                resultHtml += '</ul>';
                
                if(response.message) {
                    resultHtml += '<p class="mt-2 mb-0"><small>' + response.message + '</small></p>';
                }
                
                // Download do ficheiro de erros se existir
                if(response.errors > 0 && downloadErrors && response.error_file) {
                    resultHtml += '<hr>';
                    resultHtml += '<a href="' + response.error_file + '" class="btn btn-sm btn-danger" download>';
                    resultHtml += '<i class="bi bi-download"></i> Download Linhas Rejeitadas (' + response.errors + ')';
                    resultHtml += '</a>';
                    
                    // Trigger automático do download
                    setTimeout(function() {
                        window.location.href = response.error_file;
                    }, 1000);
                }
                
                resultHtml += '</div>';
                $('#importResultUsers').html(resultHtml).show();
                
                // Recarregar tabela
                table.ajax.reload();
                
                // Limpar form após 5 segundos
                setTimeout(function() {
                    $('#formImportarUsers')[0].reset();
                    $('#importProgressUsers').hide();
                    $('#importResultUsers').hide();
                }, 5000);
                
            } else {
                var resultHtml = '<div class="alert alert-danger">';
                resultHtml += '<h6><i class="bi bi-x-circle"></i> Erro na Importação</h6>';
                resultHtml += '<p class="mb-0">' + (response.message || 'Erro desconhecido') + '</p>';
                resultHtml += '</div>';
                $('#importResultUsers').html(resultHtml).show();
            }
        },
        error: function(xhr) {
            $('#btnImportarUsers').prop('disabled', false);
            var errorMsg = 'Erro ao processar ficheiro';
            if(xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            
            var resultHtml = '<div class="alert alert-danger">';
            resultHtml += '<h6><i class="bi bi-x-circle"></i> Erro</h6>';
            resultHtml += '<p class="mb-0">' + errorMsg + '</p>';
            resultHtml += '</div>';
            $('#importResultUsers').html(resultHtml).show();
        }
    });
});

// Resetar modal ao fechar
$('#modalImportarUsers').on('hidden.bs.modal', function() {
    $('#formImportarUsers')[0].reset();
    $('#importProgressUsers').hide();
    $('#importResultUsers').hide();
    $('#btnImportarUsers').prop('disabled', false);
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>