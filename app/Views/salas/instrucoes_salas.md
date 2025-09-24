# Sistema de Gestão de Salas - CodeIgniter 4

## Ficheiros Criados

1. **SalasModel.php** - Modelo com queries diversas para gestão de salas com relação às escolas
2. **SalaController.php** - Controlador com funcionalidades CRUD completas e filtragem por escola
3. **salas_index.php** - View com dropdown de escolas, DataTable dinâmica, modalboxes e notificações toast

## Instruções de Implementação

### 1. Colocação dos Ficheiros

#### SalasModel.php
- Localização: `app/Models/SalasModel.php`

#### SalaController.php
- Localização: `app/Controllers/SalaController.php`

#### salas_index.php
- Localização: `app/Views/salas/salas_index.php`

### 2. Configuração de Rotas

Adicione as seguintes rotas no ficheiro `app/Config/Routes.php`:

```php
// Rotas para gestão de salas
$routes->group('salas', function($routes) {
    $routes->get('/', 'SalaController::index');
    $routes->post('getDataTable', 'SalaController::getDataTable');
    $routes->get('getDataTable', 'SalaController::getDataTable'); // Rota adicional para compatibilidade
    $routes->get('getSala/(:num)', 'SalaController::getSala/$1');
    $routes->post('create', 'SalaController::create');
    $routes->post('update/(:num)', 'SalaController::update/$1');
    $routes->post('delete/(:num)', 'SalaController::delete/$1');
    $routes->get('getStats', 'SalaController::getStats');
    $routes->get('search', 'SalaController::search');
    $routes->get('exportCSV', 'SalaController::exportCSV');
    $routes->get('getEscolasDropdown', 'SalaController::getEscolasDropdown');
    $routes->get('getSalasDropdown', 'SalaController::getSalasDropdown');
    $routes->post('advancedSearch', 'SalaController::advancedSearch');
    $routes->post('deleteMultiple', 'SalaController::deleteMultiple');
    $routes->get('getRecent', 'SalaController::getRecent');
    $routes->post('checkCodigo', 'SalaController::checkCodigo');
    $routes->get('getEscolaInfo/(:num)', 'SalaController::getEscolaInfo/$1');
});
```

### 3. Migração da Base de Dados

Certifique-se de que executou as migrações para criar as tabelas `escolas` e `salas`:

```php
// Migration para escolas (já deve existir)
$this->forge->addField([
    'id'    => ['type' => 'INT', 'auto_increment' => true],
    'nome'  => ['type' => 'VARCHAR', 'constraint' => 150],
    'morada'=> ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
]);
$this->forge->addKey('id', true);
$this->forge->createTable('escolas');

// Migration para salas (fornecida pelo utilizador)
$this->forge->addField([
    'id'         => ['type' => 'INT', 'auto_increment' => true],
    'escola_id'  => ['type' => 'INT'],
    'codigo_sala'=> ['type' => 'VARCHAR', 'constraint' => 50],
]);
$this->forge->addKey('id', true);
$this->forge->addForeignKey('escola_id', 'escolas', 'id', 'CASCADE', 'CASCADE');
$this->forge->createTable('salas');
```

Execute as migrações:
```bash
php spark migrate
```

### 4. Dependências Necessárias

#### CSS (no head da página ou layout)
```html
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<!-- Font Awesome (se não estiver incluído) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
```

#### JavaScript (no footer da página ou layout)
```html
<!-- jQuery (se não estiver incluído) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap 5 JS (se não estiver incluído) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
```

#### Ficheiro de Tradução DataTables
Para resolver o problema CORS mencionado anteriormente:
1. Descarregue o ficheiro `pt-PT.json` de: `http://cdn.datatables.net/plug-ins/1.13.7/i18n/pt-PT.json`
2. Crie a pasta: `public/assets/datatables/i18n/`
3. Coloque o ficheiro `pt-PT.json` nessa pasta
4. O código já está configurado para usar: `<?= base_url("assets/datatables/i18n/pt-PT.json") ?>`

### 5. Funcionalidades Implementadas

#### SalasModel.php
- ✅ Validação automática de dados com relação às escolas
- ✅ Queries diversas (pesquisa, filtros, estatísticas por escola)
- ✅ Métodos para DataTable filtrada por escola
- ✅ Gestão de unicidade de códigos de sala por escola
- ✅ Pesquisa avançada com filtros múltiplos
- ✅ Operações em lote (múltiplas salas)
- ✅ Estatísticas detalhadas por escola
- ✅ Relação com tabela de escolas via foreign key

#### SalaController.php
- ✅ CRUD completo via AJAX
- ✅ Validação de dados e relações
- ✅ Exportação para CSV (filtrada por escola)
- ✅ Pesquisa de salas por escola
- ✅ Estatísticas por escola
- ✅ Verificação de unicidade de códigos por escola
- ✅ Operações em lote
- ✅ Integração com sistema de escolas

#### salas_index.php
- ✅ Dropdown para seleção de escola (obrigatório)
- ✅ DataTable responsiva em português (só aparece após seleção de escola)
- ✅ Modalboxes para criar/editar/visualizar salas
- ✅ Modal de informações da escola selecionada
- ✅ Modal de estatísticas das salas da escola
- ✅ Notificações toast
- ✅ Validação frontend
- ✅ Interface AdminLTE4 + Bootstrap5
- ✅ Filtragem automática por escola selecionada

### 6. Como Usar

