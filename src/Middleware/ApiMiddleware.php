<?php

namespace App\Middleware;

use App\Helpers\ResponseHelper;
use App\Controllers\DBController;
use App\Models\Token\ApiTokenModel;

class ApiMiddleware extends DBController
{

    private $db;
    private $ApiTokenModel;
    private $authHeader;


    public function __construct()
    {
        parent::__construct();
        $this->db = $this->connection();
        $this->ApiTokenModel = new ApiTokenModel($this->db);
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        $this->authHeader = $headers['authorization'] ?? '';
    }

    public function handle($callback)
    {


        // Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        if (empty($this->authHeader)) {
            return ResponseHelper::errorResponse(400, 'error',  'Missing Authorization header');
        }
        try {

            $token = trim(str_replace('Bearer', '', $this->authHeader));
            $stmt = $this->ApiTokenModel->getApiTokenByToken($token);

            if (!$stmt) {
                return ResponseHelper::errorResponse(403, 'error');
            }

            $usageCount = $stmt['usage_count'] + 1;

            $update = $this->ApiTokenModel->updateApiTokenUsageTime($stmt['owner'], $stmt['token_id'], $usageCount);

            if (!$update) {
                return ResponseHelper::errorResponse(400, 'error', 'Failed to update token usage time');
            }

            return call_user_func($callback);
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error');
        }
    }
}
