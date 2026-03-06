# 🚀 INSTALAÇÃO RÁPIDA - Sistema de Alocação de Salas

## ✅ O que foi implementado?

Sistema completo para alocar **múltiplas salas** a uma sessão de exame, com:
- ✅ Cálculo automático de vigilantes (2 por sala, exceto MODa = 1 por 20 alunos)
- ✅ Interface visual para gestão de salas
- ✅ Validação de capacidade das salas
- ✅ Estatísticas em tempo real
- ✅ DataTables com badges coloridos

---

## 📦 Ficheiros Criados

### 🗄️ Migrations (2)
- `app/Database/Migrations/2026-01-31-100001_CreateSessaoExameSalaTable.php`
- `app/Database/Migrations/2026-01-31-100002_AlterConvocatoriaAddSessaoExameSala.php`

### 💻 Backend (2)
- `app/Models/SessaoExameSalaModel.php`
- `app/Controllers/SessaoExameSalaController.php`

### 🎨 Frontend (1)
- `app/Views/sessoes_exame/alocar_salas.php`

### 📖 Documentação (2)
- `IMPLEMENTACAO_ALOCACAO_SALAS_EXAMES.md` (documentação completa)
- `MIGRATION_ALOCACAO_SALAS.sql` (script SQL direto)

### ⚙️ Ficheiros Modificados (3)
- `app/Controllers/ConvocatoriaController.php`
- `app/Views/sessoes_exame/detalhes.php`
- `app/Config/Routes.php`

---

## 🔧 INSTALAÇÃO EM 2 PASSOS

### Passo 1: Executar Migrations

```bash
cd c:\xampp\htdocs\ci4-adminlte
php spark migrate
```

**O que acontece:**
1. ✅ Cria tabela `sessao_exame_sala`
2. ✅ Remove campo `sala_id` de `convocatoria`
3. ✅ Adiciona campo `sessao_exame_sala_id` a `convocatoria`

### Passo 2: Testar Interface

1. Fazer login no sistema
2. Ir a **Sec. Exames → Sessões de Exame**
3. Clicar em **"Detalhes"** de uma sessão
4. Clicar no botão **"Alocar Salas"** (novo!)
5. Adicionar salas clicando **"Adicionar Sala"**

---

## 🎯 COMO USAR

### 1️⃣ Criar Sessão de Exame

**Menu:** Sec. Exames → Sessões de Exame → Nova Sessão

Preencher:
- Exame (ex: 639 - Matemática A)
- Fase (1ª Fase)
- Data (20/06/2026)
- Hora (09:30)
- Duração (150 minutos)

### 2️⃣ Alocar Salas

**Página:** Detalhes da Sessão → Botão "Alocar Salas"

Para cada sala:
1. Clicar **"Adicionar Sala"**
2. Selecionar sala do dropdown
3. Inserir número de alunos
4. Sistema calcula automaticamente vigilantes
5. Guardar

**Exemplo:**
```
Sala A101 → 40 alunos → 2 vigilantes necessários
Sala A102 → 38 alunos → 2 vigilantes necessários
Sala B205 → 35 alunos → 2 vigilantes necessários
```

### 3️⃣ Convocar Vigilantes

Na lista de salas alocadas:
1. Clicar ícone **verde (+)** "Convocar Vigilantes"
2. Selecionar professor
3. Função = "Vigilante"
4. Repetir até completar

**Estado das Salas:**
- 🟢 **Completo** - Todos os vigilantes alocados
- 🟡 **Parcial** - Faltam vigilantes
- 🔴 **Sem Vigilantes** - Nenhum alocado

---

## ⚙️ REGRA ESPECIAL MODa

### Provas MODa (310, 311, 312, 323)

**Regra:** 1 vigilante por 20 alunos (em vez de 2 fixos)

**Exemplos:**
- 15 alunos → **1 vigilante**
- 25 alunos → **2 vigilantes**
- 40 alunos → **2 vigilantes**

**Outras provas:** SEMPRE 2 vigilantes por sala

---

## 🔍 VERIFICAÇÃO

### Ver se tabela foi criada:
```sql
DESCRIBE sessao_exame_sala;
```

