<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Site em Manutenção - AE João de Barros</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .maintenance-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            max-width: 600px;
            text-align: center;
        }
        
        .maintenance-icon {
            font-size: 80px;
            color: #667eea;
            margin-bottom: 30px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }
        
        .maintenance-title {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }
        
        .maintenance-message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .maintenance-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .maintenance-info p {
            margin: 0;
            color: #555;
            font-size: 14px;
        }
        
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <img src="https://www.aejoaodebarros.pt/images/template/logo_topo_cores1.png" alt="AE João de Barros" class="logo">
        
        <div class="maintenance-icon">
            <i class="bi bi-tools"></i>
        </div>
        
        <h1 class="maintenance-title">Site em Manutenção</h1>
        
        <div class="maintenance-message">
            <?= nl2br(esc($config['mensagem'])) ?>
        </div>
        
        <?php if ($config['data_fim_prevista']): ?>
            <div class="maintenance-info">
                <p>
                    <i class="bi bi-clock"></i>
                    <strong>Retorno Previsto:</strong><br>
                    <?= date('d/m/Y', strtotime($config['data_fim_prevista'])) ?> às 
                    <?= date('H:i', strtotime($config['data_fim_prevista'])) ?>
                </p>
            </div>
        <?php endif; ?>
        
        <div class="maintenance-info mt-3">
            <p>
                <i class="bi bi-info-circle"></i>
                Para mais informações, contacte a equipa de apoio.
            </p>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Atualizar a página a cada 5 minutos para verificar se a manutenção foi desativada
        setTimeout(function() {
            location.reload();
        }, 300000); // 5 minutos
    </script>
</body>
</html>
