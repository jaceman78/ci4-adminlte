-- =====================================================
-- QUERIES ÚTEIS - SISTEMA DE CONVOCATÓRIAS PARA EXAMES
-- =====================================================

-- 1. LISTAR TODAS AS SESSÕES DE EXAME COM INFORMAÇÃO COMPLETA
SELECT 
    se.id,
    e.codigo_prova,
    e.nome_prova,
    e.tipo_prova,
    e.ano_escolaridade,
    se.fase,
    DATE_FORMAT(se.data_exame, '%d/%m/%Y') as data_formatada,
    DATE_FORMAT(se.hora_exame, '%H:%i') as hora_formatada,
    se.duracao_minutos,
    se.tolerancia_minutos,
    se.num_alunos,
    ADDTIME(se.hora_exame, SEC_TO_TIME((se.duracao_minutos + se.tolerancia_minutos) * 60)) as hora_fim,
    (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id) as total_convocados,
    (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id AND estado_confirmacao = 'Confirmado') as confirmados,
    (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id AND estado_confirmacao = 'Pendente') as pendentes
FROM sessao_exame se
INNER JOIN exame e ON e.id = se.exame_id
WHERE se.ativo = 1
ORDER BY se.data_exame ASC, se.hora_exame ASC;


-- 2. LISTAR PRÓXIMAS SESSÕES (PRÓXIMOS 30 DIAS)
SELECT 
    se.id,
    e.codigo_prova,
    e.nome_prova,
    se.fase,
    DATE_FORMAT(se.data_exame, '%d/%m/%Y') as data,
    DATE_FORMAT(se.hora_exame, '%H:%i') as hora,
    se.num_alunos,
    (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id) as convocados
FROM sessao_exame se
INNER JOIN exame e ON e.id = se.exame_id
WHERE se.ativo = 1 
  AND se.data_exame BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
ORDER BY se.data_exame ASC, se.hora_exame ASC;


-- 3. MAPA DE VIGILÂNCIA DE UMA SESSÃO ESPECÍFICA
SELECT 
    c.funcao,
    u.name as professor,
    u.telefone,
    u.email,
    COALESCE(s.codigo_sala, 'Suplente') as sala,
    s.descricao as sala_descricao,
    c.estado_confirmacao,
    DATE_FORMAT(c.data_confirmacao, '%d/%m/%Y %H:%i') as data_confirmacao,
    c.observacoes
FROM convocatoria c
INNER JOIN user u ON u.id = c.user_id
LEFT JOIN salas s ON s.id = c.sala_id
WHERE c.sessao_exame_id = 1  -- SUBSTITUIR pelo ID da sessão
ORDER BY 
    FIELD(c.funcao, 'Vigilante', 'Coadjuvante', 'Júri', 'Verificar Calculadoras', 'Apoio TIC', 'Suplente'),
    u.name ASC;


-- 4. CONVOCATÓRIAS PENDENTES DE CONFIRMAÇÃO
SELECT 
    c.id,
    u.name as professor,
    u.email,
    u.telefone,
    e.nome_prova,
    se.fase,
    DATE_FORMAT(se.data_exame, '%d/%m/%Y') as data,
    DATE_FORMAT(se.hora_exame, '%H:%i') as hora,
    c.funcao,
    COALESCE(s.codigo_sala, 'Suplente') as sala,
    DATEDIFF(se.data_exame, CURDATE()) as dias_ate_exame
FROM convocatoria c
INNER JOIN user u ON u.id = c.user_id
INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
INNER JOIN exame e ON e.id = se.exame_id
LEFT JOIN salas s ON s.id = c.sala_id
WHERE c.estado_confirmacao = 'Pendente'
  AND se.ativo = 1
  AND se.data_exame >= CURDATE()
ORDER BY se.data_exame ASC, u.name ASC;


-- 5. PROFESSORES COM MAIS CONVOCATÓRIAS
SELECT 
    u.id,
    u.name as professor,
    u.email,
    COUNT(*) as total_convocatorias,
    SUM(CASE WHEN c.estado_confirmacao = 'Confirmado' THEN 1 ELSE 0 END) as confirmadas,
    SUM(CASE WHEN c.estado_confirmacao = 'Pendente' THEN 1 ELSE 0 END) as pendentes,
    SUM(CASE WHEN c.estado_confirmacao = 'Rejeitado' THEN 1 ELSE 0 END) as rejeitadas
FROM convocatoria c
INNER JOIN user u ON u.id = c.user_id
INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
WHERE se.ativo = 1
  AND se.data_exame >= CURDATE()
GROUP BY u.id, u.name, u.email
ORDER BY total_convocatorias DESC;


