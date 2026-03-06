-- =====================================================
-- TESTE DO SISTEMA DE SUPLENTES
-- Data: 2026-02-12
-- =====================================================

-- 1. CRIAR SESSÃO DE SUPLENTES PARA TESTE
-- Exemplo: 16 de Junho de 2026 - Manhã
INSERT INTO `sessao_exame` (
    `exame_id`,
    `fase`,
    `data_exame`,
    `hora_exame`,
    `duracao_minutos`,
    `tolerancia_minutos`,
    `num_alunos`,
    `observacoes`,
    `ativo`
) VALUES (
    (SELECT id FROM exame WHERE codigo_prova = 'SUP-MANHA'),
    'Época Especial',
    '2026-06-16',
    '08:30:00',
    300, -- 5 horas
    30,
    NULL, -- Sem alunos para suplentes
    'Suplentes para período da manhã - Várias provas (Alemão, Francês, Espanhol)',
    1
);

-- Obter ID da sessão criada
SET @sessao_id = LAST_INSERT_ID();
SELECT @sessao_id AS 'ID da Sessão de Suplentes Criada';

-- 2. ALOCAR SALA DE ESPERA (Exemplo: Sala dos Professores)
-- Nota: Ajustar sala_id conforme a sala disponível no sistema
/*
INSERT INTO `sessao_exame_sala` (
    `sessao_exame_id`,
    `sala_id`,
    `num_alunos_sala`
) VALUES (
    @sessao_id,
    1, -- Ajustar para ID da sala de espera real
    0  -- Sem alunos para suplentes
);
*/

-- 3. VERIFICAR SESSÃO CRIADA
SELECT 
    se.id,
    e.codigo_prova,
    e.nome_prova,
    e.tipo_prova,
    se.fase,
    se.data_exame,
    se.hora_exame,
    se.duracao_minutos,
    se.num_alunos,
    se.observacoes
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
WHERE e.tipo_prova = 'Suplentes'
ORDER BY se.data_exame, se.hora_exame;

-- 4. SIMULAR CONVOCAÇÃO DE SUPLENTES (Adicionar manualmente via interface)
-- A convocação será feita através da interface de Convocatórias
-- Pode adicionar/remover professores livremente

--5. ESTATÍSTICAS DE SUPLENTES
SELECT 
    e.nome_prova,
    se.data_exame,
    COUNT(DISTINCT c.id) as total_convocados,
    SUM(CASE WHEN c.presenca = 'Presente' THEN 1 ELSE 0 END) as total_presentes,
    SUM(CASE WHEN c.presenca = 'Ausente' THEN 1 ELSE 0 END) as total_ausentes
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
LEFT JOIN convocatoria c ON c.sessao_exame_id = se.id
WHERE e.tipo_prova = 'Suplentes'
GROUP BY e.nome_prova, se.data_exame
ORDER BY se.data_exame;

-- =====================================================
-- LIMPEZA (Se necessário remover dados de teste)
-- =====================================================
/*
-- Remover convocatórias da sessão de teste
DELETE FROM convocatoria 
WHERE sessao_exame_id IN (
    SELECT se.id FROM sessao_exame se
    JOIN exame e ON e.id = se.exame_id
    WHERE e.tipo_prova = 'Suplentes'
);

-- Remover alocações de sala da sessão de teste
DELETE FROM sessao_exame_sala 
WHERE sessao_exame_id IN (
    SELECT se.id FROM sessao_exame se
    JOIN exame e ON e.id = se.exame_id
    WHERE e.tipo_prova = 'Suplentes'
);

-- Remover sessão de teste
DELETE FROM sessao_exame 
WHERE exame_id IN (
    SELECT id FROM exame WHERE tipo_prova = 'Suplentes'
);
*/
