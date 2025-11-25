<?php

namespace Sds\WorkshopLaravel\Http\Middleware;

use Closure;
use Sds\Workshop\Webhooks\WebhookProcessor;
use Symfony\Component\HttpFoundation\Response;

class VerifyWebhook
{
    public function handle($request, Closure $next): Response
    {
        $payload   = $request->getContent();
        $signature = $request->header('Webhook-Signature');

        $processor = new WebhookProcessor(
            config('myapp.webhook_secret')
        );

        try {
            $event = $processor->process($payload, $signature);
            $request->attributes->set('myapp_event', $event);
        } catch (\Throwable $e) {
            return response('Invalid signature', 403);
        }

        return $next($request);
    }
}
