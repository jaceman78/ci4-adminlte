<?php

/**
 * EXEMPLO DE ROTAS PARA O SISTEMA DE CONVOCATÓRIAS
 * 
 * Adicionar estas rotas ao ficheiro app/Config/Routes.php
 */

// ============================================
// ÁREA DO PROFESSOR - Minhas Convocatórias
// ============================================

// Listar minhas convocatórias
$routes->get('minhas-convocatorias', 'MinhasConvocatoriasController::index', ['filter' => 'auth']);

// Ver detalhes de uma convocatória
$routes->get('minhas-convocatorias/detalhes/(:num)', 'MinhasConvocatoriasController::detalhes/$1', ['filter' => 'auth']);

// Confirmar convocatória (AJAX)
$routes->post('minhas-convocatorias/confirmar', 'MinhasConvocatoriasController::confirmar', ['filter' => 'auth']);

// Rejeitar convocatória (AJAX)
$routes->post('minhas-convocatorias/rejeitar', 'MinhasConvocatoriasController::rejeitar', ['filter' => 'auth']);

// Calendário de convocatórias
$routes->get('minhas-convocatorias/calendario', 'MinhasConvocatoriasController::calendario', ['filter' => 'auth']);

// API JSON - Listar convocatórias
$routes->get('api/minhas-convocatorias', 'MinhasConvocatoriasController::getConvocatoriasJson', ['filter' => 'auth']);

// API JSON - Contar pendentes (para badges)
$routes->get('api/convocatorias/pendentes/count', 'MinhasConvocatoriasController::countPendentes', ['filter' => 'auth']);


// ============================================
// ÁREA DE GESTÃO - Criar e Gerir Convocatórias
// ============================================

// Dashboard de exames
$routes->get('exames', 'ExameController::index', ['filter' => 'auth']);
$routes->get('exames/listar', 'ExameController::listar', ['filter' => 'auth']);
$routes->get('exames/criar', 'ExameController::criar', ['filter' => 'auth']);
$routes->post('exames/guardar', 'ExameController::guardar', ['filter' => 'auth']);
$routes->get('exames/editar/(:num)', 'ExameController::editar/$1', ['filter' => 'auth']);
$routes->post('exames/atualizar/(:num)', 'ExameController::atualizar/$1', ['filter' => 'auth']);
$routes->post('exames/eliminar/(:num)', 'ExameController::eliminar/$1', ['filter' => 'auth']);

// Sessões de Exame
$routes->get('sessoes-exame', 'SessaoExameController::index', ['filter' => 'auth']);
$routes->get('sessoes-exame/listar', 'SessaoExameController::listar', ['filter' => 'auth']);
$routes->get('sessoes-exame/criar', 'SessaoExameController::criar', ['filter' => 'auth']);
$routes->post('sessoes-exame/guardar', 'SessaoExameController::guardar', ['filter' => 'auth']);
$routes->get('sessoes-exame/editar/(:num)', 'SessaoExameController::editar/$1', ['filter' => 'auth']);
$routes->post('sessoes-exame/atualizar/(:num)', 'SessaoExameController::atualizar/$1', ['filter' => 'auth']);
$routes->post('sessoes-exame/eliminar/(:num)', 'SessaoExameController::eliminar/$1', ['filter' => 'auth']);
$routes->get('sessoes-exame/detalhes/(:num)', 'SessaoExameController::detalhes/$1', ['filter' => 'auth']);

// Calendário de todas as sessões
$routes->get('sessoes-exame/calendario', 'SessaoExameController::calendario', ['filter' => 'auth']);

// Convocatórias (Gestão)
$routes->get('convocatorias', 'ConvocatoriaController::index', ['filter' => 'auth']);
$routes->get('convocatorias/sessao/(:num)', 'ConvocatoriaController::porSessao/$1', ['filter' => 'auth']);
$routes->get('convocatorias/criar/(:num)', 'ConvocatoriaController::criar/$1', ['filter' => 'auth']); // $1 = sessao_id
$routes->post('convocatorias/guardar', 'ConvocatoriaController::guardar', ['filter' => 'auth']);
$routes->post('convocatorias/eliminar/(:num)', 'ConvocatoriaController::eliminar/$1', ['filter' => 'auth']);

