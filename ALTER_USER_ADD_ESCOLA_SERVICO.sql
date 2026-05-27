-- ====================================================================
-- ADICIONAR CAMPO escola_servico À TABELA user
-- ====================================================================
-- Sistema: Gestão Escolar - CodeIgniter 4
-- Objetivo: Adicionar campo para registar a escola onde o utilizador presta serviço
-- Data: 2026-03-13
-- ====================================================================

USE sistema_gestao;

-- 1. Verificar estrutura atual da tabela user
DESCRIBE `user`;

-- 2. Adicionar campo escola_servico (INT UNSIGNED para FK com escolas.id)
ALTER TABLE `user` 
ADD COLUMN `escola_servico` INT(5) UNSIGNED NULL DEFAULT NULL
COMMENT 'FK para escolas - Escola onde o utilizador desenvolve atividade'
AFTER `categoria`;

-- 3. Criar índice para melhor performance
ALTER TABLE `user`
ADD INDEX `idx_escola_servico` (`escola_servico`);

-- 4. Adicionar Foreign Key
ALTER TABLE `user`
ADD CONSTRAINT `fk_user_escola_servico`
FOREIGN KEY (`escola_servico`) 
REFERENCES `escolas`(`id`)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- 5. Verificar alteração
DESCRIBE `user`;

-- 6. Verificar constraints
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME,
    DELETE_RULE,
    UPDATE_RULE
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'sistema_gestao'
  AND TABLE_NAME = 'user'
  AND COLUMN_NAME = 'escola_servico';

-- 7. Ver lista de escolas disponíveis
SELECT * FROM `escolas` ORDER BY `nome`;

-- 8. Estatísticas de utilizadores por escola (após atribuição)
SELECT 
    e.nome as escola,
    COUNT(u.id) as total_utilizadores
FROM `escolas` e
LEFT JOIN `user` u ON u.escola_servico = e.id
GROUP BY e.id, e.nome
ORDER BY total_utilizadores DESC;

-- ====================================================================
-- NOTAS IMPORTANTES
-- ====================================================================
-- 1. O campo escola_servico é NULL por padrão (permite utilizadores sem escola atribuída)
-- 2. A FK permite DELETE SET NULL (se escola for apagada, o campo fica NULL)
-- 3. A FK permite UPDATE CASCADE (se ID da escola mudar, atualiza automaticamente)
-- 4. Este campo é especialmente útil para professores e staff
-- ====================================================================

-- ====================================================================
-- EXEMPLO DE USO
-- ====================================================================
-- Atribuir escola a um utilizador:
-- UPDATE `user` SET `escola_servico` = 1 WHERE `id` = 123;

-- Consultar utilizadores de uma escola:
-- SELECT u.*, e.nome as escola_nome 
-- FROM `user` u 
-- INNER JOIN `escolas` e ON u.escola_servico = e.id 
-- WHERE e.id = 1;
-- ====================================================================

-- FIM DO SCRIPT
-- ====================================================================
