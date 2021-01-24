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
use Engine\Result\Action;
use Engine\Result\View\HtmlView;
use Engine\Result\View\Paging;
use Engine\Routing\Controller;

/**
 * Обработка главной страницы, т.е. УРЛа вида /index/
 *
 * @package actions
 * @since   1.0
 */
class ActionIndex extends Controller
{
    protected $defaults = [
        /**
         * Главное меню
         */
        'sMenuHeadItemSelect' => 'blog',
        /**
         * Меню
         */
        'sMenuItemSelect' => 'index',
        /**
         * Субменю
         */
        'sMenuSubItemSelect' => 'good',
        /**
         * Число новых топиков
         */
        'iCountTopicsNew' => 0,
        /**
         * Число новых топиков в коллективных блогах
         */
        'iCountTopicsCollectiveNew' => 0,
        /**
         * Число новых топиков в персональных блогах
         */
        'iCountTopicsPersonalNew' => 0
    ];

    /**
     * Инициализация
     *
     */
    public function boot()
    {
        /** @var ModuleTopic $topic */
        $topic = LS::Make(ModuleTopic::class);
        /**
         * Подсчитываем новые топики
         */
        $this->defaults['iCountTopicsCollectiveNew'] = $topic->GetCountTopicsCollectiveNew();
        $this->defaults['iCountTopicsPersonalNew'] = $topic->GetCountTopicsPersonalNew();
        $this->defaults['iCountTopicsNew'] = $this->defaults['iCountTopicsCollectiveNew'] + $this->defaults['iCountTopicsPersonalNew'];
    }

    /**
     * Вывод ВСЕХ новых топиков
     *
     * @param \App\Modules\ModuleTopic $topic
     * @param int                      $page
     *
     * @return \Engine\Result\View\View
     */
    protected function eventNewall(ModuleTopic $topic, int $page = 1)
    {
        /**
         * Меню
         */
        $this->defaults['sMenuSubItemSelect'] = 'newall';
        /**
         * Получаем список топиков
         */
        $aResult = $topic->GetTopicsNewAll($page, Config::Get('module.topic.per_page'));
        $aTopics = $aResult['collection'];

        /**
         * Формируем постраничность
         */
        $paging = Paging::make(
            $aResult['count'],
            $page,
            Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'),
            '/index/newall'
        );

        $view = HtmlView::by('index/index')->with($this->defaults)->with([
            'aTopics' => $aTopics,
            'aPaging' => $paging->toArray(),
            'sMenuHeadItemSelect' => 'newall'
        ])->paging($paging);
        $view->meta()->setHtmlRssAlternate('/rss/new/', Config::Get('view.name'));

        return $view;
    }

    /**
     * Вывод интересных на главную
     *
     * @param \Engine\Modules\ModuleViewer $viewer
     * @param \App\Modules\ModuleUser      $user
     *
     * @param int                          $page
     *
     * @return \Engine\Result\Action
     */
    protected function eventIndex(ModuleViewer $viewer, ModuleUser $user, int $page = 1): Action
    {
        $viewer->Assign('sMenuHeadItemSelect', 'blog');
        if ($user->getUserCurrent()) {
            return Action::by('feed#index')->with($this->defaults)->with(['page' => $page]);
        } else {
            return Action::by('index#newall')->with($this->defaults)->with(['page' => $page]);
        }
    }
}
