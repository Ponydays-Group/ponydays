<?php

if (!class_exists('Plugin')) {
    die('Hacking attempt!');
}

class PluginIgnore extends Plugin
{

    public $aInherits = array(
        'action' => array(
            'ActionAjax' => '_ActionAjax'
        ),
        'module' => array(
            'ModuleUser' => '_ModuleUser',
            'ModuleTopic' => '_ModuleTopic'
        ),
        'mapper' => array(
            'ModuleUser_MapperUser' => '_ModuleUser_MapperUser',
            'ModuleTopic_MapperTopic' => '_ModuleTopic_MapperTopic'
        ),
        'entity' => array(
            'ModuleComment_EntityComment' => '_ModuleComment_EntityComment'
        ),
    );

    public function Activate()
    {
        if (!$this->isTableExists('prefix_user_ignore')) {
            $export = $this->ExportSQL(dirname(__FILE__) . '/activate.sql');
            return $export['result'];
        }
        return true;
    }

    public function Init()
    {
        
    }

}