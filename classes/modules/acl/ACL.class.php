<?php
/*-------------------------------------------------------
*
*   LiveStreet Engine Social Networking
*   Copyright © 2008 Mzhelskiy Maxim
*
*--------------------------------------------------------
*
*   Official site: www.livestreet.ru
*   Contact e-mail: rus.engine@gmail.com
*
*   GNU General Public License, version 2:
*   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
---------------------------------------------------------
*/

/**
 * ACL(Access Control List)
 * Модуль для разруливания ограничений по карме/рейтингу юзера
 *
 * @package modules.acl
 * @since 1.0
 */
class ModuleACL extends Module {
	/**
	 * Коды ответов на запрос о возможности
	 * пользователя голосовать за блог
	 */
	const CAN_VOTE_BLOG_FALSE = 0;
	const CAN_VOTE_BLOG_TRUE = 1;
	const CAN_VOTE_BLOG_ERROR_CLOSE = 2;
	/**
	 * Коды механизма удаления блога
	 */
	const CAN_DELETE_BLOG_EMPTY_ONLY  = 1;
	const CAN_DELETE_BLOG_WITH_TOPICS = 2;
	
	// Ничего не проверять
    const ACL_EC_NO_CHECK=0;
    
    // Минисальная проверка на то, что пользователь является автором коммента 
    const ACL_EC_BASIC=1;
    
    // Проверка на максимальное количество редактирований
    const ACL_EC_CHECK_MAX_EDIT_COUNT=2;
    
    // Проверка на максимальное прошедшее время
    const ACL_EC_CHECK_MAX_EDIT_PERIOD=4;
    
    // Проверка на запрет редактирования комментов с ответами
    const ACL_EC_CHECK_DENY_WITH_ANSWERS=8;
    
    // Проверка на достаточный уровень рейтинга пользователя при редактировании
    const ACL_EC_CHECK_USER_RATING=16;

	/**
	 * Инициализация модуля
	 *
	 */
	public function Init() {

	}

