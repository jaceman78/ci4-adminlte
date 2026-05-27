<?php
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');
$mysqli->set_charset('utf8mb4');

if ($mysqli->connect_error) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

echo "=== VERIFICAÇÃO DE PERMUTAS ACEITES COM CONFLITO DE HORÁRIO ===\n\n";

// Query para encontrar permutas aceites com conflito de horário
$query = "
    SELECT 
        p1.id AS permuta1_id,
        p1.estado AS permuta1_estado,
        p1.user_substituto_id AS substituto_id,
        u.name AS substituto_nome,
        se1.data_exame,
        se1.hora_exame,
        e1.codigo_prova AS prova1,
        p1.convocatoria_id AS conv1_id,
        p2.id AS permuta2_id,
        p2.estado AS permuta2_estado,
        e2.codigo_prova AS prova2,
        p2.convocatoria_id AS conv2_id,
        p1.data_resposta_substituto AS data_aceite1,
        p2.data_resposta_substituto AS data_aceite2
    FROM permutas_vigilancia p1
    JOIN convocatoria c1 ON c1.id = p1.convocatoria_id
    JOIN sessao_exame se1 ON se1.id = c1.sessao_exame_id
    JOIN exame e1 ON e1.id = se1.exame_id
    JOIN permutas_vigilancia p2 ON p2.user_substituto_id = p1.user_substituto_id 
        AND p2.id != p1.id
        AND p2.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
    JOIN convocatoria c2 ON c2.id = p2.convocatoria_id
    JOIN sessao_exame se2 ON se2.id = c2.sessao_exame_id
        AND se2.data_exame = se1.data_exame
        AND se2.hora_exame = se1.hora_exame
    JOIN exame e2 ON e2.id = se2.exame_id
    JOIN user u ON u.id = p1.user_substituto_id
    WHERE p1.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
    ORDER BY se1.data_exame, se1.hora_exame, p1.user_substituto_id
";

$result = $mysqli->query($query);

if ($result && $result->num_rows > 0) {
    echo "🚨 ENCONTRADOS " . $result->num_rows . " CONFLITO(S) DE HORÁRIO!\n";
    echo str_repeat("=", 90) . "\n\n";
    
    $conflitos = [];
    while ($row = $result->fetch_assoc()) {
        // Evitar duplicatas (cada conflito aparece 2x na query)
        $chave = min($row['permuta1_id'], $row['permuta2_id']) . '-' . max($row['permuta1_id'], $row['permuta2_id']);
        if (isset($conflitos[$chave])) continue;
        $conflitos[$chave] = true;
        
        echo "❌ CONFLITO DETECTADO:\n";
        echo "   Professor Substituto: {$row['substituto_nome']} (User ID: {$row['substituto_id']})\n";
        echo "   Data/Hora do Exame: {$row['data_exame']} às {$row['hora_exame']}\n\n";
        
        echo "   📋 Permuta #1:\n";
        echo "      - ID: {$row['permuta1_id']}\n";
        echo "      - Estado: {$row['permuta1_estado']}\n";
        echo "      - Prova: {$row['prova1']}\n";
        echo "      - Convocatória ID: {$row['conv1_id']}\n";
        echo "      - Data Aceite: {$row['data_aceite1']}\n\n";
        
        echo "   📋 Permuta #2:\n";
        echo "      - ID: {$row['permuta2_id']}\n";
        echo "      - Estado: {$row['permuta2_estado']}\n";
        echo "      - Prova: {$row['prova2']}\n";
        echo "      - Convocatória ID: {$row['conv2_id']}\n";
        echo "      - Data Aceite: {$row['data_aceite2']}\n";
        
        echo str_repeat("-", 90) . "\n\n";
    }
    
    echo "\n⚠️ PROBLEMA CONFIRMADO: O professor substituto aceitou múltiplas permutas para o mesmo horário.\n";
    echo "   A validação no código NÃO está a funcionar corretamente.\n\n";
    
} else {
    echo "✅ Nenhum conflito de horário encontrado.\n";
    echo "   Todas as permutas aceites respeitam a regra de horário único.\n\n";
}

// Verificar estatísticas gerais
echo "\n=== ESTATÍSTICAS DE PERMUTAS ===\n";

$stats = $mysqli->query("
    SELECT 
        estado,
        COUNT(*) as total
    FROM permutas_vigilancia
    GROUP BY estado
    ORDER BY 
        CASE estado
            WHEN 'PENDENTE' THEN 1
            WHEN 'ACEITE_SUBSTITUTO' THEN 2
            WHEN 'VALIDADO_SECRETARIADO' THEN 3
            WHEN 'RECUSADO_SUBSTITUTO' THEN 4
            WHEN 'REJEITADO_SECRETARIADO' THEN 5
            WHEN 'CANCELADO' THEN 6
            ELSE 7
        END
");

if ($stats) {
    echo "\n";
    while ($row = $stats->fetch_assoc()) {
        $emoji = match($row['estado']) {
            'PENDENTE' => '⏳',
            'ACEITE_SUBSTITUTO' => '✅',
            'VALIDADO_SECRETARIADO' => '✔️',
            'RECUSADO_SUBSTITUTO' => '❌',
            'REJEITADO_SECRETARIADO' => '🚫',
            'CANCELADO' => '⛔',
            default => '📋'
        };
        printf("  %s %-25s : %3d\n", $emoji, $row['estado'], $row['total']);
    }
}

// Mostrar últimas permutas aceites
echo "\n=== ÚLTIMAS 10 PERMUTAS ACEITES ===\n\n";

$ultimas = $mysqli->query("
    SELECT 
        pv.id,
        pv.convocatoria_id,
        pv.estado,
        pv.data_resposta_substituto,
        u.name AS substituto_nome,
        se.data_exame,
        se.hora_exame,
        e.codigo_prova
    FROM permutas_vigilancia pv
    JOIN user u ON u.id = pv.user_substituto_id
    JOIN convocatoria c ON c.id = pv.convocatoria_id
    JOIN sessao_exame se ON se.id = c.sessao_exame_id
    LEFT JOIN exame e ON e.id = se.exame_id
    WHERE pv.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
    ORDER BY pv.data_resposta_substituto DESC
    LIMIT 10
");

if ($ultimas && $ultimas->num_rows > 0) {
    while ($row = $ultimas->fetch_assoc()) {
        $codigoProva = $row['codigo_prova'] ?? 'N/A';
        echo "  ID {$row['id']}: {$row['substituto_nome']} → {$codigoProva} ({$row['data_exame']} {$row['hora_exame']}) - Aceite em {$row['data_resposta_substituto']}\n";
    }
} else {
    echo "  Nenhuma permuta aceite encontrada.\n";
}

$mysqli->close();
echo "\n✅ Verificação concluída.\n";
