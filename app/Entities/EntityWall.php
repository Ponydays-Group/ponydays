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

namespace App\Entities;

use App\Modules\ModuleACL;
use App\Modules\ModuleUser;
use App\Modules\ModuleWall;
use Engine\Config;
use Engine\Entity;
use Engine\LS;
use Engine\Modules\ModuleLang;

/**
 * Сущность записи на стене
 *
 * @package modules.wall
 * @since 1.0
 */
class EntityWall extends Entity {
	/**
	 * Определяем правила валидации
	 *
	 * @var array
	 */
	protected $aValidateRules=array(
		array('pid','pid','on'=>array('','add')),
		array('user_id','time_limit','on'=>array('add')),
	);

	/**
	 * Инициализация
	 */
	public function Init() {
		parent::Init();
		$this->aValidateRules[]=array('text','string','max'=>Config::Get('module.wall.text_max'),'min'=>Config::Get('module.wall.text_min'),'allowEmpty'=>false,'on'=>array('','add'));
	}
	/**
	 * Проверка на ограничение по времени
	 *
	 * @param string $sValue	Проверяемое значение
	 * @param array $aParams	Параметры
	 * @return bool|string
	 */
	public function ValidateTimeLimit($sValue,$aParams) {
		if ($oUser=LS::Make(ModuleUser::class)->GetUserById($this->getUserId())) {
			if (LS::Make(ModuleACL::class)->CanAddWallTime($oUser,$this)) {
				return true;
			}
		}
		return LS::Make(ModuleLang::class)->Get('wall_add_time_limit');
	}
	/**
	 * Валидация родительского сообщения
	 *
	 * @param string $sValue	Проверяемое значение
	 * @param array $aParams	Параметры
	 * @return bool|string
	 */
	public function ValidatePid($sValue,$aParams) {
		if (!$sValue) {
			$this->setPid(null);
			return true;
		} elseif ($oParentWall=$this->GetPidWall()) {
			/**
			 * Если отвечаем на сообщение нужной стены и оно корневое, то все ОК
			 */
			if ($oParentWall->getWallUserId()==$this->getWallUserId() and !$oParentWall->getPid()) {
				return true;
			}
		}
		return LS::Make(ModuleLang::class)->Get('wall_add_pid_error');
	}
	/**
	 * Возвращает родительскую запись
	 *
	 * @return EntityWall|null
	 */
	public function GetPidWall() {
		if ($this->getPid()) {
			return LS::Make(ModuleWall::class)->GetWallById($this->getPid());
		}
		return null;
	}
	/**
	 * Проверка на возможность удаления сообщения
	 *
	 * @return bool
	 */
	public function isAllowDelete() {
		if ($oUserCurrent=LS::Make(ModuleUser::class)->GetUserCurrent()) {
			if ($oUserCurrent->getId()==$this->getWallUserId() or $oUserCurrent->isAdministrator()) {
				return true;
			}
		}
		return false;
	}
	/**
	 * Возвращает пользователя, которому принадлежит стена
	 *
	 * @return \App\Entities\EntityUser|null
	 */
	public function getWallUser() {
		if (!$this->_getDataOne('wall_user')) {
			$this->_aData['wall_user']=LS::Make(ModuleUser::class)->GetUserById($this->getWallUserId());
		}
		return $this->_getDataOne('wall_user');
	}
	/**
	 * Возвращает URL стены
	 *
	 * @return string
	 */
	public function getUrlWall() {
		return $this->getWallUser()->getUserWebPath().'wall/';
	}
}
