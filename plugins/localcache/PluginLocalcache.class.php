<?php

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginLocalcache extends Plugin {

    public $aInherits = array(

        'module' => array(
            'ModuleUser' => '_ModuleUser',
        ),
    );

    public function Activate() {
    	
        return true;
    }

    public function Deactivate(){

    	return true;
    }

    public function Init() {    	

    }
}
?>
