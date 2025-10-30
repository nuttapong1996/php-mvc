<?php

namespace App\Controllers\Auth;

use App\Controllers\DBController;
use App\Controllers\HeaderController;
use App\Controllers\Token\TokenController;
use App\Controllers\User\UserController;
use App\Helpers\ResponseHelper;
use App\Helpers\TokenHelper;
use App\Models\Auth\AuthModel;
use App\Models\Token\TokenModel;
use App\Models\User\UserModel;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use PDO;

date_default_timezone_set('Asia/Bangkok');

$root = dirname(__DIR__, 3);
require_once $root . '/vendor/autoload.php';

class AuthController extends DBController
{
    private $db;
    private $dotenv;
    private $jwt_payload;

    private $AuthModel;
    private $TokenModel;
    private $UserModel;

    private $ResponseController;
    private $UserController;

    private $input;
    private $secret_key;
    private $domain;
    private $app_name;

    private $refresh_token_name;
    private $access_token_name;
    private $csrf_token_name;

    private $issued_at;
    private $access_lifetime_s;
    private $refresh_lifetime_s;
    private $access_token_expire;
    private $refresh_token_expire;
    private $refresh_token_cookie;
    // private $access_token_cookie;
    private $refresh_token_id;
    private $set_csrf_token;
    private $UserIP;
    private $UserDeviceType;

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->connection();

        $this->dotenv = Dotenv::createImmutable(dirname(__DIR__, 3));
        $this->dotenv->load();
        $this->jwt_payload = $_SERVER['jwt_payload'] ?? null;

        // Model
        $this->AuthModel  = new AuthModel($this->db);
        $this->TokenModel = new TokenModel($this->db);
        $this->UserModel  = new UserModel($this->db);

        // Controller
        $this->UserController  = new UserController();

