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


class PluginTalkbell_HookTalkbell extends Hook
{
    public function RegisterHook()
    {
        $this->AddHook('template_body_end', 'InjectDoMany');
        $this->AddHook('template_form_settings_tuning_end', 'TuningEnd');
        $this->AddHook('template_copyright', 'Copyright', __CLASS__);
    }

    public function TuningEnd()
    {
        return $this->Viewer_Fetch(Plugin::GetTemplatePath('talkbell') . 'tuning.tpl');
    }

    public function InjectDoMany()
    {
        $oUserCurrent = $this->User_GetUserCurrent();
        if ($oUserCurrent) {
            $this->Viewer_Assign('sTWPTalkbell', rtrim(Plugin::GetTemplateWebPath('talkbell'), '/'));
            return $this->Viewer_Fetch(Plugin::GetTemplatePath('talkbell') . 'window_message.tpl');
        }
    }

    public function Copyright()
    {
        if (Router::GetAction()!='blogs' and Router::GetAction()!='index' and Router::GetAction()!='page'){
            return false;
        }
        return 'Спонсор плагина - <a href="http://catalognica.ru" target="_blank">catalognica.ru</a><br />';
    }
}

?>
