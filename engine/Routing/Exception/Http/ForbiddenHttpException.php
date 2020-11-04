<?php

namespace Engine\Routing\Exception\Http;

use Throwable;

class ForbiddenHttpException extends HttpException
{
    public function __construct(Throwable $previous = null) {
        parent::__construct(403, $previous);
    }
}