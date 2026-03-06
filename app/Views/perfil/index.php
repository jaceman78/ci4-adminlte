<?= $this->extend('layout/master') ?>

<?= $this->section('title') ?>
Perfil
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= site_url('/dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item active">Perfil</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile text-center">
                            <?php
                            $profileImg = $user['profile_img'] ?? '';
                            if ($profileImg && str_starts_with($profileImg, 'http')) {
                                $profileUrl = $profileImg;
                            } elseif ($profileImg && $profileImg !== 'default.png') {
                                $profileUrl = base_url('writable/uploads/profiles/' . $profileImg);
                            } else {
                                $profileUrl = base_url('assets/img/default.png');
                            }
                            ?>

                            <div class="text-center mb-3">
                                <img id="profileImagePreview" class="profile-user-img img-fluid img-circle" src="<?= esc($profileUrl) ?>" alt="Imagem de Perfil">
                            </div>

                            <h3 class="profile-username mb-1"><?= esc($user['name'] ?? '') ?></h3>
                            <p class="text-muted mb-2">Grupo de Recrutamento: <?= esc($user['grupo_id'] ?? '-') ?></p>

                            <button type="button" id="btnAlterarFoto" class="btn btn-primary btn-sm">
                                <i class="fas fa-camera"></i> Alterar Foto
                            </button>
                            <input type="file" id="profileImageInput" name="profile_image" accept="image/*" class="d-none">
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Dados do Perfil</h3>
                        </div>
                        <div class="card-body">
                            <form id="profileForm">
                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Nome Completo</label>
                                            <input type="text" class="form-control" value="<?= esc($user['name'] ?? '') ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" value="<?= esc($user['email'] ?? '') ?>" disabled>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Telefone</label>
                                            <input type="text" name="telefone" id="telefone" class="form-control" value="<?= esc($user['telefone'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label>Grupo de Recrutamento</label>
                                            <input type="text" class="form-control" value="<?= esc($user['grupo_id'] ?? '-') ?>" disabled>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="profile_img" id="profile_img" value="<?= esc($user['profile_img'] ?? '') ?>">

                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar Alterações
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">Disciplinas &amp; Turmas</h3>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($disciplinasTurmas)): ?>
                                <div class="p-3 text-center text-muted">
                                    Nenhuma disciplina/turma associada ao seu NIF.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>Disciplina</th>
                                                <th>Turma</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($disciplinasTurmas as $item): ?>
                                                <tr>
                                                    <td><?= esc($item['disciplina']) ?></td>
                                                    <td><?= esc($item['turma']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAlterarFoto = document.getElementById('btnAlterarFoto');
    const fileInput = document.getElementById('profileImageInput');
    const previewImg = document.getElementById('profileImagePreview');
    const hiddenProfileImg = document.getElementById('profile_img');

    if (btnAlterarFoto && fileInput) {
        btnAlterarFoto.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('profile_image', file);

            fetch('<?= base_url('users/uploadProfileImage') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hiddenProfileImg.value = data.filename;
                    if (data.url && previewImg) {
                        previewImg.src = data.url;
                    }
                    if (typeof showToast === 'function') {
                        showToast('success', data.message || 'Imagem atualizada com sucesso.');
                    }
                } else {
                    if (typeof showToast === 'function') {
                        showToast('error', data.message || 'Erro ao fazer upload da imagem.');
                    }
                }
            })
            .catch(() => {
                if (typeof showToast === 'function') {
                    showToast('error', 'Erro ao fazer upload da imagem.');
                }
            });
        });
    }

    const profileForm = document.getElementById('profileForm');
    if (profileForm) {
        profileForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(profileForm);

            fetch('<?= base_url('perfil/update') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (typeof showToast === 'function') {
                    showToast(data.success ? 'success' : 'error', data.message || 'Erro ao guardar alterações.');
                }
            })
            .catch(() => {
                if (typeof showToast === 'function') {
                    showToast('error', 'Erro ao guardar alterações.');
                }
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
