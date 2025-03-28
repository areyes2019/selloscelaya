<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\ClientesModel;

class Clientes extends BaseController
{
	public function index()
	{
		$model = new ClientesModel();
		$data['clientes'] = $model->findAll();
		return view('Panel/clientes', $data);
	}
	public function nuevo()
	{
		$model = new ClientesModel();
		$data = [
		    'nombre' => $this->request->getPost('nombre'),
		    'telefono' => $this->request->getPost('telefono'),
		    'direcion' => $this->request->getPost('direcion')
		];
		$model->insert($data);
		return redirect()->to('/clientes');
	}
	public function editar($id)
	{

		$model = new ClientesModel();
		$resultado = $model->where('id_cliente',$id)->findAll();
		$nombre = $resultado[0]['nombre'];
		$data = ['clientes'=>$resultado,'nombre'=>$nombre];
		return view('Panel/editar_cliente',$data);

	}
	public function actualizar()
	{
		

		$modelo = new ClientesModel();
		$id = $this->request->getPost('idcliente');
		$data = [
			'nombre'=> $this->request->getPost('nombre'),
			'correo' => $this->request->getPost('correo'),
			'direccion' => $this->request->getPost('direccion'),
			'telefono' => $this->request->getPost('telefono'),
			'ciudad' => $this->request->getPost('ciudad'),
			'estado' => $this->request->getPost('estado'),
			'cp' =>$this->request->getPost('cp'),
		];
		$modelo->update($id,$data);
		return redirect()->to('/clientes');
	}
	public function eliminar($id)
	{
		$modelo = new ClientesModel();
		$modelo->delete($id);
		return redirect()->to('/clientes');

	}
}