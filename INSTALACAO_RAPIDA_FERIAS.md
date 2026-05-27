# ✅ CHECKLIST DE INSTALAÇÃO - SISTEMA DE FÉRIAS

## 📋 VERIFICAR ANTES DE COMEÇAR

- [ ] PHP 7.4+ instalado
- [ ] Composer instalado
- [ ] MySQL/MariaDB em execução
- [ ] CodeIgniter 4 funcionando
- [ ] Email SMTP configurado
- [ ] Acesso ao phpMyAdmin ou MySQL Workbench

---

## 🚀 PASSO A PASSO (5-10 minutos)

### 1️⃣ CRIAR TABELAS (2 min)

```bash
# Abrir phpMyAdmin → Selecionar base de dados → SQL
# Copiar e colar conteúdo de: CREATE_SISTEMA_FERIAS.sql
# Clicar em "Executar"
```

**Verificar:**
```sql
SHOW TABLES LIKE 'ferias%';
-- Deve mostrar 5 tabelas: ferias_atribuicao, ferias_pedido, ferias_periodo, ferias_log, ferias_feriados
```

✅ **Tabelas criadas com sucesso!**

---

### 2️⃣ INSTALAR BIBLIOTECA PDF (1 min)

```bash
cd c:\xampp\htdocs\ci4-adminlte
composer require dompdf/dompdf
```

✅ **DomPDF instalado!**

---

### 3️⃣ VERIFICAR FICHEIROS (1 min)

Confirmar que os seguintes ficheiros foram criados:

**Models:**
- [ ] `app/Models/FeriasAtribuicaoModel.php`
- [ ] `app/Models/FeriasPedidoModel.php`
- [ ] `app/Models/FeriasPeriodoModel.php`
- [ ] `app/Models/FeriasLogModel.php`
- [ ] `app/Models/FeriasFeriadosModel.php`

**Controller:**
- [ ] `app/Controllers/FeriasController.php`

**Helper:**
- [ ] `app/Helpers/ferias_helper.php`

**Views:**
- [ ] `app/Views/ferias/professor_index.php`
- [ ] `app/Views/ferias/secretaria_index.php`
- [ ] `app/Views/ferias/documento_pdf.php`

**Rotas:**
- [ ] Rotas adicionadas em `app/Config/Routes.php`

✅ **Todos os ficheiros no lugar!**

---

### 4️⃣ CRIAR PASTA PUBLIC (30 seg)

```bash
# A pasta será criada automaticamente quando necessário
# Mas garantir permissões:
chmod 755 public/
```

✅ **Permissões configuradas!**

---

### 5️⃣ POPULAR FERIADOS (OPCIONAL - 30 seg)

Os feriados de 2026 já estão inseridos. Para adicionar mais anos:

```sql
-- Via phpMyAdmin
-- Ou criar script PHP:
```

```php
<?php
// teste_feriados.php
require_once 'vendor/autoload.php';

$db = \Config\Database::connect();
$feriadosModel = new \App\Models\FeriasFeriadosModel();

// Adicionar feriados de 2027
$adicionados = $feriadosModel->adicionarFeriadosPortugal(2027);
echo "Feriados adicionados: {$adicionados}";
```

```bash
php teste_feriados.php
```

✅ **Feriados populados!**

---

### 6️⃣ VERIFICAR EMAIL (1 min)

```php
# Criar ficheiro: teste_email_ferias.php

<?php
require_once 'vendor/autoload.php';

helper('ferias');

$resultado = enviar_email_ferias(
    'seu_email@teste.pt',
    'Teste Sistema Férias',
    'Este é um email de teste do sistema de férias.'
);

echo $resultado ? 'Email enviado!' : 'Erro ao enviar email';
```

```bash
php teste_email_ferias.php
```

✅ **Emails funcionando!**

---

### 7️⃣ CRIAR UTILIZADORES DE TESTE (OPCIONAL - 2 min)

```sql
-- Criar professor de teste (se ainda não existir)
INSERT INTO user (name, email, NIF, level, status) 
VALUES ('Professor Teste', 'professor@teste.pt', '123456789', 1, 1);

-- Criar secretária de teste (se ainda não existir)
INSERT INTO user (name, email, NIF, level, status) 
VALUES ('Secretária Teste', 'secretaria@teste.pt', '987654321', 3, 1);
```

✅ **Utilizadores de teste criados!**

---

### 8️⃣ ATRIBUIR ANO LETIVO ATIVO (30 seg)

