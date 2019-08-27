<?php

class ModuleAuth extends Module
{
	public const SIGNATURE_ALGOS = array(
		'HS256' => array('hmac', 'SHA256'),
		'HS512' => array('hmac', 'SHA512')
	);

	public function Init() {}

	/**
	 * Генерирует новый подписанный ключ
	 * Используйте поля в $payload так:
	 * 'iss' => (issuer)  информация о сервере, выпускающем ключ
	 * 'sub' => (subject) информация о пользователе
	 * 'aud' => (audience) список идентификаторов возможных получателей
	 * 'exp' => (expiration) значение времени, после которого ключ считается недействительным. Если не представлено, то
	 *    ключ действителен неограниченное время
	 * 'nbf' => (not before) значение времени, после наступления которого которого ключ начинает считается
	 *    недействительным. Если не предоставлено, то ключ доступен с настоящего момента времени
	 * 'iat' => (issued at) значение времени, когда ключ был выпущен
	 * @param array $payload Данные ключа. Поля 'iss', 'sub, 'aud', 'exp', 'nbf', 'iat' зарезервированы.
	 * @param string $sign_key_type Тип ключа из брелока
	 * @param string $sign_algo Алгоритм из SIGNATURE_ALGOS, которым будет подписан ключ
	 * @param string $data_format Формат, в котором будут предоставлены данные ключа
	 * @return string Строковое значение ключа
	 */
	public function GenerateKey(array $payload, string $sign_key_type, string $sign_algo, string $data_format = 'json'): string
	{
		if(!$sign_algo) $sign_algo = Config::Get('crypto.auth.signature');

		$header = array('alg' => $sign_algo);

		list($kid, $type, $sign_key) = $this->Crypto_GetLastKeyFor($sign_key_type);

		$payload = array_merge($payload, array('kid' => $kid));

		$header_data = base64url_encode(data_format_pack($data_format, $header));
		$payload_data = base64url_encode(data_format_pack($data_format, $payload));

		$base_part = $header_data . '.' . $payload_data;

		list($signature_algo_id, $hash_algo_id) = static::SIGNATURE_ALGOS[$sign_algo];
		$signature_algo = $this->Crypto_GetSignature($signature_algo_id);

		$signature = $signature_algo->sign($base_part, $sign_key, array('hash' => $hash_algo_id));

		return $base_part . '.' . base64url_encode($signature);
	}

	/**
	 * Проверяет подписанный ключ
	 * @param string $key Строковое представление подписанного ключа
	 * @param string $sign_key Секретный ключ для подписи
	 * @param string $data_format Формат, в котором предоставлены данные ключа
	 * @return array Данные ключа, если ключ корректен и действителен
	 */
	public function VerifyKey(string $key, string $data_format = 'json'): array
	{
		$calltime = time();

		$parts = explode('.', $key);
		if(count($parts) != 3) throw new AuthException("wrong size", AuthException::INVALID_KEY_FORMAT);

		list($header_data, $payload_data, $signature_data) = $parts;
		$header = data_format_unpack($data_format, base64url_decode($header_data));
		if(!$header) throw new AuthException("no header", AuthException::INVALID_KEY_FORMAT);
		$payload = data_format_unpack($data_format, base64url_decode($payload_data));
		if(!$payload) throw new AuthException("no payload", AuthException::INVALID_KEY_FORMAT);
		$signature = base64url_decode($signature_data);
		if(!$signature) throw new AuthException("no signature", AuthException::INVALID_KEY_FORMAT);

		if(!$header['alg']) throw new AuthException("wrong header", AuthException::INVALID_KEY_FORMAT);
		$algo = static::SIGNATURE_ALGOS[$header['alg']];
		if(!$algo) throw new AuthException("unsupported alg", AuthException::INVALID_KEY_FORMAT);

		if(!$payload['kid']) throw new AuthException("no kid", AuthException::INVALID_KEY_FORMAT);

		try {
			list($kid, $type, $sign_key) = $this->Crypto_GetKeyById($payload['kid']);
		} catch(CryptoException $e) {
			throw new AuthException("error while getting a key", AuthException::EXPIRED_KEY, $e);
		}
		$signature_algo = $this->Crypto_GetSignature($algo[0]);
		if(!$signature_algo->verify($header_data . $payload_data, $signature, $sign_key, array('hash' => $algo[1]))) {
			throw new AuthException("invalid signature", AuthException::INVALID_SIGNATURE);
		}

		if($payload['exp'] && ($calltime >= $payload['exp'])) {
			throw new AuthException("expired key", AuthException::EXPIRED_KEY);
		}

		if($payload['nbf'] && ($payload['nbf'] > $calltime)) {
			throw new AuthException("key not active yet", AuthException::NOT_ACTIVE_KEY);
		}

		if($payload['iat'] && ($payload['iat'] > $calltime)) {
			throw new AuthException("wrong issue time", AuthException::INVALID_KEY_FORMAT);
		}

		return $payload;
	}
}

class AuthException extends RuntimeException
{
	public const INVALID_KEY_FORMAT = 0;
	public const INVALID_SIGNATURE = 1;
	public const EXPIRED_KEY = 2;
	public const NOT_ACTIVE_KEY = 3;
}
