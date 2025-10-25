<?php
/**
 * Script para fazer ROLLBACK - Remover prefixo u520317771_ das tabelas
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== ROLLBACK - REMOVENDO PREFIXO DAS TABELAS ===\n\n";
    echo "⚠️  Esta operação irá REMOVER o prefixo u520317771_ de todas as tabelas\n\n";
    
    // Obter todas as tabelas com o prefixo
    $query = "SELECT TABLE_NAME FROM information_schema.TABLES 
              WHERE TABLE_SCHEMA = 'sistema_gestao' 
              AND TABLE_NAME LIKE 'u520317771_%'
              ORDER BY TABLE_NAME";
    $tables = $pdo->query($query)->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Total de tabelas a renomear: " . count($tables) . "\n\n";
    
    $success = 0;
    $errors = 0;
    
    // Desabilitar verificação de chaves estrangeiras temporariamente
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
    echo "✓ Verificação de chaves estrangeiras desabilitada\n\n";
    
    foreach ($tables as $table) {
        // Remover o prefixo u520317771_
        $newTableName = str_replace('u520317771_', '', $table);
        
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
    
    echo "\n✅ ROLLBACK CONCLUÍDO!\n";
    
} catch (Exception $e) {
    echo "ERRO CRÍTICO: " . $e->getMessage() . "\n";
    exit(1);
}
