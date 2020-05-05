<?php

namespace App\Modules\Feedbacks;

use App\Modules\Feedbacks\Mapper\ModuleFeedbacks_MapperFeedbacks;
use App\Modules\User\ModuleUser;
use Engine\Engine;
use Engine\LS;
use Engine\Module;

class ModuleFeedbacks extends Module {

	protected $oMapper;

	//***************************************************************************************
	public function Init(){		
		$this->oMapper 	= Engine::MakeMapper(ModuleFeedbacks_MapperFeedbacks::class);
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
        /** @var ModuleUser $user */
        $user = LS::Make(ModuleUser::class);
		if($user->GetUserCurrent()){
			$iUserId	= $user->GetUserCurrent()->getId();
			return $this->oMapper->GetUnreadItemsCountByUserId($iUserId);
		}else return false;
	}

	//***************************************************************************************
	public function GetActionsByUserIdLastActionId($iUserId, $iLastActionId, $iActionsCount){
		return $this->oMapper->GetActionsByUserIdLastActionId($iUserId, $iLastActionId, $iActionsCount);
	}
}
