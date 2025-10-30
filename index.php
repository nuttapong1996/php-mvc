<?php

use Dotenv\Dotenv;
use AltoRouter as Router;
use App\Middleware\ApiMiddleware;
use App\Middleware\JwtMiddlewareAPI;
use App\Middleware\JwtMiddlewareWeb;

require_once 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$router = new Router();
$router->setBasePath($_ENV['BASE_PATH']);

$access_token_name = $_ENV['APP_NAME'] . '_access_token';
$refresh_token_name = $_ENV['APP_NAME'] . '_refresh_token';
$csrf_token_name = $_ENV['APP_NAME'] . '_csrf';
$secret            = $_ENV['SECRET_KEY'];


ini_set('session.gc_maxlifetime', 1440);
ini_set('session.cookie_httponly', 1);
session_set_cookie_params([
    'lifetime' => 0, // 0 = session cookie (ปิด browser แล้วหาย)
    'path'     => '/',
    'domain'   => '',       // หรือปล่อยว่างให้ใช้ domain ปัจจุบัน
    'secure'   => true,     // true = ส่ง cookie เฉพาะผ่าน HTTPS
    'httponly' => true,     // ปิดการเข้าถึงจาก JS
    'samesite' => 'Strict', // ป้องกัน CSRF จาก cross-site
]);

session_start();

// Middleware for Web
$jwtWeb = new JwtMiddlewareWeb($access_token_name, $secret);
// Middleware for API
$jwtApi = new JwtMiddlewareAPI($access_token_name, $csrf_token_name, $secret);

// ApiMiddleware for internal API
$apiMiddleware = new ApiMiddleware();


require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/user.php';
require_once __DIR__ . '/routes/web.php';
require_once __DIR__ . '/routes/push.php';
require_once __DIR__ . '/routes/token.php';
require_once __DIR__ . '/routes/leave.php';
require_once __DIR__ . '/routes/eval.php';


// ตรวจสอบและรัน route
$match = $router->match();
if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
}

ini_set('display_errors', 0);
error_reporting(0);
