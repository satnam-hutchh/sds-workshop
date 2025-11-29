<?php

namespace Sds\Workshop\Http;

use Sds\Workshop\Builders\RequestBuilder;
use Sds\Workshop\Builders\ResponseBuilder;

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

    public function for(RequestBuilder $request): static
    {
        $this->isAuthRequest = $request->authRequest;
        return $this;
    }


    public function getHeaders(): array
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

        throw new \Sds\Workshop\Exceptions\MissingBearerTokenException();
    }
}
