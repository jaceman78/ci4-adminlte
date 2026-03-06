# 🔐 Permissões de Acesso por Página

## Níveis de Utilizador

| Nível | Designação | Descrição |
|-------|------------|-----------|
| 0 | Assistente Operacional | Acesso muito limitado |
| 1 | Professor | Acesso básico como docente |
| 2 | Utilizador | Utilizador padrão com acesso limitado |
| 3 | Serviços Administrativos | Pessoal administrativo |
| 4 | Secretariado de Exames | Gestão de exames e convocatórias |
| 5 | Técnico | Acesso a tickets e gestão básica |
| 6 | Direção | Gestão de utilizadores e dados escolares |
| 7 | Técnico Sénior | Acesso avançado (reparações, equipamentos) |
| 8 | Administrador | Controlo total de tickets e sistema |
| 9 | Super Administrador | Acesso total ao sistema |

---

## 🏠 Dashboard & Home

| Rota | Nível Mínimo | Descrição |
|------|--------------|-----------|
| `/` (Home) | 0 (Público) | Página inicial |
| `/dashboard` | 5+ | Dashboard principal |
| `/dashboard-admin` | 9 | Dashboard Super Admin |
| `/dashboard-tecnico` | 8 | Dashboard Técnico Admin |
| `/dashboard-tecnico-jr` | 5 | Dashboard Técnico Júnior |

---

## 🎫 Sistema de Tickets

| Rota | Nível Mínimo | Funcionalidade |
|------|--------------|----------------|
| `/tickets` | 5+ | Listar tickets |
| `/tickets/create` | 5+ | Criar ticket |
| `/tickets/edit/:id` | 5+ | Editar ticket |
| `/tickets/view/:id` | 5+ | Ver detalhes |
| `/tickets/update/:id` | 5+ | Atualizar ticket |
| `/tickets/updateState/:id` | 5+ | Atualizar estado |
| `/tickets/delete/:id` | 8+ | Eliminar ticket (Admin) |
| `/tickets/dados-estatisticos` | 8+ | Estatísticas completas |
| `/tickets/reassign/:id` | 8+ | Reatribuir ticket |
| `/tickets-admin` | 8+ | Gestão administrativa |

**Notas:**
- Técnicos (5-7) só veem/editam tickets atribuídos a eles
- Admins (8+) veem todos os tickets
- Super Admins (9) podem eliminar tickets em qualquer estado
- Admins (8) só eliminam tickets não reparados

---

## 💻 Kit Digital

| Rota | Nível Mínimo | Funcionalidade |
|------|--------------|----------------|
| `/kit-digital` | 0 (Público) | Portal público de pedidos |
| `/kit-digital-admin` | 5+ | Gestão administrativa |
| `/kit-digital-admin/estatisticas` | 5+ | Estatísticas |
| `/kit-digital-admin/exportar` | 8+ | Exportação de dados |

---

## ⚠️ Avarias Kit Digital

| Rota | Nível Mínimo | Funcionalidade |
|------|--------------|----------------|
| `/avarias-kit` | 0 (Público) | Reportar avaria (portal público) |
| `/avarias-kit-admin` | 5+ | Gestão de avarias reportadas |
| `/avarias-kit-admin/getData` | 5+ | Dados para listagem |
| `/avarias-kit-admin/update/:id` | 5+ | Atualizar avaria |
| `/avarias-kit-admin/delete/:id` | 5+ | Eliminar registo |
| `/avarias-kit-admin/exportar` | 5+ | Exportar dados |

---

## 🖥️ Equipamentos Inutilizados

| Rota | Nível Mínimo | Funcionalidade |
|------|--------------|----------------|
| `/inutilizados-kitdigital` | 7+ | Listagem de equipamentos |
| `/inutilizados-kitdigital/getData` | 7+ | Dados para datatable |
| `/inutilizados-kitdigital/getStats` | 7+ | Estatísticas |
| `/inutilizados-kitdigital/create` | 7+ | Criar equipamento |
| `/inutilizados-kitdigital/update/:id` | 7+ | Atualizar equipamento |
| `/inutilizados-kitdigital/getDetails/:id` | 7+ | Detalhes do equipamento |
| `/inutilizados-kitdigital/delete/:id` | 7+ | Eliminar equipamento |
| `/inutilizados-kitdigital/view/:id` | 7+ | Ver página de detalhes |
| `/inutilizados-kitdigital/getQRCode/:id` | 7+ | Gerar QR Code dinâmico |
| `/inutilizados-kitdigital/buscarPorComponente` | 7+ | Busca por componente |

**Funcionalidades:**
- Gestão de equipamentos para canibalização
- Controlo de componentes disponíveis
- Geração de QR Codes com logo
- Sistema de estados (ativo/esgotado/descartado)

---

## 🔧 Reparações Externas

| Rota | Nível Mínimo | Funcionalidade |
|------|--------------|----------------|
| `/reparacoes-externas` | 7+ | Gestão de reparações externas |
| `/reparacoes-externas/*` | 7+ | Todas as funcionalidades |

---

## 👥 Gestão de Utilizadores

