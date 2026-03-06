<?php
$pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');
$stmt = $pdo->query('DESCRIBE permutas_vigilancia');
echo "Estrutura da tabela permutas_vigilancia:\n";
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  " . $row['Field'] . " - " . $row['Type'] . "\n";
}
