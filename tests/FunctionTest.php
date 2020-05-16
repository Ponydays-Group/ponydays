<?php

use PHPUnit\Framework\TestCase;

require_once('include/function.php');
require_once('include/data_format.php');

class FunctionTest extends TestCase {
	const EXAMPLES_B64URL = array(
		'MA=='  => 'MA',
		'MDE=' => 'MDE',
		'MDEy' => 'MDEy',
		'n+d9WYgyUQQ=' => 'n-d9WYgyUQQ',
		'UOMq8bRIC/s=' => 'UOMq8bRIC_s',
		'pC+40qCngBaRskEHv1s/fw==' => 'pC-40qCngBaRskEHv1s_fw'
	);

	public function testBase64UrlEncode(): void {
		foreach(self::EXAMPLES_B64URL as $key => $val) {
			self::assertEquals($val, base64url_encode(base64_decode($key)));
		}
	}

	public function testBase64UrlDecode(): void {
		foreach(self::EXAMPLES_B64URL as $key => $val) {
			self::assertEquals(base64_decode($key), base64url_decode($val));
		}
	}
}
