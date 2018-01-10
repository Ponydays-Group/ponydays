<?php

class PluginApi_ModuleApi extends Module {
    protected $oMapper;

    public function Init() {
        $this->oMapper=Engine::GetMapper(__CLASS__);
    }

    public function setKey($iId, $sKey)
    {
        if ($iResult = $this->oMapper->writeKey($iId, $sKey)) {
            return $sKey;
        } else {
            return false;
        }
    }

    public function getKey($iId)
    {
        if ($iResult = $this->oMapper->readKey($iId)) {
            return $iResult;
        } else {
            return false;
        }
    }

    public function deleteKey($sKey){
        if ($iResult = $this->oMapper->deleteKey($sKey)) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserByKey($sKey)
    {
        if ($iResult = $this->oMapper->GetUserByKey($sKey)) {
            return $iResult;
        } else {
            return false;
        }
    }
}
?>