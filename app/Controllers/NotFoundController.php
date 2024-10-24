<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Library\Http\JsonResponse;

class NotFoundController
{
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => 'route not found'
        ]);
    }
}
