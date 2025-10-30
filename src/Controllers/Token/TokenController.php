<?php

namespace App\Controllers\Token;

date_default_timezone_set('Asia/Bangkok');

use App\Controllers\DBController;
use App\Controllers\HeaderController;
use App\Controllers\User\UserController;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Models\Token\TokenModel;
use App\Models\User\UserModel;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;
use PDOException;

$root = dirname(__DIR__, 3);
require_once $root . '/vendor/autoload.php';

class TokenController extends DBController
{
    private $db;
    private $TokenModel;
    private $UserController;
    private $dotenv;
    private $jwt_payload;

    private $input;
    private $secret_key;
    private $domain;
    private $app_name;

    private $refresh_token_name;
    private $access_token_name;
    private $csrf_token_name;

    private $issued_at;
    private $refresh_lifetime_s;
    private $access_lifetime_s;
    private $refresh_token_expire;
    private $access_token_expire;
    private $refresh_token_cookie;
    private $access_token_cookie;
    private $set_csrf_token;
    private $UserIP;
    private $UserDeviceType;

    public function __construct()
    {
        parent::__construct();
        $this->db     = $this->connection();
        $this->dotenv = Dotenv::createImmutable(dirname(__DIR__, 3));
        $this->dotenv->load();
        $this->jwt_payload = $_SERVER['jwt_payload'] ?? null;

        $this->input = json_decode(file_get_contents('php://input'), true);
        // Model
        $this->TokenModel = new TokenModel($this->db);
        // Controller
        $this->UserController = new UserController();

        // Config
        $this->secret_key = $_ENV['SECRET_KEY'];
        $this->domain     = $_ENV['APP_DOMAIN'];
        $this->app_name   = $_ENV['APP_NAME'];

        $this->refresh_token_name = $this->app_name . '_refresh_token';
        $this->access_token_name  = $this->app_name . '_access_token';
        $this->csrf_token_name    = $this->app_name . '_csrf';

        $this->issued_at            = time();
        $this->refresh_lifetime_s   = 60 * 60 * 24 * 7; // 7 วัน
        $this->access_lifetime_s    = 60 * 15;          // 15 นาที
        $this->refresh_token_expire = $this->issued_at + $this->refresh_lifetime_s;
        $this->access_token_expire  = $this->issued_at + $this->access_lifetime_s;
        $this->refresh_token_cookie = trim($_COOKIE[$this->refresh_token_name] ?? '');
        $this->access_token_cookie  = trim($_COOKIE[$this->access_token_name] ?? '');
        $this->set_csrf_token       = bin2hex(random_bytes(32));
        $this->UserIP               = $this->UserController->getUserIP();
        $this->UserDeviceType       = $this->UserController->getUserDeviceType();
    }

    // ฟังก์ชั่นสำหรับสร้าง Access Token ใหม่
    private function createNewAccessToken($empCode, $role, $tokenId)
    {
        // สร้าง Acccess Token Payload
        $access_token_payload = TokenHelper::accessTokenPayload(
            $this->domain,
            $empCode,
            $role,
            $this->issued_at,
            $this->access_token_expire,
            $tokenId
        );

        // ทำการเข้ารหัส Access Token payload 
        $access_token  = JWT::encode($access_token_payload, $this->secret_key, 'HS256');

        //  ออก Cookie Access Token ใหม่
        setcookie($this->access_token_name, $access_token, TokenHelper::cookieOpts($this->access_token_expire, null, true));
        setcookie($this->csrf_token_name, $this->set_csrf_token, TokenHelper::cookieOpts($this->access_token_expire, null, false));
    }

    // ฟังก์ชั่นสำหรับสร้าง Refresh Token ใหม่
    private function createNewRefreshToken($empCode, $role, $tokenId)
    {
        // สร้าง Refresh Token Payload
        $refresh_token_payload = TokenHelper::refreshTokenPayload(
            $this->domain,
            $empCode,
            $role,
            $this->issued_at,
            $this->refresh_token_expire,
            $tokenId
        );

        // ทำการเข้ารหัส Refresh Token payload
        $refresh_token = JWT::encode($refresh_token_payload, $this->secret_key, 'HS256');

        // ทำการเข้ารหัส Refresh Token เพื่อเก็บใน DB
        $refresh_token_hash = password_hash($refresh_token, PASSWORD_ARGON2ID);

        // อัปเดต Refresh token ใหม่ใน DB
        $this->TokenModel->updateToken(
            $empCode,
            $tokenId,
            $refresh_token_hash,
            $this->UserDeviceType,
            $this->UserIP,
            date('Y-m-d H:i:s', $this->refresh_token_expire)
        );

        //  ออก Cookie Refresh Token ใหม่
        setcookie($this->refresh_token_name, $refresh_token, TokenHelper::cookieOpts($this->refresh_token_expire, null, true));
    }

