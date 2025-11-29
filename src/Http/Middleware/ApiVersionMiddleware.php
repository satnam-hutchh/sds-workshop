<?php

namespace Sds\Workshop\Http\Middleware;

use Psr\Http\Message\RequestInterface;

class ApiVersionMiddleware
{
    public function __construct(protected ?string $version = null) {}

    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {

            $uri = $request->getUri();
            $path = ltrim($uri->getPath(), '/');

            // Only add version if not already present
            if (!is_null($this->version) && !str_starts_with($path, $this->version . '/')) {
                $uri = $uri->withPath("/{$this->version}/" . $path);
                $request = $request->withUri($uri);
            }

            // Optional: add version header
            $request = $request->withHeader('X-Api-Version', $this->version);

            return $handler($request, $options);
        };
    }
}
