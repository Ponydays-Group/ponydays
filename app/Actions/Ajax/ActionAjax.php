<?php

namespace App\Actions\Ajax;

use App\Modules\ModuleACL;
use App\Modules\ModuleBlog;
use App\Modules\ModuleTalk;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Result\View\AjaxView;
use Engine\Result\View\HtmlView;
use Engine\Routing\Controller;

class ActionAjax extends Controller
{
    /**
     * @var \App\Entities\EntityUser
     */
    protected $currentUser = null;

    public function boot()
    {
        /** @var ModuleUser $user */
        $user = LS::Make(ModuleUser::class);
        $this->currentUser = $user->GetUserCurrent();
    }

    /**
     * Вывод информации о блоге
     *
     * @param \App\Modules\ModuleBlog    $blog
     * @param \App\Modules\ModuleTopic   $topic
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventInfoboxInfoBlog(ModuleBlog $blog, ModuleTopic $topic, ModuleLang $lang): AjaxView
    {
        /**
         * Если блог существует и он не персональный
         */
        if (!is_string(getRequest('iBlogId'))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        if (!($oBlog = $blog->GetBlogById(getRequest('iBlogId'))) or $oBlog->getType() == 'personal') {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Получаем локальный вьюер для рендеринга шаблона
         */
        $renderer = HtmlView::global('infobox.info.blog.tpl')->with(['oBlog' => $oBlog]);
        if ($oBlog->getType() != 'close' or $oBlog->getUserIsJoin()) {
            /**
             * Получаем последний топик
             */
            $aResult = $topic->GetTopicsByFilter([
                'blog_id' => $oBlog->getId(),
                'topic_publish' => 1
            ], 1, 1);
            $renderer->with(['oTopicLast' => reset($aResult['collection'])]);
        }

        $renderer->with(['oUserCurrent' => $this->currentUser]);

        return AjaxView::from(['sText' => $renderer->fetch()]);
    }

    protected function eventInviteUser(ModuleTalk $talk, ModuleBlog $blog, ModuleUser $user): AjaxView
    {
        $a = $_POST["to"];
        $oUserCurrent = $user->GetUserCurrent();
        $oBlog = $blog->GetBlogById($_POST["blog"]);
        $talk->SendTalk(
            "Просьба об инвайте",
            "Пользователь <a href='"."/profile/".$oUserCurrent->getLogin()."/' class='user'>"
            ."<i class='icon-user'></i>".$oUserCurrent->getLogin()."</a> просит пригласить его в блог <a href='"
            .$oBlog->getUrlFull()."'>".$oBlog->getTitle()."</a>.",
            $oUserCurrent->getId(),
            $a
        );

        return AjaxView::empty();
    }

    protected function eventTopicLockControl(ModuleACL $acl, ModuleTopic $topic, ModuleLang $lang): AjaxView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Топик существует?
         */
        if (!($oTopic = $topic->GetTopicById(getRequestStr('idTopic', null, 'post')))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        $isAllowLockControlTopic = $acl->IsAllowLockTopicControl($oTopic, $this->currentUser);
        if (!$isAllowLockControlTopic) {
            return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
        }

        $bLockState = getRequestStr('bState', null, 'post') == '1';
        $bStateOld = $oTopic->isControlLocked();
        $oTopic->setLockControl($bLockState);
        if ($bStateOld == $bLockState || $topic->UpdateControlLock($oTopic)) {
            $sNotice = $bLockState ? 'topic_control_locked' : 'topic_control_unlocked';

            return AjaxView::from(['bState' => $oTopic->isControlLocked()])->msgNotice($lang->Get($sNotice), $lang->Get('attention'));
        } else {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }
    }
}
