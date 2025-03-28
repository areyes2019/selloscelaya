<?php

namespace App\Controllers;

class Login extends BaseController{

	public function index()
	{
		return "este el panel de logeo";
	}

	public function admin()
	{
		return "Este es el panel de administracion";
	}
	public function login()
	{
		return "tienes que logiearte primero";
	}

}