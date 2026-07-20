<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

$routes->group('operator', ['namespace' => 'App\Controllers\operateur'], static function ($routes) {
	$routes->get('dashboard', 'OperateurController::dashboard');
	$routes->post('dashboard/bareme', 'OperateurController::updateBareme');

	$routes->group('prefixes', static function ($routes) {
		$routes->get('/', 'OperateurController::index');
		$routes->post('store', 'OperateurController::store');
		$routes->get('edit/(:num)', 'OperateurController::edit/$1');
		$routes->post('update/(:num)', 'OperateurController::update/$1');
		$routes->get('delete/(:num)', 'OperateurController::delete/$1');
	});
});
