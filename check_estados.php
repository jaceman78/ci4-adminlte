<?php
require 'vendor/autoload.php';

$db = \Config\Database::connect();
$result = $db->query('SELECT estado, COUNT(*) as total FROM requisicao_kit GROUP BY estado ORDER BY estado')->getResultArray();

echo "Distribuição de estados:\n";
echo str_repeat("=", 50) . "\n";
foreach ($result as $row) {
    echo sprintf("%-20s: %d\n", $row['estado'], $row['total']);
}
echo str_repeat("=", 50) . "\n";
echo "Total: " . array_sum(array_column($result, 'total')) . "\n";
