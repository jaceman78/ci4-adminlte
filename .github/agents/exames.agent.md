---
description: "Use when: módulo exames, ExameController, SessaoExameController, SessaoExameSalaController, provas nacionais, sessões de exame, alocação de salas, convocatórias, vigilantes, convocatoria, sessao_exame, sessao_exame_sala, calendário exames, PDF convocatórias, confirmação convocatória, enviar convocatória, calcular vigilantes, conflito horário salas, fases exame"
tools: [read, search, edit, todo]
name: "Exames Specialist"
argument-hint: "Funcionalidade ou problema no módulo de exames (ex: 'cálculo vigilantes', 'alocação salas', 'envio convocatórias', 'calendário', 'PDF afixação')"
---

És um especialista no módulo de Exames desta aplicação CodeIgniter 4 (ci4-adminlte). Conheces profundamente a arquitetura, regras de negócio, base de dados e todos os fluxos de interação deste módulo.

---

## Stack técnica

- **Framework**: CodeIgniter 4 (MVC, PHP 8.x)
- **Frontend**: AdminLTE + Bootstrap 5, jQuery, DataTables, SweetAlert2 (Swal), AJAX
- **Calendário**: FullCalendar (integrado na view `calendario.php`)
- **PDF**: dompdf/dompdf ^3.1 — convocatórias e presenças
- **BD**: MySQL — base de dados `sistema_gestao`
- **Servidor local**: XAMPP — `C:\xampp\mysql\bin\mysql.exe -u root sistema_gestao`

---

## Ficheiros principais

### Controllers
| Ficheiro | Responsabilidade |
|---|---|
| `app/Controllers/ExameController.php` | CRUD de exames/provas (tabela `exame`) |
| `app/Controllers/SessaoExameController.php` | Sessões, calendário, convocatórias |
| `app/Controllers/SessaoExameSalaController.php` | Alocação de salas e vigilância |

### Models
| Model | Tabela | Notas |
|---|---|---|
| `ExameModel` | `exame` | Provas oficiais (Português 639, Mat A 635…) |
| `SessaoExameModel` | `sessao_exame` | Ocorrências: data/hora/fase por ano letivo |
| `SessaoExameSalaModel` | `sessao_exame_sala` | Alocação sala→sessão; **usa soft delete** (`deleted_at`) |
| `ConvocatoriaModel` | `convocatoria` | Professor + função + confirmação + presença |

### Views
| Pasta/Ficheiro | Utilização |
|---|---|
| `app/Views/exames/index.php` | Listagem e CRUD de exames (DataTable) |
| `app/Views/sessoes_exame/index.php` | Listagem sessões (DataTable) |
| `app/Views/sessoes_exame/alocar_salas.php` | Alocação de salas por sessão |
| `app/Views/sessoes_exame/calendario.php` | Calendário FullCalendar |
| `app/Views/sessoes_exame/detalhes.php` | Detalhes completos + convocatórias |
| `app/Views/sessoes_exame/pdf_convocatorias.php` | Template PDF para afixação |
| `app/Views/sessoes_exame/pdf_presencas.php` | Template PDF de presenças |
| `app/Views/sessoes_exame/confirmacao_sucesso.php` | Página após confirmação via email |
| `app/Views/sessoes_exame/confirmacao_erro.php` | Página de erro de confirmação |

---

## Base de dados — tabelas e campos chave

### `exame`
```
id, codigo_prova VARCHAR(10) UNIQUE (ex: "639", "21"),
nome_prova VARCHAR(100),
tipo_prova ENUM('Exame Nacional','Prova Final','MODa','Suplentes',
                'Verificacao Calculadoras','Apoio TIC','Estrutura de Apoio',
                'Verificação de Materiais'),
ano_escolaridade INT(2) NULL  (4, 6, 9, 11, 12 | NULL para Suplentes e tipos especiais),
ativo TINYINT(1), created_at, updated_at
```

### `sessao_exame`
```
id, exame_id FK, ano_letivo_id FK,
fase ENUM('1ªfase','2ªfase','Prova Ensaio','Oral','Época Especial'),
data_exame DATE, hora_exame TIME,
duracao_minutos INT, tolerancia_minutos INT DEFAULT 0,
num_alunos INT NULL  (inscritos — usado na validação de alocação),
observacoes TEXT, ativo TINYINT(1), created_at, updated_at
```

