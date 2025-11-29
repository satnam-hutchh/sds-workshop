<?php

namespace Sds\Workshop\Builders;

use Psr\Http\Message\ResponseInterface;

class ResponseBuilder
{
    public int $status;
    public array $data;

    public function __construct(public ResponseInterface $response)
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
        return new $modelClass($this->data['data']??[]);
    }

    public function toObject(string $modelClass = null): \stdClass
    {
        $obj = new \stdClass();

        // API envelope fields
        $obj->status        = $this->getStatus();
        $obj->statusCode    = $this->getStatusCode();
        $obj->message       = $this->getMessage();

        // Apply model mapping if modelClass provided
        if ($modelClass) {
            $obj->data = $this->toModel($modelClass);
        } else {
            // raw array as object
            $obj->data = $this->getData();
        }

        return $obj;
    }

    public function toJson(): array
    {
        $body = $this->response->getBody();
        return json_decode($body, true);
    }

    public function body(): string
    {
        return $this->response->getBody();
    }

    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    public function successful(): bool
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    public function failed(): bool
    {
        return $this->status() >= 400;
    }

    public function throwIfFailed(): static
    {
        if ($this->failed()) {
            throw new \Sds\Workshop\Exceptions\ApiException(
                'API Error: ' . $this->body(),
                $this->status()
            );
        }

        return $this;
    }

    /**
     * Friendly message from API
     */
    public function getMessage(): string
    {
        return $this->data['message'] ?? '';
    }

     /**
     * API statusCode string
     */
    public function getStatus(): string
    {
        return $this->data['status'] ?? '';
    }

     /**
     * API statusCode string
     */
    public function getStatusCode(): string
    {
        return $this->data['statusCode'] ?? '';
    }

    /**
     * Payload data
     */
    public function getData(): mixed
    {
        return $this->body['data'] ?? null;
    }
}

