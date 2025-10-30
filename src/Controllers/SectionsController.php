<?php

namespace App\Controllers;



use App\Controllers\DBController;
use App\Controllers\HeaderController;
use App\Helpers\ResponseHelper;
use App\Models\SectionsModel;
use PDOException;

$root = dirname(__DIR__, 2);
require_once $root . '/vendor/autoload.php';

class SectionsController extends DBController
{
    private $db;
    private $input;
    private $jwt_payload;
    private $UnlockModel;

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->connection();

        $this->input = json_decode(file_get_contents('php://input'), true);

        $this->jwt_payload = $_SERVER['jwt_payload'] ?? null;

        $this->UnlockModel = new SectionsModel($this->db);
    }

    public function validate($section)
    {

        // set Header
        HeaderController::setDefaultHeaders();

        // Validate Method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return ResponseHelper::errorResponse(405, 'error');
        }

        // Check Jwt_payload from middleware
        if (! $this->jwt_payload) {
            return ResponseHelper::errorResponse(401, 'error');
        }

        // Vaidate Input
        if (empty($this->input['password']) || ! isset($this->input['password'])) {
            return ResponseHelper::errorResponse(400, 'error');
        }

        try {
            $username      = $this->jwt_payload['sub'];
            $unlockResult = $this->UnlockModel->validate($username);

            if (! $unlockResult) {
                return ResponseHelper::errorResponse(400, 'error');
            }

            if (password_verify($this->input['password'], $unlockResult)) {
                $_SESSION['unlocked_sections'][$section] = true;
                http_response_code(200);
                echo json_encode([
                    'status'   => 'success',
                    'redirect' => $section,
                ]);
                exit;
            } else {
                return ResponseHelper::errorResponse(200, 'error', 'รหัสผ่านไม่ถูกต้อง');
            }
        } catch (\Firebase\JWT\ExpiredException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        } catch (PDOException $e) {
            return ResponseHelper::errorResponse(401, 'error');
        }
    }

    public static function unlock($section, $icon, $title, $contentPath)
    {
        $basePath = dirname(__DIR__, 2) . '/views';

        // ถ้า section ยังไม่ unlock
        if (empty($_SESSION['unlocked_sections'][$section]) || ! isset($_SESSION['unlocked_sections'][$section])) {
            if (isset($_SESSION)) {
                $_SESSION['icon'] = $icon;
                $_SESSION['title'] = $title;
                require "$basePath/components/header.php";
                require "$basePath/unlock.php";
                require "$basePath/components/footer.php";
                return;
            }
        }

        // section unlock แล้ว
        require "$basePath/components/header.php";
        require $basePath . $contentPath;
        require "$basePath/components/footer.php";
    }
}
