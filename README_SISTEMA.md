# 🏫 Sistema de Gestão Escolar - AEJB

## 📋 Sobre o Projeto

Sistema completo de gestão escolar desenvolvido em **CodeIgniter 4** com interface **AdminLTE 3**, criado para o **Agrupamento de Escolas João de Barros**.

**Framework:** CodeIgniter 4  
**Template:** AdminLTE 3  
**Database:** MySQL  
**PHP:** 8.1+

---

## ⭐ Módulos Implementados

### 🎓 Gestão Letiva
- **Disciplinas** - Gestão de disciplinas do currículo
- **Professores** - Cadastro e gestão de docentes
- **Turmas** - Organização de turmas por ano letivo
- **Salas** - Gestão de espaços físicos por escola
- **Horários** - Sistema de horários de aulas

### 📝 Sistema de Exames e Convocatórias (v2.0)
- **Exames/Provas** - 30 códigos oficiais (Nacionais, Finais, MODa)
- **Sessões de Exame** - Gestão de datas e horários
- **Alocação de Salas** ⭐ NOVO - Múltiplas salas por sessão
- **Calendário Visual** ⭐ NOVO - FullCalendar integration
- **Convocatórias** - Sistema completo de vigilância
- **Cálculo Automático** - 2 vigilantes/sala ou 1/20 para MODa

### 💻 Gestão de Equipamentos
- **Equipamentos** - Inventário completo de hardware
- **Requisições de Kit Digital** - Formulário público
- **Avarias de Kit** - Reporte público de problemas
- **Inutilizados** - Gestão de equipamentos descartados
- **Reparações Externas** - Controlo de reparações terceirizadas

### 🔄 Sistema de Permutas
- **Permutas** - Troca de aulas entre professores
- **Aprovação** - Workflow de validação
- **Ano Letivo** - Contexto temporal

### 📊 Dashboards
- **Dashboard Técnicos** - Visão geral de equipamentos e tickets
- **Dashboard Professores** - Área pessoal de convocatórias

### 🔐 Sistema de Autenticação
- **Login Tradicional** - Username/Password
- **Google OAuth** - Single Sign-On
- **Níveis de Acesso** - Sistema de permissões (0-10)

---

## 🚀 Instalação Rápida

### 1. Requisitos
- PHP 8.1 ou superior
- MySQL 5.7+ ou MariaDB
- Composer
- Extensões PHP: intl, mbstring, json, mysqlnd, curl

### 2. Clonar Repositório
```bash
git clone https://github.com/jaceman78/ci4-adminlte.git
cd ci4-adminlte
```

### 3. Instalar Dependências
```bash
composer install
```

### 4. Configurar Ambiente
```bash
cp env .env
```

Editar `.env`:
```ini
# Database
database.default.hostname = localhost
database.default.database = sistema_gestao
database.default.username = root
database.default.password = 

# Base URL
app.baseURL = 'http://localhost:8080/'

# Google OAuth (opcional)
GOOGLE_CLIENT_ID = your_client_id
GOOGLE_CLIENT_SECRET = your_client_secret
```

### 5. Executar Migrations
```bash
php spark migrate
```

### 6. Popular Dados Iniciais
```bash
# Códigos de exames oficiais
php spark db:seed ExameSeeder
```

### 7. Iniciar Servidor
```bash
php spark serve
```

Aceder: `http://localhost:8080`

---

## 📂 Estrutura do Projeto

```
ci4-adminlte/
├── app/
│   ├── Config/
│   │   └── Routes.php          # Configuração de rotas (60+ rotas)
│   ├── Controllers/
│   │   ├── ExameController.php
│   │   ├── SessaoExameController.php
│   │   ├── SessaoExameSalaController.php ⭐
│   │   ├── ConvocatoriaController.php
│   │   └── ... (20+ controllers)
│   ├── Models/
│   │   ├── ExameModel.php
│   │   ├── SessaoExameSalaModel.php ⭐
│   │   └── ... (25+ models)
│   ├── Views/
│   │   ├── exames/
│   │   ├── sessoes_exame/
│   │   │   ├── alocar_salas.php ⭐
│   │   │   └── calendario.php ⭐
│   │   └── layout/
│   └── Database/
│       ├── Migrations/          # 50+ migrations
│       └── Seeds/
├── public/
│   ├── adminlte/               # AdminLTE 3 assets
│   └── assets/                 # Custom CSS/JS
├── writable/
│   └── logs/                   # Application logs
└── vendor/                     # Composer dependencies
```

