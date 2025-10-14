# Teste de Correções - Equipamentos

## Problemas Corrigidos:

### 1. ❌ Erro: `Unknown column 'equipamentos.sala_id' in 'on clause'`

**Causa:** Método `getEquipamento()` ainda fazia JOIN usando `equipamentos.sala_id`

**Solução:** Atualizado para usar `equipamentos_sala`:
```php
->join('equipamentos_sala es', 'es.equipamento_id = equipamentos.id AND es.data_saida IS NULL', 'left')
->join('salas', 'salas.id = es.sala_id', 'left')
```

### 2. ❌ Erro em `getBySala()`

**Causa:** Mesmo problema - JOIN com coluna inexistente

**Solução:** Atualizado para buscar via `equipamentos_sala`:
```php
->join('equipamentos_sala es', 'es.equipamento_id = equipamentos.id AND es.data_saida IS NULL', 'inner')
->where('es.sala_id', $salaId)
```

### 3. ⚠️ Métodos Legados

**Problema:** `create()` e `update()` tentavam gravar `sala_id`

**Solução:**
- Removido `sala_id` dos dados
- Adicionado `@deprecated` em `create()`
- `update()` agora apenas atualiza dados do equipamento
- Para atribuir/mudar sala, usar `createWithSala()`, `atribuirSala()` ou `editarSala()`

## Testes Recomendados:

### ✅ Teste 1: Ver Equipamento
**URL:** `http://localhost:8080/equipamentos`
**Ação:** Clicar no botão "Ver" (ícone olho) de um equipamento
**Esperado:** Modal abre mostrando detalhes sem erros

### ✅ Teste 2: Editar Equipamento  
**URL:** `http://localhost:8080/equipamentos`
**Ação:** Clicar no botão "Editar" (ícone lápis)
**Esperado:** Modal abre com dados preenchidos, incluindo escola e sala se existir

### ✅ Teste 3: Criar Novo Equipamento SEM Sala
**Ação:** 
1. Clicar "Novo Equipamento"
2. Preencher tipo, marca, modelo
3. Estado: "Por Atribuir"
4. NÃO selecionar escola/sala
5. Guardar

**Esperado:** Equipamento criado, aparece na lista com "Sem atribuição"

### ✅ Teste 4: Criar Novo Equipamento COM Sala
**Ação:**
1. Clicar "Novo Equipamento"
2. Preencher dados
3. Selecionar Escola → Sala
4. Preencher motivo
5. Guardar

**Esperado:** 
- Equipamento criado
- Registro em `equipamentos_sala` criado
- Aparece na lista com escola e sala

### ✅ Teste 5: Atribuir Sala a Equipamento Existente
**Pré-requisito:** Ter equipamento sem sala
**Ação:**
1. Clicar dropdown "Gerir Localização" → "Atribuir Sala"
2. Selecionar Escola → Sala
3. Motivo: "Teste de atribuição"
4. Confirmar

**Esperado:**
- Sala atribuída
- Lista atualizada mostrando escola/sala
- Histórico registado

### ✅ Teste 6: Mudar Sala de Equipamento
**Pré-requisito:** Ter equipamento COM sala
**Ação:**
1. Clicar dropdown "Gerir Localização" → "Mudar Sala"
2. Ver sala atual
3. Selecionar nova Escola → Sala
4. Motivo: "Transferência de teste"
5. Confirmar

**Esperado:**
- Registro antigo fechado (`data_saida` preenchida)
- Novo registro criado
- Lista atualizada
- Histórico mostra ambas as salas

### ✅ Teste 7: Remover Sala
**Pré-requisito:** Ter equipamento COM sala
**Ação:**
1. Clicar dropdown "Gerir Localização" → "Remover Sala"
2. Ver sala atual
3. Motivo: "Equipamento para reparação"
4. Confirmar remoção

**Esperado:**
- Registro fechado (`data_saida` preenchida)
- Lista mostra "Sem atribuição"
- Histórico mantido

### ✅ Teste 8: Ver Histórico
**Pré-requisito:** Equipamento com movimentações
**Ação:**
1. Clicar dropdown "Gerir Localização" → "Histórico"

**Esperado:**
- Modal mostra timeline
- Todas as salas onde esteve
- Datas entrada/saída
- Motivos
- Quem moveu

### ✅ Teste 9: DataTable
**Ação:** Visualizar listagem
**Esperado:**
- Coluna "Escola" mostra nome ou "Sem atribuição"
- Coluna "Sala" mostra código ou "Sem atribuição"
- Dropdown "Gerir Localização" adapta-se ao estado:
  - COM sala: "Mudar Sala" e "Remover Sala"
  - SEM sala: "Atribuir Sala"
  - Sempre: "Histórico"

### ✅ Teste 10: API - getBySala
**URL:** `http://localhost:8080/equipamentos/getBySala/1`
**Esperado:** JSON com equipamentos atualmente na sala ID 1

## Verificação de Base de Dados:

### Tabela `equipamentos`
```sql
-- Verificar que sala_id não existe mais
DESCRIBE equipamentos;
-- Colunas esperadas: id, tipo_id, marca, modelo, numero_serie, estado, data_aquisicao, observacoes, created_at, updated_at
```

### Tabela `equipamentos_sala`
```sql
-- Verificar registros
SELECT * FROM equipamentos_sala;
-- Colunas: id, equipamento_id, sala_id, data_entrada, data_saida, motivo_movimentacao, user_id, observacoes, created_at, updated_at
```

### Query para ver equipamentos com salas atuais:
```sql
SELECT 
    e.id,
    e.marca,
    e.modelo,
    e.numero_serie,
    s.codigo_sala,
    esc.nome as escola,
    es.data_entrada,
    es.motivo_movimentacao
FROM equipamentos e
LEFT JOIN equipamentos_sala es ON es.equipamento_id = e.id AND es.data_saida IS NULL
LEFT JOIN salas s ON s.id = es.sala_id
LEFT JOIN escolas esc ON esc.id = s.escola_id
ORDER BY e.id DESC;
```

## Status: ✅ TODAS AS CORREÇÕES APLICADAS

Todas as referências a `equipamentos.sala_id` foram removidas ou atualizadas para usar `equipamentos_sala`.
