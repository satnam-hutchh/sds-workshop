<?php

namespace Sds\Workshop\Services;

use Sds\Workshop\Http\ApiClient;
use Sds\Workshop\Http\RequestBuilder;
use Sds\Workshop\Models\User;

class UserService
{
    public function __construct(private ApiClient $client) {}

    public function create(array $payload): User
    {
        $req = RequestBuilder::make('POST', '/users')
            ->withBody($payload);

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