```sql
-- Verificar ano ativo
SELECT * FROM ano_letivo WHERE status = 1;

-- Se não existir, criar:
INSERT INTO ano_letivo (anoletivo, status) VALUES (2025, 1);
```

✅ **Ano letivo ativo configurado!**

---

### 9️⃣ TESTAR SISTEMA (3 min)

#### A. Atribuir Dias (Secretaria)

1. Login como secretária (level ≥ 3)
2. Aceder: `http://localhost/ci4-adminlte/ferias/atribuir`
3. Selecionar professor
4. Atribuir:
   - Dias Base: 22
   - Dias Ajuste: 0
   - Dias Extra: 0
5. Guardar

✅ **Atribuição feita!**

#### B. Marcar Férias (Professor)

1. Login como professor (level = 1)
2. Aceder: `http://localhost/ci4-adminlte/ferias`
3. Ver saldo disponível
4. Clicar "Marcar Período de Férias"
5. Selecionar datas (ex: 01/08/2026 a 10/08/2026)
6. Submeter

✅ **Pedido submetido!**

#### C. Aprovar Pedido (Secretaria)

1. Login como secretária
2. Aceder: `http://localhost/ci4-adminlte/ferias/secretaria`
3. Ver pedidos pendentes
4. Clicar "Aprovar"
5. Verificar se PDF foi gerado

✅ **Pedido aprovado e PDF gerado!**

---

## 🧪 TESTES AUTOMATIZADOS

### Teste de Cálculo de Dias Úteis

```php
<?php
// teste_calculos.php
require_once 'vendor/autoload.php';
helper('ferias');

echo "Teste 1: 01/08/2026 a 10/08/2026\n";
$dias = calcular_dias_uteis('2026-08-01', '2026-08-10', true);
echo "Dias úteis: {$dias} (esperado: ~7)\n\n";

echo "Teste 2: 01/09/2026 a 30/09/2026\n";
$dias = calcular_dias_uteis('2026-09-01', '2026-09-30', true);
echo "Dias úteis: {$dias} (esperado: ~22)\n\n";

echo "Teste 3: Validação de período\n";
$validacao = validar_periodo_ferias('2026-08-01', '2026-08-10', 2025);
echo "Válido: " . ($validacao['valido'] ? 'Sim' : 'Não') . "\n";
echo "Dias úteis: " . ($validacao['dias_uteis'] ?? 'N/A') . "\n";
```

```bash
php teste_calculos.php
```

---

## 🔍 VERIFICAÇÃO FINAL

### Checklist Completa:

- [ ] Tabelas criadas e populadas ✅
- [ ] DomPDF instalado ✅
- [ ] Todos os ficheiros no lugar ✅
- [ ] Permissões configuradas ✅
- [ ] Feriados populados ✅
- [ ] Email funcionando ✅
- [ ] Ano letivo ativo ✅
- [ ] Atribuição testada ✅
- [ ] Marcação testada ✅
- [ ] Aprovação testada ✅
- [ ] PDF gerado com sucesso ✅

---

## 🎉 SISTEMA PRONTO A USAR!

### Acessos:

**Professor:**
- URL: `http://localhost/ci4-adminlte/ferias`
- Ver saldo, marcar períodos, fazer upload de documento assinado

**Secretaria:**
- URL: `http://localhost/ci4-adminlte/ferias/secretaria`
- Atribuir dias, aprovar/rejeitar pedidos

---

## 📞 PROBLEMAS?

### Erro Comum 1: "Tabela não encontrada"
```bash
# Verificar se tabelas foram criadas
mysql -u root -p
USE sistema_gestao;
SHOW TABLES LIKE 'ferias%';
```

### Erro Comum 2: "Erro ao gerar PDF"
```bash
# Reinstalar DomPDF
composer remove dompdf/dompdf
composer require dompdf/dompdf
```

### Erro Comum 3: "Email não enviado"
```php
# Verificar config em app/Config/Email.php
// Testar envio manual
$email = \Config\Services::email();
$email->setTo('teste@teste.pt');
$email->setSubject('Teste');
$email->setMessage('Teste');
$email->send();
echo $email->printDebugger();
```

---

## 📚 DOCUMENTAÇÃO COMPLETA

Para mais detalhes, consultar:
- `IMPLEMENTACAO_SISTEMA_FERIAS.md` - Documentação completa
- `CREATE_SISTEMA_FERIAS.sql` - Script SQL das tabelas

---

**Instalação completa em ~10 minutos! 🚀**
