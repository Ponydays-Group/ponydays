<?php

/* -------------------------------------------------------
 *
 *   LiveStreet (1.x)
 *   Plugin Disabling personal blogs (v.1.2)
 *   Copyright © 2011 Bishovec Nikolay
 *
 * --------------------------------------------------------
 *
 *   Plugin Page: http://netlanc.net
 *   Contact e-mail: netlanc@yandex.ru
 *
  ---------------------------------------------------------
 */

class PluginDpb_ActionPersonalBlog extends ActionPlugin
{

    public function Init()
    {

        return Router::Action('error', '404');
    }

    protected function RegisterEvent()
    {

    }

}

?>