1. **Aceda a `/salas` no seu browser**
2. **Selecione uma escola** no dropdown (obrigatório)
3. **A DataTable das salas aparecerá automaticamente** filtrada pela escola selecionada
4. Use o botão **"Nova Sala"** para criar salas na escola selecionada
5. Clique nos ícones de ação para **editar, visualizar ou eliminar** salas
6. Use o botão **"Informações da Escola"** para ver detalhes da escola selecionada
7. Use o botão **"Estatísticas"** para ver informações resumidas das salas da escola
8. Use a **pesquisa da DataTable** para filtrar resultados dentro da escola
9. **Exporte dados para CSV** das salas da escola selecionada

### 7. Funcionalidades Especiais

#### Filtragem por Escola
- **Obrigatória:** A DataTable só aparece após selecionar uma escola
- **Dinâmica:** Mudança de escola recarrega automaticamente a DataTable
- **Contextual:** Todas as operações são filtradas pela escola selecionada

#### Validação de Unicidade
- O código da sala deve ser único **dentro da mesma escola**
- Salas de escolas diferentes podem ter o mesmo código
- Durante a edição, o próprio registo é ignorado na verificação de unicidade

#### Estatísticas por Escola
- Total de salas da escola selecionada
- Informações detalhadas da escola (nome, morada, total de salas)

#### Pesquisa e Filtros
- Pesquisa por código da sala ou nome da escola
- Pesquisa avançada com múltiplos critérios
- Filtros por data de criação
- Todos os filtros respeitam a escola selecionada

#### Operações em Lote
- Eliminação múltipla de salas
- Atualização múltipla de salas
- Sempre respeitando a escola selecionada

### 8. Relação com Sistema de Escolas

#### Dependências
- **Requer o sistema de escolas** implementado anteriormente
- Usa `EscolasModel` para carregar dropdown e validar relações
- Foreign key garante integridade referencial

#### Integração
- Dropdown de escolas carregado dinamicamente
- Validação automática de existência da escola
- Informações da escola exibidas nos modais
- Exportação inclui nome da escola

### 9. Personalização

#### Campos Adicionais
Para adicionar novos campos às salas:
1. Adicione à migration
2. Inclua em `$allowedFields` no modelo
3. Adicione validação se necessário
4. Atualize a view e controlador

#### Validação Personalizada
Ajuste as regras de validação no `SalasModel.php`:
```php
protected $validationRules = [
    'escola_id' => 'required|integer|is_not_unique[escolas.id]',
    'codigo_sala' => 'required|max_length[50]'
];
```

### 10. Segurança

- ✅ Validação CSRF (CodeIgniter 4 padrão)
- ✅ Validação de dados no servidor
- ✅ Sanitização de inputs
- ✅ Verificação de permissões AJAX
- ✅ Validação de relações (foreign keys)
- ✅ Validação de unicidade por contexto (escola)

### 11. Diferenças dos Sistemas Anteriores

#### Inovações
- **Filtragem obrigatória por escola** (UX melhorada)
- **DataTable condicional** (só aparece após seleção)
- **Validação contextual** (unicidade por escola)
- **Integração com sistema existente** (escolas)
- **Modal de informações da escola**

#### Melhorias
- Interface mais intuitiva com seleção prévia
- Validação mais robusta com relações
- Estatísticas contextualizadas
- Operações sempre filtradas por contexto

### 12. Troubleshooting

#### DataTable não aparece
- Verifique se selecionou uma escola no dropdown
- Confirme que o endpoint `/salas/getEscolasDropdown` responde
- Verifique se existem escolas na base de dados

#### Erro 404 nas rotas AJAX
- Verifique se as rotas estão corretamente configuradas
- Confirme que o `baseURL` está correto
- Certifique-se de que o `SalaController` existe

#### Validação de unicidade falha
- Certifique-se de que `$skipValidation = true` no modelo
- Confirme que o método `validateSalaData` está a ser usado
- Verifique se a escola_id está a ser passada corretamente

#### Dropdown de escolas vazio
- Verifique se existem escolas na base de dados
- Confirme que o `EscolasModel` está acessível
- Verifique se o endpoint `/salas/getEscolasDropdown` funciona

#### Foreign key constraint fails
- Certifique-se de que a escola existe antes de criar salas
- Verifique se a migração das escolas foi executada primeiro
- Confirme que a foreign key foi criada corretamente

### 13. Extensões Futuras

#### Possíveis Melhorias
- Sistema de reservas de salas
- Capacidade/lotação das salas
- Equipamentos disponíveis por sala
- Horários de funcionamento
- Integração com sistema de utilizadores (professores/alunos)
- Geolocalização das salas dentro da escola
- Upload de plantas/fotos das salas
- Sistema de manutenção/estado das salas

### 14. Fluxo de Utilização

1. **Página inicial:** Utilizador vê dropdown de escolas
2. **Seleção de escola:** Utilizador escolhe uma escola
3. **Carregamento:** DataTable aparece com salas da escola
4. **Operações:** Utilizador pode criar/editar/eliminar salas
5. **Contexto:** Todas as operações são filtradas pela escola
6. **Mudança:** Utilizador pode trocar de escola a qualquer momento

## Suporte

Este sistema foi desenvolvido para CodeIgniter 4 com AdminLTE4 e Bootstrap5, integrando-se perfeitamente com o sistema de escolas. A funcionalidade de filtragem obrigatória por escola garante uma experiência de utilizador mais intuitiva e organizada, evitando confusão entre salas de diferentes escolas.