<?php

namespace Engine\Result\View;

use Engine\LS;
use Engine\Modules\ModuleViewer;
use Engine\Result\Traits\IWithVariables;
use Engine\Result\Traits\WithVariablesArray;
use Engine\Routing\Router;

class JsonView extends View implements IWithVariables
{
    use WithVariablesArray;

    public function __construct(array $vars = [])
    {
        $this->vars = $vars;
    }

    public function render(Router $router)
    {
        /**
         * @var ModuleViewer $viewer
         */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->SetResponseJson();
        foreach ($this->getVariables() as $key => $val) {
            $viewer->AssignAjax($key, $val);
        }
    }

    public function fetch(): string
    {
        return json_encode($this->getVariables());
    }

    public static function from(array $vars): self
    {
        return new self($vars);
    }

    public static function empty(): self
    {
        return new self();
    }
}