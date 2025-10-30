<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddlewareWeb
{
    private $secret_key;
    private $access_token_name;

    public function __construct($access_token_name, $secret_key)
    {
        $this->secret_key        = $secret_key;
        $this->access_token_name = $access_token_name;
    }

    public function handle($callback)
    {
        //ดึง token จาก cookie
        $access_token = $_COOKIE[$this->access_token_name] ?? '';

        if (empty($access_token)) {
            $this->redirectToLogin();
        }
        try {
            JWT::decode($access_token, new Key($this->secret_key, 'HS256'));
            $callback();
        } catch (\Firebase\JWT\ExpiredException $e) {
            $this->redirectToLogin();
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            $this->redirectToLogin();
        } catch (\Exception $e) {
            $this->redirectToLogin();
        }
    }

    private function redirectToLogin()
    {
        $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
        header('Location: ./');
        exit;
    }
}
