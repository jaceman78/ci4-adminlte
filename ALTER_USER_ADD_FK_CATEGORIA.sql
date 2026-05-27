-- ====================================================================
-- ALTERAR TABELA user - ADICIONAR FK PARA categoria_professores
-- ====================================================================
-- Sistema: Gestão Escolar - CodeIgniter 4
-- Objetivo: Transformar campo categoria em FK para categoria_professores
-- Data: 2026-03-13
-- ====================================================================

USE sistema_gestao;

-- ====================================================================
-- IMPORTANTE: Execute este script APÓS criar a tabela categoria_professores
-- ====================================================================

-- 1. Verificar se o campo categoria já existe
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    IS_NULLABLE, 
    COLUMN_KEY 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'sistema_gestao' 
  AND TABLE_NAME = 'user' 
  AND COLUMN_NAME = 'categoria';

-- 2. Se o campo categoria NÃO existir, criar como VARCHAR temporariamente
-- (Se já existir, este comando será ignorado pelo IF NOT)
SET @sql = (SELECT 
    IF(COUNT(*) = 0,
        'ALTER TABLE `user` ADD COLUMN `categoria` VARCHAR(100) NULL DEFAULT NULL COMMENT ''Categoria do professor (temporário)'' AFTER `cod_funcionario`',
        'SELECT ''Campo categoria já existe'' as status'
    )
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'sistema_gestao' 
      AND TABLE_NAME = 'user' 
      AND COLUMN_NAME = 'categoria'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. Criar backup dos dados de categoria (em caso de valores antigos)
-- Isto ajuda a mapear os valores antigos para os novos IDs
CREATE TABLE IF NOT EXISTS `user_categoria_backup` (
    `user_id` INT(11) UNSIGNED NOT NULL,
    `categoria_antiga` VARCHAR(100) NULL,
    `categoria_nova_id` INT(11) UNSIGNED NULL,
    `backup_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Guardar valores atuais
INSERT INTO `user_categoria_backup` (`user_id`, `categoria_antiga`)
SELECT id, categoria 
FROM `user` 
WHERE categoria IS NOT NULL
ON DUPLICATE KEY UPDATE categoria_antiga = VALUES(categoria_antiga);

-- 4. Mapear categorias antigas para as novas IDs (manual - ajustar conforme necessário)
-- Exemplo: Se tinha "PQND", mapear para o ID da categoria correspondente
UPDATE `user_categoria_backup` ucb
INNER JOIN `categoria_professores` cp ON cp.codigo = 'P23S_QE_ND'
SET ucb.categoria_nova_id = cp.id
WHERE ucb.categoria_antiga = 'PQND';

UPDATE `user_categoria_backup` ucb
INNER JOIN `categoria_professores` cp ON cp.codigo = 'P23S_QE_NP'
SET ucb.categoria_nova_id = cp.id
WHERE ucb.categoria_antiga = 'PQNP';

UPDATE `user_categoria_backup` ucb
INNER JOIN `categoria_professores` cp ON cp.codigo = 'P23S_QZP_ND'
SET ucb.categoria_nova_id = cp.id
WHERE ucb.categoria_antiga = 'QZP';

UPDATE `user_categoria_backup` ucb
INNER JOIN `categoria_professores` cp ON cp.codigo = 'P23S_CONT'
SET ucb.categoria_nova_id = cp.id
WHERE ucb.categoria_antiga = 'PC';

UPDATE `user_categoria_backup` ucb
INNER JOIN `categoria_professores` cp ON cp.codigo = 'OUTRA'
SET ucb.categoria_nova_id = cp.id
WHERE ucb.categoria_antiga = 'outro';

-- 5. Mostrar mapeamento
SELECT 
    ucb.*,
    cp.nome as categoria_nova_nome
FROM `user_categoria_backup` ucb
LEFT JOIN `categoria_professores` cp ON cp.id = ucb.categoria_nova_id
LIMIT 20;

-- 6. ATENÇÃO: Remover FK antiga se existir (caso já tenha sido criada antes)
SET @sql_drop_fk = (SELECT 
    CONCAT('ALTER TABLE `user` DROP FOREIGN KEY `', CONSTRAINT_NAME, '`')
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = 'sistema_gestao' 
      AND TABLE_NAME = 'user' 
      AND COLUMN_NAME = 'categoria'
      AND CONSTRAINT_NAME LIKE 'fk_%'
    LIMIT 1
);

SET @sql_drop_fk = IFNULL(@sql_drop_fk, 'SELECT ''Nenhuma FK para remover'' as status');
PREPARE stmt FROM @sql_drop_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 7. Alterar o campo categoria de VARCHAR para INT (preparar para FK)
-- IMPORTANTE: Isto vai LIMPAR os dados existentes! Use o backup criado acima para restaurar
ALTER TABLE `user` 
MODIFY COLUMN `categoria` INT(11) UNSIGNED NULL DEFAULT NULL
COMMENT 'FK para categoria_professores';

-- 8. Restaurar dados mapeados (se houver mapeamento no backup)
UPDATE `user` u
INNER JOIN `user_categoria_backup` ucb ON u.id = ucb.user_id
SET u.categoria = ucb.categoria_nova_id
WHERE ucb.categoria_nova_id IS NOT NULL;

-- 9. Criar a Foreign Key
ALTER TABLE `user`
ADD CONSTRAINT `fk_user_categoria_professores`
FOREIGN KEY (`categoria`) 
REFERENCES `categoria_professores`(`id`)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- 10. Criar índice para melhor performance
ALTER TABLE `user`
ADD INDEX `idx_categoria` (`categoria`);

-- 11. Verificar estrutura final
DESCRIBE `user`;

-- 12. Verificar constraints
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'sistema_gestao'
  AND TABLE_NAME = 'user'
  AND COLUMN_NAME = 'categoria';

-- 13. Estatísticas de utilizadores por categoria
SELECT 
    cp.nome as categoria,
    COUNT(u.id) as total_users
FROM `user` u
LEFT JOIN `categoria_professores` cp ON u.categoria = cp.id
GROUP BY u.categoria, cp.nome
ORDER BY total_users DESC;

-- ====================================================================
-- NOTAS IMPORTANTES
-- ====================================================================
-- 1. Este script cria um backup antes de modificar os dados
-- 2. É necessário mapear manualmente os valores antigos para os novos IDs
-- 3. O campo categoria será NULL para utilizadores sem categoria
-- 4. A FK permite DELETE SET NULL e UPDATE CASCADE
-- ====================================================================

-- ====================================================================
-- FIM DO SCRIPT
-- ====================================================================
