<?php
// Carregar configuração de banco de dados
require 'app/Config/Database.php';

$config = new \Config\Database();
$dbConfig = $config->default;

// Conectar ao MySQL
$mysqli = new mysqli(
    $dbConfig['hostname'],
    $dbConfig['username'],
    $dbConfig['password'],
    $dbConfig['database']
);

if ($mysqli->connect_error) {
    die('Erro de conexão: ' . $mysqli->connect_error);
}

echo "Conexão estabelecida!\n\n";

// Query de teste
$query = "SELECT sessao_exame.*, exame.codigo_prova, exame.nome_prova, exame.tipo_prova 
          FROM sessao_exame 
          LEFT JOIN exame ON exame.id = sessao_exame.exame_id 
          LIMIT 5";

$result = $mysqli->query($query);

if ($result) {
    echo "Total de registros: " . $result->num_rows . "\n\n";
    
    if ($result->num_rows > 0) {
        $first = true;
        while ($row = $result->fetch_assoc()) {
            if ($first) {
                echo "Colunas disponíveis:\n";
                print_r(array_keys($row));
                echo "\n\nPrimeiro registro:\n";
                $first = false;
            }
            echo "ID: {$row['id']}, Exame ID: {$row['exame_id']}, ";
            echo "Código: " . ($row['codigo_prova'] ?? 'NULL') . ", ";
            echo "Nome: " . ($row['nome_prova'] ?? 'NULL') . "\n";
        }
    } else {
        echo "Nenhum registro encontrado.\n";
    }
} else {
    echo "Erro na query: " . $mysqli->error . "\n";
}

$mysqli->close();
