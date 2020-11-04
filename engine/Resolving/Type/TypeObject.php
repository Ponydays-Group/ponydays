<?php

namespace Engine\Resolving\Type;

class TypeObject extends Type
{
    /**
     * @var string
     */
    protected $classname = null;

    /**
     * @param $val
     *
     * @return \Engine\Resolving\Type\Type
     */
    public function withValue($val): Type
    {
        $this->withClass(get_class($val));

        return parent::withValue($val);
    }

    /**
     * @param string $classname
     *
     * @return \Engine\Resolving\Type\TypeObject
     */
    public function withClass(string $classname): TypeObject
    {
        $this->classname = $classname;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasClass(): bool
    {
        return $this->classname != null;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->classname;
    }

    public function isScalar(): bool
    {
        return false;
    }

    protected function canAcceptType(Type $other): bool
    {
        if ($other instanceof TypeObject) {
            if ($this->hasClass()) {
                return $other->hasClass() && is_a($other->getClass(), $this->getClass(), true);
            } else {
                return true;
            }
        }
        return false;
    }
}