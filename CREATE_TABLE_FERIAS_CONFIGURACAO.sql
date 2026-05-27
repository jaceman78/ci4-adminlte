-- ============================================================
-- TABELA: ferias_configuracao
-- Descrição: Configuração dos períodos permitidos para marcação de férias
-- Data: 2026-03-15
-- ============================================================

CREATE TABLE IF NOT EXISTS `ferias_configuracao` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `anoletivo_id` INT(11) NOT NULL COMMENT 'FK para ano_letivo.id_anoletivo',
  `data_inicio_permitida` DATE NOT NULL COMMENT 'Data inicial permitida para marcação de férias',
  `data_fim_permitida` DATE NOT NULL COMMENT 'Data final permitida para marcação de férias',
  `permite_marcacao` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=Marcação bloqueada, 1=Marcação permitida',
  `mensagem_bloqueio` TEXT DEFAULT NULL COMMENT 'Mensagem personalizada quando marcação está bloqueada',
  `observacoes` TEXT DEFAULT NULL COMMENT 'Observações gerais sobre a configuração',
  `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `atualizado_por` INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID do utilizador que fez a última atualização',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ano_letivo` (`anoletivo_id`),
  KEY `idx_anoletivo` (`anoletivo_id`),
  CONSTRAINT `ferias_configuracao_ibfk_1` FOREIGN KEY (`anoletivo_id`) REFERENCES `ano_letivo` (`id_anoletivo`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ferias_configuracao_ibfk_2` FOREIGN KEY (`atualizado_por`) REFERENCES `user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci 
COMMENT='Configuração dos períodos permitidos para marcação de férias por ano letivo';

-- ============================================================
-- INSERIR CONFIGURAÇÃO PADRÃO PARA O ANO LETIVO ATUAL
-- ============================================================

-- Buscar o ano letivo ativo (status=1)
SET @anoletivo_ativo = (SELECT id_anoletivo FROM ano_letivo WHERE status = 1 LIMIT 1);
SET @ano_corrente = (SELECT anoletivo FROM ano_letivo WHERE status = 1 LIMIT 1);

-- Inserir configuração padrão se o ano letivo ativo existir
INSERT INTO `ferias_configuracao` 
  (`anoletivo_id`, `data_inicio_permitida`, `data_fim_permitida`, `permite_marcacao`, `observacoes`)
SELECT 
  @anoletivo_ativo,
  CONCAT(@ano_corrente, '-09-01'),
  CONCAT(@ano_corrente + 1, '-12-31'),
  1,
  'Configuração padrão criada automaticamente'
WHERE @anoletivo_ativo IS NOT NULL
AND NOT EXISTS (
  SELECT 1 FROM ferias_configuracao WHERE anoletivo_id = @anoletivo_ativo
);

-- ============================================================
-- VERIFICAÇÃO
-- ============================================================
SELECT 'Tabela ferias_configuracao criada com sucesso!' AS status;
SELECT * FROM ferias_configuracao;
