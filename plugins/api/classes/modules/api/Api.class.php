<?php

class PluginApi_ModuleApi extends Module {
    protected $oMapper;

    public function Init() {
        $this->oMapper=Engine::GetMapper(__CLASS__);
    }
    public function setKey($oUserCurrent, $key_md5)
    {
        if ($iId = $this->oMapper->writeKey($this->oUserCurrent->getId(), $key_md5)) {
            return $iId;
        } else {
            return "error";
        }
    }
}
?>
