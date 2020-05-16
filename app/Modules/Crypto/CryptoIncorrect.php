<?php

namespace App\Modules\Crypto;

class CryptoIncorrect extends CryptoAlgorithm
{
    public function hash(string $password, array $params): array
    {
        return [];
    }
}
