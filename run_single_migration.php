<?php
/**
 * Script para executar uma única migration específica
 * Execute via: php run_single_migration.php
 */

require __DIR__ . '/vendor/autoload.php';

// Configurar ambiente CodeIgniter
$pathsConfig = new Config\Paths();
$bootstrap = \CodeIgniter\Config\Services::autoloader();
$bootstrap->initialize(new \Config\Autoload(), new \Config\Modules());
$bootstrap = \CodeIgniter\Config\Services::autoloader();
$bootstrap->register();

$db = \Config\Database::connect();

echo "=== Executar Migration: RemoveDataAquisicaoFromEquipamentos ===\n\n";

try {
    // Verificar se a migration já foi executada
    $exists = $db->table('migrations')
        ->where('class', 'App\\Database\\Migrations\\RemoveDataAquisicaoFromEquipamentos')
        ->countAllResults();
    
    if ($exists > 0) {
        echo "✓ Migration já foi executada anteriormente.\n";
        exit(0);
    }
    
    // Verificar se a coluna existe
    $columnExists = $db->query("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'equipamentos' 
          AND COLUMN_NAME = 'data_aquisicao'
    ")->getNumRows() > 0;
    
    if (!$columnExists) {
        echo "ℹ Coluna 'data_aquisicao' não existe em 'equipamentos'.\n";
        echo "Migration será marcada como executada...\n";
    } else {
        echo "Removendo coluna 'data_aquisicao' da tabela 'equipamentos'...\n";
        $db->query("ALTER TABLE equipamentos DROP COLUMN data_aquisicao");
        echo "✓ Coluna removida com sucesso!\n\n";
    }
    
    // Registrar a migration como executada
    $maxBatch = $db->query("SELECT COALESCE(MAX(batch), 0) as max_batch FROM migrations")->getRow()->max_batch;
    
    $db->table('migrations')->insert([
        'version' => '2025-10-26-203954',
        'class' => 'App\\Database\\Migrations\\RemoveDataAquisicaoFromEquipamentos',
        'group' => 'default',
        'namespace' => 'App',
        'time' => time(),
        'batch' => $maxBatch + 1
    ]);
    
    echo "✓ Migration registada no sistema!\n";
    echo "\n=== Concluído com sucesso ===\n";
    
} catch (\Exception $e) {
    echo "✗ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
