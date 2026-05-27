-- =====================================================
-- UPDATE sessao_exame.fase - Junho 2026
-- Data: 2026-05-25
-- Descrição: Define fase = '1ªfase' para todas as sessões
--            com data_exame entre 2026-06-16 e 2026-06-26
-- =====================================================

-- Pré-visualização dos registos afetados
SELECT id, exame_id, fase, data_exame, hora_exame
FROM sessao_exame
WHERE data_exame BETWEEN '2026-06-16' AND '2026-06-26'
ORDER BY data_exame, hora_exame;

-- Executar UPDATE
UPDATE `sessao_exame`
SET `fase` = '1ª fase'
WHERE `data_exame` BETWEEN '2026-06-16' AND '2026-06-26';

-- Confirmar resultado
SELECT id, exame_id, fase, data_exame, hora_exame
FROM sessao_exame
WHERE data_exame BETWEEN '2026-06-16' AND '2026-06-26'
ORDER BY data_exame, hora_exame;
