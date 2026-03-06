# Sistema de Convocatórias para Vigilância de Exames

## 📋 Visão Geral

Este sistema permite a gestão completa de convocatórias de professores para vigilância de exames, incluindo:
- Provas Finais do Ensino Básico (4º, 6º, 9º anos)
- Exames Nacionais do Ensino Secundário (11º e 12º anos)
- Provas MODa (Modalidades Artísticas Especializadas)

## 🗄️ Estrutura da Base de Dados

### 1. Tabela `exame`
Armazena os dados de identificação de cada prova.

**Campos:**
- `id` - Chave primária (auto-incremento)
- `codigo_prova` - Código oficial da prova (único) - Ex: 639, 91, 635
- `nome_prova` - Nome da prova - Ex: "Português", "Matemática A"
- `tipo_prova` - ENUM('Exame Nacional', 'Prova Final', 'MODa')
- `ano_escolaridade` - Ano de escolaridade (4, 6, 9, 11, 12)
- `ativo` - Estado (1=Ativo, 0=Inativo)
- `created_at` - Data de criação
- `updated_at` - Data de atualização

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE KEY (`codigo_prova`)
- KEY (`tipo_prova`)
- KEY (`ano_escolaridade`)

### 2. Tabela `sessao_exame`
Armazena os detalhes de cada ocorrência de uma prova (fases e turnos).

**Campos:**
- `id` - Chave primária (auto-incremento)
- `exame_id` - FK para `exame.id`
- `fase` - Fase/Turno do exame (Ex: "1ª Fase", "2ª Fase", "Especial")
- `data_exame` - Data da sessão (DATE)
- `hora_exame` - Hora de início (TIME)
- `duracao_minutos` - Duração total da prova em minutos
- `tolerancia_minutos` - Duração de tolerância em minutos
- `num_alunos` - Número estimado de alunos (opcional)
- `observacoes` - Observações adicionais (TEXT)
- `ativo` - Estado (1=Ativo, 0=Cancelado)
- `created_at` - Data de criação
- `updated_at` - Data de atualização

**Índices:**
- PRIMARY KEY (`id`)
- KEY (`exame_id`)
- KEY (`data_exame`)
- FOREIGN KEY (`exame_id`) REFERENCES `exame`(`id`) ON DELETE CASCADE

### 3. Tabela `convocatoria`
Liga professores às sessões de exame com funções específicas.

**Campos:**
- `id` - Chave primária (auto-incremento)
- `sessao_exame_id` - FK para `sessao_exame.id`
- `user_id` - FK para `user.id` (Professor)
- `sala_id` - FK para `salas.id` (NULL para Suplentes)
- `funcao` - ENUM('Vigilante', 'Suplente', 'Coadjuvante', 'Júri', 'Verificar Calculadoras', 'Apoio TIC')
- `estado_confirmacao` - ENUM('Pendente', 'Confirmado', 'Rejeitado') - Default: 'Pendente'
- `data_confirmacao` - Data e hora da confirmação (NULL se pendente)
- `observacoes` - Observações do professor ou coordenador (TEXT)
- `created_at` - Data de criação
- `updated_at` - Data de atualização

**Índices:**
- PRIMARY KEY (`id`)
- KEY (`sessao_exame_id`)
- KEY (`user_id`)
- KEY (`sala_id`)
- KEY (`estado_confirmacao`)
- FOREIGN KEY (`sessao_exame_id`) REFERENCES `sessao_exame`(`id`) ON DELETE CASCADE
- FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
- FOREIGN KEY (`sala_id`) REFERENCES `salas`(`id`) ON DELETE SET NULL

## 📚 Códigos Oficiais das Provas

### Provas Finais do Ensino Básico
| Código | Nome | Ano |
|--------|------|-----|
| 21 | Português | 4º |
| 22 | Matemática | 4º |
| 81 | Português | 6º |
| 82 | Matemática | 6º |
| 91 | Português | 9º |
| 92 | Matemática | 9º |

