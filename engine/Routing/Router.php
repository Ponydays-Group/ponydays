<?php

namespace Engine\Routing;

use Engine\Config;
use Engine\Engine;
use Engine\Resolving\MethodCallResolver;
use Engine\Routing\Exception\Http\NotFoundHttpException;
use Engine\Routing\Exception\RoutingException;
use Engine\Routing\Parser\RouteLexer;
use Engine\Routing\Parser\RouteParser;
use Engine\Routing\Parser\RouteWalker;
use Engine\View\View;
use FastRoute;
use FastRoute\RouteCollector;
use Throwable;

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
    /**
     * @var array
     */
    private $controllers = [];

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
                } catch (Throwable $t) {
                    file_put_contents(Config::Get('router.logFile'), $t, FILE_APPEND);
                }
            }
        }, [
            'cacheFile' => Config::Get('router.cacheFile'),
            'cacheDisabled' => true
        ]);
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

        foreach (array_reverse($this->controllers) as $controller) {
            $controller->shutdown();
        }
    }

    private function provideController(string $controllerName)
    {
        $class = Config::Get("router.page.$controllerName");
        if ($class == null) $class = $controllerName;

        if (! class_exists($class)) {
            throw new RoutingException("Could not find a controller: `$controllerName`");
        }

        if (isset($this->controllers[$class])) {
            return $this->controllers[$class];
        } else {
            $controller = new $class(Engine::getInstance(), $controllerName);
            $controller->boot();
            return $controller;
        }
    }

    /*
     * $params = [
     *      'to' => 'controller#method',
     *      'after' => 'middleware',
     *      'before' => 'middleware',
     * ]
     */
    public function runAction(Action $action)
    {
        $controller = $this->provideController($action->getControllerName());
        $method = $action->getMethodName();
        $vars = $action->getArguments();

        \Engine\Router::SetAction($controller);

        try {
            $result = MethodCallResolver::resolve_of($controller, $method)->with(
                function (array $types, string $name) use ($vars) {
                    if (isset($vars[$name]) && in_array(gettype($vars[$name]), $types)) {
                        return [$vars[$name], true];
                    }
                    if ($name == '_vars' && in_array('array', $types)) {
                        return $vars;
                    }

                    return [null, false];
                }
            )->with([Engine::getInstance(), 'resolve'])->hack()->call();
        } catch (\ReflectionException $e) {
            throw new RoutingException("Could not run an action: $action", 0, $e);
        }

        if ($result instanceof Result) {
            $result->_handle($this);
        }
    }

    private function handleNotFound() {
        $action = Config::Get('router.config.action_not_found');
        $this->runAction(Action::from($action['params'])->with($action['vars']));
    }

    private function handleFound(array $handler, array $vars) {
        if (RequestUtils::getHTTPMethod() == 'OPTIONS' && isset($handler['options'])) {
            http_response_code(204);
            header('Allow: ' . implode(', ', $handler['options']));
            return;
        }
        if (! isset($handler['params'])) return;

        try {
            $this->runAction(Action::from($handler['params'])->with($vars));
        } catch (NotFoundHttpException $e) {
            $this->handleNotFound();
        }
    }

    private function handleMethodNotAllowed(array $allowedMethods) {
        echo 'method not allowed: ' . var_export($allowedMethods, true);
    }
}