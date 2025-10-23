# 📊 Plano de Dashboard Personalizado por Nível de Utilizador

## Sistema de Gestão Escolar

### Módulos Implementados/Planeados:
1. ✅ Gestão de Tickets de Equipamentos Avariados (Implementado)
2. 🔜 Gestão de Trocas de Aulas/Horários entre Professores
3. 🔜 Outros módulos

---

## 🎯 Níveis de Utilizador

| Nível | Perfil | Permissões |
|-------|--------|------------|
| 0-4 | Utilizadores Básicos | Professores, Alunos, Staff - Acesso limitado |
| 5-7 | Técnicos | Resolução de tickets, gestão de equipamentos |
| 8 | Administradores | Supervisão geral, atribuição de tickets |
| 9 | Super Administradores | Controlo total do sistema |

---

## 💡 Dashboard por Perfil

### 1️⃣ Utilizadores Básicos (Level 0-4) - Professores/Staff

**Objetivo:** Tarefas do dia-a-dia e autoserviço

#### Cards Principais:
- 📋 **Meus Tickets Ativos**
  - Equipamentos avariados criados por mim
  - Estado atual de cada ticket
  - Tempo estimado de resolução

- ⏰ **Minhas Trocas de Aulas** 
  - Pendentes de aprovação/confirmação
  - Histórico recente
  - Trocas em negociação

- 📅 **Horário da Semana**
  - Resumo visual do horário atual
  - Próximas aulas destacadas
  - Alterações recentes

- 🔔 **Notificações Recentes**
  - Últimas 5 notificações
  - Atualizações de tickets
  - Aprovações de trocas

#### Ações Rápidas:
- ➕ Criar Ticket de Avaria
- 🔄 Solicitar Troca de Aula
- 📖 Ver Meu Horário Completo

#### Widgets Adicionais:
- 📆 Calendário mensal com eventos importantes
- 📢 Avisos/comunicados da escola
- 📊 Estatísticas pessoais (tickets resolvidos, trocas realizadas)

---

### 2️⃣ Técnicos (Level 5-7)

**Objetivo:** Gestão de trabalho técnico e produtividade

#### Cards Principais:
- 🔧 **Tickets Atribuídos a Mim**
  - Tickets aguardando resolução
  - Ordenados por prioridade
  - Com indicador de SLA

- ⚠️ **Tickets Urgentes**
  - Prioridade alta/crítica
  - Alertas visuais
  - Tempo restante para resolução

- ⏱️ **Tickets Aguardam Peça**
  - Materiais solicitados
  - Previsão de chegada
  - Ações pendentes

- 📊 **Minhas Estatísticas do Mês**
  - Tickets resolvidos
  - Tempo médio de resolução
  - Taxa de reabertura
  - Avaliação de desempenho

- 📍 **Tickets por Localização**
  - Distribuição por escola/sala
  - Mapa de calor (opcional)
  - Priorização geográfica

#### Gráficos:
- 📈 Evolução de tickets resolvidos (últimos 7 dias)
- 🔧 Tipos de avaria mais comuns
- 💻 Equipamentos mais problemáticos
- ⏰ Tempo médio de resolução por tipo

#### Ações Rápidas:
- 🎫 Ver Todos os Tickets em Tratamento
- 📦 Registar Material Necessário
- 🏫 Ver Equipamentos por Sala
- ✅ Marcar Ticket como Resolvido

---

### 3️⃣ Administradores (Level 8)

**Objetivo:** Supervisão geral e gestão de recursos

#### Cards Principais:

##### 📈 Visão Geral do Sistema:
- Total de tickets por estado:
  - 🆕 Novos
  - ⚙️ Em Resolução
  - 🔄 Aguarda Peça
  - ✅ Reparados
  - ❌ Anulados
- Tickets não atribuídos (alerta)
- Trocas de aula pendentes
- KPIs principais

##### 👥 Gestão de Técnicos:
- Disponibilidade em tempo real
- Carga de trabalho (tickets por técnico)
- Performance individual:
  - Tempo médio de resolução
  - Taxa de sucesso
  - Tickets resolvidos no mês
- Técnicos sobrecarregados (alerta)

##### 🏫 Gestão por Escola:
- Tickets ativos por localização
- Equipamentos em estado crítico
- Salas com mais problemas
- Comparativo entre escolas

##### ⚠️ Alertas e Notificações:
- 🚨 Tickets sem atribuição há mais de 24h
- ⏰ Tickets em atraso
- 📝 Trocas de aula não aprovadas
- 💻 Equipamentos inativos há muito tempo
- 📊 Anomalias no sistema

#### Gráficos Avançados:
- 📊 Tendências mensais (tickets, trocas)
- 🎯 Taxa de resolução por técnico
- 🔴 Distribuição de prioridades
- 💻 Equipamentos por estado
- 📍 Mapa de calor por localização
- 📈 Evolução de SLA

#### Ações Rápidas:
- 🎫 Atribuir Tickets Manualmente
- ✅ Aprovar/Rejeitar Trocas de Aulas
- 📄 Gerar Relatórios
- 📋 Ver Logs de Atividade
- 👥 Gerir Utilizadores
- 📧 Enviar Comunicados

