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

use App\Entities\EntityStaticPage;
use App\Modules\ModuleStaticPage;
use App\Entities\EntityTopic;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Engine;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleSecurity;
use Engine\Modules\ModuleViewer;
use Engine\Router;

class ActionPage extends Action {
	protected $sUserLogin=null;
	protected $aBadPageUrl=array('admin');

	public function Init() {
	}
	/**
	 * Регистрируем евенты
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEvent('admin','EventAdmin');

		$this->AddEventPreg('/^filter$/i', '/^[\w\-\_]+$/i', 'EventFilter');

		$this->AddEventPreg('/^[\w\-\_]*$/i','EventShowPage');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	/**
	 * Отображение страницы
	 */
	protected function EventShowPage() {
		/**
		 * Составляем полный URL страницы для поиска по нему в БД
		 */
		$sUrlFull=join('/',$this->GetParams());
		if ($sUrlFull!='') {
			$sUrlFull=$this->sCurrentEvent.'/'.$sUrlFull;
		} else {
			$sUrlFull=$this->sCurrentEvent;
		}
		/**
		 * Ищем страничку в БД
		 */
		if (!($oPage=LS::Make(ModuleStaticPage::class)->GetPageByUrlFull($sUrlFull,1))) {
			$this->EventNotFound();
			return;
		}
		/**
		 * Заполняем HTML теги и SEO
		 */
		LS::Make(ModuleViewer::class)->AddHtmlTitle($oPage->getTitle());
		if ($oPage->getSeoKeywords()) {
			LS::Make(ModuleViewer::class)->SetHtmlKeywords($oPage->getSeoKeywords());
		}
		if ($oPage->getSeoDescription()) {
			LS::Make(ModuleViewer::class)->SetHtmlDescription($oPage->getSeoDescription());
		}

		LS::Make(ModuleViewer::class)->Assign('oPage',$oPage);
		/**
		 * Устанавливаем шаблон для вывода
		 */
		$this->SetTemplateAction('page');
	}

