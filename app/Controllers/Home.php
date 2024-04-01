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
}
