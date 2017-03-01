<?php

class PluginLocalcache_ModuleUser extends PluginLocalcache_Inherit_ModuleUser{

	protected $aUsers = array();
	
	public function GetUserById($sId) {
		if(!isset($this->aUsers[$sId])){
			$this->aUsers[$sId] = parent::GetUserById($sId);
		}
		
		return $this->aUsers[$sId];
	}   
    
	public function Update(ModuleUser_EntityUser $oUser) {
		$sId = $oUser->getId();
		if(isset($this->aUsers[$sId])){
			unset($this->aUsers[$sId]);
		}
		
		return parent::Update($oUser);
	}
}
