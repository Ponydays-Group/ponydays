<?php

namespace Engine\Result\View;

use Engine\Result\Traits\JsonFromEmpty;
use Engine\Result\Traits\Messages;
use Engine\Routing\Router;

class AjaxView extends JsonView
{
    use Messages;
    use JsonFromEmpty;

    public function render(Router $router)
    {
        $fetch = $this->fetch();
        header('Content-Type: application/json');
        echo $fetch;
    }

    public function fetch(): string
    {
        $stateError = false;
        $msgTitle = '';
        $msg = '';

        if (count($this->errorMsgs) > 0) {
            $stateError = true;
            $msgTitle = $this->errorMsgs[0]['title'];
            $msg = $this->errorMsgs[0]['msg'];
        } else if (count($this->noticeMsgs) > 0) {
            $msgTitle = $this->noticeMsgs[0]['title'];
            $msg = $this->noticeMsgs[0]['msg'];
        }

        $vars = $this->getVariables();
        $vars['sMsgTitle'] = $msgTitle;
        $vars['sMsg'] = $msg;
        $vars['bStateError'] = $stateError;

        return json_encode($vars);
    }
}