<?php
require 'vendor/autoload.php';

$db = \Config\Database::connect();
$result = $db->table('sessao_exame')
    ->select('sessao_exame.*, exame.codigo_prova, exame.nome_prova, exame.tipo_prova')
    ->join('exame', 'exame.id = sessao_exame.exame_id', 'left')
    ->limit(1)
    ->get()
    ->getResultArray();

echo "Query Result:\n";
print_r($result);

if (!empty($result)) {
    echo "\n\nFirst record keys:\n";
    print_r(array_keys($result[0]));
}
