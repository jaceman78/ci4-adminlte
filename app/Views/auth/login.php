<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ESJB | Login</title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- AdminLTE CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc4/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
      * { box-sizing: border-box; }

      body.login-page {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        font-family: 'Source Sans Pro', sans-serif;
      }

      .login-wrapper {
        display: flex;
        width: 100%;
        max-width: 960px;
        min-height: 580px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0,0,0,0.5);
      }

      /* ── Coluna esquerda — Login ── */
      .login-left {
        flex: 0 0 340px;
        background: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 2.5rem 2rem;
      }

      .login-left .brand img {
        height: 56px;
        margin-bottom: .5rem;
      }
      .login-left .brand h1 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
      }
      .login-left .brand p {
        font-size: .8rem;
        color: #888;
        margin: 0 0 1.5rem;
      }

      .login-left .google-btn-wrap {
        margin-bottom: 1.2rem;
      }
      .login-left .google-btn-wrap .btn,
      .login-left .google-btn-wrap a {
        width: 100%;
      }

      .login-left .login-notice {
        background: #f0f4ff;
        border-radius: 10px;
        padding: .75rem 1rem;
        font-size: .8rem;
        color: #444;
        margin-bottom: 1rem;
      }
      .login-left .login-notice a { color: #0f3460; font-weight: 600; }

      .login-left .footer-links {
        font-size: .72rem;
        color: #aaa;
        text-align: center;
        margin-top: auto;
        padding-top: 1rem;
      }
      .login-left .footer-links a { color: #888; }

      /* ── Coluna direita — Funcionalidades ── */
      .login-right {
        flex: 1;
        background: rgba(255,255,255,0.05);
        backdrop-filter: blur(10px);
        padding: 2rem 1.8rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
      }

      .login-right .features-title {
        color: rgba(255,255,255,0.55);
        font-size: .7rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 1rem;
      }

      .features-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: .75rem;
      }

      .feature-item {
        background: rgba(255,255,255,0.07);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: .85rem .9rem;
        transition: background .2s, transform .2s;
        cursor: default;
      }
      .feature-item:hover {
        background: rgba(255,255,255,0.13);
        transform: translateY(-2px);
      }

      .feature-item .fi-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: #fff;
        margin-bottom: .5rem;
      }

      .feature-item h6 {
        color: #fff;
        font-size: .82rem;
        font-weight: 700;
        margin: 0 0 .15rem;
      }
      .feature-item small {
        color: rgba(255,255,255,0.5);
        font-size: .7rem;
        line-height: 1.3;
        display: block;
      }

      /* Responsive: empilhar em ecrãs pequenos */
      @media (max-width: 700px) {
        .login-wrapper { flex-direction: column; min-height: auto; }
        .login-left { flex: none; }
        .features-grid { grid-template-columns: 1fr 1fr; }
        .login-right { padding: 1.5rem; }
      }
    </style>
</head>
<body class="hold-transition login-page">

<div class="login-wrapper">

  <!-- ── Coluna esquerda: Login ── -->
  <div class="login-left">
    <div class="brand mb-3">
      <h1><b>ESJB</b></h1>
      <p>Escola João de Barros &mdash; Área Restrita</p>
    </div>

    <?php if(session()->getFlashdata('Error')): ?>
      <div class="alert alert-danger py-2 small"><?= session()->getFlashdata('Error') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('Success')): ?>
      <div class="alert alert-success py-2 small"><?= session()->getFlashdata('Success') ?></div>
    <?php endif; ?>

    <div class="google-btn-wrap">
      <?= $googleButton ?>
    </div>

    <?php
      $currentHost = $_SERVER['HTTP_HOST'] ?? '';
      if (strpos($currentHost, 'localhost') !== false || strpos($currentHost, '127.0.0.1') !== false) {
        $publicUrl = site_url('public');
      } else {
        $publicHost = getenv('PUBLIC_HOST') ?: env('PUBLIC_HOST', 'public.escoladigital.cloud');
        $publicUrl = 'https://' . $publicHost;
      }
    ?>
    <div class="login-notice">
      <i class="bi bi-info-circle me-1"></i>
      <strong>Não tem credenciais?</strong> Visite a <a href="<?= $publicUrl ?>">Área Pública</a> para aceder aos serviços sem login.
    </div>

    <div class="footer-links">
      © <?= date('Y') ?> Agrupamento de Escolas João de Barros<br>
      <a href="<?= site_url('privacy') ?>">Privacidade</a> &middot;
      <a href="<?= site_url('privacy/terms') ?>">Termos</a>
    </div>
  </div>

  <!-- ── Coluna direita: Funcionalidades ── -->
  <div class="login-right">
    <p class="features-title">Funcionalidades disponíveis</p>
    <div class="features-grid">

      <div class="feature-item">
        <div class="fi-icon" style="background:linear-gradient(135deg,#667eea,#764ba2);">
          <i class="bi bi-arrow-left-right"></i>
        </div>
        <h6>Permutas de Aulas</h6>
        <small>Gestão de permutas entre professores</small>
      </div>

      <div class="feature-item">
        <div class="fi-icon" style="background:linear-gradient(135deg,#f093fb,#f5576c);">
          <i class="bi bi-headset"></i>
        </div>
        <h6>Sistema de Tickets</h6>
        <small>Suporte técnico e acompanhamento</small>
      </div>

      <div class="feature-item">
        <div class="fi-icon" style="background:linear-gradient(135deg,#4facfe,#00f2fe);">
          <i class="bi bi-lightbulb"></i>
        </div>
        <h6>Caixa de Sugestões</h6>
        <small>Partilhe as suas ideias</small>
      </div>

      <div class="feature-item">
        <div class="fi-icon" style="background:linear-gradient(135deg,#43e97b,#38f9d7);">
          <i class="bi bi-calendar-check"></i>
        </div>
        <h6>Marcação de Férias</h6>
        <small>Gestão e marcação de períodos de férias</small>
      </div>

      <div class="feature-item">
        <div class="fi-icon" style="background:linear-gradient(135deg,#fa709a,#fee140);">
          <i class="bi bi-clipboard2-check"></i>
        </div>
        <h6>Convocatórias Exames</h6>
        <small>Gestão de convocatórias para vigilância</small>
      </div>

      <div class="feature-item">
        <div class="fi-icon" style="background:linear-gradient(135deg,#a18cd1,#fbc2eb);">
          <i class="bi bi-map"></i>
        </div>
        <h6>Visitas de Estudo</h6>
        <small>Organização e acompanhamento de visitas</small>
      </div>

    </div>
  </div>

</div>
</div>

<!-- Dependências JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc4/dist/js/adminlte.min.js"></script>

</body>
</html>
