<?php
/**
 * Created by PhpStorm.
 * User: mineozelot
 * Date: 5/16/20
 * Time: 3:07 AM
 */

namespace App\Modules;

use App\Mappers\MapperCrypto;
use App\Modules\Crypto\CryptoException;
use App\Modules\Crypto\CryptoIncorrect;
use App\Modules\Crypto\CryptoMD5;
use App\Modules\Crypto\CryptoPBKDF2;
use App\Modules\Crypto\CryptoSHA512;
use App\Modules\Crypto\CryptoSignature;
use App\Modules\Crypto\CryptoSignatureHMAC;
use App\Modules\Crypto\CryptoWrapped;
use Engine\Config;
use Engine\Engine;
use Engine\Module;

/**
 * Функции для работы с паролями и прочей криптографией
 * Хеши пароля, при работе с функциями этого модуля, должны иметь формат:
 *     `$algo_id$data`
 * algo_id может быть: 'md5', 'sha512', 'pbkdf2', 'wrapped'
 * Содержимое data различается для каждого алгоритма:
 *     Для md5:
 *         `$md5$hash`
 *     hash - 16 байт md5-хеша в hex-формате
 *
 *     Для sha512:
 *         `$sha512$hash`
 *     hash - 64 байта sha512-хеша в hex-формате
 *
 *     Для pbkdf2:
 *         `$pbkdf2$hash_algo$iters$salt_len$key_len$salt$key`
 *     hash_algo - идентификатор хеш-функции, лежащей в основе алгоритма pbkdf2
 *     iters - количество итераций алгоритма pbkdf2
 *     salt - значение соли в формате base64
 *     key - значение ключа, результата выполнения алгоритма pbkdf2, в формате
 *     base64
 */
class ModuleCrypto extends Module
{

    public const CRYPTO_PASSWORD_ALGOS
        = [
            'md5'    => CryptoMD5::class,
            'sha512' => CryptoSHA512::class,
            'pbkdf2' => CryptoPBKDF2::class,

            'wrapped'   => CryptoWrapped::class,
            'incorrect' => CryptoIncorrect::class,
        ];

    public const SIGNATURE_ALGOS
        = [
            'hmac' => CryptoSignatureHMAC::class,
        ];

    protected $oMapper;

    public function Init()
    {
        $this->oMapper = Engine::MakeMapper(MapperCrypto::class);
    }

    /**
     * Проверка соответствия хеша и пароля
     *
     * @param string $hash
     * @param string $password
     *
     * @return bool
     */
    public function PasswordVerify(string $password, string $hash): bool
    {
        if ($hash[0] != '$') {
            $hash = self::PasswordUpdate($hash);
        }
        $params = explode('$', $hash);
        if (count($params) < 3) {
            return false;
        }
        $correct = self::PasswordHashExt($password, $params[1],
            array_slice($params, 2));

        return hash_equals($correct, $hash);
    }

    /**
     * Проверяет, требуется ли обновить хеш пароля
     *
     * @param string $hash
     *
     * @return bool
     */
    public function PasswordNeedsRehash(string $hash): bool
    {
        if ($hash[0] != '$') {
            return true;
        }
        $params = explode('$', $hash);
        if ($params[1] == 'incorrect') {
            return false;
        }
        if (count($params) < 3) {
            return true;
        }
        if ($params[1] != Config::Get('crypto.password.current_algo')) {
            return true;
        }

        //$pbkdf2$hash_algo$iters$salt_len$key_len$salt$key
        $class = self::CRYPTO_PASSWORD_ALGOS[$params[1]];

        return (new $class())->needs_rehash(array_slice($params, 2));
    }

    /**
     * Хеширует пароль по заданному алгоритму
     *
     * @param string $password
     * @param string $algo   Идетификатор алгоритма
     * @param array  $params Параметры алгоритма
     *
     * @return string Результат хеширования
     */
    public function PasswordHashExt(
        string $password,
        string $algo,
        array $params = []
    ): string {
        $class = self::CRYPTO_PASSWORD_ALGOS[$algo];
        $data  = (new $class())->hash($password, $params);

        return implode('$', array_merge(['', $algo], $data));
    }

    /**
     * Хеширует пароль по параметрам текущей конфигурации
     *
     * @param string $password
     *
     * @return string Результат хеширования
     */
    public function PasswordHash(string $password): string
    {
        return self::PasswordHashExt($password,
            Config::Get('crypto.password.current_algo'));
    }

    private function PasswordUpdate(string $hash): string
    {
        if (strlen($hash) == 32) {
            return '$md5$' . $hash;
        }
        if (strlen($hash) == 128) {
            return '$sha512$' . $hash;
        }

        return '$incorrect$' . $hash;
    }


    /**
     * Возвращает алгоритм подписи по идентификатору
     *
     * @param string $algo
     *
     * @return CryptoSignature
     */
    public function GetSignature(string $algo): CryptoSignature
    {
        $class = self::SIGNATURE_ALGOS[$algo];

        return new $class();
    }


    /**
     * Создает новый ключ в брелоке для данного типа
     *
     * @param string $type
     *
     * @return int id ключа
     */
    public function UpdateKeyFor(string $type): int
    {
        $key          = openssl_random_pseudo_bytes(64);
        $expires_time = time() + 60 * 60 * 24; // 1 сутки

        return $this->oMapper->AddKey($type, $expires_time, $key);
    }

    /**
     * Выдает последнюю версию ключа данного типа из брелока
     *
     * @param string $type Тип ключа. До 8 символов
     *
     * @return array Ключ в формате (id, type, value)
     * @throws \App\Modules\Crypto\CryptoException
     */
    public function GetLastKeyFor(string $type): array
    {
        $data = $this->oMapper->GetLastKeyFor($type);
        if ($data) {
            list($id, $expires, $type, $key) = $data;
            if ($expires > time()) {
                self::UpdateKeyFor($type);
            }

            return [$id, $type, $key];
        } else {
            if ($id = self::UpdateKeyFor($type)) {
                return self::GetKeyById($id);
            }
        }
        throw new CryptoException("could not get a key for type '$type'",
            CryptoException::DB_ERROR);
    }

    /**
     * Выдает ключ по id из брелока
     *
     * @param int $kid id ключа в брелоке
     *
     * @return array Ключ в формате (id, value, type)
     * @throws \App\Modules\Crypto\CryptoException
     */
    public function GetKeyById(int $kid): array
    {
        $data = $this->oMapper->GetKeyById($kid);
        if ($data) {
            list($id, $expires, $type, $key) = $data;
            if ($expires < time()
                           - Config::Get("crypto.keyring.max_key_life")) {
                $this->oMapper->DestroyKey($kid);
                throw new CryptoException("key has been destroyed",
                    CryptoException::DB_ERROR);
            }

            return [$id, $type, $key];
        }
        throw new CryptoException("could not get a key by id",
            CryptoException::DB_ERROR);
    }
}
