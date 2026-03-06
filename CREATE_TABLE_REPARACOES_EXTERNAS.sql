-- Tabela para registo de reparações externas de equipamentos
CREATE TABLE IF NOT EXISTS `reparacoes_externas` (
  `id_reparacao` int(11) NOT NULL AUTO_INCREMENT,
  `n_serie_equipamento` varchar(255) NOT NULL,
  `tipologia` enum('Tipo I','Tipo II','Tipo III') NOT NULL COMMENT 'Tipo de reparação',
  `possivel_avaria` enum('Teclado','Monitor','Bateria','Disco','Sistema Operativo','CUCo','Gráfica','Outro') NOT NULL,
  `descricao_avaria` text DEFAULT NULL COMMENT 'Descrição detalhada da avaria',
  `data_envio` date NOT NULL COMMENT 'Data de envio para reparação',
  `empresa_reparacao` varchar(255) DEFAULT NULL COMMENT 'Nome da empresa que efetuou a reparação',
  `n_guia` varchar(100) DEFAULT NULL COMMENT 'Número de guia/RMA',
  `trabalho_efetuado` text DEFAULT NULL COMMENT 'Descrição do trabalho realizado',
  `custo` decimal(10,2) DEFAULT NULL COMMENT 'Custo em euros',
  `data_recepcao` date DEFAULT NULL COMMENT 'Data de receção após reparação',
  `observacoes` text DEFAULT NULL,
  `estado` enum('enviado','em_reparacao','reparado','irreparavel','cancelado') NOT NULL DEFAULT 'enviado',
  `id_tecnico` int(11) DEFAULT NULL COMMENT 'Técnico responsável pelo registo',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL COMMENT 'Para soft delete',
  PRIMARY KEY (`id_reparacao`),
  KEY `idx_n_serie` (`n_serie_equipamento`),
  KEY `idx_estado` (`estado`),
  KEY `idx_data_envio` (`data_envio`),
  KEY `idx_data_recepcao` (`data_recepcao`),
  KEY `idx_tipologia` (`tipologia`),
  KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

