<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'ItemController::index');
$routes->get('item', 'ItemController::index');
$routes->post('item/ajax', 'ItemController::ajax');
$routes->post('item/store', 'ItemController::store');
$routes->get('item/get/(:num)', 'ItemController::get/$1');
$routes->delete('item/delete/(:num)', 'ItemController::delete/$1');

$routes->get('machine', 'MachineController::index');
$routes->post('machine/ajax', 'MachineController::ajax');

$routes->get('production', 'ItemMachineController::index');
$routes->post('production/ajax', 'ItemMachineController::ajax');
