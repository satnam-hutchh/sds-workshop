<?php

namespace Sds\Workshop;

class Config
{
    public const AUTH_URL   = 'http://127.0.0.1:8011/';
    public const API_BASE   = 'http://127.0.0.1:8011/api/';
    public const VERSION    = null;

    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}
