<?php

// Configurações do banco
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'sistema_gestao';

echo "=== Últimas Permutas Criadas ===\n\n";

try {
    $mysqli = new mysqli($host, $user, $pass, $dbname);
    
    if ($mysqli->connect_error) {
        die("Erro de conexão: " . $mysqli->connect_error);
    }
    
    $result = $mysqli->query("
        SELECT 
            id, 
            aula_original_id, 
            data_aula_permutada, 
            bloco_reposicao_id, 
            sala_permutada_id, 
            estado,
            observacoes,
            created_at
        FROM permutas 
        ORDER BY id DESC 
        LIMIT 5
    ");
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "ID: {$row['id']}\n";
            echo "  Aula Original: {$row['aula_original_id']}\n";
            echo "  Data Reposição: {$row['data_aula_permutada']}\n";
            echo "  Bloco Reposição: " . ($row['bloco_reposicao_id'] ?? 'NULL') . "\n";
            echo "  Sala: " . ($row['sala_permutada_id'] ?? 'NULL') . "\n";
            echo "  Estado: {$row['estado']}\n";
            echo "  Obs: {$row['observacoes']}\n";
            echo "  Criado: {$row['created_at']}\n";
            echo "---\n";
        }
    } else {
        echo "Nenhuma permuta encontrada.\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "✗ ERRO: " . $e->getMessage() . "\n";
}
