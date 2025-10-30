<?php

/** @var AltoRouter $router */

use App\Controllers\RoleController;
use App\Controllers\RoutesController;
use App\Controllers\SectionsController;
use App\Controllers\User\UserController;

/***************************** Route Backend ************************************* */
// Route get current user
$router->map('GET', '/user/me', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new UserController();
        $controller->getProfile();
    });
});

$router->map('GET', '/user/pic', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new UserController();
        $controller->getEmpImg();
    });
});

$router->map('GET', '/user/medinfo', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new UserController();
        $controller->getEmpMedInfo();
    });
});

$router->map('GET', '/user/warninfo', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new UserController();
        $controller->getEmpwarnInfo();
    });
});


/***************************** Route Frontend ************************************* */

$router->map('GET', '/personal', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        session_destroy();
        return RoutesController::routes('/user/personal.php');
        // SectionsController::unlock('personal', 'account_box', 'ข้อมูลส่วนตัว', '/user/personal.php');
    });
});

$router->map('GET', '/medinfo', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        return RoutesController::routes('/user/medinfo.php');
        // SectionsController::unlock('personal', 'medical_information', 'ข้อมูลทางการแพทย์', '/user/medinfo.php');
    });
});

$router->map('GET', '/warninfo', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        SectionsController::unlock('warninfo', 'assignment_late', 'ข้อมูลใบเตือน', '/user/warninfo.php');
    });
});

// Route get user by usercode
// $router->map('GET', '/user/profile/[i:usercode]', function ($usercode) use ($jwtApi) {
//     return $jwtApi->handle(function () use ($usercode) {
//         $_GET['usercode'] = $usercode;
//         return require __DIR__ . '/../api/user/profile_id.php';
//     });
// });
