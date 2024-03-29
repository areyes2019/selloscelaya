<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data['header'] = view('header');
        $data['footer'] = view('footer');
        return view('welcome_message', $data);
    }
    public function autoentintables()
    {
        $data['header'] = view('header');
        $data['footer'] = view('footer');
        return view('autoentitables', $data);
    }
    public function madera(): string
    {
        $data['header'] = view('header');
        $data['footer'] = view('footer');
        return view('madera', $data);
    }
    public function portatiles(): string
    {
        $data['header'] = view('header');
        $data['footer'] = view('footer');
        return view('portatiles', $data);
    }
    public function gigantes(): string
    {
        $data['header'] = view('header');
        $data['footer'] = view('footer');
        return view('gigantes', $data);
    }
    public function fechadores(): string
    {
        $data['header'] = view('header');
        $data['footer'] = view('footer');
        return view('fechadores', $data);
    }
    public function textiles(): string
    {
        $data['header'] = view('header');
        $data['footer'] = view('footer');
        return view('textil', $data);
    }
}
