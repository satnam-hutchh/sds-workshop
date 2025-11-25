<?php

namespace Sds\Workshop\Http;

class RequestBuilder
{
    public string $method;
    public string $endpoint;
    public array $headers = [];
    public array $body = [];

    public static function make(string $method, string $endpoint): static
    {
        $obj = new static();
        $obj->method = $method;
        $obj->endpoint = $endpoint;
        return $obj;
    }

    public function withHeaders(array $headers): static
    {
        $this->headers = $headers;
        return $this;
    }

    public function withBody(array $body): static
    {
        $this->body = $body;
        return $this;
    }
}