### Exames Nacionais - 11º Ano
| Código | Nome |
|--------|------|
| 708 | Geometria Descritiva A |
| 712 | Economia A |
| 715 | Filosofia |
| 719 | Geografia A |
| 723 | História B |
| 724 | História da Cultura e das Artes |
| 732 | Física e Química A |
| 835 | Matemática Aplicada às Ciências Sociais |

### Exames Nacionais - 12º Ano
| Código | Nome |
|--------|------|
| 502 | Alemão |
| 517 | Francês |
| 547 | Espanhol |
| 550 | Inglês |
| 635 | Matemática A |
| 639 | Português |
| 702 | Biologia e Geologia |
| 706 | Desenho A |
| 710 | História A |
| 714 | Literatura Portuguesa |
| 735 | Matemática B |

### Provas MODa
| Código | Nome |
|--------|------|
| 310 | Instrumento - Sopros e Percussão |
| 311 | Instrumento - Cordas e Teclas |
| 312 | Formação Musical |
| 323 | Prova de Aptidão Artística - Dança |

## 🚀 Instalação

### 1. Executar Migrations

```bash
php spark migrate
```

Isto criará as 3 tabelas na base de dados.

### 2. Executar Seeder

```bash
php spark db:seed ExameSeeder
```

Isto populará a tabela `exame` com todos os códigos oficiais das provas.

## 💻 Models Disponíveis

### ExameModel
**Métodos principais:**
- `getByTipo($tipo)` - Busca exames por tipo
- `getByAnoEscolaridade($ano)` - Busca exames por ano
- `getByCodigo($codigo)` - Busca exame por código
- `getExamesAtivos($limit, $offset)` - Lista exames ativos com paginação
- `countByTipo()` - Conta exames por tipo

### SessaoExameModel
**Métodos principais:**
- `getWithExame($id)` - Busca sessões com informações do exame
- `getByData($data)` - Busca sessões por data específica
- `getByPeriodo($dataInicio, $dataFim)` - Busca sessões num período
- `getSessoesFuturas($limite)` - Busca próximas sessões
- `getByExame($exameId)` - Busca sessões de um exame específico
- `getVigilantesNecessarios($sessaoId)` - Calcula vigilantes necessários
- `hasConflito($data, $horaInicio)` - Verifica conflitos de horário

### ConvocatoriaModel
**Métodos principais:**
- `getWithDetails($id)` - Busca convocatórias com todas as informações
- `getBySessao($sessaoId)` - Busca convocatórias de uma sessão
- `getByProfessor($userId)` - Busca convocatórias de um professor
- `getPendentes($userId)` - Busca convocatórias pendentes
- `confirmar($id, $observacoes)` - Confirma uma convocatória
- `rejeitar($id, $observacoes)` - Rejeita uma convocatória
- `hasConflitoHorario($userId, $sessaoId)` - Verifica conflitos de horário
- `countByFuncao($sessaoId)` - Conta convocatórias por função
- `getByData($data, $userId)` - Busca convocatórias por data
- `getEstatisticas($sessaoId)` - Estatísticas de confirmações

## 🎯 Funcionalidades Implementadas

### ✅ Gestão de Exames
- ✅ Catálogo completo de códigos oficiais
- ✅ Organização por tipo (Exame Nacional, Prova Final, MODa)
- ✅ Filtros por ano de escolaridade
- ✅ Sistema de ativação/desativação

### ✅ Gestão de Sessões
- ✅ Múltiplas sessões por exame (1ª Fase, 2ª Fase, Especial)
- ✅ Controlo de data e hora
- ✅ Duração e tolerância configuráveis
- ✅ Cálculo automático de vigilantes necessários
- ✅ Deteção de conflitos de horário
- ✅ Campos para número de alunos e observações

### ✅ Gestão de Convocatórias
- ✅ Atribuição de professores por função:
  - Vigilante
  - Suplente
  - Coadjuvante
  - Júri
  - Verificar Calculadoras
  - Apoio TIC
