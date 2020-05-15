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

use App\Modules\ModuleGeo;
use App\Modules\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки статистики юзеров, т.е. УРЛа вида /people/
 *
 * @package actions
 * @since 1.0
 */
class ActionPeople extends Action {
	/**
	 * Главное меню
	 *
	 * @var string
	 */
	protected $sMenuHeadItemSelect='people';
	/**
	 * Меню
	 *
	 * @var string
	 */
	protected $sMenuItemSelect='all';

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		/**
		 * Устанавливаем title страницы
		 */
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('people'));
	}
	/**
	 * Регистрируем евенты
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEvent('online','EventOnline');
		$this->AddEvent('new','EventNew');
		$this->AddEventPreg('/^(index)?$/i','/^(page([1-9]\d{0,5}))?$/i','/^$/i','EventIndex');
		$this->AddEventPreg('/^ajax-search$/i','EventAjaxSearch');

		$this->AddEventPreg('/^country$/i','/^\d+$/i','/^(page([1-9]\d{0,5}))?$/i','EventCountry');
		$this->AddEventPreg('/^city$/i','/^\d+$/i','/^(page([1-9]\d{0,5}))?$/i','EventCity');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	/**
	 * Поиск пользователей по логину
	 */
	protected function EventAjaxSearch() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Получаем из реквеста первые быквы для поиска пользователей по логину
		 */
		$sTitle=getRequest('user_login');
		if (is_string($sTitle) and mb_strlen($sTitle,'utf-8')) {
			$sTitle=str_replace(array('_','%'),array('\_','\%'),$sTitle);
		} else {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
			return;
		}
		/**
		 * Как именно искать: совпадение в любой частилогина, или только начало или конец логина
		 */
		if (getRequest('isPrefix')) {
			$sTitle.='%';
		} elseif (getRequest('isPostfix')) {
			$sTitle='%'.$sTitle;
		} else {
			$sTitle='%'.$sTitle.'%';
		}
		/**
		 * Ищем пользователей
		 */
		$aResult=LS::Make(ModuleUser::class)->GetUsersByFilter(array('activate' => 1,'login'=>$sTitle),array('user_rating'=>'desc'),1,50);
		/**
		 * Формируем ответ
		 */
		$oViewer=LS::Make(ModuleViewer::class)->GetLocalViewer();
		$oViewer->Assign('aUsersList',$aResult['collection']);
		$oViewer->Assign('oUserCurrent',LS::Make(ModuleUser::class)->GetUserCurrent());
		$oViewer->Assign('sUserListEmpty',LS::Make(ModuleLang::class)->Get('user_search_empty'));
		LS::Make(ModuleViewer::class)->AssignAjax('sText',$oViewer->Fetch("user_list.tpl"));
	}
	/**
	 * Показывает юзеров по стране
	 *
	 */
	protected function EventCountry() {
		$this->sMenuItemSelect='country';
		/**
		 * Страна существует?
		 */
		if (!($oCountry=LS::Make(ModuleGeo::class)->GetCountryById($this->getParam(0)))) {
			parent::EventNotFound();
			return;
		}
		/**
		 * Получаем статистику
		 */
		$this->GetStats();
		/**
		 * Передан ли номер страницы
		 */
		$iPage=$this->GetParamEventMatch(1,2) ? $this->GetParamEventMatch(1,2) : 1;
		/**
		 * Получаем список вязей пользователей со страной
		 */
		$aResult=LS::Make(ModuleGeo::class)->GetTargets(array('country_id'=>$oCountry->getId(),'target_type'=>'user'),$iPage,Config::Get('module.user.per_page'));
		$aUsersId=array();
		foreach($aResult['collection'] as $oTarget) {
			$aUsersId[]=$oTarget->getTargetId();
		}
		$aUsersCountry=LS::Make(ModuleUser::class)->GetUsersAdditionalData($aUsersId);
		/**
		 * Формируем постраничность
		 */
		$aPaging=LS::Make(ModuleViewer::class)->MakePaging($aResult['count'],$iPage,Config::Get('module.user.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('people').$this->sCurrentEvent.'/'.$oCountry->getId());
		/**
		 * Загружаем переменные в шаблон
		 */
		if ($aUsersCountry) {
			LS::Make(ModuleViewer::class)->Assign('aPaging',$aPaging);
		}
		LS::Make(ModuleViewer::class)->Assign('oCountry',$oCountry);
		LS::Make(ModuleViewer::class)->Assign('aUsersCountry',$aUsersCountry);
	}
	/**
	 * Показывает юзеров по городу
	 *
	 */
	protected function EventCity() {
		$this->sMenuItemSelect='city';
		/**
		 * Город существует?
		 */
		if (!($oCity=LS::Make(ModuleGeo::class)->GetCityById($this->getParam(0)))) {
			parent::EventNotFound(); return;
		}
		/**
		 * Получаем статистику
		 */
		$this->GetStats();
		/**
		 * Передан ли номер страницы
		 */
		$iPage=$this->GetParamEventMatch(1,2) ? $this->GetParamEventMatch(1,2) : 1;
		/**
		 * Получаем список юзеров
		 */
		$aResult=LS::Make(ModuleGeo::class)->GetTargets(array('city_id'=>$oCity->getId(),'target_type'=>'user'),$iPage,Config::Get('module.user.per_page'));
		$aUsersId=array();
		foreach($aResult['collection'] as $oTarget) {
			$aUsersId[]=$oTarget->getTargetId();
		}
		$aUsersCity=LS::Make(ModuleUser::class)->GetUsersAdditionalData($aUsersId);
		/**
		 * Формируем постраничность
		 */
		$aPaging=LS::Make(ModuleViewer::class)->MakePaging($aResult['count'],$iPage,Config::Get('module.user.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('people').$this->sCurrentEvent.'/'.$oCity->getId());
		/**
		 * Загружаем переменные в шаблон
		 */
		if ($aUsersCity) {
			LS::Make(ModuleViewer::class)->Assign('aPaging',$aPaging);
		}
		LS::Make(ModuleViewer::class)->Assign('oCity',$oCity);
		LS::Make(ModuleViewer::class)->Assign('aUsersCity',$aUsersCity);
	}
	/**
	 * Показываем последних на сайте
	 *
	 */
	protected function EventOnline() {
		$this->sMenuItemSelect='online';
		/**
		 * Последние по визиту на сайт
		 */
		$aUsersLast=LS::Make(ModuleUser::class)->GetUsersByDateLast(15);
		LS::Make(ModuleViewer::class)->Assign('aUsersLast',$aUsersLast);
		/**
		 * Получаем статистику
		 */
		$this->GetStats();
	}
	/**
	 * Показываем новых на сайте
	 *
	 */
	protected function EventNew() {
		$this->sMenuItemSelect='new';
		/**
		 * Последние по регистрации
		 */
		$aUsersRegister=LS::Make(ModuleUser::class)->GetUsersByDateRegister(15);
		LS::Make(ModuleViewer::class)->Assign('aUsersRegister',$aUsersRegister);
		/**
		 * Получаем статистику
		 */
		$this->GetStats();
	}
	/**
	 * Показываем юзеров
	 *
	 */
	protected function EventIndex() {
		/**
		 * Получаем статистику
		 */
		$this->GetStats();
		/**
		 * По какому полю сортировать
		 */
		$sOrder='user_rating';
		if (getRequest('order')) {
			$sOrder=getRequestStr('order');
		}
		/**
		 * В каком направлении сортировать
		 */
		$sOrderWay='desc';
		if (getRequest('order_way')) {
			$sOrderWay=getRequestStr('order_way');
		}
		$aFilter=array(
			'activate' => 1
		);
		/**
		 * Передан ли номер страницы
		 */
		$iPage=$this->GetParamEventMatch(0,2) ? $this->GetParamEventMatch(0,2) : 1;
		/**
		 * Получаем список юзеров
		 */
		$aResult=LS::Make(ModuleUser::class)->GetUsersByFilter($aFilter,array($sOrder=>$sOrderWay),$iPage,Config::Get('module.user.per_page'));
		$aUsers=$aResult['collection'];
		/**
		 * Формируем постраничность
		 */
		$aPaging=LS::Make(ModuleViewer::class)->MakePaging($aResult['count'],$iPage,Config::Get('module.user.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('people').'index',array('order'=>$sOrder,'order_way'=>$sOrderWay));
		/**
		 * Получаем алфавитный указатель на список пользователей
		 */
		$aPrefixUser=LS::Make(ModuleUser::class)->GetGroupPrefixUser(1);
		/**
		 * Загружаем переменные в шаблон
		 */
		LS::Make(ModuleViewer::class)->Assign('aPaging',$aPaging);
		LS::Make(ModuleViewer::class)->Assign('aUsersRating',$aUsers);
		LS::Make(ModuleViewer::class)->Assign('aPrefixUser',$aPrefixUser);
		LS::Make(ModuleViewer::class)->Assign("sUsersOrder",htmlspecialchars($sOrder));
		LS::Make(ModuleViewer::class)->Assign("sUsersOrderWay",htmlspecialchars($sOrderWay));
		LS::Make(ModuleViewer::class)->Assign("sUsersOrderWayNext",htmlspecialchars($sOrderWay=='desc' ? 'asc' : 'desc'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('index');
	}
	/**
	 * Получение статистики
	 *
	 */
	protected function GetStats() {
		/**
		 * Статистика кто, где и т.п.
		 */
		$aStat=LS::Make(ModuleUser::class)->GetStatUsers();
		/**
		 * Загружаем переменные в шаблон
		 */
		LS::Make(ModuleViewer::class)->Assign('aStat',$aStat);
	}

	/**
	 * Выполняется при завершении работы экшена
	 *
	 */
	public function EventShutdown() {
		/**
		 * Загружаем в шаблон необходимые переменные
		 */
		LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect',$this->sMenuHeadItemSelect);
		LS::Make(ModuleViewer::class)->Assign('sMenuItemSelect',$this->sMenuItemSelect);
	}
}
