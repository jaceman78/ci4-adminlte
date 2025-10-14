<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rota inicial
$routes->get('/', 'Home::index');

// Rota para teste de toasts (apenas desenvolvimento)
$routes->get('teste-toasts', function() {
    return view('teste_toasts');
});

// Rotas de debug (apenas desenvolvimento)
$routes->get('debug/session', 'DebugController::checkSession');
$routes->get('debug/fix-session', 'DebugController::fixSession');
$routes->get('debug/test-email', 'DebugController::testEmailPage');
$routes->post('debug/test-email', 'DebugController::testEmail');

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
    $routes->put("update/(:num)", "TicketsController::update/$1");
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

// Rotas para obter dados para selects em modais
$routes->get("equipamentos/all", "EquipamentosController::all");
$routes->get("salas/all", "SalaController::all");
$routes->get("tipos-avaria/all", "TiposAvariaController::all");
$routes->get("users/technicians", "UserController::getTechnicians");