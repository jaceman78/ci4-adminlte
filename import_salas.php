<?php

/**
 * Script para importar salas do CSV
 * Uso: php import_salas.php
 */

// Carregar o framework CodeIgniter
require __DIR__ . '/vendor/autoload.php';

// Bootstrap do CodeIgniter
$pathsConfig = require __DIR__ . '/app/Config/Paths.php';
$paths = new $pathsConfig();
require $paths->systemDirectory . '/bootstrap.php';

// Criar aplicação
$app = \Config\Services::codeigniter();
$app->initialize();

// Obter o database
$db = \Config\Database::connect();

// Caminho do arquivo CSV
$csvFile = __DIR__ . '/salas.csv';

if (!file_exists($csvFile)) {
    die("❌ Arquivo CSV não encontrado: {$csvFile}\n");
}

echo "📄 Lendo arquivo CSV...\n";

// Abrir arquivo CSV
$handle = fopen($csvFile, 'r');
if (!$handle) {
    die("❌ Erro ao abrir arquivo CSV\n");
}

// Ler cabeçalho
$header = fgetcsv($handle, 1000, ';');
echo "📋 Cabeçalho: " . implode(', ', $header) . "\n\n";

// Contadores
$total = 0;
$success = 0;
$errors = 0;
$skipped = 0;

// Processar cada linha
while (($data = fgetcsv($handle, 1000, ';')) !== false) {
    $total++;
    
    // Mapear dados
    $escolaId = trim($data[0]);
    $codigoSala = trim($data[1]);
    $descricao = !empty($data[2]) ? trim($data[2]) : null;
    
    // Validar escola_id
    if (empty($escolaId) || !is_numeric($escolaId)) {
        echo "⚠️  Linha {$total}: Escola ID inválido: {$escolaId}\n";
        $errors++;
        continue;
    }
    
    // Validar codigo_sala
    if (empty($codigoSala)) {
        echo "⚠️  Linha {$total}: Código de sala vazio\n";
        $errors++;
        continue;
    }
    
    // Verificar se escola existe
    $escola = $db->table('escolas')->where('id', $escolaId)->get()->getRow();
    if (!$escola) {
        echo "⚠️  Linha {$total}: Escola ID {$escolaId} não existe na base de dados\n";
        $errors++;
        continue;
    }
    
    // Verificar se sala já existe (mesmo codigo_sala + escola_id)
    $salaExiste = $db->table('salas')
        ->where('escola_id', $escolaId)
        ->where('codigo_sala', $codigoSala)
        ->get()
        ->getRow();
    
    if ($salaExiste) {
        echo "⏭️  Linha {$total}: Sala '{$codigoSala}' já existe na escola {$escolaId} - Pulando\n";
        $skipped++;
        continue;
    }
    
    // Inserir sala
    try {
        $db->table('salas')->insert([
            'escola_id' => $escolaId,
            'codigo_sala' => $codigoSala,
            'descricao' => $descricao,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        echo "✅ Linha {$total}: Sala '{$codigoSala}' criada com sucesso (Escola: {$escola->nome})\n";
        $success++;
        
    } catch (\Exception $e) {
        echo "❌ Linha {$total}: Erro ao criar sala '{$codigoSala}': {$e->getMessage()}\n";
        $errors++;
    }
}

fclose($handle);

// Resumo
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 RESUMO DA IMPORTAÇÃO\n";
echo str_repeat("=", 60) . "\n";
echo "Total de linhas processadas: {$total}\n";
echo "✅ Importadas com sucesso: {$success}\n";
echo "⏭️  Já existentes (puladas): {$skipped}\n";
echo "❌ Erros: {$errors}\n";
echo str_repeat("=", 60) . "\n";

if ($success > 0) {
    echo "\n🎉 Importação concluída com sucesso!\n";
} else {
    echo "\n⚠️  Nenhuma sala foi importada.\n";
}
