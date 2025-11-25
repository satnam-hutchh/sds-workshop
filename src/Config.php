<?php

namespace Sds\Workshop;

class Config
{
    public const AUTH_URL   = 'https://api.myapp.com/';
    public const API_BASE   = 'https://api.myapp.com/';
    public const VERSION    = 'v1';

    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}
