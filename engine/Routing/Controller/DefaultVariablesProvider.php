<?php

namespace Engine\Routing\Controller;

use Engine\Result\Result;
use Engine\Result\Traits\IWithVariables;

trait DefaultVariablesProvider
{
    public function __handleResult(Result $result): Result {
        if ($result instanceof IWithVariables) {
            $result->withDefault($this->templateDefaults);
        }

        return $result;
    }
}