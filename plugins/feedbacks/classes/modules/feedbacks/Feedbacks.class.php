<?php

class PluginFeedbacks_ModuleFeedbacks extends Module{

	protected $oMapper;

	//***************************************************************************************
	public function Init(){		
		$this->oMapper 	= Engine::GetMapper(__CLASS__);
	}

	//***************************************************************************************
	public function SaveAction($oAction){
		return $this->oMapper->SaveAction($oAction);
	}

	//***************************************************************************************
	public function GetActionsByUserId($iUserId, $iActionsCount){
		return $this->oMapper->GetActionsByUserId($iUserId, $iActionsCount);
	}

	//***************************************************************************************
	public function UpdateViewDatetimeByUserId($iUserId){
		return $this->oMapper->UpdateViewDatetimeByUserId($iUserId);
	}

	//***************************************************************************************
	public function GetCurrentUserUnreadItemsCount(){
		if($this->User_GetUserCurrent()){
			$iUserId	= $this->User_GetUserCurrent()->getId();
			return $this->oMapper->GetUnreadItemsCountByUserId($iUserId);
		}else return false;
	}

	//***************************************************************************************
	public function GetActionsByUserIdLastActionId($iUserId, $iLastActionId, $iActionsCount){
		return $this->oMapper->GetActionsByUserIdLastActionId($iUserId, $iLastActionId, $iActionsCount);
	}

}
?>
