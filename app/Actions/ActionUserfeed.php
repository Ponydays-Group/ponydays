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

use App\Modules\Blog\ModuleBlog;
use App\Modules\Topic\ModuleTopic;
use App\Modules\User\Entity\ModuleUser_EntityUser;
use App\Modules\User\ModuleUser;
use App\Modules\Userfeed\ModuleUserfeed;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Hook\ModuleHook;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Viewer\ModuleViewer;

/**
 * Обрабатывает пользовательские ленты контента
 *
 * @package actions
 * @since 1.0
 */
class ActionUserfeed extends Action {
	/**
	 * Текущий пользователь
	 *
	 * @var ModuleUser_EntityUser|null
	 */
	protected $oUserCurrent;

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		/**
		 * Доступ только у авторизованных пользователей
		 */
		$this->oUserCurrent = LS::Make(ModuleUser::class)->getUserCurrent();
		if (!$this->oUserCurrent) {
			parent::EventNotFound();
		}
		$this->SetDefaultEvent('index');

		LS::Make(ModuleViewer::class)->Assign('sMenuItemSelect', 'feed');
	}
	/**
	 * Регистрация евентов
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEvent('index', 'EventIndex');
		$this->AddEvent('subscribe', 'EventSubscribe');
		$this->AddEvent('subscribeByLogin', 'EventSubscribeByLogin');
		$this->AddEvent('subscribe_all', 'EventSubscribeAll');
		$this->AddEvent('unsubscribe_all', 'EventUnsubscribeAll');
		$this->AddEvent('unsubscribe', 'EventUnSubscribe');
		$this->AddEvent('get_more', 'EventGetMore');
	}
	/**
	 * Выводит ленту контента(топики) для пользователя
	 *
	 */
	protected function EventIndex() {
		/**
		 * Получаем топики
		 */
		$aTopics = LS::Make(ModuleUserfeed::class)->read($this->oUserCurrent->getId());
		/**
		 * Вызов хуков
		 */
		LS::Make(ModuleHook::class)->Run('topics_list_show',array('aTopics'=>$aTopics));

        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        $viewer->Assign('aTopics', $aTopics);
		if (count($aTopics)) {
			$viewer->Assign('iUserfeedLastId', end($aTopics)->getId());
		}
		if (count($aTopics) < Config::Get('module.userfeed.count_default')) {
			$viewer->Assign('bDisableGetMoreButton', true);
		} else {
			$viewer->Assign('bDisableGetMoreButton', false);
		}
		$this->SetTemplateAction('list');
	}
	/**
	 * Подгрузка ленты топиков (замена постраничности)
	 *
	 */
	protected function EventGetMore() {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        /**
		 * Устанавливаем формат Ajax ответа
		 */
		$viewer->SetResponseAjax('json');
		/**
		 * Проверяем последний просмотренный ID топика
		 */
		$iFromId = getRequestStr('last_id');
		if (!$iFromId)  {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Получаем топики
		 */
		$aTopics = LS::Make(ModuleUserfeed::class)->read($this->oUserCurrent->getId(), null, $iFromId);
		/**
		 * Вызов хуков
		 */
		LS::Make(ModuleHook::class)->Run('topics_list_show',array('aTopics'=>$aTopics));
		/**
		 * Загружаем данные в ajax ответ
		 */
		$oViewer=$viewer->GetLocalViewer();
		$oViewer->Assign('aTopics',  $aTopics);
		$viewer->AssignAjax('result', $oViewer->Fetch('topic_list.tpl'));
		$viewer->AssignAjax('topics_count', count($aTopics));

		if (count($aTopics)) {
			$viewer->AssignAjax('iUserfeedLastId', end($aTopics)->getId());
		}
	}
	/**
	 * Подписка на контент блога или пользователя
	 *
	 */
	protected function EventSubscribe() {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        /**
		 * Устанавливаем формат Ajax ответа
		 */
		$viewer->SetResponseAjax('json');
		/**
		 * Проверяем наличие ID блога или пользователя
		 */
		if (!getRequest('id')) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
		}
		$sType = getRequestStr('type');
		$iType = null;
		/**
		 * Определяем тип подписки
		 */
		switch($sType) {
			case 'blogs':
				$iType = ModuleUserfeed::SUBSCRIBE_TYPE_BLOG;
                /** @var ModuleBlog $blog */
                $blog = LS::Make(ModuleBlog::class);
                /**
				 * Проверяем существование блога
				 */
				if (!$blog->GetBlogById(getRequestStr('id'))) {
					LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
					return;
				}
				if(!in_array($blog->GetBlogById(getRequestStr('id'))->getId(), LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser(LS::Make(ModuleUser::class)->GetUserCurrent())) and in_array(LS::Make(ModuleBlog::class)->GetBlogById(getRequestStr('id'))->getType(), array("close", "invite"))){
					LS::Make(ModuleMessage::class)->AddNotice("У вас нет разрешения подписываться на этот блог", "Ошибка");
					return;
				}
				break;
			default:
				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
				return;
		}
		/**
		 * Подписываем
		 */
		LS::Make(ModuleUserfeed::class)->subscribeUser($this->oUserCurrent->getId(), $iType, getRequestStr('id'));
		LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('userfeed_subscribes_updated'), "Внимание");
	}
	/**
	 * Подписка на пользвователя по логину
	 *
	 */
	protected function EventSubscribeByLogin() {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        /**
		 * Устанавливаем формат Ajax ответа
		 */
		$viewer->SetResponseAjax('json');
		/**
		 * Передан ли логин
		 */
		if (!getRequest('login') or !is_string(getRequest('login'))) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Проверяем существование прользователя
		 */
		$oUser = LS::Make(ModuleUser::class)->getUserByLogin(getRequestStr('login'));
		if (!$oUser) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('user_not_found',array('login'=>htmlspecialchars(getRequestStr('login')))),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Не даем подписаться на самого себя
		 */
		/**
		 * Подписываем
		 */
		LS::Make(ModuleUserfeed::class)->subscribeUser($this->oUserCurrent->getId(), ModuleUserfeed::SUBSCRIBE_TYPE_USER, $oUser->getId());
		/**
		 * Загружаем данные ajax ответ
		 */
		$viewer->AssignAjax('uid', $oUser->getId());
		$viewer->AssignAjax('user_login', $oUser->getLogin());
		$viewer->AssignAjax('user_web_path', $oUser->getUserWebPath());
		$viewer->AssignAjax('user_avatar_48', $oUser->getProfileAvatarPath(48));
		$viewer->AssignAjax('lang_error_msg', LS::Make(ModuleLang::class)->Get('userfeed_subscribes_already_subscribed'));
		$viewer->AssignAjax('lang_error_title', LS::Make(ModuleLang::class)->Get('error'));
		LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('userfeed_subscribes_updated'), LS::Make(ModuleLang::class)->Get('attention'));
	}
	/**
	 * Отписка от блога или пользователя
	 *
	 */
	protected function EventUnsubscribe() {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        /**
		 * Устанавливаем формат Ajax ответа
		 */
		$viewer->SetResponseAjax('json');
		if (!getRequest('id')) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		$sType = getRequestStr('type');
		$iType = null;
		/**
		 * Определяем от чего отписываемся
		 */
		switch($sType) {
			case 'blogs':
				$iType = ModuleUserfeed::SUBSCRIBE_TYPE_BLOG;
				break;
			case 'users':
				$iType = ModuleUserfeed::SUBSCRIBE_TYPE_USER;
				break;
			default:
				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'),LS::Make(ModuleLang::class)->Get('error'));
				return;
		}
		/**
		 * Отписываем пользователя
		 */
		LS::Make(ModuleUserfeed::class)->unsubscribeUser($this->oUserCurrent->getId(), $iType, getRequestStr('id'));
		LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('userfeed_subscribes_updated'), LS::Make(ModuleLang::class)->Get('attention'));
	}
	/**
	 * При завершении экшена загружаем в шаблон необходимые переменные
	 *
	 */
	protected function EventSubscribeAll() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
