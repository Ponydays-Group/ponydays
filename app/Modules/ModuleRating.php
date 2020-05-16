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

namespace App\Modules;

use App\Entities\EntityBlog;
use App\Entities\EntityComment;
use App\Entities\EntityNotification;
use App\Entities\EntityTopic;
use App\Entities\EntityUser;
use Engine\LS;
use Engine\Module;
use Engine\Modules\ModuleLogger;

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
	 * @param \App\Entities\EntityUser $oUser      Объект пользователя, который голосует
	 * @param EntityComment            $oComment   Объект комментария
	 * @param int                      $iValue     значение оценки
	 * @param int                      $iValueOld
	 * @param int                      $iCountVote 1 при добавлении оцеки, -1 при ее удалении, 0 при переголосовании
	 * @param int                      $iVoteType  0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
	 *
	 * @return int
	 * @throws \Exception
	 */
	public function VoteComment(EntityUser $oUser, EntityComment $oComment, $iValue, $iValueOld, $iCountVote, $iVoteType) {
		/**
		 * Устанавливаем рейтинг комментария
		 */
		$oComment->setRating((float)$oComment->getRating() + $iValue);
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
		$oUserComment=LS::Make(ModuleUser::class)->GetUserById($oComment->getUserId());
		$iSkillNew=$oUserComment->getSkill()+$iValue/100;
		$oUserComment->setSkill($iSkillNew);
		LS::Make(ModuleUser::class)->Update($oUserComment);

		$notificationTitle = "Пользователь <a href='".$oUser->getUserWebPath()."'>".$oUser->getLogin() . "</a>";
		$rating = $iValue;
		$notificationLink = LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId())->getUrl()."#comment".$oComment->getId();
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
		$notification = new EntityNotification(
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
		LS::Make(ModuleLogger::class)->Debug(json_encode($notification->getArrayData()));
		if($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)){
			LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oComment);
		}
		return $iValue;
	}

	/**
	 * Расчет рейтинга и силы при гоосовании за топик
	 *
	 * @param \App\Entities\EntityUser  $oUser      Объект пользователя, который голосует
	 * @param \App\Entities\EntityTopic $oTopic     Объект топика
	 * @param int                       $iValue
	 * @param int                       $iValueOld
	 * @param int                       $iCountVote 1 при добавлении оцеки, -1 при ее удалении, 0 при переголосовании
	 * @param int                       $iVoteType  0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
	 *
	 * @return int
	 * @throws \Exception
	 */
	public function VoteTopic(EntityUser $oUser, EntityTopic $oTopic, $iValue, $iValueOld, $iCountVote, $iVoteType) {
		$oTopic->setRating((float)$oTopic->getRating() + $iValue);
		/**
		 * Устанавливаем количество оценок
		 */
        $oTopic->setCountVote($oTopic->getCountVote() + $iCountVote);
		$skill=$oUser->getSkill();
		$oUserTopic=LS::Make(ModuleUser::class)->GetUserById($oTopic->getUserId());
		$iSkillNew=$oUserTopic->getSkill()+$iValue;
		$oUserTopic->setSkill($iSkillNew);
		LS::Make(ModuleUser::class)->Update($oUserTopic);

		$notificationTitle = "Пользователь <a href='".$oUser->getUserWebPath()."'>".$oUser->getLogin() . "</a>";
		if ($iVoteType == 0) {
			$notificationTitle = $notificationTitle." проголосовал за ваш пост";
		} elseif ($iVoteType == 1) {
			$notificationTitle = $notificationTitle." изменил голос за ваш пост";
		} else {
			$notificationTitle = $notificationTitle." отменил голос за ваш пост";
		}
		$notificationLink = LS::Make(ModuleTopic::class)->GetTopicById($oTopic->getId())->getUrl();
		$notificationText = "<a href='".$notificationLink."'>".$oTopic->getTitle()."</a>";
		$notification = new EntityNotification(
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
		if($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)){
			LS::Make(ModuleNower::class)->PostNotification($notificationCreated);
		}
		return $iValue;
	}
	/**
	 * Расчет рейтинга и силы при голосовании за блог
	 *
	 * @param \App\Entities\EntityUser $oUser Объект пользователя, который голосует
	 * @param \App\Entities\EntityBlog $oBlog Объект блога
	 * @param int                      $iValue
	 *
	 * @return int
	 */
	public function VoteBlog(EntityUser $oUser, EntityBlog $oBlog, $iValue) {
		$oBlog->setRating((float)$oBlog->getRating() + $iValue);
		return $iValue;
	}
	/**
	 * Расчет рейтинга и силы при голосовании за пользователя
	 *
	 * @param \App\Entities\EntityUser $oUser
	 * @param EntityUser               $oUserTarget
	 * @param int                      $iValue
	 *
	 * @return float
	 */
	public function VoteUser(EntityUser $oUser, EntityUser $oUserTarget, $iValue, $voted=false) {
		$iRatingNew=(float)$oUserTarget->getRating() + $iValue;
		$oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
		$oUserTarget->setRating($iRatingNew);
		if (!$voted){
		    if ($iValue>0){
		        $oUserTarget->setSkill((float)$oUserTarget->getSkill()+5.0);
		    } else {
			    $oUserTarget->setSkill((float)$oUserTarget->getSkill()-5.0);
		    }
	    }
		LS::Make(ModuleUser::class)->Update($oUserTarget);
		return $iValue;
	}
}
