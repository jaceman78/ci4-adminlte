# Sistema de Logs de Atividade - Instruções de Implementação

## 📋 Visão Geral

Este sistema de logs de atividade foi desenvolvido para CodeIgniter 4 com AdminLTE4 + Bootstrap5, proporcionando um registo completo e detalhado de todas as atividades dos utilizadores no sistema.

## 🗂️ Ficheiros Fornecidos

1. **Migration_LogsAtividade.php** - Migration melhorada para a tabela `logs_atividade`
2. **ActivityLogModel.php** - Modelo para gerir os logs de atividade
3. **LogHelper_Melhorado.php** - Helper com funções especializadas de logging
4. **ActivityLogController.php** - Controlador para visualização e gestão dos logs
5. **UserController_ComLogs.php** - Exemplo de integração no UserController
6. **activity_log_index.php** - Interface de visualização dos logs
7. **INSTRUCOES_LOGS_ATIVIDADE.md** - Este ficheiro de instruções

## 🚀 Passos de Implementação

### 1. Migration da Base de Dados

**Substitua a sua migration atual** pela migration melhorada fornecida:

```bash
# Copie o ficheiro Migration_LogsAtividade.php para:
app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateLogsAtividadeTable.php
```

**Execute a migration:**

```bash
php spark migrate
```

**Estrutura da tabela `logs_atividade`:**
- `id` - Chave primária
- `user_id` - ID do utilizador (FK para tabela `user`)
- `modulo` - Módulo da aplicação (users, escolas, salas, auth, system, logs)
- `acao` - Ação realizada (create, update, delete, view, login, logout, etc.)
- `descricao` - Descrição detalhada da ação
- `registro_id` - ID do registo afetado (opcional)
- `dados_anteriores` - Dados antes da alteração (JSON)
- `dados_novos` - Dados após a alteração (JSON)
- `ip_address` - Endereço IP do utilizador
- `user_agent` - Informações do navegador
- `detalhes` - Detalhes adicionais (JSON)
- `criado_em` - Data/hora da ação

### 2. Modelos

**Copie os ficheiros para as respetivas pastas:**

```bash
# ActivityLogModel.php
cp ActivityLogModel.php app/Models/

# Substitua o LogHelper existente
cp LogHelper_Melhorado.php app/Helpers/LogHelper.php
```

### 3. Controladores

**ActivityLogController:**

```bash
# Copie o controlador
cp ActivityLogController.php app/Controllers/
```

**Integração nos controladores existentes:**

Use o `UserController_ComLogs.php` como exemplo para integrar logs nos seus controladores existentes (`UserController`, `EscolaController`, `SalaController`).

**Principais alterações a fazer:**

1. **Adicionar no construtor:**
```php
helper("LogHelper"); // Carrega o helper de logs
```

2. **Adicionar logs em cada método:**
```php
// Exemplo para método create
log_user_activity('create', "Criou novo utilizador: {$userData['name']}", $userId, null, $userDataSanitized);

// Exemplo para método update
log_user_activity('update', "Atualizou utilizador: {$userData['name']}", $id, $existingUserSanitized, $userDataSanitized);

// Exemplo para método delete
log_user_activity('delete', "Eliminou utilizador: {$user['name']}", $id, $userSanitized);
```

### 4. Views

**Copie a view para a pasta correta:**

```bash
# Crie a pasta se não existir
mkdir -p app/Views/logs

# Copie a view
cp activity_log_index.php app/Views/logs/
```

### 5. Rotas

**Adicione as seguintes rotas ao ficheiro `app/Config/Routes.php`:**

```php
// Rotas para logs de atividade
$routes->group('logs', function($routes) {
    $routes->get('/', 'ActivityLogController::index');
    $routes->get('dashboard', 'ActivityLogController::dashboard');
    $routes->post('getDataTable', 'ActivityLogController::getDataTable');
    $routes->get('getDataTable', 'ActivityLogController::getDataTable'); // Compatibilidade
    $routes->get('getLog/(:num)', 'ActivityLogController::getLog/$1');
    $routes->post('delete/(:num)', 'ActivityLogController::delete/$1');
    $routes->get('getStats', 'ActivityLogController::getStats');
    $routes->get('getFilterData', 'ActivityLogController::getFilterData');
    $routes->get('exportCSV', 'ActivityLogController::exportCSV');
    $routes->post('cleanOldLogs', 'ActivityLogController::cleanOldLogs');
    $routes->get('getRecentLogs', 'ActivityLogController::getRecentLogs');
    $routes->get('search', 'ActivityLogController::search');
    $routes->get('getLogsByRecord', 'ActivityLogController::getLogsByRecord');
});
```

### 6. Dependências JavaScript

**Certifique-se de que as seguintes bibliotecas estão incluídas no seu `layout/partials/footer.php`:**

```html
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
```

**E os respetivos CSS no `layout/partials/head.php`:**

```html
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
```

### 7. Ficheiro de Tradução DataTables

**Descarregue e coloque o ficheiro de tradução:**

1. Aceda a: `https://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json`
2. Guarde o conteúdo como `pt-PT.json`
3. Crie a pasta: `public/assets/datatables/i18n/`
4. Coloque o ficheiro: `public/assets/datatables/i18n/pt-PT.json`

