<?php

namespace Engine\Result;

use Engine\Routing\Router;

abstract class Result
{
    abstract public function _handle(Router $router);
}