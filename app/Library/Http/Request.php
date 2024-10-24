<?php

declare(strict_types=1);

namespace App\Library\Http;

readonly class Request
{
    public static function file($key = null, $default = null)
    {
        $files = $_FILES;

        if (!isset($files[$key])) {
            return $default;
        }

        return $files[$key];
    }
}
