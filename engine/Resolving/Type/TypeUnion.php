<?php

namespace Engine\Resolving\Type;

class TypeUnion extends Type
{
    /**
     * @var array
     */
    protected $typelist = [];

    /**
     * TypeUnion constructor.
     *
     * @param array $typelist
     */
    public function __construct(array $typelist) {
        $this->typelist = $typelist;
    }

    /**
     * @param array $typenames
     *
     * @return \Engine\Resolving\Type\TypeUnion
     * @throws \ReflectionException
     */
    public static function from(array $typenames): TypeUnion
    {
        $types = [];
        foreach ($typenames as $typename) {
            $types[] = Type::byName($typename);
        }

        return new TypeUnion($types);
    }

    public function isScalar(): bool
    {
        return true;
    }

    protected function canAcceptType(Type $other): bool
    {
        /**
         * @var \Engine\Resolving\Type\Type $type
         */
        foreach ($this->typelist as $type) {
            if ($type->canAcceptType($other)) return true;
        }
        return false;
    }
}