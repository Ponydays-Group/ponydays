<?php

namespace Engine\Routing\Exception\Http;

use Throwable;

class NotFoundHttpException extends HttpException
{
    public function __construct(Throwable $previous = null) {
        parent::__construct(404, $previous);
    }
}