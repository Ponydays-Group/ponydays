<?php

namespace Engine\Resolving;

use ReflectionMethod;

class MethodCallResolver extends CallResolver
{
    /**
     * @var object
     */
    private $object;

    /**
     * @param string $class
     * @param string $method
     *
     * @return \Engine\Resolving\MethodCallResolver
     * @throws \ReflectionException
     */
    public static function resolve(string $class, string $method): MethodCallResolver
    {
        $reflection = new ReflectionMethod($class, $method);
        return new MethodCallResolver($reflection);
    }

    /**
     * @param object $object
     * @param string $method
     *
     * @return \Engine\Resolving\MethodCallResolver
     * @throws \ReflectionException
     */
    public static function resolve_of($object, string $method): MethodCallResolver
    {
        $resolver = self::resolve(get_class($object), $method)->of($object);
        return $resolver;
    }

    /**
     * @param object $obj
     *
     * @return \Engine\Resolving\MethodCallResolver
     */
    public function of($obj): MethodCallResolver
    {
        $this->object = $obj;
        return $this;
    }

    public function hack(): MethodCallResolver
    {
        if($this->reflection instanceof ReflectionMethod) {
            $this->reflection->setAccessible(true);
        }

        return $this;
    }

    protected function invoke($args)
    {
        return $this->reflection->invokeArgs($this->object, $args);
    }
}