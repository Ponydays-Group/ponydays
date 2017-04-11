<?php

class HookFeedbacks extends Hook{

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

		$oAction = Engine::GetEntity("ModuleFeedbacks_EntityAction");
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

			if( in_array('qatopic', $this->Plugin_GetActivePlugins()) ){
				if($oTopic->getBlog()->getUrl() == Config::Get('plugin.qatopic.qatopic_blog_url')){
					$oAction->setActionType('QaReply');
				}
			}

			$oAction->setUserIdTo($oTopic->getUserId());
			$oAction->setDestinationObjectId($oTopic->getId());

		}elseif($bTreeComment){
			// Любой комментарий в топике автора

			if($oTopic->getUserId() == $oComment->getUserId()) return false;

			$oAction->setActionType('TopicCommentTree');

			if( in_array('qatopic', $this->Plugin_GetActivePlugins()) ){
				if($oTopic->getBlog()->getUrl() == Config::Get('plugin.qatopic.qatopic_blog_url')){
					$oAction->setActionType('QaReplyTree');
				}
			}

			$oAction->setUserIdTo($oTopic->getUserId());
			$oAction->setDestinationObjectId($oTopic->getId());
		}

		$this->Feedbacks_SaveAction($oAction);
		return true;

	}

	//***************************************************************************************
	public function InsertUserbarItem(){
		if($this->User_GetUserCurrent()){
			$iUnreadActionsCount	= $this->Feedbacks_GetCurrentUserUnreadItemsCount();
			
			if($iUnreadActionsCount > 0){
				$this->Viewer_Assign('iUnreadActionsCount', $iUnreadActionsCount);
				return $this->Viewer_Fetch('userbar_item.tpl');
			}
		}
	}

	//***************************************************************************************
	public function InsertNavbarItem(){
		if($this->User_GetUserCurrent()){
			$iUnreadActionsCount	= $this->Feedbacks_GetCurrentUserUnreadItemsCount();

				$this->Viewer_Assign('iUnreadActionsCount', $iUnreadActionsCount);
				return $this->Viewer_Fetch('navbar_item.tpl');
		}
	}
	
	//***************************************************************************************
	public function InsertNavbarStream(){
		if($this->User_GetUserCurrent()){
				return $this->Viewer_Fetch('navbar_stream.tpl');
			}
		}
	

}

?>
