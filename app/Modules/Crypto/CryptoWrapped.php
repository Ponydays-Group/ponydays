<?php

namespace App\Modules\Crypto;

use Engine\Config;

class CryptoWrapped extends CryptoAlgorithm
{
    public function hash(string $password, array $params): array
    {
        list($inner_algo_id, $outer_algo_id, $inner_algo_data, $outer_algo_data) = array_pad($params, 4, null);

        $inner_algo_params = array();
        $outer_algo_params = array();

        if(!$outer_algo_id) {
            $outer_algo_id = Config::Get('crypto.password.current_algo');
        } else if($outer_algo_data) {
            $outer_algo_params = explode('$', base64_decode($outer_algo_data));
        }

        if($inner_algo_data) {
            $inner_algo_params = explode('$', base64_decode($inner_algo_data));
        }

        $inner_algo_class = \App\Modules\ModuleCrypto::CRYPTO_PASSWORD_ALGOS[$inner_algo_id];
        $outer_algo_class = \App\Modules\ModuleCrypto::CRYPTO_PASSWORD_ALGOS[$outer_algo_id];

        $inner_data = (new $inner_algo_class())->hash($password, $inner_algo_params);
        $outer_data = (new $outer_algo_class())->hash(end($inner_data), $outer_algo_params);

        return array(
            $inner_algo_id, $outer_algo_id,
            base64_encode(implode('$', array_slice($inner_data, 0, -1))),
            base64_encode(implode('$', array_slice($outer_data, 0, -1))),
            base64_encode(end($outer_data))
        );
    }

    public function needs_rehash(array $params): string {
        return true;
    }
}
