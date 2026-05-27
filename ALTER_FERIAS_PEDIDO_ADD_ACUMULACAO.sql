-- Adiciona colunas para suporte ao Pedido de Acumulação de Férias (Art.º 89 ECD)
-- Executar na base de dados do projeto ci4-adminlte

ALTER TABLE `ferias_pedido`
    ADD COLUMN `requer_acumulacao`        TINYINT(1)   NOT NULL DEFAULT 0         COMMENT 'Pedido requer acumulação de férias (art.º 89 ECD)',
    ADD COLUMN `dias_sobrantes`           INT          NOT NULL DEFAULT 0         COMMENT 'Dias de férias não marcados (a acumular)',
    ADD COLUMN `motivo_acumulacao`        TEXT         NULL                       COMMENT 'Motivo indicado pelo professor para não marcar a totalidade',
    ADD COLUMN `doc_acumulacao_pdf`       VARCHAR(255) NULL                       COMMENT 'Caminho do PDF do pedido de acumulação gerado',
    ADD COLUMN `doc_acumulacao_assinado`  VARCHAR(255) NULL                       COMMENT 'Caminho do PDF do pedido de acumulação assinado (upload professor)',
    ADD COLUMN `acumulacao_estado`        ENUM('pendente','autorizada','nao_autorizada') NULL COMMENT 'Estado do despacho do diretor para a acumulação',
    ADD COLUMN `acumulacao_despacho_por`  INT          UNSIGNED NULL              COMMENT 'FK user.id - secretária que registou o despacho',
    ADD COLUMN `acumulacao_despacho_em`   DATETIME     NULL                       COMMENT 'Data/hora do registo do despacho',
    ADD COLUMN `upload_acumulacao_em`     DATETIME     NULL                       COMMENT 'Data/hora do upload do doc assinado pelo professor';

-- Índice para pesquisas frequentes por estado de acumulação
ALTER TABLE `ferias_pedido`
    ADD INDEX `idx_acumulacao_estado` (`requer_acumulacao`, `acumulacao_estado`);
