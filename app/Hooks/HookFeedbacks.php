<?php

namespace App\Hooks;

use App\Entities\EntityFeedbacksAction;
use App\Modules\ModuleFeedbacks;
use App\Modules\ModuleUser;
use Engine\Hook;
use Engine\LS;
use Engine\Modules\ModuleViewer;

class HookFeedbacks extends Hook {

	//***************************************************************************************
	public function RegisterHook(){
		$this->AddHook('comment_add_after', 			'CommentAddAfter');

		$this->AddHook('template_athead', 	'InsertUserbarItem'); 			// В строку шапки 
		$this->AddHook('template_atmenu', 			'InsertNavbarItem'); 		// Меню пользователя
		$this->AddHook('template_menu_stream_item', 		'InsertNavbarStream');  // Пункт в Stream
	}
	
	//***************************************************************************************
	// Добавление комментария
	public function CommentAddAfter($aParams, $bTreeComment = false){

		$oComment		= $aParams['oCommentNew'];
		$oCommentParent	= $aParams['oCommentParent'];
		$oTopic			= $aParams['oTopic'];

		$oAction = new EntityFeedbacksAction();
		$oAction->setUserIdFrom($oComment->getUserId());
		$oAction->setId(null);
		$oAction->setAddDatetime(time());
		$oAction->setActionObjectId($oComment->getId());

		// Ответ на комментарий
		if(!empty($oCommentParent) and !$bTreeComment){

			// Сначала добавим действие "любой комментарий в топике автора"
			$this->CommentAddAfter($aParams, true);

			if($oCommentParent->getUserId() == $oComment->getUserId()) return false;

			$oAction->setUserIdTo($oCommentParent->getUserId());
			$oAction->setDestinationObjectId($oCommentParent->getId());
			$oAction->setActionType('CommentReply');

		}elseif(empty($oCommentParent) and !$bTreeComment){
			// Ответ на топик или вопрос

			if($oTopic->getUserId() == $oComment->getUserId()) return false;

			$oAction->setActionType('TopicComment');
			$oAction->setUserIdTo($oTopic->getUserId());
			$oAction->setDestinationObjectId($oTopic->getId());

		}elseif($bTreeComment){
			// Любой комментарий в топике автора

			if($oTopic->getUserId() == $oComment->getUserId()) return false;

			$oAction->setActionType('TopicCommentTree');

			$oAction->setUserIdTo($oTopic->getUserId());
			$oAction->setDestinationObjectId($oTopic->getId());
		}

		LS::Make(ModuleFeedbacks::class)->SaveAction($oAction);
		return true;

	}

	//***************************************************************************************
	public function InsertUserbarItem(){
		if(LS::Make(ModuleUser::class)->GetUserCurrent()){
			$iUnreadActionsCount	= LS::Make(ModuleFeedbacks::class)->GetCurrentUserUnreadItemsCount();
			
			if($iUnreadActionsCount > 0){
                /** @var \Engine\Modules\ModuleViewer $viewer */
                $viewer = LS::Make(ModuleViewer::class);
                $viewer->Assign('iUnreadActionsCount', $iUnreadActionsCount);
				return $viewer->Fetch('userbar_item.tpl');
			}
		}
		return '';
	}

	//***************************************************************************************
	public function InsertNavbarItem(){
		if(LS::Make(ModuleUser::class)->GetUserCurrent()){
			$iUnreadActionsCount	= LS::Make(ModuleFeedbacks::class)->GetCurrentUserUnreadItemsCount();
            /** @var ModuleViewer $viewer */
            $viewer = LS::Make(ModuleViewer::class);
            $viewer->Assign('iUnreadActionsCount', $iUnreadActionsCount);
            return $viewer->Fetch('navbar_item.tpl');
		}
		return '';
	}
	
	//***************************************************************************************
	public function InsertNavbarStream(){
		if(LS::Make(ModuleUser::class)->GetUserCurrent()){
				return LS::Make(ModuleFeedbacks::class)->Fetch('navbar_stream.tpl');
		}
		return '';
	}
}
