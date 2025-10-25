<?php
/**
 * Script para EXECUTAR o RENAME de todas as tabelas
 * ATENÇÃO: Este script irá renomear TODAS as tabelas!
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== EXECUÇÃO DE RENAME DE TABELAS ===\n\n";
    echo "⚠️  ATENÇÃO: Esta operação irá renomear TODAS as tabelas!\n";
    echo "Prefixo a adicionar: u520317771_\n\n";
    
    // Obter todas as tabelas
    $query = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sistema_gestao' ORDER BY TABLE_NAME";
    $tables = $pdo->query($query)->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Total de tabelas a renomear: " . count($tables) . "\n\n";
    
    $success = 0;
    $errors = 0;
    
    // Desabilitar verificação de chaves estrangeiras temporariamente
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    echo "✓ Verificação de chaves estrangeiras desabilitada\n\n";
    
    foreach ($tables as $table) {
        $newTableName = 'u520317771_' . $table;
        
        try {
            $sql = "RENAME TABLE `{$table}` TO `{$newTableName}`";
            $pdo->exec($sql);
            echo "✓ {$table} → {$newTableName}\n";
            $success++;
        } catch (Exception $e) {
            echo "✗ ERRO ao renomear {$table}: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
    
    // Reabilitar verificação de chaves estrangeiras
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "\n✓ Verificação de chaves estrangeiras reabilitada\n";
    
    echo "\n=== RESULTADO ===\n";
    echo "✓ Sucesso: {$success} tabelas renomeadas\n";
    if ($errors > 0) {
        echo "✗ Erros: {$errors} tabelas com erro\n";
    }
    
    echo "\n=== PRÓXIMO PASSO ===\n";
    echo "Agora você precisa atualizar o arquivo .env com o prefixo:\n";
    echo "database.default.DBPrefix = u520317771_\n";
    
} catch (Exception $e) {
    echo "ERRO CRÍTICO: " . $e->getMessage() . "\n";
    exit(1);
}
