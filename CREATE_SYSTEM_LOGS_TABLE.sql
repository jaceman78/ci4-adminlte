-- Tabela de logs do sistema
CREATE TABLE IF NOT EXISTS `system_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'ID do usuário que realizou a ação',
  `user_nif` varchar(20) DEFAULT NULL COMMENT 'NIF do usuário',
  `user_name` varchar(255) DEFAULT NULL COMMENT 'Nome do usuário',
  `module` varchar(50) NOT NULL COMMENT 'Módulo do sistema (permutas, creditos, horarios, tickets, etc)',
  `action` varchar(50) NOT NULL COMMENT 'Ação realizada (create, update, delete, approve, reject, etc)',
  `record_id` varchar(50) DEFAULT NULL COMMENT 'ID do registro afetado',
  `description` text DEFAULT NULL COMMENT 'Descrição detalhada da ação',
  `old_values` longtext DEFAULT NULL COMMENT 'Valores antigos (JSON)',
  `new_values` longtext DEFAULT NULL COMMENT 'Valores novos (JSON)',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'Endereço IP do usuário',
  `user_agent` varchar(500) DEFAULT NULL COMMENT 'User agent do navegador',
  `severity` enum('info','warning','error','critical') DEFAULT 'info' COMMENT 'Nível de severidade',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_user_nif` (`user_nif`),
  KEY `idx_module` (`module`),
  KEY `idx_action` (`action`),
  KEY `idx_record_id` (`record_id`),
  KEY `idx_severity` (`severity`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Logs de atividades do sistema';

-- Índice composto para consultas comuns
CREATE INDEX idx_module_action ON system_logs(module, action);
CREATE INDEX idx_module_record ON system_logs(module, record_id);
CREATE INDEX idx_created_at_module ON system_logs(created_at, module);