---

## 🎯 Funcionalidades Principais

### Sistema de Exames (v2.0)

#### 📅 Calendário Visual
- FullCalendar integration
- Cores por tipo: 🔴 Nacionais | 🔵 Finais | 🟢 MODa
- Clique para ver detalhes
- Navegação mensal/semanal

#### 🏢 Alocação de Salas
- Múltiplas salas por sessão
- Validação de capacidade
- Cálculo automático: 2 vigilantes/sala
- Provas MODa: 1 vigilante por 20 alunos
- Badges de estado em tempo real

#### 👥 Gestão de Convocatórias
- Deteção automática de conflitos
- Funções: Vigilante, Suplente, Júri, Coadjuvante
- Estados: Pendente, Confirmado, Rejeitado
- Área do professor para confirmação

### Gestão de Equipamentos

#### 💻 Inventário
- CRUD completo de equipamentos
- Estados: Disponível, Atribuído, Avariado, Reparação
- Histórico de movimentações

#### 📦 Kit Digital
- Formulário público de requisição
- Reporte de avarias (público)
- Gestão de inutilizados

#### 🔧 Reparações
- Controlo de reparações externas
- Estados do processo
- Histórico completo

### Gestão Letiva

#### 📚 Horários
- Sistema completo de horários
- Blocos horários configuráveis
- Importação CSV
- Visualização por turma/professor

#### 🔄 Permutas
- Sistema de troca de aulas
- Workflow de aprovação
- Contexto de ano letivo

---

## 🔧 Tecnologias Utilizadas

### Backend
- **CodeIgniter 4** - Framework PHP
- **MySQL** - Base de dados
- **Composer** - Gestão de dependências

### Frontend
- **AdminLTE 3** - Template administrativo
- **Bootstrap 5** - Framework CSS
- **jQuery** - JavaScript library
- **DataTables** - Tabelas interativas (server-side)
- **FullCalendar** - Calendário visual ⭐
- **SweetAlert2** - Notificações elegantes
- **Bootstrap Icons** - Iconografia

### Bibliotecas PHP
- **PHPMailer** - Envio de emails
- **Google OAuth** - Autenticação SSO
- **Endroid QR Code** - Geração de QR codes

---

## 📖 Documentação

### Guias de Instalação
- **[INSTALACAO_SISTEMA_EXAMES.md](INSTALACAO_SISTEMA_EXAMES.md)** - Sistema completo de exames (v2.0)
- **[INSTALACAO_RAPIDA_SALAS.md](INSTALACAO_RAPIDA_SALAS.md)** - Guia de alocação de salas

### Documentação Técnica
- **[IMPLEMENTACAO_CONVOCATORIAS_EXAMES.md](IMPLEMENTACAO_CONVOCATORIAS_EXAMES.md)** - Convocatórias base
- **[IMPLEMENTACAO_ALOCACAO_SALAS_EXAMES.md](IMPLEMENTACAO_ALOCACAO_SALAS_EXAMES.md)** - Alocação de salas
- **[IMPLEMENTACAO_EQUIPAMENTOS.md](IMPLEMENTACAO_EQUIPAMENTOS.md)** - Sistema de equipamentos
- **[IMPLEMENTACAO_GESTAO_LETIVA.md](IMPLEMENTACAO_GESTAO_LETIVA.md)** - Gestão letiva
- **[IMPLEMENTACAO_PERMUTAS.md](IMPLEMENTACAO_PERMUTAS.md)** - Sistema de permutas
- **[GOOGLE_OAUTH_SETUP.md](GOOGLE_OAUTH_SETUP.md)** - Configuração OAuth

### Scripts SQL
- **[MIGRATION_ALOCACAO_SALAS.sql](MIGRATION_ALOCACAO_SALAS.sql)** - Migração de salas
- **[QUERIES_UTEIS_CONVOCATORIAS.sql](QUERIES_UTEIS_CONVOCATORIAS.sql)** - Queries úteis

---

## 👥 Níveis de Acesso

| Nível | Função | Acesso |
|-------|--------|--------|
| 0 | Visitante | Formulários públicos |
| 1-3 | Professor | Área pessoal, confirmações |
| 4 | Coordenador | Gestão de turma/disciplina |
| 5-7 | Direção | Gestão completa escolar |
| 8-9 | Técnico TIC | Equipamentos, reparações |
| 10 | Administrador | Acesso total ao sistema |

---

## 🎨 Menu Principal

