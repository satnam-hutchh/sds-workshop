<?php

namespace Sds\Workshop;

use Sds\Workshop\Http\ApiClient;
use Sds\Workshop\Http\Authentication;
use Sds\Workshop\Services\TokenService;

class Workshop
{
    public ApiClient $client;
    public TokenService $tokens;

    public function __construct(
        string $clientId,
        string $clientSecret,
        array $config = []
    )
    {
        $auth = new Authentication($apiKey);
        $this->client = new ApiClient($auth, $config);
        $this->tokens = new TokenService($this->client);
    }
}