	public function CanAddFavourite(ModuleFavourite_EntityFavourite $oFavourite, ModuleUser_EntityUser $oUser){
		if ($oFavourite->getTargetType() == "talk"){
			$oTalk = $this->ModuleTalk_GetTalkById($oFavourite->getTargetId());
			if ($oUser->getId() && $oTalk->getTalkUser()){
				    return true;
			}
		} elseif ($oFavourite->getTargetType() == "topic") {
			$oTopic = $this->ModuleTopic_GetTopicById($oFavourite->getTargetId());
			if(in_array($oTopic->getBlogId(), $this->ModuleBlog_GetAccessibleBlogsByUser($oUser)) or $oTopic->getBlog()->getType() == "open"){
			    return true;
		    }
		} elseif ($oFavourite->getTargetType() == "comment") {
			$oComment = $this->ModuleComment_GetCommentById($oFavourite->getTargetId());
			if (in_array($oComment->getTarget()->getBlogId(), $this->ModuleBlog_GetAccessibleBlogsByUser($oUser)) or $oComment->getTarget()->getBlog()->getType() == "open"){
			    return true;
		    }
		}
		return false;
	}
	/**
	 * Проверяет может ли пользователь создавать блоги
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function CanCreateBlog(ModuleUser_EntityUser $oUser) {
		if ($oUser->getRating()>=Config::Get('acl.create.blog.rating')) {
			return true;
		}
		return false;
	}
	/**
	 * Проверяет может ли пользователь создавать топики в определенном блоге
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @param ModuleBlog_EntityBlog $oBlog	Блог
	 * @return bool
	 */
	public function CanAddTopic(ModuleUser_EntityUser $oUser, ModuleBlog_EntityBlog $oBlog) {
		/**
		 * Если юзер является создателем блога то разрешаем ему постить
		 */
		if ($oUser->getId()==$oBlog->getOwnerId()) {
			return true;
		}
		/**
		 * Если рейтинг юзера больше либо равен порогу постинга в блоге то разрешаем постинг
		 */
		if ($oUser->getRating()>=$oBlog->getLimitRatingTopic()) {
			return true;
		}
		return false;
	}
	/**
	 * Проверяет может ли пользователь создавать комментарии
	 *
	 * @param  ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function CanPostComment(ModuleUser_EntityUser $oUser) {
		if ($oUser->getRating()>=Config::Get('acl.create.comment.rating')) {
			return true;
		}
		return false;
	}
	/**
	 * Проверяет может ли пользователь создавать комментарии по времени(например ограничение максимум 1 коммент в 5 минут)
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function CanPostCommentTime(ModuleUser_EntityUser $oUser) {
		if (Config::Get('acl.create.comment.limit_time')>0 and $oUser->getDateCommentLast()) {
			$sDateCommentLast=strtotime($oUser->getDateCommentLast());
			if ($oUser->getRating()<Config::Get('acl.create.comment.limit_time_rating') and ((time()-$sDateCommentLast)<Config::Get('acl.create.comment.limit_time'))) {
				return false;
			}
		}
		return true;
	}
	/**
	 * Проверяет может ли пользователь создавать топик по времени
	 *
	 * @param  ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function CanPostTopicTime(ModuleUser_EntityUser $oUser) {
		// Для администраторов ограничение по времени не действует
		if($oUser->isAdministrator()
			or Config::Get('acl.create.topic.limit_time')==0
			or $oUser->getRating()>=Config::Get('acl.create.topic.limit_time_rating'))
			return true;

		/**
		 * Проверяем, если топик опубликованный меньше чем acl.create.topic.limit_time секунд назад
		 */
		$aTopics=$this->Topic_GetLastTopicsByUserId($oUser->getId(),Config::Get('acl.create.topic.limit_time'));
		if(isset($aTopics['count']) and $aTopics['count']>0){
			return false;
		}
		return true;
	}
	/**
	 * Проверяет может ли пользователь отправить инбокс по времени
	 *
	 * @param  ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function CanSendTalkTime(ModuleUser_EntityUser $oUser) {
		// Для администраторов ограничение по времени не действует
		if($oUser->isAdministrator()
			or Config::Get('acl.create.talk.limit_time')==0
			or $oUser->getRating()>=Config::Get('acl.create.talk.limit_time_rating'))
			return true;

		/**
		 * Проверяем, если топик опубликованный меньше чем acl.create.topic.limit_time секунд назад
		 */
		$aTalks=$this->Talk_GetLastTalksByUserId($oUser->getId(),Config::Get('acl.create.talk.limit_time'));
		if(isset($aTalks['count']) and $aTalks['count']>0){
			return false;
		}
		return true;
	}
	/**
	 * Проверяет может ли пользователь создавать комментарии к инбоксу по времени
	 *
	 * @param  ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function CanPostTalkCommentTime(ModuleUser_EntityUser $oUser) {
		/**
		 * Для администраторов ограничение по времени не действует
		 */
		if($oUser->isAdministrator()
			or Config::Get('acl.create.talk_comment.limit_time')==0
			or $oUser->getRating()>=Config::Get('acl.create.talk_comment.limit_time_rating'))
			return true;
		/**
		 * Проверяем, если топик опубликованный меньше чем acl.create.topic.limit_time секунд назад
		 */
		$aTalkComments=$this->Comment_GetCommentsByUserId($oUser->getId(),'talk',1,1);
		/**
		 * Если комментариев не было
		 */
		if(!is_array($aTalkComments) or $aTalkComments['count']==0){
			return true;
		}
		/**
		 * Достаем последний комментарий
		 */
		$oComment = array_shift($aTalkComments['collection']);
		$sDate = strtotime($oComment->getDate());

		if($sDate and ((time()-$sDate)<Config::Get('acl.create.talk_comment.limit_time'))) {
			return false;
		}
		return true;
	}
	/**
	 * Проверяет может ли пользователь создавать комментарии используя HTML
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function CanUseHtmlInComment(ModuleUser_EntityUser $oUser) {
		return true;
	}
	/**
	 * Проверяет может ли пользователь голосовать за конкретный комментарий
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @param ModuleComment_EntityComment $oComment	Комментарий
	 * @return bool
	 */
	public function CanVoteComment(ModuleUser_EntityUser $oUser, ModuleComment_EntityComment $oComment) {
		//if ($oUser->getRating()<Config::Get('acl.vote.comment.rating')) {
		//	return false;
		//}
		if ($oComment->getTargetType() == 'talk'){
			return false;
		}
		if (!in_array($oComment->getTarget()->getBlogId(), $this->ModuleBlog_GetAccessibleBlogsByUser($oUser)) and $oComment->getTarget()->getBlog()->getType() != "open"){
			return false;
		}
		return true;
	}
	/**
	 * Проверяет может ли пользователь голосовать за конкретный блог
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @param ModuleBlog_EntityBlog $oBlog	Блог
	 * @return bool
	 */
	public function CanVoteBlog(ModuleUser_EntityUser $oUser, ModuleBlog_EntityBlog $oBlog) {
		/**
		 * Если блог закрытый, проверяем является ли пользователь его читателем
		 */
		if($oBlog->getType()=='close') {
			$oBlogUser = $this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(),$oUser->getId());
			if(!$oBlogUser || $oBlogUser->getUserRole()<ModuleBlog::BLOG_USER_ROLE_GUEST) {
				return self::CAN_VOTE_BLOG_ERROR_CLOSE;
			}
		}
		if ($oUser->getRating()>=Config::Get('acl.vote.blog.rating')) {
			return self::CAN_VOTE_BLOG_TRUE;
		}
		return self::CAN_VOTE_BLOG_FALSE;
	}
	/**
	 * Проверяет может ли пользователь голосовать за конкретный топик
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @param ModuleTopic_EntityTopic $oTopic	Топик
	 * @return bool
	 */
	public function CanVoteTopic(ModuleUser_EntityUser $oUser, ModuleTopic_EntityTopic $oTopic) {
		if ($oUser->getRating()<Config::Get('acl.vote.topic.rating')) {
			return false;
		}
		if (!in_array($oTopic->getBlogId(), $this->ModuleBlog_GetAccessibleBlogsByUser($oUser)) and $oTopic->getBlog()->getType() != "open"){
			return false;
		}
		return true;
	}
	/**
	 * Проверяет может ли пользователь голосовать за конкретного пользователя
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @param ModuleUser_EntityUser $oUserTarget	Пользователь за которого голосуем
	 * @return bool
	 */
	public function CanVoteUser(ModuleUser_EntityUser $oUser, ModuleUser_EntityUser $oUserTarget) {
		if ($oUser->getRating()>=Config::Get('acl.vote.user.rating')) {
			return true;
		}
		return false;
	}
	/**
	 * Проверяет можно ли юзеру слать инвайты
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function CanSendInvite(ModuleUser_EntityUser $oUser) {
		if ($this->User_GetCountInviteAvailable($oUser)==0) {
			return false;
		}
		return true;
	}
	/**
	 * Проверяет можно или нет юзеру постить в данный блог
	 *
	 * @param ModuleBlog_EntityBlog $oBlog	Блог
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 */
	public function IsAllowBlog($oBlog,$oUser) {
		if ($oUser->isAdministrator()) {
			return true;
		}
		if ($oUser->getRating()<=Config::Get('acl.create.topic.limit_rating')) {
			return false;
		}
		if ($oBlog->getOwnerId()==$oUser->getId()) {
			return true;
		}
		if ($oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(),$oUser->getId())) {
			if ($this->ACL_CanAddTopic($oUser,$oBlog) or $oBlogUser->getIsAdministrator() or $oBlogUser->getIsModerator()) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Проверяет можно или нет пользователю редактировать данный топик
	 *
	 * @param  ModuleTopic_EntityTopic $oTopic	Топик
	 * @param  ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function IsAllowEditTopic($oTopic,$oUser) {
		/**
		 * Разрешаем если это админ сайта или автор топика
		 */
		if ($oTopic->getUserId()==$oUser->getId() or $oUser->isAdministrator()) {
			return true;
		}
		/**
		 * Если автор(смотритель) блога
		 */
		if ($oTopic->getBlog()->getOwnerId()==$oUser->getId()) {
			return true;
		}

                if ($oUser->isGlobalModerator() && $oTopic->getBlog()->getType() == "open" ) {
			return true;
		}
		/**
		 * Если модер или админ блога
		 */
		if ($this->User_GetUserCurrent() and $this->User_GetUserCurrent()->getId()==$oUser->getId()) {
			/**
			 * Для авторизованного пользователя данный код будет работать быстрее
			 */
			if ($oTopic->getBlog()->getUserIsAdministrator() or $oTopic->getBlog()->getUserIsModerator()) {
				return true;
			}
		} else {
			$oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oTopic->getBlogId(),$oUser->getId());
			if ($oBlogUser and ($oBlogUser->getIsModerator() or $oBlogUser->getIsAdministrator())) {
				return true;
			}
		}

		return false;
	}
	/**
	 * Проверяет можно или нет пользователю удалять данный топик
	 *
	 * @param ModuleTopic_EntityTopic $oTopic	Топик
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function IsAllowDeleteTopic($oTopic,$oUser) {
		/**
		 * Разрешаем если это админ сайта или автор топика
		 */
		if (($oTopic->getUserId()==$oUser->getId() && !$oTopic->isControlLocked()) or $oUser->getIsAdministrator()) {
			return true;
		}
		if ($oUser->isGlobalModerator() and $oTopic->getBlog()->getType() == "open" ) {
                        return true;
                }
		/**
		 * Если автор(смотритель) блога
		 */
		if ($oTopic->getBlog()->getOwnerId()==$oUser->getId()) {
			return true;
		}
		/**
		 * Если модер или админ блога
		 */
		if ($this->User_GetUserCurrent() and $this->User_GetUserCurrent()->getId()==$oUser->getId()) {
			/**
			 * Для авторизованного пользователя данный код будет работать быстрее
			 */
			if ($oTopic->getBlog()->getUserIsAdministrator() or $oTopic->getBlog()->getUserIsModerator()) {
				return true;
			}
		} else {
			$oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oTopic->getBlogId(),$oUser->getId());
			if ($oBlogUser and ($oBlogUser->getIsModerator() or $oBlogUser->getIsAdministrator())) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Проверяет можно или нет пользователю удалять данный блог
	 *
	 * @param ModuleBlog_EntityBlog $oBlog	Блог
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function IsAllowDeleteBlog($oBlog,$oUser) {
		/**
		 * Разрешаем если это админ сайта или автор блога
		 */
		if ($oUser->isAdministrator()) {
			return self::CAN_DELETE_BLOG_WITH_TOPICS;
		}
		/**
		 * Разрешаем удалять администраторам блога и автору, но только пустой
		 */
		if($oBlog->getOwnerId()==$oUser->getId()) {
			return self::CAN_DELETE_BLOG_EMPTY_ONLY;
		}

		$oBlogUser=$this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(),$oUser->getId());
		if($oBlogUser and $oBlogUser->getIsAdministrator()) {
			return self::CAN_DELETE_BLOG_EMPTY_ONLY;
		}
		return false;
	}
	/**
	 * Проверяет может ли пользователь удалить комментарий
	 *
	 * @param  ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function CanDeleteComment($oUser, $oComment) {
		if ($oUser->isGlobalModerator()) {
			if ($oComment->getTargetType() != 'talk') {
				if ($oComment->getTarget()->getBlog()->getType() != 'open'){
					return false;
				}
			}
		}
		if (!$oUser || !$oUser->isAdministrator() and !$oUser->isGlobalModerator()) {
			return false;
		}
		return true;
	}
	/**
	 * Проверяет может ли пользователь публиковать на главной
	 *
	 * @param  ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function IsAllowPublishIndex(ModuleUser_EntityUser $oUser) {
		if ($oUser->isAdministrator()) {
			return true;
		}
		return false;
	}
	/**
	 * Проверяет можно или нет пользователю редактировать данный блог
	 *
	 * @param  ModuleBlog_EntityBlog $oBlog	Блог
	 * @param  ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function IsAllowEditBlog($oBlog,$oUser) {
		if ($oUser->isAdministrator()) {
			return true;
		}
		/**
		 * Разрешаем если это создатель блога
		 */
		if ($oBlog->getOwnerId() == $oUser->getId()) {
			return true;
		}
		/**
		 * Явлется ли авторизованный пользователь администратором блога
		 */
		$oBlogUser = $this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());

		if ($oBlogUser && $oBlogUser->getIsAdministrator()) {
			return true;
		}
		return false;
	}
	/**
	 * Проверяет можно или нет пользователю управлять пользователями блога
	 *
	 * @param  ModuleBlog_EntityBlog $oBlog	Блог
	 * @param  ModuleUser_EntityUser $oUser	Пользователь
	 * @return bool
	 */
	public function IsAllowAdminBlog($oBlog,$oUser) {
		if ($oUser->isAdministrator()) {
			return true;
		}
		/**
		 * Разрешаем если это создатель блога
		 */
		if ($oBlog->getOwnerId() == $oUser->getId()) {
			return true;
		}
		/**
		 * Явлется ли авторизованный пользователь администратором блога
		 */
		$oBlogUser = $this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(), $oUser->getId());
		if ($oBlogUser && $oBlogUser->getIsAdministrator()) {
			return true;
		}
		return false;
	}
	/**
	 * Проверка на ограничение по времени на постинг на стене
	 *
	 * @param ModuleUser_EntityUser $oUser	Пользователь
	 * @param ModuleWall_EntityWall $oWall	Объект сообщения на стене
	 * @return bool
	 */
	public function CanAddWallTime($oUser,$oWall) {
		/**
		 * Для администраторов ограничение по времени не действует
		 */
		if($oUser->isAdministrator()
			or Config::Get('acl.create.wall.limit_time')==0
			or $oUser->getRating()>=Config::Get('acl.create.wall.limit_time_rating')) {
			return true;
		}
		if ($oWall->getUserId()==$oWall->getWallUserId()) {
			return true;
		}
		/**
		 * Получаем последнее сообщение
		 */
		$aWall=$this->Wall_GetWall(array('user_id'=>$oWall->getUserId()),array('id'=>'desc'),1,1,array());
		/**
		 * Если сообщений нет
		 */
		if($aWall['count']==0){
			return true;
		}

		$oWallLast = array_shift($aWall['collection']);
		$sDate = strtotime($oWallLast->getDateAdd());
		if($sDate and ((time()-$sDate)<Config::Get('acl.create.wall.limit_time'))) {
			return false;
		}
		return true;
	}
	public function IsAllowLockTopicControl($oTopic,$oUser) {
		if(!$oUser) return false;
		if(
			$oUser->isAdministrator()
//			($oUser->isGlobalModerator() && $oTopic->getBlog()->getType() == 'open')
		) return true;
		$oBlog = $oTopic->getBlog();
		if(
			$oBlog->getUserIsAdministrator() ||
			$oBlog->getUserIsModerator() ||
			$oBlog->getOwnerId() == $oUser->getId()
		) return true;
		return false;
	}
	public function IsAllowControlTopic($oTopic,$oUser) {
		return ($this::IsAllowEditTopic($oTopic,$oUser) && !$oTopic->isControlLocked()) || $this::IsAllowLockTopicControl($oTopic,$oUser);
	}
	public function CheckSimpleAccessLevel($req, $oUser, $oTarget, $sTargetType) {
		if($req == 8) return true;
		if($req == 0) return false;
		if(!$oUser && $req < 8) return false;
		if($req == 7) return true;
		if($req >= 1) {
			if($oUser->isAdministrator()) return true;
			if($req >= 2) {
				if($sTargetType === 'comment') {
					if($oTarget->getTargetType() === 'topic') {
						$oTopic = $oTarget->getTarget();
					} else if($oTarget->getTargetType() === 'talk') {
						$oTalk = $oTarget->getTarget();
					}
				} else if($sTargetType === 'topic') {
					$oTopic = $oTarget;
				} else if($sTargetType === 'talk') {
					$oTalk = $oTarget;
				} else if($sTargetType === 'blog') {
					$oBlog = $oTarget;
				} else if($sTargetType === 'user') {
					$oTargetUser = $oTarget;
				}
				if(isset($oTopic) || isset($oBlog)) {
					if(!isset($oBlog) && $oTopic) $oBlog = $oTopic->getBlog();
					if($oUser->isGlobalModerator() && $oBlog->getType() == 'open') return true;
					if($req >= 3 && ($oBlog->getUserIsAdministrator() || $oBlog->getOwnerId() == $oUser->getId())) return true;
					if($req >= 4 && $oBlog->getUserIsModerator()) return true;
					if($req >= 5) {
						if(in_array($oBlog->getId(), $this->ModuleBlog_GetAccessibleBlogsByUser($oUser)) ||
							(
								$oBlog->getType() === 'open' && (
									!($oBlogUser = $this->Blog_GetBlogUserByBlogIdAndUserId($oBlog->getId(),$oUser->getId())) ||
									$oBlogUser->getUserRole() != ModuleBlog::BLOG_USER_ROLE_BAN
								)
							)
						) {
							if($req == 6) return true;
							if($req == 5 && $oTarget->getUserId() == $oUser->getId()) return true;
						}
					}
				}
				if(isset($oTalk)) {
					if($req >= 5 && $this->ModuleTalk_GetTalkUser($oTalk->getId(),$oUser->getId())) {
						if($req == 6) return true;
						if($req == 5 && $oTarget->getUserId() == $oUser->getId()) return true;
					}
				}
				if(isset($oTargetUser)) {
					// ...
				}
			}
		}
		return false;
	}

    function UserCanEditComment($oUser,$oComment,$iCheckMask=0)
    {

        if (!$iCheckMask)
            return true;
        
        if (!$oUser || !$oComment)
            return $this->Lang_Get('not_access');
        
        if ($oUser->isAdministrator())
            return true;
        
        $aUsers=Config::Get('comment_editors');
        if (is_array($aUsers) && in_array($oUser->getId(),$aUsers))
            return true;
        if ($oComment->getTargetType() != 'talk') {
            if($oComment->getTarget()->getBlog()->getType() != 'personal') {
                $oBlog = $oComment->getTarget()->getBlog()->Blog_GetBlogUserByBlogIdAndUserId($oComment->getTarget()->getBlog()->getId(), $oUser->getId());
                if ($oBlog) {
                    if ($oBlog->getIsModerator() or $oBlog->getIsAdministrator())
                        return true;
                }
                if ($oComment->getTarget()->getBlog()->getOwnerId() == $oUser->getId()){
                    return true;
                }
            }
        }

        if ($oUser->getUserIsAdministrator())
            return true;
        if ($oComment->getTargetType() != "talk"){
	        if ($oUser->isGlobalModerator() and $oComment->getTarget()->getBlog()->getType() == "open") {
	            return true;
	        }
        }
        if ($oUser->getUserId() != $oComment->getUserId() && !$oUser->isAdministrator())
            return $this->Lang_Get('not_access');
        
        if ($iCheckMask & ModuleACL::ACL_EC_CHECK_MAX_EDIT_COUNT != 0 && Config::Get('max_edit_count'))
        {
            if ($oComment->getEditCount() > Config::Get('max_edit_count'))
                return $this->Lang_Get('err_max_edit_count');
        }
        
        if ($iCheckMask & ModuleACL::ACL_EC_CHECK_MAX_EDIT_PERIOD != 0 && Config::Get('max_edit_period'))
        {
            if (strtotime('+' . Config::Get('max_edit_period') . ' second', strtotime($oComment->getEditDate())) < time())
                return $this->Lang_Get('err_max_edit_period');
        }
        
        if ($iCheckMask & ModuleACL::ACL_EC_CHECK_DENY_WITH_ANSWERS != 0 && Config::Get('deny_with_answers'))
        {
            if ($this->Editcomment_HasAnswers($oComment->getId()))
                return $this->Lang_Get('err_deny_with_asnwers');
        }
        
        if ($iCheckMask & ModuleACL::ACL_EC_CHECK_USER_RATING != 0)
        {
            if ($oUser->getRating() < Config::Get('min_user_rating'))
                return $this->Lang_Get('err_min_user_rating');
        }
        
        return true;
    }
}
?>
