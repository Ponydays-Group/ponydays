<?php

namespace Engine\Resolving\Type;

class TypeNull extends Type
{
    public function isScalar(): bool
    {
        return false;
    }

    protected function canAcceptType(Type $other): bool
    {
        return $other instanceof TypeNull;
    }
}