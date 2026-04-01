<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ArticulosModel;
use App\Models\DescuentosModel;
use App\Models\ProveedoresModel;
use App\Models\CategoriasModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Config\Services; // Agrega esto al inicio de tu controlador
use CodeIgniter\API\ResponseTrait;

class Articulos extends BaseController
{
	use ResponseTrait; 
	public function index()
    {
        $articulosModel = new ArticulosModel();
        $categoriasModel = new CategoriasModel();
        
        // Obtener artículos con información de proveedores
        $builder = $articulosModel->select('sellopro_articulos.*, sellopro_proveedores.empresa as nombre_proveedor')
                                 ->join('sellopro_proveedores', 'sellopro_proveedores.id_proveedor = sellopro_articulos.proveedor', 'left');
        
        // Obtener todas las categorías
        $categorias = $categoriasModel->findAll();
        
        // Preparar datos para la vista
        $data = [
            'articulos' => $builder->findAll(),
            'categorias' => $categorias
        ];
        
        return view('Panel/articulos', $data);
    }
	public function mostrar()
	{
	    try {
	        $modelo = new ArticulosModel();
	        $articulos = $modelo->select('sellopro_articulos.*, sellopro_proveedores.empresa as nombre_proveedor')
	                           ->join('sellopro_proveedores', 'sellopro_proveedores.id_proveedor = sellopro_articulos.proveedor', 'left')
	                           ->findAll();
	        
	        if (empty($articulos)) {
	            return $this->response->setJSON([
	                'success' => false,
	                'message' => 'No se encontraron artículos'
	            ]);
	        }
	        
	        return $this->response->setJSON($articulos);
	        
	    } catch (\Exception $e) {
	        log_message('error', 'Error al obtener artículos: ' . $e->getMessage());
	        return $this->response->setStatusCode(500)->setJSON([
	            'success' => false,
	            'message' => 'Error interno del servidor'
	        ]);
	    }
	}
	public function mostrar_compras($id)
	{

		$buscar = new ArticulosModel();
		$buscar->where('id_proveedor',$id);
		$resultado = $buscar->findAll();
		return json_encode($resultado);
	}
	public function nuevo_art()
	{
	    // Obtener lista de proveedores
	    $modelProveedores = new ProveedoresModel();
	    $proveedores = $modelProveedores->findAll();
	    
	    // Obtener lista de categorías
	    $modelCategorias = new CategoriasModel();
	    $categorias = $modelCategorias->findAll();
	    
	    $data = [
	        'proveedores' => $proveedores,
	        'categorias' => $categorias  // Agregamos las categorías a los datos
	    ];
	    
	    return view('Panel/nuevo_articulo', $data);
	}
	public function nuevo()
	{
		// Procesamiento de la imagen
		$img = '';
		$file = $this->request->getFile('img');

		if ($file && $file->isValid() && !$file->hasMoved()) {
			$newName = $file->getRandomName();
			\Config\Services::image()
				->withFile($file->getPathname())
				->resize(800, 800, true, 'height')
				->save(FCPATH . 'public/img/catalogo/' . $newName, 70);

			$img = $newName;
		}

		$precio_prov_input = (float)$this->request->getPost('precio_prov');
		$id_proveedor = $this->request->getPost('proveedor');

		// Obtener proveedor
		$modelProveedores = new ProveedoresModel();
		$proveedor = $modelProveedores->find($id_proveedor);

		// Valores por defecto
		$precio_sin_iva = $precio_prov_input;
		$descuento = 0;

		// 🔥 1. DESGLOSAR IVA si aplica
		if ($proveedor && $proveedor['incluye_iva']) {
			$precio_sin_iva = $precio_prov_input / 1.16;
		}

		// 🔥 2. APLICAR DESCUENTO
		if ($proveedor && !empty($proveedor['descuento'])) {
			$descuento = (float)$proveedor['descuento'];
			$precio_sin_iva = $precio_sin_iva * (1 - ($descuento / 100));
		}

		// 🔥 3. Redondear (importante)
		$precio_prov = round($precio_sin_iva, 2);
		$precio_pub  = (float)$this->request->getPost('precio_pub');

		$model = new ArticulosModel();

		$data = [
			'nombre' => $this->request->getPost('nombre'),
			'modelo' => $this->request->getPost('modelo'),
			'precio_prov' => $precio_prov,
			'precio_pub' => $precio_pub,
			'precio_dist' => 0,
			'venta' => $this->request->getPost('venta') ? 1 : 0,
			'visible' => $this->request->getPost('visible') ? 1 : 0,
			'img' => $img,
			'proveedor' => $this->request->getPost('proveedor'),
			'categoria' => $this->request->getPost('categoria')
		];

		$model->insert($data);

		return redirect()->to('/articulos');
	}
	
