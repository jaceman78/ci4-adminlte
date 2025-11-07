<?php

require __DIR__ . '/vendor/autoload.php';

$db = \Config\Database::connect();

echo "=== Adicionar campo bloco_reposicao_id à tabela permutas ===\n\n";

try {
    // Verificar se a coluna já existe
    $columnExists = $db->query("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
          AND TABLE_NAME = 'permutas' 
          AND COLUMN_NAME = 'bloco_reposicao_id'
    ")->getNumRows() > 0;
    
    if ($columnExists) {
        echo "✓ Coluna 'bloco_reposicao_id' já existe!\n";
    } else {
        echo "Adicionando coluna 'bloco_reposicao_id'...\n";
        
        $db->query("
            ALTER TABLE permutas 
            ADD COLUMN bloco_reposicao_id INT(11) UNSIGNED NULL 
            COMMENT 'ID do bloco horário de reposição (quando aplicável)' 
            AFTER sala_permutada_id
        ");
        
        echo "✓ Coluna adicionada com sucesso!\n\n";
        
        // Adicionar foreign key
        echo "Adicionando foreign key...\n";
        $db->query("
            ALTER TABLE permutas 
            ADD CONSTRAINT fk_permutas_bloco_reposicao 
            FOREIGN KEY (bloco_reposicao_id) 
            REFERENCES blocos_horarios(id_bloco) 
            ON DELETE SET NULL 
            ON UPDATE CASCADE
        ");
        
        echo "✓ Foreign key adicionada com sucesso!\n";
    }
    
    echo "\n✓ Processo concluído!\n";
    
} catch (\Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
