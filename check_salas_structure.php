<?php
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');
$mysqli->set_charset('utf8mb4');

echo "=== ESTRUTURA TABELA SALAS ===\n";
$result = $mysqli->query("DESCRIBE salas");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "{$row['Field']} - {$row['Type']}\n";
    }
} else {
    echo "Erro: " . $mysqli->error . "\n";
}

$mysqli->close();
