<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UserModel;
use \Firebase\JWT\JWT;

class Auth extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            $payload = [
                'sub' => $user['id'],
                'name' => $user['nama_lengkap'],
                'iat' => time(),
                'exp' => time() + 3600, // Token berlaku selama 1 jam

            ];

            $key = getenv('JWT_SECRET');

            $token = JWT::encode($payload, $key, 'HS256');
            return $this->respond(['token' => $token]);
        } else {
            return $this->failUnauthorized('Invalid credentials');
        }
    }
}
