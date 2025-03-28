<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ProveedoresModel;
use App\Models\FamiliaModel;

class Proveedores extends BaseController
{
	public function index()
	{
		$model = new ProveedoresModel();
		$data['proveedores'] = $model->findAll();
		return view('Panel/proveedores', $data);
	}
	public function nuevo() 
	{
		$model = new ProveedoresModel();
		$data = [
		    'empresa' => $this->request->getPost('empresa'),
		    'contacto' => $this->request->getPost('contacto'),
		    'telefono' => $this->request->getPost('telefono'),
		    'correo' => $this->request->getPost('correo')
		];
		$model->insert($data);
		return redirect()->to('/proveedores');
	}
	public function editar($id)
	{

		$model = new ProveedoresModel();
		$resultado = $model->where('id_proveedor',$id)->findAll();
		$nombre = $resultado[0]['empresa'];
		$data = ['proveedores'=>$resultado,'empresa'=>$nombre];
		return view('Panel/editar_proveedor',$data);

	}
	public function actualizar()
	{
		

		$modelo = new ProveedoresModel();
		$id = $this->request->getPost('id_proveedor');
		$data = [
			'empresa'=> $this->request->getPost('empresa'),
			'contacto' => $this->request->getPost('contacto'),
			'telefono' => $this->request->getPost('telefono'),
			'correo' => $this->request->getPost('correo'),
		];
		$modelo->update($id,$data);
		return redirect()->to('/proveedores');
	}
	public function eliminar($id)
	{
		$modelo = new ProveedoresModel();
		$modelo->delete($id);
		return redirect()->to('/proveedores');

	}
	public function mostrar_familias($id)
	{
		$familia = new FamiliaModel();
		$familia->where('id_proveedor',$id);
		$resultado = $familia->findAll();
		return json_encode($resultado);
	}
	public function agregar_familia()
	{
		$nuevo = new FamiliaModel();

		$request = \Config\Services::Request();
		$nombre = $request->getvar('nombre');
		$descuento = $request->getvar('descuento');
		$id = $request->getvar('id');
		$data = [
			'nombre'=>$nombre,
			'descuento'=>$descuento,
			'id_proveedor'=>$id,
		];
		$nuevo->insert($data);

	}
}