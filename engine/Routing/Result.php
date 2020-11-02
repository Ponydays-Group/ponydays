<?php

namespace Engine\Routing;

abstract class Result
{
    abstract public function _handle(Router $router);
}