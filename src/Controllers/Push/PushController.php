<?php

namespace App\Controllers\Push;

use PDO;
use PDOException;
use Dotenv\Dotenv;
use App\Models\Push\PushModel;
use App\Helpers\ResponseHelper;
use Minishlink\WebPush\WebPush;
use App\Controllers\DBController;
use Minishlink\WebPush\Subscription;
use App\Controllers\HeaderController;
use App\Controllers\User\UserController;

$root = dirname(__DIR__, 3);
require_once $root . '/vendor/autoload.php';

class PushController extends DBController
{
    private $db;
    private $input;
    private $jwtPayload;

    private $privateKey;
    private $publicKey;


    // Model
    private $PushModel;

    // Controller
    private $UserController;

    public function __construct()
    {
        parent::__construct();
        $this->db        = $this->connection();
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 3));
        $dotenv->load();
        $this->input = json_decode(file_get_contents('php://input'), true);
        $this->jwtPayload = $_SERVER['jwt_payload'] ?? null;
        $this->privateKey = $_ENV['VAPID_PRIVATE_KEY'];
        $this->publicKey = $_ENV['VAPID_PUBLIC_KEY'];

        $this->PushModel = new PushModel($this->db);

        $this->UserController = new UserController();
    }

    // Function get Public Key.
    public function getPublicKey()
    {
        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        if (!$this->jwtPayload) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        echo json_encode([
            "publicKey"  => $_ENV['VAPID_PUBLIC_KEY'],
        ]);

        return;
    }
    // Function insert or create subscription in Database.(Sub)
    public function insertSub()
    {

        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Input 
        if (!$this->input['endpoint'] && !$this->input['keys']['p256dh'] && !$this->input['keys']['auth']) {
            return ResponseHelper::errorResponse(400, 'error', 'Missing required parameters');
        }

        // Validate JWT Payload
        if (!$this->jwtPayload) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        }

        try {
            $subcode = bin2hex(random_bytes(16));
            $username = $this->jwtPayload['sub'];
            $empDevice = $this->UserController->getUserDeviceType();
            $empIp = $this->UserController->getUserIP();
            $endPoint = $this->input['endpoint'];
            $publicKey = $this->input['keys']['p256dh'];
            $authKey = $this->input['keys']['auth'];

            $addSub = $this->PushModel->insertSub($username, $empDevice, $empIp, $subcode, $endPoint, $publicKey, $authKey);

            if ($addSub) {
                return ResponseHelper::ResponseWithTitle(200, 'success', 'การแจ้งเตือน', 'ลงทะเบียนการแจ้งเตือนสำเร็จ');
            } else {
                return ResponseHelper::ResponseWithTitle(400, 'error', 'การแจ้งเตือน', 'ลงทะเบียนการแจ้งเตือนไม่สำเร็จ');
            }
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Token expired');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error', 'Internal server error');
        }
    }
    // Function get subscription from Database.(Get sub By Endpoint)
    public function getSubByUserEndpoint()
    {
        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Input 
        if (!$this->input['endpoint']) {
            return ResponseHelper::errorResponse(400, 'error', 'Endpoint is required');
        }

        // Validate JWT Payload
        if (!$this->jwtPayload) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        }

        try {
            $username = $this->jwtPayload['sub'];
            $endPoint = $this->input['endpoint'];

            $getSub = $this->PushModel->getSubByUserEndpoint($username, $endPoint);

            if (!$getSub) {
                return ResponseHelper::ResponseWithTitle(
                    200,
                    'unsub',
                    'การแจ้งเตือน',
                    'ยังไม่ได้ลงทะเบียนการแจ้งเตือน'
                );
            }

            return ResponseHelper::ResponseWithTitle(
                200,
                'sub',
                'การแจ้งเตือน',
                'ลงทะเบียนการแจ้งเตือนแล้ว'
            );
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Token expired');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error', 'Internal server error');
        }
    }
    // Function delete subscription from Database.(Unsub)
    public function deleteSubByUserEndpoint()
    {
        // set Header
        HeaderController::setDefaultHeaders();
        // Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Input 
        if (!$this->input['endpoint']) {
            return ResponseHelper::errorResponse(400, 'error', 'Endpoint is required');
        }

        // Validate Jwt Payload
        if (!$this->jwtPayload) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        }

        try {
            $username = $this->jwtPayload['sub'];
            $endPoint = $this->input['endpoint'];

            $deleteSub = $this->PushModel->deleteSubByUserEndpoint($username, $endPoint);

            if (!$deleteSub) {
                return ResponseHelper::errorResponse(400, 'error', 'Failed to delete subscription');
            }

            return ResponseHelper::ResponseWithTitle(200, 'success', 'การแจ้งเตือน', 'ลบการแจ้งเตือนสำเร็จ');
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Token expired');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error', 'Internal server error');
        }
    }
    // Function delete subscription from Database By sub_id
    public function deleteSubByID()
    {
        // set Header
        HeaderController::setDefaultHeaders();
        // Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Input 
        if (!$this->input['subCode']) {
            return ResponseHelper::errorResponse(400, 'error', 'Endpoint is required');
        }

        // Validate Jwt Payload
        if (!$this->jwtPayload) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        }

        try {
            $username = $this->jwtPayload['sub'];
            $subCode = $this->input['subCode'];

            $deleteSub = $this->PushModel->deleteSubBySubCode($username, $subCode);

            if (!$deleteSub) {
                return ResponseHelper::errorResponse(400, 'error', 'Failed to delete subscription');
            }

            return ResponseHelper::ResponseWithTitle(200, 'success', 'การแจ้งเตือน', 'ลบการแจ้งเตือนสำเร็จ');
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Token expired');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error', 'Internal server error');
        }
    }
    // Fuction for Table user subscription
    public function getUserSubTable()
    {
        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Request Method
        if (!$_SERVER['REQUEST_METHOD'] == 'GET') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Jwt Payload
        if (!$this->jwtPayload) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        try {
            $username = $this->jwtPayload['sub'];

            $subTable = $this->PushModel->getSubListByUserCode($username);

            if ($subTable === false) {
                return ResponseHelper::errorResponse(204, 'error');
            }

            return ResponseHelper::ResponseDataArray(200, 'success', 'Subscription list', 'Subscription data', $subTable);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Token expired');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error', 'Internal server error');
        }
    }

    public function notiPush()
    {
        // set Header 
        HeaderController::setDefaultHeaders();

        // Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Input 
        if (
            !isset($this->input['username']) ||
            !isset($this->input['title']) ||
            !isset($this->input['body']) ||
            !isset($this->input['url'])
        ) {
            return ResponseHelper::errorResponse(400, 'error', 'Missing required parameters');
        }

        $username = $this->input['username'];
        $title = $this->input['title'];
        $body = $this->input['body'];
        $url = $this->input['url'];

        try {
            $stmt = $this->PushModel->getSubListByUserCode($username);

            // กำหนดตัวแปร auth สำหรับใช้ในการ ส่งแจ้งเตือน
            $auth = [
                'VAPID' => [
                    'subject'    => 'mailto:me@website.com', // can be a mailto: or your website address
                    'publicKey'  => trim($this->publicKey),
                    'privateKey' => trim($this->privateKey),
                ],
            ];

            // กำหนดตัวแปร message สำหรับใช้ในการ ส่งแจ้งเตือนโดยกําหนดค่า title , body , url จาก form
            $message = [
                'title' => $title,
                'body'  => $body,
                'url'   => $url,
            ];

            // สร้างตัวแปร webPush เพื่อใช้ส่งแจ้งเตือน
            $webPush = new WebPush($auth);

            // วนลูปเพื่อส่งแจ้งเตือนให้แต่ละ endpoint
            foreach ($stmt as $endpoint) {
                $subscription = Subscription::create([
                    'endpoint' => $endpoint['endpoint'],
                    'keys'     => [
                        'p256dh' => $endpoint['pub_key'],
                        'auth'   => $endpoint['auth_key'],
                    ],
                ]);
                $webPush->queueNotification(
                    $subscription,
                    json_encode($message)
                );
            }

            // วนลูปเพื่อส่งแจ้งเตือน
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    return ResponseHelper::ResponseWithTitle(200, 'success', 'การแจ้งเตือน', 'ส่งแจ้งเตือนให้กับ ' . $username . 'สำเร็จ');
                } else {

                    return ResponseHelper::ResponseWithTitle(400, 'error', 'การแจ้งเตือน', 'ส่งแจ้งเตือนไม่สำเร็จ : ' . $report->getReason());

                    // ถ้า subscription หมดอายุ → ลบออกจาก DB
                    if ($report->isSubscriptionExpired()) {
                        $PushModel->deleteSubByEndpoint($endpoint['endpoint']);
                    }
                }
            }
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error', 'Internal server error');
        }
    }

    public function notiPushAll()
    {
        // set Header 
        HeaderController::setDefaultHeaders();

        // Validate Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Input 
        if (
            !isset($this->input['title']) ||
            !isset($this->input['body']) ||
            !isset($this->input['url'])
        ) {
            return ResponseHelper::errorResponse(400, 'error', 'Endpoint is required');
        }

        $title = $this->input['title'];
        $body = $this->input['body'];
        $url = $this->input['url'];

        try {
            $stmt = $this->PushModel->getAllSub();

            // กำหนดตัวแปร auth สำหรับใช้ในการ ส่งแจ้งเตือน
            $auth = [
                'VAPID' => [
                    'subject'    => 'mailto:me@website.com', // can be a mailto: or your website address
                    'publicKey'  => trim($this->publicKey),
                    'privateKey' => trim($this->privateKey),
                ],
            ];

            // กำหนดตัวแปร message สำหรับใช้ในการ ส่งแจ้งเตือนโดยกําหนดค่า title , body , url จาก form
            $message = [
                'title' => $title,
                'body'  => $body,
                'url'   => $url,
            ];

            // สร้างตัวแปร webPush เพื่อใช้ส่งแจ้งเตือน
            $webPush = new WebPush($auth);

            // วนลูปเพื่อส่งแจ้งเตือนให้แต่ละ endpoint
            foreach ($stmt as $endpoint) {
                $subscription = Subscription::create([
                    'endpoint' => $endpoint['endpoint'],
                    'keys'     => [
                        'p256dh' => $endpoint['pub_key'],
                        'auth'   => $endpoint['auth_key'],
                    ],
                ]);
                $webPush->queueNotification(
                    $subscription,
                    json_encode($message)
                );
            }

            // วนลูปเพื่อส่งแจ้งเตือน
            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                if ($report->isSuccess()) {
                    return ResponseHelper::ResponseWithTitle(200, 'success', 'การแจ้งเตือน', 'ส่งแจ้งเตือนให้กับ ' . count($stmt) . ' คน สำเร็จ');
                } else {

                    return ResponseHelper::ResponseWithTitle(400, 'error', 'การแจ้งเตือน', 'ส่งแจ้งเตือนไม่สำเร็จ : ' . $report->getReason());

                    // ถ้า subscription หมดอายุ → ลบออกจาก DB
                    if ($report->isSubscriptionExpired()) {
                        $PushModel->deleteSubByEndpoint($endpoint['endpoint']);
                    }
                }
            }
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error', 'Internal server error');
        }
    }
}
