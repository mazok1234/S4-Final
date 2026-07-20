<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->group('operator', function($routes) {
    $routes->get('dashboard', 'operateur\OperateurController::dashboard');
    $routes->post('bareme/update', 'operateur\OperateurController::updateBareme');
    
    $routes->get('prefixes', 'operateur\OperateurController::index');
    $routes->post('prefixes/store', 'operateur\OperateurController::store');
    $routes->get('prefixes/edit/(:num)', 'operateur\OperateurController::edit/$1');
    $routes->post('prefixes/update/(:num)', 'operateur\OperateurController::update/$1');
    $routes->get('prefixes/delete/(:num)', 'operateur\OperateurController::delete/$1');
});
