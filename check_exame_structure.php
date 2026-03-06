<?php
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');

echo "=== ESTRUTURA TABELA EXAME ===\n";
$result = $mysqli->query("DESCRIBE exame");
while ($row = $result->fetch_assoc()) {
    echo "{$row['Field']} - {$row['Type']}\n";
}

echo "\n=== TODOS OS CAMPOS DO EXAME ===\n";
$result = $mysqli->query("SELECT * FROM exame LIMIT 3");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
        echo "\n";
    }
}

$mysqli->close();
