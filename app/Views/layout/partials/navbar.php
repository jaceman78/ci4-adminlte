<nav class="app-header navbar navbar-expand">
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

            <!--begin::Notifications-->
            <li class="nav-item dropdown" id="notif-dropdown-li">
              <a class="nav-link" href="#" data-bs-toggle="dropdown" aria-expanded="false" id="notif-bell-btn" title="Notificações">
                <i class="far fa-bell"></i>
                <span class="badge badge-danger navbar-badge d-none" id="notif-badge">0</span>
              </a>
              <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end pt-0" id="notif-dropdown-menu">
                <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom bg-light">
                  <span class="fw-bold" id="notif-header-text">Notificações</span>
                  <a href="#" class="small text-muted" id="notif-mark-all-btn">Marcar todas como lidas</a>
                </div>
                <div id="notif-list" style="max-height:320px;overflow-y:auto;">
                  <div class="text-center text-muted py-3 small" id="notif-empty-msg">Sem notificações</div>
                </div>
              </div>
            </li>
            <!--end::Notifications-->

      <!-- user -->
       <!--begin::User Menu Dropdown-->
        <li class="nav-item dropdown user-menu">
          <?php
            $navbarUser = session()->get('LoggedUserData') ?? [];
            $navbarImg  = $navbarUser['profile_img'] ?? '';

            if ($navbarImg && str_starts_with($navbarImg, 'http')) {
                $navbarImgUrl = $navbarImg;
            } elseif ($navbarImg && $navbarImg !== 'default.png') {
                $navbarImgUrl = base_url('writable/uploads/profiles/' . $navbarImg);
            } else {
                $navbarImgUrl = base_url('assets/img/default.png');
            }

            // Se impersonificação ativa, mostrar utilizador impersonado no dropdown
            $impersonatedUser = session()->get('ImpersonatedUserData');
            $displayUser = $impersonatedUser ?? $navbarUser;
            $displayImg  = $displayUser['profile_img'] ?? '';
            if ($displayImg && str_starts_with($displayImg, 'http')) {
                $displayImgUrl = $displayImg;
            } elseif ($displayImg && $displayImg !== 'default.png') {
                $displayImgUrl = base_url('writable/uploads/profiles/' . $displayImg);
            } else {
                $displayImgUrl = base_url('assets/img/default.png');
            }
          ?>
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="<?= esc($displayImgUrl) ?>" class="user-image rounded-circle shadow" alt="User Image">
            <span class="d-none d-md-inline">
              <?= esc($displayUser['name'] ?? '') ?>
              <?php if ($impersonatedUser): ?>
                <span class="badge bg-danger ms-1" style="font-size:10px;">impersonado</span>
              <?php endif; ?>
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
            <li class="dropdown-header text-center">
              <img src="<?= esc($displayImgUrl) ?>" class="user-image rounded-circle shadow" alt="User Image">
              <p><?= esc($displayUser['name'] ?? '') ?><br><small><?= get_user_level_name() ?></small></p>
              <?php if ($impersonatedUser): ?>
              <p class="text-danger small mt-1"><i class="fas fa-user-secret"></i> Admin: <?= esc($navbarUser['name'] ?? '') ?></p>
              <?php endif; ?>
            </li>
            <li><hr class="dropdown-divider"></li>
            <?php if (!$impersonatedUser): ?>
            <li><a href="<?= base_url('perfil') ?>" class="dropdown-item">Perfil</a></li>
            <?php endif; ?>
            <?php if (($navbarUser['level'] ?? 0) >= 7 && !$impersonatedUser): ?>
            <li><a href="#" class="dropdown-item text-warning" data-bs-toggle="modal" data-bs-target="#modalImpersonation"><i class="fas fa-user-secret me-1"></i> Ver como</a></li>
            <?php endif; ?>
            <?php if ($impersonatedUser): ?>
            <li><a href="<?= base_url('impersonation/stop') ?>" class="dropdown-item text-danger"><i class="fas fa-user-secret me-1"></i> Terminar Impersonificação</a></li>
            <?php endif; ?>
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

          <div class="form-group">
            <label for="sugestao_anexos">Anexos (Opcional)</label>
            <input type="file" class="form-control" id="sugestao_anexos" name="anexos[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">
            <small class="form-text text-muted">
              <i class="fas fa-paperclip"></i> Pode anexar imagens, PDFs ou documentos (máx. 5 ficheiros, 5MB cada).
            </small>
            <div id="preview_anexos" class="mt-2"></div>
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

