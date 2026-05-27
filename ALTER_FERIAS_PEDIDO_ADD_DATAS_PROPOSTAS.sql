-- Adicionar coluna para guardar os períodos propostos na remarcação de férias
-- Os dados são guardados como JSON: [{"data_inicio":"2026-07-01","data_fim":"2026-07-15"}, ...]

ALTER TABLE `ferias_pedido`
    ADD COLUMN `datas_remarcacao_propostas` TEXT NULL AFTER `motivo_remarcacao`;
