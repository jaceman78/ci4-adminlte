<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rota inicial
$routes->get('/', 'Home::index');

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
