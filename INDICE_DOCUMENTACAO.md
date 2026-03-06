# 📚 Índice de Documentação - Sistema de Gestão Escolar

## 🏠 Documentação Principal

### 📖 Guias Gerais
- **[README_SISTEMA.md](README_SISTEMA.md)** - Visão geral completa do sistema
  - Módulos implementados
  - Instalação rápida
  - Estrutura do projeto
  - Tecnologias utilizadas
  - Changelog completo

---

## 📋 Sistema de Exames e Convocatórias (v2.0)

### 🚀 Guias de Instalação
- **[INSTALACAO_SISTEMA_EXAMES.md](INSTALACAO_SISTEMA_EXAMES.md)** - Guia completo de instalação
  - 27 ficheiros criados
  - Menu e rotas
  - 5 funcionalidades principais
  - Dados pré-carregados (30 códigos oficiais)
  - Checklist de implementação

- **[INSTALACAO_RAPIDA_SALAS.md](INSTALACAO_RAPIDA_SALAS.md)** - Instalação rápida (2 passos)
  - Guia prático de alocação de salas
  - Exemplos de uso
  - Regras de vigilantes (MODa vs Normal)
  - Verificações pós-instalação

### 📘 Documentação Técnica
- **[IMPLEMENTACAO_CONVOCATORIAS_EXAMES.md](IMPLEMENTACAO_CONVOCATORIAS_EXAMES.md)** - Sistema base
  - Estrutura de base de dados (3 tabelas)
  - Models com validações
  - Controllers e rotas
  - Workflows de aprovação
  - Queries úteis

- **[IMPLEMENTACAO_ALOCACAO_SALAS_EXAMES.md](IMPLEMENTACAO_ALOCACAO_SALAS_EXAMES.md)** - Alocação de salas v2.0
  - Nova tabela `sessao_exame_sala`
  - Alterações em `convocatoria`
  - Cálculo inteligente de vigilantes
  - Calendário visual FullCalendar
  - Estatísticas em tempo real

- **[RESUMO_CONVOCATORIAS.md](RESUMO_CONVOCATORIAS.md)** - Resumo executivo
  - Visão geral do sistema
  - Principais funcionalidades
  - Casos de uso

### 🗄️ Scripts SQL
- **[CREATE_SISTEMA_CONVOCATORIAS_EXAMES.sql](CREATE_SISTEMA_CONVOCATORIAS_EXAMES.sql)** - Script de criação completo
- **[MIGRATION_ALOCACAO_SALAS.sql](MIGRATION_ALOCACAO_SALAS.sql)** - Migração de salas (v2.0)
- **[QUERIES_UTEIS_CONVOCATORIAS.sql](QUERIES_UTEIS_CONVOCATORIAS.sql)** - 15+ queries prontas

---

## 💻 Gestão de Equipamentos

### 📘 Documentação
- **[IMPLEMENTACAO_EQUIPAMENTOS.md](IMPLEMENTACAO_EQUIPAMENTOS.md)** - Sistema de equipamentos
  - Inventário completo
  - Estados e movimentações
  - Atribuições a utilizadores
  - Histórico de alterações

- **[IMPLEMENTACAO_AVARIAS_KIT.md](IMPLEMENTACAO_AVARIAS_KIT.md)** - Sistema de avarias
  - Formulário público de reporte
  - Gestão de tickets
  - Estados do processo
  - Notificações automáticas

- **[IMPLEMENTACAO_INUTILIZADOS_KITDIGITAL.md](IMPLEMENTACAO_INUTILIZADOS_KITDIGITAL.md)** - Gestão de inutilizados
  - Registo de equipamentos descartados
  - Motivos de inutilização
  - Relatórios

- **[DOCUMENTACAO_REPARACOES_EXTERNAS.md](DOCUMENTACAO_REPARACOES_EXTERNAS.md)** - Reparações externas
  - Controlo de reparações terceirizadas
  - Importação CSV
  - Estados do processo
  - Histórico completo

### 📊 Dashboards
- **[IMPLEMENTACAO_DASHBOARD_TECNICOS.md](IMPLEMENTACAO_DASHBOARD_TECNICOS.md)** - Dashboard técnicos
  - Widgets de estatísticas
  - Gráficos interativos
  - Alertas e notificações
  - Visão geral de tickets

---

## 📚 Gestão Letiva

### 📘 Documentação
- **[IMPLEMENTACAO_GESTAO_LETIVA.md](IMPLEMENTACAO_GESTAO_LETIVA.md)** - Sistema completo
  - Disciplinas
  - Professores
  - Turmas
  - Salas por escola
  - Horários

- **[app/Views/salas/instrucoes_salas.md](app/Views/salas/instrucoes_salas.md)** - Gestão de salas
  - CRUD de salas
  - Filtragem por escola
  - Capacidade e equipamentos

