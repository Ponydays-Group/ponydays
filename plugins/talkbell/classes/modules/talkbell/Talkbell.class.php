<?php

class PluginTalkbell_ModuleTalkbell extends Module
{

    protected $oMapper;
    protected $oMapperTalk;

    public function Init()
    {

        $this->oMapper = Engine::GetMapper(__CLASS__);
        $this->oMapperTalk = Engine::GetMapper('ModuleTalk');

    }

    public function GetNewMessage($sUserId)
    {
        return $this->oMapper->GetNewMessage($sUserId);
    }

    public function UpdateUserTalkBell($oUser, $sValue)
    {
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG,array('user_update'));
        $this->Cache_Delete("user_{$oUser->getId()}");
        return $this->oMapper->UpdateUserTalkBell($oUser->getId(), $sValue);
    }

    public function GetUserTalkSerialise($sUserId, $st, $sc)
    {
        $aData = array();
        if ($aData = $this->oMapper->GetUserTalkSerialise($sUserId)) {
            $this->oMapper->UpdUserTalkSerialise($sUserId, $st, $sc);
        } else {
            $this->oMapper->AddUserTalkSerialise($sUserId, $st, $sc);
        }
        return $aData;
    }

}

?>
