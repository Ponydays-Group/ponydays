<?php

namespace Engine\Result\Traits;

interface IWithVariables
{
    function with(array $vars): self;
    function withDefault(array $vars): self;

    function getVariables(): array;
}