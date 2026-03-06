<?php
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');
$mysqli->set_charset('utf8mb4');

echo "=== ESTRUTURA TABELA CONVOCATORIA ===\n";
$result = $mysqli->query("DESCRIBE convocatoria");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Field']} - {$row['Type']} - " . ($row['Null'] == 'YES' ? 'NULL' : 'NOT NULL') . "\n";
    }
} else {
    echo "Erro: " . $mysqli->error . "\n";
}

$mysqli->close();
