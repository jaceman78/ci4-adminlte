-- =====================================================
-- SCRIPT PARA ADICIONAR CAMPO ano_letivo_id À TABELA permutas
-- Execute este script via phpMyAdmin ou MySQL Workbench
--
-- Este campo permite rastrear em que ano letivo a permuta foi criada
-- =====================================================

USE sistema_gestao;

-- 1. Adicionar coluna ano_letivo_id
ALTER TABLE `permutas` 
ADD COLUMN `ano_letivo_id` INT(11) UNSIGNED NULL DEFAULT NULL 
COMMENT 'FK para ano_letivo - ano letivo em que a permuta foi criada' 
AFTER `aula_original_id`;

-- 2. Criar índice
ALTER TABLE `permutas` 
ADD INDEX `permutas_ano_letivo_idx` (`ano_letivo_id`);

-- 3. Adicionar foreign key
ALTER TABLE `permutas`
ADD CONSTRAINT `permutas_ano_letivo_fk`
FOREIGN KEY (`ano_letivo_id`) 
REFERENCES `ano_letivo`(`id_anoletivo`)
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- =====================================================
-- OPCIONAL: Atualizar permutas existentes com o ano letivo ativo
-- =====================================================

-- Se quiser atualizar todas as permutas existentes com o ano letivo atualmente ativo:
UPDATE `permutas` 
SET `ano_letivo_id` = (
    SELECT `id_anoletivo` 
    FROM `ano_letivo` 
    WHERE `status` = 1 
    LIMIT 1
)
WHERE `ano_letivo_id` IS NULL;

-- Fim do script
