-- Migração: Adicionar campos de remarcação ao pedido de férias
-- Executar em: sistema_gestao

ALTER TABLE `ferias_pedido`
    ADD COLUMN `motivo_remarcacao`      VARCHAR(500)  NULL DEFAULT NULL AFTER `numero_remarcacao`,
    ADD COLUMN `domicilio_rua`          VARCHAR(200)  NULL DEFAULT NULL AFTER `motivo_remarcacao`,
    ADD COLUMN `domicilio_localidade`   VARCHAR(100)  NULL DEFAULT NULL AFTER `domicilio_rua`,
    ADD COLUMN `telemovel_remarcacao`   VARCHAR(20)   NULL DEFAULT NULL AFTER `domicilio_localidade`,
    ADD COLUMN `documento_remarcacao`   VARCHAR(500)  NULL DEFAULT NULL AFTER `documento_assinado`,
    ADD COLUMN `pedido_remarcado_de`    INT           NULL DEFAULT NULL AFTER `documento_remarcacao`;

-- FK: liga o novo pedido (após remarcação confirmada) ao pedido original
ALTER TABLE `ferias_pedido`
    ADD CONSTRAINT `fk_pedido_remarcado_de`
        FOREIGN KEY (`pedido_remarcado_de`)
        REFERENCES `ferias_pedido`(`id`)
        ON DELETE SET NULL;
