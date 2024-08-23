<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use \Firebase\JWT\JWT;
use \Exception;
use CodeIgniter\Config\Services;
use Firebase\JWT\Key;

class JWTAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Mengambil header Authorization
        $headers = $request->getServer('HTTP_AUTHORIZATION');

        if ($headers) {
            $token = str_replace('Bearer ', '', $headers);
        } else {
            return service('response')->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'Token not provided');
        }

        if (empty($token)) {
            return service('response')->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'Token not provided');
        }

        try {
            $key = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
        } catch (Exception $e) {
            return Services::response()->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED, 'Invalid token');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
