<?php

require 'vendor/autoload.php';

// Bootstrap CodeIgniter
$pathsConfig = new Config\Paths();
$bootstrap = rtrim(realpath(__DIR__ . '/app'), '\\/') . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'Boot' . DIRECTORY_SEPARATOR . 'development.php';
if (file_exists($bootstrap)) {
    require $bootstrap;
}
$app = Config\Services::codeigniter();
$app->initialize();

// Get database connection
$db = \Config\Database::connect();

echo "=== Valores DISTINTOS de dia_semana em blocos_horarios ===\n\n";
$result = $db->query('SELECT DISTINCT dia_semana FROM blocos_horarios ORDER BY dia_semana')->getResultArray();

foreach ($result as $row) {
    echo "- " . $row['dia_semana'] . "\n";
}

echo "\n=== Total de blocos por dia ===\n\n";
$result = $db->query('SELECT dia_semana, COUNT(*) as total FROM blocos_horarios GROUP BY dia_semana ORDER BY dia_semana')->getResultArray();

foreach ($result as $row) {
    echo $row['dia_semana'] . ": " . $row['total'] . " blocos\n";
}

echo "\n=== Exemplo de registos ===\n\n";
$result = $db->query('SELECT id_bloco, dia_semana, designacao, hora_inicio, hora_fim FROM blocos_horarios LIMIT 5')->getResultArray();

foreach ($result as $row) {
    echo "ID: {$row['id_bloco']} | Dia: {$row['dia_semana']} | {$row['designacao']} | {$row['hora_inicio']}-{$row['hora_fim']}\n";
}
