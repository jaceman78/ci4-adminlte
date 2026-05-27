# Implementação: Dias Gozados no Ano Anterior

**Data:** 11 de Março de 2026  
**Objetivo:** Melhorar o processo de atribuição de férias com cálculo automático do ajuste baseado nos dias gozados no ano anterior

## 📋 Requisitos

- Campo para inserir dias gozados no ano anterior na modal de atribuição
- Cálculo automático do ajuste: `dias_ano_anterior - dias_gozados_anterior`
- Preencher automaticamente no PDF o campo "Nº de dias de licença para férias gozadas no ano anterior"
- Guardar informação na base de dados

## 🗄️ Alterações na Base de Dados

### Nova Coluna em `ferias_atribuicao`

```sql
ALTER TABLE `ferias_atribuicao`
ADD COLUMN `dias_gozados_anterior` INT NOT NULL DEFAULT 0 
COMMENT 'Número de dias efetivamente gozados no ano anterior'
AFTER `dias_ajuste`;
```

**Ficheiro:** `ALTER_FERIAS_ATRIBUICAO_ADD_DIAS_GOZADOS.sql`

## 📁 Ficheiros Alterados

### 1. **FeriasController.php**

#### Método `atribuir()`
- Busca ano letivo anterior
- Obtém atribuição do ano anterior para cada professor
- Calcula dias já gozados no ano anterior (dos pedidos aprovados)
- Passa esses dados para a view

#### Método `salvarAtribuicao()`
- Recebe novo campo `dias_gozados_anterior` do POST
- Passa para o Model ao guardar

#### Método `gerarPDFFerias()`
- Usa `dias_gozados_anterior` da atribuição atual
- Fallback: calcula dos pedidos aprovados se campo não estiver preenchido
- Passa para o PDF como `$pedido['dias_gozados_anterior']`

### 2. **FeriasAtribuicaoModel.php**

#### Método `atribuirFerias()`
- Novo parâmetro: `$diasGozadosAnterior = 0`
- Inclui campo no INSERT/UPDATE

### 3. **secretaria_atribuir.php (View)**

#### Botão de  Edição
- Passa novos parâmetros: `atribuicaoAnoAnterior` e `diasGozadosAnterior`

#### Função JavaScript `editarAtribuicao()`
- Recebe e processa dados do ano anterior
- Mostra alert informativo com:
  - Dias atribuídos no ano anterior
  - Dias gozados no ano anterior
  - Diferença (dias por gozar)
- Campo "Dias Gozados no Ano Anterior":
  - Pré-preenchido se houver dados guardados
  - Desabilitado se não houver atribuição do ano anterior
  - Dispara cálculo automático ao mudar (`onchange`)
- Campo "Ajuste":
  - Calculado automaticamente
  - Readonly (não editável manualmente)
  - Background cinza para indicar que é calculado

#### Nova Função JavaScript `calcularAjuste()`
- Calcula: `ajuste = diasAnoAnterior - diasGozados`
- Atualiza campo automaticamente

### 4. **documento_pdf.php**
- Já usava variável `$pedido['dias_gozados_anterior']`
- Sem alterações necessárias

## 🎯 Fluxo de Funcionamento

### 1. Atribuição de Férias

```
1. Secretaria acede a /ferias/atribuir
2. Controller busca dados do ano anterior de cada professor
3. Ao clicar "Editar Atribuição":
   ├─ Se houver ano anterior:
   │  ├─ Mostra alert com informação
   │  ├─ Campo "Dias Gozados Anterior" habilitado
   │  └─ Cálculo automático do ajuste
   └─ Se não houver ano anterior:
      └─ Campo desabilitado
4. Ao alterar "Dias Gozados":
   └─ Recalcula ajuste automaticamente
5. Ao salvar:
   └─ Guarda dias_gozados_anterior na BD
```

### 2. Geração de PDF

```
1. Ao aprovar pedido ou regenerar PDF:
2. Controller busca atribuição atual
3. Verifica se tem dias_gozados_anterior:
   ├─ Se SIM: Usa valor guardado
   └─ Se NÃO: Calcula dos pedidos aprovados (fallback)
4. Passa para template PDF
5. PDF preenche automaticamente:
   ├─ "Nº de dias de licença para férias a que teve direito no ano anterior"
   └─ "Nº de dias de licença para férias gozadas no ano anterior"
```

## ✅ Validações

- Campo desabilitado se não houver atribuição do ano anterior
- Cálculo automático previne erros manuais
- Fallback garante compatibilidade com dados antigos
- Valores min/max definidos nos inputs (0-50)

## 📊 Exemplo Prático

**Cenário:** Professor António tinha 22 dias no ano 2024/2025 e gozou 20 dias.

### Na Atribuição 2025/2026:

```
╔════════════════════════════════════════════╗
║  Ano Anterior:                             ║
║  Dias atribuídos: 22                       ║
║  Dias gozados: 20                          ║
║  Diferença (por gozar): 2                  ║
╚════════════════════════════════════════════╝

Dias Base: [22]
Dias Gozados no Ano Anterior: [20]
Ajuste (dias ano anterior): [2] (calculado)
Dias Extra: [0]
```

**Resultado:** Professor terá 24 dias em 2025/2026 (22 base + 2 ajuste)

### No PDF:

```
Nº de dias de licença para férias a que teve direito no ano anterior: 22
Nº de dias de licença para férias gozadas no ano anterior: 20
```

## 🔧 Manutenção

### Para executar a migration:

```sql
SOURCE ALTER_FERIAS_ATRIBUICAO_ADD_DIAS_GOZADOS.sql;
```

Ou via phpMyAdmin/HeidiSQL:
1. Abrir ficheiro `ALTER_FERIAS_ATRIBUICAO_ADD_DIAS_GOZADOS.sql`
2. Executar SQL

### Verificar coluna criada:

```sql
DESCRIBE ferias_atribuicao;
```

Deve aparecer:
- `dias_gozados_anterior` INT NOT NULL DEFAULT 0

## 📝 Notas Importantes

1. **Compatibilidade:** Sistema funciona mesmo sem executar migration (fallback)
2. **Dados Antigos:** Atribuições antigas calcularão automaticamente dos pedidos
3. **Cálculo Automático:** Ajuste não pode ser editado manualmente (previne erros)
4. **Validação:** Campo desabilitado quando não há dados do ano anterior

## 🚀 Próximos Passos

1. ExecutarALTER SQL para adicionar coluna
2. Testar atribuição com/sem dados do ano anterior
3. Verificar PDF gerado
4. Validar cálculos automáticos

---

**Status:** ✅ Implementação Completa  
**Testado:** ⏳ Aguarda execução da migration
