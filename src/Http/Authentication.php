<?php

namespace Sds\Workshop\Http;

class Authentication
{
    public ?string $accessToken = null;

    public function __construct(
        public string $clientId,
        public string $clientSecret
    ) {}

    public function setAccessToken(?string $token)
    {
        $this->accessToken = $token;
    }

    public function getAuthHeaders(): array
    {
        if ($this->accessToken) {
            return [
                'Authorization' => "Bearer {$this->accessToken}"
            ];
        }

        // Fallback for basic client auth (for token exchange)
        return [
            'Client-Id'     => $this->clientId,
            'Client-Secret' => $this->clientSecret,
        ];
    }
}
