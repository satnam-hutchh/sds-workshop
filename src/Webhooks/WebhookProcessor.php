<?php

namespace MyApp\Webhooks;

use MyApp\Webhooks\WebhookSignature;
use MyApp\Webhooks\WebhookEvent;
use MyApp\Webhooks\WebhookException;

class WebhookProcessor
{
    public function __construct(public string $secret) {}

    public function process(string $payload, string $signature): WebhookEvent
    {
        WebhookSignature::verify($payload, $signature, $this->secret);

        $data = json_decode($payload, true);

        if (!$data || !isset($data['type'])) {
            throw new WebhookException("Invalid webhook payload");
        }

        return WebhookEvent::fromArray($data);
    }
}
