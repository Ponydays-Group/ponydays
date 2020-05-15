<?php
/**
 * Created by PhpStorm.
 * User: mineozelot
 * Date: 5/16/20
 * Time: 2:48 AM
 */

namespace App\Modules;

use App\Modules\Crypto\CryptoException;
use Engine\Config;
use Engine\LS;
use Engine\Module;

class ModuleAuth extends Module
{

    public const SIGNATURE_ALGOS
        = [
            'HS256' => ['hmac', 'SHA256'],
            'HS512' => ['hmac', 'SHA512'],
        ];

    public function Init()
    {
    }

    /**
     * Генерирует новый подписанный ключ
     * Используйте поля в $payload так:
     * 'iss' => (issuer)  информация о сервере, выпускающем ключ
     * 'sub' => (subject) информация о пользователе
     * 'exp' => (expiration) значение времени, после которого ключ считается
     * недействительным. Если не представлено, то ключ действителен
     * неограниченное время
     * 'nbf' => (not before) значение времени, после наступления которого
     * которого ключ начинает считается недействительным. Если не
     * предоставлено, то ключ доступен с настоящего момента времени
     * 'iat' => (issued at) значение времени, когда ключ был выпущен
     *
     * @param array  $payload       Данные ключа. Поля 'iss', 'sub, 'aud',
     *                              'exp', 'nbf', 'iat' зарезервированы.
     * @param string $sign_key_type Тип ключа из брелока
     * @param string $sign_algo     Алгоритм из SIGNATURE_ALGOS, которым будет
     *                              подписан ключ
     * @param string $data_format   Формат, в котором будут предоставлены
     *                              данные ключа
     *
     * @return string Строковое значение ключа
     */
    public function GenerateKey(
        array $payload,
        string $sign_key_type,
        string $sign_algo,
        string $data_format = 'json'
    ): string {
        if ( ! $sign_algo) {
            $sign_algo = Config::Get('crypto.auth.signature');
        }

        /** @var ModuleCrypto $crypto */
        $crypto = LS::Make(ModuleCrypto::class);

        $header = ['alg' => $sign_algo];

        list($kid, $type, $sign_key) = $crypto->GetLastKeyFor($sign_key_type);

        $payload = array_merge($payload, ['kid' => $kid]);

        $header_data  = base64url_encode(data_format_pack($data_format,
            $header));
        $payload_data = base64url_encode(data_format_pack($data_format,
            $payload));

        $base_part = $header_data . '.' . $payload_data;

        list($signature_algo_id, $hash_algo_id)
            = static::SIGNATURE_ALGOS[$sign_algo];
        $signature_algo = $crypto->GetSignature($signature_algo_id);

        $signature = $signature_algo->sign($base_part, $sign_key,
            ['hash' => $hash_algo_id]);

        return $base_part . '.' . base64url_encode($signature);
    }

    /**
     * Проверяет подписанный ключ
     *
     * @param string $key         Строковое представление подписанного ключа
     * @param string $data_format Формат, в котором предоставлены данные ключа
     *                            (json, msgpack)
     *
     * @return array Данные ключа, если ключ корректен и действителен
     * @throws \App\Modules\AuthException
     */
    public function VerifyKey(string $key, string $data_format = 'json'): array
    {
        $calltime = time();

        /** @var ModuleCrypto $crypto */
        $crypto = LS::Make(ModuleCrypto::class);

        $parts = explode('.', $key);
        if (count($parts) != 3) {
            throw new AuthException("wrong size",
                AuthException::INVALID_KEY_FORMAT);
        }

        list($header_data, $payload_data, $signature_data) = $parts;
        $header = data_format_unpack_type($data_format,
            base64url_decode($header_data), "array");
        if ( ! $header) {
            throw new AuthException("no header",
                AuthException::INVALID_KEY_FORMAT);
        }
        $payload = data_format_unpack_type($data_format,
            base64url_decode($payload_data), "array");
        if ( ! $payload) {
            throw new AuthException("no payload",
                AuthException::INVALID_KEY_FORMAT);
        }
        $signature = base64url_decode($signature_data);
        if ( ! $signature) {
            throw new AuthException("no signature",
                AuthException::INVALID_KEY_FORMAT);
        }

        if ( ! $header['alg']) {
            throw new AuthException("wrong header",
                AuthException::INVALID_KEY_FORMAT);
        }
        $algo = static::SIGNATURE_ALGOS[$header['alg']];
        if ( ! $algo) {
            throw new AuthException("unsupported alg",
                AuthException::INVALID_KEY_FORMAT);
        }

        if ( ! $payload['kid']) {
            throw new AuthException("no kid",
                AuthException::INVALID_KEY_FORMAT);
        }

        try {
            list($kid, $type, $sign_key) = $crypto->GetKeyById($payload['kid']);
        } catch (CryptoException $e) {
            throw new AuthException("error while getting a key",
                AuthException::EXPIRED_KEY, $e);
        }
        $signature_algo = $crypto->GetSignature($algo[0]);
        if ( ! $signature_algo->verify($header_data . '.' . $payload_data,
            $signature, $sign_key, ['hash' => $algo[1]])) {
            throw new AuthException("invalid signature",
                AuthException::INVALID_SIGNATURE);
        }

        if ($payload['exp'] && ($calltime >= $payload['exp'])) {
            throw new AuthException("expired key", AuthException::EXPIRED_KEY);
        }

        if ($payload['nbf'] && ($payload['nbf'] > $calltime)) {
            throw new AuthException("key not active yet",
                AuthException::NOT_ACTIVE_KEY);
        }

        if ($payload['iat'] && ($payload['iat'] > $calltime)) {
            throw new AuthException("wrong issue time",
                AuthException::INVALID_KEY_FORMAT);
        }

        return $payload;
    }
}

class AuthException extends \RuntimeException
{
    public const INVALID_KEY_FORMAT = 0;
    public const INVALID_SIGNATURE = 1;
    public const EXPIRED_KEY = 2;
    public const NOT_ACTIVE_KEY = 3;
}
