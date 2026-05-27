-- Adicionar ano_letivo_id à tabela horario_aulas
-- Os 33080 registos existentes são atribuídos ao ano activo (id=10, 2026)

ALTER TABLE `horario_aulas`
    ADD COLUMN `ano_letivo_id` INT(11) NULL AFTER `hora_fim`,
    ADD INDEX `idx_horario_aulas_ano_letivo` (`ano_letivo_id`),
    ADD CONSTRAINT `fk_horario_aulas_ano_letivo`
        FOREIGN KEY (`ano_letivo_id`) REFERENCES `ano_letivo` (`id_anoletivo`)
        ON DELETE RESTRICT ON UPDATE CASCADE;

-- Atribuir todos os registos existentes ao ano letivo activo (2026)
UPDATE `horario_aulas`
SET `ano_letivo_id` = (SELECT `id_anoletivo` FROM `ano_letivo` WHERE `status` = 1 LIMIT 1)
WHERE `ano_letivo_id` IS NULL;

-- Tornar a coluna NOT NULL após preenchimento
ALTER TABLE `horario_aulas`
    MODIFY COLUMN `ano_letivo_id` INT(11) NOT NULL;
