<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rota inicial -> redireciona para login
$routes->get('/', 'LoginController::index');

// ---------------------------
// 🔒 Política de Privacidade e Termos (Público - para Google OAuth)
// ---------------------------
$routes->get('privacy', 'PrivacyController::index');
$routes->get('privacy/terms', 'PrivacyController::terms');

// ---------------------------
// 📁 Rota para servir ficheiros de upload (writable/uploads)
// ---------------------------
$routes->get('writable/uploads/profiles/(:any)', function($filename) {
    $filepath = WRITEPATH . 'uploads/profiles/' . $filename;
    if (file_exists($filepath)) {
        $mime = mime_content_type($filepath);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    }
    throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
});

// Rota para teste de toasts (apenas desenvolvimento)
$routes->get('teste-toasts', function() {
    return view('teste_toasts');
});

// Rotas de debug (apenas desenvolvimento)
$routes->get('debug/session', 'DebugController::checkSession');
$routes->get('debug/fix-session', 'DebugController::fixSession');
$routes->get('debug/test-email', 'DebugController::testEmailPage');
$routes->post('debug/test-email', 'DebugController::testEmail');
$routes->get('test-email-permuta', 'PermutasController::testeEmail'); // TESTE EMAIL PERMUTAS
$routes->get('test/horarios', 'TestHorariosController::index');
$routes->get('test/disciplinas', 'VerificarDisciplinasController::index');
$routes->get('test/criar-disciplinas', 'CriarDisciplinasFaltantesController::index');

// ---------------------------
// 🔐 Autenticação / Login
// ---------------------------

// Página de login
$routes->get('login', 'LoginController::index');

// Callback do Google OAuth
$routes->get('login/loginWithGoogle', 'LoginController::loginWithGoogle');

// Perfil do utilizador logado
$routes->get('layout/dashboard', 'LoginController::profile');

// Logout
$routes->get('logout', 'LoginController::logout');

// ---------------------------
// 📊 Dashboard Personalizado
// ---------------------------
$routes->group('dashboard', function($routes) {
    $routes->get('/', 'DashboardController::index'); // Dashboard principal (redireciona por nível)
    $routes->get('stats', 'DashboardController::getStats'); // API para estatísticas
    $routes->get('charts/(:any)', 'DashboardController::getChartData/$1'); // API para gráficos
});

// ---------------------------
// 👥 Gestão de Utilizadores (CRUD)
// ---------------------------
// Adicione no topo se ainda não tiver:
// use App\Controllers\UserController;


// Rotas para gestão de utilizadores
$routes->group('users', function($routes) {
    $routes->get('/', 'UserController::index');
    $routes->post('getDataTable', 'UserController::getDataTable');
    $routes->get('getDataTable', 'UserController::getDataTable'); // Adicione esta linha
    $routes->get('getUser/(:num)', 'UserController::getUser/$1');
    $routes->post('create', 'UserController::create');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->post('delete/(:num)', 'UserController::delete/$1');
    $routes->post('restore/(:num)', 'UserController::restore/$1');
    $routes->post('updateStatus', 'UserController::updateStatus');
    $routes->post('uploadProfileImage', 'UserController::uploadProfileImage');
    $routes->get('getStats', 'UserController::getStats');
    $routes->get('search', 'UserController::search');
    $routes->get('exportCSV', 'UserController::exportCSV');
    $routes->post('importar', 'UserController::importar');
});

// Rotas para gestão de escolas
$routes->group("escolas", function($routes) {
    $routes->get("/", "EscolaController::index");
    $routes->post("getDataTable", "EscolaController::getDataTable");
    $routes->get("getDataTable", "EscolaController::getDataTable"); // Rota adicional para compatibilidade
    $routes->get("getEscola/(:num)", "EscolaController::getEscola/$1");
    $routes->post("create", "EscolaController::create");
    $routes->post("update/(:num)", "EscolaController::update/$1");
    $routes->post("delete/(:num)", "EscolaController::delete/$1");
    $routes->get("getStats", "EscolaController::getStats");
    $routes->get("search", "EscolaController::search");
    $routes->get("exportCSV", "EscolaController::exportCSV");
    $routes->get("getDropdownList", "EscolaController::getDropdownList");
    $routes->post("advancedSearch", "EscolaController::advancedSearch");
    $routes->post("deleteMultiple", "EscolaController::deleteMultiple");
    $routes->get("getRecent", "EscolaController::getRecent");
    $routes->post("checkNome", "EscolaController::checkNome");
});
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
    $routes->get('getByEscola/(:num)', 'SalaController::getByEscola/$1');
});

