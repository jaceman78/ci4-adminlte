<?php
/**
 * Script para corrigir dados órfãos antes da migration
 * Execute via: php fix_horario_orphans.php
 */

require __DIR__ . '/vendor/autoload.php';

// Configurar ambiente CodeIgniter
$pathsConfig = new Config\Paths();
$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "=== Verificação de Dados Órfãos ===\n\n";

// 1. Verificar órfãos em horario_aulas.codigo_turma
echo "1. Verificando registos órfãos em horario_aulas...\n";
$orphans = $db->query("
    SELECT 
        ha.id_aula,
        ha.codigo_turma
    FROM horario_aulas ha
    LEFT JOIN turma t ON ha.codigo_turma = t.codigo
    WHERE ha.codigo_turma IS NOT NULL 
      AND t.codigo IS NULL
")->getResultArray();

if (empty($orphans)) {
    echo "   ✓ Nenhum registo órfão encontrado\n\n";
} else {
    echo "   ✗ Encontrados " . count($orphans) . " registos órfãos:\n";
    foreach ($orphans as $orphan) {
        echo "     - id_aula: {$orphan['id_aula']}, codigo_turma: {$orphan['codigo_turma']}\n";
    }
    echo "\n";
}

// 2. Verificar charset/collation
echo "2. Verificando charset e collation...\n";
$charsets = $db->query("
    SELECT 
        TABLE_NAME,
        COLUMN_NAME,
        CHARACTER_SET_NAME,
        COLLATION_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME IN ('horario_aulas', 'turma')
      AND COLUMN_NAME IN ('codigo_turma', 'codigo')
")->getResultArray();

foreach ($charsets as $col) {
    echo "   - {$col['TABLE_NAME']}.{$col['COLUMN_NAME']}: {$col['CHARACTER_SET_NAME']} / {$col['COLLATION_NAME']}\n";
}
echo "\n";

// 3. Verificar duplicados em turma.codigo
echo "3. Verificando duplicados em turma.codigo...\n";
$duplicates = $db->query("
    SELECT 
        codigo,
        COUNT(*) as total
    FROM turma
    GROUP BY codigo
    HAVING total > 1
")->getResultArray();

if (empty($duplicates)) {
    echo "   ✓ Nenhum duplicado encontrado\n\n";
} else {
    echo "   ✗ Encontrados códigos duplicados:\n";
    foreach ($duplicates as $dup) {
        echo "     - codigo: {$dup['codigo']}, total: {$dup['total']}\n";
    }
    echo "\n";
}

// 4. Perguntar se deseja corrigir
if (!empty($orphans)) {
    echo "Deseja corrigir os registos órfãos? (s/n): ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    fclose($handle);
    
    if (trim(strtolower($line)) === 's') {
        echo "Definindo codigo_turma como NULL para registos órfãos...\n";
        $db->query("
            UPDATE horario_aulas ha
            LEFT JOIN turma t ON ha.codigo_turma = t.codigo
            SET ha.codigo_turma = NULL
            WHERE ha.codigo_turma IS NOT NULL AND t.codigo IS NULL
        ");
        echo "✓ Correção concluída! " . $db->affectedRows() . " registos atualizados.\n\n";
    } else {
        echo "Correção cancelada.\n\n";
    }
}

echo "=== Verificação Concluída ===\n";
echo "Agora pode executar: php spark migrate\n";
