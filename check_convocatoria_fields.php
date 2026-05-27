<?php
require 'vendor/autoload.php';

$db = \Config\Database::connect();
$fields = $db->getFieldNames('convocatoria');

echo "Campos da tabela convocatoria:\n";
foreach ($fields as $field) {
    echo "- $field\n";
}

// Verificar dados de uma convocatoria para ver os campos
$query = $db->table('convocatoria')->limit(1)->get();
$result = $query->getRowArray();

if ($result) {
    echo "\nExemplo de registro:\n";
    foreach ($result as $key => $value) {
        echo "$key => " . (is_null($value) ? 'NULL' : $value) . "\n";
    }
}
