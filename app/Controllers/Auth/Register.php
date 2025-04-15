// app/Controllers/Auth/Register.php
<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Config\Auth;

class Register extends BaseController
{
    protected $userModel;
    protected $authConfig;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->authConfig = config('Auth');
    }
    
    public function index()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processRegistration();
        }
        
        return view('auth/register');
    }
    
    protected function processRegistration()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'active' => 1,
        ];
        
        $this->userModel->save($data);
        
        return redirect()->to('/login')->with('message', 'Registration successful. Please login.');
    }
}