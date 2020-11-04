<?php

namespace Engine\Result\View;

use Engine\LS;
use Engine\Modules\ModuleViewer;
use Engine\Routing\Router;

class AjaxView extends JsonView
{
    public static function from(array $vars): JsonView // TODO: Change to `AjaxView` after php7.4
    {
        return new AjaxView($vars);
    }

    public static function empty(): JsonView
    {
        return new AjaxView();
    }

    public function _handle(Router $router)
    {
        /**
         * @var ModuleViewer $viewer
         */
        $viewer = LS::Make(ModuleViewer::class);
        $viewer->SetResponseAjax('json');
        foreach ($this->vars as $key => $val) {
            $viewer->AssignAjax($key, $val);
        }
    }
}