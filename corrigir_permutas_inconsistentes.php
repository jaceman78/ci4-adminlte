<?php

/**
 * Script de Correção - Dados Inconsistentes em Permutas
 * 
 * Corrige permutas com estado NULL baseado em outros campos
 */

$pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');

echo "=== CORREÇÃO DE DADOS INCONSISTENTES ===\n\n";

// 1. Corrigir permutas com substituto_aceitou = 1 mas estado NULL
echo "1. Corrigindo permutas aceites sem estado definido...\n";

$stmt = $pdo->query("
    SELECT id, substituto_aceitou, validado_secretariado
    FROM permutas_vigilancia
    WHERE estado IS NULL OR estado = ''
");

$corrigidas = 0;
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $permuta) {
    $novoEstado = null;
    
    if ($permuta['validado_secretariado'] == 1) {
        $novoEstado = 'VALIDADO_SECRETARIADO';
    } elseif ($permuta['substituto_aceitou'] == 1) {
        $novoEstado = 'ACEITE_SUBSTITUTO';
    } elseif ($permuta['substituto_aceitou'] == 0) {
        $novoEstado = 'RECUSADO_SUBSTITUTO';
    } else {
        $novoEstado = 'PENDENTE';
    }
    
    $update = $pdo->prepare("UPDATE permutas_vigilancia SET estado = ? WHERE id = ?");
    $update->execute([$novoEstado, $permuta['id']]);
    
    echo "   Permuta #{$permuta['id']}: NULL → {$novoEstado}\n";
    $corrigidas++;
}

if ($corrigidas === 0) {
    echo "   ✅ Nenhuma correção necessária.\n";
} else {
    echo "   ✅ {$corrigidas} permuta(s) corrigida(s).\n";
}

echo "\n";

// 2. Verificar se há convocatórias com múltiplas permutas aceites APÓS correção
echo "2. Verificando problemas após correção...\n";

$stmt = $pdo->query("
    SELECT 
        convocatoria_id,
        COUNT(*) as num_aceites,
        GROUP_CONCAT(id ORDER BY id) as ids
    FROM permutas_vigilancia
    WHERE estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
    GROUP BY convocatoria_id
    HAVING COUNT(*) > 1
");

$problemas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($problemas)) {
    echo "   ✅ Nenhuma convocatória com múltiplas permutas aceites.\n";
} else {
    echo "   ⚠️  Encontradas " . count($problemas) . " convocatória(s) problemática(s):\n";
    foreach ($problemas as $p) {
        echo "      Convocatória #{$p['convocatoria_id']}: {$p['num_aceites']} permutas aceites (IDs: {$p['ids']})\n";
        
        // Sugerir ação: manter a mais recente, cancelar as outras
        $ids = explode(',', $p['ids']);
        $maisRecente = max($ids);
        $cancelar = array_filter($ids, fn($id) => $id != $maisRecente);
        
        echo "         Sugestão: Manter permuta #{$maisRecente}, cancelar: " . implode(', ', $cancelar) . "\n";
    }
}

echo "\n=== FIM DA CORREÇÃO ===\n";
