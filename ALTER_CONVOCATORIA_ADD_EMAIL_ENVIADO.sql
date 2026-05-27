-- =====================================================
-- Adicionar campo para controlar envio de email
-- Data: 15/03/2026
-- =====================================================

ALTER TABLE `convocatoria` 
ADD COLUMN `email_enviado_em` DATETIME NULL COMMENT 'Data e hora do envio do email. NULL = não enviado' AFTER `data_confirmacao`,
ADD INDEX `idx_email_enviado` (`email_enviado_em`);

-- Comentário: Este campo permite controlar quais convocatórias já tiveram email enviado
-- Apenas convocatórias com email enviado aparecem nos dashboards
