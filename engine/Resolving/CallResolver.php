<?php

namespace Engine\Resolving;

use Engine\Resolving\Type\Type;
use ReflectionException;
use ReflectionFunctionAbstract;

abstract class CallResolver
{
    /**
     * @var \ReflectionFunctionAbstract
     */
    protected $reflection;
    /**
     * @var array
     */
    private $resolvers;

    protected function __construct(ReflectionFunctionAbstract $reflection)
    {
        $this->reflection = $reflection;
    }

    public function with(callable $resolver): CallResolver
    {
        $this->resolvers[] = $resolver;
        return $this;
    }

    abstract protected function invoke($args);

    /**
     * @throws \ReflectionException
     */
    public function call()
    {
        $parameters = $this->reflection->getParameters();
        $resolvedArgs = [];
        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $position = $parameter->getPosition();

            $hasDefault = $parameter->isDefaultValueAvailable();

            $type = Type::fromReflection($parameter->getType());

            foreach ($this->resolvers as $resolver) {
                [$val, $resolved] = $resolver($type, $name);
                if ($resolved) {
                    $resolvedArgs[$position] = $val;
                    continue 2;
                }
            }

            if ($hasDefault) {
                $resolvedArgs[$position] = $parameter->getDefaultValue();
            } else {
                throw new ReflectionException("Could not resolve parameter `$name`");
            }
        }

        return $this->invoke($resolvedArgs);
    }
}