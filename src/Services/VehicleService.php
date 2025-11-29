<?php

namespace Sds\Workshop\Services;

use Sds\Workshop\Http\ApiClient;
use Sds\Workshop\Builders\RequestBuilder;
use Sds\Workshop\Models\User;
use Sds\Workshop\Builders\Vehicle;

class VehicleService
{
    public function __construct(private ApiClient $client) {}

    public function create(array $payload): User
    {
        $req = (new Vehicle\CreateRequest)->withBody($payload);

        return $client
            ->send($req)
            ->toModel(User::class);
    }

    public function find(string $id): User
    {
        $req = RequestBuilder::make('GET', "/users/$id");

        return $this->client
            ->send($req)
            ->toModel(User::class);
    }
}
