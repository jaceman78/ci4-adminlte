-- Adiciona campo grupo_mapa_ferias à tabela user
-- Permite separar docentes por grupo no mapa de férias de verão
-- Valores:
--   geral               → Docentes regulares (aparece no mapa geral)
--   direcao             → Membros da Direção (férias geridas pelo Conselho Geral)
--   tecnico_especializado → Técnicos Especializados (mapa próprio)

ALTER TABLE `user`
    ADD COLUMN `grupo_mapa_ferias`
        ENUM('geral', 'direcao', 'tecnico_especializado')
        NOT NULL DEFAULT 'geral'
        AFTER `escola_servico`;
