<?php

namespace MyApp\Services;

use MyApp\Http\ApiClient;
use MyApp\Http\RequestBuilder;
use MyApp\Models\User;

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
