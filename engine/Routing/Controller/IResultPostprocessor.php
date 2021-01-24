<?php

namespace Engine\Routing\Controller;

use Engine\Result\Result;

interface IResultPostprocessor
{
    function __handleResult(Result $result): Result;
}