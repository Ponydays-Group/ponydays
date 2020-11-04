<?php

namespace Engine\Result\View;

use Engine\LS;
use Engine\Modules\ModuleMessage;
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

    public function fetch(): string
    {
        $bStateError = false;
        $sMsgTitle = '';
        $sMsg = '';

        /** @var ModuleMessage $message */
        $message = LS::Make(ModuleMessage::class);
        $aMsgError = $message->GetError();
        $aMsgNotice = $message->GetNotice();

        if (count($aMsgError) > 0) {
            $bStateError = true;
            $sMsgTitle = $aMsgError[0]['title'];
            $sMsg = $aMsgError[0]['msg'];
        } else if (count($aMsgNotice) > 0) {
            $sMsgTitle = $aMsgNotice[0]['title'];
            $sMsg = $aMsgNotice[0]['msg'];
        }

        $vars = $this->vars;
        $vars['sMsgTitle'] = $sMsgTitle;
        $vars['sMsg'] = $sMsg;
        $vars['bStateError'] = $bStateError;

        return json_encode($vars);
    }
}