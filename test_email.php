<?php
// Script de teste de envio de email
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "=== Teste de Envio de Email ===\n\n";

$mail = new PHPMailer(true);

try {
    // Configurações do servidor (hardcoded para teste)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'antonioneto@aejoaodebarros.pt';
    $mail->Password   = getenv('SMTP_PASSWORD');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';
    
    // Debug
    $mail->SMTPDebug = 2; // Mostrar debug detalhado

    // Remetente
    $mail->setFrom('antonioneto@aejoaodebarros.pt', 'António Neto - Escola Digital JB');
    
    // Destinatários
    $mail->addAddress('escoladigitaljb@aejoaodebarros.pt');
    
    // Conteúdo
    $mail->isHTML(true);
    $mail->Subject = 'Teste de Email - Sistema de Gestão';
    $mail->Body    = '<h1>Teste de Email</h1><p>Este é um email de teste do sistema de gestão escolar.</p>';
    
    echo "\nEnviando email de teste...\n";
    $mail->send();
    echo "\n✅ Email enviado com sucesso!\n";
    
} catch (Exception $e) {
    echo "\n❌ Erro ao enviar email!\n";
    echo "Erro: {$mail->ErrorInfo}\n";
}
