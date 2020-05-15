<?php

namespace App\Modules\Crypto;

class CryptoSignatureHMAC extends CryptoSignature
{
    public function sign(string $msg, string $sec_key, array $params): string
    {
        $hash_algo = $params['hash'] ?: 'SHA256';
        return hash_hmac($hash_algo, $msg, $sec_key, true);
    }
}
