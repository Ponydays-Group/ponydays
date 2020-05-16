<?php

if (!function_exists('msgpack_pack')) {
    function msgpack_pack($value): string
    {
        throw new UndefinedFunctionException("function msgpack_pack is undefined");
    }
}

if (!function_exists('msgpack_unpack')) {
    function msgpack_unpack($data): object
    {
        throw new UndefinedFunctionException("function msgpack_unpack is undefined");
    }
}

function data_format_pack(string $format, $data): string
{
    switch ($format) {
        case 'json':
            return json_encode($data);
        case 'msgpack':
            return msgpack_pack($data);
        default:
            throw new InvalidArgumentException("data_format_pack function: undefined format name: ".$format);
    }
}

function data_format_unpack(string $format, string $data)
{
    switch ($format) {
        case 'json':
            return @json_decode($data, true);
        case 'msgpack':
            return @msgpack_unpack($data);
        default:
            throw new InvalidArgumentException("data_format_unpack function: undefined format name: ".$format);
    }
}

function data_format_unpack_type(string $format, string $data, string $type_expect)
{
    $value = data_format_unpack($format, $data);
    if (gettype($value) != $type_expect) {
        return false;
    }

    return $value;
}

function base64url_encode(string $data): string
{
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

function base64url_decode(string $data): string
{
    return base64_decode(strtr($data, '-_', '+/').str_repeat('=', 3 - (3 + strlen($data)) % 4));
}

class UndefinedFunctionException extends RuntimeException
{
}
