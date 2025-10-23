<?php
// Configuração do banco
$mysqli = new mysqli("localhost", "root", "", "sistema_gestao");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== Verificando dados na tabela horario_aulas ===\n\n";

// Total de aulas
$result = $mysqli->query("SELECT COUNT(*) as total FROM horario_aulas");
$row = $result->fetch_assoc();
echo "Total de aulas na tabela: {$row['total']}\n";

// Aulas com NIF
$result = $mysqli->query("SELECT COUNT(*) as total FROM horario_aulas WHERE user_nif IS NOT NULL AND user_nif != ''");
$row = $result->fetch_assoc();
echo "Aulas com NIF definido: {$row['total']}\n";

// Aulas sem NIF
$result = $mysqli->query("SELECT COUNT(*) as total FROM horario_aulas WHERE user_nif IS NULL OR user_nif = ''");
$row = $result->fetch_assoc();
echo "Aulas SEM NIF definido: {$row['total']}\n\n";

// Mostrar alguns exemplos de aulas
echo "=== Exemplos de aulas (primeiras 5) ===\n";
$result = $mysqli->query("SELECT id_aula, codigo_turma, disciplina_id, user_nif, dia_semana, hora_inicio FROM horario_aulas LIMIT 5");
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id_aula']} | Turma: {$row['codigo_turma']} | Disciplina: {$row['disciplina_id']} | NIF: {$row['user_nif']} | Dia: {$row['dia_semana']} | Hora: {$row['hora_inicio']}\n";
}

$mysqli->close();

