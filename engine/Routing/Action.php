<?php

namespace Engine\Routing;

class Action
{
    /**
     * @var string
     */
    private $controllerName;
    /**
     * @var string
     */
    private $methodName;
    /**
     * @var array
     */
    private $args;

    public function __construct(string $controllerName, string $methodName)
    {
        $this->controllerName = $controllerName;
        $this->methodName = $methodName;
    }

    public function with(array $args): Action
    {
        $this->args = $args;
        return $this;
    }

    public function getControllerName(): string
    {
        return $this->controllerName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getArguments(): array
    {
        return $this->args;
    }

    public function __toString(): string
    {
        return "$this->controllerName#$this->methodName";
    }

    /**
     * @param string $actionPath 'controller#method'
     *
     * @return \Engine\Routing\Action
     */
    public static function by(string $actionPath): Action
    {
        $split = explode('#', $actionPath);
        if (count($split) != 2) {
            throw new \InvalidArgumentException("Wrong action path: `$actionPath`");
        }

        return new Action($split[0], $split[1]);
    }

    public static function from(array $params): Action
    {
        if (! isset($params['to'])) throw new \InvalidArgumentException("Missed `to` parameter in action configuration");
        $to = $params['to'];

        $action = self::by($to);

        //TODO: middlewares

        return $action;
    }
}