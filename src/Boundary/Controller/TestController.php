<?php

namespace App\Boundary\Controller;

use App\Infrastructure\Http\JsonResponse;

class TestController
{
    public function test(): void
    {
        JsonResponse::response(
            [
                'status' => 'success',
                'data' => ['test']
            ],
        );
    }
}