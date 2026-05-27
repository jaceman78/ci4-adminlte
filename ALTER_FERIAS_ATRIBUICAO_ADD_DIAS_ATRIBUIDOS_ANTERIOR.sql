-- ====================================================================
-- ALTERAR TABELA ferias_atribuicao - ADICIONAR dias_atribuidos_anterior
-- ====================================================================
-- Sistema: Gestão de Férias - CodeIgniter 4
-- Objetivo: Guardar quantos dias foram atribuídos ao professor no ano anterior
-- Data: 2026-03-17
-- ====================================================================

USE sistema_gestao;

-- 1. Adicionar campo dias_atribuidos_anterior
ALTER TABLE `ferias_atribuicao`
ADD COLUMN `dias_atribuidos_anterior` INT(11) NOT NULL DEFAULT 0 
COMMENT 'Número de dias que foram atribuídos ao professor no ano anterior' 
AFTER `dias_gozados_anterior`;

-- 2. Verificar estrutura
DESCRIBE `ferias_atribuicao`;

-- 3. Mostrar registos existentes (verificar que campo foi adicionado)
SELECT 
    id,
    user_nif,
    anoletivo_id,
    dias_base,
    dias_ajuste,
    dias_gozados_anterior,
    dias_atribuidos_anterior,
    dias_extra,
    dias_total
FROM `ferias_atribuicao`
LIMIT 10;

-- ====================================================================
-- NOTAS:
-- - O campo dias_atribuidos_anterior guarda quantos dias o professor
--   teve direito no ano anterior (total atribuído)
-- - Permite calcular automaticamente o dias_ajuste:
--   dias_ajuste = dias_atribuidos_anterior - dias_gozados_anterior
-- - Valor padrão 0 = sem informação do ano anterior
-- - Se houver atribuição do ano anterior, este campo será preenchido
--   automaticamente pelo sistema
-- ====================================================================