        $this->input                = json_decode(file_get_contents('php://input'), true);
        $this->secret_key           = $_ENV['SECRET_KEY'];
        $this->domain               = $_ENV['APP_DOMAIN'];
        $this->app_name             = $_ENV['APP_NAME'];
        $this->refresh_token_name   = $this->app_name . '_refresh_token';
        $this->access_token_name    = $this->app_name . '_access_token';
        $this->csrf_token_name      = $this->app_name . '_csrf';
        $this->issued_at            = time();
        $this->access_lifetime_s    = 60 * 15;          // 15 นาที
        $this->refresh_lifetime_s   = 60 * 60 * 24 * 7; // 7 วัน
        $this->access_token_expire  = $this->issued_at + $this->access_lifetime_s;
        $this->refresh_token_expire = $this->issued_at + $this->refresh_lifetime_s;
        $this->refresh_token_cookie = trim($_COOKIE[$this->refresh_token_name] ?? '');
        // $this->access_token_cookie  = trim($_COOKIE[$this->access_token_name] ?? '');
        $this->refresh_token_id     = bin2hex(random_bytes(16));
        $this->set_csrf_token       = bin2hex(random_bytes(32));
        $this->UserIP               = $this->UserController->getUserIP();
        $this->UserDeviceType       = $this->UserController->getUserDeviceType();
    }

    public function login()
    {
        // Set Header
        HeaderController::setDefaultHeaders();

        // Validate Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Input
        if (empty($this->input['username']) && empty($this->input['password']) || ! isset($this->input['username']) && ! isset($this->input['password'])) {
            return ResponseHelper::errorResponse(400, 'error');
        }
        // Assign Input to variables
        $username  = $this->input['username'];
        $password = $this->input['password'];

        // Validate username
        $validateEmp = $this->AuthModel->login($username);


        if (! $validateEmp || $validateEmp->rowCount() === 0) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid username or password');
        }

        try {
            // Fetch user data
            $resultEmp = $validateEmp->fetch(PDO::FETCH_ASSOC);

            // Verify password
            if (! password_verify($password, $resultEmp['password'])) {
                return ResponseHelper::errorResponse(401, 'error', 'Invalid username or password');
            }

            // Set Access token payload
            $access_token_payload = TokenHelper::accessTokenPayload($this->domain, $resultEmp['username'], $resultEmp['user_role'], $this->issued_at, $this->access_token_expire, $this->refresh_token_id);

            // Set Refresh token payload
            $refesh_token_payload = TokenHelper::refreshTokenPayload($this->domain, $resultEmp['username'], $resultEmp['user_role'], $this->issued_at, $this->refresh_token_expire, $this->refresh_token_id);

            // Encoded or create  Access Token and Refresh Token
            $access_token  = JWT::encode($access_token_payload, $this->secret_key, 'HS256');
            $refresh_token = JWT::encode($refesh_token_payload, $this->secret_key, 'HS256');

            // Set Access token in cookie HttpOnly with secure
            setcookie($this->access_token_name, $access_token, TokenHelper::cookieOpts($this->access_token_expire, null, true));

            // Set Refresh token in cookie HttpOnly with secure
            setcookie($this->refresh_token_name, $refresh_token, TokenHelper::cookieOpts($this->refresh_token_expire, null, true));

            // Store CSRF token in Cookie secure without HttpOnly
            setcookie($this->csrf_token_name, $this->set_csrf_token, TokenHelper::cookieOpts($this->access_token_expire, null, false));

            // Hash Refresh token before store in DB
            $refresh_token_hash = password_hash($refresh_token, PASSWORD_ARGON2ID);

            // Insert Refresh token in DB
            $this->TokenModel->insertRefreshToken($resultEmp['username'], $this->refresh_token_id, $refresh_token_hash, $this->UserDeviceType, $this->UserIP, date('Y-m-d H:i:s', $this->refresh_token_expire));

            // Check and remark expired token
            $stmt_expired = $this->TokenModel->getExpiresToken($resultEmp['username']);

            if ($stmt_expired->rowCount() > 0) {
                $this->TokenModel->setExpiredToken($resultEmp['username']);
            }

            // Check and remove Revoke token or expired token that more than 7 days
            $stmt_revoke = $this->TokenModel->getRevokeToken($resultEmp['username']);

            if ($stmt_revoke->rowCount() > 0) {
                $this->TokenModel->deleteExpiredToken($resultEmp['username']);
            }

            return ResponseHelper::ResponseCsrf(200, 'success', 'success', 'Login success', $this->set_csrf_token);
        } catch (\PDOException $e) {
            return ResponseHelper::errorResponse(500, 'error', 'Server error');
        }
    }

    public function register()
    {
        // Set Header
        HeaderController::setDefaultHeaders();

        // Validate Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }
        // echo 'pass';
        // exit;

        // Validate Input
        if (empty($this->input['username']) && empty($this->input['password']) && empty($this->input['IdenCode']) && empty($this->input['email'])) {
            return ResponseHelper::errorResponse(400, 'error');
        }

        $username  = $this->input['username'];
        $password = password_hash($this->input['password'], PASSWORD_ARGON2ID);
        $idenCode = $this->input['IdenCode'];
        $email    = $this->input['email'];

        // // Validate username
        // if ($this->UserModel->getusername($username)->rowCount() === 0) {
        //     return ResponseHelper::ResponseWithTitle(200, 'notfound', 'username', 'User code not found');
        // }

        //Check existing username
        if ($this->UserModel->getExistusername($username)->rowCount() > 0) {
            return ResponseHelper::ResponseWithTitle(200, 'exist', 'username', 'User code already exists');
        }

        //Check existing ID card
        if ($this->UserModel->getExistIdenCode($idenCode)->rowCount() > 0) {
            return ResponseHelper::ResponseWithTitle(200, 'exist', 'idcard', 'ID card already exists');
        }

        //Check existing email
        if ($this->UserModel->getExistEmail($email)->rowCount() > 0) {
            return ResponseHelper::ResponseWithTitle(200, 'exist', 'email', 'Email already exists');
        }

        try {
            // Registering user
            $register = $this->AuthModel->register($username, $password, $idenCode, $email);

            if (! $register) {
                return ResponseHelper::errorResponse(400, 'error', 'Registration failed');
            } else {
                return ResponseHelper::ResponseWithTitle(201, 'success', 'success', 'Registration successful');
            }
        } catch (\PDOException $e) {
            return ResponseHelper::errorResponse(500, 'error', 'Server error');
        }
    }

    public function logout()
    {
        // Set Header
        HeaderController::setDefaultHeaders();

        // Validate Method
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Refresh token
        if (empty($this->refresh_token_cookie)) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        try {
            // Assign variables
            $username = $this->jwt_payload['sub'] ?? null;
            $tokenId = $this->jwt_payload['jti'] ?? null;
            $remark  = 'Logout';

            if (! $username || ! $tokenId) {
                return ResponseHelper::errorResponse(401, 'error', 'Invalid token payload');
            }

            // Get Refresh token from DB by Token ID
            $stmtLogout  = $this->TokenModel->getRefreshTokenByID($username, $tokenId);
            $tokenResult = $stmtLogout->fetch(PDO::FETCH_ASSOC);

            // Validate Refresh token in cookie and DB
            if (! password_verify($this->refresh_token_cookie, $tokenResult['token'])) {
                return ResponseHelper::errorResponse(401, 'error');
            }

            // Set Revoke to current token in DB
            $setRevoke = $this->TokenModel->setRevokeToken($username, $tokenId, $remark);

            if (! $setRevoke) {
                return ResponseHelper::errorResponse(400, 'error', 'Fail to logout');
            }

            // Clear Cookie and Session
            
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

            return ResponseHelper::ResponseWithTitle(200, 'success', 'success', 'Logout successfully');
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (\Firebase\JWT\SignatureInvalidException) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (\PDOException $e) {
            return ResponseHelper::errorResponse(500, 'error');
        }
    }

    public function forgot()
    {
        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }
        // Validate Input
        if (empty($this->input['username']) && empty($this->input['IdenCode'])) {
            return ResponseHelper::errorResponse(400, 'error');
        }

        try {
            $stmt_forget = $this->AuthModel->forgot($this->input['username'], $this->input['IdenCode']);

            if ($stmt_forget && $stmt_forget->rowCount() > 0) {

                $resetToken = bin2hex(random_bytes(16)); // resetToken

                $this->AuthModel->insertResetToken($this->input['username'], $resetToken);

                return ResponseHelper::ResponseDataArray(200, 'success', 'Valid', 'You can reset your password.', ['resetToken' => $resetToken]);
            } else {
                return ResponseHelper::ResponseWithTitle(200, 'error', 'Invalid', 'Invalid user code or ID card number.');
            }
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error');
        }
    }

    public function checkResetToken()
    {
        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Input
        if (empty($this->input['username']) && empty($this->input['resetToken'])) {
            return ResponseHelper::errorResponse(400, 'error');
        }
        try {

            $CheckToken = $this->AuthModel->getResetToken($this->input['username'], $this->input['resetToken']);

            if ($CheckToken == 0) {
                $this->AuthModel->insertResetToken($this->input['username'], null, null);
                return ResponseHelper::ResponseWithTitle(200, 'error', 'Invalid token', 'Token is invalid or expired.');
            }
            return ResponseHelper::ResponseWithTitle(200, 'valid', 'Valid token', 'Reset token is valid.');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error');
        }
    }

    public function reset()
    {
        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Input
        if (empty($this->input['NewPass'])) {
            return ResponseHelper::errorResponse(400, 'error');
        }

        // Reset Password
        try {

            $haspass = password_hash($this->input['NewPass'], PASSWORD_ARGON2ID);
            $reset   = $this->AuthModel->resetPass($this->input['username'], $haspass);

            if (! $reset) {
                return $this->ResponseController->ResponseWithTitle(500, 'error', 'error', 'Reset password failed');
            }

            $this->TokenModel->deleteAllToken($this->input['username']);
            setcookie($this->access_token_name, '', time() - 3600, '/', '', true, true);
            setcookie($this->refresh_token_name, '', time() - 3600, '/', '', true, true);
            setcookie($this->csrf_token_name, '', time() - 3600, '/', '', false, false);
            $_SESSION = [];
            session_destroy();

            return ResponseHelper::ResponseWithTitle(200, 'success', 'success', 'Reset password successfully');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(500, 'error');
        }
    }

    public function checkPass()
    {
        HeaderController::setDefaultHeaders();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->ResponseController->errorResponse(405, 'error');
        }

        if (empty($this->input['OldPass'])) {
            return ResponseHelper::errorResponse(400, 'error');
        }

        if (empty($this->jwt_payload)) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        try {
            $username = $this->jwt_payload['sub'];

            $validate = $this->AuthModel->getCurPass($username);

            if (! $validate) {
                return ResponseHelper::errorResponse(404, 'error');
            }

            if ($validate && password_verify($this->input['OldPass'], $validate)) {
                return ResponseHelper::ResponseWithTitle(200, 'valid', 'valid', 'Current Password is valid');
            } else {
                return ResponseHelper::ResponseWithTitle(200, 'invalid', 'invalid', 'Current Password is invalid');
            }
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (\PDOException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        }
    }

    public function changePass()
    {
        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }
        // Validate JWT
        if (empty($this->jwt_payload)) {
            return ResponseHelper::errorResponse(401, 'error');
        }
        // Validate Input
        if (empty($this->input['NewPass'])) {
            return ResponseHelper::errorResponse(400, 'error');
        }

        try {

            $username = $this->jwt_payload['sub'];

            $newpass = password_hash($this->input['NewPass'], PASSWORD_ARGON2ID);

            $stmt_reset = $this->AuthModel->resetPass($username, $newpass);

            if (! $stmt_reset) {
                return ResponseHelper::ResponseWithTitle(400, 'error', 'Could not change password', 'Failed to change password');
            }

            $this->TokenModel->deleteAllToken($username);
            setcookie($this->access_token_name, '', time() - 3600, '/', '', true, true);
            setcookie($this->refresh_token_name, '', time() - 3600, '/', '', true, true);
            setcookie($this->csrf_token_name, '', time() - 3600, '/', '', false, false);
            $_SESSION = [];
            session_destroy();
            return ResponseHelper::ResponseWithTitle(200, 'success', 'Change password successfully', 'Password has been changed successfully');
        } catch (\Firebase\JWT\ExpiredException $e) {
            return $this->ResponseController->errorResponse(401, 'error', 'Invalid or expired token');
        } catch (\Exception $e) {
            return $this->ResponseController->errorResponse(401, 'error', 'Invalid or expired token');
        }
    }
}
