<?php

namespace App\Modules\Crypto;

abstract class CryptoSignature
{
    public abstract function sign(string $msg, string $sec_key, array $params): string;
    public function verify(string $msg, string $signature, string $sec_key, array $params): bool
    {
        return hash_equals($signature, $this->sign($msg, $sec_key, $params));
    }
}
