-- ====================================================================
-- ALTERAÇÃO DA TABELA ferias_pedido
-- Adicionar estado "remarcacao_solicitada" ao ENUM
-- ====================================================================
-- Data: 18/03/2026
-- Objetivo: Permitir que pedidos de remarcação sejam aprovados pela secretaria antes de cancelar
-- ====================================================================

-- Verificar estrutura atual
SELECT 'Estado ANTES da alteração:' AS info;
SHOW COLUMNS FROM ferias_pedido WHERE Field = 'estado';

-- Alterar o ENUM para incluir 'remarcacao_solicitada'
ALTER TABLE `ferias_pedido`
MODIFY COLUMN `estado` ENUM(
    'por_preencher',
    'submetido',
    'em_aprovacao',
    'aprovado',
    'rejeitado',
    'cancelado',
    'aguarda_assinatura',
    'concluido',
    'remarcacao_solicitada'
) DEFAULT 'por_preencher'
COMMENT 'Estado do pedido: remarcacao_solicitada significa que o professor solicitou remarcação e aguarda aprovação da secretaria';

-- Verificar resultado
SELECT 'Estado APÓS a alteração:' AS info;
SHOW COLUMNS FROM ferias_pedido WHERE Field = 'estado';

SELECT '✓ Migração concluída com sucesso!' AS resultado;
SELECT '✓ Novo estado "remarcacao_solicitada" disponível' AS info;
SELECT '✓ Fluxo: Professor solicita remarcação → Estado muda para "remarcacao_solicitada" → Secretaria aprova/rejeita' AS fluxo;
