<?php

namespace App\Modules\Cast;

use App\Modules\Cast\Mapper\ModuleCast_MapperCast;
use App\Modules\Notification\Entity\ModuleNotification_EntityNotification;
use App\Modules\Notification\ModuleNotification;
use App\Modules\Nower\ModuleNower;
use App\Modules\Topic\ModuleTopic;
use App\Modules\User\ModuleUser;
use Engine\Engine;
use Engine\LS;
use Engine\Module;
use Engine\Modules\Viewer\ModuleViewer;

class ModuleCast extends Module
{
	protected $oMapper;
	protected $oUserCurrent = null;
	
    public function Init()
    {
        $this->oMapper = Engine::MakeMapper(ModuleCast_MapperCast::class);
    }
	
    public function sendCastNotify($sTarget,$oTarget,$oParentTarget,$sParsingText){ 

    	$aSendUsers = array();

        /** @var ModuleUser $user */
        $user = LS::Make(ModuleUser::class);

    	if (preg_match_all("/<ls user=\"([^<]*)\" \//",$sParsingText,$aMatch)) {
    		foreach($aMatch[0] as $sAdditionalString){
    			if (preg_match("/<ls user=\"(.*)\"/",$sAdditionalString,$aInnerMatch)){
    			  	foreach($aInnerMatch as$sUserLogin){
    			  		$oTargetUser = $user->getUserByLogin($sUserLogin);
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
                        $oTargetUser = $user->getUserByLogin($sUserLogin);
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
    
    
    public function sendCastNotifyToUser($sTarget,$oTarget,$oParentTarget,$oUser){

    	if (!$this->oMapper->castExist($sTarget,$oTarget->getId(),$oUser->getId())){

    		$this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
    		
    		$oViewerLocal = LS::Make(ModuleViewer::class)->GetLocalViewer();
			$oViewerLocal->Assign('oUser', $this->oUserCurrent);
			$oViewerLocal->Assign('oTarget', $oTarget);
			$oViewerLocal->Assign('oParentTarget', $oParentTarget);
			$oViewerLocal->Assign('oUserMarked', $oUser);

            /** @var ModuleNotification $notific */
            $notific = LS::Make(ModuleNotification::class);
            /** @var ModuleNower $nower */
            $nower = LS::Make(ModuleNower::class);

			$topicLink = LS::Make(ModuleTopic::class)->GetTopicById($oTarget->getTargetId())->getUrl();
			if ($sTarget == "topic") {
				$notificationLink = $topicLink;
				$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a> упомянул вас в посте <a href='".$notificationLink."'>".$oTarget->getTitle()."</a> ";
				$notificationText = "";
				$notification = new ModuleNotification_EntityNotification(
					array(
						'user_id' => $oUser->getId(),
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
				if ($notificationCreated = $notific->createNotification($notification)) {
					$nower->PostNotification($notificationCreated);
				}
			} else {
				$notificationLink = $topicLink."#comment".$oTarget->getId();
				$notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin() . "</a> упомянул вас в <a href='".$notificationLink."'>комментарии</a> <a href='".$topicLink."'>".$oParentTarget->getTitle()."</a>";
				$notificationText = "";
				$notification = new ModuleNotification_EntityNotification(
					array(
						'user_id' => $oUser->getId(),
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
				if ($notificationCreated = $notific->createNotification($notification)) {
					$nower->PostNotificationWithComment($notificationCreated, $oTarget);
				}
			}
    	}
    }
}