---

### 4️⃣ Super Administradores (Level 9)

**Objetivo:** Administração completa do sistema

#### Inclui tudo do nível 8, mais:

##### 🔐 Gestão de Sistema:
- Utilizadores ativos/inativos
- Níveis de acesso e permissões
- Configurações globais
- Backup e restauração
- Logs de auditoria completos

##### 🏢 Gestão de Escolas:
- CRUD completo de escolas
- Estatísticas detalhadas por escola
- Transferência de recursos
- Configuração de hierarquias

##### 📊 Analytics Avançado:
- Relatórios personalizados
- Exportação de dados (Excel, PDF, CSV)
- Auditoria completa de ações
- Business Intelligence
- Previsões e tendências

##### ⚙️ Configurações do Sistema:
- Tipos de equipamentos
- Tipos de avaria
- Materiais disponíveis
- Templates de email
- Parâmetros de SLA
- Regras de negócio

---

## 🎨 Design e Layout

### Estrutura Visual:

```
┌─────────────────────────────────────────────────────┐
│  🏠 Breadcrumb Navigation                           │
├─────────────────────────────────────────────────────┤
│  ┌───────┐ ┌───────┐ ┌───────┐ ┌───────┐          │
│  │  📋   │ │  ⚠️   │ │  ✅   │ │  👥   │  ← Stats  │
│  │  12   │ │   5   │ │   8   │ │ 100% │          │
│  │Tickets│ │Urgent │ │Solved │ │ Rate │          │
│  └───────┘ └───────┘ └───────┘ └───────┘          │
├──────────────────────────┬──────────────────────────┤
│                          │                          │
│  📊 Lista Principal      │  ⚡ Ações Rápidas       │
│  ┌────────────────────┐  │  ┌────────────────────┐ │
│  │ Tickets/Trocas     │  │  │ ➕ Nova Ação       │ │
│  │ DataTable          │  │  │ 🔄 Atualizar       │ │
│  │ Filtros avançados  │  │  │ 📄 Relatório       │ │
│  │ Ordenação          │  │  └────────────────────┘ │
│  └────────────────────┘  │                          │
│                          │  🔔 Notificações        │
│                          │  ┌────────────────────┐ │
│                          │  │ • Ticket #123...   │ │
│                          │  │ • Troca aprovada   │ │
│                          │  │ • Novo equipamento │ │
│                          │  └────────────────────┘ │
├──────────────────────────┴──────────────────────────┤
│  📈 Gráficos e Analytics (por nível)                │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐│
│  │ Line Chart   │ │ Bar Chart    │ │ Pie Chart    ││
│  │ Tendências   │ │ Comparativo  │ │ Distribuição ││
│  └──────────────┘ └──────────────┘ └──────────────┘│
└─────────────────────────────────────────────────────┘
```

### Cores e Badges (Sistema Atual):

#### Prioridades:
- 🟢 **Baixa**: `bg-success text-white` (Verde)
- 🟡 **Média**: `bg-warning text-dark` (Amarelo com texto preto)
- 🟠 **Alta**: `bg-orange text-white` (Laranja)
- 🔴 **Crítica**: `bg-danger text-white` (Vermelho)

#### Estados de Ticket:
- 🔵 **Novo**: `bg-primary text-white` (Azul)
- 🟡 **Em Resolução**: `bg-warning text-dark` (Amarelo)
- 🔷 **Aguarda Peça**: `bg-info text-white` (Ciano)
- 🟢 **Reparado**: `bg-success text-white` (Verde)
- 🔴 **Anulado**: `bg-danger text-white` (Vermelho)

#### Estados de Equipamento:
- 🟢 **Ativo**: `bg-success` (Verde)
- 🔴 **Inativo**: `bg-danger` (Vermelho)
- 🟡 **Pendente**: `bg-warning` (Amarelo)

---

## 🚀 Funcionalidades Adicionais Sugeridas

### 1. Ações Rápidas Contextuais
- **FAB (Floating Action Button)** no canto inferior direito
- Menu de ações rápidas dependendo do nível
- Atalhos para ações mais comuns

### 2. Notificações em Tempo Real
- WebSocket ou polling para atualizações automáticas
- Badge com contador no sino de notificações
- Som opcional para alertas críticos
- Notificações push (browser)

### 3. Widgets Personalizáveis
- Permitir ao utilizador escolher quais cards ver
- Drag-and-drop para reordenar widgets
- Salvar layout preferido por utilizador
- Opção de minimizar/expandir cards

### 4. Tema e Personalização
- Modo Dark/Light com toggle no header
- Salvar preferência por utilizador
- Cores personalizáveis (futuro)
- Tamanho de fonte ajustável

### 5. Atalhos de Teclado
- `Ctrl+N`: Novo ticket
- `Ctrl+T`: Nova troca de aula
- `Ctrl+S`: Pesquisar
- `Ctrl+H`: Ir para Home/Dashboard
- `Esc`: Fechar modais
- `F5`: Atualizar dados