// Rotas para gestão de Logs de Atividade
$routes->group("logs", function($routes) {
    $routes->get("/", "ActivityLogController::index");
    $routes->post("getDataTable", "ActivityLogController::getDataTable");
    $routes->get("getDataTable", "ActivityLogController::getDataTable"); // Para compatibilidade
    $routes->get("getLog/(:num)", "ActivityLogController::getLog/$1");
    $routes->post("delete/(:num)", "ActivityLogController::delete/$1");
    $routes->get("getStats", "ActivityLogController::getStats");
    $routes->get("getFilterData", "ActivityLogController::getFilterData");
    $routes->get("exportCSV", "ActivityLogController::exportCSV");
    $routes->post("cleanOldLogs", "ActivityLogController::cleanOldLogs");
});

// Rotas para gestão de Materiais
$routes->group("materiais", function($routes) {
    $routes->get("/", "MateriaisController::index");
    $routes->post("getDataTable", "MateriaisController::getDataTable");
    $routes->get("getDataTable", "MateriaisController::getDataTable"); // Para compatibilidade
    $routes->get("getMaterial/(:num)", "MateriaisController::getMaterial/$1");
    $routes->post("save", "MateriaisController::saveMaterial");
    $routes->post("delete/(:num)", "MateriaisController::deleteMaterial/$1");
    $routes->get("getStats", "MateriaisController::getStats");
});

// Rotas para Gestão de Equipamentos
$routes->group("equipamentos", function ($routes) {
    $routes->get("/", "EquipamentosController::index");
    $routes->get("all", "EquipamentosController::all");
    $routes->post("getDataTable", "EquipamentosController::getDataTable");
    $routes->get("getEquipamento/(:num)", "EquipamentosController::getEquipamento/$1");
    $routes->get("getEquipamentoCompleto/(:num)", "EquipamentosController::getEquipamentoCompleto/$1");
    $routes->post("create", "EquipamentosController::create");
    $routes->post("createWithSala", "EquipamentosController::createWithSala");
    $routes->post("update/(:num)", "EquipamentosController::update/$1");
    $routes->post("delete/(:num)", "EquipamentosController::delete/$1");
    $routes->get("getStatistics", "EquipamentosController::getStatistics");
    $routes->get("getBySala/(:num)", "EquipamentosController::getBySala/$1");
    $routes->post("atribuirSala", "EquipamentosController::atribuirSala");
    $routes->post("editarSala", "EquipamentosController::editarSala");
    $routes->post("removerSala", "EquipamentosController::removerSala");
    $routes->get("getHistorico/(:num)", "EquipamentosController::getHistorico/$1");
});

// Rotas para Gestão de Tipos de Equipamento
$routes->group("tipos_equipamentos", function ($routes) {
    $routes->get("/", "TiposEquipamentosController::index");
    $routes->post("getDataTable", "TiposEquipamentosController::getDataTable");
    $routes->get("getTipoEquipamento/(:num)", "TiposEquipamentosController::getTipoEquipamento/$1");
    $routes->post("create", "TiposEquipamentosController::create");
    $routes->post("update/(:num)", "TiposEquipamentosController::update/$1");
    $routes->post("delete/(:num)", "TiposEquipamentosController::delete/$1");
    $routes->get("getStatistics", "TiposEquipamentosController::getStatistics");
});
/*
 * --------------------------------------------------------------------*
 * Rotas para Gestão de Tipos de Avaria
 * --------------------------------------------------------------------*
 */
$routes->group("tipos_avaria", function ($routes) {
    $routes->get("/", "TiposAvariaController::index");
    $routes->post("getDataTable", "TiposAvariaController::getDataTable");
    $routes->get("getTipoAvaria/(:num)", "TiposAvariaController::getTipoAvaria/$1");
    $routes->post("create", "TiposAvariaController::create");
    $routes->post("update/(:num)", "TiposAvariaController::update/$1");
    $routes->post("delete/(:num)", "TiposAvariaController::delete/$1");
    $routes->get("getStatistics", "TiposAvariaController::getStatistics");
});

/*
 * --------------------------------------------------------------------*
 * Rotas para Gestão de Tickets
 * --------------------------------------------------------------------*
 */
