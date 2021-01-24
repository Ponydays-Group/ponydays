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

use App\Entities\EntityUser;
use App\Modules\ModuleBlog;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use App\Modules\ModuleUserfeed;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleViewer;
use Engine\Result\View\AjaxView;
use Engine\Result\View\HtmlView;
use Engine\Result\View\JsonView;
use Engine\Routing\Controller;
use Engine\Routing\Controller\DefaultVariablesProvider;
use Engine\Routing\Controller\IResultPostprocessor;
use Engine\Routing\Exception\Http\NotFoundHttpException;

/**
 * Обрабатывает пользовательские ленты контента
 *
 * @package actions
 * @since   1.0
 */
class ActionUserfeed extends Controller implements IResultPostprocessor
{
    use DefaultVariablesProvider;
    /**
     * Текущий пользователь
     *
     * @var EntityUser|null
     */
    protected $oUserCurrent;

    protected $templateDefaults = [
        /**
         * Главное меню
         */
        'sMenuItemSelect' => 'feed'
    ];

    /**
     * Инициализация
     *
     */
    public function boot()
    {
        /**
         * Доступ только у авторизованных пользователей
         */
        $this->oUserCurrent = LS::Make(ModuleUser::class)->getUserCurrent();
        if (!$this->oUserCurrent) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Выводит ленту контента(топики) для пользователя
     *
     * @param \App\Modules\ModuleUserfeed $feed
     * @param \Engine\Modules\ModuleHook  $hook
     *
     * @return \Engine\Result\View\HtmlView
     */
    protected function eventIndex(ModuleUserfeed $feed, ModuleHook $hook)
    {
        /**
         * Получаем топики
         */
        $aTopics = $feed->read($this->oUserCurrent->getId());
        /**
         * Вызов хуков
         */
        $hook->Run('topics_list_show', ['aTopics' => $aTopics]);

        $view = HtmlView::by('userfeed/list');

        $view->with(['aTopics' => $aTopics]);
        if (count($aTopics)) {
            $view->with(['iUserfeedLastId' => end($aTopics)->getId()]);
        }
        if (count($aTopics) < Config::Get('module.userfeed.count_default')) {
            $view->with(['bDisableGetMoreButton' => true]);
        } else {
            $view->with(['bDisableGetMoreButton' => false]);
        }

        return $view;
    }

    /**
     * Подгрузка ленты топиков (замена постраничности)
     *
     * @param \App\Modules\ModuleUserfeed   $feed
     * @param \Engine\Modules\ModuleViewer  $viewer
     * @param \Engine\Modules\ModuleMessage $message
     * @param \Engine\Modules\ModuleLang    $lang
     * @param \Engine\Modules\ModuleHook    $hook
     *
     * @return \Engine\Result\View\JsonView
     */
    protected function eventGetMore(ModuleUserfeed $feed, ModuleViewer $viewer, ModuleMessage $message, ModuleLang $lang, ModuleHook $hook): JsonView
    {
        /**
         * Проверяем последний просмотренный ID топика
         */
        $iFromId = getRequestStr('last_id');
        if (!$iFromId) {
            $message->AddError(
                $lang->Get('system_error'),
                $lang->Get('error')
            );

            return AjaxView::empty();
        }
        /**
         * Получаем топики
         */
        $aTopics = $feed->read($this->oUserCurrent->getId(), null, $iFromId);
        /**
         * Вызов хуков
         */
        $hook->Run('topics_list_show', ['aTopics' => $aTopics]);
        /**
         * Загружаем данные в ajax ответ
         */
        $topicListView = HtmlView::global('topic_list')->with(['aTopics' => $aTopics]);

        $view = AjaxView::from([
            'result' => $topicListView->fetch(),
            'topics_count' => count($aTopics)
        ]);

        if (count($aTopics)) {
            $view->with(['iUserfeedLastId' => end($aTopics)->getId()]);
        }

        return $view;
    }

    /**
     * Подписка на контент блога или пользователя
     *
     * @param \App\Modules\ModuleBlog       $blog
     * @param \App\Modules\ModuleUser       $user
     * @param \App\Modules\ModuleUserfeed   $feed
     * @param \Engine\Modules\ModuleMessage $message
     * @param \Engine\Modules\ModuleLang    $lang
     *
     * @return \Engine\Result\View\JsonView
     */
    protected function eventSubscribe(ModuleBlog $blog, ModuleUser $user, ModuleUserfeed $feed, ModuleMessage $message, ModuleLang $lang): JsonView
    {
        /**
         * Проверяем наличие ID блога или пользователя
         */
        if (!getRequest('id')) {
            $message->AddError($lang->Get('system_error'), $lang->Get('error'));
        }
        $sType = getRequestStr('type');
        $iType = null;
        /**
         * Определяем тип подписки
         */
        switch ($sType) {
            case 'blogs':
                $iType = ModuleUserfeed::SUBSCRIBE_TYPE_BLOG;
                /**
                 * Проверяем существование блога
                 */
                if (!$blog->GetBlogById(getRequestStr('id'))) {
                    $message->AddError($lang->Get('system_error'), $lang->Get('error'));

                    return AjaxView::empty();
                }
                if (
                    !in_array($blog->GetBlogById(getRequestStr('id'))->getId(), $blog->GetAccessibleBlogsByUser($user->GetUserCurrent()))
                    and in_array($blog->GetBlogById(getRequestStr('id'))->getType(), ["close", "invite"])
                ) {
                    $message->AddNotice(// TODO: Localization
                        "У вас нет разрешения подписываться на этот блог",
                        "Ошибка"
                    );

                    return AjaxView::empty();
                }
                break;
            default:
                $message->AddError($lang->Get('system_error'), $lang->Get('error'));

                return AjaxView::empty();
        }
        /**
         * Подписываем
         */
        $feed->subscribeUser($this->oUserCurrent->getId(), $iType, getRequestStr('id'));
        $message->AddNotice($lang->Get('userfeed_subscribes_updated'), // TODO: Localization
            "Внимание"
        );

        return AjaxView::empty();
    }

    /**
     * Подписка на пользвователя по логину
     *
     */
    protected function eventSubscribeByLogin(ModuleUser $user, ModuleUserfeed $feed, ModuleMessage $message, ModuleLang $lang): JsonView
    {
        /**
         * Передан ли логин
         */
        if (!getRequest('login') or !is_string(getRequest('login'))) {
            $message->AddError($lang->Get('system_error'), $lang->Get('error'));

            return AjaxView::empty();
        }
        /**
         * Проверяем существование прользователя
         */
        $oUser = $user->getUserByLogin(getRequestStr('login'));
        if (!$oUser) {
            $message->AddError(
                $lang->Get('user_not_found', ['login' => htmlspecialchars(getRequestStr('login'))]), $lang->Get('error')
            );

            return AjaxView::empty();
        }
        /**
         * Не даем подписаться на самого себя
         */
        /**
         * Подписываем
         */
        $feed->subscribeUser($this->oUserCurrent->getId(), ModuleUserfeed::SUBSCRIBE_TYPE_USER, $oUser->getId());
        /**
         * Загружаем данные ajax ответ
         */
        $view = AjaxView::from([
            'uid' => $oUser->getId(),
            'user_login' => $oUser->getLogin(),
            'user_web_path' => $oUser->getUserWebPath(),
            'user_avatar_48' => $oUser->getProfileAvatarPath(48),
            'lang_error_msg' => $lang->Get('userfeed_subscribes_already_subscribed'),
            'lang_error_title' => $lang->Get('error')
        ]);
        $message->AddNotice($lang->Get('userfeed_subscribes_updated'), $lang->Get('attention'));

        return $view;
    }

    /**
     * Отписка от блога или пользователя
     *
     * @param \App\Modules\ModuleUserfeed   $feed
     * @param \Engine\Modules\ModuleMessage $message
     * @param \Engine\Modules\ModuleLang    $lang
     *
     * @return \Engine\Result\View\JsonView
     */
    protected function eventUnsubscribe(ModuleUserfeed $feed, ModuleMessage $message, ModuleLang $lang): JsonView
    {
        if (!getRequest('id')) {
            $message->AddError($lang->Get('system_error'), $lang->Get('error'));

            return AjaxView::empty();
        }
        $sType = getRequestStr('type');
        $iType = null;
        /**
         * Определяем от чего отписываемся
         */
        switch ($sType) {
            case 'blogs':
                $iType = ModuleUserfeed::SUBSCRIBE_TYPE_BLOG;
                break;
            case 'users':
                $iType = ModuleUserfeed::SUBSCRIBE_TYPE_USER;
                break;
            default:
                $message->AddError($lang->Get('system_error'), $lang->Get('error'));

                return AjaxView::empty();
        }
        /**
         * Отписываем пользователя
         */
        $feed->unsubscribeUser($this->oUserCurrent->getId(), $iType, getRequestStr('id'));
        $message->AddNotice($lang->Get('userfeed_subscribes_updated'), $lang->Get('attention'));

        return AjaxView::empty();
    }

    /**
     * При завершении экшена загружаем в шаблон необходимые переменные
     *
     * @param \App\Modules\ModuleBlog       $blog
     * @param \App\Modules\ModuleUserfeed   $feed
     * @param \Engine\Modules\ModuleMessage $message
     * @param \Engine\Modules\ModuleLang    $lang
     *
     * @return \Engine\Result\View\JsonView
     */
    protected function eventSubscribeAll(ModuleBlog $blog, ModuleUserfeed $feed, ModuleMessage $message, ModuleLang $lang): JsonView
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
//		$aBlogs = LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($this->oUserCurrent);
        $aBlogs = $blog->GetBlogs();

        foreach ($aBlogs as $iBlogId) {
            $feed->subscribeUser(
                $this->oUserCurrent->getId(),
                ModuleUserfeed::SUBSCRIBE_TYPE_BLOG,
                $iBlogId->getId()
            );
        }
        $message->AddNotice($lang->Get('userfeed_subscribes_updated'), // TODO: Localization
            "Внимание"
        );

        return AjaxView::empty();
    }

