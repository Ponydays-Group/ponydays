<?php
/*
  Topicinfo plugin
  (P) PSNet, 2008 - 2012
  http://psnet.lookformp3.net/
  http://livestreet.ru/profile/PSNet/
  http://livestreetcms.com/profile/PSNet/
*/

$config = array ();

// Количество топиков пользователя для показа в блоке на странице топика
$config ['Topics_Count'] = 5;

// ---

Config::Set ('block.rule_topicinfo', array (
  'action' => array ('blog'),
  'blocks' => array (
    'right' => array (
      'topicinfo' => array (
        'params' => array ('plugin' => 'topicinfo'),
        'priority' => 500,
      ),
    )
  ),
));

return $config;

?>