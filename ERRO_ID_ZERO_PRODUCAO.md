# 🔴 ERRO: Registos inseridos com id=0 na Produção

## Problema Identificado

Os registos na tabela `ferias_atribuicao` em **produção** estão a ser inseridos com `id=0` em vez de usar AUTO_INCREMENT:

```sql
(0, 194341402, 3, 23, ...) -- id=0 ❌
(0, 194341402, 9, 23, ...) -- id=0 ❌
```

Isto causa:
- ✗ Violação de PRIMARY KEY (dois registos com mesmo id)
- ✗ Erro 400 ao tentar guardar atribuições
- ✗ Dados duplicados na base de dados

## Causas Possíveis

1. **AUTO_INCREMENT perdido/desativado** na tabela de produção
2. **Constraint UNIQUE** a bloquear UPDATE e forçar INSERT com id=0
3. **Permissões de base de dados** impedindo AUTO_INCREMENT

## 🔧 SOLUÇÃO - Executar em PRODUÇÃO

### OPÇÃO A: Via phpMyAdmin (Mais Fácil)

1. **Aceder phpMyAdmin em produção**

2. **Selecionar base de dados** (`u520317771_sistema_gestao`)

3. **Executar SQL** (separadamente):

```sql
-- 1) BACKUP primeiro! (Exportar tabela)

-- 2) Ver registos problemáticos
SELECT * FROM ferias_atribuicao WHERE id = 0;

-- 3) Apagar registos inválidos
DELETE FROM ferias_atribuicao WHERE id = 0;

-- 4) Corrigir estrutura
ALTER TABLE ferias_atribuicao 
MODIFY COLUMN id INT(11) NOT NULL AUTO_INCREMENT;

-- 5) Resetar AUTO_INCREMENT
ALTER TABLE ferias_atribuicao AUTO_INCREMENT = 1;

-- 6) Verificar
SHOW CREATE TABLE ferias_atribuicao\G
```

### OPÇÃO B: Via SSH (Script Automático)

1. **Upload do script** `CORRIGIR_FERIAS_ATRIBUICAO_PRODUCAO.sql` para o servidor

2. **Conectar via SSH**:
```bash
ssh u520317771@escoladigital.cloud
```

3. **Executar correção**:
```bash
mysql -h localhost -u u520317771_sistema -p u520317771_sistema_gestao < CORRIGIR_FERIAS_ATRIBUICAO_PRODUCAO.sql
```

4. **Verificar logs**:
```bash
tail -50 /home/u520317771/domains/escoladigital.cloud/public_html/writable/logs/log-$(date +%Y-%m-%d).log | grep "DEBUG ATRIBUICAO"
```

## 📊 Verificação Após Correção

### 1. Testar Estrutura

No phpMyAdmin ou MySQL:
```sql
-- Deve mostrar AUTO_INCREMENT
SHOW CREATE TABLE ferias_atribuicao;

-- Deve retornar 0 registos
SELECT COUNT(*) as total FROM ferias_atribuicao WHERE id = 0;

-- Ver AUTO_INCREMENT atual
SELECT AUTO_INCREMENT 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'u520317771_sistema_gestao' 
AND TABLE_NAME = 'ferias_atribuicao';
```

### 2. Testar Atribuição via Interface

1. Aceder: `https://escoladigital.cloud/ferias/atribuir`
2. Atribuir férias a um professor
3. Verificar se:
   - ✅ Não dá erro 400
   - ✅ Mensagem "Atribuição guardada com sucesso"
   - ✅ Registo criado com id sequencial (1, 2, 3...)

### 3. Verificar Logs

Console navegador (F12):
```javascript
// Deve aparecer:
success: true
message: "Atribuição guardada com sucesso"
```

Logs servidor:
```bash
grep "DEBUG ATRIBUICAO" /path/to/logs/log-2026-03-12.log
```

Deve mostrar:
```
[DEBUG ATRIBUICAO] Resultado INSERT: ID=1  ✅ (número real, não 0)
[DEBUG ATRIBUICAO] ========== FIM COM SUCESSO ==========
```

## 🐛 Se o Problema Persistir

### 1. Verificar Permissões MySQL

```sql
-- Verificar privilégios do utilizador
SHOW GRANTS FOR 'u520317771_sistema'@'localhost';

-- Deve ter pelo menos:
-- INSERT, UPDATE, DELETE, CREATE, ALTER
```

### 2. Verificar Versão MySQL/MariaDB

```sql
SELECT VERSION();

-- Versões conhecidas com problemas AUTO_INCREMENT:
-- MySQL 5.5 ou inferior - atualizar para 5.7+
-- MariaDB 10.0 ou inferior - atualizar para 10.4+
```

### 3. Forçar Recreação da Tabela

**ATENÇÃO**: Apenas se realmente necessário e com BACKUP!

```sql
-- 1. BACKUP COMPLETO
SELECT * FROM ferias_atribuicao INTO OUTFILE '/tmp/backup_ferias.csv';

-- 2. Drop e recreate (último recurso)
DROP TABLE ferias_atribuicao;

-- 3. Executar CREATE TABLE correto
-- (usar o script original CREATE_TABLE_PERMUTAS.sql ou similar)
```

## 📋 Logs Melhorados

Após as alterações, os logs em produção vão mostrar:

```
[DEBUG ATRIBUICAO MODEL] Tentando INSERT
[DEBUG ATRIBUICAO MODEL] Resultado INSERT: ID=5
[DEBUG ATRIBUICAO MODEL] Erros INSERT validação: []
[DEBUG ATRIBUICAO MODEL] Erros INSERT database: {"code":0,"message":""}
[DEBUG ATRIBUICAO MODEL] SQL Last Query: INSERT INTO ferias_atribuicao ...
```

Se der erro, vai capturar:
- Erros de validação do CodeIgniter
- Erros da base de dados (código e mensagem)
- Query SQL exata que falhou

## 🎯 Resumo da Ação Necessária

1. ✅ **Fazer BACKUP** da tabela `ferias_atribuicao`
2. ✅ **Executar** `CORRIGIR_FERIAS_ATRIBUICAO_PRODUCAO.sql` em produção
3. ✅ **Testar** atribuição via interface web
4. ✅ **Verificar logs** para confirmar INSERT com ID correto
5. ✅ **Reportar resultado** (sucesso ou erro detalhado dos logs)

---

**Data:** 2026-03-12  
**Ficheiros criados:**
- `CORRIGIR_FERIAS_ATRIBUICAO_PRODUCAO.sql` - Script de correção
- `diagnostico_ferias_producao.php` - Diagnóstico manual
- Este documento - Guia de resolução
