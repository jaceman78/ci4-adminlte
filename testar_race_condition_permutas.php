<?php
/**
 * TESTE: Simular aceitação simultânea de duas permutas
 * 
 * Este script testa se é possível aceitar duas permutas para o mesmo horário
 * devido a race condition nas validações.
 */

$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');
$mysqli->set_charset('utf8mb4');

if ($mysqli->connect_error) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

echo "=== TESTE DE RACE CONDITION EM PERMUTAS ===\n\n";

// Preparar dados de teste
echo "1. Verificando estrutura da tabela...\n";

// Verificar se existem convocatórias para testar
$convs = $mysqli->query("SELECT COUNT(*) as total FROM convocatoria");
$totalConvs = $convs->fetch_assoc()['total'];

if ($totalConvs < 2) {
    echo "\n❌ ERRO: Precisa de pelo menos 2 convocatórias para testar.\n";
    echo "   Total encontradas: $totalConvs\n";
    exit(1);
}

echo "   ✅ Encontradas $totalConvs convocatórias\n";

// Obter duas convocatórias diferentes para o mesmo dia/hora (se existirem)
$query = "
    SELECT 
        c1.id as conv1_id,
        c1.user_id as user1_id,
        c2.id as conv2_id,
        c2.user_id as user2_id,
        se.data_exame,
        se.hora_exame,
        e.codigo_prova
    FROM convocatoria c1
    JOIN sessao_exame se ON se.id = c1.sessao_exame_id
    JOIN exame e ON e.id = se.exame_id
    JOIN convocatoria c2 ON c2.sessao_exame_id = c1.sessao_exame_id
        AND c2.id != c1.id
        AND c2.user_id != c1.user_id
    WHERE c1.id < c2.id
    LIMIT 1
";

$result = $mysqli->query($query);

