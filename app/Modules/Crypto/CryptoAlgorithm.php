<?php

namespace App\Modules\Crypto;

abstract class CryptoAlgorithm
{
	public abstract function hash(string $password, array $params): array;
	public function needs_rehash(array $params): string {
		return false;
	}
}
