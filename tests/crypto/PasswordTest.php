<?php

use App\Modules\ModuleCrypto;
use Engine\Config;
use PHPUnit\Framework\TestCase;

class Engine {}

final class PasswordTest extends TestCase {
	const EXAMPLES_MD5 = array(
		'' => '$md5$d41d8cd98f00b204e9800998ecf8427e',
		'123456789' => '$md5$25f9e794323b453885f5181f1b624d0b',
		'password'  => '$md5$5f4dcc3b5aa765d61d8327deb882cf99'
	);

	const EXAMPLES_SHA512 = array(
		''          => '$sha512$bcdc6349a8b1210b88460564ede4454beab16adcf334381aa9e12b7ab9365f5174d029de37ee79d2941c213c06cc9042fc2fc1bf9571af2e3b30209f372aa00f',
		'123456789' => '$sha512$7d9c9ea82210c8649deaf2a475e472827a307532922342a81eaa726a9e2467893df29ee30210fbcb494e6d28c5936c4f760d45069d807fa85536fc45e3f80b3e',
		'password'  => '$sha512$5c2c58b8402550a71d62eca7775ae4129712ed6988f3e4debec16b78d8376d32baf006b431ad4ab166ae066d3e16dff8804b8234f1982b3222df9bacadf579f7'
	);

	const EXAMPLES_PBKDF2 = array(
		''          => '$pbkdf2$sha256$15000$24$32$/iVjY2p1x6FEhZrVpkscBUPzecjPyyHl$OME4AJIcqUXCv9qiBODy4EgOh6hDhnuV9u8X85xIkzQ=',
		'123456789' => '$pbkdf2$sha512$1000$48$64$kaqRKrL4Qo8TNyfn4Xx2dOqpSW8rgQpP4N3EohRYLN0Truin2j3zYm+vnPtC6G8w$6/IZ7Uy0cMHgHYGvWvicwQYrMFr8yVEyRM0v2vA5pt4ODYPI7F5v+QZ7Od3FJd4KHWdEo6Aw2/JRX//UJPIoXA==',
		'password'  => '$pbkdf2$sha256$1001$49$65$ShXSrykBm5Jbvv0rJUQ/q7AppY91O0Mihu39A2RKUslPa7ZsUaFenGBxFZs2bz/6yQ==$3AmsutLM9g9cmeG9rvKfAQpLmn67jmGmHelGqysLKWdEsp/nhgDsyQRuTyKi18IGGB0n3HIyTuA2slN1V7yR9z4='
	);

	const EXAMPLES_WRAPPED = array(
		''          => '$wrapped$md5$sha512$$$ZmFjYTk2MGQwMmI5YjM5NWRkM2MwYjY5MTQzOTg2YjFmMjg4ZDEyNjhjYWQ2MDZkNjE1NzQ4ODYxOGNlMmYxNmI4ZTY5YzkzZjVmYjdkNWRhZmNlZGNlNjhhZmM3MzljYTQ5YzhhMjFkZTg3ZWNhNDI2NWM3MTg4NzI3ODQ3NzI=',
		'123456789' => '$wrapped$sha512$md5$$$MWIzYzg1ODliNGMyMmRlYjQ4YWE3ZWRiZDE3YjA3NWU=',
		'password'  => '$wrapped$md5$pbkdf2$$c2hhMjU2JDEwMDAkMjQkMzIkcXNqNm5WT2lldzNlR1B2K2s2TG9ZV0RURkFKU2NOMDM=$Qi9zVDQxZnhzSjVqV253b2xrQTFTM2lyL3JsSlFyYnMrTUFnL3ZsYmxCTT0=',
		'hello'     => '$wrapped$sha512$pbkdf2$$c2hhMjU2JDEwMDAkMjQkMzIkSlhWUW82SU1NOXRLTnU5eGtpUUU3UXpQWlk0cVJhSjc=$SE9LT3gzOFpFUVhRTUpPNHBDNUNUWTNReEh4SXMwSGtYVFUzdkt0d2d0VT0=',
		'world!'    => '$wrapped$pbkdf2$md5$c2hhMjU2JDEwMDAkMjQkMzIkeGtIU0tBL1A5RGdLU0RwdlRRMlVxOWVVOXVERVZueGI=$$ZTY5YmQ1MWJlYmFiMTgzYTQzN2EzYjU5NmI0YzQwZTE=',
		'pbkdf2'    => '$wrapped$pbkdf2$pbkdf2$c2hhMjU2JDEwMDAkMjQkMzIkcUdIcEo2VlBCR1E2US9GOENobklZbXNvRzZncDJCZC8=$c2hhMjU2JDEwMDAkMjQkMzIkMm1Qd1BsTVZGeWpFUmx6akgveUc4OUtxYWtCQ3dxdks=$K1ZZd1krcUhYaXcvTk1Dc2dXK2E2NnpvbGh1UnRMM0pTeVVSMlk1dnBnbz0='
	);

