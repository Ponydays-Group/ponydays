<?php

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginApi extends Plugin {

    // Объявление делегирований (нужны для того, чтобы назначить свои экшны и шаблоны)
    public $aDelegates = array(
            /**
             * 'action' => array('ActionIndex'=>'_ActionSomepage'),
             * Замена экшна ActionIndex на ActionSomepage из папки плагина
             *
             * 'template' => array('index.tpl'=>'_my_plugin_index.tpl'),
             * Замена index.tpl из корня скина файлом /plugins/abcplugin/templates/skin/default/my_plugin_index.tpl
             *
             * 'template'=>array('actions/ActionIndex/index.tpl'=>'_actions/ActionTest/index.tpl'),
             * Замена index.tpl из скина из папки actions/ActionIndex/ файлом /plugins/abcplugin/templates/skin/default/actions/ActionTest/index.tpl
             */


    );

    // Объявление переопределений (модули, мапперы и сущности)
    protected $aInherits=array(
       /**
        * Переопределение модулей (функционал):
        * 'module'  =>array('ModuleTopic'=>'_ModuleTopic'),
        *
        * К классу ModuleTopic (/classes/modules/Topic.class.php) добавляются методы из
        * PluginAbcplugin_ModuleTopic (/plugins/abcplugin/classes/modules/Topic.class.php) - новые или замена существующих
        *
        *
        *
        * Переопределение мапперов (запись/чтение объектов в/из БД):
        * 'mapper'  =>array('ModuleTopic_MapperTopic' => '_ModuleTopic_MapperTopic'),
        *
        * К классу ModuleTopic_MapperTopic (/classes/modules/mapper/Topic.mapper.class.php) добавляются методы из
        * PluginAbcplugin_ModuleTopic_EntityTopic (/plugins/abcplugin/classes/modules/mapper/Topic.mapper.class.php) - новые или замена существующих
        *
        *
        *
        * Переопределение сущностей (интерфейс между объектом и записью/записями в БД):
        * 'entity'  =>array('ModuleTopic_EntityTopic' => '_ModuleTopic_EntityTopic'),
        *
        * К классу ModuleTopic_EntityTopic (/classes/modules/entity/Topic.entity.class.php) добавляются методы из
        * PluginAbcplugin_ModuleTopic_EntityTopic (/plugins/abcplugin/classes/modules/entity/Topic.entity.class.php) - новые или замена существующих
        *
        */
    );

    // Активация плагина
    public function Activate() {
        if (!$this->isTableExists('prefix_tablename')) {
            $this->ExportSQL(dirname(__FILE__).'/install.sql'); // Если нам надо изменить БД, делаем это здесь.
        }
        return true;
    }

    // Деактивация плагина
    public function Deactivate(){
        /*
        $this->ExportSQL(dirname(__FILE__).'/deinstall.sql'); // Выполнить деактивационный sql, если надо.
        */
        return true;
    }


    // Инициализация плагина
    public function Init() {
	if (getRequest('set-theme',false,'get')=='on') {
//                        setcookie('theme',1,time()+60*60*24*30,Config::Get('sys.cookie.path'),Config::Get('sys.cookie.host'),false);
                        $this->Viewer_AppendStyle(Plugin::GetTemplatePath(__CLASS__)."css/dark.css"); // Добавление своего CSS
         }
    }
}
?>
