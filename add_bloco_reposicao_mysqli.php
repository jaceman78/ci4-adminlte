<?php

// Configurações do banco
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'sistema_gestao';

echo "=== Adicionar campo bloco_reposicao_id à tabela permutas ===\n\n";

try {
    $mysqli = new mysqli($host, $user, $pass, $dbname);
    
    if ($mysqli->connect_error) {
        die("Erro de conexão: " . $mysqli->connect_error);
    }
    
    // Verificar se a coluna já existe
    $result = $mysqli->query("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = '$dbname' 
          AND TABLE_NAME = 'permutas' 
          AND COLUMN_NAME = 'bloco_reposicao_id'
    ");
    
    if ($result->num_rows > 0) {
        echo "✓ Coluna 'bloco_reposicao_id' já existe!\n";
    } else {
        echo "Adicionando coluna 'bloco_reposicao_id'...\n";
        
        $sql = "ALTER TABLE permutas 
                ADD COLUMN bloco_reposicao_id INT(11) UNSIGNED NULL 
                COMMENT 'ID do bloco horário de reposição (quando aplicável)' 
                AFTER sala_permutada_id";
        
        if ($mysqli->query($sql)) {
            echo "✓ Coluna adicionada com sucesso!\n\n";
            
            // Adicionar foreign key
            echo "Adicionando foreign key...\n";
            $sql = "ALTER TABLE permutas 
                    ADD CONSTRAINT fk_permutas_bloco_reposicao 
                    FOREIGN KEY (bloco_reposicao_id) 
                    REFERENCES blocos_horarios(id_bloco) 
                    ON DELETE SET NULL 
                    ON UPDATE CASCADE";
            
            if ($mysqli->query($sql)) {
                echo "✓ Foreign key adicionada com sucesso!\n";
            } else {
                echo "⚠ Aviso ao adicionar FK: " . $mysqli->error . "\n";
            }
        } else {
            throw new Exception("Erro ao adicionar coluna: " . $mysqli->error);
        }
    }
    
    $mysqli->close();
    echo "\n✓ Processo concluído!\n";
    
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
    exit(1);
}
