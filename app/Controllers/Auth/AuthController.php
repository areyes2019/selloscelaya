<?php
namespace App\Controllers\Auth;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth/login');
    }

    public function loginPost()
    {
        $session = session();
        $userModel = new UserModel();
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['active']) {
                return redirect()->back()->with('error', 'Tu cuenta está inactiva.');
            }

            $session->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'isLoggedIn' => true
            ]);
            return redirect()->to('/dashboard');
        }

        return redirect()->back()->with('error', 'Credenciales inválidas.');
    }

    public function register()
    {
        return view('auth/register');
    }

    public function registerPost()
    {
        $userModel = new UserModel();

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'active' => 1
        ];

        $userModel->insert($data);
        return redirect()->to('/login')->with('success', 'Registro exitoso. Ahora puedes iniciar sesión.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function dashboard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }
        return view('dashboard');
    }

    // Vista para pedir correo de recuperación
    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }

    public function forgotPasswordPost()
    {
        $email = $this->request->getPost('email');
        $userModel = new UserModel();
        $token = $userModel->createPasswordResetToken($email);

        if ($token) {
            // Enviar por correo en producción
            return redirect()->to('/login')->with('success', 'Revisa tu correo para restablecer tu contraseña. (Token: ' . $token . ')');
        }

        return redirect()->back()->with('error', 'Correo no encontrado.');
    }

    public function resetPassword($token)
    {
        return view('auth/reset_password', ['token' => $token]);
    }

    public function resetPasswordPost()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();

        if ($userModel->resetPassword($token, $password)) {
            return redirect()->to('/login')->with('success', 'Contraseña actualizada.');
        }

        return redirect()->back()->with('error', 'Token inválido o expirado.');
    }
}