    // ฟังก์ชั่นสำหรับตรวจสอบ คุกกี้ Refresh Token กับ DB
    private function verifyRefreshToken($refreshTokenCookie)
    {
        try {
            // Decode refresh token
            $decode = JWT::decode($refreshTokenCookie, new Key($this->secret_key, 'HS256'));

            $empCode = $decode->sub ?? null;
            $role = $decode->role ?? null;
            $tokenId = $decode->jti ?? null;

            if (!$empCode || !$tokenId) {
                return ['status' => false];
            }

            // ดึง refresh token จากฐานข้อมูล
            $refreshTokenDB = $this->TokenModel->getRefreshTokenByID($empCode, $tokenId);

            if (!$refreshTokenDB) {
                return ['status' => false];
            }

            $refreshTokenResult = $refreshTokenDB->fetch(PDO::FETCH_ASSOC);
            if (!$refreshTokenResult || !isset($refreshTokenResult['token'])) {
                return ['status' => false];
            }

            // ตรวจสอบว่าค่า refresh token ใน cookie ตรงกับ hash ใน DB หรือไม่
            if (!password_verify($refreshTokenCookie, $refreshTokenResult['token'])) {
                return ['status' => false];
            }

            return [
                'status'  => true,
                'empCode' => $empCode,
                'role'    => $role,
                'tokenId' => $tokenId
            ];
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ['status' => false];
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ['status' => false];
        } catch (\Exception $e) {
            return ['status' => false];
        }
    }

    // ฟังก์ชั่นสำหรับลบ Cookie ทั้งหมด
    private function clearCookie()
    {
        // ตรวจสอบว่ามี header ส่งไปแล้วหรือยัง
        if (headers_sent()) {
            return false; // ป้องกัน warning ถ้ามี output ก่อนหน้า
        }

        // ลบ Cookies ทั้งหมด (token + csrf)
        $cookies = [
            [$this->access_token_name, true, true],
            [$this->refresh_token_name, true, true],
            [$this->csrf_token_name, false, false],
        ];

        foreach ($cookies as [$name, $secure, $httpOnly]) {
            setcookie($name, '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => $secure,
                'httponly' => $httpOnly,
                'samesite' => 'strict', // ปลอดภัยขึ้น
            ]);
        }

