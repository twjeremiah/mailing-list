<?php

namespace App\Infrastructure\Http;

class JsonResponse
{
    public static function response(array $data, $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}