<form id="userForm">
    <?= csrf_field() ?>
    <input type="hidden" name="id" value="<?= $user['id'] ?? '' ?>">
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?= $user['name'] ?? '' ?>" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= $user['email'] ?? '' ?>" required>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="NIF" class="form-label">NIF</label>
                <input type="text" class="form-control" id="NIF" name="NIF" 
                       value="<?= $user['NIF'] ?? '' ?>" maxlength="9">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="grupo_id" class="form-label">Grupo</label>
                <select class="form-select" id="grupo_id" name="grupo_id">
                    <option value="">Selecionar Grupo</option>
                    <!-- Populate with groups from database -->
                    <?php if (isset($grupos)): ?>
                        <?php foreach ($grupos as $grupo): ?>
                            <option value="<?= $grupo['id'] ?>" 
                                <?= (isset($user['grupo_id']) && $user['grupo_id'] == $grupo['id']) ? 'selected' : '' ?>>
                                <?= $grupo['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="level" class="form-label">Nível de Acesso</label>
                <select class="form-select" id="level" name="level" required>
                    <option value="0" <?= (isset($user['level']) && $user['level'] == 0) ? 'selected' : '' ?>>Utilizador</option>
                    <option value="1" <?= (isset($user['level']) && $user['level'] == 1) ? 'selected' : '' ?>>Editor</option>
                    <option value="2" <?= (isset($user['level']) && $user['level'] == 2) ? 'selected' : '' ?>>Moderador</option>
                    <option value="3" <?= (isset($user['level']) && $user['level'] == 3) ? 'selected' : '' ?>>Administrador</option>
                    <option value="4" <?= (isset($user['level']) && $user['level'] == 4) ? 'selected' : '' ?>>Super Admin</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="1" <?= (isset($user['status']) && $user['status'] == 1) ? 'selected' : '' ?>>Ativo</option>
                    <option value="0" <?= (isset($user['status']) && $user['status'] == 0) ? 'selected' : '' ?>>Inativo</option>
                    <option value="2" <?= (isset($user['status']) && $user['status'] == 2) ? 'selected' : '' ?>>Pendente</option>
                </select>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </div>
</form>