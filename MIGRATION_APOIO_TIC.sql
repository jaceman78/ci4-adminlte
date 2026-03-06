-- =====================================================
-- SISTEMA DE APOIO TIC PARA PROVAS EM COMPUTADOR
-- Data: 13/02/2026
-- =====================================================
-- Descrição: Adiciona suporte para convocação de equipa
--            de Apoio TIC para provas realizadas em computador,
--            seguindo o modelo dos Suplentes.
-- =====================================================

-- 1. ADICIONAR 'Apoio TIC' AO ENUM tipo_prova
-- Este tipo permite criar sessões especiais onde técnicos/professores
-- são convocados exclusivamente para apoio técnico informático

ALTER TABLE `exame` 
MODIFY COLUMN `tipo_prova` ENUM(
    'Exame Nacional', 
    'Prova Final', 
    'MODa', 
    'Suplentes',
    'Verificacao Calculadoras',
    'Apoio TIC'
) NOT NULL COMMENT 'Categoria da prova';

-- 2. CRIAR EXAME VIRTUAL PARA APOIO TIC
-- Este exame funciona como "identificador" para as sessões
-- de apoio técnico, permitindo convocações ad-hoc

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
    'TIC-APOIO', 
    'Apoio TIC - Provas em Computador', 
    'Apoio TIC', 
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

-- Listar os exames de apoio TIC criados
SELECT 'Exames de Apoio TIC criados' as verificacao;
SELECT * FROM exame WHERE tipo_prova = 'Apoio TIC';

