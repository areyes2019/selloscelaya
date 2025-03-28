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
	public function nuevo()
	{
		$model = new ArticulosModel();
		$data = [
		    'nombre' => $this->request->getPost('nombre'),
		    'modelo' => $this->request->getPost('modelo'),
		    'precio_prov' => $this->request->getPost('precio_prov'),
		    'precio_pub' => $this->request->getPost('precio_pub')
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
		

		$modelo = new ArticulosModel();
		$id = $this->request->getPost('idarticulo');
		if (!empty($this->request->getPost('stock'))) {
			$stock = $this->request->getPost('stock');
		}else{
			$stock = 0;
		}
		$data = [
			'nombre'=> $this->request->getPost('nombre'),
			'modelo' => $this->request->getPost('modelo'),
			'precio_prov' => $this->request->getPost('precio_prov'),
			'precio_pub' => $this->request->getPost('precio_pub'),
			'minimo' => $this->request->getPost('minimo'),
			'stock' => $stock,
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