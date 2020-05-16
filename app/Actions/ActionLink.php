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

use App\Modules\ModuleACL;
use App\Modules\ModuleBlog;
use App\Modules\ModuleComment;
use App\Modules\ModuleStream;
use App\Modules\ModuleSubscribe;
use App\Entities\EntityTopic;
use App\Modules\ModuleTopic;
use App\Entities\EntityUser;
use App\Modules\ModuleUser;
use Engine\Action;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleSecurity;
use Engine\Modules\ModuleText;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки УРЛа вида /link/ - управление своими топиками(тип: ссылка)
 *
 * @package actions
 * @since 1.0
 */
class ActionLink extends Action {
	/**
	 * Главное меню
	 *
	 * @var string
	 */
	protected $sMenuHeadItemSelect='blog';
	/**
	 * Меню
	 *
	 * @var string
	 */
	protected $sMenuItemSelect='topic';
	/**
	 * СубМеню
	 *
	 * @var string
	 */
	protected $sMenuSubItemSelect='link';
	/**
	 * Текущий юзер
	 *
	 * @var \App\Entities\EntityUser|null
	 */
	protected $oUserCurrent=null;

	/**
	 * Инициализация
	 *
	 */
	public function Init() {
		/**
		 * Получаем текущего пользователя
		 */
		$this->oUserCurrent=LS::Make(ModuleUser::class)->GetUserCurrent();
		/**
		 * Устанавливаем дефолтный евент
		 */
		$this->SetDefaultEvent('add');
		/**
		 * Устанавливаем title страницы
		 */
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('topic_link_title'));
	}
	/**
	 * Регистрируем евенты
	 *
	 */
	protected function RegisterEvent() {
		$this->AddEvent('add','EventAdd');
		$this->AddEvent('edit','EventEdit');
		$this->AddEvent('go','EventGo');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	/**
	 * Переход по ссылке с подсчетом количества переходов
	 *
	 */
	protected function EventGo() {
		/**
		 * Получаем номер топика из УРЛ и проверяем существует ли он
		 */
		$sTopicId=$this->GetParam(0);
		if (!($oTopic=LS::Make(ModuleTopic::class)->GetTopicById($sTopicId)) or !$oTopic->getPublish()) {
			parent::EventNotFound();
			return;
		}
		/**
		 * проверяем является ли топик ссылкой
		 */
		if ($oTopic->getType()!='link') {
			parent::EventNotFound();
			return;
		}
		/**
		 * увелививаем число переходов по ссылке
		 */
		$oTopic->setLinkCountJump($oTopic->getLinkCountJump()+1);
		LS::Make(ModuleTopic::class)->UpdateTopic($oTopic);
		/**
		 * собственно сам переход по ссылке
		 */
		Router::Location($oTopic->getLinkUrl());
	}
	/**
	 * Редактирование топика-ссылки
	 *
	 */
	protected function EventEdit() {
		/**
		 * Проверяем авторизован ли юзер
		 */
		if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'),LS::Make(ModuleLang::class)->Get('error'));
			Router::Action('error'); return;
		}
		/**
		 * Получаем номер топика из УРЛ и проверяем существует ли он
		 */
		$sTopicId=$this->GetParam(0);
		if (!($oTopic=LS::Make(ModuleTopic::class)->GetTopicById($sTopicId))) {
			parent::EventNotFound(); return;
		}
		/**
		 * Проверяем тип топика
		 */
		if ($oTopic->getType()!='link') {
			parent::EventNotFound(); return;
		}
		/**
		 * Если права на редактирование
		 */
		if (!LS::Make(ModuleACL::class)->IsAllowEditTopic($oTopic,$this->oUserCurrent)) {
			parent::EventNotFound(); return;
		}
		/**
		 * Вызов хуков
		 */
		LS::Make(ModuleHook::class)->Run('topic_edit_show',array('oTopic'=>$oTopic));
		/**
		 * Загружаем переменные в шаблон
		 */
		LS::Make(ModuleViewer::class)->Assign('aBlogsAllow',LS::Make(ModuleBlog::class)->GetBlogsAllowByUser($this->oUserCurrent));
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('topic_link_title_edit'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('add');
		/**
		 * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
		 */
		if (isset($_REQUEST['submit_topic_publish']) or isset($_REQUEST['submit_topic_save'])) {
			/**
			 * Обрабатываем отправку формы
			 */
			$this->SubmitEdit($oTopic);
			return;
		} else {
			/**
			 * Заполняем поля формы для редактирования
			 * Только перед отправкой формы!
			 */
			$_REQUEST['topic_title']=$oTopic->getTitle();
			$_REQUEST['topic_link_url']=$oTopic->getLinkUrl();
			$_REQUEST['topic_text']=$oTopic->getTextSource();
			$_REQUEST['topic_tags']=$oTopic->getTags();
			$_REQUEST['blog_id']=$oTopic->getBlogId();
			$_REQUEST['topic_id']=$oTopic->getId();
			$_REQUEST['topic_publish_index']=$oTopic->getPublishIndex();
			$_REQUEST['topic_forbid_comment']=$oTopic->getForbidComment();
		}
	}
	/**
	 * Добавление топика-ссылки
	 *
	 */
	protected function EventAdd() {
		/**
		 * Проверяем авторизован ли юзер
		 */
		if (!LS::Make(ModuleUser::class)->IsAuthorization()) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'),LS::Make(ModuleLang::class)->Get('error'));
			Router::Action('error'); return;
		}
		/**
		 * Вызов хуков
		 */
		LS::Make(ModuleHook::class)->Run('topic_add_show');
		/**
		 * Загружаем переменные в шаблон
		 */
		LS::Make(ModuleViewer::class)->Assign('aBlogsAllow',LS::Make(ModuleBlog::class)->GetBlogsAllowByUser($this->oUserCurrent));
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('topic_link_title_create'));
		/**
		 * Обрабатываем отправку формы
		 */
		$this->SubmitAdd();
		return;
	}
	/**
	 * Обработка добавлени топика
     */
	protected function SubmitAdd() {
		/**
		 * Проверяем отправлена ли форма с данными(хотяб одна кнопка)
		 */
		if (!isPost('submit_topic_publish') and !isPost('submit_topic_save')) {
			return;
		}
		$oTopic = new EntityTopic();
		$oTopic->_setValidateScenario('link');
		/**
		 * Заполняем поля для валидации
		 */
		$oTopic->setBlogId(getRequestStr('blog_id'));
		$oTopic->setTitle(strip_tags(getRequestStr('topic_title')));
		$oTopic->setTextSource(getRequestStr('topic_text'));
		$oTopic->setTags(getRequestStr('topic_tags'));
		$oTopic->setUserId($this->oUserCurrent->getId());
		$oTopic->setType('link');
		$oTopic->setLinkUrl(getRequestStr('topic_link_url'));
		$oTopic->setDateAdd(date("Y-m-d H:i:s"));
		$oTopic->setUserIp(func_getIp());
		/**
		 * Проверка корректности полей формы
		 */
		if (!$this->checkTopicFields($oTopic)) {
			return;
		}
		/**
		 * Определяем в какой блог делаем запись
		 */
		$iBlogId=$oTopic->getBlogId();
		if ($iBlogId==0) {
			$oBlog=LS::Make(ModuleBlog::class)->GetPersonalBlogByUserId($this->oUserCurrent->getId());
		} else {
			$oBlog=LS::Make(ModuleBlog::class)->GetBlogById($iBlogId);
		}
		/**
		 * Если блог не определен выдаем предупреждение
		 */
		if (!$oBlog) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('topic_create_blog_error_unknown'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Проверяем права на постинг в блог
		 */
		if (!LS::Make(ModuleACL::class)->IsAllowBlog($oBlog,$this->oUserCurrent)) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('topic_create_blog_error_noallow'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Проверяем разрешено ли постить топик по времени
		 */
		if (isPost('submit_topic_publish') and !LS::Make(ModuleACL::class)->CanPostTopicTime($this->oUserCurrent)) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('topic_time_limit'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Теперь можно смело добавлять топик к блогу
		 */
		$oTopic->setBlogId($oBlog->getId());
		$oTopic->setText(LS::Make(ModuleText::class)->Parser($oTopic->getTextSource()));
		$oTopic->setTextShort($oTopic->getText());
		$oTopic->setCutText(null);
		/**
		 * Публикуем или сохраняем
		 */
		if (isset($_REQUEST['submit_topic_publish'])) {
			$oTopic->setPublish(1);
			$oTopic->setPublishDraft(1);
		} else {
			$oTopic->setPublish(0);
			$oTopic->setPublishDraft(0);
		}
		/**
		 * Принудительный вывод на главную
		 */
		$oTopic->setPublishIndex(0);
		if (LS::Make(ModuleACL::class)->IsAllowPublishIndex($this->oUserCurrent))	{
			if (getRequest('topic_publish_index')) {
				$oTopic->setPublishIndex(1);
			}
		}
		/**
		 * Запрет на комментарии к топику
		 */
		$oTopic->setForbidComment(0);
		if (getRequest('topic_forbid_comment')) {
			$oTopic->setForbidComment(1);
		}
		/**
		 * Запускаем выполнение хуков
		 */
		LS::Make(ModuleHook::class)->Run('topic_add_before', array('oTopic'=>$oTopic,'oBlog'=>$oBlog));
		/**
		 * Добавляем топик
		 */
		if (LS::Make(ModuleTopic::class)->AddTopic($oTopic)) {
			LS::Make(ModuleHook::class)->Run('topic_add_after', array('oTopic'=>$oTopic,'oBlog'=>$oBlog));
			/**
			 * Получаем топик, чтоб подцепить связанные данные
			 */
			$oTopic=LS::Make(ModuleTopic::class)->GetTopicById($oTopic->getId());
			/**
			 * Обновляем количество топиков в блоге
			 */
			LS::Make(ModuleBlog::class)->RecalculateCountTopicByBlogId($oTopic->getBlogId());
			/**
			 * Добавляем автора топика в подписчики на новые комментарии к этому топику
			 */
			LS::Make(ModuleSubscribe::class)->AddSubscribeSimple('topic_new_comment',$oTopic->getId(),$this->oUserCurrent->getMail());
			//Делаем рассылку спама всем, кто состоит в этом блоге
			if ($oTopic->getPublish()==1 and $oBlog->getType()!='personal') {
				LS::Make(ModuleTopic::class)->SendNotifyTopicNew($oBlog,$oTopic,$this->oUserCurrent);
			}
			/**
			 * Добавляем событие в ленту
			 */
			LS::Make(ModuleStream::class)->write($oTopic->getUserId(), 'add_topic', $oTopic->getId(),$oTopic->getPublish() && $oBlog->getType()!='close');
			Router::Location($oTopic->getUrl());
		} else {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
			Router::Action('error'); return;
		}
	}
	/**
	 * Обработка редактирования топика
	 *
	 * @param EntityTopic $oTopic
	 */
	protected function SubmitEdit($oTopic) {
		$oTopic->_setValidateScenario('link');
		/**
		 * Сохраняем старое значение идентификатора блога
		 */
		$sBlogIdOld = $oTopic->getBlogId();
		/**
		 * Заполняем поля для валидации
		 */
		$oTopic->setBlogId(getRequestStr('blog_id'));
		$oTopic->setTitle(strip_tags(getRequestStr('topic_title')));
		$oTopic->setLinkUrl(getRequestStr('topic_link_url'));
		$oTopic->setTextSource(getRequestStr('topic_text'));
		$oTopic->setTags(getRequestStr('topic_tags'));
		$oTopic->setUserIp(func_getIp());
		/**
		 * Проверка корректности полей формы
		 */
		if (!$this->checkTopicFields($oTopic)) {
			return;
		}
		/**
		 * Определяем в какой блог делаем запись
		 */
		$iBlogId=$oTopic->getBlogId();
		if ($iBlogId==0) {
			$oBlog=LS::Make(ModuleBlog::class)->GetPersonalBlogByUserId($oTopic->getUserId());
		} else {
			$oBlog=LS::Make(ModuleBlog::class)->GetBlogById($iBlogId);
		}
		/**
		 * Если блог не определен выдаем предупреждение
		 */
		if (!$oBlog) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('topic_create_blog_error_unknown'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Проверяем права на постинг в блог
		 */
		if (!LS::Make(ModuleACL::class)->IsAllowBlog($oBlog,$this->oUserCurrent)) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('topic_create_blog_error_noallow'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Проверяем разрешено ли постить топик по времени
		 */
		if (isPost('submit_topic_publish') and !$oTopic->getPublishDraft() and !LS::Make(ModuleACL::class)->CanPostTopicTime($this->oUserCurrent)) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('topic_time_limit'),LS::Make(ModuleLang::class)->Get('error'));
			return;
		}
		/**
		 * Теперь можно смело редактировать топик
		 */
		$oTopic->setBlogId($oBlog->getId());
		$oTopic->setText(LS::Make(ModuleText::class)->Parser($oTopic->getTextSource()));
		$oTopic->setTextShort($oTopic->getText());
		/**
		 * Публикуем или сохраняем в черновиках
		 */
		$bSendNotify=false;
		if (isset($_REQUEST['submit_topic_publish'])) {
			$oTopic->setPublish(1);
			if ($oTopic->getPublishDraft()==0) {
				$oTopic->setPublishDraft(1);
				$oTopic->setDateAdd(date("Y-m-d H:i:s"));
				$bSendNotify=true;
			}
		} else {
			$oTopic->setPublish(0);
		}
		/**
		 * Принудительный вывод на главную
		 */
		if (LS::Make(ModuleACL::class)->IsAllowPublishIndex($this->oUserCurrent))	{
			if (getRequest('topic_publish_index')) {
				$oTopic->setPublishIndex(1);
			} else {
				$oTopic->setPublishIndex(0);
			}
		}
		/**
		 * Запрет на комментарии к топику
		 */
		$oTopic->setForbidComment(0);
		if (getRequest('topic_forbid_comment')) {
			$oTopic->setForbidComment(1);
		}
		LS::Make(ModuleHook::class)->Run('topic_edit_before', array('oTopic'=>$oTopic,'oBlog'=>$oBlog));
		/**
		 * Сохраняем топик
		 */
		if (LS::Make(ModuleTopic::class)->UpdateTopic($oTopic)) {
			LS::Make(ModuleHook::class)->Run('topic_edit_after', array('oTopic'=>$oTopic,'oBlog'=>$oBlog,'bSendNotify'=>&$bSendNotify));
			/**
			 * Обновляем данные в комментариях, если топик был перенесен в новый блог
			 */
			if($sBlogIdOld!=$oTopic->getBlogId()) {
				LS::Make(ModuleComment::class)->UpdateTargetParentByTargetId($oTopic->getBlogId(), 'topic', $oTopic->getId());
				LS::Make(ModuleComment::class)->UpdateTargetParentByTargetIdOnline($oTopic->getBlogId(), 'topic', $oTopic->getId());
			}
			/**
			 * Обновляем количество топиков в блоге
			 */
			if ($sBlogIdOld!=$oTopic->getBlogId()) {
				LS::Make(ModuleBlog::class)->RecalculateCountTopicByBlogId($sBlogIdOld);
			}
			LS::Make(ModuleBlog::class)->RecalculateCountTopicByBlogId($oTopic->getBlogId());
			/**
			 * Добавляем событие в ленту
			 */
			LS::Make(ModuleStream::class)->write($oTopic->getUserId(), 'add_topic', $oTopic->getId(),$oTopic->getPublish() && $oBlog->getType()!='close');
			/**
			 * Рассылаем о новом топике подписчикам блога
			 */
			if ($bSendNotify)	 {
				LS::Make(ModuleTopic::class)->SendNotifyTopicNew($oBlog,$oTopic,$this->oUserCurrent);
			}
			if (!$oTopic->getPublish() and !$this->oUserCurrent->isAdministrator() and $this->oUserCurrent->getId()!=$oTopic->getUserId()) {
				Router::Location($oBlog->getUrlFull());
			}
			Router::Location($oTopic->getUrl());
		} else {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
			Router::Action('error'); return;
		}
	}
	/**
	 * Проверка полей формы
	 *
	 * @param EntityTopic $oTopic
	 *
	 * @return bool
	 */
	protected function checkTopicFields($oTopic) {
		LS::Make(ModuleSecurity::class)->ValidateSendForm();

		$bOk=true;
		if (!$oTopic->_Validate()) {
			LS::Make(ModuleMessage::class)->AddError($oTopic->_getValidateError(),LS::Make(ModuleLang::class)->Get('error'));
			$bOk=false;
		}
		/**
		 * Выполнение хуков
		 */
		LS::Make(ModuleHook::class)->Run('check_link_fields', array('bOk'=>&$bOk));
		return $bOk;
	}
	/**
	 * При завершении экшена загружаем необходимые переменные
	 *
	 */
	public function EventShutdown() {
		LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect',$this->sMenuHeadItemSelect);
		LS::Make(ModuleViewer::class)->Assign('sMenuItemSelect',$this->sMenuItemSelect);
		LS::Make(ModuleViewer::class)->Assign('sMenuSubItemSelect',$this->sMenuSubItemSelect);
	}
}
