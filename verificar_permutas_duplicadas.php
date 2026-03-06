<?php
$pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');

echo "=== ANÁLISE DE PERMUTAS POR CONVOCATÓRIA ===\n\n";

// Buscar permutas agrupadas por convocatória
$stmt = $pdo->query("
    SELECT 
        pv.id as permuta_id,
        pv.convocatoria_id,
        pv.estado,
        pv.substituto_aceitou,
        pv.validado_secretariado,
        pv.criado_em,
        c.user_id as convocatoria_user_id,
        u1.name as original_nome,
        u2.name as substituto_nome,
        se.data_exame,
        e.codigo_prova
    FROM permutas_vigilancia pv
    LEFT JOIN convocatoria c ON c.id = pv.convocatoria_id
    LEFT JOIN user u1 ON u1.id = pv.user_original_id
    LEFT JOIN user u2 ON u2.id = pv.user_substituto_id
    LEFT JOIN sessao_exame se ON se.id = c.sessao_exame_id
    LEFT JOIN exame e ON e.id = se.exame_id
    WHERE pv.estado NOT IN ('CANCELADO', 'RECUSADO_SUBSTITUTO')
    ORDER BY pv.convocatoria_id, pv.criado_em
");

$permutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($permutas)) {
    echo "Nenhuma permuta ativa encontrada.\n";
    exit;
}

// Agrupar por convocatória
$porConvocatoria = [];
foreach ($permutas as $permuta) {
    $convId = $permuta['convocatoria_id'];
    if (!isset($porConvocatoria[$convId])) {
        $porConvocatoria[$convId] = [];
    }
    $porConvocatoria[$convId][] = $permuta;
}

// Identificar problemas
$problemas = 0;

foreach ($porConvocatoria as $convId => $permutasConv) {
    if (count($permutasConv) > 1) {
        $problemas++;
        $primeira = $permutasConv[0];
        
        echo "⚠️  PROBLEMA DETECTADO - Convocatória ID: {$convId}\n";
        echo "   Exame: {$primeira['codigo_prova']} em {$primeira['data_exame']}\n";
        echo "   Professor Original: {$primeira['original_nome']}\n";
        echo "   " . str_repeat("-", 60) . "\n";
        
        foreach ($permutasConv as $idx => $p) {
            $aceitou = $p['substituto_aceitou'] ? '✅ ACEITOU' : '⏳ Pendente';
            $validado = $p['validado_secretariado'] ? '✅ Validado' : '⏳ Não validado';
            
            echo "   Permuta #{$p['permuta_id']} ({$p['criado_em']})\n";
            echo "     Substituto: {$p['substituto_nome']}\n";
            echo "     Estado: {$p['estado']}\n";
            echo "     Aceite Substituto: {$aceitou}\n";
            echo "     Validação: {$validado}\n";
            echo "\n";
        }
    }
}

if ($problemas === 0) {
    echo "✅ Nenhum problema detectado. Todas as convocatórias têm no máximo 1 permuta ativa.\n";
} else {
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "TOTAL DE PROBLEMAS: {$problemas} convocatória(s) com múltiplas permutas ativas\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
}
