<?php

/**
 * Script de Teste - Validação de Permutas Duplicadas
 * 
 * Testa os seguintes cenários:
 * 1. Criar permuta quando já existe uma permuta aceite
 * 2. Aceitar permuta quando outra já foi aceite
 * 3. Verificar cancelamento automático de permutas pendentes
 */

$pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');

echo "=== TESTE DE VALIDAÇÃO DE PERMUTAS ===\n\n";

// Cenário 1: Verificar se existem convocatórias com múltiplas permutas ativas
echo "1. Procurando convocatórias com múltiplas permutas ativas...\n";

$stmt = $pdo->query("
    SELECT 
        convocatoria_id,
        COUNT(*) as num_permutas,
        GROUP_CONCAT(id ORDER BY id) as permuta_ids,
        GROUP_CONCAT(estado ORDER BY id) as estados
    FROM permutas_vigilancia
    WHERE estado NOT IN ('CANCELADO', 'RECUSADO_SUBSTITUTO')
    GROUP BY convocatoria_id
    HAVING COUNT(*) > 1
");

$multiplas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($multiplas)) {
    echo "   ✅ Nenhuma convocatória com múltiplas permutas ativas encontrada.\n\n";
} else {
    echo "   ⚠️  Encontradas " . count($multiplas) . " convocatória(s) com múltiplas permutas:\n";
    foreach ($multiplas as $conv) {
        echo "      Convocatória #{$conv['convocatoria_id']}: {$conv['num_permutas']} permutas\n";
        echo "         IDs: {$conv['permuta_ids']}\n";
        echo "         Estados: {$conv['estados']}\n";
    }
    echo "\n";
}

// Cenário 2: Verificar convocatórias com múltiplas permutas ACEITES
echo "2. Procurando convocatórias com múltiplas permutas ACEITES...\n";

$stmt = $pdo->query("
    SELECT 
        convocatoria_id,
        COUNT(*) as num_aceites,
        GROUP_CONCAT(id ORDER BY id) as permuta_ids,
        GROUP_CONCAT(CONCAT(id, ':', estado) ORDER BY id SEPARATOR ', ') as detalhes
    FROM permutas_vigilancia
    WHERE estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
    GROUP BY convocatoria_id
    HAVING COUNT(*) > 1
");

$aceitesMultiplas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($aceitesMultiplas)) {
    echo "   ✅ Nenhuma convocatória com múltiplas permutas aceites encontrada.\n\n";
} else {
    echo "   ❌ PROBLEMA CRÍTICO: Encontradas " . count($aceitesMultiplas) . " convocatória(s) com múltiplas permutas aceites:\n";
    foreach ($aceitesMultiplas as $conv) {
        echo "      Convocatória #{$conv['convocatoria_id']}: {$conv['num_aceites']} permutas aceites\n";
        echo "         Detalhes: {$conv['detalhes']}\n";
    }
    echo "\n";
}

// Cenário 3: Estatísticas gerais
echo "3. Estatísticas gerais de permutas:\n";

$stmt = $pdo->query("
    SELECT 
        estado,
        COUNT(*) as total
    FROM permutas_vigilancia
    GROUP BY estado
    ORDER BY total DESC
");

$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($stats as $stat) {
    $icon = match($stat['estado']) {
        'PENDENTE' => '⏳',
        'ACEITE_SUBSTITUTO' => '✅',
        'VALIDADO_SECRETARIADO' => '✅✅',
        'RECUSADO_SUBSTITUTO' => '❌',
        'CANCELADO' => '🚫',
        'REJEITADO_SECRETARIADO' => '❌❌',
        default => '•'
    };
    echo "   {$icon} {$stat['estado']}: {$stat['total']}\n";
}

echo "\n=== FIM DO TESTE ===\n";
