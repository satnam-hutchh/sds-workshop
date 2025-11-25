<?php

namespace MyAppLaravel\Http\Middleware;

use Closure;
use MyApp\Webhooks\WebhookProcessor;
use Symfony\Component\HttpFoundation\Response;

class VerifyMyAppWebhook
{
    public function handle($request, Closure $next): Response
    {
        $payload   = $request->getContent();
        $signature = $request->header('MyApp-Signature');

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