### Ver se convocatoria foi alterada:
```sql
DESCRIBE convocatoria;
-- Deve mostrar: sessao_exame_sala_id
-- Não deve mostrar: sala_id
```

### Testar alocação:
```sql
-- Ver salas alocadas
SELECT * FROM sessao_exame_sala;

-- Ver convocatórias novas
SELECT 
    c.id,
    u.name AS professor,
    s.nome AS sala,
    c.funcao
FROM convocatoria c
LEFT JOIN sessao_exame_sala ses ON ses.id = c.sessao_exame_sala_id
LEFT JOIN salas s ON s.id = ses.sala_id
JOIN user u ON u.id = c.user_id;
```

---

## ❌ ROLLBACK (Se necessário)

Se algo correr mal e quiser voltar atrás:

```bash
php spark migrate:rollback
```

Ou executar SQL manual:
```sql
-- Ver script em MIGRATION_ALOCACAO_SALAS.sql
-- Secção "4. ROLLBACK"
```

---

## 📊 INTERFACE VISUAL

### Página "Alocar Salas"

**URL:** `/sessoes-exame/alocar-salas/{id}`

**Elementos:**
1. **Card Info** - Exame, data, hora, fase
2. **Card Estatísticas** - Total salas, alunos, vigilantes
3. **DataTable** - Lista de salas com estado
4. **Modal** - Formulário adicionar/editar sala

**Colunas da Tabela:**
- Sala
- Alunos
- Capacidade
- Vigilantes Necessários
- Vigilantes Alocados
- Em Falta
- Estado (badge)
- Observações
- Ações (Editar/Convocar/Remover)

---

## 🎨 Cores e Badges

### Estados:
- 🟢 `bg-success` - Completo
- 🟡 `bg-warning` - Parcial
- 🔴 `bg-danger` - Sem Vigilantes

### Alertas:
- ⚠️ Badge vermelho - Alunos excedem capacidade

---

## 🔗 ROTAS NOVAS

```
GET  /sessoes-exame/alocar-salas/{id}
POST /sessoes-exame-salas/getDataTable
GET  /sessoes-exame-salas/get/{id}
POST /sessoes-exame-salas/store
POST /sessoes-exame-salas/update/{id}
POST /sessoes-exame-salas/delete/{id}
GET  /sessoes-exame-salas/getSalasDisponiveis
GET  /sessoes-exame-salas/estatisticas/{id}
```

---

## 📞 SUPORTE

### Documentação Completa:
📄 **IMPLEMENTACAO_ALOCACAO_SALAS_EXAMES.md**

### Script SQL Manual:
📄 **MIGRATION_ALOCACAO_SALAS.sql**

### Queries Úteis:
```sql
-- Ver tudo sobre uma sessão
SELECT 
    se.id,
    e.codigo_prova,
    COUNT(DISTINCT ses.id) AS salas,
    SUM(ses.num_alunos_sala) AS alunos,
    SUM(ses.vigilantes_necessarios) AS vigilantes_necessarios
FROM sessao_exame se
JOIN exame e ON e.id = se.exame_id
LEFT JOIN sessao_exame_sala ses ON ses.sessao_exame_id = se.id
WHERE se.id = 1
GROUP BY se.id;
```

---

## ✅ CHECKLIST FINAL

- [ ] Executar `php spark migrate`
- [ ] Verificar tabela `sessao_exame_sala` criada
- [ ] Verificar campo `sessao_exame_sala_id` em `convocatoria`
- [ ] Aceder página "Alocar Salas"
- [ ] Adicionar sala de teste
- [ ] Convocar vigilante de teste
- [ ] Verificar badges de estado

---

## 🎉 PRONTO!

Sistema totalmente funcional e integrado com:
- ✅ AdminLTE design
- ✅ DataTables server-side
- ✅ SweetAlert confirmações
- ✅ Bootstrap 5 modais
- ✅ Validações backend/frontend
- ✅ Cálculo automático vigilantes
- ✅ Estatísticas em tempo real

**Data:** 31 Janeiro 2026  
**Versão:** 2.0 - Alocação de Salas  
**Status:** ✅ Completo
