<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use Config\Auth;
use Config\Services;

class ForgotPassword extends BaseController
{
    protected $userModel;
    protected $authConfig;
    protected $email;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->authConfig = config('Auth');
        $this->email = Services::email();
    }
    
    public function index()
    {
        if ($this->request->getMethod() === 'post') {
            return $this->processForgotPassword();
        }
        
        return view('auth/forgot_password');
    }
    
    protected function processForgotPassword()
    {
        $rules = ['email' => 'required|valid_email'];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $email = $this->request->getPost('email');
        $user = $this->userModel->getUserByEmail($email);
        
        if (!$user) {
            return redirect()->back()->with('error', 'If the email exists, a password reset link has been sent.');
        }
        
        $token = $this->userModel->createPasswordResetToken($email);
        
        if ($token) {
            $this->sendResetEmail($email, $token);
        }
        
        return redirect()->back()->with('message', 'If the email exists, a password reset link has been sent.');
    }
    
    protected function sendResetEmail($email, $token)
    {
        $resetLink = site_url("reset-password?token=$token");
        
        $this->email->setTo($email);
        $this->email->setSubject('Password Reset Request');
        $this->email->setMessage(view('auth/email/reset_password', [
            'resetLink' => $resetLink,
            'expiration' => $this->authConfig->passwordResetExpire / 3600, // horas
        ]));
        
        $this->email->send();
    }
    
    public function resetPassword()
    {
        $token = $this->request->getGet('token');
        
        if (!$token) {
            return redirect()->to('forgot-password')->with('error', 'Invalid password reset token');
        }
        
        $user = $this->userModel->getUserByToken($token);
        
        if (!$user) {
            return redirect()->to('forgot-password')->with('error', 'Invalid or expired password reset token');
        }
        
        if ($this->request->getMethod() === 'post') {
            return $this->processResetPassword($token);
        }
        
        return view('auth/reset_password', ['token' => $token]);
    }
    
    protected function processResetPassword($token)
    {
        $rules = [
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $password = $this->request->getPost('password');
        
        if ($this->userModel->resetPassword($token, $password)) {
            return redirect()->to('login')->with('message', 'Password reset successfully. Please login with your new password.');
        }
        
        return redirect()->to('forgot-password')->with('error', 'Unable to reset password. Please try again.');
    }
}