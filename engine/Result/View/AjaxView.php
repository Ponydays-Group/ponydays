<?php

namespace Engine\Result\View;

use Engine\LS;
use Engine\Modules\ModuleViewer;
use Engine\Routing\Router;

class AjaxView extends JsonView
{
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