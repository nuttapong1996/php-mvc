<?php 

use App\Controllers\Sections\Evaluation\EvaluationController;
use App\Controllers\SectionsController;

/** @var AltoRouter $router */

/***************************** FRONTEND ************************************* */
$router->map('GET' , '/evaluation' , function () use ($jwtWeb){
    return $jwtWeb->handle(function () {
        SectionsController::unlock('evaluation','grading','ผลการประเมินการปฏิบัติงาน','/sections/evaluation/eval_home.php');
    });
});


/***************************** BACKEND ************************************* */

$router->map('GET' , '/api/emp-score/[i:year]' , function($year) use ($jwtApi){
    return $jwtApi->handle(function () use ($year){
        $EvaluationController = new EvaluationController();
        return $EvaluationController->getEmpScore($year);
    });
});

?>