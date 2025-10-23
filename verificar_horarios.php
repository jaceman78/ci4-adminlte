<?php
// Carregar o framework CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);

require __DIR__ . '/vendor/autoload.php';

// Inicializar o CodeIgniter
$paths = new \Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = require $bootstrap;
$app->initialize();

$db = \Config\Database::connect();

echo "=== VERIFICAÇÃO DE DADOS PARA HORÁRIOS ===\n\n";

// Verificar professores com NIF
echo "1. Professores com NIF (level=5):\n";
$professores = $db->query("SELECT id, name, NIF, level FROM user WHERE level = 5 AND NIF IS NOT NULL AND NIF != '' LIMIT 5")->getResultArray();
echo "Total: " . count($professores) . "\n";
if (count($professores) > 0) {
    foreach ($professores as $prof) {
        echo "  - {$prof['name']} (NIF: {$prof['NIF']})\n";
    }
}
echo "\n";

// Verificar turmas com código
echo "2. Turmas com código:\n";
$turmas = $db->query("SELECT id_turma, codigo, nome, ano FROM turma WHERE codigo IS NOT NULL AND codigo != '' LIMIT 5")->getResultArray();
echo "Total: " . count($turmas) . "\n";
if (count($turmas) > 0) {
    foreach ($turmas as $turma) {
        echo "  - {$turma['codigo']} - {$turma['nome']} ({$turma['ano']}º)\n";
    }
}
echo "\n";

// Verificar disciplinas
echo "3. Disciplinas:\n";
$disciplinas = $db->query("SELECT id_disciplina, abreviatura FROM disciplina LIMIT 5")->getResultArray();
echo "Total: " . count($disciplinas) . "\n";
if (count($disciplinas) > 0) {
    foreach ($disciplinas as $disc) {
        echo "  - {$disc['id_disciplina']} - {$disc['abreviatura']}\n";
    }
}
echo "\n";

// Verificar salas
echo "4. Salas:\n";
$salas = $db->query("SELECT id, codigo_sala, descricao FROM salas LIMIT 5")->getResultArray();
echo "Total: " . count($salas) . "\n";
if (count($salas) > 0) {
    foreach ($salas as $sala) {
        echo "  - {$sala['codigo_sala']}" . ($sala['descricao'] ? " - {$sala['descricao']}" : "") . "\n";
    }
}
echo "\n";

// Verificar estrutura da tabela horario_aulas
echo "5. Estrutura da tabela horario_aulas:\n";
$columns = $db->getFieldData('horario_aulas');
foreach ($columns as $col) {
    echo "  - {$col->name} ({$col->type})\n";
}
