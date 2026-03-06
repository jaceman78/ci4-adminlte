<?php
// Script de teste para atualização da tabela convocatoria
require __DIR__ . '/vendor/autoload.php';

// Carregar configuração do CodeIgniter
$pathsConfig = APPPATH . 'Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;

$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

// Conectar à base de dados
$db = \Config\Database::connect();

echo "=== Teste de Atualização da Tabela Convocatoria ===\n\n";

// Buscar uma convocatória existente
$convocatoria = $db->table('convocatoria')->limit(1)->get()->getRowArray();

if (!$convocatoria) {
    echo "❌ Nenhuma convocatória encontrada na base de dados.\n";
    exit;
}

echo "✓ Convocatória encontrada: ID = {$convocatoria['id']}\n";
echo "  Email enviado atual: {$convocatoria['email_enviado']}\n";
echo "  Data envio atual: " . ($convocatoria['data_envio_email'] ?? 'NULL') . "\n\n";

// Tentar atualizar
echo "Tentando atualizar...\n";
try {
    $result = $db->table('convocatoria')
        ->where('id', $convocatoria['id'])
        ->update([
            'email_enviado' => 1,
            'data_envio_email' => date('Y-m-d H:i:s')
        ]);
    
    echo "✓ Update executado com sucesso!\n";
    echo "  Linhas afetadas: " . $db->affectedRows() . "\n\n";
    
    // Verificar atualização
    $updated = $db->table('convocatoria')->where('id', $convocatoria['id'])->get()->getRowArray();
    echo "Valores após update:\n";
    echo "  Email enviado: {$updated['email_enviado']}\n";
    echo "  Data envio: {$updated['data_envio_email']}\n\n";
    
    // Reverter para estado original (apenas para teste)
    $db->table('convocatoria')
        ->where('id', $convocatoria['id'])
        ->update([
            'email_enviado' => $convocatoria['email_enviado'],
            'data_envio_email' => $convocatoria['data_envio_email']
        ]);
    echo "✓ Valores revertidos para o estado original.\n";
    
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Teste Concluído ===\n";
?>
