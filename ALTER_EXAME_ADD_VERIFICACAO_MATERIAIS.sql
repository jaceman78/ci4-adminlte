-- Migração: Adicionar 'Verificação de Materiais' ao ENUM tipo_prova de exame
-- e adicionar 'Verificar Materiais' ao ENUM funcao de convocatoria
-- Nota: o valor 'Verificação de Materiais' em exame.tipo_prova pode já ter sido
-- adicionado manualmente pelo utilizador. Executar apenas se necessário.

-- 1. Adicionar 'Verificação de Materiais' ao ENUM tipo_prova da tabela exame
ALTER TABLE exame 
MODIFY COLUMN tipo_prova ENUM(
    'Exame Nacional',
    'Prova Final',
    'MODa',
    'Suplentes',
    'Verificacao Calculadoras',
    'Apoio TIC',
    'Estrutura de Apoio',
    'Verificação de Materiais'
) NOT NULL;

-- 2. Adicionar 'Verificar Materiais' ao ENUM funcao da tabela convocatoria
ALTER TABLE convocatoria 
MODIFY COLUMN funcao ENUM(
    'Vigilante',
    'Suplente',
    'Coadjuvante',
    'Júri',
    'Verificar Calculadoras',
    'Apoio TIC',
    'Estrutura de Apoio',
    'Verificar Materiais'
) NOT NULL;

-- 3. (Opcional) Inserir o exame virtual VER-MAT se não existir ainda
INSERT IGNORE INTO exame (codigo_prova, nome_prova, tipo_prova, ano_escolaridade, ativo)
VALUES ('VER-MAT', 'Verificação de Materiais', 'Verificação de Materiais', NULL, 1);
