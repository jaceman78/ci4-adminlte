<?php
// Script temporário para verificar dados das permutas

// Bootstrap CodeIgniter
$pathsPath = realpath(FCPATH . '../app/Config/Paths.php');
require realpath($pathsPath) ?: $pathsPath;

$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "=== Verificando dados da tabela permutas ===\n\n";

// Verificar estrutura da tabela
echo "Estrutura da tabela:\n";
$query = $db->query("DESCRIBE permutas");
$columns = $query->getResultArray();
foreach ($columns as $col) {
    if (strpos($col['Field'], 'data') !== false) {
        echo "- {$col['Field']}: {$col['Type']}\n";
    }
}

echo "\n=== Dados das permutas (primeiras 5) ===\n";
$query = $db->query("SELECT id, aula_original_id, data_aula_original, data_aula_permutada, estado FROM permutas LIMIT 5");
$permutas = $query->getResultArray();

if (empty($permutas)) {
    echo "Nenhuma permuta encontrada.\n";
} else {
    foreach ($permutas as $p) {
        echo "\nPermuta ID: {$p['id']}\n";
        echo "  - Aula Original ID: {$p['aula_original_id']}\n";
        echo "  - Data Aula Original: {$p['data_aula_original']}\n";
        echo "  - Data Aula Permutada: {$p['data_aula_permutada']}\n";
        echo "  - Estado: {$p['estado']}\n";
    }
}

echo "\n=== Testando query completa do getDetalhesPermuta ===\n";
$query = $db->query("
    SELECT p.*, 
           p.data_aula_original, p.data_aula_permutada,
           ha.codigo_turma, ha.disciplina_id
    FROM permutas p
    LEFT JOIN horario_aulas ha ON ha.id_aula = p.aula_original_id
    WHERE p.id = 1
");
$detalhe = $query->getRowArray();

if ($detalhe) {
    echo "Permuta encontrada!\n";
    echo "  - ID: {$detalhe['id']}\n";
    echo "  - data_aula_original: " . (isset($detalhe['data_aula_original']) ? $detalhe['data_aula_original'] : 'NÃO EXISTE') . "\n";
    echo "  - data_aula_permutada: " . (isset($detalhe['data_aula_permutada']) ? $detalhe['data_aula_permutada'] : 'NÃO EXISTE') . "\n";
    echo "  - codigo_turma: " . ($detalhe['codigo_turma'] ?? 'NULL') . "\n";
} else {
    echo "Nenhuma permuta com ID 1 encontrada.\n";
}
