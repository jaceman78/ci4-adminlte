-- =====================================================
-- SISTEMA DE CONVOCATÓRIAS PARA VIGILÂNCIA DE EXAMES
-- Data: 30/01/2026
-- =====================================================

-- 1. TABELA EXAME
-- Armazena os dados de identificação de cada prova
CREATE TABLE IF NOT EXISTS `exame` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `codigo_prova` VARCHAR(10) NOT NULL COMMENT 'Código oficial da prova (ex: 639, 91, 47)',
    `nome_prova` VARCHAR(100) NOT NULL COMMENT 'Nome da prova (ex: Português, Matemática A)',
    `tipo_prova` ENUM('Exame Nacional', 'Prova Final', 'MODa') NOT NULL COMMENT 'Categoria da prova',
    `ano_escolaridade` INT(2) NOT NULL COMMENT 'Ano de escolaridade (ex: 4, 6, 9, 11, 12)',
    `ativo` TINYINT(1) DEFAULT 1 COMMENT '1=Ativo, 0=Inativo',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_codigo_prova` (`codigo_prova`),
    KEY `idx_tipo_prova` (`tipo_prova`),
    KEY `idx_ano_escolaridade` (`ano_escolaridade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABELA SESSAO_EXAME
-- Armazena os detalhes de cada ocorrência de uma prova
CREATE TABLE IF NOT EXISTS `sessao_exame` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `exame_id` INT(11) UNSIGNED NOT NULL COMMENT 'Chave estrangeira para exame.id',
    `fase` ENUM('1ªfase', '2ªfase', 'Prova Ensaio', 'Oral', 'Época Especial') NOT NULL COMMENT 'Fase/Turno do exame',
    `data_exame` DATE NOT NULL COMMENT 'Data da sessão de exame',
    `hora_exame` TIME NOT NULL COMMENT 'Hora de início da sessão',
    `duracao_minutos` INT(11) NOT NULL COMMENT 'Duração total da prova em minutos',
    `tolerancia_minutos` INT(11) NOT NULL DEFAULT 0 COMMENT 'Duração de tolerância da prova em minutos',
    `num_alunos` INT(11) NULL COMMENT 'Número estimado de alunos',
    `observacoes` TEXT NULL COMMENT 'Observações adicionais sobre a sessão',
    `ativo` TINYINT(1) DEFAULT 1 COMMENT '1=Ativo, 0=Cancelado',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_exame_id` (`exame_id`),
    KEY `idx_data_exame` (`data_exame`),
    KEY `idx_fase` (`fase`),
    CONSTRAINT `fk_sessao_exame_exame` 
        FOREIGN KEY (`exame_id`) 
        REFERENCES `exame`(`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABELA CONVOCATORIA
-- Liga professores às sessões de exame com funções específicas
CREATE TABLE IF NOT EXISTS `convocatoria` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `sessao_exame_id` INT(11) UNSIGNED NOT NULL COMMENT 'Chave estrangeira para sessao_exame.id',
    `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'Chave estrangeira para user.id (Professor)',
    `sala_id` INT(11) UNSIGNED NULL COMMENT 'Chave estrangeira para salas.id - NULL para Suplentes',
    `funcao` ENUM('Vigilante', 'Suplente', 'Coadjuvante', 'Júri', 'Verificar Calculadoras', 'Apoio TIC') NOT NULL COMMENT 'Função atribuída ao professor',
    `estado_confirmacao` ENUM('Pendente', 'Confirmado', 'Rejeitado') NOT NULL DEFAULT 'Pendente' COMMENT 'Estado do Tomei Conhecimento',
    `presenca` ENUM('Pendente', 'Presente', 'Falta', 'Falta Justificada') NOT NULL DEFAULT 'Pendente' COMMENT 'Estado de presença do professor na vigilância',
    `data_confirmacao` DATETIME NULL COMMENT 'Data e hora da confirmação',
    `observacoes` TEXT NULL COMMENT 'Observações do professor ou coordenador',
    `created_at` DATETIME NULL,
    `updated_at` DATETIME NULL,
    PRIMARY KEY (`id`),
    KEY `idx_sessao_exame_id` (`sessao_exame_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_sala_id` (`sala_id`),
    KEY `idx_estado_confirmacao` (`estado_confirmacao`),
    KEY `idx_presenca` (`presenca`),
    CONSTRAINT `fk_convocatoria_sessao_exame` 
        FOREIGN KEY (`sessao_exame_id`) 
        REFERENCES `sessao_exame`(`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT `fk_convocatoria_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `user`(`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT `fk_convocatoria_sala` 
        FOREIGN KEY (`sala_id`) 
        REFERENCES `salas`(`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- POPULAR TABELA EXAME COM CÓDIGOS OFICIAIS
-- =====================================================

-- PROVAS FINAIS DO ENSINO BÁSICO
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`) VALUES
('21', 'Português - 4º ano', 'Prova Final', 4, 1),
('22', 'Matemática - 4º ano', 'Prova Final', 4, 1),
('81', 'Português - 6º ano', 'Prova Final', 6, 1),
('82', 'Matemática - 6º ano', 'Prova Final', 6, 1),
('91', 'Português - 9º ano', 'Prova Final', 9, 1),
('92', 'Matemática - 9º ano', 'Prova Final', 9, 1);

