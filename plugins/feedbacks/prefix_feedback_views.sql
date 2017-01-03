CREATE TABLE IF NOT EXISTS `prefix_feedback_views` (
	
	`user_id`				  INT(11) UNSIGNED NOT NULL,
	`view_datetime`		INT(11) UNSIGNED NOT NULL,


	PRIMARY KEY (`user_id`),
	KEY `view_datetime` 		(`view_datetime`)

) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
