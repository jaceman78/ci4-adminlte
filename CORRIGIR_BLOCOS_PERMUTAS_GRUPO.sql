-- =====================================================
-- Script para corrigir blocos de reposição em grupos de permutas
-- =====================================================
-- Problema: Todas as permutas de um grupo têm o mesmo bloco_reposicao_id
-- Solução: Atribuir blocos consecutivos baseado no horário original das aulas
-- =====================================================

-- Este script precisa ser executado para cada grupo de permutas
-- Substitua 'GP_XXXXXXXX' pelo ID do grupo que deseja corrigir

-- Exemplo para o grupo GP_20251121102407_69203dc79ba6a:
SET @grupo_id = 'GP_20251121102407_69203dc79ba6a';

-- Visualizar permutas do grupo antes da correção
SELECT 
    p.id,
    p.aula_original_id,
    ha.hora_inicio as hora_original,
    ha.hora_fim as hora_fim_original,
    p.bloco_reposicao_id,
    bh.designacao as bloco_atual,
    bh.hora_inicio as bloco_hora_inicio
FROM permutas p
LEFT JOIN horario_aulas ha ON ha.id_aula = p.aula_original_id
LEFT JOIN blocos_horarios bh ON bh.id_bloco = p.bloco_reposicao_id
WHERE p.grupo_permuta = @grupo_id
ORDER BY ha.hora_inicio;

-- Atualizar permutas do grupo com blocos sequenciais
-- ATENÇÃO: Este update usa variáveis MySQL para gerar sequência
SET @bloco_atual = 0;
SET @grupo_id = 'GP_20251121102407_69203dc79ba6a';

-- Primeiro, obter o bloco inicial (menor bloco_reposicao_id do grupo)
SET @bloco_inicial = (
    SELECT MIN(bloco_reposicao_id) 
    FROM permutas 
    WHERE grupo_permuta = @grupo_id
);

-- Criar tabela temporária com a ordem correta
DROP TEMPORARY TABLE IF EXISTS temp_permutas_ordem;
CREATE TEMPORARY TABLE temp_permutas_ordem AS
SELECT 
    p.id as permuta_id,
    p.bloco_reposicao_id as bloco_antigo,
    (@bloco_inicial + (@row_num := @row_num + 1) - 1) as novo_bloco
FROM permutas p
JOIN horario_aulas ha ON ha.id_aula = p.aula_original_id
CROSS JOIN (SELECT @row_num := 0) r
WHERE p.grupo_permuta = @grupo_id
ORDER BY ha.hora_inicio;

-- Visualizar o que será atualizado
SELECT 
    t.permuta_id,
    t.bloco_antigo,
    bh_antigo.designacao as bloco_antigo_nome,
    t.novo_bloco,
    bh_novo.designacao as novo_bloco_nome,
    bh_novo.hora_inicio as nova_hora_inicio,
    bh_novo.hora_fim as nova_hora_fim
FROM temp_permutas_ordem t
LEFT JOIN blocos_horarios bh_antigo ON bh_antigo.id_bloco = t.bloco_antigo
LEFT JOIN blocos_horarios bh_novo ON bh_novo.id_bloco = t.novo_bloco
ORDER BY t.permuta_id;

-- DESCOMENTE A LINHA ABAIXO PARA EXECUTAR A CORREÇÃO
-- UPDATE permutas p
-- JOIN temp_permutas_ordem t ON t.permuta_id = p.id
-- SET p.bloco_reposicao_id = t.novo_bloco
-- WHERE p.grupo_permuta = @grupo_id;

-- Verificar após a atualização
-- SELECT 
--     p.id,
--     p.aula_original_id,
--     ha.hora_inicio as hora_original,
--     p.bloco_reposicao_id,
--     bh.designacao as bloco_reposicao,
--     bh.hora_inicio as bloco_hora_inicio,
--     bh.hora_fim as bloco_hora_fim
-- FROM permutas p
-- LEFT JOIN horario_aulas ha ON ha.id_aula = p.aula_original_id
-- LEFT JOIN blocos_horarios bh ON bh.id_bloco = p.bloco_reposicao_id
-- WHERE p.grupo_permuta = @grupo_id
-- ORDER BY ha.hora_inicio;

-- Limpar tabela temporária
DROP TEMPORARY TABLE IF EXISTS temp_permutas_ordem;

-- =====================================================
-- CORREÇÃO AUTOMÁTICA PARA TODOS OS GRUPOS
-- =====================================================
-- Este procedimento corrige TODOS os grupos de permutas de uma vez
-- Use com cuidado!

DELIMITER $$

DROP PROCEDURE IF EXISTS corrigir_todos_grupos_permutas$$

CREATE PROCEDURE corrigir_todos_grupos_permutas()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_grupo VARCHAR(100);
    DECLARE grupo_cursor CURSOR FOR 
        SELECT DISTINCT grupo_permuta 
        FROM permutas 
        WHERE grupo_permuta IS NOT NULL 
        AND grupo_permuta != '';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    OPEN grupo_cursor;

    read_loop: LOOP
        FETCH grupo_cursor INTO v_grupo;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Para cada grupo, atribuir blocos sequenciais
        SET @bloco_inicial = (
            SELECT MIN(bloco_reposicao_id) 
            FROM permutas 
            WHERE grupo_permuta = v_grupo
        );

        SET @row_num = 0;
        
        DROP TEMPORARY TABLE IF EXISTS temp_permutas_ordem;
        CREATE TEMPORARY TABLE temp_permutas_ordem AS
        SELECT 
            p.id as permuta_id,
            (@bloco_inicial + (@row_num := @row_num + 1) - 1) as novo_bloco
        FROM permutas p
        JOIN horario_aulas ha ON ha.id_aula = p.aula_original_id
        WHERE p.grupo_permuta = v_grupo
        ORDER BY ha.hora_inicio;

        UPDATE permutas p
        JOIN temp_permutas_ordem t ON t.permuta_id = p.id
        SET p.bloco_reposicao_id = t.novo_bloco
        WHERE p.grupo_permuta = v_grupo;

        DROP TEMPORARY TABLE IF EXISTS temp_permutas_ordem;

    END LOOP;

    CLOSE grupo_cursor;
    
    SELECT CONCAT('Correção concluída para todos os grupos de permutas') as resultado;
END$$

DELIMITER ;

-- Para executar a correção automática de todos os grupos:
-- CALL corrigir_todos_grupos_permutas();
