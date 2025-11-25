<?php

namespace Sds\Workshop\Webhooks;

use Sds\Workshop\Webhooks\WebhookException;

class WebhookSignature
{
    public static function verify(string $payload, string $signature, string $secret): bool
    {
        $expected = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expected, $signature)) {
            throw new WebhookException("Invalid webhook signature");
        }

        return true;
    }
}
