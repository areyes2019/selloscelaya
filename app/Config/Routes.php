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
$routes->group('', ['namespace' => 'App\Controllers\Auth'], function($routes) {
    $routes->get('register', 'Register::index');
    $routes->post('register', 'Register::index');
    
    $routes->get('login', 'Login::index');
    $routes->post('login', 'Login::index');
    $routes->get('logout', 'Login::logout');
    
    $routes->get('forgot-password', 'ForgotPassword::index');
    $routes->post('forgot-password', 'ForgotPassword::index');
    $routes->get('reset-password', 'ForgotPassword::resetPassword');
    $routes->post('reset-password', 'ForgotPassword::resetPassword');
});

$routes->group('',static function($routes){
	
	$routes->get('admin', 'Admin\Admin::index');
	$routes->get('migrar956_56', 'Admin\EjecutarMigraciones::index');
	
	/*Clientes*/
	$routes->get('clientes', 'Admin\Clientes::index');
	$routes->post('nuevo_cliente', 'Admin\Clientes::nuevo');
	$routes->get('editar_cliente/(:num)', 'Admin\Clientes::editar/$1');
	$routes->post('actualizar_cliente', 'Admin\Clientes::actualizar');
	$routes->get('eliminar_cliente/(:num)', 'Admin\Clientes::eliminar/$1');
	
	/*Proveedores*/
	$routes->get('proveedores', 'Admin\Proveedores::index');
	$routes->post('nuevo_proveedor', 'Admin\Proveedores::nuevo');
	$routes->get('editar_proveedor/(:num)', 'Admin\Proveedores::editar/$1');
	$routes->post('actualizar_proveedor', 'Admin\Proveedores::actualizar');
	$routes->get('eliminar_proveedor/(:num)', 'Admin\Proveedores::eliminar/$1');
	$routes->get('mostrar_familias/(:num)', 'Admin\Proveedores::mostrar_familias/$1');
	$routes->post('agregar_familia', 'Admin\Proveedores::agregar_familia');

	/*Articulos*/
	$routes->get('articulos', 'Admin\Articulos::index');
	$routes->get('mostrar_articulos', 'Admin\Articulos::mostrar');
	$routes->get('mostrar_articulos_compras/(:num)', 'Admin\Articulos::mostrar_compras/$1');
	$routes->get('editar_rapido/(:num)', 'Admin\Articulos::editar_rapido/$1');
	$routes->post('nuevo_articulo', 'Admin\Articulos::nuevo');
	$routes->get('editar_articulo/(:num)', 'Admin\Articulos::editar/$1');
	$routes->post('actualizar_articulo', 'Admin\Articulos::actualizar');
	$routes->get('eliminar_articulo/(:num)', 'Admin\Articulos::eliminar/$1');
	$routes->get('ver_imagen/(:any)', 'Admin\Articulos::verImagen/$1');
	$routes->get('nuevo_art_vista', 'Admin\Articulos::nuevo_art');
	$routes->post('import_masivo', 'Admin\Articulos::importArticulos');
	/*Cotizaciones*/
	$routes->get('cotizaciones', 'Admin\Cotizaciones::index');
	$routes->get('nueva_cotizacion/(:num)', 'Admin\Cotizaciones::nueva/$1');
	$routes->get('pagina_cotizador/(:any)', 'Admin\Cotizaciones::pagina/$1');
	$routes->get('editar_cotizacion/(:num)', 'Admin\Cotizaciones::editar/$1');
	$routes->get('actualizar_cotizacion/(:num)', 'Admin\Cotizaciones::actualizar/$1');
	$routes->get('eliminar_cotizacion/(:num)', 'Admin\Cotizaciones::eliminar/$1');
	$routes->post('agregar_articulo', 'Admin\Cotizaciones::agregar');
	$routes->post('agregar_articulo_ind', 'Admin\Cotizaciones::agregar_ind');
	$routes->get('mostrar_detalles/(:num)', 'Admin\Cotizaciones::mostrar_detalles/$1');
	$routes->get('borrar_linea/(:num)', 'Admin\Cotizaciones::borrar_linea/$1');
	$routes->get('descargar_cotizacion/(:num)', 'Admin\Cotizaciones::cotizacion_pdf/$1');
	$routes->get('enviar', 'Admin\Cotizaciones::enviar');
	$routes->get('enviar_pdf/(:num)', 'Admin\Cotizaciones::enviar_pdf/$1');
	$routes->post('pago', 'Admin\Cotizaciones::pago');
	$routes->post('modificar_cantidad', 'Admin\Cotizaciones::modificar_cantidad');
	$routes->post('marcar_entregado', 'Admin\Cotizaciones::entregado');
	$routes->get('totales/(:num)', 'Admin\Cotizaciones::totales/$1');
	$routes->post('descuento', 'Admin\Cotizaciones::descuento');
	/*Facturas*/
	$routes->post('facturar_cotizacion', 'Admin\FacturaController::convertir');

	/*Ordenes de Compras*/
	$routes->get('compras', 'Admin\Compras::index');
	$routes->get('mostrar_compras', 'Admin\Compras::mostrar');
	$routes->get('pedido/(:num)', 'Admin\Compras::pedido/$1');
	$routes->get('nueva_compra/(:num)', 'Admin\Compras::nueva/$1');
	$routes->get('pagina_orden/(:any)', 'Admin\Compras::pagina/$1');
	$routes->get('editar_compra/(:num)', 'Admin\Compras::editar/$1');
	$routes->get('actualizar_compra/(:num)', 'Admin\Compras::actualizar/$1');
	$routes->get('eliminar_compra/(:num)', 'Admin\Compras::eliminar/$1');
	$routes->post('agregar_articulo_compras', 'Admin\Compras::agregar');
	$routes->get('mostrar_detalles_compras/(:num)', 'Admin\Compras::mostrar_detalles/$1');
	$routes->get('borrar_linea_compras/(:num)', 'Admin\Compras::borrar_linea/$1');
	$routes->get('descargar_orden/(:num)', 'Admin\Compras::cotizacion_pdf/$1');
	$routes->get('enviar_orden', 'Admin\Compras::enviar');
	$routes->get('enviar_pdf_orden/(:num)', 'Admin\Compras::enviar_pdf/$1');
	$routes->post('pago_compras', 'Admin\Compras::pago');
	$routes->post('recibido_compras', 'Admin\Compras::recibida');
	$routes->post('modificar_cantidad_compras', 'Admin\Compras::modificar_cantidad');

	/*Cuentas*/
	$routes->get('contabilidad', 'Admin\Contabiidad::index');
	$routes->get('nueva_cuenta/(:num)', 'Admin\Contabiidad::nuevo/$1');
	$routes->get('editar_cuenta/(:num)', 'Admin\Contabiidad::editar/$1');
	$routes->post('actualizar_cuenta', 'Admin\Contabiidad::actualizar');
	$routes->get('eliminar_cuenta/(:num)', 'Admin\Contabiidad::eliminar/$1');

	
	/*Pedidos*/
	$routes->get('pedidos', 'Admin\Pedidos::index');
	$routes->get('nuevo_pedido/(:num)', 'Admin\Pedidos::nueva/$1');
	$routes->get('pagina_pedido/(:any)', 'Admin\Pedidos::pagina/$1');
	$routes->get('editar_pedido/(:num)', 'Admin\Pedidos::editar/$1');
	$routes->get('actualizar_pedido/(:num)', 'Admin\Pedidos::actualizar/$1');
	$routes->get('eliminar_pedido/(:num)', 'Admin\Pedidos::eliminar/$1');
	$routes->post('agregar_articulo_pedido', 'Admin\Pedidos::agregar');
	$routes->get('mostrar_detalles_pedido/(:num)', 'Admin\Pedidos::mostrar_detalles/$1');
	$routes->get('borrar_linea_pedido/(:num)', 'Admin\Pedidos::borrar_linea/$1');
	$routes->get('descargar_pedido/(:num)', 'Admin\Pedidos::cotizacion_pdf/$1');
	$routes->get('enviar_pedido', 'Admin\Pedidos::enviar');
	$routes->get('enviar_pdf_pedido/(:num)', 'Admin\Pedidos::enviar_pdf/$1');
	$routes->post('pago_pedido', 'Admin\Pedidos::pago');

	$routes->get('nueva_venta', 'admin\Ventas::nueva');
	$routes->get('cotizaciones', 'admin\Cotizaciones::index');
	$routes->get('editar_cotizacion', 'admin\Cotizaciones::editar');
	$routes->get('nueva_cotizacion', 'admin\Cotizaciones::nueva');


	/*Pos experimental*/
	// Rutas para el módulo de Pedidos POS
	$routes->group('ventas', static function ($routes) {
	    $routes->get('pos', 'Admin\PuntoVentaController::index');          // Listar pedidos (historial)
	    $routes->get('new', 'Admin\PuntoVentaController::new');         // Mostrar formulario POS
	    $routes->post('create', 'Admin\PuntoVentaController::create');    // Procesar nuevo pedido
	    $routes->get('mostrar_articulos', 'Admin\PuntoVentaController::articulos');    // Procesar nuevo pedido
	    $routes->get('show/(:num)', 'Admin\PuntoVentaController::show/$1'); // Ver detalle pedido
	    $routes->get('ticket/(:num)', 'Admin\PuntoVentaController::ticket/$1'); // Ver vista de ticket post-creación
	    $routes->get('download/(:num)', 'Admin\PuntoVentaController::downloadTicket/$1'); // Descargar ticket
	    $routes->get('edit/(:num)', 'Admin\PuntoVentaController::edit/$1');   // (Opcional) Mostrar form edición
	    $routes->post('update/(:num)', 'Admin\PuntoVentaController::update/$1'); // (Opcional) Procesar edición
	    $routes->get('delete/(:num)', 'Admin\PuntoVentaController::delete/$1'); // Eliminar pedido (GET para simplicidad, POST/DELETE es mejor)
	    $routes->post('pagar/(:num)', 'Admin\PuntoVentaController::pagar/$1');
	    // Si usas resource, ajusta o añade las personalizadas
	    // $routes->resource('pedidos', ['controller' => 'PedidosController']);
	    // $routes->get('pedidos/download/(:num)', 'PedidosController::downloadTicket/$1');
	});

	/*Pos experimental*/
	// Rutas para el módulo de Pedidos POS
	$routes->group('reportes', static function ($routes) {
	    $routes->get('reporte', 'Admin\BalanceController::index'); //reporte de utilidede
	    $routes->get('este_mes', 'Admin\BalanceController::mes_actual'); //reporte de utilidede
	    $routes->get('hoy', 'Admin\BalanceController::hoy'); //reporte de utilidede
	});

	// Rutas para el módulo de Pedidos POS
	$routes->group('pedidos', static function ($routes) {
	    $routes->get('pos', 'Admin\PedidosController::index');          // Listar pedidos (historial)
	    $routes->get('new', 'Admin\PedidosController::new');         // Mostrar formulario POS
	    $routes->post('create', 'Admin\PedidosController::create');    // Procesar nuevo pedido
	    $routes->get('mostrar_articulos', 'Admin\PedidosController::mostrar_articulos');    // Procesar nuevo pedido
	    $routes->get('show/(:num)', 'Admin\PedidosController::show/$1'); // Ver detalle pedido
	    $routes->get('ticket/(:num)', 'Admin\PedidosController::ticket/$1'); // Ver vista de ticket post-creación
	    $routes->get('download/(:num)', 'Admin\PedidosController::downloadTicket/$1'); // Descargar ticket
	    $routes->get('edit/(:num)', 'Admin\PedidosController::edit/$1');   // (Opcional) Mostrar form edición
	    $routes->post('update/(:num)', 'Admin\PedidosController::update/$1'); // (Opcional) Procesar edición
	    $routes->get('delete/(:num)', 'Admin\PedidosController::delete/$1'); // Eliminar pedido (GET para simplicidad, POST/DELETE es mejor)
	    $routes->post('pagar/(:num)', 'Admin\PedidosController::pagar/$1');
	    // Si usas resource, ajusta o añade las personalizadas
	    // $routes->resource('pedidos', ['controller' => 'PedidosController']);
	    // $routes->get('pedidos/download/(:num)', 'PedidosController::downloadTicket/$1');
	});

	// Dentro de tu grupo 'admin' o similar si lo tienes, o directamente:
	$routes->group('ordenes', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
	    $routes->get('/', 'OrdenTrabajoController::index', ['as' => 'ordenes_dashboard']); // Dashboard
	    $routes->get('new/(:num)', 'OrdenTrabajoController::new/$1'); // Formulario nuevo con ID de pedido
	    $routes->post('create', 'OrdenTrabajoController::create'); // Procesar creación
	    $routes->get('edit/(:num)', 'OrdenTrabajoController::edit/$1'); // (Futuro) Formulario editar
	    $routes->post('update/(:num)', 'OrdenTrabajoController::update/$1'); // (Futuro) Procesar update
	    $routes->post('cambiar_status/(:num)', 'OrdenTrabajoController::cambiarStatus/$1'); // Ruta para cambiar status
	    $routes->get('delete/(:num)', 'OrdenTrabajoController::delete/$1'); // (Futuro) Eliminar
	    $routes->get('imagen/(:segment)', 'OrdenTrabajoController::serveImage/$1', ['as' => 'orden_imagen']); // Ruta para servir imágenes
	});
	//gastos
	$routes->group('gastos',static function($routes) {
	    $routes->get('inicio', 'Admin\GastosController::index');
	    $routes->get('nuevo', 'Admin\GastosController::nuevo');
	    $routes->post('guardar', 'Admin\GastosController::guardar');
	    $routes->get('mostrar/(:num)', 'Admin\GastosController::mostrar/$1');
	    $routes->get('editar/(:num)', 'Admin\GastosController::editar/$1');
	    $routes->get('reporte', 'Admin\GastosController::reporteFinanciero');
	    $routes->post('actualizar/(:num)', 'Admin\GastosController::actualizar/$1');
	    $routes->post('eliminar/(:num)', 'Admin\GastosController::eliminar/$1');
	});

	//existenias
	$routes->group('existencias',static function($routes) {
		$routes->get('existencias_admin', 'Admin\Existencias::index');
	    // Rutas para el CRUD de Existencias/Inventario
	    $routes->get('existencias', 'Admin\Existencias::index');
	    $routes->get('nuevo', 'Admin\Existencias::nuevo');         // Muestra form para crear
	    $routes->post('crear', 'Admin\Existencias::crear');        // Procesa creación (POST)
	    $routes->get('editar/(:num)', 'Admin\Existencias::editar/$1'); // Muestra form para editar
	    $routes->post('actualizar/(:num)', 'Admin\Existencias::actualizar/$1'); // Procesa actualización (POST)
	    $routes->post('eliminar/(:num)', 'Admin\Existencias::eliminar/$1');  // Procesa eliminación (POST)
	});
	





});


