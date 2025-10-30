<?php 
/** @var AltoRouter $router */

use App\Controllers\Sections\Leave\LeaveController;
use App\Controllers\SectionsController;

/** @var AltoRouter $router */

/***************************** FRONTEND ************************************* */

$router->map('GET', '/leave', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        SectionsController::unlock('leave','event' ,'ข้อมูลสถิติการลา','/sections/leave/leave_home.php');
    });
});

$router->map('GET', '/leave-table', function () use ($jwtWeb) {
    return $jwtWeb->handle(function () {
        SectionsController::unlock('leave','table_chart','ตารางการลา','/sections/leave/leave_table.php');
    });
});


/***************************** BACKEND ************************************* */

$router->map('GET' , '/api/leave-num/[i:year]', function ($year) use ($jwtApi){
    return $jwtApi->handle(function () use ($year) {
        $LeaveController = new LeaveController();
        return $LeaveController->sumLeave($year);
    });
});

$router->map('GET' , '/api/leave-detail/[i:year]', function ($year) use ($jwtApi){
    return $jwtApi->handle(function () use ($year) {
        $LeaveController = new LeaveController();
        return $LeaveController->leaveDetail($year);
    });
});


