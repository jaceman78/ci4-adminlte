<?php
/**
 * Script de teste de email para Kit Digital
 * Acesso: http://localhost:8080/test_email_kit.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Carregar .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configurações de email do .env
$config = [
    'protocol' => $_ENV['email.protocol'] ?? 'smtp',
    'SMTPHost' => $_ENV['email.SMTPHost'] ?? 'smtp.gmail.com',
    'SMTPUser' => $_ENV['email.SMTPUser'] ?? '',
    'SMTPPass' => $_ENV['email.SMTPPass'] ?? '',
    'SMTPPort' => (int)($_ENV['email.SMTPPort'] ?? 587),
    'SMTPCrypto' => $_ENV['email.SMTPCrypto'] ?? 'tls',
    'fromEmail' => $_ENV['email.fromEmail'] ?? 'noreply@aejoaodebarros.pt',
    'fromName' => $_ENV['email.fromName'] ?? 'AE João de Barros',
];

echo "<h1>Teste de Configuração de Email - Kit Digital</h1>";
echo "<h2>Configurações Atuais:</h2>";
echo "<pre>";
echo "Protocol: " . $config['protocol'] . "\n";
echo "SMTP Host: " . $config['SMTPHost'] . "\n";
echo "SMTP User: " . ($config['SMTPUser'] ? '****' . substr($config['SMTPUser'], -10) : 'NÃO CONFIGURADO') . "\n";
echo "SMTP Pass: " . ($config['SMTPPass'] ? '****' : 'NÃO CONFIGURADA') . "\n";
echo "SMTP Port: " . $config['SMTPPort'] . "\n";
echo "SMTP Crypto: " . $config['SMTPCrypto'] . "\n";
echo "From Email: " . $config['fromEmail'] . "\n";
echo "From Name: " . $config['fromName'] . "\n";
echo "</pre>";

if (empty($config['SMTPUser']) || empty($config['SMTPPass'])) {
    echo "<p style='color:red;'><strong>ERRO:</strong> Credenciais SMTP não configuradas no .env!</p>";
    echo "<p>Adicione ao ficheiro .env:</p>";
    echo "<pre>
email.protocol = smtp
email.SMTPHost = smtp.gmail.com
email.SMTPUser = seu_email@gmail.com
email.SMTPPass = sua_senha_app
email.SMTPPort = 587
email.SMTPCrypto = tls
email.SMTPAuth = true
email.fromEmail = noreply@aejoaodebarros.pt
email.fromName = AE João de Barros - Kit Digital
    </pre>";
    exit;
}

// Tentar enviar email de teste
if (isset($_GET['send'])) {
    $emailDestino = $_GET['email'] ?? '';
    
    if (empty($emailDestino) || !filter_var($emailDestino, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>Email de destino inválido!</p>";
    } else {
        echo "<h2>Enviando email de teste para: {$emailDestino}</h2>";
        
        // Inicializar CodeIgniter Email
        $email = \Config\Services::email();
        
        $message = "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <h2>Teste de Email - Kit Digital</h2>
            <p>Este é um email de teste do sistema de Kit Digital.</p>
            <p>Se recebeu este email, significa que as configurações estão corretas!</p>
            <p>Data/Hora: " . date('Y-m-d H:i:s') . "</p>
            <p><strong>Escola Digital<br>Agrupamento de Escolas João de Barros</strong></p>
        </body>
        </html>
        ";
        
        $email->setFrom($config['fromEmail'], $config['fromName']);
        $email->setTo($emailDestino);
        $email->setSubject('Teste de Email - Kit Digital');
        $email->setMessage($message);
        
        if ($email->send()) {
            echo "<p style='color:green;'><strong>✅ Email enviado com sucesso!</strong></p>";
        } else {
            echo "<p style='color:red;'><strong>❌ Erro ao enviar email:</strong></p>";
            echo "<pre>" . $email->printDebugger(['headers']) . "</pre>";
        }
    }
}

?>

<hr>
<h3>Enviar Email de Teste</h3>
<form method="get">
    <label>Email de destino:</label><br>
    <input type="email" name="email" required placeholder="seu_email@exemplo.com" style="width:300px; padding:5px;">
    <input type="hidden" name="send" value="1">
    <button type="submit" style="padding:5px 15px;">Enviar Teste</button>
</form>

<hr>
<p><small>Script de teste: test_email_kit.php</small></p>
