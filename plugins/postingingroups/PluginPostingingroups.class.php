<?php
/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
	die('Hacking attemp!');
}

class PluginPostingingroups extends Plugin
{

	/**
	 * Наследование сущностей
	 * @var array
	 */
	protected $aInherits=array(
		'entity' => array(
			'ModuleTopic_EntityTopic'=>'PluginPostingingroups_ModuleTopic_EntityTopic'
		),
		'module' => array(
			'ModuleTopic'=>'PluginPostingingroups_ModuleTopic'
		)
	);

	// Активация плагина
	public function Activate()
	{

		return true;
	}

	// Деактивация плагина
	public function Deactivate()
	{

		return true;
	}


	// Инициализация плагина
	public function Init()
	{

	}
}