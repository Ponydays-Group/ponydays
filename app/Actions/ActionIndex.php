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
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleViewer;
use Engine\Routing\Action;
use Engine\Routing\Controller;
use Engine\View\View;

/**
 * Обработка главной страницы, т.е. УРЛа вида /index/
 *
 * @package actions
 * @since   1.0
 */
class ActionIndex extends Controller
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
    public function init()
    {
        /**
         * Подсчитываем новые топики
         */
        $this->iCountTopicsCollectiveNew = LS::Make(ModuleTopic::class)->GetCountTopicsCollectiveNew();
        $this->iCountTopicsPersonalNew = LS::Make(ModuleTopic::class)->GetCountTopicsPersonalNew();
        $this->iCountTopicsNew = $this->iCountTopicsCollectiveNew + $this->iCountTopicsPersonalNew;
    }

    /**
     * Вывод ВСЕХ новых топиков
     *
     * @param \Engine\Modules\ModuleViewer $viewer
     * @param \App\Modules\ModuleTopic     $topic
     * @param int                          $page
     *
     * @return \Engine\View\View
     */
    protected function newall(ModuleViewer $viewer, ModuleTopic $topic, int $page = 1)
    {
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = 'newall';
        /**
         * Получаем список топиков
         */
        $aResult = $topic->GetTopicsNewAll($page, Config::Get('module.topic.per_page'));
        $aTopics = $aResult['collection'];

        /**
         * Формируем постраничность
         */
        $aPaging = $viewer->MakePaging(
            $aResult['count'],
            $page,
            Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'),
            '/index/newall'
        );

        $viewer->SetHtmlRssAlternate('/rss/new/', Config::Get('view.name'));

        return View::by('index/index')->with([
            'aTopics' => $aTopics,
            'aPaging' => $aPaging,
            'sMenuHeadItemSelect' => 'newall'
        ]);
    }

    /**
     * Вывод интересных на главную
     *
     * @param \Engine\Modules\ModuleViewer $viewer
     * @param \App\Modules\ModuleUser      $user
     *
     * @param int                          $page
     *
     * @return \Engine\Routing\Action
     */
    protected function index(ModuleViewer $viewer, ModuleUser $user, int $page = 1): Action
    {
        $viewer->Assign('sMenuHeadItemSelect', 'blog');
        if ($user->getUserCurrent()) {
            return Action::by('feed#index')->with(['page' => $page]);
        } else {
            return Action::by('index#newall')->with(['page' => $page]);
        }
    }

    /**
     * При завершении экшена загружаем переменные в шаблон
     *
     */
    public function shutdown()
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
