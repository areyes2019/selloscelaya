<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ArticulosModel;
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
	public function nuevo()
	{
		//tenemos que quitarle el iva a el precio

		$precio = $this->request->getPost('precio_pub');
		//$sin_impuesto = $precio/1.16;

		$model = new ArticulosModel();
		$data = [
		    'nombre' => $this->request->getPost('nombre'),
		    'modelo' => $this->request->getPost('modelo'),
		    'precio_prov' => $this->request->getPost('precio_prov'),
		    'precio_pub' => $precio
		];
		$model->insert($data);
		return redirect()->to('/articulos');
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
	    
	    // Validaciones básicas
	    if (!$file || !$file->isValid() || !in_array($file->getExtension(), ['xlsx', 'xls'])) {
	        return redirect()->back()->with('error', 'Archivo no válido. Sube un archivo Excel (.xlsx o .xls)');
	    }

	    try {
	        $spreadsheet = IOFactory::load($file->getPathname());
	        $rows = $spreadsheet->getActiveSheet()->toArray();
	        
	        // Eliminar encabezados si existen
	        if (isset($rows[0]) && is_string($rows[0][0])) {
	            array_shift($rows);
	        }

	        $articulosModel = new ArticulosModel();
	        $imported = 0;
	        $errors = [];

	        foreach ($rows as $index => $row) {
	            // Saltar filas vacías
	            if (empty(array_filter($row))) continue;

	            $data = [
	                'nombre'       => $row[0] ?? '',
	                'modelo'       => !empty($row[1]) ? $row[1] : null,
	                'precio_prov' => is_numeric($row[2] ?? 0) ? (float)$row[2] : 0.00,
	                'precio_pub'   => is_numeric($row[3] ?? 0) ? (float)$row[3] : 0.00,
	                'precio_dist'   => is_numeric($row[4] ?? 0) ? (float)$row[3] : 0.00,
	                'minimo'       => is_numeric($row[5] ?? 0) ? (int)$row[4] : 0,
	                'stock'        => is_numeric($row[6] ?? 0) ? (int)$row[5] : 0,
	                'clave_producto' => $row[7] ?? '', // Nueva columna
	                'created_at'   => date('Y-m-d H:i:s'),
	                'updated_at'   => date('Y-m-d H:i:s')
	            ];

	            if ($articulosModel->insert($data)) {
	                $imported++;
	            } else {
	                $errors[] = "Fila {$index}: " . implode(', ', $articulosModel->errors());
	            }
	        }

	        $message = "Importación completada: {$imported} artículos importados";
	        if (!empty($errors)) {
	            $message .= ". Errores: " . implode('; ', $errors);
	            return redirect()->back()->with('warning', $message);
	        }

	        return redirect()->back()->with('success', $message);

	    } catch (\Exception $e) {
	        return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
	    }
	}

}