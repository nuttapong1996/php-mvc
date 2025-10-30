<?php

namespace App\Controllers;

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class RoleController
{

    private $dotenv;

    public function __construct()
    {
        $this->dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $this->dotenv->load();
    }

    public static function getRole()
    {
        $access_token_name = $_ENV['APP_NAME'] . '_access_token';
        $access_token_cookie = trim($_COOKIE[$access_token_name] ?? '');

        if (!$access_token_cookie) {
            return null;
        }
        try {
            $decoded = JWT::decode($access_token_cookie, new Key($_ENV['SECRET_KEY'], 'HS256'));
            return $decoded->role;
        } catch (\Firebase\JWT\ExpiredException $e) {
            return null;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function checkRole(callable $callbackUser, callable $callbackAdmin)
    {
        if (RoleController::getRole() === 'u') {
            $callbackUser();
        } else if (RoleController::getRole() === 'a') {
            $callbackAdmin();
        }
    }
}
