-- =====================================================
-- CORREÇÃO: Permutas duplicadas para o mesmo dia/hora
-- Data: 2026-02-12
-- Problema: Substituto aceitou múltiplas permutas para o mesmo dia e hora
-- =====================================================

-- 1. IDENTIFICAR PERMUTAS DUPLICADAS
-- Mostrar todas as permutas aceites onde o mesmo substituto tem múltiplas para o mesmo dia/hora
SELECT 
    pv.user_substituto_id,
    u.name AS nome_substituto,
    se.data_exame,
    se.hora_exame,
    COUNT(*) AS num_permutas,
    GROUP_CONCAT(pv.id ORDER BY pv.criado_em DESC) AS permuta_ids,
    GROUP_CONCAT(pv.convocatoria_id ORDER BY pv.criado_em DESC) AS convocatoria_ids,
    GROUP_CONCAT(pv.estado ORDER BY pv.criado_em DESC) AS estados
FROM permutas_vigilancia pv
JOIN convocatoria c ON c.id = pv.convocatoria_id
JOIN sessao_exame se ON se.id = c.sessao_exame_id
JOIN user u ON u.id = pv.user_substituto_id
WHERE pv.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
GROUP BY pv.user_substituto_id, se.data_exame, se.hora_exame
HAVING COUNT(*) > 1;

-- 2. CORRIGIR AUTOMATICAMENTE
-- Para cada caso duplicado, manter apenas a permuta mais recente e cancelar as outras

-- ATENÇÃO: Este script vai CANCELAR automaticamente as permutas mais antigas!
-- Execute apenas se tiver certeza!

-- Descomente as linhas abaixo para executar a correção:

/*
-- Cancelar permutas duplicadas (mantendo apenas a mais recente)
UPDATE permutas_vigilancia pv
JOIN (
    -- Subquery: Identificar permutas que NÃO são as mais recentes
    SELECT pv2.id
    FROM permutas_vigilancia pv2
    JOIN convocatoria c2 ON c2.id = pv2.convocatoria_id
    JOIN sessao_exame se2 ON se2.id = c2.sessao_exame_id
    WHERE pv2.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
    AND EXISTS (
        -- Verificar se existe outra permuta mais recente do mesmo substituto para mesmo dia/hora
        SELECT 1
        FROM permutas_vigilancia pv3
        JOIN convocatoria c3 ON c3.id = pv3.convocatoria_id
        JOIN sessao_exame se3 ON se3.id = c3.sessao_exame_id
        WHERE pv3.user_substituto_id = pv2.user_substituto_id
        AND se3.data_exame = se2.data_exame
        AND se3.hora_exame = se2.hora_exame
        AND pv3.id != pv2.id
        AND pv3.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
        AND pv3.criado_em > pv2.criado_em  -- Mais recente
    )
) duplicadas ON duplicadas.id = pv.id
SET pv.estado = 'CANCELADO',
    pv.observacoes = CONCAT(
        COALESCE(pv.observacoes, ''), 
        '\n[AUTO] Permuta cancelada automaticamente em 2026-02-12 por conflito de horário.'
    );
*/

-- 3. VERIFICAÇÃO FINAL
-- Verificar se ainda existem duplicados após correção
SELECT 
    'Verificação após correção' AS status,
    CASE 
        WHEN COUNT(*) = 0 THEN '✅ Sem conflitos de horário'
        ELSE CONCAT('⚠️ ', COUNT(*), ' conflito(s) encontrado(s)')
    END AS resultado
FROM (
    SELECT 
        pv.user_substituto_id,
        se.data_exame,
        se.hora_exame,
        COUNT(*) AS num_permutas
    FROM permutas_vigilancia pv
    JOIN convocatoria c ON c.id = pv.convocatoria_id
    JOIN sessao_exame se ON se.id = c.sessao_exame_id
    WHERE pv.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
    GROUP BY pv.user_substituto_id, se.data_exame, se.hora_exame
    HAVING COUNT(*) > 1
) conflitos;

-- 4. CASO ESPECÍFICO: António Neto (ID 1)
-- Verificar as permutas específicas identificadas
SELECT 
    pv.id,
    pv.convocatoria_id,
    pv.estado,
    se.data_exame,
    se.hora_exame,
    ex.codigo_prova,
    pv.criado_em,
    CASE 
        WHEN pv.criado_em = (
            SELECT MAX(pv2.criado_em)
            FROM permutas_vigilancia pv2
            JOIN convocatoria c2 ON c2.id = pv2.convocatoria_id
            JOIN sessao_exame se2 ON se2.id = c2.sessao_exame_id
            WHERE pv2.user_substituto_id = 1
            AND se2.data_exame = '2026-07-24'
            AND se2.hora_exame = '09:30:00'
            AND pv2.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
        ) THEN '✅ MANTER'
        ELSE '❌ CANCELAR'
    END AS acao
FROM permutas_vigilancia pv
JOIN convocatoria c ON c.id = pv.convocatoria_id
JOIN sessao_exame se ON se.id = c.sessao_exame_id
JOIN exame ex ON ex.id = se.exame_id
WHERE pv.user_substituto_id = 1
AND se.data_exame = '2026-07-24'
AND se.hora_exame = '09:30:00'
AND pv.estado IN ('ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO')
ORDER BY pv.criado_em DESC;
