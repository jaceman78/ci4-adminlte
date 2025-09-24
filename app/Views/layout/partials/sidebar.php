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
                    <a href="<?= base_url('users') ?>" class="nav-link ">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Utilizadores</p>
                    </a>
                  </li>
                  <li class="nav-item">
                     <a href="<?= base_url('escolas') ?>" class="nav-link ">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Escolas</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?= base_url('salas') ?>" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>salas</p>
                    </a>
                  </li>
                                    <li class="nav-item">
                    <a href="<?= base_url('logs') ?>" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>logs</p>
                    </a>
                  </li>
                </ul>
              </li>
      </ul>
    </nav>
  </div>
</aside>
<!--end::Sidebar-->