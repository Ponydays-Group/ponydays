<?php

namespace Engine\Routing\Exception\Http;

use Throwable;

class MethodNotAllowedHttpException extends HttpException
{
    /**
     * @var array
     */
    private $allowedMethods = [];

    public function __construct(array $allowedMethods, Throwable $previous = null) {
        parent::__construct(405, $previous);
        $this->allowedMethods = $allowedMethods;
    }
}