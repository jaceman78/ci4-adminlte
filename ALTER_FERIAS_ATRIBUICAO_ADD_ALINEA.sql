-- Adicionar coluna 'alinea' à tabela ferias_atribuicao
-- Valores possíveis: NULL (professor com férias normais), 'a' a 'f' (situação especial)
-- a) Junta Médica
-- b) Mobilidade Especial
-- c) Em Mobilidade
-- d) Licença s/ vencimento
-- e) Licença ao abrigo do artigo 37.º da Lei n.º 7/2009
-- f) Licença ao abrigo do Art.º53 da Lei nº90/2019

ALTER TABLE `ferias_atribuicao`
ADD COLUMN `alinea` ENUM('a','b','c','d','e','f') NULL DEFAULT NULL
    COMMENT 'Situção especial: a=Junta Médica, b=Mobilidade Especial, c=Em Mobilidade, d=Licença s/vencimento, e=Lic. art.37 Lei7/2009, f=Lic. art.53 Lei90/2019'
AFTER `observacoes`;
