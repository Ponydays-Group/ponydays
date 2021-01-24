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
use Engine\Config;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleLang;
use Engine\Result\View\HtmlView;
use Engine\Result\View\Paging;
use Engine\Routing\Controller;
use Engine\Routing\Controller\DefaultVariablesProvider;
use Engine\Routing\Controller\IResultPostprocessor;

/**
 * Экшен обработки поиска по тегам
 *
 * @package actions
 * @since   1.0
 */
class ActionTag extends Controller implements IResultPostprocessor
{
    use DefaultVariablesProvider;

    protected $templateDefaults = [
        /**
         * Главное меню
         */
        'sMenuHeadItemSelect' => 'blog'
    ];

    /**
     * Отображение топиков
     *
     * @param \App\Modules\ModuleTopic   $topics
     * @param \Engine\Modules\ModuleHook $hooks
     * @param \Engine\Modules\ModuleLang $lang
     * @param string                     $tag
     * @param int                        $page
     *
     * @return \Engine\Result\View\HtmlView
     */
    protected function eventTags(ModuleTopic $topics, ModuleHook $hooks, ModuleLang $lang, string $tag, int $page = 1)
    {
        /**
         * Получаем список топиков
         */
        $aResult = $topics->GetTopicsByTag($tag, $page, Config::Get('module.topic.per_page'));
        $aTopics = $aResult['collection'];
        /**
         * Вызов хуков
         */
        $hooks->Run('topics_list_show', ['aTopics' => $aTopics]);
        /**
         * Формируем постраничность
         */
        $paging = Paging::make($aResult['count'],
            $page,
            Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'),
            '/tag/'.htmlspecialchars($tag)
        );

        $view = HtmlView::by('tag/index')->paging($paging)->with([
            'aPaging' => $paging->toArray(),
            'aTopics' => $aTopics,
            'sTag' => $tag
        ])->withHtmlTitle([$lang->Get('tag_title'), $tag]);

        $view->meta()->setHtmlRssAlternate('/rss/tag/'.$tag.'/', $tag);

        return $view;
    }
}
