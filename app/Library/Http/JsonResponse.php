<?php

declare(strict_types=1);

namespace App\Library\Http;

readonly class JsonResponse
{
    public function __construct(
        private string|array|object $content,
        private int $statusCode = 200,
        private array $headers = []
    ) {
    }

    public function __toString()
    {
        header('Content-Type: application/json');

        foreach ($this->headers as $header => $value) {
            header("$header: $value");
        }

        http_response_code($this->statusCode);

        return json_encode($this->content, JSON_UNESCAPED_UNICODE);
    }
}
