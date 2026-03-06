-- Adicionar estado 'dados_invalidos' ao ENUM da tabela requisicao_kit
-- Execute este script na base de dados

ALTER TABLE `requisicao_kit` 
MODIFY COLUMN `estado` ENUM('pendente','dados_invalidos','por levantar','rejeitado','anulado','terminado') 
NOT NULL DEFAULT 'pendente';
