-- =====================================================
-- INSERT Provas-Ensaio - Abril 2026
-- Data: 2026-02-09
-- Descrição: Inserção das provas-ensaio recalendarizadas
--            para abril de 2026 (14 a 23 de abril)
-- Fonte: Informação do Ministério da Educação (06/02/2026)
-- =====================================================

-- Verificar se o tipo 'Prova ensaio' existe no ENUM
-- Se necessário, executar primeiro: ALTER_EXAME_ADD_PROVA_ENSAIO.sql

-- 14/04/2026 - 6º ano
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`, `created_at`, `updated_at`) 
VALUES ('65', 'Inglês', 'Prova ensaio', 6, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    nome_prova = VALUES(nome_prova),
    tipo_prova = VALUES(tipo_prova),
    ano_escolaridade = VALUES(ano_escolaridade),
    updated_at = NOW();

-- 15/04/2026 - 4º ano
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`, `created_at`, `updated_at`) 
VALUES 
    ('41', 'Português', 'Prova ensaio', 4, 1, NOW(), NOW()),
    ('43', 'PLNM', 'Prova ensaio', 4, 1, NOW(), NOW()),
    ('46', 'PLNM', 'Prova ensaio', 4, 1, NOW(), NOW()),
    ('44', 'PL2', 'Prova ensaio', 4, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    nome_prova = VALUES(nome_prova),
    tipo_prova = VALUES(tipo_prova),
    ano_escolaridade = VALUES(ano_escolaridade),
    updated_at = NOW();

-- 16/04/2026 - 6º ano
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`, `created_at`, `updated_at`) 
VALUES 
    ('61', 'Português', 'Prova ensaio', 6, 1, NOW(), NOW()),
    ('63', 'PLNM', 'Prova ensaio', 6, 1, NOW(), NOW()),
    ('64', 'PLNM', 'Prova ensaio', 6, 1, NOW(), NOW()),
    ('62', 'PL2', 'Prova ensaio', 6, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    nome_prova = VALUES(nome_prova),
    tipo_prova = VALUES(tipo_prova),
    ano_escolaridade = VALUES(ano_escolaridade),
    updated_at = NOW();

-- 17/04/2026 - 4º ano
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`, `created_at`, `updated_at`) 
VALUES ('42', 'Matemática', 'Prova ensaio', 4, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    nome_prova = VALUES(nome_prova),
    tipo_prova = VALUES(tipo_prova),
    ano_escolaridade = VALUES(ano_escolaridade),
    updated_at = NOW();

-- 20/04/2026 - 6º ano
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`, `created_at`, `updated_at`) 
VALUES ('68', 'Matemática', 'Prova ensaio', 6, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    nome_prova = VALUES(nome_prova),
    tipo_prova = VALUES(tipo_prova),
    ano_escolaridade = VALUES(ano_escolaridade),
    updated_at = NOW();

-- 21/04/2026 - 9º ano
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`, `created_at`, `updated_at`) 
VALUES 
    ('91', 'Português', 'Prova ensaio', 9, 1, NOW(), NOW()),
    ('93', 'PLNM', 'Prova ensaio', 9, 1, NOW(), NOW()),
    ('94', 'PLNM', 'Prova ensaio', 9, 1, NOW(), NOW()),
    ('95', 'PL2', 'Prova ensaio', 9, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    nome_prova = VALUES(nome_prova),
    tipo_prova = VALUES(tipo_prova),
    ano_escolaridade = VALUES(ano_escolaridade),
    updated_at = NOW();

-- 23/04/2026 - 9º ano
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`, `created_at`, `updated_at`) 
VALUES ('92', 'Matemática', 'Prova ensaio', 9, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    nome_prova = VALUES(nome_prova),
    tipo_prova = VALUES(tipo_prova),
    ano_escolaridade = VALUES(ano_escolaridade),
    updated_at = NOW();

-- Verificar inserções
SELECT codigo_prova, nome_prova, tipo_prova, ano_escolaridade 
FROM exame 
WHERE tipo_prova = 'Prova ensaio' 
ORDER BY ano_escolaridade, codigo_prova;
