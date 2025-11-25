<?php

namespace MyApp\Webhooks;

class WebhookEvent
{
    public function __construct(
        public string $id,
        public string $type,
        public array $data,
        public string $timestamp
    ) {}

    public static function fromArray(array $arr): static
    {
        return new static(
            $arr['id'],
            $arr['type'],
            $arr['data'],
            $arr['timestamp'] ?? now()
        );
    }
}
