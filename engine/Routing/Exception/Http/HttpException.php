<?php

namespace Engine\Routing\Exception\Http;

use Throwable;

class HttpException extends \RuntimeException
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct(
            "HTTP $code",
            $code,
            $previous
        );
    }
}