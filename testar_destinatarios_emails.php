<?php

/**
 * Script de Teste - Destinatários de Emails de Permutas
 * 
 * Verifica quais utilizadores receberiam emails em diferentes cenários
 */

$pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');

echo "=== TESTE DE DESTINATÁRIOS DE EMAILS ===\n\n";

// 1. Listar utilizadores por nível
echo "1. UTILIZADORES POR NÍVEL\n";
echo str_repeat("-", 60) . "\n";

$stmt = $pdo->query("
    SELECT level, COUNT(*) as total, GROUP_CONCAT(name ORDER BY name SEPARATOR ', ') as names
    FROM user
    WHERE level >= 1
    GROUP BY level
    ORDER BY level
");

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $label = match((int)$row['level']) {
        1, 2, 3 => "Professores",
        4 => "Secretariado de Exames",
        5 => "Técnicos",
        8 => "Administrador",
        9 => "Super Administrador",
        default => "Outros"
    };
    
    echo sprintf("Nível %d (%s): %d utilizador(es)\n", $row['level'], $label, $row['total']);
    if ($row['total'] <= 5) {
        echo "   → " . $row['names'] . "\n";
    }
    echo "\n";
}

// 2. Secretariado de Exames (devem receber emails de validação)
echo "\n2. SECRETARIADO DE EXAMES (Níveis 4, 8, 9)\n";
echo str_repeat("-", 60) . "\n";
echo "Estes utilizadores receberão emails de pedidos de validação:\n\n";

$stmt = $pdo->query("
    SELECT id, name, email, level
    FROM user
    WHERE level IN (4, 8, 9)
    ORDER BY level, name
");

$secretariado = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($secretariado)) {
    echo "⚠️  AVISO: Nenhum membro do secretariado encontrado!\n";
} else {
    foreach ($secretariado as $user) {
        $cargo = match((int)$user['level']) {
            4 => "Secretariado de Exames",
            8 => "Administrador",
            9 => "Super Admin",
            default => "Outro"
        };
        echo sprintf("✅ %s (%s) - %s\n", $user['name'], $cargo, $user['email']);
    }
}

// 3. Técnicos (NÃO devem receber emails de validação)
echo "\n\n3. TÉCNICOS (Nível 5)\n";
echo str_repeat("-", 60) . "\n";
echo "Estes utilizadores NÃO receberão emails de validação:\n\n";

$stmt = $pdo->query("
    SELECT id, name, email
    FROM user
    WHERE level = 5
    ORDER BY name
");

$tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($tecnicos)) {
    echo "Nenhum técnico encontrado.\n";
} else {
    foreach ($tecnicos as $user) {
        echo sprintf("❌ %s - %s\n", $user['name'], $user['email']);
    }
}

// 4. Comparação: Antes vs Depois
echo "\n\n4. COMPARAÇÃO DE DESTINATÁRIOS\n";
echo str_repeat("-", 60) . "\n";

$stmt = $pdo->query("SELECT COUNT(*) as total FROM user WHERE level >= 4 AND level < 9");
$antesCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM user WHERE level IN (4, 8, 9)");
$depoisCount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

echo "ANTES da correção (level >= 4 AND level < 9):\n";
echo "   📧 {$antesCount} destinatário(s) - Incluía níveis 4, 5, 6, 7, 8\n\n";

echo "DEPOIS da correção (level IN (4, 8, 9)):\n";
echo "   📧 {$depoisCount} destinatário(s) - Apenas níveis 4, 8, 9\n\n";

if ($antesCount > $depoisCount) {
    $economia = $antesCount - $depoisCount;
    echo "✅ Redução de {$economia} email(s) desnecessário(s) por permuta!\n";
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "CONCLUSÃO:\n";
echo "• Técnicos (nível 5) não receberão mais emails do secretariado\n";
echo "• Apenas membros do Secretariado de Exames receberão notificações\n";
echo "• Substitutos continuam recebendo pedido de permuta individual\n";
echo str_repeat("=", 60) . "\n";
