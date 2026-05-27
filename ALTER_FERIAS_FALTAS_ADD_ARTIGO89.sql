-- Adicionar valor 'artigo_89_ecd' ao ENUM tipo_falta da tabela ferias_faltas_desconto
-- Artigo 89.º do ECD (D.L. n.º 1/98) — Acumulação de férias
ALTER TABLE `ferias_faltas_desconto`
    MODIFY COLUMN `tipo_falta` ENUM('artigo_102', 'art_135_n4_ltfp', 'artigo_89_ecd', 'injustificada') NOT NULL;
