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
      .service-card {
        border: none;
        border-radius: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
      }
      .service-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      }
      .service-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
      }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <b>Login</b>ESJB
  </div>
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Entre com a sua conta Google</p>

      <!-- Mensagens de flash -->
      <?php if(session()->getFlashdata('Error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('Error') ?></div>
      <?php endif; ?>
      <?php if(session()->getFlashdata('Success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('Success') ?></div>
      <?php endif; ?>

      <!-- Botão Google -->
      <?= $googleButton ?>
      
      <!-- Aviso Área Pública -->
      <div class="alert alert-info mt-3 mb-3">
        <div class="d-flex align-items-center">
          <i class="bi bi-info-circle me-2" style="font-size: 1.2rem;"></i>
          <div class="flex-grow-1">
            <strong>Não tem credenciais?</strong>
            <p class="mb-0 small">Esta é a área restrita para funcionários. Para aceder aos serviços públicos, visite:</p>
          </div>
        </div>
        <div class="text-center mt-2">
          <?php 
            $publicHost = getenv('PUBLIC_HOST') ?: env('PUBLIC_HOST', 'public.escoladigital.cloud');
            $publicUrl = 'https://' . $publicHost . '/';
          ?>
          <a href="<?= $publicUrl ?>" class="btn btn-sm btn-outline-primary" target="_blank">
            <i class="bi bi-globe"></i> Aceder à Área Pública
          </a>
        </div>
      </div>
      
      <!-- Links de Privacidade e Termos -->
      <div class="text-center mt-4">
        <small class="text-muted">
          Ao fazer login, concorda com a nossa<br>
          <a href="<?= site_url('privacy') ?>" target="_blank" class="text-primary">Política de Privacidade</a> e 
          <a href="<?= site_url('privacy/terms') ?>" target="_blank" class="text-primary">Termos de Serviço</a>
        </small>
      </div>
    </div>
    <!-- /.card-body -->
    
    <div class="card-footer text-center">
      <small class="text-muted">
        © <?= date('Y') ?> Agrupamento de Escolas João de Barros
      </small>
    </div>
  </div>
  <!-- /.card -->

    <!-- Service Cards -->
    <div class="mt-4">
      <div class="row g-3">
        <div class="col-12">
          <div class="card service-card shadow-sm">
            <div class="card-body d-flex align-items-center p-3">
              <div class="service-icon me-3">
                <i class="bi bi-arrow-left-right"></i>
              </div>
              <div>
                <h6 class="mb-1 fw-bold">Permutas de Aulas</h6>
                <small class="text-muted">Gestão de permutas entre professores</small>
              </div>
            </div>
          </div>
        </div>
      
        <div class="col-12">
          <div class="card service-card shadow-sm">
            <div class="card-body d-flex align-items-center p-3">
              <div class="service-icon me-3">
                <i class="bi bi-headset"></i>
              </div>
              <div>
                <h6 class="mb-1 fw-bold">Sistema de Tickets</h6>
                <small class="text-muted">Suporte técnico e acompanhamento</small>
              </div>
            </div>
          </div>
        </div>
      
        <div class="col-12">
          <div class="card service-card shadow-sm">
            <div class="card-body d-flex align-items-center p-3">
              <div class="service-icon me-3">
                <i class="bi bi-lightbulb"></i>
              </div>
              <div>
                <h6 class="mb-1 fw-bold">Caixa de Sugestões</h6>
                <small class="text-muted">Partilhe as suas ideias</small>
              </div>
            </div>
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
