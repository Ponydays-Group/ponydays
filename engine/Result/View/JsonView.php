<?php

namespace Engine\Result\View;

use Engine\LS;
use Engine\Modules\ModuleViewer;
use Engine\Result\Traits\JsonFromEmpty;
use Engine\Result\Traits\WithVariables;
use Engine\Routing\Router;

class JsonView extends View
{
    use WithVariables;
    use JsonFromEmpty;

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
}