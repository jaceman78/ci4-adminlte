-- =====================================================
-- ALTER TABLE convocatoria - Adicionar campo presença
-- Data: 2026-02-11
-- Descrição: Adiciona campo para marcar presença/falta dos professores
-- =====================================================

-- Adicionar campo presenca
ALTER TABLE `convocatoria` 
ADD COLUMN `presenca` ENUM('Pendente', 'Presente', 'Falta', 'Falta Justificada') NOT NULL DEFAULT 'Pendente' 
COMMENT 'Estado de presença do professor na vigilância' 
AFTER `estado_confirmacao`;

-- Adicionar índice para pesquisas rápidas
ALTER TABLE `convocatoria` 
ADD INDEX `idx_presenca` (`presenca`);

-- Verificar a alteração
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'convocatoria' 
AND COLUMN_NAME = 'presenca';
