<?php

namespace Sds\Workshop\Http;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Sds\Workshop\Http\ApiException;
use Sds\Workshop\Http\RequestBuilder;
use Sds\Workshop\Http\ResponseBuilder;
use Sds\Workshop\Config;
use Sds\WorkshopLaravel\Http\Middleware\ApiVersionMiddleware;

class ApiClient
{
    protected Client $http;
    public string $baseUrl;
    public string $version;

    public function __construct(public Authentication $auth, array $config = [])
    {
        $this->baseUrl  = $config['base_uri'] ?? Config::API_BASE; // NEW
        $this->version  = $config['version'] ?? Config::VERSION;

        $stack = HandlerStack::create();
        $stack->push(new ApiVersionMiddleware($version));

        $this->http = new Client([
            'base_uri' => $this->baseUrl,
            'handler'  => $stack,
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
