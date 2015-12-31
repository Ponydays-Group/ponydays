<?php

/*-------------------------------------------------------
*
*   LiveStreet (v.1.x)
*   Plugin Talk Bell (v.0.3)
*   Copyright © 2011 Bishovec Nikolay
*
*--------------------------------------------------------
*
*   Plugin Page: http://netlanc.net
*   Contact e-mail: netlanc@yandex.ru
*
---------------------------------------------------------
*/


$config = array();
$config['table']['talk_bell'] = '___db.table.prefix___talk_bell';

$config['time_periodical'] = 1000 * 30; // 1000 умножаем на время в секундах через которое делать запрос на новые сообщения
$config['group'] = array('talk' => 3, 'comment' => 3); // колличество новых сообщений для групировки в одно (talk - для писем, comment - для коментариев)

Config::Set('router.page.talkbell', 'PluginTalkbell_ActionTalkbell');

return $config;

?>
