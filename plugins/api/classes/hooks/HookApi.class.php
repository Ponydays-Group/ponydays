<?php

class PluginApi_HookApi extends Hook {

    /*
     * Регистрация событий на хуки
     */
	public function RegisterHook() {
                $this->AddHook('viewer_init_start', 'ViewerInitStart');
	}
	public function ViewerInitStart($aParams) {
//		Config::Set('view.skin','developer');
		if (in_array('SiteStyle', array_keys($_COOKIE))){
	        if ($_COOKIE['SiteStyle']=='Dark') {
                if ($_COOKIE['use_mobile'] != 1)
                    $this->Viewer_AppendStyle(Plugin::GetTemplatePath(__CLASS__)."css/dark.css"); // Добавление своего CSS
                }
            }
	    }
        /*if (in_array('old', array_keys($_COOKIE))){
            if ($_COOKIE['old'] == 1) {
                Config::Set('view.skin', 'bunker1');
            }
        }*/
    }
?>
