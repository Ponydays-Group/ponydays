CREATE TABLE IF NOT EXISTS `prefix_feedback_actions` (
	
	`id`				      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id_to` 			INT(11) UNSIGNED NOT NULL,
	`user_id_from` 		INT(11) UNSIGNED NOT NULL,
	`action_type`	    VARCHAR(25),
	`add_datetime`		INT(11) UNSIGNED,

	`destination_object_id`				INT(11) UNSIGNED,
	`action_object_id`				    INT(11) UNSIGNED,


	PRIMARY KEY (`id`),
	KEY `add_datetime` 		(`add_datetime`),
	KEY `user_id_to`    	(`user_id_to`),
	KEY `user_id_from` 	  (`user_id_from`),
	KEY `action_type` 	  (`action_type`)

) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
