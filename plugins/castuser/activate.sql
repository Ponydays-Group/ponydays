CREATE TABLE IF NOT EXISTS `prefix_user_cast_history` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `target` enum('comment','topic') CHARACTER SET utf8 DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UK_user_cast_history` (`target`,`target_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;