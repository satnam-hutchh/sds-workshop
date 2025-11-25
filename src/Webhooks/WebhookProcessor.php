<?php

namespace Sds\Workshop\Webhooks;

use Sds\Workshop\Webhooks\WebhookSignature;
use Sds\Workshop\Webhooks\WebhookEvent;
use Sds\Workshop\Webhooks\WebhookException;

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
