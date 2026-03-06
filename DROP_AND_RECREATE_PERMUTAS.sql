-- =====================================================
-- SCRIPT PARA DROPAR E RECRIAR A TABELA PERMUTAS
-- Execute este script via phpMyAdmin ou MySQL Workbench
--
-- IMPORTANTE: Este script também cria um índice na coluna
-- user.NIF que é necessário para as foreign keys funcionarem.
-- =====================================================

USE sistema_gestao;

-- 1. Dropar a tabela antiga
DROP TABLE IF EXISTS permutas;

-- 2. Criar a nova tabela com a estrutura correta
CREATE TABLE `permutas` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `aula_original_id` INT(11) NOT NULL COMMENT 'FK para horario_aulas',
    `ano_letivo_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'FK para ano_letivo - ano letivo em que a permuta foi criada',
    `data_aula_original` DATE NOT NULL COMMENT 'Data da aula a permutar',
    `data_aula_permutada` DATE NOT NULL COMMENT 'Data da reposição',
    `professor_autor_nif` INT(11) NULL DEFAULT NULL COMMENT 'NIF do professor que pediu a permuta',
    `professor_substituto_nif` INT(11) NULL DEFAULT NULL COMMENT 'NIF do professor que fará a substituição',
    `sala_permutada_id` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Sala para a reposição (null = mesma sala)',
    `grupo_permuta` VARCHAR(100) NULL DEFAULT NULL COMMENT 'ID do grupo se várias permutas juntas',
    `estado` ENUM('pendente','aprovada','rejeitada','cancelada') NOT NULL DEFAULT 'pendente',
    `observacoes` TEXT NULL DEFAULT NULL COMMENT 'Observações do professor',
    `motivo_rejeicao` TEXT NULL DEFAULT NULL COMMENT 'Motivo da rejeição (se aplicável)',
    `aprovada_por_user_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'ID do user que aprovou/rejeitou',
    `data_aprovacao` DATETIME NULL DEFAULT NULL COMMENT 'Data e hora da aprovação/rejeição',
    `created_at` DATETIME NULL DEFAULT NULL,
    `updated_at` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `permutas_aula_original_idx` (`aula_original_id`),
    INDEX `permutas_ano_letivo_idx` (`ano_letivo_id`),
    INDEX `permutas_professor_autor_idx` (`professor_autor_nif`),
    INDEX `permutas_professor_substituto_idx` (`professor_substituto_nif`),
    INDEX `permutas_estado_idx` (`estado`),
    INDEX `permutas_grupo_idx` (`grupo_permuta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Adicionar Foreign Keys
-- Primeiro, criar índice na coluna NIF da tabela user (se não existir)
ALTER TABLE `user` ADD INDEX `idx_user_nif` (`NIF`);

ALTER TABLE `permutas`
    ADD CONSTRAINT `permutas_aula_original_fk`
        FOREIGN KEY (`aula_original_id`) 
        REFERENCES `horario_aulas`(`id_aula`)
        ON DELETE CASCADE 
        ON UPDATE CASCADE;

ALTER TABLE `permutas`
    ADD CONSTRAINT `permutas_ano_letivo_fk`
        FOREIGN KEY (`ano_letivo_id`) 
        REFERENCES `ano_letivo`(`id_anoletivo`)
        ON DELETE SET NULL 
        ON UPDATE CASCADE;

ALTER TABLE `permutas`
    ADD CONSTRAINT `permutas_professor_autor_fk`
        FOREIGN KEY (`professor_autor_nif`) 
        REFERENCES `user`(`NIF`)
        ON DELETE CASCADE 
        ON UPDATE CASCADE;

ALTER TABLE `permutas`
    ADD CONSTRAINT `permutas_professor_substituto_fk`
        FOREIGN KEY (`professor_substituto_nif`) 
        REFERENCES `user`(`NIF`)
        ON DELETE CASCADE 
        ON UPDATE CASCADE;

ALTER TABLE `permutas`
    ADD CONSTRAINT `permutas_aprovador_fk`
        FOREIGN KEY (`aprovada_por_user_id`) 
        REFERENCES `user`(`id`)
        ON DELETE SET NULL 
        ON UPDATE CASCADE;

-- 4. Atualizar a tabela de migrações para marcar como executada
-- (Opcional - apenas se quiser manter o histórico de migrações limpo)
-- DELETE FROM migrations WHERE version = '2025-10-16-145700';
-- INSERT INTO migrations (version, class, `group`, namespace, time, batch) 
-- VALUES ('2025-10-21-203600', 'App\\Database\\Migrations\\RecreatePermutasTable', 'default', 'App', UNIX_TIMESTAMP(), 22);

SELECT 'Tabela permutas recriada com sucesso!' AS Status;
