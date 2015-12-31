CREATE TABLE IF NOT EXISTS `prefix_user_ignore` (
  `user_id` int(11) unsigned NOT NULL,
  `user_ignored_id` int(11) unsigned NOT NULL,
  `ignore_type` enum('topics','comments', 'blogs') NOT NULL DEFAULT 'topics',
  UNIQUE KEY `ignorance` (`user_id`,`user_ignored_id`,`ignore_type`),
  KEY `user_ignored_id_2` (`user_ignored_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `prefix_user_ignore`
  ADD CONSTRAINT `prefix_user_ignore_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prefix_user_ignore_ibfk_2` FOREIGN KEY (`user_ignored_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `prefix_user_forbid_ignore` (
  `user_id` int(11) unsigned NOT NULL,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `prefix_user_forbid_ignore`
  ADD CONSTRAINT `prefix_user_forbid_ignore_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `prefix_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

