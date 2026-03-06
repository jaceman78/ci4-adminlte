<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .error-card {
            max-width: 600px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .header-error {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        .header-error i {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .card-body {
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="header-error">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <h1 class="mb-0"><?= esc($title) ?></h1>
        </div>
        <div class="card-body">
            <div class="alert alert-danger">
                <i class="bi bi-x-circle me-2"></i>
                <?= esc($message) ?>
            </div>

            <div class="alert alert-info">
                <strong><i class="bi bi-info-circle me-2"></i>O que fazer?</strong>
                <ul class="mb-0 mt-2">
                    <li>Verifique se o link está correto</li>
                    <li>Se o link expirou, contacte a secretaria</li>
                    <li>Pode solicitar um novo email de convocatória</li>
                </ul>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted mb-3">Em caso de dúvidas, contacte a secretaria da escola.</p>
                <?php if (session()->has('user_id')): ?>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-primary">
                    <i class="bi bi-house-door"></i> Ir para o Dashboard
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
