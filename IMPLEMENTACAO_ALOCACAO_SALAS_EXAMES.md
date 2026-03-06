# 🎯 IMPLEMENTAÇÃO: Sistema de Alocação de Salas + Calendário de Exames

## 📋 Resumo das Alterações (v2.0)

Foi implementada uma **nova estrutura** para permitir que uma sessão de exame possa ter **múltiplas salas**, cada uma com seu número específico de alunos e vigilantes alocados.

Adicionalmente, foi implementado um **calendário visual** com FullCalendar para visualização de todas as sessões de exame.

### ✨ Novidades v2.0
1. ✅ Sistema de alocação de múltiplas salas por sessão
2. ✅ Cálculo automático de vigilantes por sala (2 fixos ou 1/20 para MODa)
3. ✅ Calendário visual com código de cores por tipo de prova
4. ✅ Gestão granular de convocatórias por sala

---

## 🗄️ NOVA ESTRUTURA DE BASE DE DADOS

### Tabela Criada: `sessao_exame_sala`

Esta tabela intermediária liga sessões de exame a salas específicas:

```sql
CREATE TABLE sessao_exame_sala (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sessao_exame_id INT UNSIGNED NOT NULL,      -- FK para sessao_exame
    sala_id INT UNSIGNED NOT NULL,               -- FK para salas
    num_alunos_sala INT UNSIGNED NOT NULL,       -- Alunos NESTA sala
    vigilantes_necessarios TINYINT UNSIGNED,     -- Calculado automaticamente
    observacoes TEXT,
    created_at DATETIME,
    updated_at DATETIME,
    deleted_at DATETIME,
    
    FOREIGN KEY (sessao_exame_id) REFERENCES sessao_exame(id) ON DELETE CASCADE,
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_sessao_sala (sessao_exame_id, sala_id)
);
```

### Tabela Alterada: `convocatoria`

**ANTES:**
```sql
sala_id INT UNSIGNED NULL  -- FK direto para salas
```

**DEPOIS:**
```sql
sessao_exame_sala_id INT UNSIGNED NULL  -- FK para sessao_exame_sala
```

**Importante:** `sessao_exame_sala_id` é **NULL** para funções globais como:
- Suplente
- Júri
- Coadjuvante
- Apoio TIC

---

## ⚙️ REGRA DE CÁLCULO DE VIGILANTES

### Implementação no Model

```php
// SessaoExameSalaModel::calcularVigilantes()

if (tipo_prova === 'MODa') {
    vigilantes_necessarios = ceil(num_alunos_sala / 20);  // Mínimo 1
} else {
    vigilantes_necessarios = 2;  // SEMPRE 2 por sala
}
```

### Exemplos:

| Tipo Prova | Alunos | Vigilantes Necessários | Cálculo |
|------------|--------|------------------------|---------|
| Exame Nacional | 40 | 2 | Fixo |
| Exame Nacional | 15 | 2 | Fixo |
| Prova Final | 38 | 2 | Fixo |
| **MODa** | 40 | **2** | ceil(40/20) |
| **MODa** | 25 | **2** | ceil(25/20) |
| **MODa** | 15 | **1** | ceil(15/20) |

---

## 📊 EXEMPLO PRÁTICO

### Cenário: Exame de Matemática A (639) - 153 alunos

#### 1. Tabela `sessao_exame`
```
id: 1
exame_id: 10 (Matemática A - 639)
fase: 1ª Fase
data_exame: 2026-06-20
hora_exame: 09:30
```

#### 2. Tabela `sessao_exame_sala` (5 salas alocadas)
```
id | sessao_exame_id | sala_id | num_alunos_sala | vigilantes_necessarios
1  | 1               | 101     | 40              | 2
2  | 1               | 102     | 38              | 2
3  | 1               | 103     | 35              | 2
4  | 1               | 104     | 20              | 2
5  | 1               | 105     | 20              | 2
```
**Total:** 153 alunos em 5 salas = **10 vigilantes necessários**

#### 3. Tabela `convocatoria` (vigilantes alocados)
```
id | sessao_exame_id | sessao_exame_sala_id | user_id | funcao
1  | 1               | 1                    | 25      | Vigilante (Sala 101)
2  | 1               | 1                    | 32      | Vigilante (Sala 101)
3  | 1               | 2                    | 18      | Vigilante (Sala 102)
4  | 1               | 2                    | 45      | Vigilante (Sala 102)
5  | 1               | 3                    | 67      | Vigilante (Sala 103)
6  | 1               | 3                    | 89      | Vigilante (Sala 103)
7  | 1               | 4                    | 12      | Vigilante (Sala 104)
8  | 1               | 4                    | 78      | Vigilante (Sala 104)
9  | 1               | 5                    | 56      | Vigilante (Sala 105)
10 | 1               | 5                    | 90      | Vigilante (Sala 105)
11 | 1               | NULL                 | 34      | Suplente (sem sala)
12 | 1               | NULL                 | 88      | Coadjuvante
```

