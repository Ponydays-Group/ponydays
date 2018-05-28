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


/**
 * Экшен обработки URL'ов вида /deleted/
 *
 * @package actions
 * @since 1.0
 */
class ActionDeleted extends Action
{
	/**
	 * Главное меню
	 *
	 * @var string
	 */
	protected $sMenuHeadItemSelect = 'deleted';
	/**
	 * Какое меню активно
	 *
	 * @var string
	 */
	protected $sMenuItemSelect = 'deleted';
	/**
	 * Какое подменю активно
	 *
	 * @var string
	 */
	protected $sMenuSubItemSelect = 'topics';
	/**
	 * УРЛ блога который подставляется в меню
	 *
	 * @var string
	 */
	protected $sMenuSubBlogUrl;
	/**
	 * Текущий пользователь
	 *
	 * @var ModuleUser_EntityUser|null
	 */
	protected $oUserCurrent = null;
	/**
	 * Число новых топиков в коллективных блогах
	 *
	 * @var int
	 */
	protected $iCountTopicsCollectiveNew = 0;
	/**
	 * Число новых топиков в персональных блогах
	 *
	 * @var int
	 */
	protected $iCountTopicsPersonalNew = 0;
	/**
	 * Число новых топиков в конкретном блоге
	 *
	 * @var int
	 */
	protected $iCountTopicsBlogNew = 0;
	/**
	 * Число новых топиков
	 *
	 * @var int
	 */
	protected $iCountTopicsNew = 0;

    /**
     * Инизиализация экшена
     *
     */
    public function Init()
    {
        /**
         * Устанавливаем евент по дефолту, т.е. будем показывать хорошие топики из коллективных блогов
         */
        $this->SetDefaultEvent('topics');
        $this->sMenuSubBlogUrl = Router::GetPath('deleted');
        /**
         * Достаём текущего пользователя
         */
        $this->oUserCurrent = $this->User_GetUserCurrent();
        /**
         * Подсчитываем новые топики
         */
    }

    /**
     * Регистрируем евенты, по сути определяем УРЛы вида /blog/.../
     *
     */
    protected function RegisterEvent()
    {
		$this->AddEvent('topics', array('EventDeletedTopics', 'topics'));
		$this->AddEvent('blogs', array('EventDeletedBlogs', 'blogs'));

    }


    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

