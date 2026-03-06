<?php
// Teste do método getAnoAtivo() do AnoLetivoModel
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

require 'vendor/autoload.php';

// Bootstrap CodeIgniter
$pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
$paths = require realpath($pathsPath);
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

// Criar uma instância do modelo
$anoLetivoModel = new \App\Models\AnoLetivoModel();

echo "=== Teste do AnoLetivoModel::getAnoAtivo() ===\n\n";

$anoAtivo = $anoLetivoModel->getAnoAtivo();

if ($anoAtivo) {
    echo "✅ Ano Ativo Encontrado:\n";
    echo json_encode($anoAtivo, JSON_PRETTY_PRINT) . "\n\n";
    
    $anoLetivoId = $anoAtivo['id_anoletivo'] ?? null;
    echo "ID extraído: " . ($anoLetivoId ?? 'NULL') . "\n";
} else {
    echo "❌ Nenhum ano letivo ativo encontrado!\n";
}

// Testar query direta
echo "\n=== Query Direta ===\n";
$db = \Config\Database::connect();
$result = $db->query('SELECT * FROM ano_letivo WHERE status = 1')->getResultArray();
echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
