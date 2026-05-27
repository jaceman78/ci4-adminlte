<?php

/**
 * SCRIPT DE DIAGNÓSTICO - TABELA ferias_atribuicao PRODUÇÃO
 * 
 * Execute em produção:
 * php spark db:query "SELECT @@version as mysql_version"
 */

// Conectar à base de dados de produção e executar:

echo "=== DIAGNÓSTICO TABELA ferias_atribuicao ===\n\n";

echo "1. VERIFICAR ESTRUTURA DA TABELA:\n";
echo "SHOW CREATE TABLE ferias_atribuicao\\G\n\n";

echo "2. VERIFICAR REGISTOS COM id=0:\n";
echo "SELECT * FROM ferias_atribuicao WHERE id = 0;\n\n";

echo "3. VERIFICAR AUTO_INCREMENT ATUAL:\n";
echo "SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ferias_atribuicao';\n\n";

echo "4. TENTAR CORRIGIR (SE NECESSÁRIO):\n\n";

echo "-- A) Apagar registos inválidos com id=0\n";
echo "DELETE FROM ferias_atribuicao WHERE id = 0;\n\n";

echo "-- B) Garantir que id tem AUTO_INCREMENT\n";
echo "ALTER TABLE ferias_atribuicao MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT;\n\n";

echo "-- C) Resetar AUTO_INCREMENT para próximo valor correto\n";
echo "ALTER TABLE ferias_atribuicao AUTO_INCREMENT = 1;\n\n";

echo "5. VERIFICAR CONSTRAINTS:\n";
echo "SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_NAME = 'ferias_atribuicao';\n\n";

echo "6. VERIFICAR ÍNDICES:\n";
echo "SHOW INDEXES FROM ferias_atribuicao;\n\n";

echo "\n=== EXECUTAR EM PRODUÇÃO ===\n";
echo "mysql -h [host] -u [user] -p [database] < diagnostico.sql\n";

?>
