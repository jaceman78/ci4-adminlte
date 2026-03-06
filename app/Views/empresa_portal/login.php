<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Empresa - Reparações Externas</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }
        
        .login-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-floating {
            margin-bottom: 20px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
            transition: transform 0.2s;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .icon-container {
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .icon-container i {
            font-size: 40px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="icon-container">
                    <i class="bi bi-tools"></i>
                </div>
                <h1>Portal Empresa</h1>
                <p>Sistema de Gestão de Reparações Externas</p>
            </div>
            
            <div class="login-body">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div><?= session()->getFlashdata('error') ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <div><?= session()->getFlashdata('success') ?></div>
                    </div>
                <?php endif; ?>
                
                <form action="<?= base_url('empresa/processar-login') ?>" method="POST" id="loginForm">
                    <?= csrf_field() ?>
                    
                    <div class="form-floating mb-3">
                        <input type="text" 
                               class="form-control" 
                               id="chave_acesso" 
                               name="chave_acesso" 
                               placeholder="Digite a chave de acesso" 
                               required 
                               autofocus>
                        <label for="chave_acesso">
                            <i class="bi bi-key me-2"></i>Chave de Acesso
                        </label>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-login">
                            <i class="bi bi-box-arrow-in-right me-2"></i>
                            Entrar no Sistema
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Não tem uma chave de acesso? Contacte o administrador do sistema.
                    </small>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <small class="text-white">
                <i class="bi bi-shield-lock me-1"></i>
                Acesso seguro e monitorizado
            </small>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validação básica do formulário
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const chave = document.getElementById('chave_acesso').value.trim();
            
            if (chave.length < 20) {
                e.preventDefault();
                alert('A chave de acesso deve ter pelo menos 20 caracteres');
                return false;
            }
        });
    </script>
</body>
</html>
