<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddlewareAPI
{
    private $secret_key;
    private $access_token_name;
    private $csrf_token_name;

    public function __construct($access_token_name,$csrf_token_name, $secret_key)
    {
        $this->secret_key        = $secret_key;
        $this->access_token_name = $access_token_name;
        $this->csrf_token_name = $csrf_token_name;
    }

    public function handle($callback)
    {
        //ดึง token จาก cookie
        $access_token = $_COOKIE[$this->access_token_name] ?? '';

        if (! $access_token) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['code' => 401, 'status' => 'Unauthorized', 'message' => 'Unauthorized Access']);
            exit;
        }

        try {
            
            $decoded = JWT::decode($access_token, new Key($this->secret_key, 'HS256'));
            $payload = (array) $decoded;

            // แนบข้อมูล user ไปที่ global เพื่อใช้ใน controller ได้
            $_SERVER['jwt_payload'] = $payload;

            // ตรวจ CSRF สำหรับ method เปลี่ยน state
            $method = $_SERVER['REQUEST_METHOD'];
            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                $csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
                $csrfCookie = $_COOKIE[$this->csrf_token_name] ?? '';
                if (! $csrfHeader || $csrfHeader !== $csrfCookie) {
                    header('Content-Type: application/json');
                    http_response_code(403);
                    echo json_encode(['code' => 403, 'status' => 'error', 'message' => 'CSRF check failed']);
                    exit;
                }
            }

            return $callback();

        } catch (\Firebase\JWT\ExpiredException $e) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'code'    => 401,
                'status'  => 'error',
                'message' => 'Invalid or expired token',
                'error'   => $e->getMessage(),
            ]);
            exit;
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'code'    => 401,
                'status'  => 'error',
                'message' => 'Invalid Signature',
                'error'   => $e->getMessage(),
            ]);
            exit;
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'code'    => 401,
                'status'  => 'error',
                'message' => 'Invalid or expired token',
                'error'   => $e->getMessage(),
            ]);
            exit;
        }
    }
}
