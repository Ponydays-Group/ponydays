<?php

namespace Engine\Resolving;

use ReflectionFunction;

class FunctionCallResolver extends CallResolver
{
    /**
     * @param callable $func
     *
     * @return \Engine\Resolving\FunctionCallResolver
     * @throws \ReflectionException
     */
    public static function resolve(callable $func): FunctionCallResolver
    {
        $reflection = new ReflectionFunction($func);
        return new FunctionCallResolver($reflection);
    }

    protected function invoke($args)
    {
        return $this->reflection->invokeArgs($args);
    }
}