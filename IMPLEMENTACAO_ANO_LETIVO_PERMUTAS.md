# Implementação do Campo ano_letivo_id na Tabela Permutas

## 📋 Resumo das Alterações

Foi adicionado o campo `ano_letivo_id` à tabela `permutas` para rastrear automaticamente em que ano letivo cada permuta foi criada.

## 📁 Ficheiros Alterados

### 1. Migration (CodeIgniter)
**Ficheiro:** `app/Database/Migrations/2025-12-10-120000_AddAnoLetivoIdToPermutas.php`
- Adiciona coluna `ano_letivo_id` INT(11) UNSIGNED NULL
- Cria índice `permutas_ano_letivo_idx`
- Adiciona foreign key para `ano_letivo.id_anoletivo` com ON DELETE SET NULL

**Para executar:**
```bash
php spark migrate
```

### 2. Model
**Ficheiro:** `app/Models/PermutasModel.php`
- Adicionado `'ano_letivo_id'` ao array `$allowedFields`

### 3. Controller
**Ficheiro:** `app/Controllers/PermutasController.php`
- No método que cria permutas (linha ~525):
  - Obtém o ano letivo ativo usando `AnoLetivoModel::getAnoAtivo()`
  - Inclui automaticamente `'ano_letivo_id' => $anoLetivoId` em todos os INSERTs

### 4. Scripts SQL

#### a) Script de Atualização (Recomendado)
**Ficheiro:** `ADD_ANO_LETIVO_TO_PERMUTAS.sql`
- Adiciona campo à tabela existente sem perder dados
- Inclui UPDATE opcional para preencher permutas existentes

#### b) Script de Recriação (Atualizado)
**Ficheiro:** `DROP_AND_RECREATE_PERMUTAS.sql`
- Atualizado para incluir o novo campo `ano_letivo_id`
- ⚠️ ATENÇÃO: Este script apaga todos os dados da tabela!

## 🚀 Implementação

### Opção 1: Via Migration (Recomendado)
```bash
php spark migrate
```

### Opção 2: Via Script SQL
Execute o script `ADD_ANO_LETIVO_TO_PERMUTAS.sql` no phpMyAdmin ou MySQL Workbench.

## ✅ Funcionamento

Quando uma permuta é criada:

1. O sistema obtém automaticamente o ano letivo ativo da tabela `ano_letivo` (WHERE `status` = 1)
2. O `id_anoletivo` é inserido no campo `ano_letivo_id` da nova permuta
3. Se não houver ano letivo ativo, o campo fica NULL

### Exemplo de Código:
```php
// Obter ano letivo ativo
$anoLetivoModel = new \App\Models\AnoLetivoModel();
$anoAtivo = $anoLetivoModel->getAnoAtivo();
$anoLetivoId = $anoAtivo['id_anoletivo'] ?? null;

// Criar permuta
$permutaData = [
    'aula_original_id'          => $aulaId,
    'ano_letivo_id'             => $anoLetivoId,  // ← Inserção automática
    'data_aula_original'        => $post['data_aula_original'],
    'data_aula_permutada'       => $post['data_aula_permutada'],
    // ... outros campos
];

$permutaId = $this->permutaModel->insert($permutaData);
```

## 🔍 Consultas Úteis

### Permutas por Ano Letivo:
```sql
SELECT p.*, al.anoletivo
FROM permutas p
LEFT JOIN ano_letivo al ON al.id_anoletivo = p.ano_letivo_id
WHERE al.anoletivo = 2024;
```

### Contar Permutas do Ano Ativo:
```sql
SELECT COUNT(*) as total
FROM permutas p
JOIN ano_letivo al ON al.id_anoletivo = p.ano_letivo_id
WHERE al.status = 1;
```

## 📊 Estrutura da Coluna

- **Nome:** `ano_letivo_id`
- **Tipo:** INT(11) UNSIGNED
- **NULL:** Sim (permite permutas antigas sem ano letivo)
- **Default:** NULL
- **Índice:** Sim (`permutas_ano_letivo_idx`)
- **Foreign Key:** `ano_letivo.id_anoletivo` (ON DELETE SET NULL, ON UPDATE CASCADE)

## ⚠️ Notas Importantes

1. **Permutas Existentes:** Após a migration, permutas antigas terão `ano_letivo_id = NULL`. Use o UPDATE opcional no script SQL se quiser preenchê-las.

2. **Ano Letivo Inativo:** Se não houver nenhum ano letivo com `status = 1`, as novas permutas terão `ano_letivo_id = NULL`.

3. **Integridade Referencial:** Se um ano letivo for eliminado, o campo `ano_letivo_id` das permutas associadas será automaticamente definido como NULL (ON DELETE SET NULL).

## 🎯 Benefícios

- ✅ Rastreamento automático do ano letivo de cada permuta
- ✅ Facilita relatórios e estatísticas por ano letivo
- ✅ Não requer alteração nos formulários (inserção automática)
- ✅ Compatível com permutas existentes (campo NULL)
- ✅ Integridade referencial garantida por foreign key

---

**Data de Implementação:** 10/12/2025
