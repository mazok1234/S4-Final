<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\AuthController;

/**
 * @var RouteCollection $routes
 */

$routes->view('login', 'Home::index');

$routes->group('operator', ['namespace' => 'App\Controllers\operateur'], static function ($routes) {
    
    $routes->get('dashboard', 'OperateurController::dashboard');
  
    $routes->group('prefixes', static function ($routes) {
        $routes->get('/', 'OperateurController::index');
        $routes->post('store', 'OperateurController::store');
        $routes->get('edit/(:num)', 'OperateurController::edit/$1');
        $routes->post('update/(:num)', 'OperateurController::update/$1');
        $routes->get('delete/(:num)', 'OperateurController::delete/$1');
    });

   
    $routes->get('gestion_baremes', 'BaremeController::index');
    $routes->post('gestion_baremes/store', 'BaremeController::store');
    $routes->get('gestion_baremes/edit/(:num)', 'BaremeController::edit/$1');
    $routes->post('gestion_baremes/update/(:num)', 'BaremeController::update/$1');
    $routes->get('gestion_baremes/delete/(:num)', 'BaremeController::delete/$1');

    $routes->get('types_operation', 'TypeOperationController::index');
    $routes->post('types_operation/store', 'TypeOperationController::store');
    $routes->get('types_operation/edit/(:num)', 'TypeOperationController::edit/$1');
    $routes->post('types_operation/update/(:num)', 'TypeOperationController::update/$1');
    $routes->get('types_operation/delete/(:num)', 'TypeOperationController::delete/$1');
});
$routes->post('/auth/login', [AuthController::class, 'login']);
$routes->get('/client/test', [AuthController::class, 'dashboard']);
$routes->view('login', 'front_office/login');

$routes->post('/auth/login', [AuthController::class, 'login']);
$routes->get('/client/test', [AuthController::class, 'dashboard']);