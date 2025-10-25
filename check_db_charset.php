<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');
    
    // Verificar charset e collation da base de dados
    $result = $pdo->query('SELECT @@character_set_database as charset, @@collation_database as collation')->fetch(PDO::FETCH_ASSOC);
    
    echo "\n=== INFORMAÇÃO DA BASE DE DADOS ===\n\n";
    echo "Conjunto de Caracteres (Charset): " . $result['charset'] . "\n";
    echo "Compilação (Collation): " . $result['collation'] . "\n";
    
    // Verificar também as configurações do servidor
    $server = $pdo->query('SELECT @@character_set_server as server_charset, @@collation_server as server_collation')->fetch(PDO::FETCH_ASSOC);
    
    echo "\n=== CONFIGURAÇÕES DO SERVIDOR MySQL ===\n\n";
    echo "Charset do Servidor: " . $server['server_charset'] . "\n";
    echo "Collation do Servidor: " . $server['server_collation'] . "\n";
    
    // Listar algumas tabelas e suas collations
    echo "\n=== COLLATION DAS TABELAS ===\n\n";
    $tables = $pdo->query("SELECT TABLE_NAME, TABLE_COLLATION FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'sistema_gestao' ORDER BY TABLE_NAME LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($tables as $table) {
        echo $table['TABLE_NAME'] . ": " . $table['TABLE_COLLATION'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
