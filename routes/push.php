<?php

/** @var AltoRouter $router */

use App\Controllers\Push\PushController;

/***************************** Route Backend ************************************* */
// get Public Key
$router->map('GET', '/getpub', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new PushController();
        $controller->getPublicKey();
    });
});

// get subscription status 
$router->map('POST', '/push/get-sub', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new PushController();
        $controller->getSubByUserEndpoint();
    });
});


// add subscription (Registing to push service)
$router->map('POST', '/push/add-sub', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new PushController();
        $controller->insertSub();
    });
});

// delete subscription by user code
$router->map('POST', '/push/un-sub', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new PushController();
        $controller->deleteSubByUserEndpoint();
    });
});

// delete subscription by user code
$router->map('POST', '/push/del-sub', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new PushController();
        $controller->deleteSubByID();
    });
});

$router->map('GET', '/push/usersub-list', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new PushController();
        $controller->getUserSubTable();
    });
});

// ******* Route สำหรับ call จากนอกโปรแกรม โดยต้องใช้ API Token ที่ส่งมากับ header authorization *********
// JSON สำหรับ push
// {
//     "username" : "รหัสพนักงาน",
//     "title" : "หัวข้อ",
//     "body" : "ข้อความ",
//     "url" : "ลิงค์ URL ที่ต้องการให้ redirect"
// }
$router->map('POST', '/api/noti/push', function () use ($apiMiddleware) {
    return $apiMiddleware->handle(function () {
        $controller = new PushController();
        $controller->notiPush();
    });
});

// JSON สำหรับ push-all
// {
//     "username" : "รหัสพนักงาน",
//     "title" : "หัวข้อ",
//     "body" : "ข้อความ",
//     "url" : "ลิงค์ URL ที่ต้องการให้ redirect"
// }
$router->map('POST', '/api/noti/push-all', function () use ($apiMiddleware) {
    return $apiMiddleware->handle(function () {
        $controller = new PushController();
        $controller->notiPushAll();
    });
});
