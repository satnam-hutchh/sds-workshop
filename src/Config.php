<?php

namespace MyApp;

class Config
{
    public const API_BASE = 'https://api.myapp.com/v1';

    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}
