<?php
$db = mysqli_connect('localhost', 'root', '', 'sistema_gestao');
if (!$db) {
    die('Erro de conexão: ' . mysqli_connect_error());
}

$result = mysqli_query($db, "SHOW TABLES LIKE 'estados_ticket'");
if (mysqli_num_rows($result) > 0) {
    echo "✓ Tabela estados_ticket EXISTE\n\n";
    
    $desc = mysqli_query($db, "DESCRIBE estados_ticket");
    echo "Estrutura da tabela:\n";
    while ($row = mysqli_fetch_assoc($desc)) {
        echo "  - {$row['Field']} ({$row['Type']})\n";
    }
} else {
    echo "✗ Tabela estados_ticket NÃO EXISTE\n";
}

$result2 = mysqli_query($db, "SHOW TABLES LIKE 'estados_ticket_transicoes'");
if (mysqli_num_rows($result2) > 0) {
    echo "\n✓ Tabela estados_ticket_transicoes EXISTE\n";
} else {
    echo "\n✗ Tabela estados_ticket_transicoes NÃO EXISTE\n";
}

mysqli_close($db);
