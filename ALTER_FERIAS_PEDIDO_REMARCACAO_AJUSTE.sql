-- Migração: Ajustar campos de remarcação em ferias_pedido
-- Executar em: sistema_gestao

-- Remover campo que passou a ser desnecessário (telefone fica em user.telefone)
ALTER TABLE `ferias_pedido`
    DROP COLUMN IF EXISTS `telemovel_remarcacao`;

-- Adicionar campo para o PDF de Alteração de Férias assinado (upload do professor)
ALTER TABLE `ferias_pedido`
    ADD COLUMN `documento_remarcacao_assinado` VARCHAR(500) NULL DEFAULT NULL AFTER `documento_remarcacao`;
