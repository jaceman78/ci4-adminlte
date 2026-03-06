<?php

/**
 * Script de teste para verificar convocatórias de utilizadores nível 5 (técnicos)
 */

// Conectar diretamente à base de dados
$host = 'localhost';
$dbname = 'sistema_gestao';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Teste de Convocatórias para Técnicos (Nível 5) ===\n\n";

    // 1. Encontrar utilizadores de nível 5
    $stmt = $pdo->query("SELECT id, name, email, level FROM user WHERE level = 5 LIMIT 5");
    $tecnicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Utilizadores de nível 5 encontrados: " . count($tecnicos) . "\n";
    foreach ($tecnicos as $tecnico) {
        echo "  - ID: {$tecnico['id']}, Nome: {$tecnico['name']}, Email: {$tecnico['email']}\n";
    }

    if (empty($tecnicos)) {
        echo "\nNenhum técnico (nível 5) encontrado no sistema.\n";
        exit;
    }

    echo "\n--- Verificando convocatórias ---\n";

    foreach ($tecnicos as $tecnico) {
        $userId = $tecnico['id'];
        echo "\nTécnico: {$tecnico['name']} (ID: {$userId})\n";
        
        // Buscar convocatórias deste técnico
        $stmt = $pdo->prepare("
            SELECT 
                c.id, 
                c.user_id, 
                c.sessao_exame_id,
                se.data_exame,
                se.hora_exame,
                e.codigo_prova,
                e.nome_prova
            FROM convocatoria c
            LEFT JOIN sessao_exame se ON se.id = c.sessao_exame_id
            LEFT JOIN exame e ON e.id = se.exame_id
            WHERE c.user_id = ?
            ORDER BY se.data_exame DESC
            LIMIT 5
        ");
        $stmt->execute([$userId]);
        $convocatorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($convocatorias)) {
            echo "  ❌ Nenhuma convocatória encontrada para este técnico\n";
        } else {
            echo "  ✅ " . count($convocatorias) . " convocatória(s) encontrada(s):\n";
            foreach ($convocatorias as $conv) {
                echo "    - Convocatória ID: {$conv['id']}, Exame: {$conv['codigo_prova']} - {$conv['nome_prova']}, Data: {$conv['data_exame']} {$conv['hora_exame']}\n";
            }
        }
    }

    echo "\n--- Verificando permutas existentes ---\n";

    $stmt = $pdo->query("
        SELECT 
            pv.id,
            pv.user_original_id,
            u1.name as original_nome,
            u1.level as original_level,
            pv.user_substituto_id,
            u2.name as substituto_nome,
            u2.level as substituto_level,
            pv.estado,
            pv.criado_em
        FROM permutas_vigilancia pv
        LEFT JOIN user u1 ON u1.id = pv.user_original_id
        LEFT JOIN user u2 ON u2.id = pv.user_substituto_id
        WHERE u1.level = 5 OR u2.level = 5
        ORDER BY pv.criado_em DESC
        LIMIT 10
    ");

    $permutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($permutas)) {
        echo "Nenhuma permuta envolvendo técnicos (nível 5) encontrada.\n";
    } else {
        echo count($permutas) . " permuta(s) envolvendo técnicos:\n";
        foreach ($permutas as $permuta) {
            echo "  - Permuta ID: {$permuta['id']}\n";
            echo "    Original: {$permuta['original_nome']} (Nível {$permuta['original_level']})\n";
            echo "    Substituto: {$permuta['substituto_nome']} (Nível {$permuta['substituto_level']})\n";
            echo "    Estado: {$permuta['estado']}, Criada em: {$permuta['criado_em']}\n";
        }
    }

    echo "\n=== Fim do teste ===\n";
    
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage() . "\n";
}
