<?php

namespace MyApp\Services;

class AuthUrlBuilder
{
    public static function build(
        string $clientId,
        string $redirectUri,
        array $scopes = [],
        string $state = null,
        string $baseAuthUrl = 'https://api.myapp.com/oauth/authorize'
    ): string {
        $params = [
            'response_type' => 'code',
            'client_id'     => $clientId,
            'redirect_uri'  => $redirectUri,
            'scope'         => implode(' ', $scopes),
            'state'         => $state ?? bin2hex(random_bytes(16)),
        ];

        return $baseAuthUrl . '?' . http_build_query($params);
    }
}
