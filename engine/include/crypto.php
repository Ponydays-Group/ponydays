<?php

/*
 * Функции для работы с паролями и прочей криптографией
 * Хеши пароля, при работе с этими функциями, должны иметь формат:
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
 *     key - значение ключа, результата выполнения алгоритма pbkdf2, в формате base64
 */

const CRYPTO_PASSWORD_ALGOS = array(
	'md5' => CryptoMD5::class,
	'sha512' => CryptoSHA512::class,
	'pbkdf2' => CryptoPBKDF2::class
);

abstract class CryptoAlgorithm {
	public abstract function hash(string $password, array $params): string;
	public function needs_rehash(array $params): string {
		return false;
	}
}

/**
 * Проверка соответствия хеша и пароля
 * @param string $hash
 * @param string $password
 * @return bool
 */
function crypto_password_verify(string $hash, string $password): bool {
	if($hash[0] != '$') return false;
	$params = explode('$', $hash);
	if(count($params) < 3) return false;
	$correct = crypto_password_hash_ext($password, $params[1], array_slice($params, 2));

	return hash_equals($correct, $hash);
}

/**
 * Проверяет, требуется ли обновить хеш пароля
 * @param string $hash
 * @return bool
 */
function crypto_password_needs_rehash(string $hash): bool {
	if($hash[0] != '$') return true;
	$params = explode('$', $hash);
	if(count($params) < 3) return true;
	if($params[1] != Config::Get('crypto.password.current_algo')) return true;

	//$pbkdf2$hash_algo$iters$salt_len$key_len$salt$key
	$class = CRYPTO_PASSWORD_ALGOS[$params[1]];
	return (new $class())->needs_rehash(array_slice($params, 2));
}

/**
 * Хеширует пароль по заданному алгоритму
 * @param string $password
 * @param string $algo Идетификатор алгоритма
 * @param array $params Параметры алгоритма
 * @return string Результат хеширования
 */
function crypto_password_hash_ext(string $password, string $algo, array $params = array()): string {
	$class = CRYPTO_PASSWORD_ALGOS[$algo];
	$data = (new $class())->hash($password, $params);

	return implode('$', array('', $algo, $data));
}

/**
 * Хеширует пароль по параметрам текущей конфигурации
 * @param string $password
 * @return string Результат хеширования
 */
function crypto_password_hash(string $password): string {
	return crypto_password_hash_ext($password, Config::Get('crypto.password.current_algo'));
}



class CryptoMD5 extends CryptoAlgorithm {
	public function hash(string $password, array $params): string {
		return md5($password);
	}
}

class CryptoSHA512 extends CryptoAlgorithm {
	public function hash(string $password, array $params): string {
		return hash('sha512', $password . hash('sha512', $password) . Config::Get('crypto.password.sha512.salt'));
	}
}

class CryptoPBKDF2 extends CryptoAlgorithm {
	public function hash(string $password, array $params): string {
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

		return implode('$', array(
			$hash_algo, $iters, $salt_len, $key_len,
			base64_encode($salt), base64_encode($key)
		));
	}

	public function needs_rehash(array $params): string {
		list($hash_algo, $iters, $salt_len, $key_len) = array_pad($params, 4, null);
		if($hash_algo != Config::Get('crypto.password.pbkdf2.hash_algo')) return true;
		if($iters != Config::Get('crypto.password.pbkdf2.iterations')) return true;
		if($salt_len != Config::Get('crypto.password.pbkdf2.salt_len')) return true;
		if($key_len != Config::Get('crypto.password.pbkdf2.key_len')) return true;
		return false;
	}
}
