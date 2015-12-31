<?php
/*-------------------------------------------------------
*
*   LiveStreet (v.1.x)
*   Plugin Talk Bell (v.0.3)
*   Copyright © 2011 Bishovec Nikolay
*
*--------------------------------------------------------
*
*   Plugin Page: http://netlanc.net
*   Contact e-mail: netlanc@yandex.ru
*
---------------------------------------------------------
*/

if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginTalkbell extends Plugin
{

    public function Activate()
    {

        if (!$this->isTableExists('prefix_talk_bell')) {
            $this->ExportSQL(dirname(__FILE__) . '/dump.sql');
        }

        return true;

    }

    public function Deactivate()
    {
        $this->ExportSQL(dirname(__FILE__) . '/dump_deactivate.sql');
        return true;
    }

    public function Init()
    {

    }


}

?>
