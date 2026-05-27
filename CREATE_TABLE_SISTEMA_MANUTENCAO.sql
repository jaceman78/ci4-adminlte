-- =====================================================
-- TABELA: sistema_manutencao
-- DESCRIÇÃO: Armazena a configuração do modo manutenção
-- DATA: 19/03/2026
-- =====================================================

CREATE TABLE IF NOT EXISTS `sistema_manutencao` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ativo` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Desativado, 1 = Ativado',
  `mensagem` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mensagem personalizada para exibir na página de manutenção',
  `data_inicio` datetime DEFAULT NULL COMMENT 'Data/hora de início da manutenção',
  `data_fim_prevista` datetime DEFAULT NULL COMMENT 'Data/hora prevista para fim da manutenção',
  `criado_por` int(11) DEFAULT NULL COMMENT 'NIF do utilizador que ativou',
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ativo` (`ativo`),
  KEY `fk_criado_por` (`criado_por`),
  CONSTRAINT `fk_manutencao_user` FOREIGN KEY (`criado_por`) REFERENCES `user` (`NIF`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configuração do modo de manutenção do sistema';

-- Inserir registo inicial (manutenção desativada)
INSERT INTO `sistema_manutencao` (`id`, `ativo`, `mensagem`, `data_inicio`, `data_fim_prevista`, `criado_por`) 
VALUES (1, 0, 'O site está temporariamente em manutenção. Por favor, volte mais tarde.', NULL, NULL, NULL);
