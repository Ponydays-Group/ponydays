<?php

namespace Engine\Routing;

use Engine\Config;
use Engine\Routing\Parser\RouteLexer;
use Engine\Routing\Parser\RouteParser;
use Engine\Routing\Parser\RouteWalker;
use FastRoute;
use FastRoute\RouteCollector;

class Router
{
    private static $instance = null;
    public static function getInstance(): Router
    {
        return self::$instance ?: self::$instance = new Router();
    }

    /**
     * @var \FastRoute\Dispatcher
     */
    private $dispatcher = null;

    public function init()
    {
        $this->dispatcher = FastRoute\cachedDispatcher(function (RouteCollector $r) {
            $lexer = new RouteLexer();
            $parser = new RouteParser();
            $walker = new RouteWalker();

            foreach (Config::Get('router.registries') as $reg) {
                try {
                    $routeSource = file_get_contents($reg);
                    $lexer->init($routeSource, $reg);
                    $parser->init($lexer);
                    $result = $parser->parse();
                    $walker->walkList($result, $r);
                } catch (\Throwable $t) {
                    file_put_contents(Config::Get('router.logFile'), $t, FILE_APPEND);
                }
            }
        }, ['cacheFile' => Config::Get('router.cacheFile')]);
    }

    public function route()
    {
        $method = RequestUtils::getHTTPMethod();
        $uri = RequestUtils::getUri();
        $routeInfo = $this->dispatcher->dispatch($method, $uri);
        switch($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $this->handleNotFound();
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                $this->handleFound($handler, $vars);
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $this->handleMethodNotAllowed($allowedMethods);
                break;
        }
    }

    private function handleNotFound() {
        echo 'not found';
    }

    private function handleFound(array $handler, array $vars) {
        if (RequestUtils::getHTTPMethod() == 'OPTIONS' && isset($handler['options'])) {
            http_response_code(204);
            header('Allow: ' . implode(', ', $handler['options']));
            return;
        }
        echo var_export($handler, true) . '<br>***<br>' . var_export($vars, true);
    }

    private function handleMethodNotAllowed(array $allowedMethods) {
        echo 'method not allowed: ' . var_export($allowedMethods, true);
    }
}