$routes->group("tickets", function ($routes) {
    $routes->get("novo", "TicketsController::novoTicket");
    $routes->get("meus", "TicketsController::meusTickets");
    $routes->get("tratamento", "TicketsController::tratamentoTickets");
    $routes->get("todos", "TicketsController::todosTickets");
    $routes->get("view/(:num)", "TicketsController::viewTicket/$1"); // Ver detalhes do ticket

    // Rotas AJAX para CRUD e DataTables
    $routes->post("create", "TicketsController::create");
    $routes->match(['post', 'put'], "update/(:num)", "TicketsController::update/$1");
    $routes->delete("delete/(:num)", "TicketsController::delete/$1");
    $routes->get("get/(:num)", "TicketsController::get/$1"); // Para carregar detalhes do ticket para edição/visualização via AJAX

    $routes->get("meus-datatable", "TicketsController::getMyTicketsDataTable");
    $routes->get("tratamento-datatable", "TicketsController::getTicketsForTreatmentDataTable");
    $routes->get("todos-datatable", "TicketsController::getAllTicketsDataTable");

    // Rotas de ação
    $routes->post("assign", "TicketsController::assignTicket");
    $routes->post("assignTicket", "TicketsController::assignTicket"); // Alias para compatibilidade
    $routes->get("accept/(:num)", "TicketsController::acceptTicket/$1"); // Para aceitar ticket via link de email
    $routes->post("updatePrioridade", "TicketsController::updatePrioridade"); // Atualizar prioridade
    $routes->post("resolverTicket", "TicketsController::resolverTicket"); // Resolver ticket
    $routes->post("reabrirTicket", "TicketsController::reabrirTicket"); // Reabrir ticket (apenas admins)
    $routes->post("aceitarTicket", "TicketsController::aceitarTicket"); // Aceitar ticket atribuído
    $routes->post("rejeitarTicket", "TicketsController::rejeitarTicket"); // Rejeitar ticket atribuído

    // Rotas para estatísticas
    $routes->get("statistics", "TicketsController::getStatistics");
    $routes->get("advanced-statistics", "TicketsController::getAdvancedStatistics");
    $routes->get("export-excel", "TicketsController::exportToExcel");
});

// ---------------------------
// 📚 Gestão Letiva
// ---------------------------

// Rotas para Gestão de Turmas
$routes->group('turmas', function($routes) {
    $routes->get('/', 'TurmasController::index');
    $routes->post('getDataTable', 'TurmasController::getDataTable');
    $routes->get('getDataTable', 'TurmasController::getDataTable');
    $routes->get('get/(:num)', 'TurmasController::get/$1');
    $routes->post('create', 'TurmasController::create');
    $routes->post('update/(:num)', 'TurmasController::update/$1');
    $routes->post('delete/(:num)', 'TurmasController::delete/$1');
    $routes->post('importar', 'TurmasController::importar');
});

// Rotas para Gestão de Disciplinas
$routes->group('disciplinas', function($routes) {
    $routes->get('/', 'DisciplinasController::index');
    $routes->post('getDataTable', 'DisciplinasController::getDataTable');
    $routes->get('getDataTable', 'DisciplinasController::getDataTable');
    $routes->get('get/(:num)', 'DisciplinasController::get/$1');
    $routes->post('create', 'DisciplinasController::create');
    $routes->post('update/(:num)', 'DisciplinasController::update/$1');
    $routes->post('delete/(:num)', 'DisciplinasController::delete/$1');
    $routes->post('importar', 'DisciplinasController::importar');
});

// Rotas para Gestão de Horários
$routes->group('horarios', function($routes) {
    $routes->get('/', 'HorariosController::index');
    $routes->post('getDataTable', 'HorariosController::getDataTable');
    $routes->get('getDataTable', 'HorariosController::getDataTable');
    $routes->get('get/(:num)', 'HorariosController::get/$1');
    $routes->post('create', 'HorariosController::create');
    $routes->post('update/(:num)', 'HorariosController::update/$1');
    $routes->post('delete/(:num)', 'HorariosController::delete/$1');
    $routes->post('importarCSV', 'HorariosController::importarCSV');
});

