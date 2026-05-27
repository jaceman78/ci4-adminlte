-- ====================================================================
-- CRIAR TABELA categoria_professores
-- ====================================================================
-- Sistema: Gestão Escolar - CodeIgniter 4
-- Objetivo: Modularizar as categorias de professores
-- Data: 2026-03-13
-- ====================================================================

USE sistema_gestao;

-- 1. Criar tabela categoria_professores
CREATE TABLE IF NOT EXISTS `categoria_professores` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(150) NOT NULL COMMENT 'Nome da categoria',
    `codigo` VARCHAR(20) NOT NULL COMMENT 'Código identificador da categoria',
    `tipo_ensino` ENUM('2º e 3º Ciclos e Sec.', '1º Ciclo', 'Educação Infantil', 'Educação Especial', 'Outra') NOT NULL COMMENT 'Tipo de ensino',
    `tipo_quadro` ENUM('Quadro Escola', 'QZP', 'Quadro Agrupamento', 'Contratado', 'Estagiário', 'Outra') NOT NULL COMMENT 'Tipo de quadro',
    `tipo_nomeacao` ENUM('Nomeação Definitiva', 'Nomeação Provisória', 'Contratado', 'Estagiário', 'N/A') NOT NULL DEFAULT 'N/A' COMMENT 'Tipo de nomeação',
    `ordem` INT(11) NOT NULL DEFAULT 0 COMMENT 'Ordem de exibição',
    `ativo` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_codigo` (`codigo`),
    KEY `idx_tipo_ensino` (`tipo_ensino`),
    KEY `idx_tipo_quadro` (`tipo_quadro`),
    KEY `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci 
COMMENT='Tabela de categorias de professores';

-- 2. Inserir as categorias solicitadas
INSERT INTO `categoria_professores` (`nome`, `codigo`, `tipo_ensino`, `tipo_quadro`, `tipo_nomeacao`, `ordem`, `ativo`) VALUES

-- Professores do 2º e 3º Ciclos e Sec.
('Professores do 2º e 3º Ciclos e Sec. - Quadro Escola - Nomeação Definitiva', 'P23S_QE_ND', '2º e 3º Ciclos e Sec.', 'Quadro Escola', 'Nomeação Definitiva', 1, 1),
('Professores do 2º e 3º Ciclos e Sec. - Quadro Escola - Nomeação Provisória', 'P23S_QE_NP', '2º e 3º Ciclos e Sec.', 'Quadro Escola', 'Nomeação Provisória', 2, 1),
('Professores do 2º e 3º Ciclos e Sec. - QZP - Nomeação Definitiva', 'P23S_QZP_ND', '2º e 3º Ciclos e Sec.', 'QZP', 'Nomeação Definitiva', 3, 1),
('Professores do 2º e 3º Ciclos e Sec. - QZP - Nomeação Provisória', 'P23S_QZP_NP', '2º e 3º Ciclos e Sec.', 'QZP', 'Nomeação Provisória', 4, 1),
('Professores do 2º e 3º Ciclos e Sec. - Quadro Agrupamento - Nomeação Definitiva', 'P23S_QA_ND', '2º e 3º Ciclos e Sec.', 'Quadro Agrupamento', 'Nomeação Definitiva', 5, 1),
('Professores do 2º e 3º Ciclos e Sec. - Contratado', 'P23S_CONT', '2º e 3º Ciclos e Sec.', 'Contratado', 'Contratado', 6, 1),
('Professores do 2º e 3º Ciclos e Sec. - Estagiário', 'P23S_EST', '2º e 3º Ciclos e Sec.', 'Estagiário', 'Estagiário', 7, 1),

-- Professores do 1º Ciclo
('Professores do 1º Ciclo - Quadro Escola - Nomeação Definitiva', 'P1C_QE_ND', '1º Ciclo', 'Quadro Escola', 'Nomeação Definitiva', 8, 1),
('Professores do 1º Ciclo - QZP - Nomeação Definitiva', 'P1C_QZP_ND', '1º Ciclo', 'QZP', 'Nomeação Definitiva', 9, 1),
('Professores do 1º Ciclo - QZP - Nomeação Provisória', 'P1C_QZP_NP', '1º Ciclo', 'QZP', 'Nomeação Provisória', 10, 1),
('Professores do 1º Ciclo - Quadro Agrupamento - Nomeação Definitiva', 'P1C_QA_ND', '1º Ciclo', 'Quadro Agrupamento', 'Nomeação Definitiva', 11, 1),
('Professores do 1º Ciclo - Quadro Escola - Nomeação Provisória', 'P1C_QE_NP', '1º Ciclo', 'Quadro Escola', 'Nomeação Provisória', 12, 1),
('Professores do 1º Ciclo - Contratado', 'P1C_CONT', '1º Ciclo', 'Contratado', 'Contratado', 13, 1),

-- Educadores de Infância
('Educadores Infância - QZP - Nomeação Definitiva', 'EDU_INF_QZP_ND', 'Educação Infantil', 'QZP', 'Nomeação Definitiva', 14, 1),
('Educadores Infância - QZP - Nomeação Provisória', 'EDU_INF_QZP_NP', 'Educação Infantil', 'QZP', 'Nomeação Provisória', 15, 1),
('Educadores Infância - Quadro Agrupamento - Nomeação Definitiva', 'EDU_INF_QA_ND', 'Educação Infantil', 'Quadro Agrupamento', 'Nomeação Definitiva', 16, 1),
('Educadores Infância - Quadro Escola - Nomeação Definitiva', 'EDU_INF_QE_ND', 'Educação Infantil', 'Quadro Escola', 'Nomeação Definitiva', 17, 1),

-- Professores de Educação Especial
('Professor de Educação Especial - Quadro Escola - Nomeação Definitiva', 'PEE_QE_ND', 'Educação Especial', 'Quadro Escola', 'Nomeação Definitiva', 18, 1),
('Professor de Educação Especial - Quadro Escola - Nomeação Provisória', 'PEE_QE_NP', 'Educação Especial', 'Quadro Escola', 'Nomeação Provisória', 19, 1),
('Professor de Educação Especial - QZP - Nomeação Definitiva', 'PEE_QZP_ND', 'Educação Especial', 'QZP', 'Nomeação Definitiva', 20, 1),
('Professores de Educação Especial - Quadro Agrupamento - Nomeação Definitiva', 'PEE_QA_ND', 'Educação Especial', 'Quadro Agrupamento', 'Nomeação Definitiva', 21, 1),
('Professor de Educação Especial - Contratado - Contratado', 'PEE_CONT', 'Educação Especial', 'Contratado', 'Contratado', 22, 1),

-- Outra
('Outra', 'OUTRA', 'Outra', 'Outra', 'N/A', 23, 1);

-- 3. Verificar inserção
SELECT * FROM `categoria_professores` ORDER BY `ordem`;

-- 4. Estatísticas
SELECT 
    tipo_ensino, 
    COUNT(*) as total_categorias 
FROM `categoria_professores` 
WHERE ativo = 1 
GROUP BY tipo_ensino;

-- ====================================================================
-- FIM DO SCRIPT
-- ====================================================================
