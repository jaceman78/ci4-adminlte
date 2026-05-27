<?php
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');
$mysqli->set_charset('utf8mb4');

echo "=== ANÁLISE COMPLETA DE PERMUTAS ===\n\n";

// Total de permutas
$total = $mysqli->query("SELECT COUNT(*) as total FROM permutas_vigilancia");
$totalRow = $total->fetch_assoc();
echo "Total de permutas no sistema: {$totalRow['total']}\n\n";

// Por estado
echo "Permutas por estado:\n";
$porEstado = $mysqli->query("SELECT estado, COUNT(*) as cnt FROM permutas_vigilancia GROUP BY estado ORDER BY cnt DESC");
while($row = $porEstado->fetch_assoc()) {
    echo "  {$row['estado']}: {$row['cnt']}\n";
}

// Últimas permutas criadas (qualquer estado)
echo "\n=== ÚLTIMAS 10 PERMUTAS CRIADAS ===\n\n";
$ultimas = $mysqli->query("
    SELECT 
        pv.id,
        pv.convocatoria_id,
        pv.estado,
        pv.criado_em,
        pv.data_resposta_substituto,
        u1.name AS autor_nome,
        u2.name AS substituto_nome,
        se.data_exame,
        se.hora_exame
    FROM permutas_vigilancia pv
    LEFT JOIN user u1 ON u1.id = pv.user_original_id
    LEFT JOIN user u2 ON u2.id = pv.user_substituto_id
    LEFT JOIN convocatoria c ON c.id = pv.convocatoria_id
    LEFT JOIN sessao_exame se ON se.id = c.sessao_exame_id
    ORDER BY pv.criado_em DESC
    LIMIT 10
");

if ($ultimas && $ultimas->num_rows > 0) {
    while ($row = $ultimas->fetch_assoc()) {
        echo "ID {$row['id']} - {$row['estado']}\n";
        echo "  Autor: {$row['autor_nome']}\n";
        echo "  Substituto: {$row['substituto_nome']}\n";
        echo "  Exame: {$row['data_exame']} {$row['hora_exame']}\n";
        echo "  Criado: {$row['criado_em']}\n";
        if ($row['data_resposta_substituto']) {
            echo "  Resposta: {$row['data_resposta_substituto']}\n";
        }
        echo "\n";
    }
} else {
    echo "Nenhuma permuta encontrada.\n";
}

// Verificar se есть convocatórias
echo "=== CONVOCATÓRIAS NO SISTEMA ===\n";
$convs = $mysqli->query("SELECT COUNT(*) as total FROM convocatoria");
$convsRow = $convs->fetch_assoc();
echo "Total de convocatórias: {$convsRow['total']}\n";

$mysqli->close();
