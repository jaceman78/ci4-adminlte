<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste de Upload de Imagens</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .test-section {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .info {
            background-color: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        img {
            max-width: 200px;
            margin: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
        }
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔧 Teste de Correção: Erro 403 nas Imagens</h1>

    <div class="test-section info">
        <h2>ℹ️ Informações</h2>
        <p><strong>Problema:</strong> Erro 403 ao carregar imagens de perfil</p>
        <p><strong>Causa:</strong> MaintenanceFilter + .htaccess bloqueando uploads</p>
        <p><strong>Data Correção:</strong> 19/03/2026</p>
    </div>

    <div class="test-section">
        <h2>✅ Verificações Realizadas</h2>
        <ul>
            <li>MaintenanceFilter atualizado com exceções para 'writable'</li>
            <li>Criado .htaccess em writable/uploads/profiles/</li>
            <li>Criado .htaccess em writable/uploads/qrcodes/</li>
        </ul>
    </div>

    <div class="test-section">
        <h2>🧪 Teste de Imagens</h2>
        <p>Se as imagens abaixo carregarem corretamente, o problema está resolvido:</p>
        
        <h3>Teste Local</h3>
        <p>URL Base: <code><?= base_url() ?></code></p>
        
        <?php
        $uploadsDir = WRITEPATH . 'uploads/profiles/';
        $images = [];
        
        if (is_dir($uploadsDir)) {
            $files = scandir($uploadsDir);
            foreach ($files as $file) {
                if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif'])) {
                    $images[] = $file;
                    if (count($images) >= 5) break; // Máximo 5 imagens
                }
            }
        }
        ?>
        
        <?php if (empty($images)): ?>
            <div class="error">
                <p>⚠️ Nenhuma imagem encontrada em writable/uploads/profiles/</p>
                <p>Faça upload de uma foto de perfil primeiro.</p>
            </div>
        <?php else: ?>
            <div class="success">
                <p>✅ Encontradas <?= count($images) ?> imagem(ns) de teste</p>
            </div>
            
            <?php foreach ($images as $image): ?>
                <div style="display: inline-block; margin: 10px; text-align: center;">
                    <img src="<?= base_url('writable/uploads/profiles/' . $image) ?>" 
                         alt="Teste"
                         onerror="this.parentElement.innerHTML='<div class=error>❌ Erro 403/404</div>'">
                    <br>
                    <small><?= $image ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="test-section">
        <h2>📋 Próximos Passos</h2>
        <ol>
            <li><strong>Local (XAMPP):</strong> Este teste deve mostrar as imagens</li>
            <li><strong>Deploy para Produção:</strong>
                <ul>
                    <li>Fazer push das alterações para o repositório</li>
                    <li>Pull no servidor: <code>git pull origin main</code></li>
                    <li>Verificar permissões: <code>chmod 755 writable/uploads/profiles</code></li>
                    <li>Testar em https://escoladigital.cloud/perfil</li>
                </ul>
            </li>
        </ol>
    </div>

    <div class="test-section info">
        <h2>🔍 Debug: Estrutura de Diretórios</h2>
        <pre><?php
        echo "writable/\n";
        echo "├── .htaccess (Require all denied)\n";
        echo "└── uploads/\n";
        echo "    ├── profiles/\n";
        echo "    │   ├── .htaccess (Require all granted para imagens) ✅\n";
        echo "    │   └── *.png, *.jpg\n";
        echo "    └── qrcodes/\n";
        echo "        └── .htaccess (Require all granted para imagens) ✅\n";
        ?></pre>
    </div>

    <div class="test-section">
        <h2>📄 Arquivos Modificados</h2>
        <ul>
            <li><code>app/Filters/MaintenanceFilter.php</code></li>
            <li><code>writable/uploads/profiles/.htaccess</code> (novo)</li>
            <li><code>writable/uploads/qrcodes/.htaccess</code> (novo)</li>
        </ul>
    </div>
</body>
</html>