<?php if ((session()->get('LoggedUserData')['level'] ?? 0) >= 8): ?>
<!-- Modal Impersonificação -->
<div class="modal fade" id="modalImpersonation" tabindex="-1" aria-labelledby="modalImpersonationLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="modalImpersonationLabel">
          <i class="fas fa-user-secret"></i> Ver como utilizador
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small">Pesquise um utilizador para ver a aplicação como ele a vê. As ações realizadas ficam registadas na sua conta.</p>
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="fas fa-search"></i></span>
          <input type="text" id="impersonationSearch" class="form-control" placeholder="Nome ou email..." autocomplete="off">
        </div>
        <div id="impersonationResults" class="list-group" style="max-height:300px; overflow-y:auto;"></div>
        <div id="impersonationEmpty" class="text-center text-muted py-3" style="display:none;">
          <i class="fas fa-user-slash fa-2x mb-2"></i><br>Nenhum utilizador encontrado
        </div>
        <div id="impersonationLoading" class="text-center py-3" style="display:none;">
          <div class="spinner-border spinner-border-sm text-warning"></div> A pesquisar...
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmação Impersonificação -->
<div class="modal fade" id="modalImpersonationConfirm" tabindex="-1" aria-labelledby="modalImpersonationConfirmLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content border-warning">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="modalImpersonationConfirmLabel">
          <i class="fas fa-user-secret"></i> Confirmar
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-1">Vai ver a aplicação como:</p>
        <p class="fw-bold fs-6 mb-0" id="impersonationConfirmName"></p>
        <p class="text-muted small mb-3" id="impersonationConfirmEmail"></p>
        <div class="alert alert-warning py-2 text-start small mb-0">
          <i class="fas fa-exclamation-triangle"></i>
          As suas ações ficarão registadas na sua conta de administrador.
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="button" class="btn btn-warning btn-sm" id="btnImpersonationConfirm">
          <i class="fas fa-user-secret"></i> Ver como este utilizador
        </button>
      </div>
    </div>
  </div>
</div>

<script>
(function() {
  let _impTimer = null;
  let _pendingUserId = null;
  let modalSearch  = null;
  let modalConfirm = null;

  document.addEventListener('DOMContentLoaded', function() {
    modalSearch  = new bootstrap.Modal(document.getElementById('modalImpersonation'));
    modalConfirm = new bootstrap.Modal(document.getElementById('modalImpersonationConfirm'));
  });

  document.getElementById('impersonationSearch').addEventListener('input', function() {
    const q = this.value.trim();
    const results = document.getElementById('impersonationResults');
    const empty   = document.getElementById('impersonationEmpty');
    const loading = document.getElementById('impersonationLoading');

    results.innerHTML = '';
    empty.style.display   = 'none';
    loading.style.display = 'none';

    if (q.length < 2) return;

    clearTimeout(_impTimer);
    _impTimer = setTimeout(function() {
      loading.style.display = 'block';
      fetch('<?= base_url('impersonation/search') ?>?q=' + encodeURIComponent(q), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(r => r.json())
      .then(data => {
        loading.style.display = 'none';
        results.innerHTML = '';
        if (!data.length) { empty.style.display = 'block'; return; }
        data.forEach(function(u) {
          const levelBadge = '<span class="badge bg-secondary ms-1">nível ' + u.level + '</span>';
          const btn = document.createElement('button');
          btn.type = 'button';
          btn.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
          btn.innerHTML = '<span><strong>' + u.name + '</strong> <small class="text-muted">' + u.email + '</small></span>' + levelBadge;
          btn.addEventListener('click', function() {
            _pendingUserId = u.id;
            document.getElementById('impersonationConfirmName').textContent  = u.name;
            document.getElementById('impersonationConfirmEmail').textContent = u.email;
            modalSearch.hide();
            modalConfirm.show();
          });
          results.appendChild(btn);
        });
      })
      .catch(() => { loading.style.display = 'none'; });
    }, 300);
  });

  document.getElementById('btnImpersonationConfirm').addEventListener('click', function() {
    if (!_pendingUserId) return;
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> A carregar...';

    fetch('<?= base_url('impersonation/start') ?>/' + _pendingUserId, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
        '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
      }
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        window.location.href = res.redirect;
      } else {
        modalConfirm.hide();
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-user-secret"></i> Ver como este utilizador';
        // Mostrar erro inline sem alert()
        const alertEl = document.createElement('div');
        alertEl.className = 'alert alert-danger alert-dismissible fade show position-fixed bottom-0 end-0 m-3';
        alertEl.style.zIndex = '99999';
        alertEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (res.message || 'Erro ao iniciar impersonificação.') +
          '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        document.body.appendChild(alertEl);
        setTimeout(() => alertEl.remove(), 5000);
      }
    })
    .catch(() => {
      modalConfirm.hide();
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-user-secret"></i> Ver como este utilizador';
    });
  });

  // Limpar ao fechar o modal de pesquisa
  document.getElementById('modalImpersonation').addEventListener('hidden.bs.modal', function() {
    document.getElementById('impersonationSearch').value = '';
    document.getElementById('impersonationResults').innerHTML = '';
    document.getElementById('impersonationEmpty').style.display = 'none';
  });

  // Limpar ao fechar o modal de confirmação sem confirmar
  document.getElementById('modalImpersonationConfirm').addEventListener('hidden.bs.modal', function() {
    _pendingUserId = null;
  });
})();
</script>
<?php endif; ?>