	const EXAMPLES_PASSWORDS = array(
		'',
		'123456789',
		'password',
		'qxa\5&@PC%jyCdUY'
	);

	/**
	 * @var ModuleCrypto $crypto
	 */
	private static $crypto;

	public static function setUpBeforeClass(): void {
		self::$crypto = new ModuleCrypto();
		Config::Set('crypto.password.current_algo', 'pbkdf2');
		Config::Set('crypto.password.sha512.salt', 'testsalt');
		Config::Set('crypto.password.pbkdf2.hash_algo', 'sha256');
		Config::Set('crypto.password.pbkdf2.iterations', '1000');
		Config::Set('crypto.password.pbkdf2.salt_len', '24');
		Config::Set('crypto.password.pbkdf2.key_len', '32');
		Config::Set('crypto.password.pbkdf2.local_salt', 'zAcPHVMJKr+FnVlguADyYQo/tHHEzu5M');
	}

	private function checkHash(string $algo, array $data): void {
		foreach($data as $pass => $hash) {
			self::assertEquals($hash, self::$crypto->PasswordHashExt($pass, $algo));
		}
	}

	public function testHash(): void {
		self::checkHash('md5', self::EXAMPLES_MD5);
		self::checkHash('sha512', self::EXAMPLES_SHA512);
	}

	private function checkVerify(string $algo, array $data): void {
		foreach($data as $pass => $hash) {
			self::assertTrue(self::$crypto->PasswordVerify($pass, $hash), "$algo failed for pass '$pass'");
		}
	}

	public function testVerify(): void {
		self::checkVerify('md5', self::EXAMPLES_MD5);
		self::checkVerify('sha512', self::EXAMPLES_SHA512);
		self::checkVerify('pbkdf2', self::EXAMPLES_PBKDF2);
		self::checkVerify('wrapped', self::EXAMPLES_WRAPPED);
	}

	public function testCreateAndVerify(): void {
		foreach(self::EXAMPLES_PASSWORDS as $pass) {
			self::assertTrue(self::$crypto->PasswordVerify($pass, self::$crypto->PasswordHashExt($pass, 'md5')), "md5 failed for pass '$pass'");
			self::assertTrue(self::$crypto->PasswordVerify($pass, self::$crypto->PasswordHashExt($pass, 'sha512')), "sha512 failed for pass '$pass'");
			self::assertTrue(self::$crypto->PasswordVerify($pass, self::$crypto->PasswordHashExt($pass, 'pbkdf2')), "pbkdf2 failed for pass '$pass'");
		}
	}

	public function testWrongPassword(): void {
		foreach(self::EXAMPLES_PASSWORDS as $pass) {
			self::assertFalse(self::$crypto->PasswordVerify('wrong_password', self::$crypto->PasswordHashExt($pass, 'md5')), "md5 failed for pass '$pass'");
			self::assertFalse(self::$crypto->PasswordVerify('wrong_password', self::$crypto->PasswordHashExt($pass, 'sha512')), "sha512 failed for pass '$pass'");
			self::assertFalse(self::$crypto->PasswordVerify('wrong_password', self::$crypto->PasswordHashExt($pass, 'pbkdf2')), "pbkdf2 failed for pass '$pass'");
		}
	}

	public function testNeedsRehash(): void {
		self::assertTrue(self::$crypto->PasswordNeedsRehash(self::$crypto->PasswordHashExt('password', 'md5')), 'md5 failed');
		self::assertTrue(self::$crypto->PasswordNeedsRehash(self::$crypto->PasswordHashExt('password', 'sha512')), 'sha512 failed');
		self::assertTrue(self::$crypto->PasswordNeedsRehash(self::$crypto->PasswordHashExt('password', 'pbkdf2', array('sha512'))), 'pbkdf2 failed');
		self::assertFalse(self::$crypto->PasswordNeedsRehash(self::$crypto->PasswordHashExt('password', 'pbkdf2')), 'pbkdf2 failed');
		self::assertFalse(self::$crypto->PasswordNeedsRehash(self::$crypto->PasswordHash('password')), 'current failed');
	}
}
