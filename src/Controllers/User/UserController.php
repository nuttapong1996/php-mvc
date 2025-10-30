<?php

namespace App\Controllers\User;

use App\Controllers\DBController;
use App\Controllers\Ftp\FtpImageController;
use App\Controllers\HeaderController;
use App\Helpers\ResponseHelper;
use App\Models\Ftp\FtpImageModel;
use App\Models\User\UserModel;
use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use PDO;

$root = dirname(__DIR__, 3);
require_once $root . '/vendor/autoload.php';

class UserController extends DBController
{
    private $db;
    private $dotenv;
    private $jwtPayload;

    // Model
    private $userModel;
    private $ftpImageModel;

    // Controller
    private $ftpImageController;

    // Fpt for image
    private $ftpServer;
    private $ftpUser;
    private $ftpPass;

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->connection();

        $this->userModel = new UserModel($this->db);
        $this->dotenv    = Dotenv::createImmutable(dirname(__DIR__, 3));
        $this->dotenv->load();
        $this->jwtPayload = $_SERVER['jwt_payload'] ?? null;

        // FTP Config
        // $this->ftpServer = $_ENV['FTP_SERVER'];
        // $this->ftpUser   = $_ENV['FTP_USERNAME'];
        // $this->ftpPass   = $_ENV['FTP_PASSWORD'];

