<?php
/**
 * Script para gerar comandos SQL de RENAME de todas as tabelas
 * Adiciona o prefixo: u520317771_
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== GERADOR DE COMANDOS RENAME PARA TABELAS ===\n\n";
    echo "Prefixo a adicionar: u520317771_\n";
    echo "Base de Dados: sistema_gestao\n\n";
    
    // Obter todas as tabelas
    $query = "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sistema_gestao' ORDER BY TABLE_NAME";
    $tables = $pdo->query($query)->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Total de tabelas encontradas: " . count($tables) . "\n\n";
    echo "=== COMANDOS SQL PARA RENOMEAR TABELAS ===\n\n";
    
    $sqlCommands = [];
    
    foreach ($tables as $table) {
        $newTableName = 'u520317771_' . $table;
        $sqlCommand = "RENAME TABLE `{$table}` TO `{$newTableName}`;";
        $sqlCommands[] = $sqlCommand;
        echo $sqlCommand . "\n";
    }
    
    echo "\n=== SALVANDO EM ARQUIVO SQL ===\n\n";
    
    // Criar arquivo SQL
    $sqlContent = "-- Script de RENAME de tabelas\n";
    $sqlContent .= "-- Adiciona o prefixo: u520317771_\n";
    $sqlContent .= "-- Base de Dados: sistema_gestao\n";
    $sqlContent .= "-- Data: " . date('Y-m-d H:i:s') . "\n\n";
    $sqlContent .= "USE sistema_gestao;\n\n";
    $sqlContent .= implode("\n", $sqlCommands);
    
    file_put_contents('RENAME_TABLES_ADD_PREFIX.sql', $sqlContent);
    
    echo "✓ Arquivo SQL criado: RENAME_TABLES_ADD_PREFIX.sql\n";
    echo "✓ Total de comandos: " . count($sqlCommands) . "\n\n";
    
    echo "=== LISTA DE TABELAS (ANTES → DEPOIS) ===\n\n";
    foreach ($tables as $table) {
        echo "{$table} → u520317771_{$table}\n";
    }
    
    echo "\n=== EXECUTAR COMANDOS? ===\n";
    echo "Para executar, rode: php rename_tables_execute.php\n";
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
