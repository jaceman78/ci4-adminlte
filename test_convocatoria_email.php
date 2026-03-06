<?php
// Script de teste para verificar o envio de email de convocatória

require __DIR__ . '/vendor/autoload.php';

// Simular ambiente CodeIgniter
$_SERVER['CI_ENVIRONMENT'] = 'development';
define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', ROOTPATH . 'vendor/codeigniter4/framework/system/');

// Carregar .env
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== TESTE DE CONFIGURAÇÃO DE EMAIL ===\n\n";

echo "Configurações do .env:\n";
echo "email.fromEmail: " . getenv('email.fromEmail') . "\n";
echo "email.fromName: " . getenv('email.fromName') . "\n";
echo "email.SMTPUser: " . getenv('email.SMTPUser') . "\n";
echo "email.SMTPHost: " . getenv('email.SMTPHost') . "\n";
echo "email.SMTPPort: " . getenv('email.SMTPPort') . "\n";
echo "email.protocol: " . getenv('email.protocol') . "\n";
echo "\n";

// Inicializar CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();
$context = is_cli() ? 'php-cli' : 'web';
$app->setContext($context);

// Obter instância de Email
$email = \Config\Services::email();

echo "=== TESTE DE ENVIO DE EMAIL ===\n\n";

try {
    // Configurar remetente explicitamente
    $fromEmail = getenv('email.fromEmail') ?: 'escoladigitaljb@aejoaodebarros.pt';
    $fromName = getenv('email.fromName') ?: 'Escola Digital - AE João de Barros';
    
    echo "Configurando remetente:\n";
    echo "De: $fromEmail ($fromName)\n\n";
    
    $email->setFrom($fromEmail, $fromName);
    $email->setTo('antonioneto@aejoaodebarros.pt'); // Email de teste
    $email->setSubject('TESTE - Convocatória - Verificação de Remetente');
    
    $message = '<html><body>';
    $message .= '<h2>TESTE DE CONFIGURAÇÃO DE EMAIL</h2>';
    $message .= '<p>Este é um email de teste para verificar se o remetente está configurado corretamente.</p>';
    $message .= '<p><strong>Remetente esperado:</strong> escoladigitaljb@aejoaodebarros.pt</p>';
    $message .= '<p><strong>Data/Hora:</strong> ' . date('Y-m-d H:i:s') . '</p>';
    $message .= '</body></html>';
    
    $email->setMessage($message);
    
    if ($email->send()) {
        echo "✅ Email enviado com sucesso!\n\n";
        echo "Verifique a caixa de entrada e confirme o remetente.\n";
    } else {
        echo "❌ Falha ao enviar email!\n\n";
        echo "Detalhes do erro:\n";
        echo $email->printDebugger(['headers', 'subject', 'body']);
    }
} catch (\Exception $e) {
    echo "❌ Exceção: " . $e->getMessage() . "\n";
}
