<?php

/** @var AltoRouter $router */

use App\Controllers\Token\ApiTokenController;

$router->map('GET', '/token/gettoken', function () use ($jwtApi) {
    return $jwtApi->handle(function () {
        $controller = new ApiTokenController();
        $controller->getApiTokenByUserCode();
    });
});

$router->map('POST' , '/token/create' , function() use ($jwtApi){
    return $jwtApi->handle(function(){
        $controller = new ApiTokenController();
        $controller->createApiToken();
    });
});

$router->map('DELETE' , '/token/delete' , function() use ($jwtApi){
    return $jwtApi->handle(function(){
        $controller  = new ApiTokenController();
        $controller->deleteApiToken();
    });
});



