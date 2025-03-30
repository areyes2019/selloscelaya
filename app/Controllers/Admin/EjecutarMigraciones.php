<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;

class EjecutarMigraciones extends BaseController
{
	public function index()
	{
		$migrate = \Config\Services::migrations();
		try {
            if ($migrate->latest()) {
                echo "Migraciones ejecutadas correctamente";
            }
        } catch (\Exception $e) {
            echo "Error al ejecutar migraciones: " . $e->getMessage();
        }
	}
}