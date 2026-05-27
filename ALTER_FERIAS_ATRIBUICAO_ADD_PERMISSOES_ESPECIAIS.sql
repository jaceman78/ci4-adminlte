-- Adicionar campos para permissões especiais de férias
-- Permite controle individual por professor

ALTER TABLE ferias_atribuicao
ADD COLUMN permite_marcar_fora_periodo TINYINT(1) NOT NULL DEFAULT 0 
    COMMENT 'Permite marcar férias fora do período configurado (0=Não, 1=Sim)' 
    AFTER observacoes,
ADD COLUMN obriga_totalidade_dias TINYINT(1) NOT NULL DEFAULT 1 
    COMMENT 'Obriga a marcar a totalidade dos dias disponíveis (0=Não, 1=Sim)' 
    AFTER permite_marcar_fora_periodo;

-- Exibir estrutura atualizada
DESCRIBE ferias_atribuicao;

SELECT 'Campos adicionados com sucesso!' AS status;
