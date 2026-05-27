-- =====================================================
-- ALTER sessao_exame.fase - Adicionar 'Fase única'
-- Data: 2026-05-25
-- Descrição: Adiciona o valor 'Fase única' ao ENUM fase
--            da tabela sessao_exame
-- =====================================================

ALTER TABLE `sessao_exame` 
MODIFY COLUMN `fase` ENUM('1ª fase', '2ªfase', 'Prova Ensaio', 'Oral', 'Época Especial', 'Fase única') NOT NULL 
COMMENT 'Fase/Turno do exame';

-- Verificar a alteração
SELECT 'sessao_exame.fase' AS tabela_campo, COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'sessao_exame' 
  AND COLUMN_NAME = 'fase';