-- EXAMES NACIONAIS - 11º ANO
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`) VALUES
('708', 'Geometria Descritiva A', 'Exame Nacional', 11, 1),
('712', 'Economia A', 'Exame Nacional', 11, 1),
('715', 'Filosofia', 'Exame Nacional', 11, 1),
('719', 'Geografia A', 'Exame Nacional', 11, 1),
('723', 'História B', 'Exame Nacional', 11, 1),
('724', 'História da Cultura e das Artes', 'Exame Nacional', 11, 1),
('732', 'Física e Química A', 'Exame Nacional', 11, 1),
('835', 'Matemática Aplicada às Ciências Sociais', 'Exame Nacional', 11, 1);

-- EXAMES NACIONAIS - 12º ANO
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`) VALUES
('502', 'Alemão', 'Exame Nacional', 12, 1),
('517', 'Francês', 'Exame Nacional', 12, 1),
('550', 'Inglês', 'Exame Nacional', 12, 1),
('547', 'Espanhol', 'Exame Nacional', 12, 1),
('639', 'Português', 'Exame Nacional', 12, 1),
('702', 'Biologia e Geologia', 'Exame Nacional', 12, 1),
('706', 'Desenho A', 'Exame Nacional', 12, 1),
('635', 'Matemática A', 'Exame Nacional', 12, 1),
('735', 'Matemática B', 'Exame Nacional', 12, 1),
('710', 'História A', 'Exame Nacional', 12, 1),
('714', 'Literatura Portuguesa', 'Exame Nacional', 12, 1);

-- PROVAS MODa (Modalidades Artísticas Especializadas)
INSERT INTO `exame` (`codigo_prova`, `nome_prova`, `tipo_prova`, `ano_escolaridade`, `ativo`) VALUES
('310', 'Instrumento - Sopros e Percussão', 'MODa', 12, 1),
('311', 'Instrumento - Cordas e Teclas', 'MODa', 12, 1),
('312', 'Formação Musical', 'MODa', 12, 1),
('323', 'Prova de Aptidão Artística - Dança', 'MODa', 12, 1);

-- =====================================================
-- VIEWS ÚTEIS
-- =====================================================

-- View: Sessões de Exame com detalhes completos
CREATE OR REPLACE VIEW vw_sessoes_exames_completo AS
SELECT 
    se.id,
    se.fase,
    se.data_exame,
    se.hora_exame,
    se.duracao_minutos,
    se.tolerancia_minutos,
    se.num_alunos,
    se.observacoes,
    se.ativo,
    e.codigo_prova,
    e.nome_prova,
    e.tipo_prova,
    e.ano_escolaridade,
    -- Hora de fim calculada
    ADDTIME(se.hora_exame, SEC_TO_TIME((se.duracao_minutos + se.tolerancia_minutos) * 60)) as hora_fim,
    -- Contagem de convocatórias
    (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id) as total_convocados,
    (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id AND estado_confirmacao = 'Confirmado') as total_confirmados,
    (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_id = se.id AND estado_confirmacao = 'Pendente') as total_pendentes
FROM sessao_exame se
INNER JOIN exame e ON e.id = se.exame_id;

-- View: Convocatórias com detalhes completos
CREATE OR REPLACE VIEW vw_convocatorias_completo AS
SELECT 
    c.id,
    c.sessao_exame_id,
    c.user_id,
    c.sala_id,
    c.funcao,
    c.estado_confirmacao,
    c.data_confirmacao,
    c.observacoes as convocatoria_observacoes,
    u.name as professor_nome,
    u.email as professor_email,
    u.telefone as professor_telefone,
    u.NIF as professor_nif,
    s.codigo_sala,
    s.descricao as sala_descricao,
    se.data_exame,
    se.hora_exame,
    se.duracao_minutos,
    se.tolerancia_minutos,
    se.fase,
    se.observacoes as sessao_observacoes,
    e.codigo_prova,
    e.nome_prova,
    e.tipo_prova,
    e.ano_escolaridade
FROM convocatoria c
INNER JOIN user u ON u.id = c.user_id
LEFT JOIN salas s ON s.id = c.sala_id
INNER JOIN sessao_exame se ON se.id = c.sessao_exame_id
INNER JOIN exame e ON e.id = se.exame_id;

-- =====================================================
-- EXEMPLO DE DADOS (OPCIONAL - COMENTADO)
-- =====================================================
/*
-- Exemplo: Criar sessão de exame de Matemática A
INSERT INTO `sessao_exame` (`exame_id`, `fase`, `data_exame`, `hora_exame`, `duracao_minutos`, `tolerancia_minutos`, `num_alunos`, `observacoes`) 
VALUES 
(
    (SELECT id FROM exame WHERE codigo_prova = '635'), -- Matemática A
    '1ª Fase',
    '2026-06-18',
    '09:30:00',
    150,
    30,
    45,
    'Exame com calculadora gráfica permitida'
);

-- Exemplo: Criar convocatórias para a sessão
INSERT INTO `convocatoria` (`sessao_exame_id`, `user_id`, `sala_id`, `funcao`, `estado_confirmacao`) 
VALUES 
(1, 5, 8, 'Vigilante', 'Pendente'),
(1, 12, 8, 'Vigilante', 'Pendente'),
(1, 23, NULL, 'Suplente', 'Pendente');
*/

-- =====================================================
-- FIM DO SCRIPT
-- =====================================================

SELECT 'Sistema de Convocatórias criado com sucesso!' AS Status;
