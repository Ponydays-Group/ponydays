<?php

namespace App\Modules\Crypto;

class CryptoMD5 extends CryptoAlgorithm
{
    public function hash(string $password, array $params): array
    {
        return [md5($password)];
    }
}
