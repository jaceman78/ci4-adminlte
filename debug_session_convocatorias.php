<?php

// DEBUG: Verificar dados de sessão e convocatórias
// Este ficheiro deve ser executado através do navegador por um utilizador logado

// Inicializar CodeIgniter
require_once __DIR__ . '/app/Config/Paths.php';

$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';
require $bootstrap;

$app = Config\Services::codeigniter();
$app->initialize();

// Obter dados da sessão
$session = session();
$loggedUserData = $session->get('LoggedUserData');

echo "<h2>DEBUG: Dados da Sessão e Convocatórias</h2>";
echo "<h3>Informações do Utilizador Logado:</h3>";
echo "<pre>";
print_r($loggedUserData);
echo "</pre>";

if (!$loggedUserData) {
    echo "<p style='color: red;'>❌ Nenhum utilizador logado!</p>";
    exit;
}

$userId = $loggedUserData['id'] ?? null;
$userLevel = $loggedUserData['level'] ?? null;

echo "<h3>Verificação de Convocatórias:</h3>";
echo "<p>UserId: {$userId}</p>";
echo "<p>UserLevel: {$userLevel}</p>";

// Carregar ConvocatoriaModel
$convocatoriaModel = new \App\Models\ConvocatoriaModel();
$convocatorias = $convocatoriaModel->getByProfessor($userId, true);

echo "<p>Número de convocatórias encontradas: <strong>" . count($convocatorias) . "</strong></p>";

if (!empty($convocatorias)) {
    echo "<h4>Listagem de Convocatórias:</h4>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>User ID</th><th>Exame</th><th>Data</th><th>Hora</th><th>Sala</th></tr>";
    
    foreach ($convocatorias as $conv) {
        $userMatch = ($conv['user_id'] == $userId) ? "✅" : "❌";
        echo "<tr>";
        echo "<td>{$conv['id']}</td>";
        echo "<td>{$conv['user_id']} {$userMatch}</td>";
        echo "<td>{$conv['codigo_prova']} - {$conv['nome_prova']}</td>";
        echo "<td>{$conv['data_exame']}</td>";
        echo "<td>{$conv['hora_exame']}</td>";
        echo "<td>" . ($conv['codigo_sala'] ?? 'N/A') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ Nenhuma convocatória encontrada para este utilizador.</p>";
    echo "<p>Se está a ver convocatórias no dashboard, algo está errado com a consulta.</p>";
}
