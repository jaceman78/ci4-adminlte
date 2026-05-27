-- Criar tabela para registar faltas que descontam em férias
-- Suporta os dois tipos de faltas previstas na legislação:
--   - Artigo 102.º do ECD
--   - Artigo 134.º n.3 do ECD

CREATE TABLE IF NOT EXISTS ferias_faltas_desconto (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_nif INT(11) NOT NULL COMMENT 'NIF do professor',
    anoletivo_id_falta INT(11) NOT NULL COMMENT 'Ano letivo em que ocorreu a falta',
    anoletivo_id_desconto INT(11) NOT NULL COMMENT 'Ano letivo onde será descontado',
    data_falta DATE NOT NULL COMMENT 'Data da falta',
    tipo_falta ENUM('artigo_102', 'artigo_134_n3') NOT NULL COMMENT 'Tipo de falta segundo legislação',
    dias_desconto DECIMAL(5,2) NOT NULL DEFAULT 1.00 COMMENT 'Dias a descontar (permite meios dias)',
    motivo TEXT NULL COMMENT 'Descrição/motivo da falta',
    observacoes TEXT NULL COMMENT 'Observações internas',
    registado_por INT(11) UNSIGNED NULL COMMENT 'Utilizador que registou',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    FOREIGN KEY (user_nif) REFERENCES user(NIF) ON DELETE CASCADE,
    FOREIGN KEY (anoletivo_id_falta) REFERENCES ano_letivo(id_anoletivo) ON DELETE RESTRICT,
    FOREIGN KEY (anoletivo_id_desconto) REFERENCES ano_letivo(id_anoletivo) ON DELETE RESTRICT,
    FOREIGN KEY (registado_por) REFERENCES user(id) ON DELETE SET NULL,
    
    -- Índices para otimização
    INDEX idx_user_nif (user_nif),
    INDEX idx_anoletivo_falta (anoletivo_id_falta),
    INDEX idx_anoletivo_desconto (anoletivo_id_desconto),
    INDEX idx_data_falta (data_falta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Registo de faltas que descontam em dias de férias';

-- Inserir dados de exemplo (opcional)
SELECT 'Tabela ferias_faltas_desconto criada com sucesso!' AS status;

-- Ver estrutura
DESCRIBE ferias_faltas_desconto;
