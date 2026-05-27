# 🔍 DEBUG - Erro 400 em Produção no Submeter Férias

## Problema
Erro 400 ao submeter pedido de férias em **https://escoladigital.cloud/ferias/marcar** mas funciona localmente.

## Alterações Implementadas

### 1. Logs Detalhados no Backend
Adicionados logs com prefixo `[DEBUG FERIAS]` em cada ponto de validação do método `submeterPedido()`:
- Autenticação
- Validação NIF
- Ano letivo ativo
- Dados recebidos (JSON)
- Períodos validados
- Verificação saldo
- Criação do pedido

### 2. Logs no Frontend (JavaScript)
Console logs para debug:
- Dados enviados
- Status HTTP
- Resposta do servidor (JSON e texto)

### 3. Endpoint de Debug
Criado endpoint temporário: **`/ferias/debug-status`**

---

## 📋 INSTRUÇÕES DE DEBUG

### PASSO 1: Verificar Estado do Sistema

Acesse no navegador (em produção):
```
https://escoladigital.cloud/ferias/debug-status
```

Irá retornar algo como:
```json
{
  "timestamp": "2026-03-12 10:30:00",
  "timezone": "Europe/Lisbon",
  "authenticated": true,
  "user_nif": 123456789,
  "ano_ativo": {
    "id_anoletivo": 2,
    "anoletivo": 2025,
    "status": 1
  },
  "helper_loaded": true,
  "php_version": "8.1.2",
  "ci_version": "4.3.0",
  "teste_validacao": {
    "valido": true,
    "dias_uteis": 8
  }
}
```

**Verificar:**
- ✅ `authenticated: true` - se está autenticado
- ✅ `user_nif` - se tem NIF
- ✅ `ano_ativo` - se existe ano letivo ativo (status=1)
- ✅ `helper_loaded: true` - se o helper está carregado
- ✅ `teste_validacao.valido: true` - se a função de validação funciona

---

### PASSO 2: Verificar Console do Navegador

1. Abra **Chrome DevTools** (F12) → aba **Console**
2. Tente submeter um pedido de férias
3. Procure por:

```javascript
[DEBUG] Enviando dados: {...}
[DEBUG] Erro xhr: {...}
[DEBUG] Status: 400
[DEBUG] Response: "mensagem de erro"
[DEBUG] ResponseJSON: {...}
```

**Copie toda a informação do console** e analise.

---

### PASSO 3: Verificar Logs do Servidor

Conecte via SSH ao servidor:

```bash
# Opção 1: Ver logs em tempo real
tail -f /path/to/writable/logs/log-$(date +%Y-%m-%d).log | grep "DEBUG FERIAS"

# Opção 2: Ver últimos 100 registos
tail -100 /path/to/writable/logs/log-$(date +%Y-%m-%d).log | grep "DEBUG FERIAS"

# Opção 3: Ver tudo de hoje com DEBUG FERIAS
grep "DEBUG FERIAS" /path/to/writable/logs/log-$(date +%Y-%m-%d).log

# Opção 4: Ver também erros gerais
tail -200 /path/to/writable/logs/log-$(date +%Y-%m-%d).log
```

**Procure pela sequência de logs:**
```
[DEBUG FERIAS] Início submeterPedido
[DEBUG FERIAS] NIF: 123456789
[DEBUG FERIAS] Ano ativo: {...}
[DEBUG FERIAS] Request data: {...}
[DEBUG FERIAS] Períodos recebidos: 2
[DEBUG FERIAS] Validação pedidos pendentes OK
[DEBUG FERIAS] Período validado: 2025-07-01 a 2025-07-10
...
```

**O log irá parar no ponto exato onde falha!**

---

### PASSO 4: Verificar Base de Dados em Produção

```bash
mysql -u username -p database_name
```

```sql
-- Verificar ano letivo ativo
SELECT * FROM ano_letivo WHERE status = 1;

-- Verificar atribuição do utilizador
SELECT * FROM ferias_atribuicao 
WHERE user_nif = 123456789 
AND anoletivo_id = (SELECT id_anoletivo FROM ano_letivo WHERE status = 1)
LIMIT 1;

-- Verificar pedidos pendentes
SELECT * FROM ferias_pedido 
WHERE user_nif = 123456789 
AND estado IN ('submetido', 'em_aprovacao')
LIMIT 5;

-- Verificar feriados
SELECT COUNT(*) as total_feriados FROM ferias_feriados;
```

