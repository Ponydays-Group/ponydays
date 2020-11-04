<?php

namespace Engine\Routing\Exception\Http;

use Throwable;

class BadRequestHttpException extends HttpException
{
    public function __construct(Throwable $previous = null) {
        parent::__construct(400, $previous);
    }
}