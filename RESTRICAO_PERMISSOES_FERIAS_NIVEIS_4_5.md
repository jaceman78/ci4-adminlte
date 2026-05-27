# 🔐 RESTRIÇÃO DE PERMISSÕES - FÉRIAS

## Alteração Implementada

### Problema
Utilizadores com nível 4 (Secretariado de Exames) e nível 5 (Técnico) tinham acesso às funcionalidades de secretaria e configurações do sistema de férias, quando deveriam ter apenas acesso às funcionalidades de professor (marcar férias).

### Solução
Restringir o acesso às seções administrativas apenas para níveis 3 (Secretaria) e 6+ (Administração), excluindo os níveis 4 e 5.

---

## Níveis de Acesso

### 📊 Hierarquia de Níveis
```
Nível 1: Professor (básico)
Nível 2: Professor (coordenador)
Nível 3: Secretaria        ✅ Acesso administrativo
Nível 4: Secretariado Exames ❌ SEM acesso administrativo (apenas professor)
Nível 5: Técnico            ❌ SEM acesso administrativo (apenas professor)
Nível 6+: Administração     ✅ Acesso administrativo
```

### ✅ Acesso de TODOS os níveis (1-6+)
- 🏠 **Minhas Férias** - Dashboard pessoal
- ➕ **Marcar Período** - Submeter pedidos de férias
- 📜 **Histórico de Pedidos** - Ver seus próprios pedidos
- 📄 **Meus Documentos** - Ver/fazer upload de documentos assinados

### 🔒 Acesso RESTRITO (apenas níveis 3 e 6+)

#### Secretaria
- 📊 **Dashboard Secretaria**
- ✏️ **Atribuir Dias** - Atribuir dias de férias aos funcionários
- ⏳ **Pedidos Pendentes** - Aprovar/rejeitar pedidos
- 📋 **Todos os Pedidos** - Visualização geral de todos os pedidos
- 📈 **Relatórios** - Estatísticas e relatórios do sistema

#### Configurações
- 🗓️ **Gestão de Feriados** - Adicionar/remover feriados
- 👥 **Gestão de Utilizadores** - Editar dados de utilizadores
- 📝 **Logs do Sistema** - Visualizar logs de ações

---

## Alterações Técnicas

### 1. Sidebar - Menu de Navegação
**Ficheiro:** `app/Views/layout/partials/sidebar.php`

**Antes:**
```php
<?php if ($userLevel >= 3): ?>
<!-- MENU SERVIÇOS ADMINISTRATIVOS (Level >= 3) -->
```

**Depois:**
```php
<?php if ($userLevel == 3 || $userLevel >= 6): ?>
<!-- MENU SERVIÇOS ADMINISTRATIVOS (Level 3 ou >= 6) -->
<!-- Exclui níveis 4 e 5 que são professores -->
```

### 2. Controller - Validações de Acesso
**Ficheiro:** `app/Controllers/FeriasController.php`

**Antes:**
```php
if (!$userData || $userData['level'] < 3) {
    return redirect()->to('/dashboard')->with('error', 'Acesso negado');
}
```

**Depois:**
```php
// Apenas níveis 3 e >= 6 (exclui 4 e 5 que são professores)
if (!$userData || $userData['level'] < 3 || in_array($userData['level'], [4, 5])) {
    return redirect()->to('/dashboard')->with('error', 'Acesso negado');
}
```

### 3. Métodos Atualizados

Todos os métodos abaixo foram atualizados com a nova validação:

#### Visualização
- `secretaria()` - Dashboard da secretaria
- `pedidosPendentes()` - Pedidos pendentes
- `todosPedidos()` - Todos os pedidos
- `relatorios()` - Relatórios
- `detalhesPedido($pedidoId)` - Detalhes de um pedido específico

#### Gestão de Férias
- `atribuir()` - Página de atribuição de dias
- `salvarAtribuicao()` - Salvar atribuição de dias (AJAX)
- `enviarEmailAtribuicao($atribuicaoId)` - Enviar email de notificação

#### Aprovação/Rejeição
- `aprovarPedido($pedidoId)` - Aprovar pedido
- `rejeitarPedido($pedidoId)` - Rejeitar pedido

#### Documentos
- `regenerarPDF($pedidoId)` - Regenerar PDF de pedido

#### Configurações - Utilizadores
- `utilizadores()` - Gestão de utilizadores
- `atualizarUtilizador()` - Atualizar dados de utilizador (AJAX)

#### Configurações - Feriados
- `feriados()` - Gestão de feriados
- `adicionarFeriado()` - Adicionar feriado (AJAX)
- `removerFeriado($id)` - Remover feriado (AJAX)
- `gerarFeriadosAno()` - Gerar feriados de um ano (AJAX)

#### Logs
- `logs()` - Visualização de logs do sistema

---

## 🧪 Testes

### Cenário 1: Utilizador Nível 4 (Secretariado Exames)
1. ✅ Pode aceder a `/ferias` (dashboard pessoal)
2. ✅ Pode aceder a `/ferias/marcar` (marcar férias)
3. ✅ Pode aceder a `/ferias/meus-pedidos` (histórico)
4. ✅ Pode aceder a `/ferias/meus-documentos` (documentos)
5. ❌ **Não** vê menu "Secretaria" na sidebar
6. ❌ **Não** vê menu "Configurações" na sidebar
7. ❌ Redireccionado para dashboard se tentar aceder a `/ferias/atribuir`
8. ❌ Redireccionado para dashboard se tentar aceder a `/ferias/feriados`

### Cenário 2: Utilizador Nível 5 (Técnico)
Mesmo comportamento do Cenário 1

### Cenário 3: Utilizador Nível 3 (Secretaria)
1. ✅ Pode aceder a todas as páginas de professor
2. ✅ Vê menu "Secretaria" na sidebar
3. ✅ Vê menu "Configurações" na sidebar
4. ✅ Pode aceder a todas as funcionalidades administrativas

### Cenário 4: Utilizador Nível 6+ (Administração)
Mesmo comportamento do Cenário 3

---

## 📝 Notas Importantes

1. **Níveis 4 e 5 são professores especiais:**
   - Têm funções específicas (exames, técnico)
   - Mas no sistema de férias, são tratados como professores normais
   - Apenas podem marcar suas próprias férias

2. **Segurança:**
   - Validação no **frontend** (sidebar) - esconde menus
   - Validação no **backend** (controller) - bloqueia acesso direto via URL
   - Mesmo que tentem aceder diretamente à URL, serão bloqueados

3. **Compatibilidade:**
   - Não afeta utilizadores de nível 1, 2 (professores)
   - Não afeta utilizadores de nível 3 (secretaria)
   - Não afeta utilizadores de nível 6+ (administração)
   - Apenas restringe níveis 4 e 5

---

## 🔄 Rollback (se necessário)

Se for necessário reverter esta alteração:

### 1. Sidebar
```php
// Reverter para:
<?php if ($userLevel >= 3): ?>
```

### 2. Controller
```php
// Reverter para:
if (!$userData || $userData['level'] < 3) {
```

Ou simplesmente fazer `git revert` do commit destas alterações.

---

**Data de Implementação:** 2026-03-12  
**Versão:** 1.0  
**Status:** ✅ Implementado e Testado
