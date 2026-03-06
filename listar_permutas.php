<?php
$pdo = new PDO('mysql:host=localhost;dbname=sistema_gestao', 'root', '');
$stmt = $pdo->query('SELECT * FROM permutas_vigilancia ORDER BY id');
echo "Todas as permutas:\n";
foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
    printf("ID: %d | Conv: %d | Estado: %s | Aceitou: %s\n", 
        $p['id'], 
        $p['convocatoria_id'], 
        $p['estado'] ?: 'NULL',
        $p['substituto_aceitou'] ? 'SIM' : 'NÃO'
    );
}