```
📊 Dashboard
├── 🏠 Início
└── 📈 Estatísticas

👥 Utilizadores
├── 👤 Gestão de Utilizadores
└── 🔑 Permissões

📚 Gestão Letiva
├── 📖 Disciplinas
├── 👨‍🏫 Professores
├── 🎓 Turmas
├── 🏫 Salas
└── ⏰ Horários

📋 Sec. Exames ⭐ NOVO
├── 📄 Exames/Provas
├── 📅 Sessões de Exame
├── 📆 Calendário de Exames
└── ✅ Convocatórias/Vigilâncias

💻 Kit Digital
├── 🖥️ Equipamentos
├── 📦 Requisições
├── ⚠️ Avarias
├── 🗑️ Inutilizados
└── 🔧 Reparações Externas

🔄 Permutas
├── 📝 Gestão de Permutas
└── 📅 Anos Letivos
```

---

## 🔥 Novidades da Versão 2.0

### ✨ Sistema de Alocação de Salas
- Alocar múltiplas salas a uma sessão de exame
- Definir número de alunos por sala
- Cálculo automático de vigilantes:
  - **Regra geral:** 2 vigilantes por sala (fixo)
  - **Provas MODa:** 1 vigilante por 20 alunos
- Validação de capacidade da sala
- Estatísticas em tempo real
- Badges de estado: 🟢 Completo | 🟡 Parcial | 🔴 Sem Vigilantes

### 📆 Calendário Visual de Exames
- Integração com FullCalendar
- Código de cores por tipo de prova
- Navegação mensal/semanal/diária
- Clique no evento para ver detalhes
- Informações no hover (fase, hora, duração, alunos)
- Responsivo para mobile/tablet/desktop

### 🎯 Melhorias no Sistema de Convocatórias
- Associação de vigilantes a salas específicas
- Funções globais (Suplente, Júri) sem sala
- Deteção avançada de conflitos
- Gestão granular por sala

---

## 🐛 Resolução de Problemas

### Erro: Database connection failed
```bash
# Verificar credenciais em .env
# Criar base de dados manualmente
mysql -u root -p
CREATE DATABASE sistema_gestao CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Erro: Migration failed
```bash
# Limpar cache
php spark cache:clear

# Verificar status
php spark migrate:status

# Rollback e re-executar
php spark migrate:rollback
php spark migrate
```

### Erro: Assets não carregam
```bash
# Verificar permissões
chmod -R 755 public/
chmod -R 777 writable/
```

---

## 📊 Estatísticas do Projeto

- **Controllers:** 25+
- **Models:** 30+
- **Views:** 80+
- **Migrations:** 50+
- **Rotas:** 200+
- **Linhas de Código:** 50.000+

---

## 🤝 Contribuir

1. Fork o projeto
2. Criar branch de feature (`git checkout -b feature/NovaFuncionalidade`)
3. Commit as mudanças (`git commit -m 'Adiciona nova funcionalidade'`)
4. Push para o branch (`git push origin feature/NovaFuncionalidade`)
5. Abrir Pull Request

---

## 📝 Changelog

### v2.0 - 31 Janeiro 2026
- ✨ Adicionado sistema de alocação de salas
- ✨ Implementado calendário visual de exames
- ⚡ Melhorado cálculo de vigilantes (por sala)
- 🐛 Corrigidos conflitos de horário
- 📚 Documentação atualizada

### v1.0 - 30 Janeiro 2026
- 🎉 Sistema base de convocatórias
- 📝 Gestão de exames e sessões
- 👥 Área do professor
- 📊 30 códigos oficiais de provas

---

## 📞 Suporte

**Desenvolvido para:** Agrupamento de Escolas João de Barros  
**Email:** suporte@aejb.pt  
**Repositório:** [github.com/jaceman78/ci4-adminlte](https://github.com/jaceman78/ci4-adminlte)

---

## 📄 Licença

Este projeto é propriedade do **Agrupamento de Escolas João de Barros**.

---

## 🙏 Agradecimentos

- **CodeIgniter Team** - Framework PHP excelente
- **AdminLTE** - Template responsivo e moderno
- **FullCalendar** - Biblioteca de calendário
- **Comunidade PHP** - Suporte e bibliotecas

---

**Última Atualização:** 2 de Fevereiro de 2026  
**Versão:** 2.0  
**Status:** ✅ Em Produção

---

<div align="center">

**🏫 Sistema de Gestão Escolar AEJB 🏫**

Desenvolvido com ❤️ para a educação

</div>
