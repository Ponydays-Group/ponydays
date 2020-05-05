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

namespace App\Modules\Rating;

use App\Modules\Blog\Entity\ModuleBlog_EntityBlog;
use App\Modules\Comment\Entity\ModuleComment_EntityComment;
use App\Modules\Notification\Entity\ModuleNotification_EntityNotification;
use App\Modules\Notification\ModuleNotification;
use App\Modules\Nower\ModuleNower;
use App\Modules\Topic\Entity\ModuleTopic_EntityTopic;
use App\Modules\Topic\ModuleTopic;
use App\Modules\User\Entity\ModuleUser_EntityUser;
use App\Modules\User\ModuleUser;
use Engine\LS;
use Engine\Module;
use Engine\Modules\Logger\ModuleLogger;

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
	 * @throws \Exception
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
		$notification = new ModuleNotification_EntityNotification(
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
	 * @param ModuleUser_EntityUser $oUser Объект пользователя, который голосует
	 * @param ModuleTopic_EntityTopic $oTopic Объект топика
	 * @param int $iValue
	 * @param int $iValueOld
	 * @param int $iCountVote 1 при добавлении оцеки, -1 при ее удалении, 0 при переголосовании
	 * @param int $iVoteType 0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
	 * @return int
	 * @throws \Exception
	 */
	public function VoteTopic(ModuleUser_EntityUser $oUser, ModuleTopic_EntityTopic $oTopic, $iValue, $iValueOld, $iCountVote, $iVoteType) {
		$oTopic->setRating($oTopic->getRating()+$iValue);
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
		$notification = new ModuleNotification_EntityNotification(
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
		$oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
		$oUserTarget->setRating($iRatingNew);
		if (!$voted){
		    if ($iValue>0){
		        $oUserTarget->setSkill($oUserTarget->getSkill()+5.0);
		    } else {
			    $oUserTarget->setSkill($oUserTarget->getSkill()-5.0);
		    }
	    }
		LS::Make(ModuleUser::class)->Update($oUserTarget);
		return $iValue;
	}
}
