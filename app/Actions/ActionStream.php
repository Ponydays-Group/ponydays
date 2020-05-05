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

use App\Modules\Stream\ModuleStream;
use App\Modules\User\Entity\ModuleUser_EntityUser;
use App\Modules\User\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Viewer\ModuleViewer;

/**
 * Экшен обработки ленты активности
 *
 * @package actions
 * @since 1.0
 */
class ActionStream extends Action {
	/**
	 * Текущий пользователь
	 *
	 * @var ModuleUser_EntityUser|null
	 */
	protected $oUserCurrent;
	/**
	 * Какое меню активно
	 *
	 * @var string
	 */
	protected $sMenuItemSelect='user';

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		/**
		 * Личная лента доступна только для авторизованных, для гостей показываем общую ленту
		 */
		$this->oUserCurrent = LS::Make(ModuleUser::class)->getUserCurrent();
		if ($this->oUserCurrent) {
			$this->SetDefaultEvent('user');
		} else {
			$this->SetDefaultEvent('all');
		}
		LS::Make(ModuleViewer::class)->Assign('aStreamEventTypes', LS::Make(ModuleStream::class)->getEventTypes());

		LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect', 'stream');
		/**
		 * Загружаем в шаблон JS текстовки
		 */
		LS::Make(ModuleLang::class)->AddLangJs(array(
								  'stream_subscribes_already_subscribed','error'
							  ));
	}
	/**
	 * Регистрация евентов
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEvent('user', 'EventUser');
		$this->AddEvent('all', 'EventAll');
		$this->AddEvent('subscribe', 'EventSubscribe');
		$this->AddEvent('subscribeByLogin', 'EventSubscribeByLogin');
		$this->AddEvent('unsubscribe', 'EventUnSubscribe');
		$this->AddEvent('switchEventType', 'EventSwitchEventType');
		$this->AddEvent('get_more', 'EventGetMore');
		$this->AddEvent('get_more_user', 'EventGetMoreUser');
		$this->AddEvent('get_more_all', 'EventGetMoreAll');
	}

	/**
	 * Список событий в ленте активности пользователя
	 *
	 */
	protected function EventUser() {
		/**
		 * Пользователь авторизован?
		 */
		if (!$this->oUserCurrent) {
			parent::EventNotFound();
		}
		LS::Make(ModuleViewer::class)->AddBlock('right','streamConfig');
		/**
		 * Читаем события
		 */
		$aEvents = LS::Make(ModuleStream::class)->Read();
		LS::Make(ModuleViewer::class)->Assign('bDisableGetMoreButton', LS::Make(ModuleStream::class)->GetCountByReaderId($this->oUserCurrent->getId()) < Config::Get('module.stream.count_default'));
		LS::Make(ModuleViewer::class)->Assign('aStreamEvents', $aEvents);
		if (count($aEvents)) {
			$oEvenLast=end($aEvents);
			LS::Make(ModuleViewer::class)->Assign('iStreamLastId', $oEvenLast->getId());
		}
	}
	/**
	 * Список событий в общей ленте активности сайта
	 *
	 */
	protected function EventAll() {
		$this->sMenuItemSelect='all';
		/**
		 * Читаем события
		 */
		$aEvents = LS::Make(ModuleStream::class)->ReadAll();
		LS::Make(ModuleViewer::class)->Assign('bDisableGetMoreButton', LS::Make(ModuleStream::class)->GetCountAll() < Config::Get('module.stream.count_default'));
		LS::Make(ModuleViewer::class)->Assign('aStreamEvents', $aEvents);
		if (count($aEvents)) {
			$oEvenLast=end($aEvents);
			LS::Make(ModuleViewer::class)->Assign('iStreamLastId', $oEvenLast->getId());
		}
	}
	/**
	 * Активаци/деактивация типа события
	 *
	 */
	protected function EventSwitchEventType() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Пользователь авторизован?
		 */
		if (!$this->oUserCurrent) {
			parent::EventNotFound();
		}
		if (!getRequest('type')) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
		}
		/**
		 * Активируем/деактивируем тип
		 */
		LS::Make(ModuleStream::class)->switchUserEventType($this->oUserCurrent->getId(), getRequestStr('type'));
		LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('stream_subscribes_updated'), LS::Make(ModuleLang::class)->Get('attention'));
	}
	/**
	 * Погрузка событий (замена постраничности)
	 *
	 */
	protected function EventGetMore() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Пользователь авторизован?
		 */
		if (!$this->oUserCurrent) {
			parent::EventNotFound();
		}
		/**
		 * Необходимо передать последний просмотренный ID событий
		 */
		$iFromId = getRequestStr('last_id');
		if (!$iFromId)  {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Получаем события
		 */
		$aEvents = LS::Make(ModuleStream::class)->Read(null, $iFromId);

		$oViewer=LS::Make(ModuleViewer::class)->GetLocalViewer();
		$oViewer->Assign('aStreamEvents', $aEvents);
		$oViewer->Assign('sDateLast', getRequestStr('date_last'));
		if (count($aEvents)) {
			$oEvenLast=end($aEvents);
			LS::Make(ModuleViewer::class)->AssignAjax('iStreamLastId', $oEvenLast->getId());
		}
		/**
		 * Возвращаем данные в ajax ответе
		 */
		LS::Make(ModuleViewer::class)->AssignAjax('result', $oViewer->Fetch('actions/ActionStream/events.tpl'));
		LS::Make(ModuleViewer::class)->AssignAjax('events_count', count($aEvents));
	}
	/**
	 * Погрузка событий для всего сайта
	 *
	 */
	protected function EventGetMoreAll() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Пользователь авторизован?
		 */
		if (!$this->oUserCurrent) {
			parent::EventNotFound();
		}
		/**
		 * Необходимо передать последний просмотренный ID событий
		 */
		$iFromId = getRequestStr('last_id');
		if (!$iFromId)  {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Получаем события
		 */
		$aEvents = LS::Make(ModuleStream::class)->ReadAll(null, $iFromId);

		$oViewer=LS::Make(ModuleViewer::class)->GetLocalViewer();
		$oViewer->Assign('aStreamEvents', $aEvents);
		$oViewer->Assign('sDateLast', getRequestStr('date_last'));
		if (count($aEvents)) {
			$oEvenLast=end($aEvents);
			LS::Make(ModuleViewer::class)->AssignAjax('iStreamLastId', $oEvenLast->getId());
		}
		/**
		 * Возвращаем данные в ajax ответе
		 */
		LS::Make(ModuleViewer::class)->AssignAjax('result', $oViewer->Fetch('actions/ActionStream/events.tpl'));
		LS::Make(ModuleViewer::class)->AssignAjax('events_count', count($aEvents));
	}
	/**
	 * Подгрузка событий для пользователя
	 *
	 */
	protected function EventGetMoreUser() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Пользователь авторизован?
		 */
		if (!$this->oUserCurrent) {
			parent::EventNotFound();
		}
		/**
		 * Необходимо передать последний просмотренный ID событий
		 */
		$iFromId = getRequestStr('last_id');
		if (!$iFromId)  {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		if (!($oUser=LS::Make(ModuleUser::class)->GetUserById(getRequestStr('user_id')))) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Получаем события
		 */
		$aEvents = LS::Make(ModuleStream::class)->ReadByUserId($oUser->getId(), null, $iFromId);

		$oViewer=LS::Make(ModuleViewer::class)->GetLocalViewer();
		$oViewer->Assign('aStreamEvents', $aEvents);
		$oViewer->Assign('sDateLast', getRequestStr('date_last'));
		if (count($aEvents)) {
			$oEvenLast=end($aEvents);
			LS::Make(ModuleViewer::class)->AssignAjax('iStreamLastId', $oEvenLast->getId());
		}
		/**
		 * Возвращаем данные в ajax ответе
		 */
		LS::Make(ModuleViewer::class)->AssignAjax('result', $oViewer->Fetch('actions/ActionStream/events.tpl'));
		LS::Make(ModuleViewer::class)->AssignAjax('events_count', count($aEvents));
	}
	/**
	 * Подписка на пользователя по ID
	 *
	 */
	protected function EventSubscribe() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Пользователь авторизован?
		 */
		if (!$this->oUserCurrent) {
			parent::EventNotFound();
		}
		/**
		 * Проверяем существование пользователя
		 */
		if (!LS::Make(ModuleUser::class)->getUserById(getRequestStr('id'))) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
		}
		if ($this->oUserCurrent->getId() == getRequestStr('id')) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('stream_error_subscribe_to_yourself'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Подписываем на пользователя
		 */
		LS::Make(ModuleStream::class)->subscribeUser($this->oUserCurrent->getId(), getRequestStr('id'));
		LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('stream_subscribes_updated'), LS::Make(ModuleLang::class)->Get('attention'));
	}
	/**
	 * Подписка на пользователя по логину
	 *
	 */
	protected function EventSubscribeByLogin() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Пользователь авторизован?
		 */
		if (!$this->oUserCurrent) {
			parent::EventNotFound();
		}
		if (!getRequest('login') or !is_string(getRequest('login'))) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Проверяем существование пользователя
		 */
		$oUser = LS::Make(ModuleUser::class)->getUserByLogin(getRequestStr('login'));
		if (!$oUser) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('user_not_found',array('login'=>htmlspecialchars(getRequestStr('login')))),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		if ($this->oUserCurrent->getId() == $oUser->getId()) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('stream_error_subscribe_to_yourself'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Подписываем на пользователя
		 */
		LS::Make(ModuleStream::class)->subscribeUser($this->oUserCurrent->getId(),  $oUser->getId());
		LS::Make(ModuleViewer::class)->AssignAjax('uid', $oUser->getId());
		LS::Make(ModuleViewer::class)->AssignAjax('user_login', $oUser->getLogin());
		LS::Make(ModuleViewer::class)->AssignAjax('user_web_path', $oUser->getUserWebPath());
		LS::Make(ModuleViewer::class)->AssignAjax('user_avatar_48', $oUser->getProfileAvatarPath(48));
		LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('userfeed_subscribes_updated'), LS::Make(ModuleLang::class)->Get('attention'));
	}
	/**
	 * Отписка от пользователя
	 *
	 */
	protected function EventUnsubscribe() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Пользователь авторизован?
		 */
		if (!$this->oUserCurrent) {
			parent::EventNotFound();
		}
		/**
		 * Пользователь с таким ID существует?
		 */
		if (!LS::Make(ModuleUser::class)->getUserById(getRequestStr('id'))) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
		}
		/**
		 * Отписываем
		 */
		LS::Make(ModuleStream::class)->unsubscribeUser($this->oUserCurrent->getId(), getRequestStr('id'));
		LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('stream_subscribes_updated'), LS::Make(ModuleLang::class)->Get('attention'));
	}
	/**
	 * Выполняется при завершении работы экшена
	 *
	 */
	public function EventShutdown() {
		/**
		 * Загружаем в шаблон необходимые переменные
		 */
		LS::Make(ModuleViewer::class)->Assign('sMenuItemSelect',$this->sMenuItemSelect);
	}
}
