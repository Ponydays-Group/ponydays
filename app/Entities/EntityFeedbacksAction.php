<?php

namespace App\Entities;

use App\Modules\ModuleComment;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Entity;
use Engine\LS;

class EntityFeedbacksAction extends Entity
{

    //**************************************************************************************************
    public function GetUserFrom()
    {
        return LS::Make(ModuleUser::class)->GetUserById($this->getUserIdFrom());
    }

    //**************************************************************************************************
    public function getEventType()
    {
        return $this->getActionType();
    }

    //**************************************************************************************************
    public function getTarget()
    {
        $sActionType = $this->getActionType();

        if (in_array($sActionType,
            [
                'QaReply',
                'QaReplyTree',
                'TopicComment',
                'TopicCommentTree',
                'VoteTopic',
                'VoteDownTopic',
                'VoteAbstainTopic'
            ]
        )
        ) {
            return LS::Make(ModuleTopic::class)->GetTopicById($this->getDestinationObjectId());
        }

        if (in_array($sActionType, ['CommentReply', 'VoteComment', 'VoteDownComment'])) {
            return LS::Make(ModuleComment::class)->GetCommentById($this->getDestinationObjectId());
        }

        return null;
    }

    //**************************************************************************************************
    public function getActionObject()
    {
        $sActionType = $this->getActionType();

        if (in_array($sActionType, ['QaReply', 'TopicComment', 'QaReplyTree', 'TopicCommentTree', 'CommentReply'])) {
            return LS::Make(ModuleComment::class)->GetCommentById($this->getActionObjectId());
        }

        return null;
    }

    //**************************************************************************************************
    public function GetTargetCommentUrl()
    {
        $sActionType = $this->getActionType();
        if (in_array($sActionType, ['CommentReply', 'VoteComment', 'VoteDownComment'])) {
            $oTopic = $this->getTarget()->getTarget();

            return "{$oTopic->getUrl()}#comment{$this->getTarget()->getId()}";
        }

        return null;
    }

    //**************************************************************************************************
    public function GetTargetObjectCommentUrl()
    {

    }

}
