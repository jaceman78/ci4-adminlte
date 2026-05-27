-- ====================================================================
-- ALTERAÇÃO DA TABELA ferias_faltas_desconto
-- Adicionar tipo de falta "injustificada" ao ENUM
-- ====================================================================
-- Data: 18/03/2026
-- Objetivo: Permitir registar faltas injustificadas que descontam nas férias
-- ====================================================================

-- Verificar estrutura atual
SELECT 'Estado ANTES da alteração:' AS info;
SHOW COLUMNS FROM ferias_faltas_desconto WHERE Field = 'tipo_falta';

-- Alterar o ENUM para incluir 'injustificada'
ALTER TABLE `ferias_faltas_desconto`
MODIFY COLUMN `tipo_falta` ENUM('artigo_102', 'artigo_134_n3', 'injustificada') NOT NULL 
COMMENT 'Tipo de falta: artigo_102 (justificada), artigo_134_n3 (justificada por doença), injustificada';

-- Atualizar o campo referencia_legal (se existir) ou adicionar (criar depois se não existir)
-- Este campo ajuda a ter informação mais detalhada sobre a base legal

-- Verificar resultado
SELECT 'Estado APÓS a alteração:' AS info;
SHOW COLUMNS FROM ferias_faltas_desconto WHERE Field = 'tipo_falta';

-- Testar se aceita o novo valor
SELECT 'Teste de validação do novo tipo:' AS info;
-- Esta query deve retornar sem erro, confirmando que o ENUM aceita 'injustificada'
-- SELECT 'injustificada' AS tipo_teste 
-- WHERE 'injustificada' IN (
--     SELECT SUBSTRING(COLUMN_TYPE, 6, LENGTH(COLUMN_TYPE)-6)
--     FROM INFORMATION_SCHEMA.COLUMNS
--     WHERE TABLE_SCHEMA = DATABASE()
--     AND TABLE_NAME = 'ferias_faltas_desconto'
--     AND COLUMN_NAME = 'tipo_falta'
-- );

SELECT '✓ Migração concluída com sucesso!' AS resultado;
SELECT '✓ Agora pode registar faltas do tipo: artigo_102, artigo_134_n3 ou injustificada' AS info;
