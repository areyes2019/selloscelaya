<?php
namespace App\Controllers\Auth;
use App\Controllers\BaseController;
use App\Models\UserModel;
use Config\Auth;
use Config\Services;

class Login extends BaseController
{
    protected $userModel;
    protected $authConfig;
    protected $session;
    protected $validation;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->authConfig = config('Auth');
        $this->session = Services::session();
        $this->validation = Services::validation();
    }
    
    public function index()
    {   
        return view('auth/login');
    }
    
    public function processLogin()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $ip = $this->request->getIPAddress();
        $cacheKey = 'login_attempts_' . md5($ip);
        $attempts = cache($cacheKey) ?? 0;

        if ($attempts >= 5) {
            return redirect()->back()->withInput()->with('error', 'Demasiados intentos fallidos. Espera 15 minutos e intenta de nuevo.');
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember') === '1';

        $user = $this->userModel->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            cache()->save($cacheKey, $attempts + 1, 900); // bloqueo 15 min
            return redirect()->back()->withInput()->with('error', 'Correo o contraseña incorrectos');
        }
        
        if (!$user['active']) {
            return redirect()->back()->withInput()->with('error', 'La cuenta no esta activa');
        }
        
        cache()->delete($cacheKey); // resetear contador al login exitoso

        $this->setUserSession($user);

        if ($remember) {
            $this->setRememberMe($user['id']);
        }

        return redirect()->to('admin');
    }
    
    protected function setUserSession($user)
    {
        $userData = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'logged_in' => true,
        ];
        
        $this->session->set($userData);
    }
    
    protected function setRememberMe($userId)
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60)); // 30 días
        
        $this->userModel->db->table('auth_tokens')->insert([
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expires,
        ]);
        
        helper('cookie');
        set_cookie('remember_me', $token, 30 * 24 * 60 * 60); // 30 días
    }
    
    public function logout()
    {
        $this->session->destroy();
        helper('cookie');
        delete_cookie('remember_me');
        
        return redirect()->to('login');
    }
    public function registro()
    {
        return view('auth/register');
    }
}