### 8. Funções Helper Auxiliares

**Adicione as seguintes funções ao seu helper ou crie um helper adicional:**

```php
// app/Helpers/SessionHelper.php (criar se não existir)
<?php

if (!function_exists('get_current_user_id')) {
    function get_current_user_id(): ?int
    {
        return session()->get('user_id') ?? session()->get('id') ?? null;
    }
}

if (!function_exists('get_current_user_level')) {
    function get_current_user_level(): int
    {
        return session()->get('level') ?? 0;
    }
}
```

**Carregue este helper no `app/Config/Autoload.php`:**

```php
public $helpers = ['LogHelper', 'SessionHelper'];
```

## 🔧 Configuração e Personalização

### Níveis de Acesso

O sistema considera os seguintes níveis de utilizador:
- **Nível 0-8:** Utilizadores normais (podem ver logs)
- **Nível 9+:** Administradores (podem eliminar logs e limpar logs antigos)

### Módulos Suportados

- `users` - Gestão de utilizadores
- `escolas` - Gestão de escolas
- `salas` - Gestão de salas
- `auth` - Autenticação (login/logout)
- `system` - Operações do sistema
- `logs` - Gestão de logs

### Ações Suportadas

- `create` - Criação de registos
- `update` - Atualização de registos
- `delete` - Eliminação de registos
- `view` - Visualização de registos
- `login` - Início de sessão
- `logout` - Fim de sessão
- `export` - Exportação de dados
- `search` - Pesquisas
- `upload` - Upload de ficheiros

## 📊 Funcionalidades da Interface

### Página Principal (`/logs`)

- **DataTable responsiva** com paginação, ordenação e pesquisa
- **Filtros avançados** por utilizador, módulo, ação e período
- **Visualização detalhada** de cada log com dados antes/depois
- **Exportação para CSV** com filtros aplicados
- **Estatísticas** em tempo real
- **Limpeza de logs antigos** (apenas administradores)

### Filtros Disponíveis

- **Utilizador:** Dropdown com todos os utilizadores que têm logs
- **Módulo:** Dropdown com todos os módulos registados
- **Ação:** Dropdown com todas as ações registadas
- **Período:** Campos de data início e fim

### Estatísticas

- Total de logs no sistema
- Logs por período (hoje, semana, mês)
- Distribuição por módulo
- Distribuição por ação
- Utilizadores mais ativos

## 🔒 Segurança e Privacidade

### Sanitização de Dados

O sistema automaticamente sanitiza dados sensíveis:
- Passwords são removidas dos logs
- Tokens de autenticação são ocultados
- Dados pessoais sensíveis são mascarados

### Controlo de Acesso

- Apenas utilizadores autenticados podem aceder aos logs
- Eliminação de logs restrita a administradores (nível 9+)
- Limpeza de logs antigos restrita a administradores

### Retenção de Dados

- Configuração flexível para limpeza automática
- Opções de retenção: 30, 60, 90, 180 dias ou 1 ano
- Logs críticos podem ser excluídos da limpeza automática

## 🚨 Resolução de Problemas

### Erro: "Call to undefined function log_activity"

**Solução:** Certifique-se de que o `LogHelper` está carregado:
```php
// No construtor do controlador
helper("LogHelper");
```

### Erro: "Unknown column in field list"

**Solução:** Execute a migration da base de dados:
```bash
php spark migrate
```

### DataTable não carrega

**Solução:** Verifique se:
1. jQuery está carregado antes do DataTables
2. O ficheiro `pt-PT.json` está no local correto
3. As rotas estão configuradas corretamente

### Logs não aparecem

**Solução:** Verifique se:
1. A função `get_current_user_id()` retorna um ID válido
2. A sessão do utilizador está ativa
3. Os logs estão a ser criados na base de dados

## 📈 Monitorização e Manutenção

### Limpeza Automática

Configure uma tarefa cron para limpeza automática:
```bash
# Executar diariamente às 2:00
0 2 * * * /usr/bin/php /path/to/your/project/spark logs:clean --days=90
```

### Monitorização de Performance

- Monitore o tamanho da tabela `logs_atividade`
- Configure índices adicionais se necessário
- Considere particionamento para grandes volumes

### Backup

- Inclua a tabela `logs_atividade` nos backups regulares
- Considere backups separados para logs críticos
- Teste a restauração periodicamente

## 🎯 Próximos Passos

1. **Teste o sistema** com alguns logs de teste
2. **Configure os níveis de utilizador** conforme necessário
3. **Personalize as mensagens** de log conforme o seu contexto
4. **Configure a limpeza automática** de logs antigos
5. **Integre nos restantes controladores** (EscolaController, SalaController)
6. **Configure alertas** para atividades suspeitas (opcional)

## 📞 Suporte

Para questões ou problemas com a implementação:
1. Verifique os logs de erro do CodeIgniter
2. Confirme que todas as dependências estão instaladas
3. Teste cada componente individualmente
4. Consulte a documentação do CodeIgniter 4

---

**Nota:** Este sistema foi desenvolvido seguindo as melhores práticas de segurança e performance. Certifique-se de testar em ambiente de desenvolvimento antes de implementar em produção.

