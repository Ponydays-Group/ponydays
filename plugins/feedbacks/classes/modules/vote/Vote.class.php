<?php

class PluginFeedbacks_ModuleVote extends PluginFeedbacks_Inherit_ModuleVote{

		public function AddVote(ModuleVote_EntityVote $oVote){
			if( ! $oVote->getIp() ){
				$oVote->setIp(func_getIp());
			}
			if( $this->oMapper->AddVote($oVote) ){
				$this->Cache_Delete("vote_{$oVote->getTargetType()}_{$oVote->getTargetId()}_{$oVote->getVoterId()}");
				$this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array("vote_update_{$oVote->getTargetType()}_{$oVote->getVoterId()}"));

				if( in_array($oVote->getTargetType(), array('topic', 'comment', 'user')) ){
						$oAction = Engine::GetEntity("PluginFeedbacks_ModuleFeedbacks_EntityAction");
						$oAction->setUserIdFrom($oVote->getVoterId());
						$oAction->setId(null);
						$oAction->setAddDatetime(time());
						$oAction->setDestinationObjectId($oVote->getTargetId());

						if($oVote->getTargetType() == 'topic'){
							$oTopic = $this->Topic_GetTopicById($oVote->getTargetId());
							$oAction->setUserIdTo($oTopic->getUserId());

							if($oVote->getDirection() > 0)
								$oAction->setActionType('VoteTopic');
							if($oVote->getDirection() < 0)
								$oAction->setActionType('VoteDownTopic');
							if($oVote->getDirection() == 0)
								$oAction->setActionType('VoteAbstainTopic');
						}

						if($oVote->getTargetType() == 'comment'){
							$oComment	= $this->Comment_GetCommentById($oVote->getTargetId());
							$oAction->setUserIdTo($oComment->getUserId());
							if($oVote->getDirection() > 0)
								$oAction->setActionType('VoteComment');
							else
								$oAction->setActionType('VoteDownComment');
						}

					if($oVote->getTargetType() == 'user'){
							$oAction->setUserIdTo($oVote->getTargetId());
							if($oVote->getDirection() > 0)
								$oAction->setActionType('VoteUser');
							else
								$oAction->setActionType('VoteDownUser');
						}
                        return true;
						$this->PluginFeedbacks_Feedbacks_SaveAction($oAction);

				}

				return true;
			}

			return false;
		}

}

?>