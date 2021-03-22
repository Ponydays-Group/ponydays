<?php

namespace Engine\Result;

use Engine\Engine;
use Engine\Routing\Router;

class Redirect extends Result
{
    /**
     * @var string
     */
    protected $uri;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public static function to(string $uri): self
    {
        return new Redirect($uri);
    }

    public function render(Router $router)
    {
        Engine::getInstance()->Shutdown();
        func_header_location($this->uri);
    }
}