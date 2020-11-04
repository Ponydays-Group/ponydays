<?php

namespace Engine\Result\View;

use Engine\Result\Result;

abstract class View extends Result
{
    abstract public function fetch(): string;
}