<aside class="app-sidebar bg-primary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="<?= base_url() ?>" class="brand-link">
      <img src="<?= base_url('adminlte/img/AdminLTELogo.png') ?>" alt="AdminLTE Logo"
           class="brand-image opacity-75 shadow">
      <span class="brand-text fw-light">AdminLTE 4 Side</span>
    </a>
  </div>

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
        <!--li class="nav-header">MENU</!--li>-->
           <li class="nav-item menu-close">
                <a href="#" class="nav-link active">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>
                    Reportar avarias
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?= base_url('tickets/novo') ?>" class="nav-link ">
                       <i class="nav-icon bi bi-circle"></i>
                      <p>Criar Novo Ticket</p>
                    </a>
                  </li>
                  <li class="nav-item">
                     <a href="<?= base_url('escolas') ?>" class="nav-link ">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Escolas</p>
                    </a>
                  </li>
                </ul>
              </li>
                <li class="nav-item menu-open">
                <a href="#" class="nav-link active">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>
                    Dashboard
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <?php $segments = explode('/', uri_string()); ?>
                    <a href="<?= base_url('users') ?>" class="nav-link <?= ($segments[0] ?? '') == 'users' ? 'active' : '' ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Utilizadores</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('escolas') ?>" class="nav-link <?= ($segments[0] ?? '') == 'escolas' ? 'active' : '' ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Escolas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('salas') ?>" class="nav-link <?= ($segments[0] ?? '') == 'salas' ? 'active' : '' ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>salas</p>
                    </a>
                  </li>
                    <li class="nav-item">
                    <a href="<?= base_url('equipamentos') ?>" class="nav-link <?= ($segments[0] ?? '') == 'equipamentos' ? 'active' : '' ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>equipamentos</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('tipos_equipamentos') ?>" class="nav-link <?= ($segments[0] ?? '') == 'tipos_equipamentos' ? 'active' : '' ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>tipos de equipamentos</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('tipos_avaria') ?>" class="nav-link <?= ($segments[0] ?? '') == 'tipos_avaria' ? 'active' : '' ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>tipos de avaria</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('materiais') ?>" class="nav-link <?= ($segments[0] ?? '') == 'materiais' ? 'active' : '' ?>">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>materiais</p>
                    </a>
                  </li>
                  <?php if ((session()->get('LoggedUserData')['level'] ?? 0) == 9): ?>
                  <li class="nav-item">
                      <a href="<?= base_url('logs') ?>" class="nav-link <?= ($segments[0] ?? '') == 'logs' ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-circle"></i>
                        <p>logs</p>
                      </a>
                  </li>
                  <?php endif; ?>
                </ul>
              </li>
      </ul>
    </nav>
  </div>
</aside>
<!--end::Sidebar-->