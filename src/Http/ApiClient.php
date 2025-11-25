<?php

namespace MyApp\Http;

use GuzzleHttp\Client;
use MyApp\Http\ApiException;
use MyApp\Http\RequestBuilder;
use MyApp\Http\ResponseBuilder;

class ApiClient
{
    protected Client $http;

    public function __construct(public Authentication $auth, array $config = [])
    {
        $this->http = new Client([
            'base_uri' => $config['base_uri'] ?? \MyApp\Config::API_BASE,
            'timeout'  => $config['timeout'] ?? 10,
        ]);
    }

    public function send(RequestBuilder $request): ResponseBuilder
    {
        try {
            $response = $this->http->request(
                $request->method,
                $request->endpoint,
                [
                    'headers' => array_merge(
                        $this->auth->getHeaders(),
                        $request->headers
                    ),
                    'json' => $request->body,
                ]
            );

            return new ResponseBuilder($response);

        } catch (\Throwable $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }
}
