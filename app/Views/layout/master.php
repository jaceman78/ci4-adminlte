<!DOCTYPE html>
<html lang="pt">
<?= $this->include('layout/partials/head') ?>


<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

<!-- Preloader -->
<div id="esjb-preloader">
  <div class="spinner-container">
    <img src="<?= base_url('adminlte/img/logo_spin.png') ?>" alt="A carregar..." class="logo-3d">
  </div>
  <div class="text">Carregando...</div>
</div>

<div class="app-wrapper">

  <!-- Navbar -->
  <?= $this->include('layout/partials/navbar') ?>

  <!-- Sidebar -->
  <?= $this->include('layout/partials/sidebar') ?>

  <!-- Conteúdo principal -->
  <main class="app-main">
    <div class="content-header">
      <div class="container-fluid">
        <?= $this->renderSection('pageHeader') ?>
      </div>
    </div>

    <div class="app-content">
      <div class="container-fluid">
        <?= $this->renderSection('content') ?>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <?= $this->include('layout/partials/footer') ?>

   <?= $this->renderSection('scripts') ?> 

<script>
  window.addEventListener('load', function () {
    var el = document.getElementById('esjb-preloader');
    if (el) {
      el.classList.add('esjb-preloader-hide');
      setTimeout(function () { el.remove(); }, 600);
    }
  });

  // Partículas flutuantes do preloader
  (function () {
    var container = document.querySelector('#esjb-preloader .spinner-container');
    if (!container) return;
    for (var i = 0; i < 18; i++) {
      var p = document.createElement('div');
      p.className = 'light-particle';
      var size = Math.random() * 6 + 4;
      p.style.width  = size + 'px';
      p.style.height = size + 'px';
      p.style.left              = Math.random() * 100 + '%';
      p.style.top               = Math.random() * 100 + '%';
      p.style.animationDuration = (Math.random() * 5 + 4) + 's';
      p.style.animationDelay    = '-' + (Math.random() * 6) + 's';
      p.style.opacity           = Math.random() * 0.6 + 0.4;
      container.appendChild(p);
    }
  })();
</script>

    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>

  