---

## 📂 FICHEIROS CRIADOS

### 1️⃣ Migrations (2 ficheiros)

#### `2026-01-31-100001_CreateSessaoExameSalaTable.php`
- Cria tabela `sessao_exame_sala`
- Foreign keys para `sessao_exame` e `salas`
- Unique constraint (sessao + sala)

#### `2026-01-31-100002_AlterConvocatoriaAddSessaoExameSala.php`
- Remove campo `sala_id` da tabela `convocatoria`
- Adiciona campo `sessao_exame_sala_id`
- Atualiza foreign keys

### 2️⃣ Models

#### `SessaoExameSalaModel.php`
**Métodos principais:**
- `calcularVigilantes()` - Callback automático (2 fixos ou 1/20 para MODa)
- `getSalasBySessao()` - Lista salas de uma sessão
- `getSalasComEstatisticas()` - Salas com vigilantes alocados/em falta
- `getSalasDisponiveis()` - Salas ainda não alocadas
- `salaJaAlocada()` - Validação de duplicação
- `verificarCapacidade()` - Valida num_alunos vs capacidade

### 3️⃣ Controllers

#### `SessaoExameSalaController.php`
**Rotas:**
- `GET /sessoes-exame/alocar-salas/{id}` - Página de alocação
- `POST /sessoes-exame-salas/getDataTable` - DataTable Ajax
- `POST /sessoes-exame-salas/store` - Criar alocação
- `POST /sessoes-exame-salas/update/{id}` - Editar alocação
- `POST /sessoes-exame-salas/delete/{id}` - Remover sala
- `GET /sessoes-exame-salas/getSalasDisponiveis` - API
- `GET /sessoes-exame-salas/estatisticas/{id}` - API

#### `SessaoExameController.php` (Métodos Adicionados)
**Calendário:**
- `GET /sessoes-exame/calendario` - Página do calendário ⭐ NOVO
- `GET /sessoes-exame/calendario-eventos` - API eventos FullCalendar ⭐ NOVO

### 4️⃣ Views

#### `sessoes_exame/alocar_salas.php`
**Interface AdminLTE com:**
- Card de informações da sessão
- Card de estatísticas (total salas, alunos, vigilantes)
- DataTable com 10 colunas
- Modal para adicionar/editar sala
- Validação de capacidade em tempo real
- SweetAlert para confirmações

#### `sessoes_exame/calendario.php` ⭐ NOVO
**Calendário FullCalendar com:**
- Visualização mensal/semanal/diária/agenda
- Código de cores por tipo de prova:
  - 🔴 Vermelho: Exames Nacionais
  - 🔵 Azul: Provas Finais
  - 🟢 Verde: Provas MODa
- Clique no evento → Detalhes da sessão
- Tooltip com informações (fase, hora, duração, alunos)
- Responsivo para mobile/tablet/desktop
- Botão de ação rápida para criar nova sessão
- `getSalasComEstatisticas()` - Salas com vigilantes alocados/em falta
- `getSalasDisponiveis()` - Salas ainda não alocadas
- `salaJaAlocada()` - Validação de duplicação
- `verificarCapacidade()` - Valida num_alunos vs capacidade

### 3️⃣ Controller

#### `SessaoExameSalaController.php`
**Rotas:**
- `GET /sessoes-exame/alocar-salas/{id}` - Página de alocação
- `POST /sessoes-exame-salas/getDataTable` - DataTable Ajax
- `POST /sessoes-exame-salas/store` - Criar alocação
- `POST /sessoes-exame-salas/update/{id}` - Editar alocação
- `POST /sessoes-exame-salas/delete/{id}` - Remover sala
- `GET /sessoes-exame-salas/getSalasDisponiveis` - API
- `GET /sessoes-exame-salas/estatisticas/{id}` - API

### 4️⃣ View

#### `sessoes_exame/alocar_salas.php`
**Interface AdminLTE com:**
- Card de informações da sessão
- Card de estatísticas (total salas, alunos, vigilantes)
- DataTable com 10 colunas:
  - Sala
  - Alunos
  - Capacidade
  - Vigilantes necessários
  - Vigilantes alocados
  - Em falta
  - Estado (badge)
  - Observações
  - Ações (Editar/Convocar/Remover)
