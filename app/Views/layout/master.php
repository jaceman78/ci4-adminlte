<!DOCTYPE html>
<html lang="pt">
<?= $this->include('layout/partials/head') ?>


<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
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

    
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>

  




