<?php
// Verificar estrutura da tabela users
$host = 'localhost';
$db   = 'sistema_gestao';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ESTRUTURA DA TABELA 'user' ===\n\n";
    
    $stmt = $pdo->query("DESCRIBE user");
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($fields as $field) {
        echo "Campo: " . $field['Field'] . "\n";
        echo "  Tipo: " . $field['Type'] . "\n";
        echo "  Null: " . $field['Null'] . "\n";
        echo "  Default: " . ($field['Default'] ?? 'NULL') . "\n";
        echo "  Extra: " . $field['Extra'] . "\n";
        echo "\n";
    }
    
    // Verificar se profile_img existe
    $hasProfileImg = false;
    foreach ($fields as $field) {
        if ($field['Field'] === 'profile_img') {
            $hasProfileImg = true;
            break;
        }
    }
    
    if ($hasProfileImg) {
        echo "✓ Campo 'profile_img' existe na tabela!\n";
    } else {
        echo "✗ Campo 'profile_img' NÃO existe na tabela!\n";
        echo "\nSQL para adicionar o campo:\n";
        echo "ALTER TABLE `user` ADD COLUMN `profile_img` VARCHAR(255) NULL DEFAULT NULL AFTER `oauth_id`;\n";
    }
    
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
