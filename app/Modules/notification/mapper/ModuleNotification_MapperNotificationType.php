<?php

use Engine\Config;
use Engine\Engine;
use Engine\Mapper;

/**
 * Маппер типов уведомлений, работа с базой данных
 *
 * @package modules.notification
 */
class ModuleNotification_MapperNotificationType extends Mapper {

	/**
	 * Получение всех типов уведомлений
	 * @return array
	 * @throws Exception
	 */
	public function getNotificationTypes(){
		$sql = "SELECT
				*
				FROM
				".Config::Get('db.table.notification_type')."
		";
		$aNotifications=array();
		if ($aRows=$this->oDb->select($sql)) {
			foreach ($aRows as $aRow) {
				$aNotifications[]=Engine::GetEntity('Notification_NotificationType',$aRow);
			}
		}
		return $aNotifications;
	}

}
