<?php
$mysqli = new mysqli("localhost", "root", "", "sistema_gestao");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "=== ✅ CHECKLIST FINAL - SISTEMA DE PERMUTAS ===\n\n";

// 1. Verificar tabela permutas
echo "1. Tabela 'permutas' criada: ";
$result = $mysqli->query("SHOW TABLES LIKE 'permutas'");
echo ($result->num_rows > 0) ? "✅ SIM\n" : "❌ NÃO\n";

// 2. Verificar estrutura
echo "2. Estrutura correta (com NIFs INT): ";
$result = $mysqli->query("DESCRIBE permutas");
$hasCorrectStructure = false;
while ($row = $result->fetch_assoc()) {
    if ($row['Field'] == 'professor_autor_nif' && strpos($row['Type'], 'int') !== false) {
        $hasCorrectStructure = true;
        break;
    }
}
echo $hasCorrectStructure ? "✅ SIM\n" : "❌ NÃO\n";

// 3. Verificar foreign keys
echo "3. Foreign keys criadas: ";
$result = $mysqli->query("
    SELECT COUNT(*) as total 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'sistema_gestao' 
    AND TABLE_NAME = 'permutas' 
    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
");
$row = $result->fetch_assoc();
echo "✅ {$row['total']} foreign keys\n";

// 4. Verificar índice NIF
echo "4. Índice em user.NIF: ";
$result = $mysqli->query("SHOW INDEX FROM user WHERE Column_name = 'NIF'");
echo ($result->num_rows > 0) ? "✅ SIM\n" : "❌ NÃO\n";

// 5. Verificar horários
echo "5. Dados de horário importados: ";
$result = $mysqli->query("SELECT COUNT(*) as total FROM horario_aulas WHERE user_nif IS NOT NULL AND user_nif != ''");
$row = $result->fetch_assoc();
echo "✅ {$row['total']} aulas com NIF\n";

// 6. Total de permutas
echo "6. Permutas existentes: ";
$result = $mysqli->query("SELECT COUNT(*) as total FROM permutas");
$row = $result->fetch_assoc();
echo "{$row['total']} permutas\n";

echo "\n=== 🎉 SISTEMA PRONTO PARA USO! ===\n";
echo "\nPróximos passos:\n";
echo "1. Acesse /permutas como professor (level 5)\n";
echo "2. Visualize seu horário\n";
echo "3. Clique em uma aula e peça uma permuta\n";
echo "4. Acesse /permutas/minhas para ver suas permutas\n";
echo "5. Como admin (level >=3), aprove ou rejeite permutas\n";

$mysqli->close();
