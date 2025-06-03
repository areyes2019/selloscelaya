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
	    // Obtener porcentajes desde la base de datos (solo para distribuidor)
	    $model = new DescuentosModel();
	    $porcentajes_dist = $model->find('2');
	    
	    // Convertir porcentaje entero a multiplicador (ej: 30 → 1.30)
	    $porcentaje_venta_distribuidor = 1 + ($porcentajes_dist['descuento'] / 100);

	    // Procesamiento de la imagen
	    $img = '';
	    $file = $this->request->getFile('img');
	    
	    if ($file && $file->isValid() && !$file->hasMoved()) {
	        // Procesar y comprimir la nueva imagen
	        $newName = $file->getRandomName();
	        $maxSize = 70 * 1024; // 70KB en bytes
	        $quality = 70; // Calidad inicial
	        
	        // Primera compresión
	        \Config\Services::image()
	            ->withFile($file->getPathname())
	            ->resize(800, 800, true, 'height')
	            ->save(FCPATH . 'public/img/catalogo/' . $newName, $quality);
	        
	        // Verificar tamaño y ajustar si es necesario
	        $fileSize = filesize(FCPATH . 'public/img/catalogo/' . $newName);
	        
	        if ($fileSize > $maxSize) {
	            // Calcular nueva calidad proporcionalmente
	            $quality = 70 - (($fileSize - $maxSize) / $maxSize * 20);
	            $quality = max($quality, 10); // No bajar de 10 de calidad
	            
	            // Segunda compresión con calidad ajustada
	            \Config\Services::image()
	                ->withFile($file->getPathname())
	                ->resize(800, 800, true, 'height')
	                ->save(FCPATH . 'public/img/catalogo/' . $newName, $quality);
	        }
	        
	        $img = $newName;
	    }

	    // Cálculo de precio distribuidor
	    $precio_prov = (float)$this->request->getPost('precio_prov');
	    $precio_pub = (float)$this->request->getPost('precio_pub');
	    $precio_dist = round($precio_prov * $porcentaje_venta_distribuidor);

	    $model = new ArticulosModel();
	    $data = [
	        'nombre' => $this->request->getPost('nombre'),
	        'modelo' => $this->request->getPost('modelo'),
	        'precio_prov' => $precio_prov,
	        'precio_pub' => $precio_pub,
	        'precio_dist' => $precio_dist,
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
	    // Obtener porcentajes para cálculo de precios (si es necesario)
	    $modelDescuentos = new DescuentosModel();
	    $porcentajes_dist = $modelDescuentos->find('2');
	    $porcentaje_venta_distribuidor = 1 + ($porcentajes_dist['descuento'] / 100);

	    // Procesamiento de la imagen
	    $img = $this->request->getPost('imagen_actual');
	    
	    // Verificar si se solicita eliminar la imagen actual
	    if ($this->request->getPost('eliminar_imagen')) {
	        if ($img && file_exists(FCPATH . 'public/img/catalogo/' . $img)) {
	            unlink(FCPATH . 'public/img/catalogo/' . $img);
	        }
	        $img = '';
	    }

	    $file = $this->request->getFile('img');
	    
	    if ($file && $file->isValid() && !$file->hasMoved()) {
	        // Eliminar la imagen anterior si existe
	        $imagenAnterior = $this->request->getPost('imagen_actual');
	        if ($imagenAnterior && file_exists(FCPATH . 'public/img/catalogo/' . $imagenAnterior)) {
	            unlink(FCPATH . 'public/img/catalogo/' . $imagenAnterior);
	        }
	        
	        // Procesar y comprimir la nueva imagen
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

	    // Actualizar datos del artículo
	    $modelo = new ArticulosModel();
	    $id = $this->request->getPost('idarticulo');
	    
	    $data = [
	        'nombre' => $this->request->getPost('nombre'),
	        'modelo' => $this->request->getPost('modelo'),
	        'precio_prov' => (float)$this->request->getPost('precio_prov'),
	        'precio_pub' => (float)$this->request->getPost('precio_pub'),
	        'precio_dist' => (float)$this->request->getPost('precio_dist'),
	        'minimo' => (int)$this->request->getPost('minimo'),
	        'clave_producto' => $this->request->getPost('clave_producto'),
	        'stock' => (int)$this->request->getPost('stock'),
	        'venta' => $this->request->getPost('venta') == '1' ? 1 : 0, // Modificado para select
	        'visible' => $this->request->getPost('visible') == '1' ? 1 : 0, // Modificado para select
	        'img' => $img,
	        'proveedor' => $this->request->getPost('proveedor'),
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
	    // Validar CSRF token
	    if (!$this->request->is('post') || !$this->request->getPost(csrf_token())) {
	        return redirect()->back()->with('error', 'Token CSRF inválido o expirado');
	    }

	    // Validar archivo
	    $validation = \Config\Services::validation();
	    $validation->setRules([
	        'archivo_excel' => [
	            'label' => 'Archivo Excel',
	            'rules' => 'uploaded[archivo_excel]|max_size[archivo_excel,5120]|ext_in[archivo_excel,xls,xlsx]',
	            'errors' => [
	                'uploaded' => 'Debes seleccionar un archivo Excel',
	                'max_size' => 'El archivo no debe exceder 5MB',
	                'ext_in' => 'Solo se permiten archivos .xls o .xlsx'
	            ]
	        ]
	    ]);

	    if (!$validation->withRequest($this->request)->run()) {
	        return redirect()->back()->withInput()->with('errors', $validation->getErrors());
	    }

	    $file = $this->request->getFile('archivo_excel');

	    try {
	        $spreadsheet = IOFactory::load($file->getTempName());
	        $worksheet = $spreadsheet->getActiveSheet();
	        $rows = $worksheet->toArray();

	        // Debug: Ver contenido del archivo
	        //dd($rows); // Descomenta esta línea para ver la estructura de datos

	        // Eliminar encabezados si existen
	        $header = array_shift($rows);
	        
	        // Debug: Ver encabezados
	        //dd($header); // Descomenta para ver los encabezados

	        $model = new ArticulosModel();
	        $db = \Config\Database::connect();
	        $db->transStart();

	        $imported = 0;
	        $errors = [];

	        foreach ($rows as $index => $row) {
	            // Limpiar valores nulos
	            $row = array_map(function($value) {
	                return $value === null ? '' : $value;
	            }, $row);

	            // Debug: Ver fila actual
	            // dd($row); // Descomenta para ver los datos de cada fila

	            // Validar que la fila tenga datos
	            if (empty(array_filter($row))) {
	                continue; // Saltar filas vacías
	            }

	            // Validar que la fila tenga al menos 13 columnas
	            if (count($row) < 13) {
	                $errors[] = "Fila " . ($index + 2) . ": Debe tener 13 columnas, tiene solo " . count($row);
	                continue;
	            }

	            // Mapeo de columnas (ajustado a tu estructura)
	            $data = [
	                'nombre'         => trim($row[0]),
	                'modelo'         => trim($row[1]),
	                'precio_prov'    => (float)str_replace(',', '', $row[2]),
	                'precio_pub'     => (float)str_replace(',', '', $row[3]),
	                'precio_dist'    => (float)str_replace(',', '', $row[4]),
	                'minimo'         => (int)$row[5],
	                'stock'          => (int)$row[6],
	                'img'            => trim($row[7]),
	                'venta'          => is_numeric($row[8]) ? (int)$row[8] : (strtolower($row[8]) == 'si' ? 1 : 0),
	                'proveedor'      => (int)$row[9],
	                'categoria'      => (int)$row[10],
	                'clave_producto' => trim($row[11]),
	                'visible'        => is_numeric($row[12]) ? (int)$row[12] : (strtolower($row[12]) == 'si' ? 1 : 0),
	            ];

	            // Validaciones básicas
	            if (empty($data['nombre'])) {
	                $errors[] = "Fila " . ($index + 2) . ": El nombre es obligatorio";
	                continue;
	            }

	            if (empty($data['clave_producto'])) {
	                $errors[] = "Fila " . ($index + 2) . ": La clave de producto es obligatoria";
	                continue;
	            }

	            if ($data['precio_prov'] <= 0) {
	                $errors[] = "Fila " . ($index + 2) . ": El precio de proveedor debe ser mayor a 0";
	                continue;
	            }

	            // Intentar insertar
	            try {
	                if ($model->insert($data) === false) {
	                    $modelErrors = $model->errors();
	                    $errors[] = "Fila " . ($index + 2) . ": " . implode(', ', $modelErrors);
	                } else {
	                    $imported++;
	                }
	            } catch (\Exception $e) {
	                $errors[] = "Fila " . ($index + 2) . ": Error al guardar - " . $e->getMessage();
	            }
	        }

	        $db->transComplete();

	        // Debug: Ver resultados
	        // dd(['imported' => $imported, 'errors' => $errors]);

	        $message = "Importación completada: $imported registros importados";
	        if (!empty($errors)) {
	            $message .= "<br>Errores encontrados: " . count($errors);
	            session()->setFlashdata('import_errors', $errors);
	        }

	        return redirect()->back()->with('success', $message);

	    } catch (\Exception $e) {
	        return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
	    }
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