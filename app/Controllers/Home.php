<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('home');
        /*$data['header'] = view('header');
        $data['footer'] = view('footer');
        return view('welcome_message', $data);*/
    }
    public function autoentintables()
    {
        return view('autoentitables');
    }
    public function madera(): string
    {
        return view('madera');
    }
    public function portatiles(): string
    {
        return view('portatiles');
    }
    public function gigantes(): string
    {
        return view('gigantes');
    }
    public function fechadores(): string
    {
        return view('fechadores');
    }
    public function textiles(): string
    {
        return view('textil');
    }
    public function catalogo()
    {
        return view('catalogo');
    }
    public function contacto()
    {
        $correo = $this->request->getPost('correo');
        $nombre = $this->request->getPost('nombre');
        $texto = $this->request->getPost('texto');
        
        $email = \Config\Services::email();
        $email->setFrom($correo, $nombre);
        $email->setTo('ventas@selloscelaya.com');
        $email->setSubject('Quiero más información');
        $email->setMessage($texto);
        $email->send();
        return view('home');
    }
}
