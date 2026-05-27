-- Adicionar ano_letivo_id à tabela sessao_exame
-- Os 115 registos existentes são atribuídos ao ano activo (id=10, 2026)

ALTER TABLE `sessao_exame`
    ADD COLUMN `ano_letivo_id` INT(11) NULL AFTER `observacoes`,
    ADD INDEX `idx_sessao_exame_ano_letivo` (`ano_letivo_id`),
    ADD CONSTRAINT `fk_sessao_exame_ano_letivo`
        FOREIGN KEY (`ano_letivo_id`) REFERENCES `ano_letivo` (`id_anoletivo`)
        ON DELETE RESTRICT ON UPDATE CASCADE;

-- Atribuir todos os registos existentes ao ano letivo activo (2026)
UPDATE `sessao_exame`
SET `ano_letivo_id` = (SELECT `id_anoletivo` FROM `ano_letivo` WHERE `status` = 1 LIMIT 1)
WHERE `ano_letivo_id` IS NULL;

-- Tornar a coluna NOT NULL após preenchimento
ALTER TABLE `sessao_exame`
    MODIFY COLUMN `ano_letivo_id` INT(11) NOT NULL;
\\