| Rota | Nível Mínimo | Funcionalidade |
|------|--------------|----------------|
| `/users` | 6+ | Listar utilizadores |
| `/users/create` | 6+ | Criar utilizador |
| `/users/edit/:id` | 6+ | Editar utilizador |
| `/users/delete/:id` | 6+ | Eliminar utilizador |
| `/users/profile` | 5+ | Ver/editar perfil próprio |

---

## 🏫 Gestão Escolar (Horários)

| Rota | Nível Mínimo | Funcionalidade |
|------|--------------|----------------|
| `/escolas` | 6+ | Gestão de escolas |
| `/disciplinas` | 6+ | Gestão de disciplinas |
| `/blocos` | 6+ | Gestão de blocos horários |
| `/anos-letivos` | 6+ | Gestão de anos letivos |
| `/horarios` | 6+ | Gestão de horários |
| `/equipamentos` | 6+ | Gestão de equipamentos |

---

## 🔑 Empresas e Chaves (Portal)

| Rota | Nível Mínimo | Funcionalidade |
|------|--------------|----------------|
| `/empresas-chaves` | 9 | Gestão de empresas com chaves API |
| `/empresa-portal` | 0 (via API) | Portal público com chave |

**Nota:** Acesso ao portal de empresa requer chave API válida

---

## 📊 Logs de Atividade

| Rota | Nível Mínimo | Funcionalidade |
|------|--------------|----------------|
| `/activity-logs` | 6+ | Visualizar logs |
| `/activity-logs/delete/:id` | 9 | Eliminar log (apenas Super Admin) |

---

## 🔒 Resumo de Permissões

### Nível 0 - Assistente Operacional
- ✅ Acesso ao portal público (Kit Digital, Avarias)
- ❌ Dashboard
- ❌ Tickets
- ❌ Gestão administrativa

### Nível 1 - Professor
- ✅ Acesso ao portal público
- ✅ Visualização básica (se implementado)
- ❌ Dashboard técnico
- ❌ Gestão de tickets

### Nível 2 - Utilizador
- ✅ Portal público
- ✅ Acesso básico ao sistema
- ❌ Dashboard técnico
- ❌ Criação de tickets

### Nível 3 - Serviços Administrativos
- ✅ Tudo do nível 2
- ✅ Acesso a funcionalidades administrativas básicas
- ❌ Dashboard técnico
- ❌ Gestão de tickets

### Nível 4 - Secretariado de Exames
- ✅ Tudo do nível 3
- ✅ Gestão de exames e convocatórias
- ✅ Alocação de salas
- ❌ Dashboard técnico
- ❌ Gestão de tickets

### Nível 5 - Técnico
- ✅ Dashboard básico
- ✅ Tickets (criar, visualizar atribuídos)
- ✅ Kit Digital Admin (visualizar)
- ✅ Avarias Kit (gerir)
- ❌ Gestão escolar
- ❌ Eliminar tickets
- ❌ Equipamentos inutilizados
- ❌ Reparações externas

### Nível 6 - Direção
- ✅ Tudo do nível 5
- ✅ Gestão de utilizadores
- ✅ **Gestão escolar completa** (escolas, disciplinas, blocos, horários, equipamentos)
- ✅ Anos letivos
- ✅ Logs de atividade (visualizar)
- ❌ Eliminar logs
- ❌ Equipamentos inutilizados
- ❌ Reparações externas

### Nível 7 - Técnico Sénior
- ✅ Tudo do nível 6
- ✅ **Equipamentos Inutilizados** (gestão completa)
- ✅ **Reparações Externas** (gestão completa)
- ✅ QR Codes com logo
- ❌ Eliminar logs
- ❌ Exportação Kit Digital
- ❌ Gestão administrativa tickets

### Nível 8 - Administrador
- ✅ Tudo do nível 7
- ✅ Dashboard Técnico Admin
- ✅ Tickets Admin (todos)
- ✅ Eliminar tickets (não reparados)
- ✅ Reatribuir tickets
- ✅ Estatísticas completas
- ✅ Exportação Kit Digital
- ❌ Eliminar logs
- ❌ Gestão de empresas/chaves

### Nível 9 - Super Administrador
- ✅ **ACESSO TOTAL**
- ✅ Dashboard Super Admin
- ✅ Eliminar qualquer ticket
- ✅ Eliminar logs
- ✅ Gestão de empresas e chaves API
- ✅ Todas as funcionalidades do sistema

---

## 🔐 Autenticação

### Login Normal
- `/login` - Login com username/password

### Google OAuth
- `/auth/google` - Iniciar login com Google
- `/auth/google/callback` - Callback OAuth
- Após login bem-sucedido, redireciona para dashboard apropriado

---

## 📝 Notas Importantes

1. **Verificação de Sessão**: Todas as rotas protegidas verificam se o utilizador está autenticado
2. **Soft Delete**: Equipamentos inutilizados usam soft delete (`deleted_at`)
3. **Logs**: Todas as ações importantes são registadas em `activity_logs`
4. **AJAX**: Muitas rotas requerem requisições AJAX e retornam JSON
5. **Redirecionamento**: Utilizadores sem permissão são redirecionados para a home com mensagem de erro

---

*Última atualização: 4 de Março de 2026*
