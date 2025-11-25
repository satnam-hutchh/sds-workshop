<?php

namespace Sds\Workshop\Services;

use Sds\Workshop\Http\ApiClient;
use Sds\Workshop\Http\RequestBuilder;
use Sds\Workshop\Models\Token;

class TokenService
{
    public function __construct(private ApiClient $client) {}

    public function getAuthorizeUrl(): string
    {
        return rtrim($this->client->baseUrl, '/') . '/' . $this->client->version . '/oauth/authorize';
    }

    public function authUrl(string $redirectUri, array $scopes = null): string
    {
        return AuthUrlBuilder::build(
            clientId    : $this->client->auth->clientId,
            redirectUri : $redirectUri,
            scopes      : $scopes,
            baseAuthUrl : $this->getAuthorizeUrl()
        );
    }

    public function authorizationCode(string $code, string $redirectUri): Token
    {
        $req = RequestBuilder::make('POST', '/oauth/token')
            ->withBody([
                'grant_type'   => 'authorization_code',
                'code'         => $code,
                'redirect_uri' => $redirectUri,
                'client_id'    => $this->client->auth->clientId,
                'client_secret'=> $this->client->auth->clientSecret,
            ]);

        $response = $this->client->send($req)->toArray();

        $token = new Token($response);
        $this->client->auth->setAccessToken($token->access_token);

        return $token;
    }

    public function clientCredentials(): Token
    {
        $req = RequestBuilder::make('POST', '/oauth/token')
            ->withBody([
                'grant_type' => 'client_credentials'
            ]);

        $response = $this->client->send($req)->toArray();

        $token = new Token($response);
        $this->client->auth->setAccessToken($token->access_token);

        return $token;
    }

    public function refresh(string $refreshToken): Token
    {
        $req = RequestBuilder::make('POST', '/oauth/token')
            ->withBody([
                'grant_type'    => 'refresh_token',
                'refresh_token' => $refreshToken
            ]);

        $response = $this->client->send($req)->toArray();

        $token = new Token($response);
        $this->client->auth->setAccessToken($token->access_token);

        return $token;
    }
}