	/**
	 * Админка статическими страницами
	 *
	 */
	protected function EventAdmin() {
		/**
		 * Если пользователь не авторизован и не админ, то выкидываем его
		 */
		$oUserCurrent=LS::Make(ModuleUser::class)->GetUserCurrent();
		if (!$oUserCurrent or !$oUserCurrent->isAdministrator()) {
			$this->EventNotFound(); return;
		}

		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('page.admin'));
		/**
		 * Обработка создания новой странички
		 */
		if (isPost('submit_page_save')) {
			if (!getRequest('page_id')) {
				$this->SubmitAddPage();
			}
		}
		/**
		 * Обработка показа странички для редактирования
		 */
		if ($this->GetParam(0)=='edit') {
			if ($oPageEdit=LS::Make(ModuleStaticPage::class)->GetPageById($this->GetParam(1))) {
				if (!isPost('submit_page_save')) {
					$_REQUEST['page_title']=$oPageEdit->getTitle();
					$_REQUEST['page_pid']=$oPageEdit->getPid();
					$_REQUEST['page_url']=$oPageEdit->getUrl();
					$_REQUEST['page_text']=$oPageEdit->getText();
					$_REQUEST['page_seo_keywords']=$oPageEdit->getSeoKeywords();
					$_REQUEST['page_seo_description']=$oPageEdit->getSeoDescription();
					$_REQUEST['page_active']=$oPageEdit->getActive();
					$_REQUEST['page_main']=$oPageEdit->getMain();
					$_REQUEST['page_sort']=$oPageEdit->getSort();
					$_REQUEST['page_auto_br']=$oPageEdit->getAutoBr();
					$_REQUEST['page_id']=$oPageEdit->getId();
				}	else {
					/**
					 * Если отправили форму с редактированием, то обрабатываем её
					 */
					$this->SubmitEditPage($oPageEdit);
				}
				LS::Make(ModuleViewer::class)->Assign('oPageEdit',$oPageEdit);
			} else {
				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('page.edit_notfound'),LS::Make(ModuleLang::class)->Get('error'));
				$this->SetParam(0,null);
			}
		}
		/**
		 * Обработка удаления страницы
		 * Замечание: если используется тип таблиц MyISAM, а InnoDB то возможно некорректное удаление вложенных страниц
		 */
		if ($this->GetParam(0)=='delete') {
			LS::Make(ModuleSecurity::class)->ValidateSendForm();
			if (LS::Make(ModuleStaticPage::class)->deletePageById($this->GetParam(1))) {
				LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('page.admin_action_delete_ok'));
			} else {
				LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('page.admin_action_delete_error'),LS::Make(ModuleLang::class)->Get('error'));
			}
		}
		/**
		 * Обработка изменения сортировки страницы
		 */
		if ($this->GetParam(0)=='sort' and $oPage=LS::Make(ModuleStaticPage::class)->GetPageById($this->GetParam(1))) {
			LS::Make(ModuleSecurity::class)->ValidateSendForm();
			$sWay=$this->GetParam(2)=='down' ? 'down' : 'up';
			$iSortOld=$oPage->getSort();
			if ($oPagePrev=LS::Make(ModuleStaticPage::class)->GetNextPageBySort($iSortOld,$oPage->getPid(),$sWay)) {
				$iSortNew=$oPagePrev->getSort();
				$oPagePrev->setSort($iSortOld);
				LS::Make(ModuleStaticPage::class)->UpdatePage($oPagePrev);
			} else {
				if ($sWay=='down') {
					$iSortNew=$iSortOld-1;
				} else {
					$iSortNew=$iSortOld+1;
				}
			}
			/**
			 * Меняем значения сортировки местами
			 */
			$oPage->setSort($iSortNew);
			LS::Make(ModuleStaticPage::class)->UpdatePage($oPage);
		}
		/**
		 * Получаем и загружаем список всех страниц
		 */
		$aPages=LS::Make(ModuleStaticPage::class)->GetPages();
		if (count($aPages)==0 and LS::Make(ModuleStaticPage::class)->GetCountPage()) {
			LS::Make(ModuleStaticPage::class)->SetPagesPidToNull();
			$aPages=LS::Make(ModuleStaticPage::class)->GetPages();
		}
		LS::Make(ModuleViewer::class)->Assign('aPages',$aPages);
	}

	/**
     * Поиск последнего топика, соответствующего данному фильтру, и редирект на него.
     */
	protected function EventFilter() {
	    $filter_id = $this->GetParam(0);
	    if(gettype($filter_id) != 'string' || !isset(Config::Get('page.filters')[$filter_id])) {
	        parent::EventNotFound(); return;
        }
        $aFilter = Config::Get('page.filters')[$filter_id];

        $eng = Engine::getInstance();

	    /** @var ModuleTopic $topic */
	    $topic = $eng->make(ModuleTopic::class);

	    /** @var EntityTopic $last_topic */
	    $last_topic = reset($topic->GetTopicsByFilter($aFilter, 1, 1, ['blog'])['collection']);

        if(!$last_topic) {
            parent::EventNotFound(); return;
        }

        Router::Location($last_topic->getUrl());
        return;
    }
	/**
	 * Обработка отправки формы при редактировании страницы
	 *
	 * @param EntityStaticPage $oPageEdit
	 */
	protected function SubmitEditPage($oPageEdit) {
		/**
		 * Проверяем корректность полей
		 */
		if (!$this->CheckPageFields()) {
			return ;
		}
		if ($oPageEdit->getId()==getRequest('page_pid')) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'));
			return;
		}

		/**
		 * Обновляем свойства страницы
		 */
		$oPageEdit->setActive(getRequest('page_active') ? 1 : 0);
		$oPageEdit->setAutoBr(getRequest('page_auto_br') ? 1 : 0);
		$oPageEdit->setMain(getRequest('page_main') ? 1 : 0);
		$oPageEdit->setDateEdit(date("Y-m-d H:i:s"));
		if (getRequest('page_pid')==0) {
			$oPageEdit->setUrlFull(getRequest('page_url'));
			$oPageEdit->setPid(null);
		} else {
			$oPageEdit->setPid(getRequest('page_pid'));
			$oPageParent=LS::Make(ModuleStaticPage::class)->GetPageById(getRequest('page_pid'));
			$oPageEdit->setUrlFull($oPageParent->getUrlFull().'/'.getRequest('page_url'));
		}
		$oPageEdit->setSeoDescription(getRequest('page_seo_description'));
		$oPageEdit->setSeoKeywords(getRequest('page_seo_keywords'));
		$oPageEdit->setText(getRequest('page_text'));
		$oPageEdit->setTitle(getRequest('page_title'));
		$oPageEdit->setUrl(getRequest('page_url'));
		$oPageEdit->setSort(getRequest('page_sort'));
		/**
		 * Обновляем страницу
		 */
		if (LS::Make(ModuleStaticPage::class)->UpdatePage($oPageEdit)) {
			LS::Make(ModuleStaticPage::class)->RebuildUrlFull($oPageEdit);
			LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('page.edit_submit_save_ok'));
			$this->SetParam(0,null);
			$this->SetParam(1,null);
		} else {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'));
		}
	}
	/**
	 * Обработка отправки формы добавления новой страницы
	 *
	 */
	protected function SubmitAddPage() {
		/**
		 * Проверяем корректность полей
		 */
		if (!$this->CheckPageFields()) {
			return ;
		}
		/**
		 * Заполняем свойства
		 */
		$oPage = new EntityStaticPage();
		$oPage->setActive(getRequest('page_active') ? 1 : 0);
		$oPage->setAutoBr(getRequest('page_auto_br') ? 1 : 0);
		$oPage->setMain(getRequest('page_main') ? 1 : 0);
		$oPage->setDateAdd(date("Y-m-d H:i:s"));
		if (getRequest('page_pid')==0) {
			$oPage->setUrlFull(getRequest('page_url'));
			$oPage->setPid(null);
		} else {
			$oPage->setPid(getRequest('page_pid'));
			$oPageParent=LS::Make(ModuleStaticPage::class)->GetPageById(getRequest('page_pid'));
			$oPage->setUrlFull($oPageParent->getUrlFull().'/'.getRequest('page_url'));
		}
		$oPage->setSeoDescription(getRequest('page_seo_description'));
		$oPage->setSeoKeywords(getRequest('page_seo_keywords'));
		$oPage->setText(getRequest('page_text'));
		$oPage->setTitle(getRequest('page_title'));
		$oPage->setUrl(getRequest('page_url'));
		if (getRequest('page_sort')) {
			$oPage->setSort(getRequest('page_sort'));
		} else {
			$oPage->setSort(LS::Make(ModuleStaticPage::class)->GetMaxSortByPid($oPage->getPid()) + 1);
		}
		/**
		 * Добавляем страницу
		 */
		if (LS::Make(ModuleStaticPage::class)->AddPage($oPage)) {
			LS::Make(ModuleMessage::class)->AddNotice(LS::Make(ModuleLang::class)->Get('page.create_submit_save_ok'));
			$this->SetParam(0,null);
		} else {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('system_error'));
		}
	}
	/**
	 * Проверка полей на корректность
	 *
	 * @return bool
	 */
	protected function CheckPageFields() {
		LS::Make(ModuleSecurity::class)->ValidateSendForm();

		$bOk=true;
		/**
		 * Проверяем есть ли заголовок топика
		 */
		if (!func_check(getRequest('page_title',null,'post'),'text',2,200)) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('page.create_title_error'),LS::Make(ModuleLang::class)->Get('error'));
			$bOk=false;
		}
		/**
		 * Проверяем есть ли заголовок топика, с заменой всех пробельных символов на "_"
		 */
		$pageUrl=preg_replace("/\s+/",'_',(string)getRequest('page_url',null,'post'));
		$_REQUEST['page_url']=$pageUrl;
		if (!func_check(getRequest('page_url',null,'post'),'login',1,50)) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('page.create_url_error'),LS::Make(ModuleLang::class)->Get('error'));
			$bOk=false;
		}
		/**
		 * Проверяем на счет плохих УРЛов
		 */
		if (in_array(getRequest('page_url',null,'post'),$this->aBadPageUrl)) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('page.create_url_error_bad').' '.join(',',$this->aBadPageUrl),LS::Make(ModuleLang::class)->Get('error'));
			$bOk=false;
		}
		/**
		 * Проверяем есть ли содержание страницы
		 */
		if (!func_check(getRequest('page_text',null,'post'),'text',1,50000)) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('page.create_text_error'),LS::Make(ModuleLang::class)->Get('error'));
			$bOk=false;
		}
		/**
		 * Проверяем страницу в которую хотим вложить
		 */
		if (getRequest('page_pid')!=0 and !($oPageParent=LS::Make(ModuleStaticPage::class)->GetPageById(getRequest('page_pid')))) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('page.create_parent_page_error'),LS::Make(ModuleLang::class)->Get('error'));
			$bOk=false;
		}
		/**
		 * Проверяем сортировку
		 */
		if (getRequest('page_sort') and !is_numeric(getRequest('page_sort'))) {
			LS::Make(ModuleMessage::class)->AddError(LS::Make(ModuleLang::class)->Get('page.create_sort_error'),LS::Make(ModuleLang::class)->Get('error'));
			$bOk=false;
		}
		/**
		 * Выполнение хуков
		 */
		LS::Make(ModuleHook::class)->Run('check_page_fields', array('bOk'=>&$bOk));

		return $bOk;
	}
}
