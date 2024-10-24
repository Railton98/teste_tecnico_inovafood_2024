<?php

declare(strict_types=1);

namespace App\Library\Route;

readonly class Route
{
    public function __construct(
        public string $uri,
        public string $request,
        public string $controller,
    ) {
    }

    private function currentUri(): string
    {
        return $_SERVER['REQUEST_URI'] !== '/' ? rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/') : '/';
    }

    private function currentRequest(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function match(): null|static
    {
        if (
            $this->uri === $this->currentUri() &&
            strtolower($this->request) === $this->currentRequest()
        ) {
            return $this;
        }

        return null;
    }
}
