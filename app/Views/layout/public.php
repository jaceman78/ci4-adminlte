<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> - Sistema de Gestão Escolar</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('adminlte/dist/css/adminlte.min.css') ?>">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <?= $this->renderSection('styles') ?>
</head>
<body class="hold-transition">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('/') ?>">
            <i class="fas fa-school"></i> <strong>AE João de Barros</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('privacy') ?>">
                        <i class="fas fa-shield-alt"></i> Privacidade
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('privacy/terms') ?>">
                        <i class="fas fa-file-contract"></i> Termos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= site_url('login') ?>">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<?= $this->renderSection('content') ?>

<!-- Footer -->
<footer class="bg-light py-4 mt-5">
    <div class="container text-center">
        <p class="text-muted mb-2">
            <small>
                Sistema de Gestão Escolar - Agrupamento de Escolas João de Barros<br>
                © <?= date('Y') ?> Todos os direitos reservados
            </small>
        </p>
        <p class="mb-0">
            <a href="<?= site_url('privacy') ?>" class="text-decoration-none me-3">Política de Privacidade</a>
            <a href="<?= site_url('privacy/terms') ?>" class="text-decoration-none">Termos de Serviço</a>
        </p>
    </div>
</footer>

<!-- jQuery -->
<script src="<?= base_url('adminlte/plugins/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('adminlte/dist/js/adminlte.min.js') ?>"></script>

<?= $this->renderSection('scripts') ?>

</body>
</html>
