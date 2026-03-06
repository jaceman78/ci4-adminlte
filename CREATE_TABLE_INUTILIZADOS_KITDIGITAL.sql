-- ========================================
-- Tabela: inutilizados_kitdigital
-- Descrição: Gestão de equipamentos inutilizados para canibalização de componentes
-- Data: 2026-01-26
-- ========================================

CREATE TABLE IF NOT EXISTS `inutilizados_kitdigital` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `n_serie` VARCHAR(255) NOT NULL COMMENT 'Número de série do equipamento',
    `marca` VARCHAR(100) NOT NULL COMMENT 'Marca do equipamento',
    `modelo` VARCHAR(100) NULL COMMENT 'Modelo do equipamento',
    `ram` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Disponível, 0=Já utilizado',
    `disco` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Disponível, 0=Já utilizado',
    `teclado` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Disponível, 0=Já utilizado',
    `ecra` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Disponível, 0=Já utilizado',
    `bateria` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Disponível, 0=Já utilizado',
    `caixa` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=Disponível, 0=Já utilizado',
    `outros` TEXT NULL COMMENT 'Outros componentes disponíveis',
    `observacoes` TEXT NULL COMMENT 'Observações gerais',
    `qr_code` VARCHAR(255) NULL COMMENT 'Caminho para o arquivo do QR Code',
    `id_tecnico` INT(11) UNSIGNED NULL COMMENT 'ID do técnico que registou o equipamento',
    `estado` VARCHAR(50) NOT NULL DEFAULT 'ativo' COMMENT 'Estado: ativo, esgotado, descartado',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    `deleted_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_n_serie` (`n_serie`),
    KEY `idx_estado` (`estado`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- Inserir alguns registos de exemplo (opcional)
-- ========================================
-- INSERT INTO `inutilizados_kitdigital` 
-- (`n_serie`, `marca`, `modelo`, `ram`, `disco`, `teclado`, `ecra`, `bateria`, `caixa`, `estado`, `created_at`)
-- VALUES
-- ('ABC123456', 'HP', 'ProBook 450 G5', 1, 1, 0, 1, 0, 1, 'ativo', NOW()),
-- ('DEF789012', 'Lenovo', 'ThinkPad T480', 0, 1, 1, 0, 1, 1, 'ativo', NOW()),
-- ('GHI345678', 'Dell', 'Latitude 5490', 1, 0, 1, 1, 1, 0, 'ativo', NOW());