	public function editar_rapido($id)
	{
		$model = new ArticulosModel();
		$resultado = $model->where('id_articulo',$id)->findAll();
		if (empty($resultado)){
		    return $this->response->setJSON([
		        'status'=>'error',
		        'message'=>'No se hizo la consulta',
		        'flag'=>0
		    ]);
		}else{
			return $this->response->setJSON([
		        'status'=>'success',
		        'message'=>'Consulta realizada con éxito',
		        'flag'=>1,
		        'data'=> $resultado
		    ]);
		}

	}
	public function editar($id)
	{
	    // Obtener el artículo a editar
	    $modelArticulos = new ArticulosModel();
	    $resultado = $modelArticulos->where('id_articulo', $id)->findAll();
	    $nombre = $resultado[0]['nombre']." - ".$resultado[0]['modelo'];

	    // Obtener lista de proveedores
	    $modelProveedores = new ProveedoresModel();
	    $resultado_prov = $modelProveedores->findAll();

	    // Obtener lista de categorías
	    $modelCategorias = new CategoriasModel();
	    $categorias = $modelCategorias->findAll();

	    $data = [
	        'articulos' => $resultado,
	        'nombre' => $nombre,
	        'proveedores' => $resultado_prov,
	        'categorias' => $categorias  // Agregamos las categorías a los datos
	    ];
	    
	    return view('Panel/editar_articulo', $data);
	}
	public function actualizar_rapido($idArticulo)
	{
	    // Validar que la solicitud sea POST
	    if (!$this->request->is('post')) {
	        return $this->response->setStatusCode(405)->setJSON(['error' => 'Método no permitido']);
	    }

	    // Obtener los datos enviados
	    $datos = $this->request->getJSON(true);
	    
	    // Validar los datos recibidos
	    $reglas = [
	        'nombre' => 'required|min_length[3]|max_length[100]',
	        'modelo' => 'permit_empty|max_length[50]',
	        'precio_pub' => 'required|decimal',
	        'precio_dist' => 'required|decimal',
	        'precio_prov' => 'required|decimal',
	        'categoria' => 'permit_empty|max_length[3]'
	    ];

	    if (!$this->validate($reglas)) {
	        return $this->response
	            ->setStatusCode(400)
	            ->setJSON(['errors' => $this->validator->getErrors()]);
	    }

	    try {
	        // Cargar el modelo de artículos
	        $articuloModel = new \App\Models\ArticulosModel();
	        
	        // Verificar que el artículo existe
	        $articulo = $articuloModel->find($idArticulo);
	        if (!$articulo) {
	            return $this->response
	                ->setStatusCode(404)
	                ->setJSON(['error' => 'Artículo no encontrado']);
	        }

	        // Actualizar el artículo
	        $articuloModel->update($idArticulo, [
	            'nombre' => $datos['nombre'],
	            'modelo' => $datos['modelo'],
	            'precio_pub' => $datos['precio_pub'],
	            'precio_dist' => $datos['precio_dist'],
	            'precio_prov' => $datos['precio_prov'],
	            'categoria' => $datos['categoria'],
	        ]);

	        return $this->response->setJSON(['success' => true]);

	    } catch (\Exception $e) {
	        log_message('error', 'Error al actualizar artículo: ' . $e->getMessage());
	        return $this->response
	            ->setStatusCode(500)
	            ->setJSON(['error' => 'Error interno del servidor']);
	    }
	}
	public function actualizar()
	{
		// -------------------------
		// 📊 DESCUENTO DISTRIBUIDOR (opcional)
		// -------------------------
		$modelDescuentos = new DescuentosModel();
		$porcentajes_dist = $modelDescuentos->find(2);

		$descuento_dist = 0;

		if ($porcentajes_dist && isset($porcentajes_dist['descuento'])) {
			$descuento_dist = (float)$porcentajes_dist['descuento'];
		}

		$porcentaje_venta_distribuidor = 1 + ($descuento_dist / 100);


		// -------------------------
		// 🖼️ PROCESAMIENTO DE IMAGEN
		// -------------------------
		$img = $this->request->getPost('imagen_actual');

		if ($this->request->getPost('eliminar_imagen')) {
			if ($img && file_exists(FCPATH . 'public/img/catalogo/' . $img)) {
				unlink(FCPATH . 'public/img/catalogo/' . $img);
			}
			$img = '';
		}

		$file = $this->request->getFile('img');

		if ($file && $file->isValid() && !$file->hasMoved()) {

			$imagenAnterior = $this->request->getPost('imagen_actual');
			if ($imagenAnterior && file_exists(FCPATH . 'public/img/catalogo/' . $imagenAnterior)) {
				unlink(FCPATH . 'public/img/catalogo/' . $imagenAnterior);
			}

			$newName = $file->getRandomName();
			$maxSize = 70 * 1024;
			$quality = 70;

			\Config\Services::image()
				->withFile($file->getPathname())
				->resize(800, 800, true, 'height')
				->save(FCPATH . 'public/img/catalogo/' . $newName, $quality);

			$fileSize = filesize(FCPATH . 'public/img/catalogo/' . $newName);

			if ($fileSize > $maxSize) {
				$quality = 70 - (($fileSize - $maxSize) / $maxSize * 20);
				$quality = max($quality, 10);

				\Config\Services::image()
					->withFile($file->getPathname())
					->resize(800, 800, true, 'height')
					->save(FCPATH . 'public/img/catalogo/' . $newName, $quality);
			}

			$img = $newName;
		}


		// -------------------------
		// 💰 CÁLCULO PRECIO PROVEEDOR
		// -------------------------
		$precio_input = (float)$this->request->getPost('precio_prov');
		$id_proveedor = $this->request->getPost('proveedor');

		$modelProveedores = new ProveedoresModel();
		$proveedor = $modelProveedores->find($id_proveedor);

		$precio = $precio_input;

		$iva = 0.16;

		// 🔥 1. DESGLOSAR IVA (si el proveedor ya lo incluye)
		if ($proveedor && isset($proveedor['incluye_iva']) && $proveedor['incluye_iva'] == 1) {
			$precio = $precio / (1 + $iva);
		}

		// 🔥 2. APLICAR DESCUENTO (aunque sea 0)
		if ($proveedor && isset($proveedor['descuento'])) {
			$descuento = (float)$proveedor['descuento'];
			$precio = $precio * (1 - ($descuento / 100));
		}

		// 🔥 3. REDONDEAR
		$precio_prov = round($precio, 2);


		// -------------------------
		// 📝 ACTUALIZAR ARTÍCULO
		// -------------------------
		$modelo = new ArticulosModel();
		$id = $this->request->getPost('idarticulo');

		$data = [
			'nombre' => $this->request->getPost('nombre'),
			'modelo' => $this->request->getPost('modelo'),
			'precio_prov' => $precio_prov,
			'precio_pub' => (float)$this->request->getPost('precio_pub'),
			'precio_dist' => (float)$this->request->getPost('precio_dist'),
			'minimo' => (int)$this->request->getPost('minimo'),
			'clave_producto' => $this->request->getPost('clave_producto'),
			'stock' => (int)$this->request->getPost('stock'),
			'venta' => $this->request->getPost('venta') == '1' ? 1 : 0,
			'visible' => $this->request->getPost('visible') == '1' ? 1 : 0,
			'img' => $img,
			'proveedor' => $id_proveedor,
			'categoria' => $this->request->getPost('categoria')
		];

		$modelo->update($id, $data);

		return redirect()->to('/articulos')->with('success', 'Artículo actualizado correctamente');
	}
	public function eliminar($id)
	{
		$modelo = new ArticulosModel();
		$modelo->delete($id);
		return redirect()->to('/articulos');

	}
	public function eliminarMasivo()
	{
	    $ids = $this->request->getVar('ids');
	    
	    if (empty($ids)) {
	        return $this->response->setJSON([
	            'success' => false,
	            'message' => 'No se recibieron IDs para eliminar'
	        ]);
	    }
	    
	    $modelo = new ArticulosModel();
	    $deleted = 0;
	    
	    foreach ($ids as $id) {
	        if ($modelo->delete($id)) {
	            $deleted++;
	        }
	    }
	    
	    return $this->response->setJSON([
	        'success' => true,
	        'deleted' => $deleted,
	        'message' => 'Artículos eliminados correctamente'
	    ]);
	}
	public function importArticulos()
	{
		$validation = \Config\Services::validation();

		$validation->setRules([
			'archivo_excel' => [
				'label' => 'Archivo CSV',
				'rules' => 'uploaded[archivo_excel]|max_size[archivo_excel,5120]|ext_in[archivo_excel,csv]',
			]
		]);

		if (!$validation->withRequest($this->request)->run()) {
			return redirect()->back()->with('error', 'Archivo inválido');
		}

		$file = $this->request->getFile('archivo_excel');

		if (!$file->isValid()) {
			return redirect()->back()->with('error', $file->getErrorString());
		}

		$path = $file->getTempName();

		$handle = fopen($path, "r");

		if (!$handle) {
			return redirect()->back()->with('error', 'No se pudo abrir el archivo');
		}

		$model = new \App\Models\ArticulosModel();

		// Leer encabezado
		$header = fgetcsv($handle, 1000, ",");

		$expected = [
			'nombre',
			'modelo',
			'precio_proveedor',
			'precio_publico',
			'precio_distribuidor',
			'minimo',
			'stock',
			'imagen',
			'venta',
			'proveedor',
			'categoria',
			'clave',
			'visible'
		];

		$header = array_map(function($h) {
			$h = trim($h);

			// 🔥 quitar BOM
			$h = preg_replace('/^\xEF\xBB\xBF/', '', $h);

			$h = strtolower($h);
			$h = str_replace([' ', '-'], '_', $h);
			$h = str_replace(['á','é','í','ó','ú'], ['a','e','i','o','u'], $h);
			return $h;
		}, $header);

		if ($header !== $expected) {
			dd([
				'header_recibido' => $header,
				'header_esperado' => $expected
			]);
		}

		$imported = 0;
		$errors = [];

		while (($row = fgetcsv($handle, 1000, ",")) !== false) {

			if (empty(array_filter($row))) continue;

			$toBool = fn($v) => in_array(strtolower(trim($v)), ['1','si','sí','true']) ? 1 : 0;
			$toFloat = fn($v) => (float) str_replace([',', '$'], '', $v);

			$data = [
				'nombre'         => trim($row[0]),
				'modelo'         => trim($row[1]),
				'precio_prov'    => $toFloat($row[2]),
				'precio_pub'     => $toFloat($row[3]),
				'precio_dist'    => $toFloat($row[4]),
				'minimo'         => (int)$row[5],
				'stock'          => (int)$row[6],
				'img'            => trim($row[7]),
				'venta'          => $toBool($row[8]),
				'proveedor'      => (int)$row[9],
				'categoria'      => (int)$row[10],
				'clave_producto' => trim($row[11]),
				'visible'        => $toBool($row[12]),
			];

			// Validaciones básicas
			if (empty($data['nombre']) || empty($data['clave_producto'])) {
				$errors[] = "Fila inválida (nombre o clave vacía)";
				continue;
			}

			if ($data['precio_prov'] <= 0) {
				$errors[] = "Precio inválido en: " . $data['nombre'];
				continue;
			}

			try {
				$model->insert($data);
				$imported++;
			} catch (\Exception $e) {
				$errors[] = $e->getMessage();
			}
		}

		fclose($handle);

		$msg = "Importados: $imported";

		if (!empty($errors)) {
			session()->setFlashdata('import_errors', $errors);
			$msg .= " | Errores: " . count($errors);
		}
		//dd($errors);
		return redirect()->back()->with('success', $msg);
	}
	public function cambiarVisibilidad($id_articulo = null)
    {
        // Verificar que sea una petición AJAX y POST
        if (!$this->request->isAJAX() || $this->request->getMethod(true) !== 'POST') {
            return $this->failForbidden('Acceso no permitido.');
        }

        $articulosModel = new ArticulosModel();
        $articulo = $articulosModel->find($id_articulo);

        if (!$articulo) {
            return $this->failNotFound('Artículo no encontrado.');
        }

        // Obtener el nuevo estado de visibilidad del cuerpo de la solicitud JSON
        $jsonData = $this->request->getJSON();

        if (!isset($jsonData->visible) || !in_array($jsonData->visible, [0, 1])) {
            return $this->failValidationErrors('El estado de visibilidad no es válido. Debe ser 0 o 1.');
        }

        $nuevoEstadoVisible = (int) $jsonData->visible;

        try {
            $data = [
                'visible' => $nuevoEstadoVisible
            ];

            if ($articulosModel->update($id_articulo, $data)) {
                $mensaje = $nuevoEstadoVisible == 1 ? 'Artículo marcado como visible.' : 'Artículo marcado como oculto.';
                return $this->respond(['success' => true, 'message' => $mensaje]);
            } else {
                // Esto podría ocurrir si la actualización no afecta filas, o hay un error de DB no capturado
                log_message('error', 'Error al actualizar visibilidad del artículo ID: ' . $id_articulo . ' - Errores del modelo: ' . json_encode($articulosModel->errors()));
                return $this->fail('No se pudo actualizar la visibilidad del artículo. Revise los logs.', 500);
            }
        } catch (\Exception $e) {
            log_message('error', '[ERROR] ArticuloController::cambiarVisibilidad: ' . $e->getMessage());
            return $this->failServerError('Ocurrió un error en el servidor al intentar actualizar la visibilidad.');
        }
    }


}