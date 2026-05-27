-- Migration: Adicionar campo dias_gozados_anterior à tabela ferias_atribuicao
-- Data: 2026-03-11
-- Descrição: Adiciona campo para guardar o número de dias efetivamente gozados no ano anterior
--            Este campo será usado para calcular automaticamente o ajuste e preencher o PDF

-- Adicionar coluna dias_gozados_anterior
ALTER TABLE `ferias_atribuicao`
ADD COLUMN `dias_gozados_anterior` INT NOT NULL DEFAULT 0 
COMMENT 'Número de dias efetivamente gozados no ano anterior'
AFTER `dias_ajuste`;

-- Nota: O campo dias_ajuste será calculado automaticamente como:
-- dias_ajuste = dias_ano_anterior - dias_gozados_anterior
-- 
-- Exemplo:
-- Se no ano anterior tinha 22 dias e gozou 20, sobram 2 dias
-- dias_ajuste = 22 - 20 = 2 (positivo, dias por gozar)
-- 
-- Se gozou 24 dias mas só tinha 22, excedeu 2 dias
-- dias_ajuste = 22 - 24 = -2 (negativo, dias em excesso)
