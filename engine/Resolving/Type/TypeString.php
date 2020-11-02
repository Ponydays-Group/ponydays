<?php

namespace Engine\Resolving\Type;

class TypeString extends Type
{
    public function isScalar(): bool
    {
        return false;
    }

    protected function canAcceptType(Type $other): bool
    {
        return $other instanceof TypeString || $other->isScalar() || ($other instanceof TypeObject && method_exists($other->getClass(), '__toString'));
    }
}