    /**
     * @param \App\Modules\ModuleBlog       $blog
     * @param \App\Modules\ModuleUserfeed   $feed
     * @param \Engine\Modules\ModuleMessage $message
     * @param \Engine\Modules\ModuleLang    $lang
     *
     * @return \Engine\Result\View\JsonView
     */
    protected function eventUnsubscribeAll(ModuleBlog $blog, ModuleUserfeed $feed, ModuleMessage $message, ModuleLang $lang): JsonView
    {
        $aBlogs = $blog->GetBlogs();
        foreach ($aBlogs as $iBlogId) {
            $feed->unsubscribeUser(
                $this->oUserCurrent->getId(),
                ModuleUserfeed::SUBSCRIBE_TYPE_BLOG,
                $iBlogId->getId()
            );
        }
        $message->AddNotice($lang->Get('userfeed_subscribes_updated'), // TODO: Localization
            "Внимание"
        );

        return AjaxView::empty();
    }

    public function shutdown()
    {
        /** @var ModuleTopic $topic */
        $topic = LS::Make(ModuleTopic::class);
        /**
         * Подсчитываем новые топики
         */
        $iCountTopicsCollectiveNew = $topic->GetCountTopicsCollectiveNew();
        $iCountTopicsPersonalNew = $topic->GetCountTopicsPersonalNew();
        $iCountTopicsNew = $iCountTopicsCollectiveNew + $iCountTopicsPersonalNew;
        /**
         * Загружаем переменные в шаблон
         */
        /** @var \Engine\Modules\ModuleViewer $viewer */
        $viewer = LS::Make(ModuleViewer::class);

        $viewer->Assign('iCountTopicsCollectiveNew', $iCountTopicsCollectiveNew);
        $viewer->Assign('iCountTopicsPersonalNew', $iCountTopicsPersonalNew);
        $viewer->Assign('iCountTopicsNew', $iCountTopicsNew);
    }
}
