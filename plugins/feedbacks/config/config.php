<?php

$config = array();

// Служебные настройки. Без необходимости не менять.
$config['table']['actions'] 	= '___db.table.prefix___feedback_actions';
$config['table']['views'] 		= '___db.table.prefix___feedback_views';

$config['url']	= 'feedbacks';
Config::Set('router.page.'.$config['url'], 'PluginFeedbacks_ActionFeedbacks');

$config['block']['feedbacks'] = array(
	'action'  => array($config['url']),
	'blocks'  => array(
			'right' => array('stream'=>array('priority'=>100),'tags'=>array('priority'=>50))
		),
	'clear' => false,
);
Config::Set('block.feedbacks', $config['block']['feedbacks']);

return $config;

?>