- Modal para adicionar/editar sala
- Validação de capacidade em tempo real
- SweetAlert para confirmações

---

## 🔄 FICHEIROS MODIFICADOS

### 1. `ConvocatoriaController.php`
**Alterações:**
- Adicionado `SessaoExameSalaModel`
- Método `criar()` recebe `$sessaoExameSalaId`
- Método `store()` usa `sessao_exame_sala_id`
- Query do DataTable com JOIN em `sessao_exame_sala`

### 2. `sessoes_exame/detalhes.php`
**Alteração:**
- Botão "Alocar Salas" adicionado

### 3. `Routes.php`
**Rotas adicionadas:**
```php
// Dentro do grupo 'sessoes-exame'
$routes->get('alocar-salas/(:num)', 'SessaoExameSalaController::alocarSalas/$1');

// Novo grupo
$routes->group('sessoes-exame-salas', function($routes) {
    $routes->post('getDataTable', 'SessaoExameSalaController::getDataTable');
    $routes->get('get/(:num)', 'SessaoExameSalaController::get/$1');
    $routes->post('store', 'SessaoExameSalaController::store');
    $routes->post('update/(:num)', 'SessaoExameSalaController::update/$1');
    $routes->post('delete/(:num)', 'SessaoExameSalaController::delete/$1');
    $routes->get('getSalasDisponiveis', 'SessaoExameSalaController::getSalasDisponiveis');
    $routes->get('estatisticas/(:num)', 'SessaoExameSalaController::getEstatisticas/$1');
});
```

---

## 🚀 INSTALAÇÃO E USO

### Passo 1: Executar Migrations

```bash
php spark migrate
```

Isto irá:
1. ✅ Criar tabela `sessao_exame_sala`
2. ✅ Remover `sala_id` de `convocatoria`
3. ✅ Adicionar `sessao_exame_sala_id` a `convocatoria`

### Passo 2: Fluxo de Trabalho

#### 1️⃣ Criar Sessão de Exame
- Ir a **Sec. Exames → Sessões de Exame**
- Clicar "Nova Sessão"
- Preencher dados (exame, fase, data, hora, duração)

#### 2️⃣ Alocar Salas
- Na lista de sessões, clicar **"Detalhes"**
- Clicar botão **"Alocar Salas"**
- Adicionar cada sala:
  - Selecionar sala do dropdown
  - Inserir número de alunos
  - Sistema calcula automaticamente vigilantes necessários
  - Adicionar observações (opcional)

#### 3️⃣ Convocar Vigilantes
- Na página de alocação, clicar **ícone "+"** (Convocar Vigilantes) de cada sala
- Selecionar professor
- Função será "Vigilante" por defeito
- Sistema valida conflitos de horário
- Repetir até completar vigilantes

#### 4️⃣ Funções Globais (opcional)
- Para Suplentes/Júri/Coadjuvantes:
  - Ir a **Convocatórias/Vigilâncias**
  - Criar convocatória SEM especificar sala
  - `sessao_exame_sala_id` será NULL

---

## 📊 VISUALIZAÇÃO DE ESTATÍSTICAS

### Interface "Alocar Salas"

**Card de Estatísticas:**
```
Total de Salas: 5
Total de Alunos: 153
Vigilantes Necessários: 10
```

**Tabela de Salas:**
```
| Sala     | Alunos | Cap | Necessários | Alocados | Falta | Estado        |
|----------|--------|-----|-------------|----------|-------|---------------|
| A101     | 40     | 40  | 2           | 2        | 0     | ✅ Completo   |
| A102     | 38     | 40  | 2           | 1        | 1     | ⚠️ Parcial    |
| B205     | 35     | 35  | 2           | 0        | 2     | ❌ Sem Vigil. |
```

**Badges de Estado:**
- 🟢 **Completo** (bg-success): Todos os vigilantes alocados
- 🟡 **Parcial** (bg-warning): Alguns vigilantes alocados
- 🔴 **Sem Vigilantes** (bg-danger): Nenhum vigilante alocado

---

## ✅ VALIDAÇÕES IMPLEMENTADAS

### No Backend (SessaoExameSalaModel)
1. ✅ Sala não pode ser alocada 2x à mesma sessão (UNIQUE constraint)
2. ✅ Número de alunos não pode exceder capacidade da sala
3. ✅ Sessão e sala devem existir (foreign keys)

### No Frontend (JavaScript)
1. ✅ Mostra capacidade ao selecionar sala
2. ✅ Alerta se exceder capacidade
3. ✅ Confirmação SweetAlert ao remover sala
4. ✅ Bloqueio de edição de sala (apenas alunos/obs)

