-- ============================================================
-- CORREÇÃO TABELA ferias_atribuicao EM PRODUÇÃO
-- Data: 2026-03-12
-- Problema: Registos inseridos com id=0
-- ============================================================

-- PASSO 1: Fazer BACKUP (IMPORTANTE!)
-- Exportar tabela antes de fazer alterações
-- mysqldump -u user -p database ferias_atribuicao > backup_ferias_atribuicao_2026-03-12.sql

-- PASSO 2: Verificar registos problemáticos
SELECT 'Registos com id=0:' as info;
SELECT * FROM ferias_atribuicao WHERE id = 0;

-- PASSO 3: Verificar AUTO_INCREMENT atual
SELECT 'AUTO_INCREMENT atual:' as info;
SELECT AUTO_INCREMENT 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'ferias_atribuicao';

-- PASSO 4: SOLUÇÃO - Apagar registos inválidos e corrigir estrutura
START TRANSACTION;

-- 4.1 - Apagar registos com id=0 (são inválidos)
DELETE FROM ferias_atribuicao WHERE id = 0;
SELECT 'Registos com id=0 apagados' as info;

-- 4.2 - Garantir que coluna id tem AUTO_INCREMENT
ALTER TABLE ferias_atribuicao 
MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT;
SELECT 'Coluna id configurada com AUTO_INCREMENT' as info;

-- 4.3 - Resetar AUTO_INCREMENT para próximo ID correto
-- Descobre o maior ID existente e define próximo
SET @max_id = (SELECT IFNULL(MAX(id), 0) FROM ferias_atribuicao);
SET @sql = CONCAT('ALTER TABLE ferias_atribuicao AUTO_INCREMENT = ', @max_id + 1);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
SELECT CONCAT('AUTO_INCREMENT resetado para ', @max_id + 1) as info;

-- 4.4 - Verificar estrutura final
SHOW CREATE TABLE ferias_atribuicao\G

COMMIT;

-- PASSO 5: Testar INSERT
SELECT 'Testando INSERT...' as info;
-- Este INSERT deve criar um novo id automaticamente (não incluir id no INSERT)
-- Exemplo: 
-- INSERT INTO ferias_atribuicao (user_nif, anoletivo_id, dias_base, atribuido_por) 
-- VALUES (123456789, 9, 22, 1);

SELECT 'Últimos registos inseridos:' as info;
SELECT * FROM ferias_atribuicao ORDER BY id DESC LIMIT 5;

-- ============================================================
-- EXECUTAR EM PRODUÇÃO:
-- mysql -h [host] -u [user] -p [database] < CORRIGIR_FERIAS_ATRIBUICAO_PRODUCAO.sql
-- ============================================================
