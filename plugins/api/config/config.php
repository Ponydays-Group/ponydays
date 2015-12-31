<?php
/**
 * Конфиг
 */

$config = array();

// Переопределить имеющуюся переменную в конфиге:
// Переопределение роутера на наш новый Action - добавляем свой урл  http://domain.com/api
Config::Set('router.page.api', 'PluginApi_ActionApi');

// Добавить новую переменную:
// $config['per_page'] = 15;
// Эта переменная будет доступна в плагине как Config::Get('plugin.api.per_page')
$config['table']['test_table'] = '___db.table.prefix___api_keys';
return $config;
