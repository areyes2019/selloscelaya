<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ArticulosModel;
use App\Models\DescuentosModel;
use App\Models\ProveedoresModel;
use App\Models\CategoriasModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Config\Services; // Agrega esto al inicio de tu controlador

class Articulos extends BaseController
{
	public function index()
	{
	    $model = new ArticulosModel();
	    $builder = $model->select('sellopro_articulos.*, sellopro_proveedores.empresa as nombre_proveedor')
	                     ->join('sellopro_proveedores', 'sellopro_proveedores.id_proveedor = sellopro_articulos.proveedor', 'left');
	    
	    $data['articulos'] = $builder->findAll();
	    return view('Panel/articulos', $data);
	}
	public function mostrar()
	{
		$modelo = new ArticulosModel();
		$query = $modelo->findAll();
		return json_encode($query);
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
	        'categoria' => 'permit_empty|max_length[1]'
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

	public function importArticulos()
	{
	    // Validar CSRF token (forma correcta en CI4)
	    if (!$this->request->getPost(csrf_token())) {
	        return redirect()->back()->with('error', 'Token CSRF inválido o expirado');
	    }

	    // Validar que se haya subido un archivo
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
	        return redirect()->back()->with('errors', $validation->getErrors());
	    }

	    $file = $this->request->getFile('archivo_excel');

	    try {
	        // Cargar el archivo Excel
	        $spreadsheet = IOFactory::load($file->getTempName());
	        $worksheet = $spreadsheet->getActiveSheet();
	        $rows = $worksheet->toArray();

	        // Eliminar encabezados si existen
	        array_shift($rows);

	        $model = new ArticulosModel();
	        $db = \Config\Database::connect();
	        $db->transStart(); // Iniciar transacción

	        $imported = 0;
	        $errors = [];

	        // Columnas esperadas en el Excel (13 en total):
	        // 0: nombre, 1: modelo, 2: precio_prov, 3: precio_pub, 4: precio_dist, 
	        // 5: minimo, 6: stock, 7: img, 8: venta, 9: clave_producto, 
	        // 10: visible, 11: categoria, 12: proveedor

	        foreach ($rows as $index => $row) {
	            // Validar que la fila tenga al menos 13 columnas
	            if (count($row) < 13) {
	                $errors[] = "Fila " . ($index + 1) . ": Debe tener 13 columnas, tiene solo " . count($row);
	                continue;
	            }

	            // Limpiar y validar datos
	            $data = [
	                'nombre'         => trim($row[0] ?? ''),
	                'modelo'         => trim($row[1] ?? ''),
	                'precio_prov'    => (float)($row[2] ?? 0),
	                'precio_pub'     => (float)($row[3] ?? 0),
	                'precio_dist'    => (float)($row[4] ?? 0),
	                'minimo'         => (int)($row[5] ?? 0),
	                'stock'          => (int)($row[6] ?? 0),
	                'img'            => trim($row[7] ?? ''),
	                'venta'          => strtolower(trim($row[8] ?? '')) === 'si' ? 1 : 0,
	                'clave_producto' => trim($row[9] ?? ''),
	                'visible'        => strtolower(trim($row[10] ?? '')) === 'si' ? 1 : 0,
	                'categoria'      => (int)($row[11] ?? 0),
	                'proveedor'     => (int)($row[12] ?? 0)
	            ];

	            // Validaciones básicas
	            if (empty($data['nombre']) || empty($data['clave_producto'])) {
	                $errors[] = "Fila " . ($index + 1) . ": Nombre y Clave Producto son obligatorios";
	                continue;
	            }

	            if ($data['precio_prov'] <= 0) {
	                $errors[] = "Fila " . ($index + 1) . ": Precio Proveedor debe ser mayor a 0";
	                continue;
	            }

	            // Validar que exista la categoría y proveedor (opcional)
	            // Puedes agregar consultas a sus respectivas tablas aquí

	            // Intentar insertar (usando el modelo para seguridad)
	            try {
	                if (!$model->save($data)) {
	                    $errors[] = "Fila " . ($index + 1) . ": " . implode(', ', $model->errors());
	                } else {
	                    $imported++;
	                }
	            } catch (\Exception $e) {
	                $errors[] = "Fila " . ($index + 1) . ": Error al guardar - " . $e->getMessage();
	            }
	        }

	        $db->transComplete();

	        if ($db->transStatus() === false) {
	            return redirect()->back()->with('error', 'Error en la transacción de base de datos');
	        }

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


}