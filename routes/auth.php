<?php

use App\Controllers\Auth\AuthController;
use App\Controllers\RoutesController;
use App\Controllers\Token\TokenController;

/** @var AltoRouter $router */

/***************************** BACKEND ************************************* */

// Login route
$router->map('POST', '/auth/login', function () {
    $controller = new AuthController();
    $controller->login();
});

// Logout route
$router->map('GET', '/auth/logout', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new AuthController();
        $controller->logout();
    });
});

// Register route
$router->map('POST', '/auth/register', function () {
    $controller = new AuthController();
    $controller->register();
});

// Forgot route
$router->map('POST', '/auth/forgot', function () {
    $controller = new AuthController();
    $controller->forgot();
});

// Check token route
$router->map('POST', '/auth/checkreset', function () {
    $controller = new AuthController();
    $controller->checkResetToken();
});

// Reset route
$router->map('POST', '/auth/reset', function () {
    $controller = new AuthController();
    $controller->reset();
});

// Check current password route
$router->map('POST', '/auth/checkpass', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new AuthController();
        $controller->checkPass();
    });
});

// Change password route
$router->map('POST', '/auth/change', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new AuthController();
        $controller->changePass();
    });
});

/******************* TOKEN ************************** */
$router->map('POST', '/auth/refresh', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new TokenController();
        $controller->refreshAccessToken();
    });
});

$router->map('DELETE', '/auth/rmtoken', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new TokenController();
        $controller->deleteTokenByID();
    });
});

$router->map('GET', '/data/tokenlist', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new TokenController();
        $controller->getRefreshTokenList();
    });
});

/***************************** FRONTEND ************************************* */

$router->map('GET', '/', function () {
    $controller = new TokenController();
    if ($controller->checkToken() !== true) {        
        return RoutesController::routesAuth('/auth/login.html');
    } else {
        header('Location: home');
    }
});

$router->map('GET', '/login', function () {
    $controller = new TokenController();
    if ($controller->checkToken() !== true) {
        return RoutesController::routesAuth('/auth/login.html');
    } else {
        header('Location: home');
    }
});

$router->map('GET', '/register', function () {
    return RoutesController::routesAuth('/auth/regis.html');
});

$router->map('GET', '/forgot', function () {
    return RoutesController::routesAuth('/auth/forgot.html');
});

$router->map('GET', '/reset/[a:userCode]/[a:resetToken]', function ($userCode, $resetToken) {
    $_GET['userCode']   = $userCode;
    $_GET['resetToken'] = $resetToken;
    return RoutesController::routesAuth('/auth/reset.html');
});
