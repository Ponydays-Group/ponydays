<?php

namespace Engine\Result\View;

use Engine\LS;
use Engine\Modules\ModuleViewer;
use Engine\Routing\Router;

class JsonView extends View
{
    /**
     * @var array
     */
    protected $vars = [];

    public function __construct(array $vars = [])
    {
        $this->vars = $vars;
    }

    public function with(array $vars): JsonView
    {
        $this->vars = array_merge($this->vars, $vars);

        return $this;
    }

    public static function from(array $vars): JsonView
    {
        return new JsonView($vars);
    }

    public static function empty(): JsonView
    {
        return new JsonView();
    }

    public function _handle(Router $router)
    {
        /**
         * @var ModuleViewer $viewer
         */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->SetResponseJson();
        foreach ($this->vars as $key => $val) {
            $viewer->AssignAjax($key, $val);
        }
    }

    public function fetch(): string
    {
        return json_encode($this->vars);
    }
}