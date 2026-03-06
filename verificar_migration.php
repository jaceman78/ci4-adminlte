<?php
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');
$mysqli->set_charset('utf8mb4');

echo "=== Últimas Migrations ===\n";
$result = $mysqli->query('SELECT version, class, time FROM migrations ORDER BY id DESC LIMIT 5');
while($row = $result->fetch_assoc()) {
    echo $row['version'] . ' - ' . $row['class'] . ' - ' . date('Y-m-d H:i:s', $row['time']) . PHP_EOL;
}

echo "\n=== Estrutura da Tabela Permutas ===\n";
$result = $mysqli->query('DESCRIBE permutas');
while($row = $result->fetch_assoc()) {
    if ($row['Field'] === 'ano_letivo_id') {
        echo "✅ Campo ano_letivo_id existe: " . json_encode($row) . "\n";
    }
}

$mysqli->close();