        // จัดการ Session
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            session_destroy();
        }

        return false;
    }

    // ฟังก์ชั่นภายใน สำหรับออก Access / Refresh Token ใหม่ เมื่อ Access Token หมดอายุ
    private function renewTokenIfValid()
    {
        if (empty($this->refresh_token_cookie)) {
            return $this->clearCookie();
        }

        $result = $this->verifyRefreshToken($this->refresh_token_cookie);
        if ($result['status'] === true) {
            $this->createNewAccessToken($result['empCode'], $result['role'], $result['tokenId']);
            $this->createNewRefreshToken($result['empCode'], $result['role'], $result['tokenId']);
            return true;
        }

        return $this->clearCookie();
    }

    // ฟังก์ชันตรวจสอบ Acess Token เพื่อทำการอกก Access / Refresh Token ใหม่ เมื่อ Access Token หมดอายุ
    public function checkToken()
    {
        // กรณีที่ Accces Token หมดอายุ
        if (empty($this->access_token_cookie)) {
            // ให้เช็คและ สร้าง Access / Refresh Token ใหม่
            return $this->renewTokenIfValid();
        }

        // กรณีที่ยังมี Access Token 
        try {
            // 1.ทำการเช็คและถอดรหัส Access Token
            JWT::decode($this->access_token_cookie, new Key($this->secret_key, 'HS256'));
            // 2.ทำการเช็ค Cookie Refresh token กับ DB
            if ($this->verifyRefreshToken($this->refresh_token_cookie)['status'] === true) {
                //ผ่านได้  
                return true;
            }
            // หากไม่ผ่าน หรือ Refresh Token ไม่ถูกต้อง ให้ลบ Cookie ทั้งหมด
            return $this->clearCookie();
        } catch (\Exception $e) {
            // หากไม่มี Access Token ให้เช็คและ สร้าง Access / Refresh Token ใหม่
            return $this->renewTokenIfValid();
        }
    }

    // ฟังก์ชั่นสำหรับต่ออายุ Access Token
    public function refreshAccessToken()
    {
        // กำหนด Header
        HeaderController::setDefaultHeaders();

        // ตรวจสอบ Request Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // ตรวจสอบ JWT Payload ที่ส่งมาจาก Middleware
        if (! $this->jwt_payload) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        // ตรวจสอบ Cookie Refresh Token ว่ามีหรือไม่
        if (empty($this->refresh_token_cookie)) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        try {

            // กำหนดตัวแปรให้กับค่าที่ได้จาก JWT Payload
            $empCode = $this->jwt_payload['sub'];
            $role = $this->jwt_payload['role'];
            $tokenId = $this->jwt_payload['jti'];

            // ดึง Refresh Token จากฐานข้อมูล
            $refreshTokenDB = $this->TokenModel->getRefreshTokenByID($empCode, $tokenId);

            // ตรวจสอบว่ามี Refresh Token จากฐานข้อมูล หรือไม่
            if (! $refreshTokenDB) {
                return ResponseHelper::errorResponse(400, 'error');
            }

            // ทำการ fetch Refresh Token ที่ถูกดึงจากฐานข้อมูล
            $refreshTokenResult = $refreshTokenDB->fetch(PDO::FETCH_ASSOC);

            // ตรวจสอบว่าค่า refresh token ใน cookie ตรงกับ hash ใน DB หรือไม่
            if (! password_verify($this->refresh_token_cookie, $refreshTokenResult['token'])) {
                return ResponseHelper::errorResponse(401, 'error');
            }

            // ทำการสร้าง Access Token ใหม่
            $this->createNewAccessToken($empCode, $role, $tokenId);

            // return ค่ากลับไปว่าสร้าง Access Token สำเร็จ
            return ResponseHelper::ResponseCsrf(200, 'success', 'Token refreshed', 'Token refreshed successfully', $this->set_csrf_token);

            // $access_token = JWT::encode(TokenHelper::accessTokenPayload($this->domain, $empCode, $role, $this->issued_at, $this->access_token_expire, $tokenId), $this->secret_key, 'HS256');

            // // Store Access token in Cookie HttpOnly with secure
            // setcookie($this->access_token_name, $access_token, TokenHelper::cookieOpts($this->access_token_expire, null, true));

            // // Store CSRF token in Cookie secure without HttpOnly
            // setcookie($this->csrf_token_name, $this->set_csrf_token, TokenHelper::cookieOpts($this->access_token_expire, null, false));

        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (\PDOException $e) {
            return ResponseHelper::errorResponse(400, 'error');
        }
    }

    // ฟังก์ชั่นสำหรับเรียกรายการ Token ทั้งหมด
    public function getRefreshTokenList()
    {
        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Method
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        try {
            $empCode = $this->jwt_payload['sub'];
            $tokenId = $this->jwt_payload['jti'];
            $stmt    = $this->TokenModel->getRefreshTokenList($empCode);

            if ($stmt->rowCount() == 0) {
                return ResponseHelper::errorResponse(404, 'error');
            }

            while ($result = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
                foreach ($result as $row) {
                    $row['device_name'] = $row['device_name'];
                    $row['ip_address']  = $row['ip_address'];
                    $row['expires_at']  = $row['expires_at'];
                    if ($tokenId == $row['token_id']) {
                        $row['remark'] = '(Current Device)';
                    }
                    $arr['response'][] = $row;
                }
            }

            return ResponseHelper::ResponseDataArray(200, 'success', 'Success', $stmt->rowCount() . ' records', $arr['response']);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(400, 'error');
        }
    }

    // ฟังก์ชั่นสำหรับลบ Token จาก TokenID
    public function deleteTokenByID()
    {
        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Method
        if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Vaidate Input
        if (empty($this->input['tokenid']) || ! isset($this->input['tokenid'])) {
            return ResponseHelper::errorResponse(400, 'error',);
        }

        try {
            $empCode = $this->jwt_payload['sub'];
            $tokenid = trim($this->input['tokenid']);
            $stmt    = $this->TokenModel->deleteTokenByID($empCode, $tokenid);

            if (! $stmt || $stmt->rowCount() == 0) {
                return ResponseHelper::errorResponse(400, 'error');
            }
            return ResponseHelper::ResponseWithTitle(200, 'success', 'Token deleted!', 'You have successfully delete the device token.');
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(400, 'error');
        }
    }

    // Function Renew RefreshToken
    // public function renewRefreshToken()
    // {
    //     // set Header
    //     HeaderController::setDefaultHeaders();

    //     // Validate Method
    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //         return ResponseHelper::errorResponse(405, 'error');
    //     }

    //     // Check Access Token Cookie
    //     if (! empty($this->access_token_cookie)) {
    //         return ResponseHelper::ResponseWithTitle(200, 'success', 'valid', 'Token valid.');
    //     }

    //     try {

    //         $decode = JWT::decode($this->refresh_token_cookie, new Key($this->secret_key, 'HS256'));

    //         // Assign jwt_payload from decoded token
    //         $empCode = $decode->sub;
    //         $role = $decode->role;
    //         $tokenId = $decode->jti;

    //         $refreshTokenDB = $this->TokenModel->getRefreshTokenByID($empCode, $tokenId);

    //         if (! $refreshTokenDB) {
    //             return ResponseHelper::errorResponse(400, 'error');
    //         }

    //         $refreshTokenResult = $refreshTokenDB->fetch(PDO::FETCH_ASSOC);

    //         if (! password_verify($this->refresh_token_cookie, $refreshTokenResult['token'])) {
    //             return ResponseHelper::errorResponse(401, 'error');
    //         }

    //         // Set Access token payload
    //         $access_token_payload = TokenHelper::accessTokenPayload($this->domain, $empCode, $role, $this->issued_at, $this->access_token_expire, $tokenId);

    //         // Set Refresh token payload
    //         $refesh_token_payload = TokenHelper::refreshTokenPayload($this->domain, $empCode, $role, $this->issued_at, $this->refresh_token_expire, $tokenId);

    //         // Encoded or create  Access Token and Refresh Token
    //         $access_token       = JWT::encode($access_token_payload, $this->secret_key, 'HS256');
    //         $refresh_token      = JWT::encode($refesh_token_payload, $this->secret_key, 'HS256');
    //         $refresh_token_hash = password_hash($refresh_token, PASSWORD_ARGON2ID);

    //         // Update refresh token in DB
    //         $this->TokenModel->updateToken($empCode, $tokenId, $refresh_token_hash, $this->UserDeviceType, $this->UserIP, $this->issued_at, $this->refresh_token_expire);

    //         // Store Access token in cookie HttpOnly with secure
    //         setcookie($this->access_token_name, $access_token, TokenHelper::cookieOpts($this->access_token_expire, null, true));

    //         // Store Refresh token in cookie HttpOnly with secure
    //         setcookie($this->refresh_token_name, $refresh_token, TokenHelper::cookieOpts($this->refresh_token_expire, null, true));

    //         // Store CSRF token in Cookie secure without HttpOnly
    //         setcookie($this->csrf_token_name, $this->set_csrf_token, TokenHelper::cookieOpts($this->access_token_expire, null, false));

    //         return ResponseHelper::ResponseCsrf(200, 'success', 'Token renewed', 'Token renewed successfully', $this->set_csrf_token);
    //     } catch (\Firebase\JWT\ExpiredException $e) {
    //         return ResponseHelper::errorResponse(401, 'error');
    //     } catch (\Firebase\JWT\SignatureInvalidException $e) {
    //         return ResponseHelper::errorResponse(401, 'error');
    //     } catch (PDOException $e) {
    //         return ResponseHelper::errorResponse(400, 'error');
    //     }
    // }
}
