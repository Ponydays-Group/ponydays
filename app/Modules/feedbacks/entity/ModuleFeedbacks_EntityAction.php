<?php

use Engine\Entity;

class ModuleFeedbacks_EntityAction extends Entity {

	//**************************************************************************************************
	public function GetUserFrom(){
		return $this->User_GetUserById($this->getUserIdFrom());
	}

	//**************************************************************************************************
	public function getEventType(){
		return $this->getActionType();
	}

	//**************************************************************************************************
	public function getTarget(){
		$sActionType	= $this->getActionType();

		if( in_array($sActionType, array('QaReply', 'QaReplyTree', 'TopicComment', 'TopicCommentTree', 'VoteTopic', 'VoteDownTopic', 'VoteAbstainTopic')) )
			return $this->Topic_GetTopicById($this->getDestinationObjectId());

		if( in_array($sActionType, array('CommentReply', 'VoteComment', 'VoteDownComment')) )
			return $this->Comment_GetCommentById($this->getDestinationObjectId());
	}

	//**************************************************************************************************
	public function getActionObject(){
		$sActionType	= $this->getActionType();

		if( in_array($sActionType, array('QaReply', 'TopicComment', 'QaReplyTree', 'TopicCommentTree', 'CommentReply')) )
			return $this->Comment_GetCommentById($this->getActionObjectId());
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
