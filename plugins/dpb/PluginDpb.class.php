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

if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginDpb extends Plugin
{
    public $aInherits = array(
        'action' => array('ActionPersonalBlog')
    );

    public $aDelegates = array(
        'template' => array(
            'menu.blog.tpl' => '_menu.blog.tpl',
        ),
    );
    public function Activate()
    {
        return true;
    }

    public function Init()
    {

    }

}

?>
