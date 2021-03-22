<?php

namespace Engine\Result;

use Engine\Result\Traits\IWithVariables;
use Engine\Result\Traits\WithVariablesArray;
use Engine\Routing\Router;

class Action extends Result implements IWithVariables
{
    use WithVariablesArray;
    /**
     * @var string
     */
    private $controllerName;
    /**
     * @var string
     */
    private $methodName;

    public function __construct(string $controllerName, string $methodName)
    {
        $this->controllerName = $controllerName;
        $this->methodName = $methodName;
    }

    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getRealMethodName(): string
    {
        return 'event'.ucfirst($this->getMethodName());
    }

    public function __toString(): string
    {
        return "$this->controllerName#$this->methodName";
    }

    /**
     * @param string $actionPath 'controller#method'
     *
     * @return \Engine\Result\Action
     */
    public static function by(string $actionPath): self
    {
        $split = explode('#', $actionPath);
        if (count($split) != 2) {
            throw new \InvalidArgumentException("Wrong action path: `$actionPath`");
        }

        return new Action($split[0], $split[1]);
    }

    public static function from(array $params): self
    {
        if (! isset($params['to'])) throw new \InvalidArgumentException("Missed `to` parameter in action configuration");
        $to = $params['to'];

        $action = self::by($to);

        //TODO: middlewares

        return $action;
    }

    public function render(Router $router)
    {
        $router->runAction($this);
    }

    public function copy(): self
    {
        return (new Action($this->controllerName, $this->methodName))->with($this->getVariables());
    }
}