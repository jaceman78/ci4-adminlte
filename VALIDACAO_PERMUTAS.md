# Validação de Permutas - Prevenção de Múltiplas Aceitações

## Problema Identificado
Era possível que dois professores substitutos aceitassem permuta para a mesma convocatória, criando conflito.

**Exemplo do problema:**
1. Professor A cria permuta para a Convocatória #10, pedindo ao Substituto B
2. Professor A cria outra permuta para a Convocatória #10, pedindo ao Substituto C
3. Substituto B aceita ✅
4. Substituto C também aceita ✅ ← **PROBLEMA!**

## Soluções Implementadas

### 1. Validação na Aceitação
**Arquivo:** `app/Controllers/PermutasVigilanciaController.php`

#### Método `aceitar()`
- ✅ Verifica se já existe outra permuta aceite/validada para a mesma convocatória
- ❌ Bloqueia aceitação se já houver permuta aceite
- 🚫 Cancela automaticamente permutas pendentes após aceitação

```php
// Validação antes de aceitar
$permutaJaAceite = $this->permutasModel
    ->where('convocatoria_id', $permuta['convocatoria_id'])
    ->where('id !=', $id)
    ->whereIn('estado', ['ACEITE_SUBSTITUTO', 'VALIDADO_SECRETARIADO'])
    ->first();

if ($permutaJaAceite) {
    return $this->response->setJSON([
        'status' => 'error', 
        'message' => 'Já existe outra permuta aceite para esta convocatória'
    ]);
}
```

#### Método `responder()`
- ✅ Mesma validação quando `aceitar == true`
- 🚫 Cancela permutas pendentes após aceitação

```php
// Se aceitou, cancelar automaticamente outras permutas pendentes
if ($aceitar) {
    $this->permutasModel
        ->where('convocatoria_id', $permuta['convocatoria_id'])
        ->where('id !=', $id)
        ->where('estado', 'PENDENTE')
        ->set(['estado' => 'CANCELADO'])
        ->update();
}
```

### 2. Cancelamento Automático
Quando uma permuta é aceite, todas as outras permutas **PENDENTE** para a mesma convocatória são automaticamente canceladas.

**Estados possíveis:**
- `PENDENTE` - Aguardando resposta do substituto
- `ACEITE_SUBSTITUTO` - Substituto aceitou
- `RECUSADO_SUBSTITUTO` - Substituto recusou
- `VALIDADO_SECRETARIADO` - Secretariado validou
- `REJEITADO_SECRETARIADO` - Secretariado rejeitou
- `CANCELADO` - Cancelado automaticamente ou pelo professor

### 3. Correção de Dados Antigos
**Script:** `corrigir_permutas_inconsistentes.php`

Corrige permutas com `estado = NULL`, atribuindo estado correto baseado em:
- `validado_secretariado = 1` → `VALIDADO_SECRETARIADO`
- `substituto_aceitou = 1` → `ACEITE_SUBSTITUTO`
- `substituto_aceitou = 0` → `RECUSADO_SUBSTITUTO`
- Caso contrário → `PENDENTE`

## Scripts de Teste e Validação

### `testar_validacao_permutas.php`
Verifica:
- ✅ Convocatórias com múltiplas permutas ativas
- ✅ Convocatórias com múltiplas permutas aceites
- 📊 Estatísticas gerais por estado

### `verificar_permutas_duplicadas.php`
Lista permutas agrupadas por convocatória, identificando problemas.

## Comportamento Esperado

### Cenário Normal
1. Professor A pede permuta ao Substituto B → **PENDENTE**
2. Substituto B aceita → **ACEITE_SUBSTITUTO**
3. Secretariado valida → **VALIDADO_SECRETARIADO**

### Cenário com Múltiplos Pedidos
1. Professor A pede permuta ao Substituto B → **PENDENTE** (Permuta #1)
2. Professor A pede permuta ao Substituto C → **PENDENTE** (Permuta #2)
3. Substituto B aceita → **ACEITE_SUBSTITUTO** (Permuta #1)
   - Permuta #2 automaticamente → **CANCELADO**
4. Substituto C tenta aceitar → ❌ **Erro: "Já existe outra permuta aceite para esta convocatória"**

## Testes Realizados
✅ Dados corrigidos: 2 permutas com estado NULL
✅ Nenhuma convocatória com múltiplas permutas aceites
✅ Validação implementada em `aceitar()` e `responder()`
✅ Cancelamento automático de permutas pendentes funcionando

## Mensagens ao Utilizador
- **Erro ao aceitar:** "Já existe outra permuta aceite para esta convocatória"
- **Cancelamento automático:** Silencioso, permutas ficam com estado `CANCELADO`

---
**Data:** 12 de Fevereiro de 2026  
**Implementado por:** GitHub Copilot (Claude Sonnet 4.5)
