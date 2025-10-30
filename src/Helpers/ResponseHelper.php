<?php
namespace App\Helpers;

class ResponseHelper
{
    // Use for an error response
    public static function errorResponse(int $code, string $status, string $message = '')
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');

        // ถ้า $message ไม่ส่งเข้ามา จะใช้ข้อความ default
        if (empty($message)) {
            $message = [
                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                500 => 'Internal Server Error',
                204 => 'No Content',
            ][$code] ?? 'Error';
        }

        echo json_encode([
            'code'    => $code,
            'status'  => $status,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }

    // Use for various response that use title
    public static function ResponseWithTitle(int $code, string $status, string $title, string $message)
    {
        http_response_code($code);
        echo json_encode([
            'code'    => $code,
            'status'  => $status,
            'title'   => $title,
            'message' => $message,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Use for response with csrf token mostly POST DELETE PUT request
    public static function ResponseCsrf(int $code, string $status, string $title, string $message, string $csrf_token)
    {
        http_response_code($code);
        echo json_encode([
            'code'       => $code,
            'status'     => $status,
            'title'      => $title,
            'message'    => $message,
            'csrf_token' => $csrf_token,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Use for response with data mostly GET request
    public static function ResponseData(int $code, string $status, string $title, string $message, $data)
    {
        http_response_code($code);
        echo json_encode([
            'code'    => $code,
            'status'  => $status,
            'title'   => $title,
            'message' => $message,
            'data'    => $data,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Use for response with data array mostly GET request
    public static function ResponseDataArray(int $code, string $status, string $title, string $message, array $data)
    {
        http_response_code($code);
        echo json_encode([
            'code'    => $code,
            'status'  => $status,
            'title'   => $title,
            'message' => $message,
            'count'   => count($data),
            'data'    => $data,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
