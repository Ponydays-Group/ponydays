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

use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Обработка главной страницы, т.е. УРЛа вида /index/
 *
 * @package actions
 * @since   1.0
 */
class ActionIndex extends Action
{
    /**
     * Главное меню
     *
     * @var string
     */
    protected $sMenuHeadItemSelect = 'blog';
    /**
     * Меню
     *
     * @var string
     */
    protected $sMenuItemSelect = 'index';
    /**
     * Субменю
     *
     * @var string
     */
    protected $sMenuSubItemSelect = 'good';
    /**
     * Число новых топиков
     *
     * @var int
     */
    protected $iCountTopicsNew = 0;
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
     * Инициализация
     *
     */
    public function Init()
    {
        /**
         * Подсчитываем новые топики
         */
        $this->iCountTopicsCollectiveNew = LS::Make(ModuleTopic::class)->GetCountTopicsCollectiveNew();
        $this->iCountTopicsPersonalNew = LS::Make(ModuleTopic::class)->GetCountTopicsPersonalNew();
        $this->iCountTopicsNew = $this->iCountTopicsCollectiveNew + $this->iCountTopicsPersonalNew;
    }

    /**
     * Регистрация евентов
     *
     */
    protected function RegisterEvent()
    {
        $this->AddEventPreg('/^(page([1-9]\d{0,5}))?$/i', 'EventIndex');
        $this->AddEventPreg('/^newall$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventNewAll');
    }


    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    /**
     * Вывод рейтинговых топиков
     */
    protected function EventTop()
    {
        $sPeriod = 1; // по дефолту 1 день
        if (in_array(getRequestStr('period'), [1, 7, 30, 'all'])) {
            $sPeriod = getRequestStr('period');
        }
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = 'top';

        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->GetParamEventMatch(0, 2) ? $this->GetParamEventMatch(0, 2) : 1;
        if ($iPage == 1 and !getRequest('period')) {
            $viewer->SetHtmlCanonical(Router::GetPath('index').'top/');
        }
        /**
         * Получаем список топиков
         */
        $aResult = LS::Make(ModuleTopic::class)->GetTopicsTop(
            $iPage,
            Config::Get('module.topic.per_page'),
            $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
        );
        /**
         * Если нет топиков за 1 день, то показываем за неделю (7)
         */
        if (!$aResult['count'] and $iPage == 1 and !getRequest('period')) {
            $sPeriod = 7;
            $aResult = LS::Make(ModuleTopic::class)->GetTopicsTop(
                $iPage,
                Config::Get('module.topic.per_page'),
                $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
            );
        }
        $aTopics = $aResult['collection'];
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('topics_list_show', ['aTopics' => $aTopics]);
        /**
         * Формируем постраничность
         */
        $aPaging = $viewer->MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'),
            Router::GetPath('index').'top',
            ['period' => $sPeriod]
        );
        /**
         * Загружаем переменные в шаблон
         */
        $viewer->Assign('aTopics', $aTopics);
        $viewer->Assign('aPaging', $aPaging);
        $viewer->Assign('sPeriodSelectCurrent', $sPeriod);
        $viewer->Assign('sPeriodSelectRoot', Router::GetPath('index').'top/');
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('index');
    }

    /**
     * Вывод обсуждаемых топиков
     */
    protected function EventDiscussed()
    {
        $sPeriod = 1; // по дефолту 1 день
        if (in_array(getRequestStr('period'), [1, 7, 30, 'all'])) {
            $sPeriod = getRequestStr('period');
        }
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = 'discussed';

        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->GetParamEventMatch(0, 2) ? $this->GetParamEventMatch(0, 2) : 1;
        if ($iPage == 1 and !getRequest('period')) {
            $viewer->SetHtmlCanonical(Router::GetPath('index').'discussed/');
        }
        /**
         * Получаем список топиков
         */
        $aResult = LS::Make(ModuleTopic::class)->GetTopicsDiscussed(
            $iPage,
            Config::Get('module.topic.per_page'),
            $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
        );
        /**
         * Если нет топиков за 1 день, то показываем за неделю (7)
         */
        if (!$aResult['count'] and $iPage == 1 and !getRequest('period')) {
            $sPeriod = 7;
            $aResult = LS::Make(ModuleTopic::class)->GetTopicsDiscussed(
                $iPage,
                Config::Get('module.topic.per_page'),
                $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
            );
        }
        $aTopics = $aResult['collection'];
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('topics_list_show', ['aTopics' => $aTopics]);
        /**
         * Формируем постраничность
         */
        $aPaging = $viewer->MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'),
            Router::GetPath('index').'discussed',
            ['period' => $sPeriod]
        );
        /**
         * Загружаем переменные в шаблон
         */
        $viewer->Assign('aTopics', $aTopics);
        $viewer->Assign('aPaging', $aPaging);
        $viewer->Assign('sPeriodSelectCurrent', $sPeriod);
        $viewer->Assign('sPeriodSelectRoot', Router::GetPath('index').'discussed/');
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('index');
    }

    /**
     * Вывод новых топиков
     */
    protected function EventNew()
    {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        $viewer->SetHtmlRssAlternate(Router::GetPath('rss').'new/', Config::Get('view.name'));
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = 'new';
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->GetParamEventMatch(0, 2) ? $this->GetParamEventMatch(0, 2) : 1;
        /**
         * Получаем список топиков
         */
        $aResult = LS::Make(ModuleTopic::class)->GetTopicsNew($iPage, Config::Get('module.topic.per_page'));
        $aTopics = $aResult['collection'];
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('topics_list_show', ['aTopics' => $aTopics]);
        /**
         * Формируем постраничность
         */
        $aPaging = $viewer->MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'),
            Router::GetPath('index').'new'
        );
        /**
         * Загружаем переменные в шаблон
         */
        $viewer->Assign('aTopics', $aTopics);
        $viewer->Assign('aPaging', $aPaging);
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('index');
    }

    /**
     * Вывод ВСЕХ новых топиков
     */
    protected function EventNewAll()
    {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        $viewer->SetHtmlRssAlternate(Router::GetPath('rss').'new/', Config::Get('view.name'));
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = 'newall';
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->GetParamEventMatch(0, 2) ? $this->GetParamEventMatch(0, 2) : 1;
        /**
         * Получаем список топиков
         */
        $aResult = LS::Make(ModuleTopic::class)->GetTopicsNewAll($iPage, Config::Get('module.topic.per_page'));
        $aTopics = $aResult['collection'];
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('topics_list_show', ['aTopics' => $aTopics]);
        /**
         * Формируем постраничность
         */
        $aPaging = $viewer->MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'),
            Router::GetPath('index').'newall'
        );
        /**
         * Загружаем переменные в шаблон
         */
        $viewer->Assign('aTopics', $aTopics);
        $viewer->Assign('aPaging', $aPaging);
        /**
         * Устанавливаем шаблон вывода
         */
        $viewer->Assign('sMenuHeadItemSelect', 'newall');
        $this->SetTemplateAction('index');
    }

    /**
     * Вывод интересных на главную
     *
     * @param \Engine\Modules\ModuleViewer $viewer
     * @param \App\Modules\ModuleUser      $user
     *
     * @return void
     */
    protected function EventIndex(ModuleViewer $viewer, ModuleUser $user)
    {
        $viewer->Assign('sMenuHeadItemSelect', 'blog');
        if ($user->getUserCurrent()) {
            Router::Action('feed');
        } else {
            Router::Action('index', 'newall');
        }
    }

    /**
     * При завершении экшена загружаем переменные в шаблон
     *
     */
    public function EventShutdown()
    {
        /** @var ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        $viewer->Assign('sMenuItemSelect', $this->sMenuItemSelect);
        $viewer->Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
        $viewer->Assign('iCountTopicsNew', $this->iCountTopicsNew);
        $viewer->Assign('iCountTopicsCollectiveNew', $this->iCountTopicsCollectiveNew);
        $viewer->Assign('iCountTopicsPersonalNew', $this->iCountTopicsPersonalNew);
    }
}

