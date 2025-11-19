<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->renderSection('title') ?> - AE João de Barros</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; min-height: 100vh; display: flex; flex-direction: column; }
    .navbar { box-shadow: 0 2px 4px rgba(0,0,0,.08); padding-top: .25rem; padding-bottom: .25rem; }
    .navbar-brand img { height: 36px; width: auto; }
    .nav-link { font-weight: 500; transition: color 0.2s; padding: .25rem .5rem; }
        .nav-link:hover { color: #0d6efd !important; }
        main { flex: 1; }
        footer { margin-top: auto; background: #f8f9fa; border-top: 1px solid #dee2e6; }
    </style>
    
    <?= $this->renderSection('styles') ?>
</head>
<body>
<?php 
    $publicHost = getenv('PUBLIC_HOST') ?: env('PUBLIC_HOST', 'public.escoladigital.cloud');
    $publicUrl = 'https://' . $publicHost . '/';
?>
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="<?= $publicUrl ?>">
            <img src="https://www.aejoaodebarros.pt/images/template/logo_topo_cores1.png" alt="AE João de Barros" class="me-2">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item"><a class="nav-link" href="<?= $publicUrl ?>"><i class="bi bi-house-door"></i> Início</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $publicUrl ?>about"><i class="bi bi-info-circle"></i> Sobre</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $publicUrl ?>kit-digital"><i class="bi bi-laptop"></i> Kit Digital</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= site_url('privacy') ?>"><i class="bi bi-shield-check"></i> Privacidade</a></li>
                <li class="nav-item ms-lg-2"><a class="btn btn-primary btn-sm" href="<?= site_url('login') ?>"><i class="bi bi-box-arrow-in-right"></i> Área Restrita</a></li>
            </ul>
        </div>
    </div>
</nav>
<main><?= $this->renderSection('content') ?></main>
<footer class="py-4 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="text-muted mb-1"><strong>Agrupamento de Escolas João de Barros</strong></p>
                <p class="text-muted small mb-0"> <?= date('Y') ?> Todos os direitos reservados</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="<?= site_url('privacy') ?>" class="text-decoration-none text-muted me-3 small"><i class="bi bi-shield-check"></i> Política de Privacidade</a>
                <a href="<?= site_url('privacy/terms') ?>" class="text-decoration-none text-muted small"><i class="bi bi-file-text"></i> Termos de Serviço</a>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
