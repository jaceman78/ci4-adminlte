-- Tabela para armazenar anexos de sugestões
CREATE TABLE IF NOT EXISTS `sugestoes_anexos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sugestao_id` int(11) NOT NULL,
  `nome_original` varchar(255) NOT NULL COMMENT 'Nome original do ficheiro',
  `nome_ficheiro` varchar(255) NOT NULL COMMENT 'Nome do ficheiro no servidor',
  `tipo_mime` varchar(100) DEFAULT NULL,
  `tamanho` int(11) DEFAULT NULL COMMENT 'Tamanho em bytes',
  `caminho` varchar(500) NOT NULL COMMENT 'Caminho completo do ficheiro',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_sugestoes_anexos_sugestao` (`sugestao_id`),
  CONSTRAINT `fk_sugestoes_anexos_sugestao` FOREIGN KEY (`sugestao_id`) REFERENCES `sugestoes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
