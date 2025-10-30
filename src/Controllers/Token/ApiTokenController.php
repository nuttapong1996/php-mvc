<?php

namespace App\Controllers\Token;

date_default_timezone_set('Asia/Bangkok');

use App\Helpers\ResponseHelper;
use App\Controllers\DBController;
use App\Models\Token\ApiTokenModel;
use App\Controllers\HeaderController;
use App\Controllers\User\UserController;




$root = dirname(__DIR__, 3);
require_once $root . '/vendor/autoload.php';

class ApiTokenController extends DBController
{
    private $db;
    private $ApiTokenModel;
    private $UserController;
    private $jwtPayload;




    public function __construct()
    {
        parent::__construct();
        $this->db = $this->connection();
        $this->ApiTokenModel = new ApiTokenModel($this->db);
        $this->UserController = new UserController();
        $this->jwtPayload = $_SERVER['jwt_payload'] ?? null;
    }

    public function getApiTokenByUserCode()
    {
        // set Header 
        HeaderController::setDefaultHeaders();

        //Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Jwt Payload
        if (!$this->jwtPayload) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        try {
            $username = $this->jwtPayload['sub'];

            $apiToken = $this->ApiTokenModel->getApiTokenByUserCode($username);

            if (!$apiToken) {
                return ResponseHelper::ResponseWithTitle(200, 'sucess', 'api token', 'not found');
                // return ResponseHelper::errorResponse(400, 'error');
            }

            return ResponseHelper::ResponseData(200, 'success', 'success', 'api token', $apiToken);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Token expired');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(400, 'error');
        }
    }

    public function createApiToken()
    {
        // set Header 
        HeaderController::setDefaultHeaders();

        // Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate JWT Payload
        if (!$this->jwtPayload) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        try {
            $username = $this->jwtPayload['sub'];
            $token = bin2hex(random_bytes(32));
            $tokenid = bin2hex(random_bytes(8));
            $tokenHash = password_hash($token, PASSWORD_ARGON2ID);
            $ip = $this->UserController->getUserIP();


            $stmt = $this->ApiTokenModel->insertApiToken($username, $tokenHash, $tokenid, $ip);

            if (!$stmt) {
                return ResponseHelper::errorResponse(400, 'error', 'error on create api token');
            }

            $getToken = $token;

            if (!$getToken) {
                return ResponseHelper::errorResponse(400, 'error', 'error on get api token');
            }
            return ResponseHelper::ResponseData(200, 'success', 'success', 'api token', $getToken);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(400, 'error');
        }
    }

    public function deleteApiToken()
    {
        // set Header 
        HeaderController::setDefaultHeaders();

        // Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate JWT
        if (!$this->jwtPayload) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        try {
            $username = $this->jwtPayload['sub'];
            $getid = $this->ApiTokenModel->getApiTokenByUserCode($username);

            if(!$getid){
                return ResponseHelper::errorResponse(400, 'error');
            }

            $token_id = $getid['token_id'];

            $stmt = $this->ApiTokenModel->deleteApiToken($username, $token_id);

            if(!$stmt){
                return ResponseHelper::errorResponse(400, 'error' , 'Error on delete api token');
            }

            return ResponseHelper::ResponseWithTitle(200 , 'success', 'delete api token' , 'Delete Api token successfully');

        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error' ,'Token expired');
        }catch(\Firebase\JWT\SignatureInvalidException){
            return ResponseHelper::errorResponse(401, 'error' ,'Invalid token');
        }catch(\Exception $e){
            return ResponseHelper::errorResponse(400, 'error');
        }
    }
}
