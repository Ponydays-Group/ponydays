/**
 * Брелок ключей безопасности разного назначения для сервера.
 */
CREATE TABLE `security_keyring` (
  `key_id` int AUTO_INCREMENT NOT NULL,
  `key_type` char(8) NOT NULL,
  `expire_time` timestamp NOT NULL,
  `sec_key` binary(64) NOT NULL,

  PRIMARY KEY (`key_id`),
  KEY `key_type` (`key_type`)
);
