-- =====================================================
-- SISTEMA DE GESTÃO DE FÉRIAS - ESTRUTURA COMPLETA
-- Script de criação de tabelas para gestão de férias de professores
-- Data: 2026-03-07
-- =====================================================

USE sistema_gestao;

-- =====================================================
-- 1. TABELA: ferias_atribuicao
-- Gestão de dias de férias atribuídos por ano letivo
-- =====================================================

CREATE TABLE IF NOT EXISTS `ferias_atribuicao` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_nif` INT(11) NOT NULL COMMENT 'NIF do professor (FK para user.NIF)',
    `anoletivo_id` INT(11) NOT NULL COMMENT 'FK para ano_letivo.id_anoletivo',
    
    -- Gestão de dias
    `dias_base` INT NOT NULL DEFAULT 22 COMMENT 'Dias base de férias (normalmente 22)',
    `dias_ajuste` INT NOT NULL DEFAULT 0 COMMENT 'Ajuste de dias do ano anterior (pode ser negativo)',
    `dias_extra` INT NOT NULL DEFAULT 0 COMMENT 'Dias extra atribuídos manualmente pela secretaria',
    `dias_total` INT GENERATED ALWAYS AS (dias_base + dias_ajuste + dias_extra) STORED COMMENT 'Total de dias disponíveis (calculado)',
    
    -- Observações
    `observacoes` TEXT NULL COMMENT 'Observações da secretaria sobre a atribuição',
    
    -- Controlo
    `atribuido_por` INT(11) UNSIGNED NULL COMMENT 'ID do utilizador que fez a atribuição (user.id)',
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices e constraints
    UNIQUE KEY `unique_user_ano` (`user_nif`, `anoletivo_id`),
    INDEX `idx_user_nif` (`user_nif`),
    INDEX `idx_anoletivo` (`anoletivo_id`),
    INDEX `idx_atribuido_por` (`atribuido_por`),
    
    FOREIGN KEY (`user_nif`) REFERENCES `user`(`NIF`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`anoletivo_id`) REFERENCES `ano_letivo`(`id_anoletivo`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`atribuido_por`) REFERENCES `user`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Atribuição de dias de férias por professor e ano letivo';

-- =====================================================
-- 2. TABELA: ferias_pedido
-- Pedidos de férias submetidos pelos professores
-- =====================================================

CREATE TABLE IF NOT EXISTS `ferias_pedido` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_nif` INT(11) NOT NULL COMMENT 'NIF do professor (FK para user.NIF)',
    `anoletivo_id` INT(11) NOT NULL COMMENT 'FK para ano_letivo.id_anoletivo',
    
    -- Estado do pedido
    `estado` ENUM(
        'por_preencher',
        'submetido',
        'em_aprovacao',
        'aprovado',
        'rejeitado',
        'cancelado',
        'aguarda_assinatura',
        'concluido'
    ) DEFAULT 'por_preencher' COMMENT 'Estado atual do pedido',
    
    -- Dias e períodos
    `total_dias` INT DEFAULT 0 COMMENT 'Total de dias úteis solicitados (soma dos períodos)',
    
    -- Documentos
    `documento_pdf` VARCHAR(255) NULL COMMENT 'Caminho do PDF gerado para assinatura',
    `documento_assinado` VARCHAR(255) NULL COMMENT 'Caminho do PDF assinado pelo professor',
    
    -- Observações e motivos
    `observacoes_professor` TEXT NULL COMMENT 'Observações adicionais do professor',
    `observacoes_secretaria` TEXT NULL COMMENT 'Observações da secretaria (motivo rejeição, etc)',
    
    -- Controlo de aprovação
    `submetido_em` DATETIME NULL COMMENT 'Data de submissão do pedido',
    `aprovado_em` DATETIME NULL COMMENT 'Data de aprovação',
    `aprovado_por` INT(11) UNSIGNED NULL COMMENT 'ID do utilizador que aprovou (user.id)',
    `rejeitado_em` DATETIME NULL COMMENT 'Data de rejeição',
    `rejeitado_por` INT(11) UNSIGNED NULL COMMENT 'ID do utilizador que rejeitou',
    `upload_assinatura_em` DATETIME NULL COMMENT 'Data do upload do documento assinado',
    
    -- Controlo
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices e constraints
    INDEX `idx_user_nif` (`user_nif`),
    INDEX `idx_anoletivo` (`anoletivo_id`),
    INDEX `idx_estado` (`estado`),
    INDEX `idx_aprovado_por` (`aprovado_por`),
    INDEX `idx_rejeitado_por` (`rejeitado_por`),
    
    FOREIGN KEY (`user_nif`) REFERENCES `user`(`NIF`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`anoletivo_id`) REFERENCES `ano_letivo`(`id_anoletivo`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`aprovado_por`) REFERENCES `user`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (`rejeitado_por`) REFERENCES `user`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Pedidos de férias dos professores';