- ✅ Associação opcional a salas (suplentes não têm sala)
- ✅ Sistema de confirmação (Pendente/Confirmado/Rejeitado)
- ✅ Registo de data e hora de confirmação
- ✅ Campo para observações
- ✅ Deteção de conflitos de horário do professor
- ✅ Listagens por professor, sessão ou data
- ✅ Estatísticas de confirmações

## 🔒 Regras de Negócio

### Vigilantes Necessários
- **Regra base:** 1 vigilante por cada 20 alunos
- **Mínimo:** 2 vigilantes por sala
- **Suplentes:** 1 suplente por cada 3 vigilantes (mínimo 1)

### Conflitos de Horário
O sistema verifica automaticamente se:
- Um professor já está convocado para outra sessão no mesmo horário
- Considera a duração completa + tolerância da prova
- Previne sobreposições de horários

### Confirmação
- Estado inicial: **Pendente**
- Professor pode confirmar: **Confirmado**
- Professor pode rejeitar: **Rejeitado** (com observações)
- Data de confirmação é registada automaticamente

## 📊 Melhorias Adicionadas

Além da estrutura solicitada, foram adicionados:

1. **Campos `created_at` e `updated_at`** em todas as tabelas para auditoria
2. **Campo `ativo`** nas tabelas `exame` e `sessao_exame` para soft-delete
3. **Campo `num_alunos`** em `sessao_exame` para cálculo de vigilantes
4. **Campo `observacoes`** em `sessao_exame` e `convocatoria`
5. **Campo `tolerancia_minutos`** separado da duração
6. **Estado `Rejeitado`** no enum de confirmação
7. **Índices otimizados** para melhor performance
8. **Foreign keys com CASCADE e SET NULL** apropriados
9. **Validações completas** nos Models
10. **Métodos auxiliares** para operações comuns

## 🔄 Próximos Passos Sugeridos

Para completar o sistema, considere implementar:

1. **Controllers:**
   - `ExameController` - CRUD de exames
   - `SessaoExameController` - CRUD de sessões
   - `ConvocatoriaController` - Gestão de convocatórias

2. **Views:**
   - Dashboard com calendário de exames
   - Lista de convocatórias pendentes
   - Formulários de criação/edição
   - Área do professor para confirmação

3. **Funcionalidades Adicionais:**
   - Notificações por email aos professores
   - Exportação de mapas de vigilância (PDF)
   - Relatórios estatísticos
   - Integração com sistema de horários
   - Histórico de convocatórias

4. **Permissões:**
   - Coordenadores: criar sessões e convocatórias
   - Professores: ver e confirmar suas convocatórias
   - Admin: acesso total

## 📝 Exemplo de Uso

### Criar uma Sessão de Exame

```php
$sessaoModel = new SessaoExameModel();

$data = [
    'exame_id' => 12, // Matemática A
    'fase' => '1ª Fase',
    'data_exame' => '2026-06-18',
    'hora_exame' => '09:30:00',
    'duracao_minutos' => 150,
    'tolerancia_minutos' => 30,
    'num_alunos' => 45,
    'observacoes' => 'Exame com calculadora gráfica'
];

$sessaoId = $sessaoModel->insert($data);
```

### Criar Convocatórias

```php
$convocatoriaModel = new ConvocatoriaModel();

// Vigilante principal
$convocatoriaModel->insert([
    'sessao_exame_id' => $sessaoId,
    'user_id' => 15,
    'sala_id' => 8,
    'funcao' => 'Vigilante'
]);

// Suplente (sem sala)
$convocatoriaModel->insert([
    'sessao_exame_id' => $sessaoId,
    'user_id' => 23,
    'sala_id' => null,
    'funcao' => 'Suplente'
]);
```

### Confirmar Convocatória

```php
$convocatoriaModel = new ConvocatoriaModel();
$convocatoriaModel->confirmar(5, 'Confirmo presença');
```

### Listar Convocatórias do Professor

```php
$convocatoriaModel = new ConvocatoriaModel();
$minhasConvocatorias = $convocatoriaModel->getByProfessor(session('user_id'));
```

---

**Data de Criação:** 30/01/2026  
**Versão:** 1.0  
**Autor:** Sistema de Gestão Escolar