//		$aBlogs = LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($this->oUserCurrent);
		$aBlogs = LS::Make(ModuleBlog::class)->GetBlogs();

		foreach ($aBlogs as $iBlogId) {
			LS::Make(ModuleUserfeed::class)->subscribeUser($this->oUserCurrent->getId(), ModuleUserfeed::SUBSCRIBE_TYPE_BLOG, $iBlogId->getId());
		}
		LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('userfeed_subscribes_updated'), "Внимание");
	}

	protected function EventUnsubscribeAll() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		$aBlogs = LS::Make(ModuleBlog::class)->GetBlogs();
		foreach ($aBlogs as $iBlogId) {
			LS::Make(ModuleUserfeed::class)->unsubscribeUser($this->oUserCurrent->getId(), ModuleUserfeed::SUBSCRIBE_TYPE_BLOG, $iBlogId->getId());
		}
		LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('userfeed_subscribes_updated'), "Внимание");
	}

	public function EventShutdown() {
		/**
		 * Подсчитываем новые топики
		 */
		$iCountTopicsCollectiveNew=LS::Make(ModuleTopic::class)->GetCountTopicsCollectiveNew();
		$iCountTopicsPersonalNew=LS::Make(ModuleTopic::class)->GetCountTopicsPersonalNew();
		$iCountTopicsNew=$iCountTopicsCollectiveNew+$iCountTopicsPersonalNew;
		/**
		 * Загружаем переменные в шаблон
		 */
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        $viewer->Assign('iCountTopicsCollectiveNew',$iCountTopicsCollectiveNew);
		$viewer->Assign('iCountTopicsPersonalNew',$iCountTopicsPersonalNew);
		$viewer->Assign('iCountTopicsNew',$iCountTopicsNew);
	}
}