<script>
// ---------------------------------------------------------------
// Sistema de Notificações In-App
// ---------------------------------------------------------------
(function() {
    'use strict';

    const NOTIF_URL      = '<?= base_url('notificacoes/nao-lidas') ?>';
    const MARK_ALL_URL   = '<?= base_url('notificacoes/marcar-todas-lidas') ?>';
    const MARK_ONE_BASE  = '<?= base_url('notificacoes/marcar-lida') ?>';
    const POLL_INTERVAL  = 60000; // 60 segundos

    const badge    = document.getElementById('notif-badge');
    const list     = document.getElementById('notif-list');
    const emptyMsg = document.getElementById('notif-empty-msg');
    const header   = document.getElementById('notif-header-text');

    const tipoIcon = {
        success : '<i class="fas fa-check-circle text-success me-2"></i>',
        warning : '<i class="fas fa-exclamation-triangle text-warning me-2"></i>',
        danger  : '<i class="fas fa-times-circle text-danger me-2"></i>',
        info    : '<i class="fas fa-info-circle text-info me-2"></i>',
    };

    function formatRelTime(dt) {
        const diff = Math.floor((Date.now() - new Date(dt)) / 1000);
        if (diff < 60)    return 'agora mesmo';
        if (diff < 3600)  return Math.floor(diff/60) + ' min atrás';
        if (diff < 86400) return Math.floor(diff/3600) + 'h atrás';
        return Math.floor(diff/86400) + 'd atrás';
    }

    function renderNotif(n) {
        const icon = tipoIcon[n.tipo] || tipoIcon.info;
        const href = n.url ? n.url : '#';
        return `<a href="${href}" class="dropdown-item notif-item" data-id="${n.id}" style="white-space:normal;border-bottom:1px solid #f0f0f0;">
                    <div class="d-flex align-items-start py-1">
                        <div class="pt-1">${icon}</div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">${n.titulo}</div>
                            <div class="small text-muted">${n.mensagem}</div>
                            <div class="text-muted" style="font-size:0.72rem;">${formatRelTime(n.criado_em)}</div>
                        </div>
                    </div>
                </a>`;
    }

    function fetchNotifs() {
        fetch(NOTIF_URL, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
            .then(r => r.json())
            .then(data => {
                const total = data.total || 0;
                const notifs = data.notificacoes || [];

                // Badge
                if (total > 0) {
                    badge.textContent = total > 99 ? '99+' : total;
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }

                // Header
                header.textContent = total > 0 ? `Notificações (${total})` : 'Notificações';

                // Lista
                if (notifs.length === 0) {
                    list.innerHTML = '<div class="text-center text-muted py-3 small">Sem notificações pendentes</div>';
                } else {
                    list.innerHTML = notifs.map(renderNotif).join('');
                    // Marcar como lida ao clicar
                    list.querySelectorAll('.notif-item').forEach(el => {
                        el.addEventListener('click', function() {
                            const id = this.dataset.id;
                            fetch(`${MARK_ONE_BASE}/${id}`, {
                                method: 'POST',
                                headers: {'X-Requested-With': 'XMLHttpRequest'},
                            });
                            this.style.opacity = '0.5';
                        });
                    });
                }
            })
            .catch(() => {}); // falha silenciosa — não interrompe o utilizador
    }

    // Marcar todas como lidas
    const markAllBtn = document.getElementById('notif-mark-all-btn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            fetch(MARK_ALL_URL, {
                method: 'POST',
                headers: {'X-Requested-With': 'XMLHttpRequest'},
            }).then(() => fetchNotifs());
        });
    }

    // Atualizar ao abrir o dropdown
    const dropdownEl = document.getElementById('notif-dropdown-li');
    if (dropdownEl) {
        dropdownEl.addEventListener('show.bs.dropdown', fetchNotifs);
    }

    // Polling automático
    fetchNotifs();
    setInterval(fetchNotifs, POLL_INTERVAL);
})();
</script>
