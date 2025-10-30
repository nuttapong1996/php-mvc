<?php

/** @var AltoRouter $router */

use App\Controllers\RoleController;
use App\Controllers\SectionsController;
use App\Controllers\RoutesController;

/*****************************Route Backend ************************************* */

// route section ต่างๆ
$router->map('POST', '/[a:section]', function ($section) use ($jwtApi) {
    return $jwtApi->handle(function () use ($section) {
        $SectionsController = new SectionsController;
        $SectionsController->validate($section);
    });
});

/*****************************Route Frontend ************************************* */
$router->map('GET', '/home', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {

        // ตรวจสอบว่ามี intended_url เก็บไว้หรือเปล่า กรณที่มีการ push มาจาก Webpush
        if (! empty($_SESSION['intended_url'])) {
            // สร้างตัวแปร $url ดึง $_SESSION['intended_url'] มาจาก JwtMiddleware
            $url = $_SESSION['intended_url'];
            // ทำการเคีลยร์ session intended url อันเก่า
            unset($_SESSION['intended_url']);
            header("Location: $url");
            exit;
        }

        // เคลียร์ unlocked sections ถ้าเข้า home ปกติ
        unset($_SESSION['unlocked_sections']);
        // ทำการเคีลยร์ session intended url อันเก่า
        unset($_SESSION['intended_url']);

        return RoutesController::routes('/main.php');
    });
});

$router->map('GET', '/settings', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        RoleController::checkRole(
            function () {
                SectionsController::unlock('settings', 'settings', 'การตั้งค่า', '/settings/settings.php');
            },
            function () {
                SectionsController::unlock('settings', 'settings', 'การตั้งค่า', '/settings/settings-admin.php');
            }
        );
    });
});

$router->map('GET', '/settings/api-token', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        RoleController::checkRole(
            function(){
                RoutesController::routes('/main.php');
            }, 
            function(){
                SectionsController::unlock('settings', 'settings', 'การตั้งค่า', '/settings/api-token.php');
            }
        );
    });
});

$router->map('GET', '/settings/noti', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        SectionsController::unlock('settings', 'notifications', 'การแจ้งเตือน', '/settings/sub_list.php');
    });
});

$router->map('GET', '/settings/password', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        SectionsController::unlock('settings', 'password', 'เปลี่ยนรหัสผ่าน', '/settings/change.php');
    });
});

$router->map('GET', '/settings/token', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        SectionsController::unlock('settings', 'devices_other', 'กิจกรรมการใช้งาน', '/settings/token.php');
    });
});




$router->map('GET', '/menu1', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        SectionsController::unlock('menu1' ,'menu' ,'Menu 1' ,'/sections/menu1/index.php');
    });
});
$router->map('GET', '/menu2', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        SectionsController::unlock('menu2' ,'menu' ,'Menu 2' ,'/sections/menu2/index.php');
    });
});
$router->map('GET', '/menu3', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        SectionsController::unlock('menu3' ,'menu' ,'Menu 3' ,'/sections/menu3/index.php');
    });
});


/*********************** Route section ****************************/
