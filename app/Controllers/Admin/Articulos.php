<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ArticulosModel;
use App\Models\DescuentosModel;
use App\Models\ProveedoresModel;
use App\Models\CategoriasModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
class Articulos extends BaseController
{
	public function index()
	{
		$model = new ArticulosModel();
		$data['articulos'] = $model->findAll();
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
	    $file = $this->request->getFile('archivo_excel');
	    
	    // Validar archivo Excel
	    if (!$file || !$file->isValid() || !in_array($file->getExtension(), ['xlsx', 'xls'])) {
	        return redirect()->back()->with('error', 'Archivo no válido. Solo se permiten .xlsx o .xls');
	    }

	    try {
	        // Obtener porcentajes de descuento
	        $descuentosModel = new DescuentosModel();
	        $porcentajes_publico = $descuentosModel->find('1');
	        $porcentajes_dist = $descuentosModel->find('2');
	        
	        $porcentaje_publico = 1 + ($porcentajes_publico['descuento'] / 100);
	        $porcentaje_distribuidor = 1 + ($porcentajes_dist['descuento'] / 100);

	        // Procesar Excel
	        $spreadsheet = IOFactory::load($file->getPathname());
	        $sheet = $spreadsheet->getActiveSheet();
	        $rows = $sheet->toArray();
	        
	        // Eliminar encabezados
	        array_shift($rows);

	        $articulosModel = new ArticulosModel();
	        $imported = 0;
	        $errors = [];
	        $now = date('Y-m-d H:i:s');

	        foreach ($rows as $index => $row) {
	            $rowNumber = $index + 2; // +2 por encabezados y base 0
	            
	            // Validar fila mínima
	            if (empty($row[0])) {
	                $errors[] = "Fila {$rowNumber}: Falta el nombre del artículo";
	                continue;
	            }

	            // Calcular precios automáticamente
	            $precio_prov = is_numeric($row[2] ?? 0) ? (float)$row[2] : 0.00;
	            $precio_pub = round($precio_prov * $porcentaje_publico);
	            $precio_dist = round($precio_prov * $porcentaje_distribuidor);

	            $data = [
	                'nombre'        => trim($row[0]),
	                'modelo'        => !empty($row[1]) ? trim($row[1]) : null,
	                'precio_prov'  => $precio_prov,
	                'precio_pub'   => $precio_pub,
	                'precio_dist'  => $precio_dist,
	                'minimo'       => is_numeric($row[3] ?? 0) ? (int)$row[4] : 0,
	                'stock'        => is_numeric($row[4] ?? 0) ? (int)$row[5] : 0,
	                'clave_producto' => trim($row[5] ?? ''),
	                'img'          => trim($row[6] ?? ''), // Nombre de imagen (sin procesar)
	                'venta'        => (strtolower(trim($row[7] ?? '')) === 'no') ? 0 : 1,
	                'created_at'   => $now,
	                'updated_at'   => $now
	            ];

	            // Validación adicional
	            if (empty($data['nombre'])) {
	                $errors[] = "Fila {$rowNumber}: El nombre no puede estar vacío";
	                continue;
	            }

	            try {
	                if ($articulosModel->insert($data)) {
	                    $imported++;
	                } else {
	                    $errors[] = "Fila {$rowNumber}: " . implode(', ', $articulosModel->errors());
	                }
	            } catch (\Exception $e) {
	                $errors[] = "Fila {$rowNumber}: Error al insertar - " . $e->getMessage();
	            }
	        }

	        // Preparar resultado
	        $message = "Importación completada: {$imported} artículos importados";
	        if (!empty($errors)) {
	            $message .= ". Errores en " . count($errors) . " filas";
	            return redirect()->back()
	                ->with('warning', $message)
	                ->with('error_details', array_slice($errors, 0, 20));
	        }

	        return redirect()->back()->with('success', $message);

	    } catch (\Exception $e) {
	        log_message('error', 'Error en importArticulos: ' . $e->getMessage());
	        return redirect()->back()->with('error', 'Error al procesar: ' . $e->getMessage());
	    }
	}

}