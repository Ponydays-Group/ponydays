CREATE TABLE IF NOT EXISTS `prefix_editcomment_data` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `date_add` datetime NOT NULL,
  `comment_text_source` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `comment_id` (`comment_id`),
  KEY `fsearch` (`comment_id`,`date_add`)
) ENGINE=InnoDB   DEFAULT CHARSET=utf8;

ALTER TABLE `prefix_editcomment_data`
  ADD CONSTRAINT `prefix_editcomment_data_fk0` FOREIGN KEY (`comment_id`) REFERENCES `prefix_comment` (`comment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_editcomment_data_fk3` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
