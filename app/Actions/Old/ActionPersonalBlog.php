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
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки персональных блогов, т.е. УРла вида /personal_blog/
 *
 * @package actions
 * @since   1.0
 */
class ActionPersonalBlog extends Action
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
    protected $sMenuItemSelect = 'log';
    /**
     * Субменю
     *
     * @var string
     */
    protected $sMenuSubItemSelect = 'good';

    /**
     * Инициализация
     *
     */
    public function Init()
    {
        $this->SetDefaultEvent('good');
    }

    /**
     * Регистрируем необходимые евенты
     *
     */
    protected function RegisterEvent()
    {
        $this->AddEventPreg('/^good$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventTopics');
        $this->AddEvent('good', 'EventTopics');
        $this->AddEventPreg('/^bad$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventTopics');
        $this->AddEventPreg('/^new$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventTopics');
        $this->AddEventPreg('/^newall$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventTopics');
        $this->AddEventPreg('/^discussed$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventTopics');
        $this->AddEventPreg('/^top$/i', '/^(page([1-9]\d{0,5}))?$/i', 'EventTopics');
    }


    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    /**
     * Показ топиков
     *
     */
    protected function EventTopics()
    {
        $sPeriod = 1; // по дефолту 1 день
        if (in_array(getRequestStr('period'), [1, 7, 30, 'all'])) {
            $sPeriod = getRequestStr('period');
        }
        $sShowType = 'newall';
        if (!in_array($sShowType, ['discussed', 'top'])) {
            $sPeriod = 'all';
        }
        /**
         * Меню
         */
        $this->sMenuSubItemSelect = $sShowType == 'newall' ? 'new' : $sShowType;
        /**
         * Передан ли номер страницы
         */
        $iPage = $this->GetParamEventMatch(0, 2) ? $this->GetParamEventMatch(0, 2) : 1;
        if ($iPage == 1 and !getRequest('period')) {
            LS::Make(ModuleViewer::class)->SetHtmlCanonical(Router::GetPath('personal_blog').$sShowType.'/');
        }
        /**
         * Получаем список топиков
         */
        $aResult = LS::Make(ModuleTopic::class)->GetTopicsPersonal(
            $iPage,
            Config::Get('module.topic.per_page'),
            $sShowType,
            $sPeriod == 'all' ? null : $sPeriod * 60 * 60 * 24
        );
        /**
         * Если нет топиков за 1 день, то показываем за неделю (7)
         */
        if (in_array($sShowType, ['discussed', 'top']) and !$aResult['count'] and $iPage == 1 and !getRequest(
                'period'
            )
        ) {
            $sPeriod = 7;
            $aResult = LS::Make(ModuleTopic::class)->GetTopicsPersonal(
                $iPage,
                Config::Get('module.topic.per_page'),
                $sShowType,
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
        $aPaging = LS::Make(ModuleViewer::class)->MakePaging(
            $aResult['count'],
            $iPage,
            Config::Get('module.topic.per_page'),
            Config::Get('pagination.pages.count'),
            Router::GetPath('personal_blog').$sShowType,
            in_array($sShowType, ['discussed', 'top']) ? ['period' => $sPeriod] : []
        );
        /**
         * Вызов хуков
         */
        LS::Make(ModuleHook::class)->Run('personal_show', ['sShowType' => $sShowType]);
        /**
         * Загружаем переменные в шаблон
         */
        LS::Make(ModuleViewer::class)->Assign('aTopics', $aTopics);
        LS::Make(ModuleViewer::class)->Assign('aPaging', $aPaging);
        if (in_array($sShowType, ['discussed', 'top'])) {
            LS::Make(ModuleViewer::class)->Assign('sPeriodSelectCurrent', $sPeriod);
            LS::Make(ModuleViewer::class)->Assign('sPeriodSelectRoot', Router::GetPath('personal_blog').$sShowType.'/');
        }
        /**
         * Устанавливаем шаблон вывода
         */
        $this->SetTemplateAction('index');
    }

    /**
     * При завершении экшена загружаем в шаблон необходимые переменные
     *
     */
    public function EventShutdown()
    {
        /**
         * Подсчитываем новые топики
         */
        $iCountTopicsCollectiveNew = LS::Make(ModuleTopic::class)->GetCountTopicsCollectiveNew();
        $iCountTopicsPersonalNew = LS::Make(ModuleTopic::class)->GetCountTopicsPersonalNew();
        $iCountTopicsNew = $iCountTopicsCollectiveNew + $iCountTopicsPersonalNew;
        /**
         * Загружаем переменные в шаблон
         */
        LS::Make(ModuleViewer::class)->Assign('sMenuHeadItemSelect', $this->sMenuHeadItemSelect);
        LS::Make(ModuleViewer::class)->Assign('sMenuItemSelect', $this->sMenuItemSelect);
        LS::Make(ModuleViewer::class)->Assign('sMenuSubItemSelect', $this->sMenuSubItemSelect);
        LS::Make(ModuleViewer::class)->Assign('iCountTopicsCollectiveNew', $iCountTopicsCollectiveNew);
        LS::Make(ModuleViewer::class)->Assign('iCountTopicsPersonalNew', $iCountTopicsPersonalNew);
        LS::Make(ModuleViewer::class)->Assign('iCountTopicsNew', $iCountTopicsNew);
    }
}
