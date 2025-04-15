<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Auth;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'username', 'email', 'password', 'reset_token', 
        'reset_expires', 'active', 'created_at', 'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
    
    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
    
    public function getUserByToken($token)
    {
        return $this->where('reset_token', $token)
                    ->where('reset_expires >', date('Y-m-d H:i:s'))
                    ->first();
    }
    
    public function createPasswordResetToken($email)
    {
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + config('Auth')->passwordResetExpire);
        
        $this->update($user['id'], [
            'reset_token' => $token,
            'reset_expires' => $expires,
        ]);
        
        return $token;
    }
    
    public function resetPassword($token, $password)
    {
        $user = $this->getUserByToken($token);
        
        if (!$user) {
            return false;
        }
        
        $this->update($user['id'], [
            'password' => $password,
            'reset_token' => null,
            'reset_expires' => null,
        ]);
        
        return true;
    }
}