-- 6. CONVOCATÓRIAS DE UM PROFESSOR ESPECÍFICO
SELECT 
    c.id,
    e.codigo_prova,
    e.nome_prova,
    e.tipo_prova,
    se.fase,
    DATE_FORMAT(se.data_exame, '%d/%m/%Y') as data,
    DATE_FORMAT(se.hora_exame, '%H:%i') as hora,
    se.duracao_minutos,
    c.funcao,
    COALESCE(s.codigo_sala, 'Suplente') as sala,
    c.estado_confirmacao,
    c.observacoes
FROM convocatoria c
INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
INNER JOIN exame e ON e.id = se.exame_id
LEFT JOIN salas s ON s.id = c.sala_id
WHERE c.user_id = 15  -- SUBSTITUIR pelo ID do professor
  AND se.ativo = 1
  AND se.data_exame >= CURDATE()
ORDER BY se.data_exame ASC, se.hora_exame ASC;


-- 7. SESSÕES COM FALTA DE VIGILANTES
SELECT 
    se.id,
    e.codigo_prova,
    e.nome_prova,
    DATE_FORMAT(se.data_exame, '%d/%m/%Y') as data,
    se.num_alunos,
    CEILING(se.num_alunos / 20) as vigilantes_necessarios,
    (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id AND funcao = 'Vigilante') as vigilantes_atribuidos,
    (CEILING(se.num_alunos / 20) - (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id AND funcao = 'Vigilante')) as em_falta
FROM sessao_exame se
INNER JOIN exame e ON e.id = se.exame_id
WHERE se.ativo = 1
  AND se.data_exame >= CURDATE()
  AND se.num_alunos IS NOT NULL
HAVING em_falta > 0
ORDER BY se.data_exame ASC;


-- 8. CONFLITOS DE HORÁRIO (PROFESSORES CONVOCADOS PARA DUAS SESSÕES NO MESMO HORÁRIO)
SELECT 
    u.name as professor,
    DATE_FORMAT(se1.data_exame, '%d/%m/%Y') as data,
    e1.nome_prova as exame1,
    DATE_FORMAT(se1.hora_exame, '%H:%i') as hora1,
    e2.nome_prova as exame2,
    DATE_FORMAT(se2.hora_exame, '%H:%i') as hora2
FROM convocatoria c1
INNER JOIN convocatoria c2 ON c1.user_id = c2.user_id AND c1.id < c2.id
INNER JOIN sessao_exame se1 ON se1.id = c1.sessao_exame_id
INNER JOIN sessao_exame se2 ON se2.id = c2.sessao_exame_id
INNER JOIN exame e1 ON e1.id = se1.exame_id
INNER JOIN exame e2 ON e2.id = se2.exame_id
INNER JOIN user u ON u.id = c1.user_id
WHERE se1.data_exame = se2.data_exame
  AND se1.ativo = 1
  AND se2.ativo = 1
  AND (
    -- Verifica sobreposição de horários
    (se1.hora_exame <= se2.hora_exame 
     AND ADDTIME(se1.hora_exame, SEC_TO_TIME((se1.duracao_minutos + se1.tolerancia_minutos) * 60)) > se2.hora_exame)
    OR
    (se2.hora_exame <= se1.hora_exame 
     AND ADDTIME(se2.hora_exame, SEC_TO_TIME((se2.duracao_minutos + se2.tolerancia_minutos) * 60)) > se1.hora_exame)
  );


-- 9. ESTATÍSTICAS POR TIPO DE EXAME
SELECT 
    e.tipo_prova,
    COUNT(DISTINCT e.id) as total_exames,
    COUNT(DISTINCT se.id) as total_sessoes,
    COUNT(c.id) as total_convocatorias,
    SUM(CASE WHEN c.estado_confirmacao = 'Confirmado' THEN 1 ELSE 0 END) as confirmadas,
    SUM(CASE WHEN c.estado_confirmacao = 'Pendente' THEN 1 ELSE 0 END) as pendentes,
    SUM(CASE WHEN c.estado_confirmacao = 'Rejeitado' THEN 1 ELSE 0 END) as rejeitadas
FROM exame e
LEFT JOIN sessao_exame se ON se.exame_id = e.id AND se.ativo = 1
LEFT JOIN convocatoria c ON c.sessao_exame_id = se.id
WHERE e.ativo = 1
GROUP BY e.tipo_prova
ORDER BY e.tipo_prova;


-- 10. CALENDÁRIO MENSAL DE EXAMES
SELECT 
    DATE_FORMAT(se.data_exame, '%Y-%m') as mes,
    COUNT(DISTINCT se.id) as total_sessoes,
    COUNT(DISTINCT se.data_exame) as dias_com_exames,
    SUM(se.num_alunos) as total_alunos,
    COUNT(c.id) as total_convocatorias
