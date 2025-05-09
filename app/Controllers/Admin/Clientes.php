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
	// Versión con más validaciones
	public function actualizar()
	{
	    $modelo = new ClientesModel();
	    $id = $this->request->getPost('idcliente');
	    
	    // Validar que el ID existe
	    if(!$modelo->find($id)) {
	        return redirect()->to('/clientes')->with('error', 'Cliente no encontrado');
	    }
	    
	    $data = [
	        'nombre'    => $this->request->getPost('nombre', FILTER_SANITIZE_STRING),
	        'correo'    => $this->request->getPost('correo', FILTER_SANITIZE_EMAIL),
	        'direccion' => $this->request->getPost('direccion', FILTER_SANITIZE_STRING),
	        'telefono'  => $this->request->getPost('telefono', FILTER_SANITIZE_STRING),
	        'ciudad'    => $this->request->getPost('ciudad', FILTER_SANITIZE_STRING),
	        'estado'    => $this->request->getPost('estado'),
	        'cp'        => $this->request->getPost('cp', FILTER_SANITIZE_STRING),
	        'tipo'      => $this->request->getPost('tipo') == '2' ? '2' : '1'
	    ];
	    
	    if($modelo->update($id, $data)) {
	        return redirect()->to('/clientes')->with('success', 'Cliente actualizado correctamente');
	    } else {
	        return redirect()->back()->withInput()->with('errors', $modelo->errors());
	    }
	}
	public function eliminar($id)
	{
		$modelo = new ClientesModel();
		$modelo->delete($id);
		return redirect()->to('/clientes');

	}
}