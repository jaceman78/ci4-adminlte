-- Tabela para registo de avarias do Kit Digital
CREATE TABLE IF NOT EXISTS `registo_avarias_kit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `numero_aluno` varchar(5) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `turma` varchar(50) NOT NULL,
  `nif` varchar(9) NOT NULL,
  `email_aluno` varchar(255) NOT NULL,
  `email_ee` varchar(255) NOT NULL,
  `estado` enum('pendente','a analisar','por levantar','rejeitado','anulado','terminado') NOT NULL DEFAULT 'pendente',
  `avaria` text NOT NULL,
  `obs` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `finished_at` datetime DEFAULT NULL,
  `id_ano_letivo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_estado` (`estado`),
  KEY `idx_numero_aluno` (`numero_aluno`),
  KEY `idx_nif` (`nif`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_ano_letivo` (`id_ano_letivo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nota: Foreign key para ano_letivo removida para evitar erros de criação
-- Se necessário, pode ser adicionada posteriormente com:
-- ALTER TABLE `registo_avarias_kit` 
-- ADD CONSTRAINT `fk_avarias_kit_ano_letivo` 
-- FOREIGN KEY (`id_ano_letivo`) REFERENCES `ano_letivo` (`id`) ON DELETE SET NULL;
