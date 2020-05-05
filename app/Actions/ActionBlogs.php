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
use App\Modules\User\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\Lang\ModuleLang;
use Engine\Modules\Message\ModuleMessage;
use Engine\Modules\Viewer\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки УРЛа вида /comments/
 *
 * @package actions
 * @since 1.0
 */
class ActionBlogs extends Action {
	/**
	 * Инициализация
	 */
	public function Init() {
		/**
		 * Загружаем в шаблон JS текстовки
		 */
		LS::Make(ModuleLang::class)->AddLangJs(array(
								  'blog_join','blog_leave'
							  ));
	}
	/**
	 * Регистрируем евенты
	 */
	protected function RegisterEvent() {
		$this->AddEventPreg('/^(page([1-9]\d{0,5}))?$/i','EventShowBlogs');
		$this->AddEventPreg('/^ajax-search$/i','EventAjaxSearch');
	}


	/**********************************************************************************
	 ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
	 **********************************************************************************
	 */

	/**
	 * Поиск блогов по названию
	 */
	protected function EventAjaxSearch() {
		/**
		 * Устанавливаем формат Ajax ответа
		 */
		LS::Make(ModuleViewer::class)->SetResponseAjax('json');
		/**
		 * Получаем из реквеста первые буквы блога
		 */
		if ($sTitle=getRequestStr('blog_title')) {
			$sTitle=str_replace('%','',$sTitle);
		}
		if (!$sTitle) {
			LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));
			return;
		}
		/**
		 * Ищем блоги
		 */
		$aResult=LS::Make(ModuleBlog::class)->GetBlogsByFilter(array('exclude_type' => 'personal','title'=>"%{$sTitle}%"),array('blog_title'=>'asc'),1,100);
		/**
		 * Формируем и возвращает ответ
		 */
		$oViewer=LS::Make(ModuleViewer::class)->GetLocalViewer();
		$oViewer->Assign('aBlogs',$aResult['collection']);
		$oViewer->Assign('oUserCurrent',LS::Make(ModuleUser::class)->GetUserCurrent());
		$oViewer->Assign('sBlogsEmptyList',LS::Make(ModuleLang::class)->Get('blogs_search_empty'));
		LS::Make(ModuleViewer::class)->AssignAjax('sText',$oViewer->Fetch("blog_list.tpl"));
	}
	/**
	 * Отображение списка блогов
	 */
	protected function EventShowBlogs() {
		/**
		 * По какому полю сортировать
		 */
		$sOrder='blog_rating';
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
		/**
		 * Фильтр поиска блогов
		 */
		$aFilter=array(
			'exclude_type' => 'personal'
		);
		/**
		 * Передан ли номер страницы
		 */
		$iPage=	preg_match("/^\d+$/i",$this->GetEventMatch(2)) ? $this->GetEventMatch(2) : 1;
		/**
		 * Получаем список блогов
		 */
		$aResult=LS::Make(ModuleBlog::class)->GetBlogsByFilter($aFilter,array($sOrder=>$sOrderWay),$iPage,Config::Get('module.blog.per_page'));
		$aBlogs=$aResult['collection'];
		/**
		 * Формируем постраничность
		 */
		$aPaging=LS::Make(ModuleViewer::class)->MakePaging($aResult['count'],$iPage,Config::Get('module.blog.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('blogs'),array('order'=>$sOrder,'order_way'=>$sOrderWay));
		/**
		 * Загружаем переменные в шаблон
		 */
		LS::Make(ModuleViewer::class)->Assign('aPaging',$aPaging);
		LS::Make(ModuleViewer::class)->Assign("aBlogs",$aBlogs);
		LS::Make(ModuleViewer::class)->Assign("sBlogOrder",htmlspecialchars($sOrder));
		LS::Make(ModuleViewer::class)->Assign("sBlogOrderWay",htmlspecialchars($sOrderWay));
		LS::Make(ModuleViewer::class)->Assign("sBlogOrderWayNext",htmlspecialchars($sOrderWay=='desc' ? 'asc' : 'desc'));
		/**
		 * Устанавливаем title страницы
		 */
		LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('blog_menu_all_list'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('index');
	}
}
