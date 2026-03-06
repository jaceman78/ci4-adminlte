<?php
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');

if ($mysqli->connect_error) {
    die('Erro: ' . $mysqli->connect_error);
}

echo "=== TABELA EXAME ===\n";
$result = $mysqli->query("SELECT id, codigo_prova, nome_prova FROM exame LIMIT 5");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}, Código: {$row['codigo_prova']}, Nome: {$row['nome_prova']}\n";
    }
} else {
    echo "Nenhum exame encontrado.\n";
}

echo "\n=== TABELA SESSAO_EXAME ===\n";
$result = $mysqli->query("SELECT id, exame_id, fase, data_exame FROM sessao_exame LIMIT 5");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}, Exame_ID: {$row['exame_id']}, Fase: {$row['fase']}, Data: {$row['data_exame']}\n";
    }
} else {
    echo "Nenhuma sessão encontrada.\n";
}

echo "\n=== TESTE JOIN ===\n";
$query = "SELECT s.id, s.exame_id, s.fase, e.codigo_prova, e.nome_prova 
          FROM sessao_exame s
          LEFT JOIN exame e ON e.id = s.exame_id
          LIMIT 5";
$result = $mysqli->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Sessão ID: {$row['id']}, Exame_ID: {$row['exame_id']}, ";
        echo "Código: " . ($row['codigo_prova'] ?? 'NULL') . ", ";
        echo "Nome: " . ($row['nome_prova'] ?? 'NULL') . "\n";
    }
} else {
    echo "Nenhum resultado no JOIN.\n";
}

$mysqli->close();
