-- Script para identificar e corrigir dados órfãos antes da migration
-- Execute este script no servidor remoto ANTES de rodar a migration

-- 1. Verificar dados órfãos em horario_aulas.codigo_turma
SELECT 
    ha.id_aula,
    ha.codigo_turma,
    'Órfão - turma não existe' as problema
FROM horario_aulas ha
LEFT JOIN turma t ON ha.codigo_turma = t.codigo
WHERE ha.codigo_turma IS NOT NULL 
  AND t.codigo IS NULL;

-- 2. Verificar se há diferenças de charset/collation
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CHARACTER_SET_NAME,
    COLLATION_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME IN ('horario_aulas', 'turma')
  AND COLUMN_NAME IN ('codigo_turma', 'codigo');

-- 3. OPÇÃO A: Deletar registos órfãos (se não forem necessários)
-- CUIDADO: Isto vai APAGAR dados! Descomente apenas se tiver certeza
-- DELETE ha FROM horario_aulas ha
-- LEFT JOIN turma t ON ha.codigo_turma = t.codigo
-- WHERE ha.codigo_turma IS NOT NULL AND t.codigo IS NULL;

-- 4. OPÇÃO B: Definir codigo_turma como NULL nos órfãos (mais seguro)
-- UPDATE horario_aulas ha
-- LEFT JOIN turma t ON ha.codigo_turma = t.codigo
-- SET ha.codigo_turma = NULL
-- WHERE ha.codigo_turma IS NOT NULL AND t.codigo IS NULL;

-- 5. Verificar se há duplicados em turma.codigo (não pode haver para FK)
SELECT 
    codigo,
    COUNT(*) as total
FROM turma
GROUP BY codigo
HAVING total > 1;

-- 6. Verificar se o índice único já existe
SELECT 
    INDEX_NAME,
    COLUMN_NAME,
    NON_UNIQUE
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'turma'
  AND COLUMN_NAME = 'codigo';
