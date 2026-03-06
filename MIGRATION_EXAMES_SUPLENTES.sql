-- =====================================================
-- SISTEMA DE SUPLENTES (EXAMES VIRTUAIS)
-- Data: 2026-02-12
-- Descrição: Criar exames virtuais para convocar suplentes por período
-- =====================================================

-- 1. ADICIONAR TIPO 'Suplentes' AO ENUM DA TABELA EXAME
ALTER TABLE `exame` 
MODIFY COLUMN `tipo_prova` ENUM('Exame Nacional', 'Prova Final', 'MODa', 'Suplentes') NOT NULL;

-- 2. PERMITIR ano_escolaridade NULL (para exames de suplentes)
ALTER TABLE `exame` 
MODIFY COLUMN `ano_escolaridade` INT(2) NULL DEFAULT NULL;

-- 3. CRIAR OS EXAMES DE SUPLENTES (Manhã e Tarde)
INSERT INTO `exame` (
    `codigo_prova`, 
    `nome_prova`, 
    `tipo_prova`, 
    `ano_escolaridade`, 
    `ativo`
) VALUES 
(
    'SUP-MANHA',
    'Suplentes - Período da Manhã',
    'Suplentes',
    NULL,
    1
),
(
    'SUP-TARDE',
    'Suplentes - Período da Tarde',
    'Suplentes',
    NULL,
    1
);

-- =====================================================
-- QUERIES ÚTEIS
-- =====================================================

-- Ver exames de suplentes criados
SELECT * FROM exame WHERE tipo_prova = 'Suplentes';

-- Listar todas as sessões de suplentes
SELECT 
    se.id,
    e.nome_prova,
    se.data_exame,
    se.hora_exame,
    se.duracao_minutos,
    COUNT(c.id) as num_suplentes_convocados,
    SUM(CASE WHEN c.presenca = 'Presente' THEN 1 ELSE 0 END) as suplentes_presentes
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
LEFT JOIN convocatoria c ON c.sessao_exame_id = se.id
WHERE e.tipo_prova = 'Suplentes'
GROUP BY se.id
ORDER BY se.data_exame, se.hora_exame;

-- =====================================================
-- ROLLBACK (Se necessário)
-- =====================================================
/*
-- Remover exames de suplentes
DELETE FROM exame WHERE tipo_prova = 'Suplentes';

-- Restaurar ENUM (remover 'Suplentes')
ALTER TABLE `exame` 
MODIFY COLUMN `tipo_prova` ENUM('Exame Nacional', 'Prova Final', 'MODa') NOT NULL;

-- Restaurar ano_escolaridade NOT NULL
ALTER TABLE `exame` 
MODIFY COLUMN `ano_escolaridade` INT(2) NOT NULL;
*/
