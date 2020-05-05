<?php

namespace App\Modules\Feedbacks\Entity;

use Engine\Entity;
use Engine\LS;
use App\Modules\Comment\ModuleComment;
use App\Modules\Topic\ModuleTopic;
use App\Modules\User\ModuleUser;

class ModuleFeedbacks_EntityAction extends Entity {

	//**************************************************************************************************
	public function GetUserFrom(){
		return LS::Make(ModuleUser::class)->GetUserById($this->getUserIdFrom());
	}

	//**************************************************************************************************
	public function getEventType(){
		return $this->getActionType();
	}

	//**************************************************************************************************
	public function getTarget(){
		$sActionType	= $this->getActionType();

		if( in_array($sActionType, array('QaReply', 'QaReplyTree', 'TopicComment', 'TopicCommentTree', 'VoteTopic', 'VoteDownTopic', 'VoteAbstainTopic')) )
			return LS::Make(ModuleTopic::class)->GetTopicById($this->getDestinationObjectId());

		if( in_array($sActionType, array('CommentReply', 'VoteComment', 'VoteDownComment')) )
			return LS::Make(ModuleComment::class)->GetCommentById($this->getDestinationObjectId());
	}

	//**************************************************************************************************
	public function getActionObject(){
		$sActionType	= $this->getActionType();

		if( in_array($sActionType, array('QaReply', 'TopicComment', 'QaReplyTree', 'TopicCommentTree', 'CommentReply')) )
			return LS::Make(ModuleComment::class)->GetCommentById($this->getActionObjectId());
	}

	//**************************************************************************************************
	public function GetTargetCommentUrl(){
		$sActionType	= $this->getActionType();
		if( in_array($sActionType, array('CommentReply', 'VoteComment', 'VoteDownComment')) ){
			$oTopic	= $this->getTarget()->getTarget();
			return "{$oTopic->getUrl()}#comment{$this->getTarget()->getId()}";
		}
	}

	//**************************************************************************************************
	public function GetTargetObjectCommentUrl(){

	}

}