-- =====================================================
-- NOTAS IMPORTANTES
-- =====================================================
/*
COMO USAR:
----------

1. CRIAR SESSÃO DE APOIO TIC
   - Aceder: Sec. Exames → Sessões de Exame → Nova Sessão
   - Selecionar exame: TIC-APOIO
   - Data/Hora: Mesmo dia das provas em computador
   - Duração: Tempo total da prova + preparação/encerramento
   - Nº Alunos: Deixar em branco (não aplicável)
   - Observações: Ex: "Apoio TIC - Prova de Aplicações Informáticas B"

2. ALOCAR SALA
   - Selecionar sala de computadores onde decorrerá a prova
   - Nº de alunos: pode ser 0 ou qualquer valor

3. CONVOCAR EQUIPA TIC
   - Função: "Apoio TIC" (já existe na tabela convocatoria)
   - Número flexível: Pode adicionar/remover técnicos conforme necessário
   - Sistema ad-hoc: Ajustável até ao dia da prova

4. NO DIA DA PROVA
   - Equipa TIC convocada marca presença
   - Chegam 30min antes para preparar sistemas
   - Garantem funcionamento de:
     * Computadores
     * Software necessário
     * Rede/Internet (se aplicável)
     * Periféricos (impressoras, scanners, etc.)
   - Durante a prova: Resolução de problemas técnicos
   - Após a prova: Backup/export de ficheiros dos alunos

EXEMPLO PRÁTICO:
---------------
Data: 20/06/2026
Prova: Aplicações Informáticas B (12º ano) - 14:00 às 16:30

Sessão Apoio TIC:
- Exame: TIC-APOIO
- Hora: 13:30 (30min antes)
- Duração: 180 minutos (3h)
- Sala: Laboratório de Informática 1
- Equipa: 2-3 técnicos/professores convocados

Cenário: 40 alunos, 2 salas de computadores
Equipa TIC:
- 1 técnico por sala (2 pessoas)
- 1 supervisor geral (opcional)
- Total: 2-3 pessoas

VANTAGENS:
----------
✓ Número flexível de técnicos (indeterminado à partida)
✓ Convocação ad-hoc (pode ajustar até ao dia)
✓ Usa infraestrutura existente (sem código novo complexo)
✓ Integração completa (notificações, presença, relatórios)
✓ Interface familiar para secretariado
✓ Gestão centralizada de apoio técnico

TIPOS DE PROVAS QUE REQUEREM APOIO TIC:
---------------------------------------
- Aplicações Informáticas (A, B)
- Design de Comunicação
- Multimédia
- Geometria Descritiva (CAD)
- Provas com componente digital
- Exames online/digitais

QUERIES ÚTEIS:
-------------

-- Listar sessões de apoio TIC
SELECT 
    se.id,
    se.data_exame,
    se.hora_exame,
    se.duracao_minutos,
    COUNT(c.id) as num_tecnicos_convocados,
    SUM(CASE WHEN c.presenca = 'Presente' THEN 1 ELSE 0 END) as tecnicos_presentes,
    GROUP_CONCAT(DISTINCT s.codigo_sala) as salas
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
LEFT JOIN convocatoria c ON c.sessao_exame_id = se.id
LEFT JOIN sessao_exame_sala ses ON ses.sessao_exame_id = se.id
LEFT JOIN salas s ON s.id = ses.sala_id
WHERE e.tipo_prova = 'Apoio TIC'
GROUP BY se.id
ORDER BY se.data_exame, se.hora_exame;

-- Ver equipa TIC convocada para uma data específica
SELECT 
    c.id,
    u.name as tecnico,
    u.email,
    se.hora_exame,
    c.presenca,
    c.estado_confirmacao,
    c.observacoes,
    GROUP_CONCAT(DISTINCT s.codigo_sala) as salas
FROM convocatoria c
JOIN user u ON u.id = c.user_id
JOIN sessao_exame se ON se.id = c.sessao_exame_id
JOIN exame e ON e.id = se.exame_id
LEFT JOIN sessao_exame_sala ses ON ses.sessao_exame_id = se.id
LEFT JOIN salas s ON s.id = ses.sala_id
WHERE e.tipo_prova = 'Apoio TIC'
    AND se.data_exame = '2026-06-20'
GROUP BY c.id
ORDER BY se.hora_exame, u.name;

-- Estatísticas de apoio TIC por técnico
SELECT 
    u.name as tecnico,
    COUNT(c.id) as total_apoios,
    SUM(CASE WHEN c.presenca = 'Presente' THEN 1 ELSE 0 END) as presencas,
    SUM(CASE WHEN c.presenca = 'Falta' THEN 1 ELSE 0 END) as faltas
FROM convocatoria c
JOIN user u ON u.id = c.user_id
JOIN sessao_exame se ON se.id = c.sessao_exame_id
JOIN exame e ON e.id = se.exame_id
WHERE e.tipo_prova = 'Apoio TIC'
    AND c.funcao = 'Apoio TIC'
GROUP BY u.id, u.name
ORDER BY total_apoios DESC;

-- Sessões TIC com equipa reduzida (alerta)
SELECT 
    se.id,
    se.data_exame,
    se.hora_exame,
    se.observacoes,
    COUNT(ses.id) as num_salas,
    COUNT(DISTINCT c.id) as num_tecnicos,
    CASE 
        WHEN COUNT(DISTINCT c.id) < COUNT(ses.id) THEN 'CRÍTICO - Faltam técnicos'
        WHEN COUNT(DISTINCT c.id) = COUNT(ses.id) THEN 'ATENÇÃO - 1 técnico por sala'
        ELSE 'OK'
    END as status_equipa
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
LEFT JOIN sessao_exame_sala ses ON ses.sessao_exame_id = se.id
LEFT JOIN convocatoria c ON c.sessao_exame_id = se.id
WHERE e.tipo_prova = 'Apoio TIC'
    AND se.data_exame >= CURDATE()
GROUP BY se.id
ORDER BY se.data_exame, se.hora_exame;

ROLLBACK (se necessário):
------------------------
-- Remover exames de apoio TIC
DELETE FROM exame WHERE tipo_prova = 'Apoio TIC';

-- Restaurar ENUM (remover 'Apoio TIC')
ALTER TABLE exame 
MODIFY COLUMN tipo_prova ENUM(
    'Exame Nacional', 
    'Prova Final', 
    'MODa', 
    'Suplentes',
    'Verificacao Calculadoras'
) NOT NULL;

BOAS PRÁTICAS:
-------------

QUANTOS TÉCNICOS CONVOCAR?

Regra Base:
- Mínimo: 1 técnico por sala de computadores
- Recomendado: 1 técnico por 20-25 alunos
- Para provas críticas: 1 supervisor adicional

Exemplo 1: 40 alunos, 2 salas
- Mínimo: 2 técnicos (1 por sala)
- Recomendado: 3 técnicos (1 por sala + supervisor)

Exemplo 2: 80 alunos, 3 salas
- Mínimo: 3 técnicos
- Recomendado: 4 técnicos

TIMING:
- Chegada: 30-45min antes da prova
- Preparação: Ligar computadores, testar software, verificar rede
- Durante prova: Disponibilidade para resolver problemas
- Após prova: 15-30min para backup e encerramento

RESPONSABILIDADES:
1. Pré-prova:
   - Verificar funcionamento de todos os computadores
   - Confirmar que software necessário está instalado
   - Testar ligação à rede/internet
   - Criar pastas de trabalho para alunos
   - Configurar restrições de acesso

2. Durante prova:
   - Resolver problemas técnicos rapidamente
   - Reiniciar computadores se necessário
   - Garantir que não há perda de trabalho
   - Apoiar vigilantes em questões técnicas

3. Pós-prova:
   - Fazer backup dos trabalhos dos alunos
   - Exportar ficheiros para formato requerido
   - Verificar integridade dos ficheiros
   - Limpar pastas temporárias
   - Relatório de incidentes (se houver)
*/

-- =====================================================
-- FIM DA MIGRAÇÃO
-- =====================================================
COMMIT;

SELECT 'Migração concluída com sucesso!' as status;
