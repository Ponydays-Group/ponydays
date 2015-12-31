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

class PluginDpb_HookDpb extends Hook
{

    public function RegisterHook()
    {
        $this->AddHook('check_topic_fields', 'CheckTopicFields', __CLASS__);
        $this->AddHook('check_photoset_fields', 'CheckTopicFields', __CLASS__);
        $this->AddHook('check_link_fields', 'CheckTopicFields', __CLASS__);
        $this->AddHook('check_question_fields', 'CheckTopicFields', __CLASS__);
        $this->AddHook('template_copyright', 'Copyright', __CLASS__);
    }

    public function CheckTopicFields($aVars)
    {
        /**
         * Проверяем id блога
         */
        if (getRequest('blog_id') <= 0) {
            $this->Message_AddError($this->Lang_Get('plugin.dpb.topic_create_blog_id_empty'), $this->Lang_Get('error'));
            $aVars['bOk'] = false;
        }
    }

    public function Copyright()
    {
        if (Router::GetAction()!='blogs'){
            return;
        }
        return '<a href="http://imonger.ru" target="_blank">Спонсор DPB - ЯТрейдер</a><br />';
    }

}

?>
