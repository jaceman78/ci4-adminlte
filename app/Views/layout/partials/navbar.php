<nav class="app-header navbar navbar-expand bg-body">
  <div class="container-fluid">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
          <i class="bi bi-list"></i>
        </a>
      </li>
      <li class="nav-item d-none d-md-block"><a href="<?= base_url() ?>" class="nav-link">Home</a></li>
      <li class="nav-item d-none d-md-block"><a href="https://sites.google.com/aejoaodebarros.pt/pagina-interna" class="nav-link" target="_blank">Página Interna</a></li>
      <li class="nav-item d-none d-md-block"><a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#modalSugestao"><i class="fas fa-lightbulb"></i> Caixa de Sugestões</a></li>
    </ul>

    <ul class="navbar-nav ms-auto">
      <!-- Pesquisar -->

            <li class="nav-item">
              <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
              </a>
            </li>
            <!--end::Fullscreen Toggle-->
      <!-- user -->
       <!--begin::User Menu Dropdown-->
        <li class="nav-item dropdown user-menu">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?= session()->get("LoggedUserData")['profile_img']; ?>" class="user-image rounded-circle shadow" alt="User Image">          
            <span class="d-none d-md-inline">
              <?= session()->get("LoggedUserData")['name'] ?? ""; ?>
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <li class="dropdown-header text-center">
              <img src="<?= session()->get("LoggedUserData")['profile_img']; ?>" class="user-image rounded-circle shadow" alt="User Image">
              <p><?= session()->get("LoggedUserData")['name'] ?? ""; ?><br><small><?= get_user_level_name() ?></small></p>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a href="#" class="dropdown-item">Perfil</a></li>
            <li><a href="<?=base_url("logout")?>" class="dropdown-item">Terminar Sessão</a></li>
          </ul>
        </li>

        

    </ul>
  </div>
</nav>

<!-- Modal Caixa de Sugestões -->
<div class="modal fade" id="modalSugestao" tabindex="-1" aria-labelledby="modalSugestaoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalSugestaoLabel">
          <i class="fas fa-lightbulb"></i> Caixa de Sugestões
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <form id="formSugestao">
        <div class="modal-body">
          <p class="text-muted">
            Partilhe as suas ideias, sugestões ou feedback connosco. A sua opinião é importante para melhorarmos continuamente!
          </p>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="sugestao_categoria">Categoria *</label>
                <select class="form-control" id="sugestao_categoria" name="categoria" required>
                  <option value="">Selecione...</option>
                  <option value="Funcionalidade Nova">Funcionalidade Nova</option>
                  <option value="Melhoria">Melhoria</option>
                  <option value="Bug/Erro">Bug/Erro</option>
                  <option value="Interface">Interface</option>
                  <option value="Desempenho">Desempenho</option>
                  <option value="Documentação">Documentação</option>
                  <option value="Outro">Outro</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="sugestao_prioridade">Prioridade *</label>
                <select class="form-control" id="sugestao_prioridade" name="prioridade" required>
                  <option value="media" selected>Média</option>
                  <option value="baixa">Baixa</option>
                  <option value="alta">Alta</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="sugestao_titulo">Título *</label>
            <input type="text" class="form-control" id="sugestao_titulo" name="titulo" required maxlength="200" placeholder="Resumo breve da sua sugestão">
            <small class="form-text text-muted">Máximo 200 caracteres</small>
          </div>

          <div class="form-group">
            <label for="sugestao_descricao">Descrição Detalhada *</label>
            <textarea class="form-control" id="sugestao_descricao" name="descricao" rows="6" required placeholder="Descreva a sua sugestão com o máximo de detalhes possível..."></textarea>
            <small class="form-text text-muted">Explique o que pretende, porque é importante e como poderia funcionar.</small>
          </div>

          <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle"></i> 
            <strong>Nota:</strong> A sua sugestão será analisada pela equipa responsável. Receberá uma resposta assim que possível.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Enviar Sugestão
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
