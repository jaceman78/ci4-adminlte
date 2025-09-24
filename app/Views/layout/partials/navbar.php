<nav class="app-header navbar navbar-expand bg-body">
  <div class="container-fluid">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
          <i class="bi bi-list"></i>
        </a>
      </li>
      <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Home</a></li>
      <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Contact</a></li>
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
              <p><?= session()->get("LoggedUserData")['name'] ?? ""; ?><br><small>Administrador</small></p>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a href="#" class="dropdown-item">Profile</a></li>
            <li><a href="<?=base_url("logout")?>" class="dropdown-item">Sign out</a></li>
          </ul>
        </li>

        

    </ul>
  </div>
</nav>