	/**
	 * Показ всех удаленных топиков
	 *
	 */
	protected function EventDeletedTopics()
	{
		$sPeriod = 'all';
		$sShowType = 'topics';
		/**
		 * Меню
		 */
		$this->sMenuSubItemSelect =  $sShowType;
		/**
		 * Передан ли номер страницы
		 */
		$iPage = $this->GetParamEventMatch(0, 2) ? $this->GetParamEventMatch(0, 2) : 1;
		if ($iPage == 1 and !getRequest('period')) {
			$this->Viewer_SetHtmlCanonical(Router::GetPath('deleted') . $sShowType . '/');
		}
		/**
		 * Получаем список топиков
		 */
		$aResult = $this->Topic_GetDeletedTopicsCollective($iPage, Config::Get('module.topic.per_page'), $sShowType, $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24);
		$aTopics = $aResult['collection'];
		$aTopicsC = [];
		foreach ($aTopics as $oTopic ) {
			/**
			 * проверяем есть ли право на удаление топика
			 */
			if ($this->oUserCurrent && $this->ACL_IsAllowDeleteTopic($oTopic,$this->oUserCurrent)) {
				array_push($aTopicsC, $oTopic);
			}
		}
		$aTopics = $aTopicsC;
		/**
		 * Вызов хуков
		 */
		$this->Hook_Run('topics_list_show', array('aTopics' => $aTopics));
		/**
		 * Формируем постраничность
		 */
		$aPaging = $this->Viewer_MakePaging($aResult['count'], $iPage, Config::Get('module.topic.per_page'), Config::Get('pagination.pages.count'), Router::GetPath('deleted') . $sShowType, in_array($sShowType, array('discussed', 'top')) ? array('period' => $sPeriod) : array());
		/**
		 * Вызов хуков
		 */
		$this->Hook_Run('blog_show', array('sShowType' => $sShowType));
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aTopics', $aTopics);
		$this->Viewer_Assign('aPaging', $aPaging);
		$this->Viewer_Assign('bInTrash', true);
		if (in_array($sShowType, array('discussed', 'top'))) {
			$this->Viewer_Assign('sPeriodSelectCurrent', $sPeriod);
			$this->Viewer_Assign('sPeriodSelectRoot', Router::GetPath('deleted') . $sShowType . '/');
		}
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('deleted_topics');
	}
	/**
	 * Показ всех удаленных блогов
	 *
	 */
	protected function EventDeletedBlogs()
	{
		$sShowType = 'blogs';
		/**
		 * Меню
		 */
		$this->sMenuSubItemSelect =  $sShowType;
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
			'exclude_type' => 'personal',
			'deleted' => 1
		);
		/**
		 * Передан ли номер страницы
		 */
		$iPage=	preg_match("/^\d+$/i",$this->GetEventMatch(2)) ? $this->GetEventMatch(2) : 1;
		/**
		 * Получаем список блогов
		 */
		$aResult=$this->Blog_GetBlogsByFilter($aFilter,array($sOrder=>$sOrderWay),$iPage,Config::Get('module.blog.per_page'));
		$aBlogs=$aResult['collection'];
		$aBlogsC = [];
		foreach ($aBlogs as $aBlog ) {
			/**
			 * проверяем есть ли право на удаление топика
			 */
			if ($this->oUserCurrent && $this->ACL_IsAllowDeleteBlog($aBlog,$this->oUserCurrent)) {
				array_push($aBlogsC, $aBlog);
			}
		}
		$aBlogs = $aBlogsC;
		/**
		 * Формируем постраничность
		 */
		$aPaging=$this->Viewer_MakePaging($aResult['count'],$iPage,Config::Get('module.blog.per_page'),Config::Get('pagination.pages.count'),Router::GetPath('blogs'),array('order'=>$sOrder,'order_way'=>$sOrderWay));
		/**
		 * Загружаем переменные в шаблон
		 */
		$this->Viewer_Assign('aPaging',$aPaging);
		$this->Viewer_Assign("aBlogs",$aBlogs);
		$this->Viewer_Assign("sBlogOrder",htmlspecialchars($sOrder));
		$this->Viewer_Assign("sBlogOrderWay",htmlspecialchars($sOrderWay));
		$this->Viewer_Assign("sBlogOrderWayNext",htmlspecialchars($sOrderWay=='desc' ? 'asc' : 'desc'));
		/**
		 * Устанавливаем title страницы
		 */
		$this->Viewer_AddHtmlTitle($this->Lang_Get('blog_menu_all_list'));
		/**
		 * Устанавливаем шаблон вывода
		 */
		$this->SetTemplateAction('deleted_blogs');
	}

    /**
     * Выполняется при завершении работы экшена
     *
     */
    public function EventShutdown()
    {
        /**
         * Загружаем в шаблон необходимые переменные
         */
        $this->Viewer_Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
        $this->Viewer_Assign('sMenuItemSelect', $this->sMenuItemSelect);
        $this->Viewer_Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
        $this->Viewer_Assign('sMenuSubBlogUrl', $this->sMenuSubBlogUrl);
        $this->Viewer_Assign('iCountTopicsCollectiveNew', $this->iCountTopicsCollectiveNew);
        $this->Viewer_Assign('iCountTopicsPersonalNew', $this->iCountTopicsPersonalNew);
        $this->Viewer_Assign('iCountTopicsBlogNew', $this->iCountTopicsBlogNew);
        $this->Viewer_Assign('iCountTopicsNew', $this->iCountTopicsNew);

        $this->Viewer_Assign('BLOG_USER_ROLE_GUEST', ModuleBlog::BLOG_USER_ROLE_GUEST);
        $this->Viewer_Assign('BLOG_USER_ROLE_USER', ModuleBlog::BLOG_USER_ROLE_USER);
        $this->Viewer_Assign('BLOG_USER_ROLE_MODERATOR', ModuleBlog::BLOG_USER_ROLE_MODERATOR);
        $this->Viewer_Assign('BLOG_USER_ROLE_ADMINISTRATOR', ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR);
        $this->Viewer_Assign('BLOG_USER_ROLE_INVITE', ModuleBlog::BLOG_USER_ROLE_INVITE);
        $this->Viewer_Assign('BLOG_USER_ROLE_REJECT', ModuleBlog::BLOG_USER_ROLE_REJECT);
        $this->Viewer_Assign('BLOG_USER_ROLE_BAN', ModuleBlog::BLOG_USER_ROLE_BAN);
    }
}

?>
