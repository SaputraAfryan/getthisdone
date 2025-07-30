<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ItemController::index');
$routes->group('item', function ($routes) {
    $routes->get('/', 'ItemController::index');
    $routes->post('ajax', 'ItemController::ajax');
    $routes->post('store', 'ItemController::store');
    $routes->get('get/(:num)', 'ItemController::get/$1');
    $routes->delete('delete/(:num)', 'ItemController::delete/$1');
});

$routes->get('machine', 'MachineController::index');
$routes->post('machine/ajax', 'MachineController::ajax');

$routes->get('production', 'ItemMachineController::index');
$routes->post('production/ajax', 'ItemMachineController::ajax');
