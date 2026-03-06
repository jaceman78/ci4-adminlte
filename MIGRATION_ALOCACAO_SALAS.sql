-- ============================================
-- SCRIPT DE MIGRAÇÃO: Alocação de Salas
-- Data: 31 Janeiro 2026
-- ============================================

-- ============================================
-- 1. CRIAR TABELA sessao_exame_sala
-- ============================================

CREATE TABLE IF NOT EXISTS `sessao_exame_sala` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `sessao_exame_id` INT UNSIGNED NOT NULL COMMENT 'FK para sessao_exame.id - Sessão de exame',
    `sala_id` INT UNSIGNED NOT NULL COMMENT 'FK para salas.id - Sala alocada',
    `num_alunos_sala` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Número de alunos nesta sala específica',
    `vigilantes_necessarios` TINYINT UNSIGNED NOT NULL DEFAULT 2 COMMENT 'Número de vigilantes necessários (2 por sala, exceto MODa)',
    `observacoes` TEXT NULL COMMENT 'Observações sobre a sala',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    `deleted_at` DATETIME NULL,
    
    KEY `idx_sessao_exame_id` (`sessao_exame_id`),
    KEY `idx_sala_id` (`sala_id`),
    
    CONSTRAINT `fk_sessao_exame_sala_sessao` 
        FOREIGN KEY (`sessao_exame_id`) 
        REFERENCES `sessao_exame`(`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    CONSTRAINT `fk_sessao_exame_sala_sala` 
        FOREIGN KEY (`sala_id`) 
        REFERENCES `salas`(`id`) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    CONSTRAINT `unique_sessao_sala` 
        UNIQUE KEY (`sessao_exame_id`, `sala_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Alocação de salas para sessões de exame';

-- ============================================
-- 2. ALTERAR TABELA convocatoria
-- ============================================

-- 2.1 Remover foreign key antiga
ALTER TABLE `convocatoria` 
DROP FOREIGN KEY IF EXISTS `convocatoria_sala_id_foreign`;

-- 2.2 Remover coluna sala_id
ALTER TABLE `convocatoria` 
DROP COLUMN IF EXISTS `sala_id`;

-- 2.3 Adicionar nova coluna sessao_exame_sala_id
ALTER TABLE `convocatoria` 
ADD COLUMN `sessao_exame_sala_id` INT UNSIGNED NULL 
    COMMENT 'FK para sessao_exame_sala.id - Sala específica (NULL para Suplentes/Júri)' 
    AFTER `sessao_exame_id`;

-- 2.4 Adicionar índice
ALTER TABLE `convocatoria` 
ADD KEY `idx_sessao_exame_sala` (`sessao_exame_sala_id`);

-- 2.5 Adicionar foreign key
ALTER TABLE `convocatoria` 
ADD CONSTRAINT `fk_convocatoria_sessao_exame_sala` 
    FOREIGN KEY (`sessao_exame_sala_id`) 
    REFERENCES `sessao_exame_sala`(`id`) 
    ON DELETE RESTRICT 
    ON UPDATE CASCADE;

-- ============================================
-- 3. VERIFICAÇÃO (Executar após migração)
-- ============================================

-- Ver estrutura da nova tabela
DESCRIBE sessao_exame_sala;

-- Ver estrutura atualizada de convocatoria
DESCRIBE convocatoria;

-- Ver foreign keys
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME IN ('sessao_exame_sala', 'convocatoria')
    AND REFERENCED_TABLE_NAME IS NOT NULL;

-- ============================================
-- 4. ROLLBACK (Se necessário)
-- ============================================

/*
-- Remover foreign key nova
ALTER TABLE `convocatoria` DROP FOREIGN KEY `fk_convocatoria_sessao_exame_sala`;

-- Remover coluna nova
ALTER TABLE `convocatoria` DROP COLUMN `sessao_exame_sala_id`;

-- Recriar coluna antiga sala_id
ALTER TABLE `convocatoria` 
ADD COLUMN `sala_id` INT UNSIGNED NULL 
    COMMENT 'FK para salas.id - Sala (NULL para Suplentes)' 
    AFTER `user_id`;

-- Recriar foreign key antiga
ALTER TABLE `convocatoria` 
ADD CONSTRAINT `convocatoria_sala_id_foreign` 
    FOREIGN KEY (`sala_id`) 
    REFERENCES `salas`(`id`) 
    ON DELETE RESTRICT 
    ON UPDATE CASCADE;

-- Remover tabela sessao_exame_sala
DROP TABLE IF EXISTS `sessao_exame_sala`;
*/

-- ============================================
-- 5. DADOS DE TESTE (Opcional)
-- ============================================

-- Exemplo: Alocar 3 salas para a sessão de exame ID 1
/*
INSERT INTO sessao_exame_sala (sessao_exame_id, sala_id, num_alunos_sala, vigilantes_necessarios, created_at)
VALUES 
    (1, 1, 40, 2, NOW()),  -- Sala 1: 40 alunos, 2 vigilantes
    (1, 2, 38, 2, NOW()),  -- Sala 2: 38 alunos, 2 vigilantes
    (1, 3, 35, 2, NOW());  -- Sala 3: 35 alunos, 2 vigilantes

-- Criar convocatórias para as salas alocadas
INSERT INTO convocatoria (sessao_exame_id, sessao_exame_sala_id, user_id, funcao, estado_confirmacao, created_at)
VALUES 
    (1, 1, 10, 'Vigilante', 'Pendente', NOW()),  -- Sala 1, Vigilante 1
    (1, 1, 11, 'Vigilante', 'Pendente', NOW()),  -- Sala 1, Vigilante 2
    (1, 2, 12, 'Vigilante', 'Pendente', NOW()),  -- Sala 2, Vigilante 1
    (1, 2, 13, 'Vigilante', 'Pendente', NOW()),  -- Sala 2, Vigilante 2
    (1, NULL, 14, 'Suplente', 'Pendente', NOW()); -- Suplente (sem sala específica)
*/

-- ============================================
-- 6. QUERIES ÚTEIS APÓS MIGRAÇÃO
-- ============================================

-- Ver todas as salas alocadas com estatísticas
SELECT 
    se.id AS sessao_id,
    e.codigo_prova,
    e.nome_prova,
    se.data_exame,
    s.nome AS sala,
    ses.num_alunos_sala,
    s.capacidade AS sala_capacidade,
    ses.vigilantes_necessarios,
    COUNT(c.id) AS vigilantes_alocados,
    (ses.vigilantes_necessarios - COUNT(c.id)) AS vigilantes_em_falta
FROM sessao_exame_sala ses
JOIN sessao_exame se ON se.id = ses.sessao_exame_id
JOIN exame e ON e.id = se.exame_id
JOIN salas s ON s.id = ses.sala_id
LEFT JOIN convocatoria c ON c.sessao_exame_sala_id = ses.id AND c.funcao = 'Vigilante'
GROUP BY ses.id
ORDER BY se.data_exame, s.nome;

-- Ver convocatórias por sala
SELECT 
    s.nome AS sala,
    u.name AS professor,
    c.funcao,
    c.estado_confirmacao
FROM convocatoria c
LEFT JOIN sessao_exame_sala ses ON ses.id = c.sessao_exame_sala_id
LEFT JOIN salas s ON s.id = ses.sala_id
JOIN user u ON u.id = c.user_id
WHERE c.sessao_exame_id = 1  -- Mudar para ID da sessão desejada
ORDER BY COALESCE(s.nome, 'ZZZ_Sem_Sala'), c.funcao;

-- Estatísticas gerais de uma sessão
SELECT 
    se.id,
    e.codigo_prova,
    e.nome_prova,
    se.data_exame,
    COUNT(DISTINCT ses.id) AS total_salas,
    SUM(ses.num_alunos_sala) AS total_alunos,
    SUM(ses.vigilantes_necessarios) AS vigilantes_necessarios,
    COUNT(DISTINCT CASE WHEN c.funcao = 'Vigilante' THEN c.id END) AS vigilantes_alocados
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
LEFT JOIN sessao_exame_sala ses ON ses.sessao_exame_id = se.id
LEFT JOIN convocatoria c ON c.sessao_exame_sala_id = ses.id
WHERE se.id = 1  -- Mudar para ID da sessão desejada
GROUP BY se.id;

-- ============================================
-- FIM DO SCRIPT
-- ============================================
