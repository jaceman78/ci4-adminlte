<?php
/**
 * Script para limpar caches do PHP e CodeIgniter
 */

// Limpar OPcache do PHP
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache limpo com sucesso!\n";
} else {
    echo "⚠️ OPcache não está ativo ou não disponível.\n";
}

// Limpar cache de realpath
if (function_exists('clearstatcache')) {
    clearstatcache(true);
    echo "✅ Stat cache limpo com sucesso!\n";
}

// Limpar cache de scripts do CodeIgniter
$cacheDir = __DIR__ . '/writable/cache';
if (is_dir($cacheDir)) {
    $files = glob($cacheDir . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ Cache do CodeIgniter limpo com sucesso!\n";
}

echo "\n✅ Todos os caches foram limpos! Recarregue a página.\n";
