<?php

namespace Engine;

class CallResolver
{
    /**
     * @var \ReflectionFunction
     */
    private $reflection;
    /**
     * @var array
     */
    private $args;
    /**
     * @var array
     */
    private $resolvers;

    /**
     * @param callable $func
     *
     * @return \Engine\CallResolver
     * @throws \ReflectionException
     */
    public static function resolve(callable $func): CallResolver
    {
        $reflection = new \ReflectionFunction(\Closure::fromCallable($func));
        return new CallResolver($reflection);
    }

    private function __construct(\ReflectionFunction $reflection)
    {
        $this->reflection = $reflection;
    }

    public function with(callable $resolver): CallResolver
    {
        $this->resolvers[] = $resolver;
        return $this;
    }

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
            $type = $parameter->getType();

            foreach ($this->resolvers as $resolver) {
                [$val, $resolved] = $resolver($type->getName(), $name);
                if ($resolved) {
                    $resolvedArgs[$position] = $val;
                    continue 2;
                }
            }

            if ($hasDefault) {
                $resolvedArgs[$position] = $parameter->getDefaultValue();
            } else {
                throw new \ReflectionException("Could not resolve parameter `$name`");
            }
        }

        return $this->reflection->invokeArgs($resolvedArgs);
    }
}