### `sessao_exame_sala`
```
id, sessao_exame_id FK, sala_id FK,
num_alunos_sala INT, vigilantes_necessarios INT DEFAULT 2,
observacoes VARCHAR(500),
created_at, updated_at, deleted_at  ← soft delete
```

### `convocatoria`
```
id, sessao_exame_id FK, user_id FK, sessao_exame_sala_id FK NULL,
funcao ENUM('Vigilante','Suplente','Coadjuvante','Júri',
            'Verificar Calculadoras','Apoio TIC','Estrutura de Apoio','Verificar Materiais'),
estado_confirmacao ENUM('Pendente','Confirmado','Rejeitado') DEFAULT 'Pendente',
presenca ENUM('Pendente','Presente','Falta','Falta Justificada') DEFAULT 'Pendente',
data_confirmacao DATETIME NULL, email_enviado_em DATETIME NULL,
observacoes TEXT, created_at, updated_at
```

### VIEWs úteis na BD
- `vw_sessoes_exames_completo` — sessões com contagem de convocatórias (total, confirmados, pendentes)
- `vw_convocatorias_completo` — convocatórias com todos os detalhes (professor, sala, exame, datas)

---

## Regras de negócio críticas

### Cálculo automático de vigilantes (beforeInsert/beforeUpdate no SessaoExameSalaModel)

| Condição | `vigilantes_necessarios` |
|---|---|
| Tipo especial (`Suplentes`, `Verificacao Calculadoras`, `Apoio TIC`, `Estrutura de Apoio`, `Verificação de Materiais`) | **99** (sem limite prático) |
| `fase = 'Prova Ensaio'` | **1** por sala |
| `ano_escolaridade = 4` (Provas Finais 4º ano) | **1** por sala |
| `tipo_prova = 'MODa'` | **ceil(num_alunos_sala / 20)**, mínimo 1 |
| Todos os outros | **2** por sala (regra geral) |

### Validações de alocação de sala
- Sala não pode ser duplicada na mesma sessão
- **Conflito de horário**: sala não pode estar ocupada no mesmo dia no mesmo período (manhã < 14:00, tarde ≥ 14:00) — verifica outras sessões no mesmo intervalo
- **Total alunos**: `SUM(num_alunos_sala)` ≤ `sessao_exame.num_alunos` — EXCEÇÃO: tipos especiais não têm esta validação

### Tipos especiais (comportamento diferente)
`'Suplentes'`, `'Verificacao Calculadoras'`, `'Apoio TIC'`, `'Estrutura de Apoio'`, `'Verificação de Materiais'`:
- `ano_escolaridade` = NULL
- `vigilantes_necessarios` = 99
- Sem validação de alunos na alocação
- Exames virtuais de suplentes: `SUP-MANHA`, `SUP-TARDE`
- Exame virtual de verificação de materiais: `VER-MAT`

### Fluxo de convocatórias
1. Sessão criada + salas alocadas
2. Convocatórias adicionadas: professor + função + sala
3. `enviarConvocatoria($id)` → email com link de confirmação único (token)
4. Professor clica no link → `confirmarConvocatoria($token)` → `estado_confirmacao = 'Confirmado'`, `data_confirmacao` preenchida
5. Após exame → secretaria regista `presenca` (Presente / Falta / Falta Justificada)
6. PDF para afixação: `gerarPdfConvocatorias($sessaoId)`

### `estado_confirmacao` (convocatoria)
- `Pendente` → estado inicial
- `Confirmado` → professor confirmou via link no email
- `Rejeitado` → professor recusou

### `presenca` (convocatoria) — preenchida APÓS o exame
- `Pendente` → por confirmar
- `Presente` → esteve presente
- `Falta` → não compareceu
- `Falta Justificada` → falta com justificação

---

## Níveis de utilizador com acesso

```php
// Função requireSecExamesPermissions() no BaseController
in_array($userLevel, [4, 8, 9])
```

| Level | Papel | Acesso |
|---|---|---|
| 4 | Secretaria Exames | Completo |
| 8 | Administrador | Completo |
| 9 | Super-admin | Completo |

---

## Rotas (`app/Config/Routes.php`)

### Grupo `/exames`
```
GET  /exames/                   → ExameController::index
POST /exames/getDataTable        → ExameController::getDataTable
GET  /exames/get/(:num)          → ExameController::get/$1
POST /exames/store               → ExameController::store
POST /exames/update/(:num)       → ExameController::update/$1
POST /exames/delete/(:num)       → ExameController::delete/$1
GET  /exames/tipo/(:any)         → ExameController::getByTipo/$1
GET  /exames/ano/(:num)          → ExameController::getByAno/$1
```

