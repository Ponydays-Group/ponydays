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
 * Модуль управления рейтингами и силой
 *
 * @package modules.rating
 * @since 1.0
 */
class ModuleRating extends Module {

	/**
	 * Инициализация модуля
	 *
	 */
	public function Init() {

	}

	/**
	 * Расчет рейтинга при голосовании за комментарий
	 *
	 * @param ModuleUser_EntityUser $oUser Объект пользователя, который голосует
	 * @param ModuleComment_EntityComment $oComment Объект комментария
	 * @param int $iValue значение оценки
	 * @param int $iValueOld
	 * @param int $iCountVote 1 при добавлении оцеки, -1 при ее удалении, 0 при переголосовании
	 * @param int $iVoteType 0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
	 * @return int
	 * @throws Exception
	 */
	public function VoteComment(ModuleUser_EntityUser $oUser, ModuleComment_EntityComment $oComment, $iValue, $iValueOld, $iCountVote, $iVoteType) {
		/**
		 * Устанавливаем рейтинг комментария
		 */
		$oComment->setRating($oComment->getRating()+$iValue);
		/**
		 * Устанавливаем количество оценок
		 */
   		$oComment->setCountVote($oComment->getCountVote() + $iCountVote);
		/**
		 * Начисляем силу автору коммента, используя логарифмическое распределение
		 */
		$skill=$oUser->getSkill();
		/**
		 * Сохраняем силу
		 */
		$oUserComment=$this->User_GetUserById($oComment->getUserId());
		$iSkillNew=$oUserComment->getSkill()+$iValue/100;
		$oUserComment->setSkill($iSkillNew);
		$this->User_Update($oUserComment);

		$notificationTitle = "Пользователь <a href='".$oUser->getUserWebPath()."'>".$oUser->getLogin() . "</a>";
		$rating = $iValue;
		$notificationLink = $this->Topic_GetTopicById($oComment->getTargetId())->getUrl()."#comment".$oComment->getId();
		if ($iVoteType == 0) {
			$notificationTitle = $notificationTitle." проголосовал за ваш <a href='".$notificationLink."'>комментарий</a>";
		} elseif ($iVoteType == 1) {
			$notificationTitle = $notificationTitle." изменил голос за ваш <a href='".$notificationLink."'>комментарий</a>";
			$rating = $rating / 2;
		} else {
			$notificationTitle = $notificationTitle." отменил голос за ваш <a href='".$notificationLink."'>комментарий</a>";
			$rating = 0;
		}
		$notificationText = "";
		$notification = Engine::GetEntity(
			'Notification',
			array(
				'user_id' => $oUserComment->getId(),
				'text' => $notificationText,
				'title' => $notificationTitle,
				'link' => $notificationLink,
				'rating' => $rating,
				'notification_type' => 10,
				'target_type' => 'comment',
				'target_id' => $oComment->getId(),
				'sender_user_id' => $oUser->getId(),
				'group_target_type' => $oComment->getTargetType(),
				'group_target_id' => $oComment->getTargetId()
			)
		);
		$this->Logger_Debug(json_encode($notification->getArrayData()));
		if($notificationCreated = $this->Notification_createNotification($notification)){
			$this->Nower_PostNotificationWithComment($notificationCreated, $oComment);
		}
		return $iValue;
	}

	/**
	 * Расчет рейтинга и силы при гоосовании за топик
	 *
	 * @param ModuleUser_EntityUser $oUser Объект пользователя, который голосует
	 * @param ModuleTopic_EntityTopic $oTopic Объект топика
	 * @param int $iValue
	 * @param int $iValueOld
	 * @param int $iCountVote 1 при добавлении оцеки, -1 при ее удалении, 0 при переголосовании
	 * @param int $iVoteType 0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
	 * @return int
	 * @throws Exception
	 */
	public function VoteTopic(ModuleUser_EntityUser $oUser, ModuleTopic_EntityTopic $oTopic, $iValue, $iValueOld, $iCountVote, $iVoteType) {
		$oTopic->setRating($oTopic->getRating()+$iValue);
		/**
		 * Устанавливаем количество оценок
		 */
        $oTopic->setCountVote($oTopic->getCountVote() + $iCountVote);
		$skill=$oUser->getSkill();
		$oUserTopic=$this->User_GetUserById($oTopic->getUserId());
		$iSkillNew=$oUserTopic->getSkill()+$iValue;
		$oUserTopic->setSkill($iSkillNew);
		$this->User_Update($oUserTopic);

		$notificationTitle = "Пользователь <a href='".$oUser->getUserWebPath()."'>".$oUser->getLogin() . "</a>";
		if ($iVoteType == 0) {
			$notificationTitle = $notificationTitle." проголосовал за ваш пост";
		} elseif ($iVoteType == 1) {
			$notificationTitle = $notificationTitle." изменил голос за ваш пост";
		} else {
			$notificationTitle = $notificationTitle." отменил голос за ваш пост";
		}
		$notificationLink = $this->Topic_GetTopicById($oTopic->getId())->getUrl();
		$notificationText = "<a href='".$notificationLink."'>".$oTopic->getTitle()."</a>";
		$notification = Engine::GetEntity(
			'Notification',
			array(
				'user_id' => $oUserTopic->getId(),
				'text' => $notificationText,
				'title' => $notificationTitle,
				'link' => $notificationLink,
				'rating' => $iValue,
				'notification_type' => 11,
				'target_type' => "topic",
				'target_id' => $oTopic->getId(),
				'sender_user_id' => $oUser->getId(),
				'group_target_type' => 'topic',
				'group_target_id' => $oTopic->getId()
			)
		);
		if($notificationCreated = $this->Notification_createNotification($notification)){
			$this->Nower_PostNotification($notificationCreated);
		}
		return $iValue;
	}
	/**
	 * Расчет рейтинга и силы при голосовании за блог
	 *
	 * @param ModuleUser_EntityUser $oUser	Объект пользователя, который голосует
	 * @param ModuleBlog_EntityBlog $oBlog	Объект блога
	 * @param int $iValue
	 * @return int
	 */
	public function VoteBlog(ModuleUser_EntityUser $oUser, ModuleBlog_EntityBlog $oBlog, $iValue) {
		$oBlog->setRating($oBlog->getRating()+$iValue);
		return $iValue;
	}
	/**
	 * Расчет рейтинга и силы при голосовании за пользователя
	 *
	 * @param ModuleUser_EntityUser $oUser
	 * @param ModuleUser_EntityUser $oUserTarget
	 * @param int $iValue
	 * @return float
	 */
	public function VoteUser(ModuleUser_EntityUser $oUser, ModuleUser_EntityUser $oUserTarget, $iValue, $voted=false) {
		$iRatingNew=$oUserTarget->getRating()+$iValue;
		$oUserCurrent = $this->User_GetUserCurrent();
		$oUserTarget->setRating($iRatingNew);
		if (!$voted){
		    if ($iValue>0){
		        $oUserTarget->setSkill($oUserTarget->getSkill()+5.0);
		    } else {
			    $oUserTarget->setSkill($oUserTarget->getSkill()-5.0);
		    }
	    }
		$this->User_Update($oUserTarget);
		return $iValue;
	}
}
?>
