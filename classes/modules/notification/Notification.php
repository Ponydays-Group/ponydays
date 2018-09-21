<?php
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
		$this->oMapper=Engine::GetMapper(__CLASS__);
		$this->oUserCurrent=$this->User_GetUserCurrent();
	}

	public function createNotification($notification){
		$sId=$this->oMapper->createNotification($notification);
		if ($sId) {
			return $this->oMapper->getNotificationById($sId);
		}
		return false;
	}

}