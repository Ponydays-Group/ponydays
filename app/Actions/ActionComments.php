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

use App\Modules\ACL\ModuleACL;
use App\Modules\Blog\ModuleBlog;
use App\Modules\Comment\ModuleComment;
use App\Modules\User\Entity\ModuleUser_EntityUser;
use App\Modules\User\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Viewer\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки УРЛа вида /comments/
 *
 * @package actions
 * @since 1.0
 */
class ActionComments extends Action {
	/**
	 * Текущий юзер
	 *
	 * @var ModuleUser_EntityUser|null
	 */
	protected $oUserCurrent=null;
	/**
	 * Главное меню
	 *
	 * @var string
	 */
	protected $sMenuHeadItemSelect='blog';

	/**
	 * Инициализация
	 */
	public function Init() {
		$this->oUserCurrent=LS::Make(ModuleUser::class)->GetUserCurrent();
	}
	/**
	 * Регистрация евентов
	 */
	protected function RegisterEvent() {
		$this->AddEventPreg('/^(page([1-9]\d{0,5}))?$/i','EventComments');
		$this->AddEventPreg('/^\d+$/i','EventShowComment');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	/**
	 * Выводим список комментариев
	 *
	 */
	protected function EventComments() {
		/**
		 * Передан ли номер страницы
		 */
		$iPage=$this->GetEventMatch(2) ? $this->GetEventMatch(2) : 1;
		/**
		 * Исключаем из выборки идентификаторы закрытых блогов (target_parent_id)
		 */
		$aCloseBlogs = ($this->oUserCurrent)
			? LS::Make(ModuleBlog::class)->GetInaccessibleBlogsByUser($this->oUserCurrent)
			: LS::Make(ModuleBlog::class)->GetInaccessibleBlogsByUser();
		/**
		 * Получаем список комментов
		 */
		$aResult=LS::Make(ModuleComment::class)->GetCommentsAll('topic',$iPage,Config::Get('module.comment.per_page'),array(),$aCloseBlogs);
		$aComments=$aResult['collection'];
		/**
		 * Формируем постраничность
		 */
		$aPaging=LS::Make(ModuleViewer::class)->MakePaging($aResult['count'],$iPage,Config::Get('module.comment.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('comments'));
		/**
		 * Загружаем переменные в шаблон
		 */
		LS::Make(ModuleViewer::class)->Assign('aPaging',$aPaging);
		LS::Make(ModuleViewer::class)->Assign("aComments",$aComments);
		LS::Make(ModuleViewer::class)->Assign('bEnableCommentsVoteInfo',LS::Make(ModuleACL::class)->CheckSimpleAccessLevel(Config::Get('acl.vote_list.comment.ne_enable_level'), $this->oUserCurrent, null, '__non_checkable_visible__'));
		/**
		 * Устанавливаем title страницы
		 */
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('comments_all'));
		LS::Make(ModuleViewer::class)->SetHtmlRssAlternate(Router::GetPath('rss').'allcomments/',LS::Make(ModuleLang::class)->Get('comments_all'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('index');
	}
	/**
	 * Обрабатывает ссылку на конкретный комментарий, определят к какому топику он относится и перенаправляет на него
	 * Актуально при использовании постраничности комментариев
	 */
	protected function EventShowComment() {
		$iCommentId=$this->sCurrentEvent;
		/**
		 * Проверяем к чему относится комментарий
		 */
		if (!($oComment=LS::Make(ModuleComment::class)->GetCommentById($iCommentId))) {
			parent::EventNotFound(); return;
		}
		if ($oComment->getTargetType()!='topic' or !($oTopic=$oComment->getTarget())) {
			parent::EventNotFound(); return;
		}
		/**
		 * Определяем необходимую страницу для отображения комментария
		 */
		if (!Config::Get('module.comment.use_nested') or !Config::Get('module.comment.nested_per_page')) {
			Router::Location($oTopic->getUrl().'#comment'.$oComment->getId());
		}
		$iPage=LS::Make(ModuleComment::class)->GetPageCommentByTargetId($oComment->getTargetId(),$oComment->getTargetType(),$oComment);
		if ($iPage==1) {
			Router::Location($oTopic->getUrl().'#comment'.$oComment->getId());
		} else {
			Router::Location($oTopic->getUrl()."?cmtpage={$iPage}#comment".$oComment->getId());
		}
		exit();
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
	}
}
