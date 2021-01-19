<?php

namespace Engine\Result\Traits;

trait JsonFromEmpty
{
    public static function from(array $vars): self
    {
        return new self($vars);
    }

    public static function empty(): self
    {
        return new self();
    }

}