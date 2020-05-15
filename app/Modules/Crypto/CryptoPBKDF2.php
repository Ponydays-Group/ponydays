<?php

namespace App\Modules\Crypto;

use Engine\Config;

class CryptoPBKDF2 extends CryptoAlgorithm
{
    public function hash(string $password, array $params): array
    {
        list($hash_algo, $iters, $salt_len, $key_len, $salt) = array_pad($params, 5, null);

        if(!$hash_algo) $hash_algo = Config::Get('crypto.password.pbkdf2.hash_algo');
        if(!$iters) $iters = Config::Get('crypto.password.pbkdf2.iterations');
        if(!$salt_len) $salt_len = Config::Get('crypto.password.pbkdf2.salt_len');
        if(!$key_len) $key_len = Config::Get('crypto.password.pbkdf2.key_len');
        if(!$salt) {
            $salt = openssl_random_pseudo_bytes($salt_len);
        } else {
            $salt = base64_decode($salt);
        }

        $local_salt = Config::Get('crypto.password.pbkdf2.local_salt');
        $key = openssl_pbkdf2($password, $salt ^ base64_decode($local_salt), $key_len, $iters, $hash_algo);

        return array(
            $hash_algo, $iters, $salt_len, $key_len,
            base64_encode($salt), base64_encode($key)
        );
    }

    public function needs_rehash(array $params): string
    {
        list($hash_algo, $iters, $salt_len, $key_len) = array_pad($params, 4, null);
        if($hash_algo != Config::Get('crypto.password.pbkdf2.hash_algo')) return true;
        if($iters != Config::Get('crypto.password.pbkdf2.iterations')) return true;
        if($salt_len != Config::Get('crypto.password.pbkdf2.salt_len')) return true;
        if($key_len != Config::Get('crypto.password.pbkdf2.key_len')) return true;
        return false;
    }
}
