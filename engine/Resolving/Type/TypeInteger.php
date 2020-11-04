<?php

namespace Engine\Resolving\Type;

class TypeInteger extends Type
{
    public function isScalar(): bool
    {
        return true;
    }

    protected function canAcceptType(Type $other): bool
    {
        return $other->isScalar()
            || ($other instanceof TypeString && ($other->value == null || strlen($other->value) < 16));
    }
}