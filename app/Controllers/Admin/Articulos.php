<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ArticulosModel;
use App\Models\DescuentosModel;
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
		return view('Panel/nuevo_articulo');
	}
	public function nuevo()
	{
	    // Obtener porcentajes desde la base de datos
	    $model = new DescuentosModel();
	    $porcentajes_publico = $model->find('1');
	    $porcentajes_dist = $model->find('2');
	    
	    // Convertir porcentajes enteros a multiplicadores (ej: 30 → 1.30)
	    $porcentaje_venta_publico = 1 + ($porcentajes_publico['descuento'] / 100);
	    $porcentaje_venta_distribuidor = 1 + ($porcentajes_dist['descuento'] / 100);

	    // Procesamiento de la imagen
	    $img = '';
	    $file = $this->request->getFile('img');
	    if ($file && $file->isValid() && !$file->hasMoved()) {
	        $newName = $file->getRandomName();
	        $file->move(WRITEPATH . 'uploads/articulos', $newName);
	        $img = $newName;
	    }

	    // Cálculo de precios automáticos
	    $precio_prov = (float)$this->request->getPost('precio_prov');
	    $precio_pub = round($precio_prov * $porcentaje_venta_publico);
	    $precio_dist = round($precio_prov * $porcentaje_venta_distribuidor);

	    $model = new ArticulosModel();
	    $data = [
	        'nombre' => $this->request->getPost('nombre'),
	        'modelo' => $this->request->getPost('modelo'),
	        'precio_prov' => $precio_prov,
	        'precio_pub' => $precio_pub,
	        'precio_dist' => $precio_dist,
	        'venta' => $this->request->getPost('venta') ? 1 : 0,
	        'img' => $img
	    ];
	    
	    $model->insert($data);
	    return redirect()->to('/articulos');
	}
	public function verImagen($nombreImagen)
	{
	    $rutaImagen = WRITEPATH . 'uploads/articulos/' . $nombreImagen;
	    
	    if(!file_exists($rutaImagen)) {
	        throw new \CodeIgniter\Exceptions\PageNotFoundException('Imagen no encontrada');
	    }

	    $mime = mime_content_type($rutaImagen);
	    header('Content-Type: '.$mime);
	    readfile($rutaImagen);
	    exit;
	}
	public function editar($id)
	{

		$model = new ArticulosModel();
		$resultado = $model->where('id_articulo',$id)->findAll();
		$nombre = $resultado[0]['nombre']." - ".$resultado[0]['modelo'];
		$data = ['articulos'=>$resultado,'nombre'=>$nombre];
		return view('Panel/editar_articulo',$data);

	}
	public function actualizar()
	{
		
		$precio = $this->request->getPost('precio_pub');
		//$sin_impuesto = $precio/1.16;

		$modelo = new ArticulosModel();
		$id = $this->request->getPost('idarticulo');
		$data = [
			'nombre'=> $this->request->getPost('nombre'),
			'modelo' => $this->request->getPost('modelo'),
			'precio_prov' => $this->request->getPost('precio_prov'),
			'precio_pub' => $precio,
		];
		$modelo->update($id,$data);
		return redirect()->to('/articulos');
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