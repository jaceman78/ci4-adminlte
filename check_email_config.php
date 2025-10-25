#!/usr/bin/env php
<?php
/**
 * Script de verificação de configurações de email
 */

echo "=== VERIFICAÇÃO DE CONFIGURAÇÕES DE EMAIL ===\n\n";

// Carregar o .env
if (file_exists(__DIR__ . '/.env')) {
    $envContent = file_get_contents(__DIR__ . '/.env');
    
    // Parse manualmente o .env
    $lines = explode("\n", $envContent);
    $envVars = [];
    
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $envVars[$key] = $value;
        }
    }
    
    echo "📧 CONFIGURAÇÕES DE EMAIL NO .env:\n\n";
    echo "✓ email.fromEmail: " . ($envVars['email.fromEmail'] ?? 'NÃO CONFIGURADO') . "\n";
    echo "✓ email.fromName: " . ($envVars['email.fromName'] ?? 'NÃO CONFIGURADO') . "\n";
    echo "✓ email.SMTPUser: " . ($envVars['email.SMTPUser'] ?? 'NÃO CONFIGURADO') . "\n";
    echo "✓ email.SMTPPass: " . (isset($envVars['email.SMTPPass']) && !empty($envVars['email.SMTPPass']) ? '***CONFIGURADO***' : 'NÃO CONFIGURADO') . "\n";
    echo "✓ email.SMTPHost: " . ($envVars['email.SMTPHost'] ?? 'NÃO CONFIGURADO') . "\n";
    echo "✓ email.SMTPPort: " . ($envVars['email.SMTPPort'] ?? 'NÃO CONFIGURADO') . "\n";
    
    // Verificar se fromEmail e SMTPUser são iguais
    echo "\n📋 VERIFICAÇÕES:\n\n";
    
    if (isset($envVars['email.fromEmail']) && isset($envVars['email.SMTPUser'])) {
        if ($envVars['email.fromEmail'] === $envVars['email.SMTPUser']) {
            echo "✅ fromEmail e SMTPUser são IGUAIS (correto)\n";
        } else {
            echo "⚠️  fromEmail ({$envVars['email.fromEmail']}) DIFERENTE de SMTPUser ({$envVars['email.SMTPUser']})\n";
        }
    }
    
    // Verificar se é antonioneto@aejoaodebarros.pt
    if (isset($envVars['email.fromEmail'])) {
        if (strpos($envVars['email.fromEmail'], 'antonioneto@aejoaodebarros.pt') !== false) {
            echo "✅ Email configurado para antonioneto@aejoaodebarros.pt\n";
        } else {
            echo "⚠️  Email NÃO está configurado para antonioneto@aejoaodebarros.pt\n";
            echo "    Atual: {$envVars['email.fromEmail']}\n";
        }
    }
    
} else {
    echo "❌ Arquivo .env não encontrado!\n";
}

echo "\n=== VERIFICANDO CONTROLLERS ===\n\n";

// Verificar TicketsController.php
$ticketsController = file_get_contents(__DIR__ . '/app/Controllers/TicketsController.php');
if (strpos($ticketsController, 'antonioneto@aejoaodebarros.pt') !== false) {
    echo "✅ TicketsController.php: Configurado para antonioneto@aejoaodebarros.pt\n";
} else if (strpos($ticketsController, "getenv('email.fromEmail')") !== false) {
    echo "✅ TicketsController.php: Usando getenv('email.fromEmail') - OK\n";
} else {
    echo "⚠️  TicketsController.php: Verificar configuração\n";
}

// Verificar Email.php
$emailConfig = file_get_contents(__DIR__ . '/app/Config/Email.php');
if (strpos($emailConfig, 'antonioneto@aejoaodebarros.pt') !== false) {
    echo "✅ Config/Email.php: Configurado para antonioneto@aejoaodebarros.pt como fallback\n";
} else {
    echo "⚠️  Config/Email.php: Verificar configuração padrão\n";
}

echo "\n✅ VERIFICAÇÃO CONCLUÍDA!\n";
