<?php

namespace Sds\Workshop\Http;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use Sds\Workshop\Builders\RequestBuilder;
use Sds\Workshop\Builders\ResponseBuilder;
use Sds\Workshop\Config;
use Sds\Workshop\Http\Middleware\ApiVersionMiddleware;
use Sds\Workshop\Exceptions\ApiException;

class ApiClient
{
    protected Client $http;
    public string $baseUrl;
    public ?string $version;

    public function __construct(public Authentication $auth, array $config = [])
    {
        $this->baseUrl  = $config['base_uri'] ?? Config::API_BASE; // NEW
        $this->version  = $config['version'] ?? Config::VERSION;

        $stack = HandlerStack::create();
        $stack->push(new ApiVersionMiddleware($this->version));

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
                    'json'  => $request->toPayload(),
                    'query' => $request->query ?? [],
                ]
            );

            return new ResponseBuilder($response);

        } catch (\Throwable $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

     /**
     * Generic request. Auto refresh + retry once when 401 is encountered.
     *
     * @param string $method
     * @param string $path
     * @param array $options (json => [...], query => [...], headers => [...])
     * @return array decoded json
     */
    public function request(string $method, string $path, array $options = []): array
    {
        $attempt = 0;
        $maxAttempts = 2;

        do {
            $attempt++;
            try {
                $response = $this->doRequest($method, $path, $options);
                // return json_decode($resp->getBody()->getContents(), true);
                return new ResponseBuilder($response);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
                // if 401 and we haven't refreshed yet => refresh and retry
                if ($status === 401 && $attempt < $maxAttempts) {
                    // try refresh via token store; prefer refresh token if stored
                    $stored = $this->tokenService->loadFromStore();
                    if ($stored && !empty($stored['refresh_token'])) {
                        try {
                            $this->tokenService->refresh($stored['refresh_token']);
                            // now retry
                            continue;
                        } catch (\Throwable $ex) {
                            // refresh failed - clear access token and rethrow original
                            $this->auth->clearAccessToken();
                            throw new ApiException('Token refresh failed: ' . $ex->getMessage(), 401);
                        }
                    } else {
                        // no refresh token — cannot refresh
                        throw new ApiException('Unauthorized and no refresh token available', 401);
                    }
                }

                // other 4xx/5xx => propagate as ApiException
                $body = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
                throw new ApiException($body, $status);
            } catch (\Throwable $e) {
                throw new ApiException($e->getMessage(), $e->getCode() ?: 500);
            }
        } while ($attempt < $maxAttempts);

        throw new ApiException('Max attempts reached', 500);
    }

    protected function doRequest(string $method, string $path, array $options = []): ResponseInterface
    {
        // build headers including auth headers
        $headers = $options['headers'] ?? [];
        $headers = array_merge($this->auth->getAuthHeaders(), $headers);

        // set json/query body
        $guzzleOpts = ['headers' => $headers];
        if (!empty($options['json'])) $guzzleOpts['json'] = $options['json'];
        if (!empty($options['query'])) $guzzleOpts['query'] = $options['query'];

        return $this->http->request($method, ltrim($path, '/'), $guzzleOpts);
    }
}
