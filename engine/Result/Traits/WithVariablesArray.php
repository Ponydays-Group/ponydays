<?php

namespace Engine\Result\Traits;

trait WithVariablesArray
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

    public function withDefault(array $vars): self
    {
        $this->vars = array_merge($vars, $this->vars);

        return $this;
    }

    public function getVariables(): array
    {
        return $this->vars;
    }
}