if (!$result || $result->num_rows == 0) {
    echo "\n⚠️  Não há convocatórias DIFERENTES para o MESMO horário.\n";
    echo "   Teste de conflito não pode ser executado.\n\n";
    
    // Mostrar convocatórias disponíveis
    echo "=== CONVOCATÓRIAS DISPONÍVEIS ===\n";
    $allConvs = $mysqli->query("
        SELECT 
            c.id,
            c.user_id,
            u.name,
            se.data_exame,
            se.hora_exame,
            e.codigo_prova
        FROM convocatoria c
        JOIN user u ON u.id = c.user_id
        JOIN sessao_exame se ON se.id = c.sessao_exame_id
        JOIN exame e ON e.id = se.exame_id
        ORDER BY se.data_exame, se.hora_exame
    ");
    
    if ($allConvs) {
        while ($row = $allConvs->fetch_assoc()) {
            echo "  Conv #{$row['id']}: {$row['name']} → {$row['codigo_prova']} ({$row['data_exame']} {$row['hora_exame']})\n";
        }
    }
    
    $mysqli->close();
    exit(0);
}

$testData = $result->fetch_assoc();

echo "\n2. Cenário de teste identificado:\n";
echo "   Convocatória 1: ID {$testData['conv1_id']} (Professor ID: {$testData['user1_id']})\n";
echo "   Convocatória 2: ID {$testData['conv2_id']} (Professor ID: {$testData['user2_id']})\n";
echo "   Mesmo horário: {$testData['data_exame']} às {$testData['hora_exame']}\n";
echo "   Prova: {$testData['codigo_prova']}\n\n";

// Escolher um professor substituto diferente
$substitutoQuery = $mysqli->query("
    SELECT id, name 
    FROM user 
    WHERE id NOT IN ({$testData['user1_id']}, {$testData['user2_id']})
    AND level IN (5, 6, 7, 8, 9)
    LIMIT 1
");

if (!$substitutoQuery || $substitutoQuery->num_rows == 0) {
    echo "❌ ERRO: Não há professor substituto disponível.\n";
    $mysqli->close();
    exit(1);
}

$substituto = $substitutoQuery->fetch_assoc();
echo "3. Professor Substituto: {$substituto['name']} (ID: {$substituto['id']})\n\n";

// Agora vamos simular a race condition
echo "=== SIMULAÇÃO DE RACE CONDITION ===\n\n";

echo "Cenário:\n";
echo "  - Professor A (ID {$testData['user1_id']}) pede permuta ao Substituto\n";
echo "  - Professor B (ID {$testData['user2_id']}) pede permuta ao Substituto\n";
echo "  - Substituto aceita AMBAS ao mesmo tempo\n\n";

// Criar as duas permutas
$mysqli->begin_transaction();

try {
    // Permuta 1
    $mysqli->query("
        INSERT INTO permutas_vigilancia 
        (convocatoria_id, user_original_id, user_substituto_id, estado, motivo, criado_em)
        VALUES 
        ({$testData['conv1_id']}, {$testData['user1_id']}, {$substituto['id']}, 'PENDENTE', 'Teste de race condition - Permuta 1', NOW())
    ");
    $permuta1_id = $mysqli->insert_id;
    
    // Permuta 2
    $mysqli->query("
        INSERT INTO permutas_vigilancia 
        (convocatoria_id, user_original_id, user_substituto_id, estado, motivo, criado_em)
        VALUES 
        ({$testData['conv2_id']}, {$testData['user2_id']}, {$substituto['id']}, 'PENDENTE', 'Teste de race condition - Permuta 2', NOW())
    ");
    $permuta2_id = $mysqli->insert_id;
    
    $mysqli->commit();
    
    echo "✅ Duas permutas criadas:\n";
    echo "   Permuta #$permuta1_id (Convocatória {$testData['conv1_id']})\n";
    echo "   Permuta #$permuta2_id (Convocatória {$testData['conv2_id']})\n\n";
    
} catch (Exception $e) {
    $mysqli->rollback();
    echo "❌ Erro ao criar permutas: " . $e->getMessage() . "\n";
    $mysqli->close();
    exit(1);
}

// Agora tentar aceitar as duas (simulando requisições simultâneas)
echo "=== TESTE: Aceitar ambas as permutas ===\n\n";

// ACEITAÇÃO 1
echo "1. Aceitando Permuta #$permuta1_id...\n";

// Simular a validação do controller
$dataExame = $testData['data_exame'];
$horaExame = $testData['hora_exame'];

$conflitoAntes1 = $mysqli->query("
    SELECT pv.id
    FROM permutas_vigilancia pv
    JOIN convocatoria c ON c.id = pv.convocatoria_id
    JOIN sessao_exame se ON se.id = c.sessao_exame_id
    WHERE pv.user_substituto_id = {$substituto['id']}
    AND pv.id != $permuta1_id
    AND pv.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
    AND se.data_exame = '$dataExame'
    AND se.hora_exame = '$horaExame'
");

if ($conflitoAntes1->num_rows > 0) {
    echo "   ❌ VALIDAÇÃO BLOQUEOU: Conflito de horário detectado\n";
} else {
    echo "   ✅ VALIDAÇÃO PASSOU: Nenhum conflito detectado\n";
    $mysqli->query("
        UPDATE permutas_vigilancia 
        SET estado = 'ACEITE_SUBSTITUTO',
            substituto_aceitou = 1,
            data_resposta_substituto = NOW()
        WHERE id = $permuta1_id
    ");
    echo "   ✅ Permuta #$permuta1_id ACEITE\n\n";
}

// ACEITAÇÃO 2 (simulando requisição simultânea)
echo "2. Aceitando Permuta #$permuta2_id...\n";

$conflitoAntes2 = $mysqli->query("
    SELECT pv.id
    FROM permutas_vigilancia pv
    JOIN convocatoria c ON c.id = pv.convocatoria_id
    JOIN sessao_exame se ON se.id = c.sessao_exame_id
    WHERE pv.user_substituto_id = {$substituto['id']}
    AND pv.id != $permuta2_id
    AND pv.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
    AND se.data_exame = '$dataExame'
    AND se.hora_exame = '$horaExame'
");

if ($conflitoAntes2->num_rows > 0) {
    echo "   ❌ VALIDAÇÃO BLOQUEOU: Conflito de horário detectado\n";
    echo "   ✅ SISTEMA FUNCIONOU CORRETAMENTE!\n\n";
} else {
    echo "   ⚠️  VALIDAÇÃO PASSOU: Nenhum conflito detectado\n";
    $mysqli->query("
        UPDATE permutas_vigilancia 
        SET estado = 'ACEITE_SUBSTITUTO',
            substituto_aceitou = 1,
            data_resposta_substituto = NOW()
        WHERE id = $permuta2_id
    ");
    echo "   ❌ Permuta #$permuta2_id ACEITE\n";
    echo "   🚨 BUG CONFIRMADO: Duas permutas aceites para o mesmo horário!\n\n";
}

// Verificar resultado final
echo "=== RESULTADO FINAL ===\n\n";

$resultado = $mysqli->query("
    SELECT 
        pv.id,
        pv.estado,
        se.data_exame,
        se.hora_exame
    FROM permutas_vigilancia pv
    JOIN convocatoria c ON c.id = pv.convocatoria_id
    JOIN sessao_exame se ON se.id = c.sessao_exame_id
    WHERE pv.id IN ($permuta1_id, $permuta2_id)
    ORDER BY pv.id
");

while ($row = $resultado->fetch_assoc()) {
    echo "Permuta #{$row['id']}: {$row['estado']} ({$row['data_exame']} {$row['hora_exame']})\n";
}

// Limpar dados de teste
echo "\n=== LIMPEZA ===\n";
$mysqli->query("DELETE FROM permutas_vigilancia WHERE id IN ($permuta1_id, $permuta2_id)");
echo "✅ Permutas de teste removidas\n\n";

$mysqli->close();

echo "✅ Teste concluído.\n";
