-- Script para corrigir NIFs inválidos nas turmas
-- Este script corrige campos dir_turma_nif e secretario_nif que estão vazios, zero ou inválidos
-- Execução: Verificar primeiro, depois aplicar a correção

-- 1. VERIFICAR dados problemáticos antes de corrigir
SELECT 
    id_turma,
    codigo,
    nome,
    dir_turma_nif,
    secretario_nif
FROM turma
WHERE 
    dir_turma_nif = '' 
    OR dir_turma_nif = '0' 
    OR secretario_nif = '' 
    OR secretario_nif = '0'
ORDER BY id_turma;

-- 2. VERIFICAR utilizadores com NIF = 0 ou vazio (possível causa do problema)
SELECT 
    id,
    NIF,
    name,
    email,
    level
FROM user
WHERE 
    NIF = '' 
    OR NIF = '0' 
    OR NIF = 0
    OR NIF IS NULL
ORDER BY id;

-- 3. CORRIGIR: Definir dir_turma_nif como NULL quando for vazio ou '0'
UPDATE turma 
SET dir_turma_nif = NULL 
WHERE 
    dir_turma_nif = '' 
    OR dir_turma_nif = '0';

-- 4. CORRIGIR: Definir secretario_nif como NULL quando for vazio ou '0'
UPDATE turma 
SET secretario_nif = NULL 
WHERE 
    secretario_nif = '' 
    OR secretario_nif = '0';

-- 5. VERIFICAR resultado após correção
SELECT 
    id_turma,
    codigo,
    nome,
    dir_turma_nif,
    secretario_nif
FROM turma
WHERE 
    dir_turma_nif IS NULL 
    OR secretario_nif IS NULL
ORDER BY id_turma;

-- 6. OPCIONAL: Se houver utilizador com NIF inválido, pode corrigi-lo ou removê-lo
-- CUIDADO: Apenas executar se tiver certeza de que este utilizador não deve existir
-- ou deve ter um NIF válido atribuído

-- Verificar se há turmas referenciando este utilizador problemático:
-- SELECT t.*, u.name, u.email 
-- FROM turma t
-- LEFT JOIN user u ON (u.NIF = t.dir_turma_nif OR u.NIF = t.secretario_nif)
-- WHERE u.NIF = '0' OR u.NIF = 0 OR u.NIF = '';

-- NOTA: Não execute UPDATE ou DELETE em user sem confirmação!