FROM sessao_exame se
LEFT JOIN convocatoria c ON c.sessao_exame_id = se.id
WHERE se.ativo = 1
  AND se.data_exame >= CURDATE()
GROUP BY DATE_FORMAT(se.data_exame, '%Y-%m')
ORDER BY mes ASC;


-- 11. PROFESSORES DISPONÍVEIS PARA UMA DATA/HORA ESPECÍFICA
-- (Professores que NÃO têm convocatória nesse horário)
SELECT 
    u.id,
    u.name,
    u.email,
    u.telefone
FROM user u
WHERE u.status = 1
  AND u.level > 0  -- Apenas professores
  AND u.id NOT IN (
    SELECT c.user_id
    FROM convocatoria c
    INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
    WHERE se.data_exame = '2026-06-18'  -- SUBSTITUIR pela data
      AND se.hora_exame = '09:30:00'    -- SUBSTITUIR pela hora
      AND se.ativo = 1
  )
ORDER BY u.name ASC;


-- 12. RESUMO DIÁRIO DE EXAMES
SELECT 
    DATE_FORMAT(se.data_exame, '%d/%m/%Y') as data,
    DAYNAME(se.data_exame) as dia_semana,
    COUNT(DISTINCT se.id) as num_sessoes,
    COUNT(DISTINCT e.id) as num_exames_diferentes,
    SUM(se.num_alunos) as total_alunos,
    GROUP_CONCAT(DISTINCT e.nome_prova ORDER BY se.hora_exame SEPARATOR ' | ') as exames
FROM sessao_exame se
INNER JOIN exame e ON e.id = se.exame_id
WHERE se.ativo = 1
  AND se.data_exame BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 60 DAY)
GROUP BY se.data_exame
ORDER BY se.data_exame ASC;


-- 13. TAXA DE CONFIRMAÇÃO POR FUNÇÃO
SELECT 
    c.funcao,
    COUNT(*) as total,
    SUM(CASE WHEN c.estado_confirmacao = 'Confirmado' THEN 1 ELSE 0 END) as confirmadas,
    SUM(CASE WHEN c.estado_confirmacao = 'Pendente' THEN 1 ELSE 0 END) as pendentes,
    SUM(CASE WHEN c.estado_confirmacao = 'Rejeitado' THEN 1 ELSE 0 END) as rejeitadas,
    ROUND((SUM(CASE WHEN c.estado_confirmacao = 'Confirmado' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as taxa_confirmacao
FROM convocatoria c
INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
WHERE se.ativo = 1
  AND se.data_exame >= CURDATE()
GROUP BY c.funcao
ORDER BY c.funcao;


-- 14. ALERTAS: SESSÕES SEM CONVOCATÓRIAS A MENOS DE 7 DIAS
SELECT 
    se.id,
    e.codigo_prova,
    e.nome_prova,
    DATE_FORMAT(se.data_exame, '%d/%m/%Y') as data,
    DATE_FORMAT(se.hora_exame, '%H:%i') as hora,
    DATEDIFF(se.data_exame, CURDATE()) as dias_faltam,
    (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id) as total_convocatorias
FROM sessao_exame se
INNER JOIN exame e ON e.id = se.exame_id
WHERE se.ativo = 1
  AND se.data_exame BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
  AND (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id) = 0
ORDER BY se.data_exame ASC;


-- 15. EXPORT CSV - MAPA DE VIGILÂNCIA COMPLETO
SELECT 
    DATE_FORMAT(se.data_exame, '%d/%m/%Y') as 'Data',
    DATE_FORMAT(se.hora_exame, '%H:%i') as 'Hora',
    e.codigo_prova as 'Código',
    e.nome_prova as 'Exame',
    se.fase as 'Fase',
    u.name as 'Professor',
    c.funcao as 'Função',
    COALESCE(s.codigo_sala, 'Suplente') as 'Sala',
    c.estado_confirmacao as 'Estado',
    u.telefone as 'Telefone',
    u.email as 'Email'
FROM convocatoria c
INNER JOIN user u ON u.id = c.user_id
INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
INNER JOIN exame e ON e.id = se.exame_id
LEFT JOIN salas s ON s.id = c.sala_id
WHERE se.ativo = 1
  AND se.data_exame >= CURDATE()
ORDER BY se.data_exame ASC, se.hora_exame ASC, c.funcao ASC;

-- =====================================================
-- FIM DAS QUERIES ÚTEIS
-- =====================================================