-- =====================================================
-- 3. TABELA: ferias_periodo
-- Períodos específicos de férias dentro de um pedido
-- =====================================================

CREATE TABLE IF NOT EXISTS `ferias_periodo` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pedido_id` INT NOT NULL COMMENT 'FK para ferias_pedido.id',
    
    -- Datas do período
    `data_inicio` DATE NOT NULL COMMENT 'Data de início do período de férias',
    `data_fim` DATE NOT NULL COMMENT 'Data de fim do período de férias',
    `dias_uteis` INT NOT NULL COMMENT 'Número de dias úteis neste período',
    
    -- Observações
    `observacoes` TEXT NULL COMMENT 'Observações específicas deste período',
    
    -- Controlo
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices e constraints
    INDEX `idx_pedido` (`pedido_id`),
    INDEX `idx_datas` (`data_inicio`, `data_fim`),
    
    FOREIGN KEY (`pedido_id`) REFERENCES `ferias_pedido`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Validação: data_fim deve ser >= data_inicio
    CONSTRAINT `chk_periodo_datas` CHECK (`data_fim` >= `data_inicio`),
    CONSTRAINT `chk_dias_uteis_positivo` CHECK (`dias_uteis` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Períodos específicos de férias dentro de um pedido';

-- =====================================================
-- 4. TABELA: ferias_log (OPCIONAL - Histórico)
-- Log de todas as ações realizadas no sistema de férias
-- =====================================================

CREATE TABLE IF NOT EXISTS `ferias_log` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pedido_id` INT NULL COMMENT 'FK para ferias_pedido.id (NULL se for ação de atribuição)',
    `user_nif` INT(11) NULL COMMENT 'NIF do professor afetado',
    `acao` VARCHAR(100) NOT NULL COMMENT 'Descrição da ação (ex: "Pedido submetido", "Aprovado pela secretaria")',
    `detalhes` TEXT NULL COMMENT 'Detalhes adicionais da ação',
    `realizado_por` INT(11) UNSIGNED NULL COMMENT 'ID do utilizador que realizou a ação (user.id)',
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX `idx_pedido` (`pedido_id`),
    INDEX `idx_user_nif` (`user_nif`),
    INDEX `idx_data` (`criado_em`),
    
    FOREIGN KEY (`pedido_id`) REFERENCES `ferias_pedido`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`user_nif`) REFERENCES `user`(`NIF`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`realizado_por`) REFERENCES `user`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Log histórico de ações no sistema de férias';

-- =====================================================
-- 5. TABELA: ferias_feriados (OPCIONAL - Feriados)
-- Tabela de feriados para cálculo correto de dias úteis
-- =====================================================

