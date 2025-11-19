<?= $this->extend('layout/public') ?>
<?= $this->section('title') ?>Início<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
    padding: 80px 0;
}
.feature-card { border: none; border-radius: 12px; transition: transform .2s, box-shadow .2s; }
.feature-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,.1); }
.icon-box { width:64px; height:64px; background: linear-gradient(135deg,#667eea 0%, #764ba2 100%); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:28px; color:#fff; margin-bottom:20px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Agrupamento de Escolas João de Barros</h1>
                <p class="lead mb-4">Plataforma digital para acesso a serviços e informações do nosso agrupamento.</p>

            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold mb-3">Serviços Disponíveis</h2>
        
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card feature-card shadow-sm h-100 position-relative">
                    <div style="position:absolute;top:0;right:0;z-index:2;">
                        <span class="badge bg-success rounded-start rounded-0 px-3 py-2" style="font-size:0.95em;">A decorrer</span>
                    </div>
                    <?php 
                        $publicHost = getenv('PUBLIC_HOST') ?: env('PUBLIC_HOST', 'public.escoladigital.cloud');
                        $kitDigitalUrl = 'https://' . $publicHost . '/kit-digital';
                    ?>
                    <a href="<?= $kitDigitalUrl ?>" class="stretched-link text-decoration-none text-reset">
                        <div class="card-body text-center p-4">
                            <div class="icon-box mx-auto"><i class="bi bi-laptop"></i></div>
                            <h5 class="fw-bold mb-3">Solicitar Kit Digital</h5>
                            <p class="text-muted">Requisição de Kit Digital para alunos do agrupamento. Serviço disponível online.</p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card shadow-sm h-100 position-relative">
                    <div style="position:absolute;top:0;right:0;z-index:2;">
                        <span class="badge bg-danger rounded-start rounded-0 px-3 py-2" style="font-size:0.95em;">Inscrições encerradas</span>
                    </div>
                    <div class="card-body text-center p-4">
                        <div class="icon-box mx-auto"><i class="bi bi-mortarboard"></i></div>
                        <h5 class="fw-bold mb-3">Inscrições Cursos Profissionais</h5>
                        <p class="text-muted">Formulários para os alunos interessados em fazer as suas inscrições nos cursos profissionais.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card feature-card shadow-sm h-100 position-relative">
                    <div style="position:absolute;top:0;right:0;z-index:2;">
                        <span class="badge bg-success rounded-start rounded-0 px-3 py-2" style="font-size:0.95em;">Comprar KIT</span>
                    </div>
                    <a href="https://clubshop.macron.com/amora/agrupamento-de-escolas-jo-o-de-barros" target="_blank" rel="noopener noreferrer" class="stretched-link text-decoration-none text-reset">
                        <div class="card-body text-center p-4">
                            <img src="<?= base_url('adminlte/img/macron.png') ?>" alt="Parceria Macron" class="img-fluid mb-3" style="max-height:70px;">
                            <h5 class="fw-bold mb-3">Parceria Macron</h5>
                            <p class="text-muted">Protocolo de colaboração com a empresa Macron, com o objetivo de apoiar as modalidades do Desporto Escolar.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center mb-4">
            <div class="col-lg-8 mx-auto">
                <h3 class="fw-bold mb-2">Plataformas Eletrónicas</h3>
                <p class="text-muted">Acesso rápido às plataformas Inovar</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <a href="https://aejoaodebarros.inovarmais.com/alunos" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-reset">
                    <div class="card h-100 shadow-sm text-center border-0">
                        <div class="card-body p-4">
                            <img src="<?= base_url('adminlte/img/InovarAlunos.png') ?>" alt="Inovar Alunos" class="img-fluid mb-2" style="max-height:60px;">
                            <h6 class="fw-bold">Inovar Alunos</h6>
                            <small class="text-muted">Utilização exclusiva para professores</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="https://aejoaodebarros.inovarmais.com/paa/" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-reset">
                    <div class="card h-100 shadow-sm text-center border-0">
                        <div class="card-body p-4">
                            <img src="<?= base_url('adminlte/img/InovarPAA.png') ?>" alt="Inovar PAA" class="img-fluid mb-2" style="max-height:60px;">
                            <h6 class="fw-bold">Inovar PAA</h6>
                            <small class="text-muted">Utilização exclusiva para professores</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="https://aejoaodebarros.inovarmais.com/consulta/" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-reset">
                    <div class="card h-100 shadow-sm text-center border-0">
                        <div class="card-body p-4">
                            <img src="<?= base_url('adminlte/img/InovarConsulta.png') ?>" alt="Inovar Consulta" class="img-fluid mb-2" style="max-height:60px;">
                            <h6 class="fw-bold">Inovar Consulta</h6>
                            <small class="text-muted">Alunos e Encarregados de Educação</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-md-6 col-lg-3">
                <a href="https://aejoaodebarros.unicard.pt/inovarsige" target="_blank" rel="noopener noreferrer" class="text-decoration-none text-reset">
                    <div class="card h-100 shadow-sm text-center border-0">
                        <div class="card-body p-4">
                            <img src="<?= base_url('adminlte/img/InovarSige.png') ?>" alt="Inovar SIGE" class="img-fluid mb-2" style="max-height:60px;">
                            <h6 class="fw-bold">Inovar SIGE</h6>
                            <small class="text-muted">Alunos e Encarregados de Educação</small>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
