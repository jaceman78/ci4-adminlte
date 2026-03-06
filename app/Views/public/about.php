<?= $this->extend('layout/public') ?>
<?= $this->section('title') ?>Sobre<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.about-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 0;
}
.timeline-item {
    border-left: 3px solid #667eea;
    padding-left: 30px;
    padding-bottom: 30px;
    position: relative;
}
.timeline-item::before {
    content: '';
    width: 15px;
    height: 15px;
    background: #667eea;
    border: 3px solid white;
    border-radius: 50%;
    position: absolute;
    left: -9px;
    top: 0;
}
.stat-card {
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.stat-number {
    font-size: 3rem;
    font-weight: bold;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header -->
<section class="about-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-5 fw-bold mb-3">Agrupamento de Escolas João de Barros</h1>
                <p class="lead">Servindo dois núcleos urbanos de uma freguesia que representa 30,1% da população do município do Seixal.</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold mb-4">Projeto Educativo</h2>
                <p class="lead text-muted">
                    O Projeto Educativo do Agrupamento de Escolas João de Barros constitui uma peça fundamental para o sucesso. Sucesso dos alunos, mas também sucesso dos docentes e de todos quantos dão o melhor de si em favor de uma causa, chamada Educação.
                </p>
                <p>
                    Promovemos uma formação integral, inclusiva e participativa, aliando tradição e inovação para responder às necessidades de uma comunidade educativa em crescimento.
                </p>
            </div>
        </div>

        <!-- Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card shadow">
                    <div class="stat-number">5</div>
                    <div class="mt-2">Escolas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card shadow">
                    <div class="stat-number">2508</div>
                    <div class="mt-2">Alunos</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card shadow">
                    <div class="stat-number">150+</div>
                    <div class="mt-2">Professores</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card shadow">
                    <div class="stat-number">2013</div>
                    <div class="mt-2">Fundação</div>
                </div>
            </div>
        </div>

        <!-- Removed funcionalidades timeline per request -->
    </div>
</section>

<!-- Technology Stack -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h3 class="fw-bold mb-4 text-center">Tecnologia</h3>
                <p class="text-center text-muted mb-4">Desenvolvido com tecnologias modernas e robustas</p>
                
                <div class="row g-3 text-center">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm p-3">
                            <h6 class="fw-bold mb-1">CodeIgniter 4</h6>
                            <small class="text-muted">Framework PHP</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm p-3">
                            <h6 class="fw-bold mb-1">Bootstrap 5</h6>
                            <small class="text-muted">Framework CSS</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm p-3">
                            <h6 class="fw-bold mb-1">MySQL</h6>
                            <small class="text-muted">Base de Dados</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm p-3">
                            <h6 class="fw-bold mb-1">Google OAuth</h6>
                            <small class="text-muted">Autenticação</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm p-3">
                            <h6 class="fw-bold mb-1">DataTables</h6>
                            <small class="text-muted">Gestão de Dados</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm p-3">
                            <h6 class="fw-bold mb-1">SweetAlert2</h6>
                            <small class="text-muted">Notificações</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Navegação simples -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mx-auto text-center">
                <h3 class="fw-bold mb-3">Tem Questões?</h3>
                <p class="text-muted mb-4">Entre em contacto connosco.</p>
                <a href="/public" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Voltar ao Início
                </a>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