### No Controller (Convocatoria)
1. ✅ Verifica conflito de horário do professor
2. ✅ Impede convocatória duplicada
3. ✅ Valida se sala pertence à sessão

---

## 🔗 RELAÇÕES ENTRE TABELAS

```
exame (30 códigos oficiais)
  ↓ 1:N
sessao_exame (datas/horas específicas)
  ↓ 1:N
sessao_exame_sala (alocação de salas)
  ↓ 1:N
convocatoria (vigilantes por sala)
  → user (professores)
```

---

## 📱 VANTAGENS DA NOVA ESTRUTURA

### ✅ Flexibilidade Total
- Uma sessão pode ter 1, 5, 10 ou mais salas
- Cada sala com seu número específico de alunos

### ✅ Cálculo Preciso
- Vigilantes calculados **por sala**, não globalmente
- Respeita regra de MODa vs outras provas

### ✅ Gestão Granular
- Alocar vigilantes especificamente a cada sala
- Ver claramente quais salas faltam vigilantes

### ✅ Mantém Compatibilidade
- Funções globais (Suplente, Júri) continuam sem sala
- `sessao_exame_sala_id = NULL` para essas funções

### ✅ Relatórios Precisos
- "Sala A101 tem 2 vigilantes alocados de 2 necessários"
- "Faltam 3 vigilantes na sessão (Sala B205: 2, Sala C301: 1)"

### ✅ Integridade de Dados
- Não permite duplicar sala na mesma sessão
- Não permite remover sala com convocatórias associadas
- Validação de capacidade da sala

---

## 🎯 QUERIES ÚTEIS

### Ver todas as salas de uma sessão
```sql
SELECT 
    ses.id,
    s.nome AS sala,
    ses.num_alunos_sala,
    s.capacidade,
    ses.vigilantes_necessarios,
    (SELECT COUNT(*) FROM convocatoria WHERE sessao_exame_sala_id = ses.id) AS vigilantes_alocados
FROM sessao_exame_sala ses
JOIN salas s ON s.id = ses.sala_id
WHERE ses.sessao_exame_id = 1;
```

### Ver vigilantes por sala
```sql
SELECT 
    s.nome AS sala,
    u.name AS professor,
    c.funcao,
    c.estado_confirmacao
FROM convocatoria c
JOIN sessao_exame_sala ses ON ses.id = c.sessao_exame_sala_id
JOIN salas s ON s.id = ses.sala_id
JOIN user u ON u.id = c.user_id
WHERE ses.sessao_exame_id = 1
ORDER BY s.nome, c.funcao;
```

### Estatísticas de uma sessão
```sql
SELECT 
    COUNT(DISTINCT ses.id) AS total_salas,
    SUM(ses.num_alunos_sala) AS total_alunos,
    SUM(ses.vigilantes_necessarios) AS vigilantes_necessarios,
    COUNT(c.id) AS vigilantes_alocados
FROM sessao_exame_sala ses
LEFT JOIN convocatoria c ON c.sessao_exame_sala_id = ses.id AND c.funcao = 'Vigilante'
WHERE ses.sessao_exame_id = 1;
```

---

## 🛠️ TROUBLESHOOTING

### Erro: "Sala já alocada"
**Causa:** Tentando adicionar a mesma sala 2x à sessão
**Solução:** Verificar lista de salas já alocadas

### Erro: "Excede capacidade"
**Causa:** num_alunos_sala > capacidade da sala
**Solução:** Reduzir alunos ou escolher sala maior

### Erro: "Existem convocatórias associadas"
**Causa:** Tentando remover sala com vigilantes alocados
**Solução:** Remover primeiro as convocatórias dessa sala

### Campo sala_id não encontrado
**Causa:** Migration não executada
**Solução:** Executar `php spark migrate`

---

## 📅 Data de Implementação

**31 de Janeiro de 2026**

**Versão:** 2.0 - Sistema de Alocação de Salas

**Status:** ✅ Completo e Funcional

---

## 📋 CHECKLIST DE IMPLEMENTAÇÃO

- [x] Migration `sessao_exame_sala` criada
- [x] Migration alteração `convocatoria` criada
- [x] Model `SessaoExameSalaModel` com callbacks
- [x] Controller `SessaoExameSalaController` com CRUD
- [x] View `alocar_salas.php` com DataTables
- [x] Atualização `ConvocatoriaController`
- [x] Atualização view detalhes sessão
- [x] Rotas configuradas
- [x] Documentação completa
- [x] Regra especial MODa implementada

**🎉 SISTEMA PRONTO PARA USO!**