---

## 🐛 PROBLEMAS COMUNS

### 1. Timezone diferente
**Sintoma:** Datas calculadas incorretamente

**Solução:** Verificar em `app/Config/App.php`:
```php
public string $appTimezone = 'Europe/Lisbon';
```

### 2. Helper não carregado
**Sintoma:** `helper_loaded: false` no debug-status

**Solução:** 
```bash
# Verificar se existe
ls -la app/Helpers/ferias_helper.php

# Verificar permissões
chmod 644 app/Helpers/ferias_helper.php
```

### 3. Ano letivo não ativo
**Sintoma:** `ano_ativo: null`

**Solução:**
```sql
UPDATE ano_letivo SET status = 1 WHERE anoletivo = 2025;
-- Garantir que só há 1 ativo
UPDATE ano_letivo SET status = 0 WHERE anoletivo != 2025;
```

### 4. Sem atribuição de dias
**Sintoma:** Erro "Não tem dias de férias atribuídos"

**Solução:** Ir a `/ferias/atribuir` e atribuir dias ao utilizador

### 5. JSON malformado
**Sintoma:** Erro antes de validar períodos

**Adicionar debug temporário:**
```php
// No início de submeterPedido()
$rawInput = $this->request->getBody();
log_message('error', '[DEBUG FERIAS] Raw input: ' . $rawInput);
```

### 6. CSRF Token
**Sintoma:** Erro 403 ou token inválido

**Solução:** Verificar `app/Config/Filters.php`:
```php
// Garantir que /ferias/* está protegido mas aceita POST
```

### 7. Limite de memória PHP
**Sintoma:** Erro 500 ou timeout

**Solução:** Verificar `php.ini`:
```ini
memory_limit = 256M
max_execution_time = 60
```

---

## 📊 EXEMPLO DE ANÁLISE

### Cenário 1: Para no ano letivo
```
[DEBUG FERIAS] Início submeterPedido
[DEBUG FERIAS] NIF: 123456789
[DEBUG FERIAS] Ano ativo: null
[DEBUG FERIAS] Nenhum ano letivo ativo
```
**Problema:** Não existe ano letivo com `status=1`
**Solução:** Ativar ano letivo na base de dados

### Cenário 2: Para na validação de dias
```
[DEBUG FERIAS] Total dias: 20, Disponíveis: 22
[DEBUG FERIAS] Total dias != disponíveis
```
**Problema:** Deve marcar todos os 22 dias disponíveis
**Solução:** Ajustar períodos para totalizar 22 dias

### Cenário 3: Exception não capturada
```
[DEBUG FERIAS] Exception: Call to undefined function calcular_dias_uteis()
[DEBUG FERIAS] Trace: ...
```
**Problema:** Helper não está carregado ou função em falta
**Solução:** Verificar helper e autoload

---

## 🧹 LIMPEZA APÓS DEBUG

**IMPORTANTE:** Após identificar e resolver o problema:

1. **Remover endpoint de debug:**
```php
// Em FeriasController.php - APAGAR MÉTODO:
public function debugStatus() { ... }
```

2. **Remover rota de debug:**
```php
// Em Routes.php - APAGAR LINHA:
$routes->get('debug-status', 'FeriasController::debugStatus');
```

3. **Reduzir logs (opcional):**
Alterar `log_message('error', ...)` para `log_message('info', ...)` ou comentar os logs de debug

4. **Limpar logs antigos:**
```bash
# No servidor
cd /path/to/writable/logs
rm log-2026-03-*.log
```

---

## 📞 SUPORTE

Se após todos os passos o problema persistir, reuna:
1. ✅ Output completo de `/ferias/debug-status`
2. ✅ Console logs do navegador
3. ✅ Ultimos 200 logs do servidor com grep "DEBUG FERIAS"
4. ✅ Resultado das queries SQL
5. ✅ Versão PHP e CodeIgniter em produção
6. ✅ Configuração do timezone

---

**Última atualização:** 2026-03-12
**Versão:** 1.0
