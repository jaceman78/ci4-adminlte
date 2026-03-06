-- =====================================================
-- ALTER TABLE exame - Adicionar 'Prova ensaio' ao ENUM tipo_prova
-- Data: 2026-02-09
-- Descrição: Adiciona o valor 'Prova ensaio' ao campo tipo_prova
--            para permitir a categorização de provas de ensaio
-- =====================================================

-- Modificar o campo tipo_prova para incluir 'Prova ensaio'
ALTER TABLE `exame` 
MODIFY COLUMN `tipo_prova` ENUM('Exame Nacional', 'Prova Final', 'Prova ensaio', 'MODa') NOT NULL 
COMMENT 'Categoria da prova';

-- Verificar a alteração
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_COMMENT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'exame' 
AND COLUMN_NAME = 'tipo_prova';
