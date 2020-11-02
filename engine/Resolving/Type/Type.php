<?php

namespace Engine\Resolving\Type;

use ReflectionException;
use ReflectionNamedType;
use ReflectionType;

abstract class Type
{
    private const TYPE_MAP = [
        'boolean' => TypeBoolean::class,
        'bool' => TypeBoolean::class,
        'integer' => TypeInteger::class,
        'int' => TypeInteger::class,
        'double'  => TypeDouble::class,
        'float'  => TypeDouble::class,
        'string'  => TypeString::class,
        'array'   => TypeArray::class,
        'object'  => TypeObject::class,
        'NULL'    => TypeNull::class
    ];
    /**
     * @var mixed
     */
    protected $value = null;
    /**
     * @var bool
     */
    protected $nullable = false;

    /**
     * @param $val
     *
     * @return \Engine\Resolving\Type\Type
     * @throws \ReflectionException
     */
    public static function of($val): Type
    {
        $type = self::byName(gettype($val));
        $type->withValue($val);

        return $type;
    }

    /**
     * @param string $typename
     *
     * @return \Engine\Resolving\Type\Type
     * @throws \ReflectionException
     */
    public static function byName(string $typename): Type
    {
        if (!isset(self::TYPE_MAP[$typename])) throw new ReflectionException("Type `$typename` is not supported");

        $class = self::TYPE_MAP[$typename];
        return new $class();
    }

    /**
     * @param \ReflectionType|null $reflection
     *
     * @return \Engine\Resolving\Type\Type
     * @throws \ReflectionException
     */
    public static function fromReflection(?ReflectionType $reflection): Type
    {
        if ($reflection == null) return new TypeAny();

        $nullable = $reflection->allowsNull();
        $type = null;
        if ($reflection instanceof ReflectionNamedType) {
            $name = $reflection->getName();
            $builtin = $reflection->isBuiltin();

            if ($builtin) {
                $type = self::byName($name);
            } else {
                $type = (new TypeObject())->withClass($name);
            }
        } //TODO: PHP8 feature: \ReflectionUnionType
        else {
            $classname = get_class($reflection);
            throw new ReflectionException("ReflectionType subclass `$classname` is not supported");
        }
        $type->nullable($nullable);

        return $type;
    }

    /**
     * @param $val
     *
     * @return $this
     */
    public function withValue($val): Type
    {
        $this->value = $val;

        return $this;
    }

    /**
     * @param bool $nullable
     *
     * @return $this
     */
    public function nullable($nullable = true): Type
    {
        $this->nullable = $nullable;

        return $this;
    }

    abstract public function isScalar(): bool;

    /**
     * @param \Engine\Resolving\Type\Type $other
     *
     * @return bool
     */
    public function isPresentableAs(Type $other): bool
    {
        if ($other->nullable && $this instanceof TypeNull) return true;
        return $other->canAcceptType($this);
    }

    /**
     * @param \Engine\Resolving\Type\Type $other
     *
     * @return bool
     */
    abstract protected function canAcceptType(Type $other): bool;
}