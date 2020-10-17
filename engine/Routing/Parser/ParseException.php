<?php

namespace Engine\Routing\Parser;

use Throwable;

class ParseException extends \RuntimeException
{
    public function __construct(string $filename, int $tokLine, int $tokColumn, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            "$filename:$tokLine:$tokColumn: error: $message",
            $code,
            $previous
        );
    }
}