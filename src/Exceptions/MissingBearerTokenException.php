<?php

namespace Sds\Workshop\Exceptions;

use Exception;

class MissingBearerTokenException extends Exception
{
    public function __construct(string $message = "Bearer token missing. Authentication is required.", int $code = 401)
    {
        parent::__construct($message, $code);
    }
}
