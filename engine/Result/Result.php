<?php

namespace Engine\Result;

use Engine\Routing\Router;

abstract class Result
{
    abstract public function render(Router $router);
}