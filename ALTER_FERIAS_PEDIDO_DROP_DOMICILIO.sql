-- Remoção das colunas domicilio_rua e domicilio_localidade da tabela ferias_pedido
-- A morada passa a ser guardada em observacoes_professor (campo já existente),
-- consistente com o que acontece na marcação inicial de férias.

ALTER TABLE `ferias_pedido`
    DROP COLUMN IF EXISTS `domicilio_rua`,
    DROP COLUMN IF EXISTS `domicilio_localidade`;
