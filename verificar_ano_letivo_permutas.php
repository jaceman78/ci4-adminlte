<?php
// Conectar diretamente à base de dados
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');

if ($mysqli->connect_error) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

echo "=== Verificação do Ano Letivo Ativo ===\n\n";

// Verificar ano letivo ativo
$result = $mysqli->query('SELECT * FROM ano_letivo WHERE status = 1');
$anoAtivo = $result->fetch_all(MYSQLI_ASSOC);

if (empty($anoAtivo)) {
    echo "❌ PROBLEMA: Não existe nenhum ano letivo com status = 1 (ativo)\n\n";
    
    // Mostrar todos os anos letivos
    $todosAnos = $mysqli->query('SELECT * FROM ano_letivo ORDER BY anoletivo DESC')->fetch_all(MYSQLI_ASSOC);
    echo "Anos letivos existentes:\n";
    echo json_encode($todosAnos, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "✅ Ano letivo ativo encontrado:\n";
    echo json_encode($anoAtivo, JSON_PRETTY_PRINT) . "\n";
}

// Verificar última permuta criada
echo "\n=== Última Permuta Criada ===\n\n";
$ultimaPermuta = $mysqli->query('SELECT id, aula_original_id, ano_letivo_id, data_aula_original, created_at FROM permutas ORDER BY id DESC LIMIT 1')->fetch_all(MYSQLI_ASSOC);
if (!empty($ultimaPermuta)) {
    echo json_encode($ultimaPermuta, JSON_PRETTY_PRINT) . "\n";
    
    if ($ultimaPermuta[0]['ano_letivo_id'] === null) {
        echo "\n⚠️ A última permuta foi criada SEM ano_letivo_id!\n";
    } else {
        echo "\n✅ A última permuta tem ano_letivo_id: " . $ultimaPermuta[0]['ano_letivo_id'] . "\n";
    }
} else {
    echo "Nenhuma permuta encontrada.\n";
}

$mysqli->close();
