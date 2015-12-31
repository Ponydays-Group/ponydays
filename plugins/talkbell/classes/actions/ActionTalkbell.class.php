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

class PluginTalkbell_ActionTalkbell extends ActionPlugin
{

    protected $oUserCurrent = null;

    public function Init()
    {
        $this->Viewer_SetResponseAjax('json');
        $this->oUserCurrent = $this->User_GetUserCurrent();
        $this->SetDefaultEvent('index');
    }

    protected function RegisterEvent()
    {
        $this->AddEvent('index', 'EventIndex');
        $this->AddEvent('ajaxedittuning', 'AjaxEditTuning');
    }
    protected function AjaxEditTuning()
    {

        if ($this->oUserCurrent){
            if($this->oUserCurrent->getUserSettingsTalkBell()) {
                $sValue = 0;
            } else {
                $sValue = 1;
            }

            if ($this->PluginTalkbell_Talkbell_UpdateUserTalkBell($this->oUserCurrent,$sValue)){
                $this->Message_AddNoticeSingle($this->Lang_Get('plugin.talkbell.tuning_edit_ok'), $this->Lang_Get('attention'));
                return;
            }
        }
        $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
        return;
    }

    protected function EventIndex()
    {
        $bStateError = true;
        $sMsg = '';
        $sMsgTitle = '';
        $iCountNewTalck = 0;
        $aComments = array();
        $str = $scr = serialize(array());
        $sc = $st = serialize(array());
        $aDiffSt = $aDiffSc = array();
        $aHtmlSt = $aHtmlSc = array();
        $bSt = $bSc = false;
        $aCm = array();

        if ($this->oUserCurrent) {

            $sUserId = $this->oUserCurrent->getId();

            if ($iCountNewTalck = $this->Talk_GetCountTalkNew($this->oUserCurrent->getId())) {
                $aResult = $this->PluginTalkbell_Talkbell_GetNewMessage($this->oUserCurrent->getId());

                $aTalk = $aResult['aTalk'];
                $aComment = $aResult['aComment'];
                $aTalkId = $aResult['aTalkId'];
                $aCommentId = $aResult['aCommentId'];

                if (!empty($aTalkId))
                    $st = serialize($aTalkId);
                if (!empty($aCommentId))
                    $sc = serialize($aCommentId);

                if ($aCm = $this->PluginTalkbell_Talkbell_GetUserTalkSerialise($sUserId, $st, $sc)) {
                    $str = $aCm->getUserDataTalk();
                    $scr = $aCm->getUserDataComment();
                }

                $att = array();

                if ($st != $str) {
                    $bStateError = false;
                    $aSt = unserialize($str);
                    $aDiffSt = array_diff($aTalkId, $aSt);
                    if (count($aDiffSt) > 0) {
                        $bSt = true;
                        if (count($aDiffSt) >= Config::Get('plugin.talkbell.group.talk')) {
                            $this->Viewer_Assign('sCountTalk', count($aDiffSt));
                        } else {
                            foreach ($aDiffSt as $key => $val) {
                                if (!empty($aTalk[$val])) {
                                    $att['tt'][] = $aTalk[$val];
                                    $this->Viewer_Assign('oTalk', $aTalk[$val]);
                                    $this->Viewer_Assign('oUser', $this->User_GetUserById($aTalk[$val]->getUserId()));
                                    $aHtmlSt[] = $this->Viewer_Fetch(Plugin::GetTemplatePath('talkbell') . 'talk_msg_row.tpl');
                                }
                            }
                        }
                    }
                }

                if ($sc != $scr) {
                    $bStateError = false;
                    $aSc = unserialize($scr);
                    $aDiffSc = array_diff($aCommentId, $aSc);
                    if (count($aDiffSc) > 0) {
                        $bSc = true;
                        if (count($aDiffSc) >= Config::Get('plugin.talkbell.group.comment')) {
                            $this->Viewer_Assign('sCountComment', count($aDiffSc));
                            $aHtmlSc[] = $this->Viewer_Fetch(Plugin::GetTemplatePath('talkbell') . 'comment_msg_row.tpl');
                        } else {
                            foreach ($aDiffSc as $key => $val) {
                                if (!empty($aComment[$key])) {
                                    $att['cc'][] = $aComment[$key]->getId() . '_' . $aComment[$key]->getTitle();
                                    $this->Viewer_Assign('oComment', $aComment[$key]);
                                    $aHtmlSc[] = $this->Viewer_Fetch(Plugin::GetTemplatePath('talkbell') . 'comment_msg_row.tpl');
                                }
                            }
                        }
                    }
                }
                //$sMsg=print_r(array($aCm),1);
            }
        } else {
            $this->Message_AddError($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }

        $this->Message_AddNoticeSingle('ok', $this->Lang_Get('attention'));
        $this->Viewer_AssignAjax('bStError', $bStateError);
        if (!$bStateError) {

            $this->Viewer_AssignAjax('sMsgTitle', $sMsgTitle);
            $this->Viewer_AssignAjax('sMsg', $sMsg);
            $this->Viewer_AssignAjax('iCountNewTalck', $iCountNewTalck);
            $this->Viewer_AssignAjax('aHtmlSt', $aHtmlSt);
            $this->Viewer_AssignAjax('aHtmlSc', $aHtmlSc);
            $this->Viewer_AssignAjax('bSt', $bSt);
            $this->Viewer_AssignAjax('bSc', $bSc);
        }

    }

}

?>
