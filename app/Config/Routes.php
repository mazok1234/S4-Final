<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\AuthController;

/**
 * @var RouteCollection $routes
 */

$routes->view('login', 'front_office/login');

$routes->post('/auth/login', [AuthController::class, 'login']);
$routes->get('/client/test', [AuthController::class, 'dashboard']);