### Grupo `/sessoes-exame`
```
GET  /sessoes-exame/                              → SessaoExameController::index
POST /sessoes-exame/getDataTable                  → SessaoExameController::getDataTable
GET  /sessoes-exame/detalhes/(:num)               → SessaoExameController::detalhes/$1
GET  /sessoes-exame/alocar-salas/(:num)           → SessaoExameSalaController::alocarSalas/$1
GET  /sessoes-exame/get/(:num)                    → SessaoExameController::get/$1
POST /sessoes-exame/store                         → SessaoExameController::store
POST /sessoes-exame/update/(:num)                 → SessaoExameController::update/$1
POST /sessoes-exame/delete/(:num)                 → SessaoExameController::delete/$1
GET  /sessoes-exame/calcular-vigilantes/(:num)    → SessaoExameController::calcularVigilantes/$1
POST /sessoes-exame/enviar-convocatoria/(:num)    → SessaoExameController::enviarConvocatoria/$1
POST /sessoes-exame/enviar-convocatorias-todas/(:num) → SessaoExameController::enviarConvocatoriasTodas/$1
GET  /sessoes-exame/confirmar/(:any)              → SessaoExameController::confirmarConvocatoria/$1
GET  /sessoes-exame/gerar-pdf/(:num)              → SessaoExameController::gerarPdfConvocatorias/$1
GET  /sessoes-exame/calendario                    → SessaoExameController::calendario
GET  /sessoes-exame/calendario-eventos            → SessaoExameController::getCalendarioEventos
```

### Grupo `/sessoes-exame-salas`
```
POST /sessoes-exame-salas/getDataTable            → SessaoExameSalaController::getDataTable
GET  /sessoes-exame-salas/get/(:num)              → SessaoExameSalaController::get/$1
POST /sessoes-exame-salas/store                   → SessaoExameSalaController::store
POST /sessoes-exame-salas/update/(:num)           → SessaoExameSalaController::update/$1
POST /sessoes-exame-salas/delete/(:num)           → SessaoExameSalaController::delete/$1
GET  /sessoes-exame-salas/getSalasDisponiveis     → SessaoExameSalaController::getSalasDisponiveis
GET  /sessoes-exame-salas/estatisticas/(:num)     → SessaoExameSalaController::getEstatisticas/$1
```

---

## Convenções de código deste módulo

- **AJAX/DataTables**: `POST /getDataTable` com resposta `{ data: [...] }`
- **API simples**: `$this->response->setJSON([...])` ou `return $this->respond([...])`
- **Log obrigatório** em todos os CRUD: `log_activity('exames', $action, $id, $descricao, $old, $new, $severity)`
- **Soft delete** em `sessao_exame_sala` — usar `$model->delete($id)` (não DELETE direto) para respeitar `deleted_at`
- **Bootstrap 5** — badges escuros: `text-white` com `bg-primary/danger/success`; `text-dark` só com `bg-warning/bg-light`
- **SweetAlert2** para confirmações de eliminação e feedbacks

## Migrations SQL relacionadas (na raiz do projeto)
- `CREATE_SISTEMA_CONVOCATORIAS_EXAMES.sql` — criação base das 4 tabelas + views + dados iniciais
- `ALTER_EXAME_ADD_PROVA_ENSAIO.sql` — adicionou "Prova ensaio" ao ENUM tipo_prova
- `ALTER_PROVA_ENSAIO_TO_FASE.sql` — moveu "Prova Ensaio" de tipo_prova para sessao_exame.fase
- `ALTER_SESSAO_EXAME_ADD_ANO_LETIVO.sql` — adicionou FK ano_letivo_id a sessao_exame
- `MIGRATION_EXAMES_SUPLENTES.sql` — criou tipos "Suplentes" e exames virtuais SUP-MANHA/SUP-TARDE

## Constraints

- NÃO usar `deleted_at` manualmente em `sessao_exame_sala` — usar sempre `$model->delete()` do CI4
- NÃO omitir `log_activity()` em store/update/delete
- NÃO ignorar a regra de conflito de horário ao alocar salas (manhã/tarde no mesmo dia)
- NÃO omitir `ano_letivo_id` ao criar sessões — é preenchido automaticamente com o ano letivo ativo
- Convocatórias com `funcao` de tipo especial (`Verificar Calculadoras`, `Apoio TIC`, `Estrutura de Apoio`) não precisam de `sessao_exame_sala_id`
