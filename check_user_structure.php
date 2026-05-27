<?php
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');
$mysqli->set_charset('utf8mb4');

echo "=== ESTRUTURA TABELA user ===\n\n";
$result = $mysqli->query("DESCRIBE user");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Field']} - {$row['Type']} - " . 
             ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
}

echo "\n=== EXEMPLO DE DADOS ===\n\n";
$sample = $mysqli->query("SELECT * FROM user LIMIT 1");
if ($sample && $sample->num_rows > 0) {
    $row = $sample->fetch_assoc();
    foreach ($row as $key => $value) {
        echo "$key: " . substr($value, 0, 50) . "\n";
    }
}

$mysqli->close();
