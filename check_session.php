<?php
/**
 * Script para verificar o nível do usuário logado
 */

// Simular ambiente CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

// Carregar o arquivo de sessão mais recente
$sessionPath = __DIR__ . '/writable/session/';
$sessionFiles = glob($sessionPath . 'ci_session*');

if (empty($sessionFiles)) {
    echo "Nenhum arquivo de sessão encontrado em: {$sessionPath}\n";
    exit;
}

// Ordenar por data de modificação (mais recente primeiro)
usort($sessionFiles, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

echo "=== VERIFICAÇÃO DE SESSÃO ===\n\n";
echo "Arquivo de sessão mais recente: " . basename($sessionFiles[0]) . "\n";
echo "Data de modificação: " . date('Y-m-d H:i:s', filemtime($sessionFiles[0])) . "\n\n";

$sessionData = file_get_contents($sessionFiles[0]);

// Desserializar os dados da sessão
if (strpos($sessionData, '__ci_last_regenerate') !== false) {
    // Parse do formato CodeIgniter
    preg_match_all('/(\w+)\|([^|]+)/', $sessionData, $matches, PREG_SET_ORDER);
    
    echo "=== DADOS DA SESSÃO ===\n\n";
    
    $sessionArray = [];
    foreach ($matches as $match) {
        $key = $match[1];
        $value = $match[2];
        
        // Tentar desserializar o valor
        $unserialized = @unserialize($value);
        if ($unserialized !== false) {
            $value = $unserialized;
        }
        
        $sessionArray[$key] = $value;
        
        // Exibir informações importantes
        if (in_array($key, ['user_id', 'level', 'isLoggedIn', 'username', 'user_name'])) {
            echo "{$key}: " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "\n";
        }
    }
    
    echo "\n=== VERIFICAÇÃO DE PERMISSÕES ===\n\n";
    
    if (isset($sessionArray['level'])) {
        $level = $sessionArray['level'];
        echo "Nível do usuário: {$level}\n";
        echo "Nível >= 8? " . ($level >= 8 ? 'SIM ✓' : 'NÃO ✗') . "\n";
        echo "Nível >= 9? " . ($level >= 9 ? 'SIM ✓' : 'NÃO ✗') . "\n";
        
        if ($level >= 8) {
            echo "\n✅ O usuário DEVE ver os botões de editar e eliminar!\n";
        } else {
            echo "\n❌ O usuário NÃO tem permissão para editar/eliminar.\n";
        }
    } else {
        echo "❌ Nível não encontrado na sessão!\n";
    }
    
} else {
    echo "Formato de sessão não reconhecido.\n";
    echo "Conteúdo bruto (primeiros 500 caracteres):\n";
    echo substr($sessionData, 0, 500) . "\n";
}
