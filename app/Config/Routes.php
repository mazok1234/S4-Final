<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

$routes->group('operator', ['namespace' => 'App\Controllers\operateur'], static function ($routes) {
    
  
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
});