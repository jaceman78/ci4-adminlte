<?php
$mysqli = new mysqli("localhost", "root", "", "sistema_gestao");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== Verificando índices na coluna NIF da tabela user ===\n";
$result = $mysqli->query("SHOW INDEX FROM user WHERE Column_name = 'NIF'");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "❌ A coluna NIF NÃO tem índice! Isso impede a criação de foreign keys.\n";
}

echo "\n=== Estrutura completa da coluna NIF ===\n";
$result = $mysqli->query("DESCRIBE user");
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] == 'NIF') {
        print_r($row);
    }
}

$mysqli->close();