---

## 🔄 Sistema de Permutas

### 📘 Documentação
- **[IMPLEMENTACAO_PERMUTAS.md](IMPLEMENTACAO_PERMUTAS.md)** - Sistema de permutas
  - Workflow de aprovação
  - Estados das permutas
  - Validações de conflito
  - Histórico

- **[IMPLEMENTACAO_ANO_LETIVO_PERMUTAS.md](IMPLEMENTACAO_ANO_LETIVO_PERMUTAS.md)** - Contexto de ano letivo
  - Gestão de anos letivos
  - Permutas por período
  - Relatórios anuais

---

## 🔐 Autenticação e Segurança

### 📘 Documentação
- **[GOOGLE_OAUTH_SETUP.md](GOOGLE_OAUTH_SETUP.md)** - Configuração OAuth
  - Google Cloud Console setup
  - Credenciais e permissões
  - Integração CodeIgniter
  - Troubleshooting

- **[PERMISSOES_ACESSO.md](PERMISSOES_ACESSO.md)** - Sistema de permissões
  - Níveis de acesso (0-10)
  - Controlo por módulo
  - Gestão de utilizadores

---

## 🛠️ Sistemas de Suporte

### 📘 Documentação
- **[LOGS_IMPLEMENTATION_GUIDE.md](LOGS_IMPLEMENTATION_GUIDE.md)** - Sistema de logs
  - Registo de ações
  - Níveis de log
  - Visualização e filtros
  - Análise de erros

- **[SISTEMA_TOASTS.md](SISTEMA_TOASTS.md)** - Notificações toast
  - SweetAlert2 integration
  - Tipos de notificação
  - Mensagens de sucesso/erro

---

## 📊 Migrações e Atualizações

### 📘 Documentação
- **[MIGRACAO_ESTADOS_TICKET.md](MIGRACAO_ESTADOS_TICKET.md)** - Migração de estados
  - Alterações de schema
  - Scripts de atualização
  - Rollback procedures

---

## 🧪 Testes e Validação

### 📘 Documentação
- **[TESTES_EQUIPAMENTOS.md](TESTES_EQUIPAMENTOS.md)** - Testes de equipamentos
  - Casos de teste
  - Validações
  - Resultados esperados

---

## 📝 Planeamento

### 📘 Documentação
- **[PLANO_DASHBOARD.md](PLANO_DASHBOARD.md)** - Planeamento de dashboards
  - Wireframes
  - Requisitos funcionais
  - Métricas a exibir

---

## 🔍 Índice por Categoria

### 📦 Por Módulo

#### Sistema de Exames (8 documentos)
1. INSTALACAO_SISTEMA_EXAMES.md
2. INSTALACAO_RAPIDA_SALAS.md
3. IMPLEMENTACAO_CONVOCATORIAS_EXAMES.md
4. IMPLEMENTACAO_ALOCACAO_SALAS_EXAMES.md
5. RESUMO_CONVOCATORIAS.md
6. CREATE_SISTEMA_CONVOCATORIAS_EXAMES.sql
7. MIGRATION_ALOCACAO_SALAS.sql
8. QUERIES_UTEIS_CONVOCATORIAS.sql

#### Equipamentos (5 documentos)
1. IMPLEMENTACAO_EQUIPAMENTOS.md
2. IMPLEMENTACAO_AVARIAS_KIT.md
3. IMPLEMENTACAO_INUTILIZADOS_KITDIGITAL.md
4. DOCUMENTACAO_REPARACOES_EXTERNAS.md
5. IMPLEMENTACAO_DASHBOARD_TECNICOS.md

#### Gestão Letiva (2 documentos)
1. IMPLEMENTACAO_GESTAO_LETIVA.md
2. app/Views/salas/instrucoes_salas.md

#### Permutas (2 documentos)
1. IMPLEMENTACAO_PERMUTAS.md
2. IMPLEMENTACAO_ANO_LETIVO_PERMUTAS.md

#### Autenticação (2 documentos)
1. GOOGLE_OAUTH_SETUP.md
2. PERMISSOES_ACESSO.md

#### Sistemas de Suporte (3 documentos)
1. LOGS_IMPLEMENTATION_GUIDE.md
2. SISTEMA_TOASTS.md
3. MIGRACAO_ESTADOS_TICKET.md

---

## 📖 Por Tipo de Documento

### 🚀 Instalação e Setup (3)
- INSTALACAO_SISTEMA_EXAMES.md
- INSTALACAO_RAPIDA_SALAS.md
- GOOGLE_OAUTH_SETUP.md

