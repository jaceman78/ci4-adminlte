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
</div>

<!-- Dependências JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc4/dist/js/adminlte.min.js"></script>

</body>
</html>
