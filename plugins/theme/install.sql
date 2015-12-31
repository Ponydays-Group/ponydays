--
-- SQL, которые надо выполнить движку при активации плагина админом. Вызывается на исполнение ВРУЧНУЮ в /plugins/PluginAbcplugin.class.php в методе Activate()
-- Например:

CREATE TABLE IF NOT EXISTS `prefix_theme` (
  `user_id` int(11) unsigned DEFAULT NULL,
  `theme_id` int(11) unsigned DEFAULT 1,
 ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
