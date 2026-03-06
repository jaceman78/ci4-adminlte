-- Adicionar campo plafond_com_iva à tabela empresas_chaves_acesso
-- Data: 2026-02-23
-- Descrição: Campo para guardar o valor do plafond que a empresa pode gastar (valor com IVA incluído)

ALTER TABLE `empresas_chaves_acesso` 
ADD COLUMN `plafond_com_iva` DECIMAL(10, 2) NULL DEFAULT NULL COMMENT 'Plafond disponível para a empresa (com IVA incluído)' 
AFTER `chave_acesso`;

-- Adicionar um comentário na tabela
ALTER TABLE `empresas_chaves_acesso` COMMENT = 'Tabela de chaves de acesso para empresas externas - gestão de reparações';
