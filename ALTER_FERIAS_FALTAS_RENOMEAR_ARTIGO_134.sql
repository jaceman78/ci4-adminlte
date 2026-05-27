-- ============================================================
-- Renomear valor ENUM artigo_134_n3 → art_135_n4_ltfp
-- Tabela: ferias_faltas_desconto, campo: tipo_falta
-- Nova referência: art 135º, nº4 de 35/2014 LTFP
-- ============================================================

-- Passo 1: Adicionar novo valor ao ENUM (mantendo o antigo)
ALTER TABLE `ferias_faltas_desconto`
MODIFY COLUMN `tipo_falta` ENUM('artigo_102', 'artigo_134_n3', 'art_135_n4_ltfp', 'injustificada') NOT NULL
COMMENT 'Tipo de falta: artigo_102 (justificada), art_135_n4_ltfp (justificada por doença), injustificada';

-- Passo 2: Atualizar registos existentes para o novo valor
UPDATE `ferias_faltas_desconto`
SET `tipo_falta` = 'art_135_n4_ltfp'
WHERE `tipo_falta` = 'artigo_134_n3';

-- Passo 3: Remover o valor antigo do ENUM
ALTER TABLE `ferias_faltas_desconto`
MODIFY COLUMN `tipo_falta` ENUM('artigo_102', 'art_135_n4_ltfp', 'injustificada') NOT NULL
COMMENT 'Tipo de falta: artigo_102 (justificada), art_135_n4_ltfp (justificada por doença), injustificada';

-- Verificação
SELECT 'Migração concluída!' AS status;
SHOW COLUMNS FROM `ferias_faltas_desconto` WHERE Field = 'tipo_falta';
SELECT tipo_falta, COUNT(*) AS total FROM `ferias_faltas_desconto` GROUP BY tipo_falta;
