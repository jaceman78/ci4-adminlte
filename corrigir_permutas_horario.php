<?php
/**
 * Script de Correção: Permutas Duplicadas para Mesmo Horário
 * 
 * Problema: Substitutos aceitaram múltiplas permutas para o mesmo dia e hora
 * Solução: Manter apenas a permuta mais recente, cancelar as antigas
 * 
 * Data: 2026-02-12
 */

$pdo = new PDO(
    "mysql:host=localhost;dbname=sistema_gestao;charset=utf8mb4",
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "===== CORREÇÃO DE PERMUTAS DUPLICADAS (MESMO HORÁRIO) =====\n\n";

// 1. IDENTIFICAR PERMUTAS DUPLICADAS
echo "1. Identificando permutas duplicadas...\n";

$stmt = $pdo->query("
    SELECT 
        pv.user_substituto_id,
        u.name AS nome_substituto,
        se.data_exame,
        se.hora_exame,
        COUNT(*) AS num_permutas,
        GROUP_CONCAT(pv.id ORDER BY pv.criado_em DESC) AS permuta_ids,
        GROUP_CONCAT(pv.convocatoria_id ORDER BY pv.criado_em DESC) AS convocatoria_ids,
        GROUP_CONCAT(pv.criado_em ORDER BY pv.criado_em DESC) AS datas_criacao
    FROM permutas_vigilancia pv
    JOIN convocatoria c ON c.id = pv.convocatoria_id
    JOIN sessao_exame se ON se.id = c.sessao_exame_id
    JOIN user u ON u.id = pv.user_substituto_id
    WHERE pv.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
    GROUP BY pv.user_substituto_id, se.data_exame, se.hora_exame
    HAVING COUNT(*) > 1
");

$conflitos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($conflitos)) {
    echo "   ✅ Nenhum conflito de horário encontrado.\n\n";
    exit(0);
}

echo "   ⚠️ Encontrados " . count($conflitos) . " conflito(s):\n\n";

foreach ($conflitos as $conflito) {
    echo "   Substituto: {$conflito['nome_substituto']} (ID: {$conflito['user_substituto_id']})\n";
    echo "   Data/Hora: {$conflito['data_exame']} {$conflito['hora_exame']}\n";
    echo "   Permutas: {$conflito['permuta_ids']}\n";
    echo "   Convocatórias: {$conflito['convocatoria_ids']}\n";
    echo "   Datas criação: {$conflito['datas_criacao']}\n\n";
}

// 2. CONFIRMAÇÃO DO UTILIZADOR
echo "2. Deseja corrigir automaticamente estes conflitos?\n";
echo "   (Será mantida a permuta mais recente, as antigas serão CANCELADAS)\n\n";
echo "   Digite 'SIM' para confirmar: ";

$handle = fopen("php://stdin", "r");
$confirmacao = trim(fgets($handle));
fclose($handle);

if (strtoupper($confirmacao) !== 'SIM') {
    echo "\n❌ Operação cancelada pelo utilizador.\n";
    exit(0);
}

echo "\n3. Corrigindo conflitos...\n\n";

$corrigidas = 0;

foreach ($conflitos as $conflito) {
    $permutaIds = explode(',', $conflito['permuta_ids']);
    $maisRecente = $permutaIds[0]; // Primeira da lista (ORDER BY criado_em DESC)
    $paraCancel = array_slice($permutaIds, 1); // Todas exceto a mais recente
    
    echo "   Substituto: {$conflito['nome_substituto']}\n";
    echo "   Mantendo: Permuta #{$maisRecente}\n";
    echo "   Cancelando: Permutas #" . implode(', #', $paraCancel) . "\n";
    
    $stmt = $pdo->prepare("
        UPDATE permutas_vigilancia 
        SET estado = 'CANCELADO',
            observacoes = CONCAT(
                COALESCE(observacoes, ''),
                '\n[AUTO-2026-02-12] Permuta cancelada automaticamente por conflito de horário. Substituto já aceitou outra permuta para o mesmo dia/hora.'
            )
        WHERE id IN (" . implode(',', array_map('intval', $paraCancel)) . ")
    ");
    
    $stmt->execute();
    $corrigidas += $stmt->rowCount();
    
    echo "   ✅ {$stmt->rowCount()} permuta(s) cancelada(s)\n\n";
}

// 4. VERIFICAÇÃO FINAL
echo "4. Verificação final...\n";

$stmt = $pdo->query("
    SELECT COUNT(*) as total
    FROM (
        SELECT 
            pv.user_substituto_id,
            se.data_exame,
            se.hora_exame,
            COUNT(*) AS num_permutas
        FROM permutas_vigilancia pv
        JOIN convocatoria c ON c.id = pv.convocatoria_id
        JOIN sessao_exame se ON se.id = c.sessao_exame_id
        WHERE pv.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
        GROUP BY pv.user_substituto_id, se.data_exame, se.hora_exame
        HAVING COUNT(*) > 1
    ) conflitos
");

$conflitosRestantes = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

if ($conflitosRestantes == 0) {
    echo "   ✅ Todos os conflitos foram corrigidos!\n";
    echo "   Total de permutas canceladas: {$corrigidas}\n";
} else {
    echo "   ⚠️ Ainda existem {$conflitosRestantes} conflito(s) não resolvidos.\n";
}

echo "\n===== CONCLUSÃO =====\n";
echo "✅ Correção concluída!\n";
echo "✅ Validação implementada no código para prevenir futuros conflitos.\n\n";
