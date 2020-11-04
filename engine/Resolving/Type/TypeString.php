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
        if ($other instanceof TypeString) return true;
        if ($other->isScalar()) return ($val = $other->value) == null || settype($val, 'string');
        return $other instanceof TypeObject && method_exists($other->getClass(), '__toString');
    }
}