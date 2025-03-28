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
$routes->get('doctores', 'Home::catalogo');
$routes->get('maestros', 'Home::catalogo');
$routes->post('contacto', 'Home::contacto');

//logeo
$routes->get('login', 'Admin::login');

$routes->group('',static function($routes){
	$routes->get('admin', 'admin\Admin::index');
	
	/*Clientes*/
	$routes->get('clientes', 'admin\Clientes::index');
	$routes->post('nuevo_cliente', 'admin\Clientes::nuevo');
	$routes->get('editar_cliente/(:num)', 'admin\Clientes::editar/$1');
	$routes->post('actualizar_cliente', 'admin\Clientes::actualizar');
	$routes->get('eliminar_cliente/(:num)', 'admin\Clientes::eliminar/$1');
	
	/*Articulos*/
	$routes->get('articulos', 'admin\Articulos::index');
	$routes->post('nuevo_articulo', 'admin\Articulos::nuevo');
	$routes->get('editar_articulo/(:num)', 'admin\Articulos::editar/$1');
	$routes->post('actualizar_articulo', 'admin\Articulos::actualizar');
	$routes->get('eliminar_articulo/(:num)', 'admin\Articulos::eliminar/$1');

	/*Cotizaciones*/
	$routes->get('cotizaciones', 'admin\Cotizaciones::index');
	$routes->get('nueva_cotizacion/(:num)', 'admin\Cotizaciones::nuevo/$1');
	$routes->get('editar_cotizacion/(:num)', 'admin\Cotizaciones::editar/$1');
	$routes->post('actualizar_cotizacion', 'admin\Cotizaciones::actualizar');
	$routes->get('eliminar_cotizacion/(:num)', 'admin\Cotizaciones::eliminar/$1');

	/*Cuentas*/
	$routes->get('contabilidad', 'admin\Contabiidad::index');
	$routes->get('nueva_cuenta/(:num)', 'admin\Contabiidad::nuevo/$1');
	$routes->get('editar_cuenta/(:num)', 'admin\Contabiidad::editar/$1');
	$routes->post('actualizar_cuenta', 'admin\Contabiidad::actualizar');
	$routes->get('eliminar_cuenta/(:num)', 'admin\Contabiidad::eliminar/$1');



	$routes->get('nueva_venta', 'admin\Ventas::nueva');
	$routes->get('cotizaciones', 'admin\Cotizaciones::index');
	$routes->get('editar_cotizacion', 'admin\Cotizaciones::editar');
	$routes->get('nueva_cotizacion', 'admin\Cotizaciones::nueva');
});


