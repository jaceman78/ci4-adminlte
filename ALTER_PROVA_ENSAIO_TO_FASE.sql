-- =====================================================
-- ATUALIZAR ESTRUTURA - Mover 'Prova ensaio' para sessao_exame.fase
-- Data: 2026-02-09
-- Descrição: Remove 'Prova ensaio' de exame.tipo_prova
--            Adiciona 'Prova ensaio' a sessao_exame.fase
-- =====================================================

-- 1. Remover 'Prova ensaio' do ENUM exame.tipo_prova
ALTER TABLE `exame` 
MODIFY COLUMN `tipo_prova` ENUM('Exame Nacional', 'Prova Final', 'MODa') NOT NULL 
COMMENT 'Categoria da prova';

-- 2. Alterar sessao_exame.fase de VARCHAR para ENUM com valores atualizados
ALTER TABLE `sessao_exame` 
MODIFY COLUMN `fase` ENUM('1ªfase', '2ªfase', 'Prova Ensaio', 'Oral', 'Época Especial') NOT NULL 
COMMENT 'Fase/Turno do exame';

-- 3. Adicionar índice ao campo fase (se ainda não existir)
ALTER TABLE `sessao_exame` 
ADD INDEX IF NOT EXISTS `idx_fase` (`fase`);

-- Verificar as alterações
SELECT 'exame.tipo_prova' as tabela_campo, COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'exame' 
AND COLUMN_NAME = 'tipo_prova'
UNION ALL
SELECT 'sessao_exame.fase' as tabela_campo, COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'sessao_exame' 
AND COLUMN_NAME = 'fase';
