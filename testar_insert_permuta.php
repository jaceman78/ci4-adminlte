<?php
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');
$mysqli->set_charset('utf8mb4');

// Obter ano letivo ativo
$anoAtivo = $mysqli->query('SELECT id_anoletivo FROM ano_letivo WHERE status = 1')->fetch_assoc();
$anoLetivoId = $anoAtivo['id_anoletivo'] ?? null;

echo "Ano Letivo ID Ativo: " . ($anoLetivoId ?? 'NULL') . "\n\n";

// Tentar inserir uma permuta de teste
echo "=== Tentando Inserir Permuta de Teste ===\n";

// Obter uma aula válida
$aulaResult = $mysqli->query('SELECT id_aula FROM horario_aulas LIMIT 1');
if ($aulaResult && $aulaResult->num_rows > 0) {
    $aulaId = $aulaResult->fetch_assoc()['id_aula'];
    echo "Usando aula ID: $aulaId\n\n";
} else {
    die("❌ Nenhuma aula encontrada para teste\n");
}

$stmt = $mysqli->prepare("
    INSERT INTO permutas (
        aula_original_id,
        ano_letivo_id,
        data_aula_original,
        data_aula_permutada,
        estado,
        created_at
    ) VALUES (?, ?, ?, ?, 'pendente', NOW())
");

$dataOriginal = '2025-12-20';
$dataPermutada = '2025-12-21';

$stmt->bind_param('iiss', $aulaId, $anoLetivoId, $dataOriginal, $dataPermutada);

if ($stmt->execute()) {
    $insertId = $mysqli->insert_id;
    echo "✅ Permuta inserida com sucesso! ID: $insertId\n";
    
    // Verificar se o ano_letivo_id foi realmente inserido
    $result = $mysqli->query("SELECT id, ano_letivo_id FROM permutas WHERE id = $insertId");
    $permuta = $result->fetch_assoc();
    
    echo "\nDados inseridos:\n";
    echo json_encode($permuta, JSON_PRETTY_PRINT) . "\n";
    
    if ($permuta['ano_letivo_id'] == $anoLetivoId) {
        echo "\n✅ ano_letivo_id foi corretamente inserido!\n";
    } else {
        echo "\n❌ ano_letivo_id NÃO foi inserido corretamente!\n";
    }
    
    // Apagar permuta de teste
    $mysqli->query("DELETE FROM permutas WHERE id = $insertId");
    echo "\nPermuta de teste removida.\n";
} else {
    echo "❌ Erro ao inserir: " . $stmt->error . "\n";
}

$stmt->close();
$mysqli->close();
