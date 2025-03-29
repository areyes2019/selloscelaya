<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ArticulosModel;
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
		$resultado = $model->where('idArticulo',$id)->findAll();
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
}