### 6. Pesquisa Global
- Barra de pesquisa no header
- Pesquisa em tickets, equipamentos, utilizadores
- Sugestões automáticas (autocomplete)
- Pesquisa avançada com filtros

### 7. Modo Offline
- Cache de dados críticos
- Sincronização automática ao reconectar
- Indicador de status de conexão

### 8. Exportação e Relatórios
- Exportar para Excel/PDF/CSV
- Relatórios agendados
- Templates personalizáveis
- Gráficos exportáveis

### 9. Mobile First
- Design totalmente responsivo
- Menu hamburger para mobile
- Touch gestures
- PWA (Progressive Web App)

### 10. Integração com Email
- Notificações por email
- Templates HTML consistentes (já implementado)
- Responder tickets por email (futuro)

---

## 📋 Roadmap de Implementação

### Fase 1: Dashboard Básico (Atual - Próximos Sprints)
- ✅ Sistema de tickets implementado
- 🔜 Dashboard personalizado por nível
- 🔜 Cards de estatísticas básicas
- 🔜 Lista de tickets/ações recentes

### Fase 2: Analytics e Gráficos
- 📊 Implementar Chart.js ou similar
- 📈 Gráficos de tendências
- 🎯 KPIs e métricas
- 📉 Relatórios visuais

### Fase 3: Gestão de Trocas de Aulas
- 📅 CRUD de trocas de aulas
- 🔔 Sistema de aprovação
- 📧 Notificações automáticas
- 🔄 Integração com horários

### Fase 4: Personalização
- 🎨 Widgets drag-and-drop
- 🌓 Modo dark/light
- ⚙️ Configurações por utilizador
- 🔔 Preferências de notificação

### Fase 5: Mobile e PWA
- 📱 Otimização mobile
- 📲 Progressive Web App
- 🔔 Push notifications
- 📴 Modo offline

---

## 🛠️ Tecnologias e Ferramentas

### Backend:
- **CodeIgniter 4.6.1**
- **PHP 8.x**
- **MySQL**
- **RESTful API**

### Frontend:
- **AdminLTE 4** (Bootstrap 5)
- **jQuery 3.7.1**
- **DataTables**
- **Chart.js** (a implementar)
- **Toastr** (notificações)
- **Font Awesome / Bootstrap Icons**

### Bibliotecas Adicionais (Futuro):
- **FullCalendar** (para horários)
- **Select2** (dropdowns avançados)
- **SortableJS** (drag-and-drop)
- **Moment.js** (manipulação de datas)

---

## 📝 Notas de Implementação

### Controllers Necessários:
- `DashboardController.php` (novo - centraliza lógica de dashboard)
- `TicketsController.php` (✅ já existe)
- `TrocasAulasController.php` (a criar)
- `HorariosController.php` (a criar)

### Models Necessários:
- `TicketsModel.php` (✅ já existe)
- `TrocasAulasModel.php` (a criar)
- `HorariosModel.php` (a criar)
- `NotificacoesModel.php` (a criar)

### Views Necessárias:
- `dashboard/index.php` (principal - substitui dashboard.php atual)
- `dashboard/widgets/` (cards reutilizáveis)
- `dashboard/charts/` (gráficos)

### Rotas:
```php
// Dashboard principal (personalizado por nível)
$routes->get('dashboard', 'DashboardController::index');

// APIs para widgets
$routes->get('dashboard/stats', 'DashboardController::getStats');
$routes->get('dashboard/recent-activity', 'DashboardController::getRecentActivity');
$routes->get('dashboard/charts/(:any)', 'DashboardController::getChartData/$1');

// Trocas de aulas (futuro)
$routes->group('trocas-aulas', function($routes) {
    $routes->get('/', 'TrocasAulasController::index');
    $routes->post('create', 'TrocasAulasController::create');
    $routes->post('approve/(:num)', 'TrocasAulasController::approve/$1');
    $routes->post('reject/(:num)', 'TrocasAulasController::reject/$1');
});
```

---

## 🎯 Métricas de Sucesso

### Para Utilizadores Básicos:
- Redução do tempo para criar ticket
- Aumento da satisfação com feedback visual
- Menos emails/chamadas de suporte

### Para Técnicos:
- Aumento de tickets resolvidos por dia
- Redução do tempo médio de resolução
- Melhor distribuição de carga de trabalho

### Para Administradores:
- Visibilidade completa do sistema
- Decisões baseadas em dados
- Redução de tickets em atraso
- Otimização de recursos

---

## 📚 Referências e Inspiração

- AdminLTE Dashboard Examples
- GitHub Issues Dashboard
- Jira Dashboard
- Trello Boards
- Google Analytics Dashboard

---

## 📅 Data de Criação
**14 de Outubro de 2025**

## 👤 Autor
Sistema de Gestão Escolar - HardWork550

---

## 🔄 Atualizações Futuras
Este documento será atualizado conforme o desenvolvimento progride e novas necessidades são identificadas.

---

**Nota:** Este é um documento vivo e deve ser atualizado regularmente com feedback dos utilizadores e novas ideias de funcionalidades.
