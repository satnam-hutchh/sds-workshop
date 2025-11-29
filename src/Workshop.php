<?php

namespace Sds\Workshop;

use Sds\Workshop\Http\ApiClient;
use Sds\Workshop\Http\Authentication;
use Sds\Workshop\Services;

class Workshop
{
    public ApiClient $client;
    public Services\TokenService $tokens;
    public Services\VehicleService $vehicle;

    public function __construct(
        string $clientId,
        string $clientSecret,
        array $config = []
    )
    {
        $auth = new Authentication($clientId, $clientSecret);
        $this->client = new ApiClient($auth, $config);
        $this->tokens = new Services\TokenService($this->client);
        $this->vehicle = new Services\VehicleService($this->client);
    }
}