<?php
namespace App\Controllers;

class RoutesController
{
    public static function routes($path)
    {
        $basePath = dirname(__DIR__, 2) . '/views';
        require $basePath . '/components/header.php';
        require $basePath .'/' .$path;
        require $basePath . '/components/footer.php';
    }

    public static function routesAuth($path)
    {
        $basePath = dirname(__DIR__, 2) . '/views';
        require $basePath . '/components/header.php';
        require $basePath .'/' .$path;
        require $basePath . '/components/footer_login.php';
    }
}