CREATE TABLE IF NOT EXISTS `ferias_feriados` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `data` DATE NOT NULL UNIQUE COMMENT 'Data do feriado',
    `descricao` VARCHAR(255) NOT NULL COMMENT 'Descrição do feriado (ex: "Dia de Natal")',
    `tipo` ENUM('fixo', 'movel', 'municipal') DEFAULT 'fixo' COMMENT 'Tipo de feriado',
    `ativo` TINYINT(1) DEFAULT 1 COMMENT '1 = ativo, 0 = inativo',
    
    -- Controlo
    `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX `idx_data` (`data`),
    INDEX `idx_ativo` (`ativo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
COMMENT='Feriados de Portugal para cálculo de dias úteis';

-- =====================================================
-- 6. INSERIR FERIADOS FIXOS DE PORTUGAL (Exemplo 2026)
-- =====================================================

INSERT INTO `ferias_feriados` (`data`, `descricao`, `tipo`) VALUES
('2026-01-01', 'Ano Novo', 'fixo'),
('2026-04-03', 'Sexta-feira Santa', 'movel'),
('2026-04-05', 'Páscoa', 'movel'),
('2026-04-25', 'Dia da Liberdade', 'fixo'),
('2026-05-01', 'Dia do Trabalhador', 'fixo'),
('2026-06-04', 'Corpo de Deus', 'movel'),
('2026-06-10', 'Dia de Portugal', 'fixo'),
('2026-08-15', 'Assunção de Nossa Senhora', 'fixo'),
('2026-10-05', 'Implantação da República', 'fixo'),
('2026-11-01', 'Todos os Santos', 'fixo'),
('2026-12-01', 'Restauração da Independência', 'fixo'),
('2026-12-08', 'Imaculada Conceição', 'fixo'),
('2026-12-25', 'Natal', 'fixo')
ON DUPLICATE KEY UPDATE descricao=VALUES(descricao);

-- =====================================================
-- 7. VIEW ÚTIL: Resumo de férias por professor
-- =====================================================

CREATE OR REPLACE VIEW `view_ferias_resumo` AS
SELECT 
    fa.user_nif,
    u.name AS nome_professor,
    u.email AS email_professor,
    fa.anoletivo_id,
    al.anoletivo AS ano,
    fa.dias_base,
    fa.dias_ajuste,
    fa.dias_extra,
    fa.dias_total AS dias_disponiveis,
    COALESCE(SUM(CASE WHEN fp.estado IN ('aprovado', 'aguarda_assinatura', 'concluido') THEN fp.total_dias ELSE 0 END), 0) AS dias_gastos,
    fa.dias_total - COALESCE(SUM(CASE WHEN fp.estado IN ('aprovado', 'aguarda_assinatura', 'concluido') THEN fp.total_dias ELSE 0 END), 0) AS dias_restantes,
    COUNT(DISTINCT fp.id) AS total_pedidos,
    SUM(CASE WHEN fp.estado = 'submetido' THEN 1 ELSE 0 END) AS pedidos_pendentes,
    SUM(CASE WHEN fp.estado = 'aprovado' THEN 1 ELSE 0 END) AS pedidos_aprovados,
    SUM(CASE WHEN fp.estado = 'concluido' THEN 1 ELSE 0 END) AS pedidos_concluidos
FROM 
    ferias_atribuicao fa
    INNER JOIN user u ON u.NIF = fa.user_nif
    INNER JOIN ano_letivo al ON al.id_anoletivo = fa.anoletivo_id
    LEFT JOIN ferias_pedido fp ON fp.user_nif = fa.user_nif AND fp.anoletivo_id = fa.anoletivo_id
GROUP BY 
    fa.user_nif, u.name, u.email, fa.anoletivo_id, al.anoletivo, fa.dias_base, fa.dias_ajuste, fa.dias_extra, fa.dias_total;

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================

-- Verificação das tabelas criadas:
-- SHOW TABLES LIKE 'ferias%';
-- 
-- Para ver a estrutura:
-- DESCRIBE ferias_atribuicao;
-- DESCRIBE ferias_pedido;
-- DESCRIBE ferias_periodo;
-- DESCRIBE ferias_log;
-- DESCRIBE ferias_feriados;
