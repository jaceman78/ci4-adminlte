<?php
/**
 * Script para verificar as tabelas após RENAME
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== VERIFICAÇÃO DAS TABELAS RENOMEADAS ===\n\n";
    
    // Obter todas as tabelas
    $query = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sistema_gestao' ORDER BY TABLE_NAME";
    $tables = $pdo->query($query)->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Total de tabelas na base de dados: " . count($tables) . "\n\n";
    
    $withPrefix = 0;
    $withoutPrefix = 0;
    
    echo "=== TABELAS ENCONTRADAS ===\n\n";
    foreach ($tables as $table) {
        if (strpos($table, 'u520317771_') === 0) {
            echo "✓ {$table}\n";
            $withPrefix++;
        } else {
            echo "⚠️  {$table} (SEM PREFIXO)\n";
            $withoutPrefix++;
        }
    }
    
    echo "\n=== RESUMO ===\n";
    echo "✓ Tabelas com prefixo: {$withPrefix}\n";
    if ($withoutPrefix > 0) {
        echo "⚠️  Tabelas sem prefixo: {$withoutPrefix}\n";
    }
    
    if ($withoutPrefix === 0 && $withPrefix > 0) {
        echo "\n✅ SUCESSO! Todas as tabelas têm o prefixo u520317771_\n";
    }
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
