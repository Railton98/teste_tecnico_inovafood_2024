<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Library\Http\JsonResponse;

class HomeController
{
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'access the `/process` route via `POST` method by uploading a `.docx` file'
        ]);
    }
}
