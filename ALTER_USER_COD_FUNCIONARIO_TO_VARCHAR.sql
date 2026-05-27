-- ====================================================================
-- CORRIGIR TIPO DE DADO DO CAMPO cod_funcionario
-- ====================================================================
-- Problema: Campo cod_funcionario é INT(11) mas precisa armazenar 
-- códigos como "F001", "F026", etc.
-- Solução: Alterar para VARCHAR(20)
-- ====================================================================

USE sistema_gestao;

-- 1. Alterar tipo de cod_funcionario de INT para VARCHAR
ALTER TABLE `user` 
MODIFY COLUMN `cod_funcionario` VARCHAR(20) NULL DEFAULT NULL
COMMENT 'Código de funcionário (ex: F001, F026, etc.)';

-- 2. Verificar alteração
DESCRIBE `user`;

-- 3. Mostrar dados atuais
SELECT id, NIF, name, cod_funcionario, categoria 
FROM `user` 
WHERE cod_funcionario IS NOT NULL OR categoria IS NOT NULL
LIMIT 10;
