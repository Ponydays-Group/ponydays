<?php

class ModuleCast extends Module
{
	protected $oMapper;
	protected $oUserCurrent = null;
	
    public function Init()
    {
        $this->oMapper = Engine::GetMapper(__CLASS__);
    }
	
    public function sendCastNotify($sTarget,$oTarget,$oParentTarget,$sParsingText){ 

    	$aSendUsers = array();
    	
    	if (preg_match_all("/<ls user=\"([^<]*)\" \//",$sParsingText,$aMatch)) {
    		foreach($aMatch[0] as $sAdditionalString){
    			if (preg_match("/<ls user=\"(.*)\"/",$sAdditionalString,$aInnerMatch)){
    			  	foreach($aInnerMatch as$sUserLogin){
    			  		$oTargetUser = $this->User_getUserByLogin($sUserLogin);
						if ($oTargetUser){
							if (!isset($aSendUsers[$oTargetUser->getId()])){
								$aSendUsers[$oTargetUser->getId()] = $oTargetUser;
							}
						}    				
    				}   				
    			}
  
    		}
		}

        if (preg_match_all("/class=\"ls-user\">([^<]*)<\/a>/",$sParsingText,$aMatch)) {        	
			foreach($aMatch[0] as $sAdditionalString){
				if (preg_match("/class=\"ls-user\">(.*)<\/a>/",$sAdditionalString,$aInnerMatch)){
					foreach($aInnerMatch as$sUserLogin){
						$oTargetUser = $this->User_getUserByLogin($sUserLogin);
						if ($oTargetUser){
							if (!isset($aSendUsers[$oTargetUser->getId()])){
								$aSendUsers[$oTargetUser->getId()] = $oTargetUser;
							}
						}						
					} 
				}				
			}
		}		
		
		foreach ($aSendUsers as $oTargetUser){
			$this->sendCastNotifyToUser($sTarget,$oTarget,$oParentTarget,$oTargetUser);
		}
    }
    
    
    protected function sendCastNotifyToUser($sTarget,$oTarget,$oParentTarget,$oUser){
    	    	
    	if (!$this->oMapper->castExist($sTarget,$oTarget->getId(),$oUser->getId())){
    		
    		$this->oUserCurrent = $this->User_GetUserCurrent();    		
    		
    		$oViewerLocal = $this->Viewer_GetLocalViewer();
			$oViewerLocal->Assign('oUser', $this->oUserCurrent);
			$oViewerLocal->Assign('oTarget', $oTarget);
			$oViewerLocal->Assign('oParentTarget', $oParentTarget);
			$oViewerLocal->Assign('oUserMarked', $oUser);

			if ($sTarget == "topic") {
				$notificationLink = "/blog/undefined/".$oTarget->getId();
				$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a> упомянул вас в посте <a href='".$notificationLink."'>".$oTarget->getTitle()."</a> ";
				$notificationText = $oTarget->getTitle();
				$notification = Engine::GetEntity(
					'Notification',
					array(
						'user_id' => $oTarget->getUserId(),
						'text' => $notificationText,
						'title' => $notificationTitle,
						'link' => $notificationLink,
						'rating' => 0,
						'notification_type' => 17,
						'target_type' => 'topic',
						'target_id' => $oTarget->getId(),
						'sender_user_id' => $this->oUserCurrent->getId(),
						'group_target_type' => 'topic',
						'group_target_id' => -1
					)
				);
			} else {
				$notificationLink = "/blog/undefined/".$oTarget->getTargetId()."#comment".$oTarget->getId();
				$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a> упомянул вас в <a href='".$notificationLink."'>комментарии</a> ".$oParentTarget->getTitle();
				$notificationText = $oTarget->getText();
				$notification = Engine::GetEntity(
					'Notification',
					array(
						'user_id' => $oTarget->getUserId(),
						'text' => $notificationText,
						'title' => $notificationTitle,
						'link' => $notificationLink,
						'rating' => 0,
						'notification_type' => 4,
						'target_type' => 'comment',
						'target_id' => $oTarget->getId(),
						'sender_user_id' => $this->oUserCurrent->getId(),
						'group_target_type' => 'topic',
						'group_target_id' => -1
					)
				);
			}
			if ($notificationCreated = $this->Notification_createNotification($notification)) {
				$this->Nower_PostNotification($notificationCreated);
			}

			//TODO: Use it optionally
//			$aAssigin = array(
//				'oUser' => $this->oUserCurrent,
//				'oTarget' => $oTarget,
//				'oParentTarget' => $oParentTarget,
//				'oUserMarked' => $oUser,
//			);
//
//			$sTemplateName = 'notify.'.$sTarget.'.tpl';
//
//			$sLangDir = 'notify/' . $this->Lang_GetLang();
//			if (is_dir($sLangDir)) {
//				$sPath = $sLangDir.'/'.$sTemplateName;
//			} else {
//				$sPath = 'notify/' . $this->Lang_GetLangDefault() .'/'. $sTemplateName;
//			}
//
//			$sText = $oViewerLocal->Fetch($sPath);
//
//			$aTitles = $this->Lang_Get('notify_title');
//			$sTitle = $aTitles[$sTarget];
//
//			$oTalk = $this->Talk_SendTalk($sTitle, $sText, $this->oUserCurrent, array($oUser), false, false);
//
//			$this->Notify_Send(
//				$oUser, $sTemplateName , $sTitle, $aAssigin, 'castuser'
//			);
//
//			$this->Talk_DeleteTalkUserByArray($oTalk->getId(), $this->oUserCurrent->getId());
//
//			$this->oMapper->saveExist($sTarget,$oTarget->getId(),$oUser->getId());
    	}
    }    
    
}

?>