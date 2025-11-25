<?php

namespace Sds\Workshop\Http;

use Psr\Http\Message\ResponseInterface;

class ResponseBuilder
{
    public int $status;
    public array $data;

    public function __construct(ResponseInterface $response)
    {
        $this->status = $response->getStatusCode();
        $this->data = json_decode($response->getBody(), true);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function toModel(string $modelClass)
    {
        return new $modelClass($this->data);
    }
}
