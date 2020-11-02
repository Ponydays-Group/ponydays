<?php

namespace Engine\Resolving\Type;

class TypeArray extends Type
{
    public function isScalar(): bool
    {
        return false;
    }

    protected function canAcceptType(Type $other): bool
    {
        return $other instanceof TypeArray;
    }
}