### 📘 Implementação Técnica (13)
- IMPLEMENTACAO_CONVOCATORIAS_EXAMES.md
- IMPLEMENTACAO_ALOCACAO_SALAS_EXAMES.md
- IMPLEMENTACAO_EQUIPAMENTOS.md
- IMPLEMENTACAO_AVARIAS_KIT.md
- IMPLEMENTACAO_INUTILIZADOS_KITDIGITAL.md
- IMPLEMENTACAO_GESTAO_LETIVA.md
- IMPLEMENTACAO_PERMUTAS.md
- IMPLEMENTACAO_ANO_LETIVO_PERMUTAS.md
- IMPLEMENTACAO_DASHBOARD_TECNICOS.md
- LOGS_IMPLEMENTATION_GUIDE.md
- SISTEMA_TOASTS.md
- MIGRACAO_ESTADOS_TICKET.md
- DOCUMENTACAO_REPARACOES_EXTERNAS.md

### 🗄️ Scripts SQL (3)
- CREATE_SISTEMA_CONVOCATORIAS_EXAMES.sql
- MIGRATION_ALOCACAO_SALAS.sql
- QUERIES_UTEIS_CONVOCATORIAS.sql

### 📊 Planeamento e Testes (3)
- PLANO_DASHBOARD.md
- TESTES_EQUIPAMENTOS.md
- PERMISSOES_ACESSO.md

### 📚 Resumos e Visão Geral (2)
- README_SISTEMA.md
- RESUMO_CONVOCATORIAS.md

---

## 🆕 Documentos Mais Recentes (Últimos 30 dias)

### 2 Fevereiro 2026
- ✨ **README_SISTEMA.md** - Visão geral completa (criado)
- 📝 **INDICE_DOCUMENTACAO.md** - Este ficheiro (criado)

### 31 Janeiro 2026
- ⚡ **INSTALACAO_SISTEMA_EXAMES.md** - Atualizado para v2.0
- ⚡ **IMPLEMENTACAO_ALOCACAO_SALAS_EXAMES.md** - Adicionado calendário
- ✨ **INSTALACAO_RAPIDA_SALAS.md** - Criado
- ✨ **MIGRATION_ALOCACAO_SALAS.sql** - Criado

### 30 Janeiro 2026
- ✨ Sistema base de convocatórias implementado
- ✨ 30 códigos oficiais de provas

---

## 🔗 Links Rápidos

### Para Começar
1. [Visão Geral do Sistema](README_SISTEMA.md)
2. [Instalar Sistema de Exames](INSTALACAO_SISTEMA_EXAMES.md)
3. [Configurar Google OAuth](GOOGLE_OAUTH_SETUP.md)

### Para Desenvolvedores
1. [Estrutura de Base de Dados - Exames](IMPLEMENTACAO_CONVOCATORIAS_EXAMES.md)
2. [Alocação de Salas - Técnico](IMPLEMENTACAO_ALOCACAO_SALAS_EXAMES.md)
3. [Sistema de Logs](LOGS_IMPLEMENTATION_GUIDE.md)

### Para Administradores
1. [Permissões de Acesso](PERMISSOES_ACESSO.md)
2. [Dashboard Técnicos](IMPLEMENTACAO_DASHBOARD_TECNICOS.md)
3. [Queries Úteis](QUERIES_UTEIS_CONVOCATORIAS.sql)

---

## 📊 Estatísticas da Documentação

- **Total de Documentos:** 25+
- **Documentos Markdown (.md):** 22
- **Scripts SQL (.sql):** 3
- **Páginas Totais:** ~400+
- **Última Atualização:** 2 Fevereiro 2026

---

## 🆘 Como Usar Este Índice

1. **Pesquise por módulo** - Use a seção "Por Módulo"
2. **Pesquise por tipo** - Use a seção "Por Tipo de Documento"
3. **Veja novidades** - Consulte "Documentos Mais Recentes"
4. **Links rápidos** - Use atalhos para documentos principais

---

## 💡 Convenções de Nomenclatura

- **INSTALACAO_** - Guias de instalação e setup
- **IMPLEMENTACAO_** - Documentação técnica de implementação
- **DOCUMENTACAO_** - Documentação geral de funcionalidades
- **_SETUP** - Configuração de sistemas externos
- **.sql** - Scripts de base de dados
- **README_** - Visões gerais e introduções

---

<div align="center">

**📚 Sistema de Gestão Escolar AEJB - Documentação 📚**

**Versão:** 2.0  
**Última Atualização:** 2 Fevereiro 2026

[🏠 Início](README_SISTEMA.md) | [📋 Exames](INSTALACAO_SISTEMA_EXAMES.md) | [💻 Equipamentos](IMPLEMENTACAO_EQUIPAMENTOS.md) | [🔐 OAuth](GOOGLE_OAUTH_SETUP.md)

</div>
