-- =====================================================
-- SISTEMA DE VERIFICAÇÃO DE CALCULADORAS
-- Data: 13/02/2026
-- =====================================================
-- Descrição: Adiciona suporte para convocação de professores
--            para verificar calculadoras nos exames de Matemática
--            e Física-Química, seguindo o modelo dos Suplentes.
-- =====================================================

-- 1. ADICIONAR 'Verificação Calculadoras' AO ENUM tipo_prova
-- Este tipo permite criar sessões especiais onde os professores
-- são convocados exclusivamente para validar calculadoras

ALTER TABLE `exame` 
MODIFY COLUMN `tipo_prova` ENUM(
    'Exame Nacional', 
    'Prova Final', 
    'MODa', 
    'Suplentes',
    'Verificação Calculadoras'
) NOT NULL COMMENT 'Categoria da prova';

-- 2. CRIAR EXAMES VIRTUAIS PARA VERIFICAÇÃO DE CALCULADORAS
-- Estes exames funcionam como "identificadores" para as sessões
-- de verificação, permitindo convocações ad-hoc

INSERT INTO `exame` (
    `codigo_prova`, 
    `nome_prova`, 
    `tipo_prova`, 
    `ano_escolaridade`, 
    `ativo`,
    `created_at`,
    `updated_at`
) 
VALUES 
(
    'CALC-VER', 
    'Verificação de Calculadoras', 
    'Verificação Calculadoras', 
    NULL, 
    1,
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    nome_prova = VALUES(nome_prova),
    tipo_prova = VALUES(tipo_prova),
    ano_escolaridade = VALUES(ano_escolaridade),
    ativo = VALUES(ativo),
    updated_at = NOW();

-- =====================================================
-- VERIFICAÇÕES DE INTEGRIDADE
-- =====================================================

-- Verificar se o tipo_prova foi adicionado corretamente
SELECT 'Verificação do ENUM tipo_prova' as verificacao;
SELECT COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'exame' 
    AND COLUMN_NAME = 'tipo_prova';

-- Listar os exames de verificação de calculadoras criados
SELECT 'Exames de Verificação de Calculadoras criados' as verificacao;
SELECT * FROM exame WHERE tipo_prova = 'Verificação Calculadoras';

-- =====================================================
-- NOTAS IMPORTANTES
-- =====================================================
/*
COMO USAR:
----------

1. CRIAR SESSÃO DE VERIFICAÇÃO
   - Aceder: Sec. Exames → Sessões de Exame → Nova Sessão
   - Selecionar exame: CALC-VER
   - Data/Hora: Mesmo dia dos exames de Matemática/Física
   - Duração: Tempo necessário para verificar todas as calculadoras
   - Nº Alunos: Deixar em branco (não aplicável)
   - Observações: Ex: "Verificação de calculadoras - Exames de Matemática 12º Ano"

2. ALOCAR SALA
   - Selecionar local onde será feita a verificação
   - Nº de alunos: pode ser 0 ou qualquer valor

3. CONVOCAR PROFESSORES
   - Função: "Verificar Calculadoras" (já existe na tabela convocatoria)
   - Número flexível: Pode adicionar/remover professores conforme necessário
   - Sistema ad-hoc: Ajustável até ao dia do exame

4. NO DIA DO EXAME
   - Professores convocados verificam calculadoras antes do início
   - Marcam presença no sistema
   - Preenchem observações se necessário

EXEMPLO PRÁTICO:
---------------
Data: 15/06/2026
Exames: Matemática A (09:00), Física-Química (14:00)

Sessão 1: Verificação Manhã
- Exame: CALC-VER
- Hora: 08:00 (1h antes do exame)
- Duração: 60 minutos
- Professores: 3-5 professores convocados

Sessão 2: Verificação Tarde
- Exame: CALC-VER
- Hora: 13:00 (1h antes do exame)
- Duração: 60 minutos
- Professores: 2-4 professores convocados

VANTAGENS:
----------
✓ Número flexível de professores (indeterminado à partida)
✓ Convocação ad-hoc (pode ajustar até ao dia)
✓ Usa infraestrutura existente (sem código novo complexo)
✓ Integração completa (notificações, presença, relatórios)
✓ Interface familiar para secretariado
✓ Distinção clara entre vigilância e verificação

QUERIES ÚTEIS:
-------------

-- Listar sessões de verificação de calculadoras
SELECT 
    se.id,
    se.data_exame,
    se.hora_exame,
    se.duracao_minutos,
    COUNT(c.id) as num_professores_convocados,
    SUM(CASE WHEN c.presenca = 'Presente' THEN 1 ELSE 0 END) as professores_presentes
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
LEFT JOIN convocatoria c ON c.sessao_exame_id = se.id
WHERE e.tipo_prova = 'Verificação Calculadoras'
GROUP BY se.id
ORDER BY se.data_exame, se.hora_exame;

-- Ver professores convocados para verificação numa data específica
SELECT 
    c.id,
    u.name as professor,
    u.email,
    se.hora_exame,
    c.presenca,
    c.estado_confirmacao,
    c.observacoes
FROM convocatoria c
JOIN user u ON u.id = c.user_id
JOIN sessao_exame se ON se.id = c.sessao_exame_id
JOIN exame e ON e.id = se.exame_id
WHERE e.tipo_prova = 'Verificação Calculadoras'
    AND se.data_exame = '2026-06-15'
ORDER BY se.hora_exame, u.name;

ROLLBACK (se necessário):
------------------------
-- Remover exames de verificação
DELETE FROM exame WHERE tipo_prova = 'Verificação Calculadoras';

-- Restaurar ENUM (remover 'Verificação Calculadoras')
ALTER TABLE exame 
MODIFY COLUMN tipo_prova ENUM(
    'Exame Nacional', 
    'Prova Final', 
    'MODa', 
    'Suplentes'
) NOT NULL;
*/

-- =====================================================
-- FIM DA MIGRAÇÃO
-- =====================================================
COMMIT;

SELECT 'Migração concluída com sucesso!' as status;
