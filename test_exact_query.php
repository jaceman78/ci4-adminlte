<?php
$mysqli = new mysqli('localhost', 'root', '', 'sistema_gestao');
$mysqli->set_charset('utf8mb4');

echo "=== QUERY EXATA DO CONTROLLER ===\n";
$query = "SELECT sessao_exame.id, sessao_exame.exame_id, sessao_exame.fase, 
          sessao_exame.data_exame, sessao_exame.hora_exame, sessao_exame.duracao_minutos, 
          sessao_exame.tolerancia_minutos, sessao_exame.num_alunos, 
          sessao_exame.observacoes, sessao_exame.ativo,
          exame.codigo_prova, exame.nome_prova, exame.tipo_prova
          FROM sessao_exame 
          LEFT JOIN exame ON exame.id = sessao_exame.exame_id
          ORDER BY sessao_exame.id DESC
          LIMIT 5";

$result = $mysqli->query($query);

if ($result && $result->num_rows > 0) {
    echo "Total: " . $result->num_rows . " registros\n\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID Sessão: {$row['id']}\n";
        echo "Exame ID: {$row['exame_id']}\n";
        echo "Código Prova: " . ($row['codigo_prova'] ?? 'NULL') . "\n";
        echo "Nome Prova: " . ($row['nome_prova'] ?? 'NULL') . "\n";
        echo "Fase: {$row['fase']}\n";
        echo "Data: {$row['data_exame']} {$row['hora_exame']}\n";
        echo "---\n";
    }
} else {
    echo "Nenhum resultado!\n";
    if ($mysqli->error) {
        echo "Erro: " . $mysqli->error . "\n";
    }
}

$mysqli->close();
