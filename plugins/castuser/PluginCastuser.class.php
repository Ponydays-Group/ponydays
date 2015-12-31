<?php

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginCastuser extends Plugin {

    public function Activate() {
        $this->Cache_Clean();
        if (!$this->isTableExists('prefix_user_cast_history')) {
            $resutls = $this->ExportSQL(dirname(__FILE__) . '/activate.sql');
            return $resutls['result'];
        }

        return true;
    }

    public function Deactivate(){
    	$this->Cache_Clean();
    	return true;
    }

    public function Init() {    	
		return true;
    }
}
?>
