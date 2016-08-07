<?php
/**
 * Для того, чтобы начать публиковать записи в группу нужно
 * 1. Создать группу (Вид сообщества: Публиная страница)
 * 2. Создать приложение на странице https://vk.com/editapp?act=create (Тип: Standalone-приложение)
 * 

 */
$config = array();

/**
 * Настройки для публикации в группе VK
 *
 */
$config['vk']['app_id'] = 5161909; // Айди приложения. Берется на странице настроек приложения
$config['vk']['group_id'] = 105592235; // Айди группы в которую будет постинг. Берется из адреса вида http://vk.com/public70092613
$config['vk']['app_scope'] = 'offline,wall,photos'; // Права, которые будет запрашивать приложение
$config['vk']['access_token'] = 'cd80567b33a1142ef158776ba698f74e7ec21e589233606a480ff02b2640ac3f7e51d1cca4b6acfaade80';  // Аксесс тоукен. Ключ доступа для публикаций на стене группы
$config['vk']['published_default'] = 1; // Чекбокс по умолчанию включен если 1

/**
 * Роутинг страницы настроек плагина
 * http://vk.com/feofannet
 * /postingingroups/admin/ - получения аксесс тоукена
 */
Config::Set('router.page.postingingroups', 'PluginPostingingroups_ActionAdmin');

return $config;
