<?php

namespace App\Modules\Crypto;

use Engine\Config;

class CryptoSHA512 extends CryptoAlgorithm
{
    public function hash(string $password, array $params): array
    {
        return [hash('sha512', $password.hash('sha512', $password).Config::Get('crypto.password.sha512.salt'))];
    }
}
