<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('autoentitables', 'Home::autoentintables');
$routes->get('madera', 'Home::madera');
$routes->get('fechadores', 'Home::fechadores');
$routes->get('portatiles', 'Home::portatiles');
$routes->get('gigantes', 'Home::gigantes');
$routes->get('textiles', 'Home::textiles');
$routes->get('catalogo', 'Home::catalogo');

//logeo
$routes->get('login', 'Login::login');

$routes->group('admin',['filter'=>'AuthFilter'],function($routes){
	$routes->get('admin', 'Login::index');
});


