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
	$routes->get('admin', 'Admin\Admin::index');
	
	/*Clientes*/
	$routes->get('clientes', 'admin\Clientes::index');
	$routes->post('nuevo_cliente', 'admin\Clientes::nuevo');
	$routes->get('editar_cliente/(:num)', 'admin\Clientes::editar/$1');
	$routes->post('actualizar_cliente', 'admin\Clientes::actualizar');
	$routes->get('eliminar_cliente/(:num)', 'admin\Clientes::eliminar/$1');
	
	/*Proveedores*/
	$routes->get('proveedores', 'admin\Proveedores::index');
	$routes->post('nuevo_proveedor', 'admin\Proveedores::nuevo');
	$routes->get('editar_proveedor/(:num)', 'admin\Proveedores::editar/$1');
	$routes->post('actualizar_proveedor', 'admin\Proveedores::actualizar');
	$routes->get('eliminar_proveedor/(:num)', 'admin\Proveedores::eliminar/$1');
	$routes->get('mostrar_familias/(:num)', 'admin\Proveedores::mostrar_familias/$1');
	$routes->post('agregar_familia', 'admin\Proveedores::agregar_familia');

	/*Articulos*/
	$routes->get('articulos', 'admin\Articulos::index');
	$routes->get('mostrar_articulos', 'admin\Articulos::mostrar');
	$routes->get('mostrar_articulos_compras/(:num)', 'admin\Articulos::mostrar_compras/$1');
	$routes->post('nuevo_articulo', 'admin\Articulos::nuevo');
	$routes->get('editar_articulo/(:num)', 'admin\Articulos::editar/$1');
	$routes->post('actualizar_articulo', 'admin\Articulos::actualizar');
	$routes->get('eliminar_articulo/(:num)', 'admin\Articulos::eliminar/$1');

	/*Cotizaciones*/
	$routes->get('cotizaciones', 'admin\Cotizaciones::index');
	$routes->get('nueva_cotizacion/(:num)', 'admin\Cotizaciones::nueva/$1');
	$routes->get('pagina_cotizador/(:any)', 'admin\Cotizaciones::pagina/$1');
	$routes->get('editar_cotizacion/(:num)', 'admin\Cotizaciones::editar/$1');
	$routes->get('actualizar_cotizacion/(:num)', 'admin\Cotizaciones::actualizar/$1');
	$routes->get('eliminar_cotizacion/(:num)', 'admin\Cotizaciones::eliminar/$1');
	$routes->post('agregar_articulo', 'admin\Cotizaciones::agregar');
	$routes->post('agregar_articulo_ind', 'admin\Cotizaciones::agregar_ind');
	$routes->get('mostrar_detalles/(:num)', 'admin\Cotizaciones::mostrar_detalles/$1');
	$routes->get('borrar_linea/(:num)', 'admin\Cotizaciones::borrar_linea/$1');
	$routes->get('descargar_cotizacion/(:num)', 'admin\Cotizaciones::cotizacion_pdf/$1');
	$routes->get('enviar', 'admin\Cotizaciones::enviar');
	$routes->get('enviar_pdf/(:num)', 'admin\Cotizaciones::enviar_pdf/$1');
	$routes->post('pago', 'admin\Cotizaciones::pago');
	$routes->post('modificar_cantidad', 'admin\Cotizaciones::modificar_cantidad');
	$routes->post('marcar_entregado', 'admin\Cotizaciones::entregado');

	/*Compras*/
	$routes->get('compras', 'admin\Compras::index');
	$routes->get('mostrar_compras', 'admin\Compras::mostrar');
	$routes->get('pedido/(:num)', 'admin\Compras::pedido/$1');
	$routes->get('nueva_compra/(:num)', 'admin\Compras::nueva/$1');
	$routes->get('pagina_orden/(:any)', 'admin\Compras::pagina/$1');
	$routes->get('editar_compra/(:num)', 'admin\Compras::editar/$1');
	$routes->get('actualizar_compra/(:num)', 'admin\Compras::actualizar/$1');
	$routes->get('eliminar_compra/(:num)', 'admin\Compras::eliminar/$1');
	$routes->post('agregar_articulo_compras', 'admin\Compras::agregar');
	$routes->get('mostrar_detalles_compras/(:num)', 'admin\Compras::mostrar_detalles/$1');
	$routes->get('borrar_linea_compras/(:num)', 'admin\Compras::borrar_linea/$1');
	$routes->get('descargar_orden/(:num)', 'admin\Compras::cotizacion_pdf/$1');
	$routes->get('enviar_orden', 'admin\Compras::enviar');
	$routes->get('enviar_pdf_orden/(:num)', 'admin\Compras::enviar_pdf/$1');
	$routes->post('pago_compras', 'admin\Compras::pago');
	$routes->post('compra_recibida', 'admin\Compras::recibida');
	$routes->post('modificar_cantidad_compras', 'admin\Compras::modificar_cantidad');

	/*Cuentas*/
	$routes->get('contabilidad', 'admin\Contabiidad::index');
	$routes->get('nueva_cuenta/(:num)', 'admin\Contabiidad::nuevo/$1');
	$routes->get('editar_cuenta/(:num)', 'admin\Contabiidad::editar/$1');
	$routes->post('actualizar_cuenta', 'admin\Contabiidad::actualizar');
	$routes->get('eliminar_cuenta/(:num)', 'admin\Contabiidad::eliminar/$1');

	
	/*Pedidos*/
	$routes->get('pedidos', 'admin\Pedidos::index');
	$routes->get('nuevo_pedido/(:num)', 'admin\Pedidos::nueva/$1');
	$routes->get('pagina_pedido/(:any)', 'admin\Pedidos::pagina/$1');
	$routes->get('editar_pedido/(:num)', 'admin\Pedidos::editar/$1');
	$routes->get('actualizar_pedido/(:num)', 'admin\Pedidos::actualizar/$1');
	$routes->get('eliminar_pedido/(:num)', 'admin\Pedidos::eliminar/$1');
	$routes->post('agregar_articulo_pedido', 'admin\Pedidos::agregar');
	$routes->get('mostrar_detalles_pedido/(:num)', 'admin\Pedidos::mostrar_detalles/$1');
	$routes->get('borrar_linea_pedido/(:num)', 'admin\Pedidos::borrar_linea/$1');
	$routes->get('descargar_pedido/(:num)', 'admin\Pedidos::cotizacion_pdf/$1');
	$routes->get('enviar_pedido', 'admin\Pedidos::enviar');
	$routes->get('enviar_pdf_pedido/(:num)', 'admin\Pedidos::enviar_pdf/$1');
	$routes->post('pago_pedido', 'admin\Pedidos::pago');

	/*Existencias*/
	$routes->get('existencias', 'admin\Existencias::index');
	$routes->get('nueva_existencia/(:num)', 'admin\Existencias::nuevo/$1');
	$routes->get('editar_existencia/(:num)', 'admin\Existencias::editar/$1');
	$routes->post('actualizar_existencia', 'admin\Existencias::actualizar');
	$routes->get('eliminar_existencia/(:num)', 'admin\Existencias::eliminar/$1');

	$routes->get('nueva_venta', 'admin\Ventas::nueva');
	$routes->get('cotizaciones', 'admin\Cotizaciones::index');
	$routes->get('editar_cotizacion', 'admin\Cotizaciones::editar');
	$routes->get('nueva_cotizacion', 'admin\Cotizaciones::nueva');
});


