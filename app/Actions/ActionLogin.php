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

use App\Modules\Crypto\ModuleCrypto;
use App\Modules\Notify\ModuleNotify;
use App\Modules\User\Entity\ModuleUser_EntityReminder;
use App\Modules\User\ModuleUser;
use Engine\Engine;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Security\ModuleSecurity;
use Engine\Modules\Session\ModuleSession;
use Engine\Modules\Viewer\ModuleViewer;
use Engine\Router;

/**
 * Обрабатывые авторизацию
 *
 * @package actions
 * @since 1.0
 */
class ActionLogin extends Action {
	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		/**
		 * Устанавливаем дефолтный евент
		 */
		$this->SetDefaultEvent('index');
		/**
		 * Отключаем отображение статистики выполнения
		 */
		Router::SetIsShowStats(false);
	}
	/**
	 * Регистрируем евенты
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEvent('index','EventLogin');
		$this->AddEvent('exit','EventExit');
		$this->AddEvent('reminder','EventReminder');
		$this->AddEvent('reactivation','EventReactivation');

		$this->AddEvent('ajax-login','EventAjaxLogin');
		$this->AddEvent('ajax-reminder','EventAjaxReminder');
		$this->AddEvent('ajax-reactivation','EventAjaxReactivation');
	}
	/**
	 * Ajax авторизация
	 */
	protected function EventAjaxLogin() {
		/**
		 * Устанвливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Логин и пароль являются строками?
		 */
		if (!is_string(getRequest('login')) or !is_string(getRequest('password'))) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
			return;
		}
		/**
		 * Проверяем есть ли такой юзер по логину
		 */
		if ((func_check(getRequest('login'),'mail') and $oUser=LS::Make(ModuleUser::class)->GetUserByMail(getRequest('login')))  or  $oUser=LS::Make(ModuleUser::class)->GetUserByLogin(getRequest('login'))) {
			// проверка на бан

//			if (LS::Make(ModuleUser::class)->isBanned($oUser->getId())) {
			if ($oUser->isBanned()) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle($oUser->getBanComment());
                LS::Make(ModuleUser::class)->Logout();
                LS::Make(ModuleSession::class)->DropSession();
                return Router::Action('error');
			}

			/**
			 * Проверяем пароль и обновляем хеш, если нужно
			 */
			$user_password = $oUser->getPassword();
			if(LS::Make(ModuleCrypto::class)->PasswordVerify(getRequest('password'), $user_password)) {
				if(LS::Make(ModuleCrypto::class)->PasswordNeedsRehash($user_password)) {
					$oUser->setPassword(LS::Make(ModuleCrypto::class)->PasswordHash(getRequest('password')));
					LS::Make(ModuleUser::class)->Update($oUser);
				}

				/**
			 	* Проверяем активен ли юзер
			 	*/
				if (!$oUser->getActivate()) {
					LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('user_not_activated', array('reactivation_path' => Router::GetPath('login') . 'reactivation')));
					return;
				}
				$bRemember=getRequest('remember',false) ? true : false;
				/**
				 * Авторизуем
				 */
				LS::Make(ModuleUser::class)->Authorization($oUser,$bRemember);
				/**
				 * Определяем редирект
				 */
				$sUrl=Config::Get('module.user.redirect_after_login');
				if (getRequestStr('return-path')) {
					$sUrl=getRequestStr('return-path');
				}
                LS::Make(ModuleViewer::class)->AssignAjax('sUrlRedirect',$sUrl ? $sUrl : Config::Get('path.root.web'));
                LS::Make(ModuleViewer::class)->AssignAjax('sKey',LS::Make(ModuleUser::class)->GenerateUserKey($oUser));
				return;
			}
		}
		LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('user_login_bad'));
	}
	/**
	 * Повторный запрос активации
	 */
	protected function EventReactivation() {
		if(LS::Make(ModuleUser::class)->GetUserCurrent()) {
			Router::Location(Config::Get('path.root.web').'/');
		}

		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('reactivation'));
	}
	/**
	 *  Ajax повторной активации
	 */
	protected function EventAjaxReactivation() {
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');

		if ((func_check(getRequestStr('mail'), 'mail') and $oUser = LS::Make(ModuleUser::class)->GetUserByMail(getRequestStr('mail')))) {
			if ($oUser->getActivate()) {
				LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('registration_activate_error_reactivate'));
				return;
			} else {
				$oUser->setActivateKey(md5(func_generator() . time()));
				if (LS::Make(ModuleUser::class)->Update($oUser)) {
					LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('reactivation_send_link'));
					LS::Make(ModuleNotify::class)->SendReactivationCode($oUser);
					return;
				}
			}
		}

		LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('password_reminder_bad_email'));
	}
	/**
	 * Обрабатываем процесс залогинивания
	 * По факту только отображение шаблона, дальше вступает в дело Ajax
	 *
	 */
	protected function EventLogin() {
		/**
		 * Если уже авторизирован
		 */
		if(LS::Make(ModuleUser::class)->GetUserCurrent()) {
			Router::Location(Config::Get('path.root.web').'/');
		}
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('login'));
	}
	/**
	 * Обрабатываем процесс разлогинивания
	 *
	 */
	protected function EventExit() {
		LS::Make(ModuleSecurity::class)->ValidateSendForm();
		LS::Make(ModuleUser::class)->Logout();
		LS::Make(ModuleViewer::class)->Assign('bRefreshToHome',true);
	}
	/**
	 * Ajax запрос на восстановление пароля
	 */
	protected function EventAjaxReminder() {
		/**
		 * Устанвливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Пользователь с таким емайлом существует?
		 */
		if ((func_check(getRequestStr('mail'),'mail') and $oUser=LS::Make(ModuleUser::class)->GetUserByMail(getRequestStr('mail')))) {
			/**
			 * Формируем и отправляем ссылку на смену пароля
			 */
			$oReminder = new ModuleUser_EntityReminder();
			$oReminder->setCode(func_generator(32));
			$oReminder->setDateAdd(date("Y-m-d H:i:s"));
			$oReminder->setDateExpire(date("Y-m-d H:i:s",time()+60*60*24*7));
			$oReminder->setDateUsed(null);
			$oReminder->setIsUsed(0);
			$oReminder->setUserId($oUser->getId());
			if (LS::Make(ModuleUser::class)->AddReminder($oReminder)) {
				LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('password_reminder_send_link'));
				LS::Make(ModuleNotify::class)->SendReminderCode($oUser,$oReminder);
				return;
			}
		}
		LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('password_reminder_bad_email'),LS::Make(ModuleLang::class)->Get('error'));
	}
	/**
	 * Обработка напоминания пароля, подтверждение смены пароля
	 *
	 */
	protected function EventReminder() {
		/**
		 * Устанавливаем title страницы
		 */
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('password_reminder'));
		/**
		 * Проверка кода на восстановление пароля и генерация нового пароля
		 */
		if (func_check($this->GetParam(0),'md5')) {
			/**
			 * Проверка кода подтверждения
			 */
			if ($oReminder=LS::Make(ModuleUser::class)->GetReminderByCode($this->GetParam(0))) {
				if (!$oReminder->getIsUsed() and strtotime($oReminder->getDateExpire())>time() and $oUser=LS::Make(ModuleUser::class)->GetUserById($oReminder->getUserId())) {
					$sNewPassword=func_generator(7);
					$oUser->setPassword(LS::Make(ModuleCrypto::class)->PasswordHash($sNewPassword));
					if (LS::Make(ModuleUser::class)->Update($oUser)) {
						$oReminder->setDateUsed(date("Y-m-d H:i:s"));
						$oReminder->setIsUsed(1);
						LS::Make(ModuleUser::class)->UpdateReminder($oReminder);
						LS::Make(ModuleNotify::class)->SendReminderPassword($oUser,$sNewPassword);
						$this->SetTemplateAction('reminder_confirm');
						return;
					}
				}
			}
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('password_reminder_bad_code'),LS::Make(ModuleLang::class)->Get('error'));
			return Router::Action('error');
		}
	}
}
