<?php

namespace Engine\Routing;

class RequestUtils
{
    public static function getHTTPMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function getFullRawUri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    public static function getFullUri(): string
    {
        return rawurldecode(self::getFullRawUri());
    }

    public static function getUri(): string
    {
        $uri = self::getFullUri();

        $question = strpos($uri, '?');
        if ($question !== false) {
            $uri = substr($uri, 0, $question);
        }

        $uri = '/' . trim($uri, '/');

        return $uri;
    }
}