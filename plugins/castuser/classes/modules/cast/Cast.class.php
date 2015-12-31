<?php

class PluginCastuser_ModuleCast extends Module
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
		
			$aAssigin = array(
				'oUser' => $this->oUserCurrent,
				'oTarget' => $oTarget,
				'oParentTarget' => $oParentTarget,
				'oUserMarked' => $oUser,		
			);
			
			$sTemplateName = 'notify.'.$sTarget.'.tpl';
			
			$sLangDir = Plugin::GetTemplatePath('castuser') . 'notify/' . $this->Lang_GetLang();
			if (is_dir($sLangDir)) {
				$sPath = $sLangDir.'/'.$sTemplateName;
			} else {
				$sPath = Plugin::GetTemplatePath('castuser') . 'notify/' . $this->Lang_GetLangDefault() .'/'. $sTemplateName;
			}

			$sText = $oViewerLocal->Fetch($sPath);

			$aTitles = $this->Lang_Get('plugin.castuser.notify_title');
			$sTitle = $aTitles[$sTarget];
			
			$oTalk = $this->Talk_SendTalk($sTitle, $sText, $this->oUserCurrent, array($oUser), false, false);
			
			$this->Notify_Send(
				$oUser, $sTemplateName , $sTitle, $aAssigin, 'castuser'
			);
						
			$this->Talk_DeleteTalkUserByArray($oTalk->getId(), $this->oUserCurrent->getId());
			
			$this->oMapper->saveExist($sTarget,$oTarget->getId(),$oUser->getId());
    	}
    }    
    
}

?>