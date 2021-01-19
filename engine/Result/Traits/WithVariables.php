<?php

namespace Engine\Result\Traits;

trait WithVariables
{
    /**
     * @var array
     */
    protected $vars = [];

    public function with(array $vars): self
    {
        $this->vars = array_merge($this->vars, $vars);

        return $this;
    }

    public function getVariables(): array
    {
        return $this->vars;
    }
}