        // $this->ftpImageModel      = new FtpImageModel($this->ftpServer, $this->ftpUser, $this->ftpPass);
        // $this->ftpImageController = new FtpImageController($this->ftpImageModel);
    }

    public function getProfile()
    {
        // Set Header
        HeaderController::setDefaultHeaders();

        // Validate Request Method
        if (!$_SERVER['REQUEST_METHOD'] == 'GET') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Validate Jwt
        if (!$this->jwtPayload) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        try {
            $username = $this->jwtPayload['sub'];
            $stmt = $this->userModel->getProfile($username);

            if (!$stmt) {
                return ResponseHelper::errorResponse(400, 'error', 'Error fetch data');
            }

            $resultCount = $stmt->rowCount();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return ResponseHelper::ResponseDataArray(200, 'success', 'User Profile',  $resultCount . ' records', $row);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Token expired');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(400, 'error');
        }
    }

    public function checkRole()
    {
        // Set Header
        HeaderController::setDefaultHeaders();

        if (!$_SERVER['REQUEST_METHOD'] == 'GET') {
            return ResponseHelper::errorResponse(405, 'error');
        }
        $access_token_name = $_ENV['APP_NAME'] . '_access_token';
        $access_token_cookie = trim($_COOKIE[$access_token_name] ?? '');

        if (!$access_token_cookie) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        try {
            $decode = JWT::decode($access_token_cookie, new Key($_ENV['SECRET_KEY'], 'HS256'));
            $username = $decode->sub;

            $stmt = $this->userModel->getRole($username);

            if ($stmt['user_role'] == 'u') {
                return false;
                // return ResponseHelper::ResponseWithTitle(200, 'success', 'Role', 'User');
            } else if ($stmt['user_role'] == 'a') {
                return true;
                // return ResponseHelper::ResponseWithTitle(200, 'success', 'Role', 'Admin');
            }
        } catch (\Exception $e) {
            return ResponseHelper::errorResponse(400, 'error');
        }
    }

    // public function getEmpImg()
    // {
    //     // Set Header
    //     HeaderController::setDefaultHeaders();

    //     // handle preflight
    //     if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    //         http_response_code(204);
    //         exit;
    //     }

    //     // Validate Request Method
    //     if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    //         return ResponseHelper::errorResponse(405, 'error');
    //     }
    //     // Validate Jwt
    //     if (!$this->jwtPayload) {
    //         return ResponseHelper::errorResponse(401, 'error');
    //     }

    //     try {
    //         $username = $this->jwtPayload['sub'];
    //         $ImgPath = $this->userModel->getEmpImgLoc($this->ftpServer, $username);

    //         if (!$ImgPath) {
    //             return ResponseHelper::errorResponse(400, 'error', 'Error fetch data');
    //         }

    //         $empPic = $this->ftpImageController->show($ImgPath);

    //         if (!$empPic) {
    //             return ResponseHelper::errorResponse(400, 'error', 'Error fetch data');
    //         }

    //         return $empPic;
    //     } catch (\Firebase\JWT\ExpiredException $e) {
    //         return ResponseHelper::errorResponse(401, 'error');
    //     } catch (\PDOException $e) {
    //         return ResponseHelper::errorResponse(400, 'error');
    //     }
    // }

    // public function getEmpMedInfo()
    // {
    //     // set Header
    //     HeaderController::setDefaultHeaders();

    //     // Validate Request Method
    //     if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    //         return ResponseHelper::errorResponse(405, 'error');
    //     }

    //     // Validate Jwt 
    //     if (!$this->jwtPayload) {
    //         return ResponseHelper::errorResponse(401, 'error');
    //     }

    //     try {
    //         $username = $this->jwtPayload['sub'];
    //         $stmt = $this->userModel->getEmpMedInfo($username);

    //         if (!$stmt) {
    //             return ResponseHelper::errorResponse(400, 'error', 'Error fetch data');
    //         }

    //         return ResponseHelper::ResponseData(200, 'success', 'Success fetch data', 'EmpMedInfo', $stmt);
    //     } catch (\Firebase\JWT\ExpiredException $e) {
    //         return ResponseHelper::errorResponse(401, 'error', 'Token expired');
    //     } catch (\Firebase\JWT\SignatureInvalidException $e) {
    //         return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
    //     } catch (\Exception $e) {
    //         return ResponseHelper::errorResponse(400, 'error');
    //     }
    // }

    // public function getEmpwarnInfo()
    // {
    //     // set Header
    //     HeaderController::setDefaultHeaders();

    //     // Validate Request Method
    //     if (!$_SERVER['REQUEST_METHOD'] == 'GET') {
    //         return ResponseHelper::errorResponse(405, 'error');
    //     }

    //     // Validate Jwt 
    //     if (!$this->jwtPayload) {
    //         return ResponseHelper::errorResponse(401, 'error');
    //     }

    //     try {
    //         $username = $this->jwtPayload['sub'];

    //         $stmt = $this->userModel->getEmpWarnInfo($username);

    //         if ($stmt == null) {
    //             return ResponseHelper::errorResponse(200, 'success', 'no data');
    //         } else if ($stmt == false) {
    //             return ResponseHelper::errorResponse(400, 'error', 'Error fetch data');
    //         }

    //         return ResponseHelper::ResponseData(200, 'success', 'Success fetch data', 'EmpWarnInfo', $stmt);
    //     } catch (\Firebase\JWT\ExpiredException $e) {
    //         return ResponseHelper::errorResponse(401, 'error', 'Token expired');
    //     } catch (\Firebase\JWT\SignatureInvalidException $e) {
    //         return ResponseHelper::errorResponse(401, 'error', 'Invalid token');
    //     } catch (\PDOException $e) {
    //         return ResponseHelper::errorResponse(400, 'error');
    //     }
    // }

    public function getUserIP()
    {
        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipList[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
    }

    public function getUserDeviceType()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ua        = strtolower($userAgent);
        if (strpos($ua, 'iphone') !== false) {
            return 'iPhone';
        }

        if (strpos($ua, 'ipad') !== false) {
            return 'iPad';
        }

        if (strpos($ua, 'android') !== false) {
            return 'Android';
        }

        if (strpos($ua, 'windows') !== false) {
            return 'Windows PC';
        }

        if (strpos($ua, 'macintosh') !== false) {
            return 'Mac';
        }

        if (strpos($ua, 'linux') !== false) {
            return 'Linux PC';
        }

        return 'Unknown';
    }
}
