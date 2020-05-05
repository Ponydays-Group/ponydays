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

namespace App\Actions;

use App\Modules\Subscribe\ModuleSubscribe;
use App\Modules\User\Entity\ModuleUser_EntityUser;
use App\Modules\User\ModuleUser;
use Engine\Action;
use Engine\LS;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Viewer\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки подписок пользователей
 *
 * @package actions
 * @since 1.0
 */
class ActionSubscribe extends Action {
	/**
	 * Текущий пользователь
	 *
	 * @var ModuleUser_EntityUser|null
	 */
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		$this->oUserCurrent=LS::Make(ModuleUser::class)->GetUserCurrent();
	}
	/**
	 * Регистрация евентов
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEventPreg('/^unsubscribe$/i','/^\w{32}$/i','EventUnsubscribe');
		$this->AddEvent('ajax-subscribe-toggle','EventAjaxSubscribeToggle');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */


	/**
	 * Отписка от подписки
	 */
	protected function EventUnsubscribe() {
		/**
		 * Получаем подписку по ключу
		 */
		if ($oSubscribe=LS::Make(ModuleSubscribe::class)->GetSubscribeByKey($this->getParam(0)) and $oSubscribe->getStatus()==1) {
			/**
			 * Отписываем пользователя
			 */
			$oSubscribe->setStatus(0);
			$oSubscribe->setDateRemove(date("Y-m-d H:i:s"));
			LS::Make(ModuleSubscribe::class)->UpdateSubscribe($oSubscribe);

			LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('subscribe_change_ok'),null,true);
		}
		/**
		 * Получаем URL для редиректа
		 */
		if ((!$sUrl=LS::Make(ModuleSubscribe::class)->GetUrlTarget($oSubscribe->getTargetType(),$oSubscribe->getTargetId()))) {
			$sUrl=Router::GetPath('index');
		}
		Router::Location($sUrl);
	}
	/**
	 * Изменение состояния подписки
	 */
	protected function EventAjaxSubscribeToggle() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Получаем емайл подписки и проверяем его на валидность
		 */
		$sMail=getRequestStr('mail');
		if ($this->oUserCurrent) {
			$sMail=$this->oUserCurrent->getMail();
		}
		if (!func_check($sMail,'mail')) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('registration_mail_error'),LS::Make(ModuleLang::class)->Get('error'));
			return ;
		}
		/**
		 * Получаем тип объекта подписки
		 */
		$sTargetType=getRequestStr('target_type');
		if (!LS::Make(ModuleSubscribe::class)->IsAllowTargetType($sTargetType)) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
			return ;
		}
		$sTargetId=getRequestStr('target_id') ? getRequestStr('target_id') : null;
		$iValue=getRequest('value') ? 1 : 0;

		$oSubscribe=null;
		/**
		 * Есть ли доступ к подписке гостям?
		 */
		if (!$this->oUserCurrent and !LS::Make(ModuleSubscribe::class)->IsAllowTargetForGuest($sTargetType)) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('need_authorization'),LS::Make(ModuleLang::class)->Get('error'));
			return ;
		}
		/**
		 * Проверка объекта подписки
		 */
		if (!LS::Make(ModuleSubscribe::class)->CheckTarget($sTargetType,$sTargetId,$iValue)) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
			return ;
		}
		/**
		 * Если подписка еще не существовала, то создаем её
		 */
		if ($oSubscribe=LS::Make(ModuleSubscribe::class)->AddSubscribeSimple($sTargetType,$sTargetId,$sMail)) {
			$oSubscribe->setStatus($iValue);
			LS::Make(ModuleSubscribe::class)->UpdateSubscribe($oSubscribe);
			LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('subscribe_change_ok'),LS::Make(ModuleLang::class)->Get('attention'));
			return ;
		}
		LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
		return ;
	}
}
