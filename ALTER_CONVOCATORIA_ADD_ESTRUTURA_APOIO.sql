-- =====================================================
-- Adicionar 'Estrutura de Apoio' ao ENUM funcao
-- Tabela: convocatoria
-- Data: 02/04/2026
-- =====================================================

ALTER TABLE `convocatoria`
MODIFY COLUMN `funcao` ENUM(
    'Vigilante',
    'Suplente',
    'Coadjuvante',
    'Júri',
    'Verificar Calculadoras',
    'Apoio TIC',
    'Estrutura de Apoio'
) NOT NULL COMMENT 'Função atribuída ao professor';

-- Verificar resultado
SELECT COLUMN_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'convocatoria'
  AND COLUMN_NAME = 'funcao';
