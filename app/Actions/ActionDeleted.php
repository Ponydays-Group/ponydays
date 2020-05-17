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
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleViewer;
use Engine\Router;


/**
 * Экшен обработки URL'ов вида /deleted/
 *
 * @package actions
 * @since   1.0
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
     * @var \App\Entities\EntityUser|null
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
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
    }

    /**
     * Регистрируем евенты, по сути определяем УРЛы вида /blog/.../
     *
     */
    protected function RegisterEvent()
    {
        $this->AddEventPreg('/^topics$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventDeletedTopics', 'topics']);
        $this->AddEventPreg('/^blogs$/i', '/^(page([1-9]\d{0,5}))?$/i', ['EventDeletedBlogs', 'blogs']);

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
        $this->sMenuSubItemSelect = $sShowType;
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->GetParamEventMatch(0, 2) ? $this->GetParamEventMatch(0, 2) : 1;
        if ($iPage == 1 and !getRequest('period')) {
            LS::Make(ModuleViewer::class)->SetHtmlCanonical(Router::GetPath('deleted').$sShowType.'/');
        }
        /**
         * Получаем список топиков
         */
        $aResult = LS::Make(ModuleTopic::class)->GetDeletedTopicsCollective(
            $iPage,
            Config::Get('module.topic.per_page'),
            $sShowType,
            $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
        );
        $aTopics = $aResult['collection'];
        $aTopicsC = [];
        foreach ($aTopics as $oTopic) {
            /**
             * проверяем есть ли право на удаление топика
             */
            if ($this->oUserCurrent && LS::Make(ModuleACL::class)->IsAllowDeleteTopic($oTopic, $this->oUserCurrent)) {
                array_push($aTopicsC, $oTopic);
            }
        }
        $aTopics = $aTopicsC;
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('topics_list_show', ['aTopics' => $aTopics]);
        /**
         * Формируем постраничность
         */
        $aPaging = LS::Make(ModuleViewer::class)->MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'),
            Router::GetPath('deleted').$sShowType,
            in_array($sShowType, ['discussed', 'top']) ? ['period' => $sPeriod] : []
        );
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('blog_show', ['sShowType' => $sShowType]);
        /**
         * Загружаем переменные в шаблон
         */
        LS::Make(ModuleViewer::class)->Assign('aTopics', $aTopics);
        LS::Make(ModuleViewer::class)->Assign('aPaging', $aPaging);
        LS::Make(ModuleViewer::class)->Assign('bInTrash', true);
        if (in_array($sShowType, ['discussed', 'top'])) {
            LS::Make(ModuleViewer::class)->Assign('sPeriodSelectCurrent', $sPeriod);
            LS::Make(ModuleViewer::class)->Assign('sPeriodSelectRoot', Router::GetPath('deleted').$sShowType.'/');
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
        $this->sMenuSubItemSelect = $sShowType;
        /**
         * По какому полю сортировать
         */
        $sOrder = 'blog_rating';
        if (getRequest('order')) {
            $sOrder = getRequestStr('order');
        }
        /**
         * В каком направлении сортировать
         */
        $sOrderWay = 'desc';
        if (getRequest('order_way')) {
            $sOrderWay = getRequestStr('order_way');
        }
        /**
         * Фильтр поиска блогов
         */
        $aFilter = [
            'exclude_type' => 'personal',
            'deleted'      => 1
        ];
        /**
         * Передан ли номер страницы
         */
        $iPage = preg_match("/^\d+$/i", $this->GetEventMatch(2)) ? $this->GetEventMatch(2) : 1;
        /**
         * Получаем список блогов
         */
        $aResult = LS::Make(ModuleBlog::class)->GetBlogsByFilter(
            $aFilter,
            [$sOrder => $sOrderWay],
            $iPage,
            Config::Get('module.blog.per_page')
        );
        $aBlogs = $aResult['collection'];
        $aBlogsC = [];
        foreach ($aBlogs as $aBlog) {
            /**
             * проверяем есть ли право на удаление топика
             */
            if ($this->oUserCurrent && LS::Make(ModuleACL::class)->IsAllowDeleteBlog($aBlog, $this->oUserCurrent)) {
                array_push($aBlogsC, $aBlog);
            }
        }
        $aBlogs = $aBlogsC;
        /**
         * Формируем постраничность
         */
        $aPaging = LS::Make(ModuleViewer::class)->MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('module.blog.per_page'),
            Config::Get('pagination.pages.count'),
            Router::GetPath('blogs'),
            ['order' => $sOrder, 'order_way' => $sOrderWay]
        );
        /**
         * Загружаем переменные в шаблон
         */
        LS::Make(ModuleViewer::class)->Assign('aPaging', $aPaging);
        LS::Make(ModuleViewer::class)->Assign("aBlogs", $aBlogs);
        LS::Make(ModuleViewer::class)->Assign("sBlogOrder", htmlspecialchars($sOrder));
        LS::Make(ModuleViewer::class)->Assign("sBlogOrderWay", htmlspecialchars($sOrderWay));
        LS::Make(ModuleViewer::class)->Assign(
            "sBlogOrderWayNext",
            htmlspecialchars($sOrderWay == 'desc' ? 'asc' : 'desc')
        );
        /**
         * Устанавливаем title страницы
         */
        LS::Make(ModuleViewer::class)->AddHtmlTitle(LS::Make(ModuleLang::class)->Get('blog_menu_all_list'));
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
        LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
        LS::Make(ModuleViewer::class)->Assign('sMenuItemSelect', $this->sMenuItemSelect);
        LS::Make(ModuleViewer::class)->Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
        LS::Make(ModuleViewer::class)->Assign('sMenuSubBlogUrl', $this->sMenuSubBlogUrl);
        LS::Make(ModuleViewer::class)->Assign('iCountTopicsCollectiveNew', $this->iCountTopicsCollectiveNew);
        LS::Make(ModuleViewer::class)->Assign('iCountTopicsPersonalNew', $this->iCountTopicsPersonalNew);
        LS::Make(ModuleViewer::class)->Assign('iCountTopicsBlogNew', $this->iCountTopicsBlogNew);
        LS::Make(ModuleViewer::class)->Assign('iCountTopicsNew', $this->iCountTopicsNew);

        LS::Make(ModuleViewer::class)->Assign('BLOG_USER_ROLE_GUEST', ModuleBlog::BLOG_USER_ROLE_GUEST);
        LS::Make(ModuleViewer::class)->Assign('BLOG_USER_ROLE_USER', ModuleBlog::BLOG_USER_ROLE_USER);
        LS::Make(ModuleViewer::class)->Assign('BLOG_USER_ROLE_MODERATOR', ModuleBlog::BLOG_USER_ROLE_MODERATOR);
        LS::Make(ModuleViewer::class)->Assign('BLOG_USER_ROLE_ADMINISTRATOR', ModuleBlog::BLOG_USER_ROLE_ADMINISTRATOR);
        LS::Make(ModuleViewer::class)->Assign('BLOG_USER_ROLE_INVITE', ModuleBlog::BLOG_USER_ROLE_INVITE);
        LS::Make(ModuleViewer::class)->Assign('BLOG_USER_ROLE_REJECT', ModuleBlog::BLOG_USER_ROLE_REJECT);
        LS::Make(ModuleViewer::class)->Assign('BLOG_USER_ROLE_BAN', ModuleBlog::BLOG_USER_ROLE_BAN);
    }
}
