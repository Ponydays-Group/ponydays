<?php

namespace App\Modules\Notification;

use App\Modules\Comment\Mapper\ModuleComment_MapperComment;
use App\Modules\Notification\Mapper\ModuleNotification_MapperNotification;
use App\Modules\User\Entity\ModuleUser_EntityUser;
use App\Modules\User\ModuleUser;
use Engine\Engine;
use Engine\LS;
use Engine\Module;

/**
 * Created by PhpStorm.
 * User: frumscepend
 * Date: 9/21/18
 * Time: 11:29 PM
 */

class ModuleNotification extends Module {
	/**
	 * Объект маппера
	 *
	 * @var ModuleComment_MapperComment
	 */
	protected $oMapper;
	/**
	 * Объект текущего пользователя
	 *
	 * @var ModuleUser_EntityUser|null
	 */
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		$this->oMapper=Engine::MakeMapper(ModuleNotification_MapperNotification::class);
		$this->oUserCurrent=LS::Make(ModuleUser::class)->GetUserCurrent();
	}

	public function createNotification($notification){
		$sId=$this->oMapper->createNotification($notification);
		if ($sId) {
			return $this->oMapper->getNotificationById($sId);
		}
		return false;
	}

	public function getNotificationById($notificationId){
		return $this->oMapper->getNotificationById($notificationId);
	}

	public function getNotification($userId, $page, $count, $types) {
		if ($types) {
			return $this->oMapper->getNotificationsFiltered($userId, $page, $count, $types);
		} else {
			return $this->oMapper->getNotifications($userId, $page, $count);
		}
	}
}