// Rotas para Permutas
$routes->group('permutas', function($routes) {
    // TESTE DE EMAIL (TEMPORÁRIO)
    $routes->get('testeEmail', 'PermutasController::testeEmail');            // Teste de envio de email
    
    // Visualização de horário e permutas
    $routes->get('/', 'PermutasController::index');                          // Meu Horário
    $routes->get('minhas', 'PermutasController::minhasPermutas');             // As Minhas Permutas
    $routes->get('lista', 'PermutasController::listaPermutas');               // Lista completa (admin - nível 6+)
    $routes->get('aprovar', 'PermutasController::aprovarPermutas');           // Lista para aprovação (admin)
    $routes->get('ver/(:num)', 'PermutasController::verPermuta/$1');         // Ver detalhes de uma permuta
    
    // Criar e gerir permutas
    $routes->get('pedir/(:num)', 'PermutasController::pedirPermuta/$1');     // Formulário pedir permuta
    $routes->post('salvar', 'PermutasController::salvarPermuta');            // Salvar permuta
    $routes->post('cancelar/(:num)', 'PermutasController::cancelarPermuta/$1'); // Cancelar permuta (autor)
    $routes->post('getSalasLivres', 'PermutasController::getSalasLivres');   // AJAX: Buscar salas livres
    
    // Gestão administrativa (aprovação/rejeição)
    $routes->post('aprovar/(:num)', 'PermutasController::aprovarPermuta/$1');   // Aprovar permuta individual (admin)
    $routes->post('rejeitar', 'PermutasController::rejeitarPermuta');           // Rejeitar permuta individual (admin)
    $routes->post('aprovarGrupo', 'PermutasController::aprovarGrupo');          // Aprovar grupo de permutas (admin)
    $routes->post('rejeitarGrupo', 'PermutasController::rejeitarGrupo');        // Rejeitar grupo de permutas (admin)
});

// Rotas para Caixa de Sugestões
$routes->group('sugestoes', function($routes) {
    $routes->get('/', 'SugestoesController::index');                          // Lista de sugestões (admin)
    $routes->get('getDataTable', 'SugestoesController::getDataTable');        // DataTable AJAX (admin)
    $routes->post('getDataTable', 'SugestoesController::getDataTable');       // DataTable AJAX (admin)
    $routes->post('salvar', 'SugestoesController::salvar');                   // Salvar nova sugestão (user)
    $routes->post('responder/(:num)', 'SugestoesController::responder/$1');   // Responder sugestão (admin)
    $routes->post('alterarEstado/(:num)', 'SugestoesController::alterarEstado/$1'); // Alterar estado (admin)
    $routes->post('excluir/(:num)', 'SugestoesController::excluir/$1');       // Excluir sugestão (admin)
});

// Rotas para Gestão de Blocos Horários
$routes->group('blocos', function($routes) {
    $routes->get('/', 'BlocosController::index');
    $routes->post('getDataTable', 'BlocosController::getDataTable');
    $routes->get('getDataTable', 'BlocosController::getDataTable');
    $routes->get('get/(:num)', 'BlocosController::get/$1');
    $routes->post('create', 'BlocosController::create');
    $routes->post('update/(:num)', 'BlocosController::update/$1');
    $routes->post('delete/(:num)', 'BlocosController::delete/$1');
});

// Rotas para Gestão de Tipologias
$routes->group('tipologias', function($routes) {
    $routes->get('/', 'TipologiasController::index');
    $routes->post('getDataTable', 'TipologiasController::getDataTable');
    $routes->get('getDataTable', 'TipologiasController::getDataTable');
    $routes->get('get/(:num)', 'TipologiasController::get/$1');
    $routes->post('create', 'TipologiasController::create');
    $routes->post('update/(:num)', 'TipologiasController::update/$1');
    $routes->post('delete/(:num)', 'TipologiasController::delete/$1');
});

// Rotas para Gestão de Anos Letivos
$routes->group('anos-letivos', function($routes) {
    $routes->get('/', 'AnosLetivosController::index');
    $routes->post('getDataTable', 'AnosLetivosController::getDataTable');
    $routes->get('getDataTable', 'AnosLetivosController::getDataTable');
    $routes->get('get/(:num)', 'AnosLetivosController::get/$1');
    $routes->post('create', 'AnosLetivosController::create');
    $routes->post('update/(:num)', 'AnosLetivosController::update/$1');
    $routes->post('delete/(:num)', 'AnosLetivosController::delete/$1');
    $routes->post('ativar/(:num)', 'AnosLetivosController::ativar/$1');
});

// Rotas para obter dados para selects em modais
$routes->get("equipamentos/all", "EquipamentosController::all");
$routes->get("salas/all", "SalaController::all");
$routes->get("tipos-avaria/all", "TiposAvariaController::all");
$routes->get("users/technicians", "UserController::getTechnicians");