// Criar múltiplas convocatórias de uma vez
$routes->post('convocatorias/criar-multiplas', 'ConvocatoriaController::criarMultiplas', ['filter' => 'auth']);

// Estatísticas
$routes->get('convocatorias/estatisticas', 'ConvocatoriaController::estatisticas', ['filter' => 'auth']);
$routes->get('convocatorias/relatorio', 'ConvocatoriaController::relatorio', ['filter' => 'auth']);

// Exportar mapa de vigilância (PDF)
$routes->get('convocatorias/exportar/(:num)', 'ConvocatoriaController::exportarPDF/$1', ['filter' => 'auth']);

// API - Buscar professores disponíveis
$routes->get('api/professores/disponiveis/(:any)', 'ConvocatoriaController::getProfessoresDisponiveis/$1', ['filter' => 'auth']); // $1 = data/hora


// ============================================
// ROTAS API PARA AJAX
// ============================================

// Buscar exames por tipo
$routes->get('api/exames/tipo/(:any)', 'ExameController::getByTipo/$1', ['filter' => 'auth']);

// Buscar exames por ano
$routes->get('api/exames/ano/(:num)', 'ExameController::getByAno/$1', ['filter' => 'auth']);

// Buscar sessões por período
$routes->get('api/sessoes-exame/periodo/(:any)/(:any)', 'SessaoExameController::getByPeriodo/$1/$2', ['filter' => 'auth']);

// Verificar conflitos de horário
$routes->post('api/sessoes-exame/verificar-conflito', 'SessaoExameController::verificarConflito', ['filter' => 'auth']);

// Calcular vigilantes necessários
$routes->get('api/sessoes-exame/vigilantes-necessarios/(:num)', 'SessaoExameController::getVigilantesNecessarios/$1', ['filter' => 'auth']);


// ============================================
// EXEMPLO DE MENU PARA AdminLTE
// ============================================

/**
 * Adicionar no ficheiro de menu (sidebar):
 * 
 * [
 *     'title' => 'Exames',
 *     'icon' => 'fas fa-clipboard-list',
 *     'children' => [
 *         [
 *             'title' => 'Catálogo de Exames',
 *             'url' => 'exames/listar',
 *             'icon' => 'fas fa-book'
 *         ],
 *         [
 *             'title' => 'Sessões de Exame',
 *             'url' => 'sessoes-exame',
 *             'icon' => 'fas fa-calendar-alt'
 *         ],
 *         [
 *             'title' => 'Convocatórias',
 *             'url' => 'convocatorias',
 *             'icon' => 'fas fa-user-clock'
 *         ],
 *         [
 *             'title' => 'Minhas Convocatórias',
 *             'url' => 'minhas-convocatorias',
 *             'icon' => 'fas fa-user-check',
 *             'badge' => 'pendentes-count', // Badge dinâmico com JS
 *             'badge-class' => 'badge-warning'
 *         ],
 *         [
 *             'title' => 'Estatísticas',
 *             'url' => 'convocatorias/estatisticas',
 *             'icon' => 'fas fa-chart-bar'
 *         ]
 *     ]
 * ]
 */


// ============================================
// GRUPOS DE ROTAS POR PERMISSÃO
// ============================================

/**
 * Exemplo de rotas com diferentes níveis de acesso:
 */

// Grupo para Coordenadores (level >= 5)
$routes->group('admin/convocatorias', ['filter' => 'auth:5'], function($routes) {
    $routes->get('dashboard', 'Admin\ConvocatoriasController::dashboard');
    $routes->get('aprovar-rejeicoes', 'Admin\ConvocatoriasController::aprovarRejeicoes');
    $routes->post('notificar-professores', 'Admin\ConvocatoriasController::notificarProfessores');
});

// Grupo para todos os professores autenticados
$routes->group('professor', ['filter' => 'auth'], function($routes) {
    $routes->get('convocatorias', 'MinhasConvocatoriasController::index');
    $routes->get('calendario', 'MinhasConvocatoriasController::calendario');
});

?>
