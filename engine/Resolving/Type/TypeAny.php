<?php

namespace Engine\Resolving\Type;

class TypeAny extends Type
{
    public function isScalar(): bool
    {
        return false;
    }

    protected function canAcceptType(Type $other): bool
    {
        return true;
    }
}