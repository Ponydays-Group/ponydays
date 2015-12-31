CREATE TABLE `prefix_talk_bell` (
  `talk_bell_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) default NULL,
  `user_data_talk` longtext,
  `user_data_comment` longtext,
  `date` datetime default NULL,
  PRIMARY KEY  (`talk_bell_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
ALTER TABLE `prefix_user` ADD `user_settings_talk_bell` TINYINT( 1 ) NOT NULL DEFAULT '1' ;
