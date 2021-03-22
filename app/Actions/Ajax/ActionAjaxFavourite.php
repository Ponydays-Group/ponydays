<?php

namespace App\Actions\Ajax;

use App\Entities\EntityFavourite;
use App\Modules\ModuleComment;
use App\Modules\ModuleFavourite;
use App\Modules\ModuleTalk;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Result\View\AjaxView;
use Engine\Routing\Controller;

class ActionAjaxFavourite extends Controller
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
     * Сохраняет теги для избранного
     *
     * @param \App\Modules\ModuleFavourite $favourite
     * @param \Engine\Modules\ModuleLang   $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventFavouriteSaveTags(ModuleFavourite $favourite, ModuleLang $lang): AjaxView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Объект уже должен быть в избранном
         */
        if ($oFavourite = $favourite->GetFavourite(getRequestStr('target_id'), getRequestStr('target_type'), $this->currentUser->getId())) {
            /**
             * Обрабатываем теги
             */
            $aTags = explode(',', trim(getRequestStr('tags'), "\r\n\t\0\x0B ."));
            $aTagsNew = [];
            $aTagsNewLow = [];
            $aTagsReturn = [];
            foreach ($aTags as $sTag) {
                $sTag = trim($sTag);
                if (func_check($sTag, 'text', 2, 50) and !in_array(mb_strtolower($sTag, 'UTF-8'), $aTagsNewLow)) {
                    $sTagEsc = htmlspecialchars($sTag);
                    $aTagsNew[] = $sTagEsc;
                    $aTagsReturn[] = [
                        'tag' => $sTagEsc,
                        'url' => $this->currentUser->getUserWebPath().'favourites/'.$oFavourite->getTargetType()
                            .'s/tag/'.$sTagEsc.'/', // костыль для URL с множественным числом
                    ];
                    $aTagsNewLow[] = mb_strtolower($sTag, 'UTF-8');
                }
            }

            if (!count($aTagsNew)) {
                $oFavourite->setTags('');
            } else {
                $oFavourite->setTags(join(',', $aTagsNew));
            }

            $favourite->UpdateFavourite($oFavourite);

            return AjaxView::from(['aTags' => $aTagsReturn]);
        }

        return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
    }

    /**
     * Обработка избранного - топик
     *
     * @param \App\Modules\ModuleTopic   $topic
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventFavouriteTopic(ModuleTopic $topic, ModuleLang $lang): AjaxView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Можно только добавить или удалить из избранного
         */
        $iType = getRequestStr('type', null, 'post');
        if (!in_array($iType, ['1', '0'])) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Топик существует?
         */
        if (!($oTopic = $topic->GetTopicById(getRequestStr('idTopic', null, 'post')))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Пропускаем топик из черновиков
         */
        if (!$oTopic->getPublish()) {
            return AjaxView::empty()->msgError($lang->Get('error_favorite_topic_is_draft'), $lang->Get('error'), true);
        }

        /**
         * Топик уже в избранном?
         */
        $oFavouriteTopic = $topic->GetFavouriteTopic($oTopic->getId(), $this->currentUser->getId());

        /**
         * Топик не в избранном и пользователь хочет добавить его в избранное
         */
        if (!$oFavouriteTopic and $iType) {
            $oFavouriteTopicNew = new EntityFavourite([
                'target_id'      => $oTopic->getId(),
                'user_id'        => $this->currentUser->getId(),
                'target_type'    => 'topic',
                'target_publish' => $oTopic->getPublish()
            ]);
            $oTopic->setCountFavourite($oTopic->getCountFavourite() + 1);
            if ($topic->AddFavouriteTopic($oFavouriteTopicNew) and $topic->UpdateTopic($oTopic)) {
                return AjaxView::from([
                    'bState' => true,
                    'iCount' => $oTopic->getCountFavourite()
                ])->msgNotice($lang->Get('topic_favourite_add_ok'), $lang->Get('attention'), true);
            } else {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
        }

        /**
         * Топик не в избранном и пользователь хочет удалить его из избранного - ошибка
         */
        if (!$oFavouriteTopic and !$iType) {
            return AjaxView::empty()->msgError($lang->Get('topic_favourite_add_no'), $lang->Get('error'), true);
        }

        /**
         * Топик в избранном и пользователь хочет добавить его в избранное - ошибка
         */
        if ($oFavouriteTopic and $iType) {
            return AjaxView::empty()->msgError($lang->Get('topic_favourite_add_already'), $lang->Get('error'), true);
        }

        /**
         * Топик в избранном и пользователь хочет удалить его из избранного
         */
        if ($oFavouriteTopic and !$iType) {
            $oTopic->setCountFavourite($oTopic->getCountFavourite() - 1);
            if ($topic->DeleteFavouriteTopic($oFavouriteTopic) and $topic->UpdateTopic($oTopic)) {
                return AjaxView::from([
                    'bState' => false,
                    'iCount' => $oTopic->getCountFavourite()
                ])->msgNotice($lang->Get('topic_favourite_del_ok'), $lang->Get('attention'), true);
            } else {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
        }

        return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
    }

    /**
     * Обработка избранного - комментарий
     *
     * @param \App\Modules\ModuleComment $comment
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventFavouriteComment(ModuleComment $comment, ModuleLang $lang): AjaxView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Можно только добавить или удалить из избранного
         */
        $iType = getRequestStr('type', null, 'post');
        if (!in_array($iType, ['1', '0'])) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Комментарий существует?
         */
        if (!($oComment = $comment->GetCommentById(getRequestStr('idComment', null, 'post')))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Запрет на добавление удаленного комментария
         */
        if ($iType === '1' and $oComment->getDelete()) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Комментарий уже в избранном?
         */
        $oFavouriteComment = $comment->GetFavouriteComment($oComment->getId(), $this->currentUser->getId());
        if (!$oFavouriteComment and $iType) {
            $oFavouriteCommentNew = new EntityFavourite([
                'target_id'      => $oComment->getId(),
                'target_type'    => 'comment',
                'user_id'        => $this->currentUser->getId(),
                'target_publish' => $oComment->getPublish()
            ]);
            $oComment->setCountFavourite($oComment->getCountFavourite() + 1);
            if ($comment->AddFavouriteComment($oFavouriteCommentNew) and $comment->UpdateComment($oComment)) {
                return AjaxView::from([
                    'bState' => true,
                    'iCount' => $oComment->getCountFavourite()
                ])->msgNotice($lang->Get('comment_favourite_add_ok'), $lang->Get('attention'), true);
            } else {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
        }

        if (!$oFavouriteComment and !$iType) {
            return AjaxView::empty()->msgError($lang->Get('comment_favourite_add_no'), $lang->Get('error'), true);
        }

        if ($oFavouriteComment and $iType) {
            return AjaxView::empty()->msgError($lang->Get('comment_favourite_add_already'), $lang->Get('error'), true);
        }

        if ($oFavouriteComment and !$iType) {
            $oComment->setCountFavourite($oComment->getCountFavourite() - 1);
            if ($comment->DeleteFavouriteComment($oFavouriteComment) and $comment->UpdateComment($oComment)) {
                return AjaxView::from([
                    'bState' => false,
                    'iCount' => $oComment->getCountFavourite()
                ])->msgNotice($lang->Get('comment_favourite_del_ok'), $lang->Get('attention'), true);
            } else {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
        }

        return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
    }

    /**
     * Обработка избранного - личное сообщение
     *
     * @param \App\Modules\ModuleTalk    $talk
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventFavouriteTalk(ModuleTalk $talk, ModuleLang $lang): AjaxView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Можно только добавить или удалить из избранного
         */
        $iType = getRequestStr('type', null, 'post');
        if (!in_array($iType, ['1', '0'])) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         *    Сообщение существует?
         */
        if (!($oTalk = $talk->GetTalkById(getRequestStr('idTalk', null, 'post')))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Сообщение уже в избранном?
         */
        $oFavouriteTalk = $talk->GetFavouriteTalk($oTalk->getId(), $this->currentUser->getId());
        if (!$oFavouriteTalk and $iType) {
            $oFavouriteTalkNew = new EntityFavourite([
                'target_id'      => $oTalk->getId(),
                'target_type'    => 'talk',
                'user_id'        => $this->currentUser->getId(),
                'target_publish' => '1'
            ]);
            if ($talk->AddFavouriteTalk($oFavouriteTalkNew)) {
                return AjaxView::from([
                    'bState' => true
                ])->msgNotice($lang->Get('talk_favourite_add_ok'), $lang->Get('attention'), true);
            } else {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
        }

        if (!$oFavouriteTalk and !$iType) {
            return AjaxView::empty()->msgError($lang->Get('talk_favourite_add_no'), $lang->Get('error'), true);
        }

        if ($oFavouriteTalk and $iType) {
            return AjaxView::empty()->msgError($lang->Get('talk_favourite_add_already'), $lang->Get('error'), true);
        }

        if ($oFavouriteTalk and !$iType) {
            if ($talk->DeleteFavouriteTalk($oFavouriteTalk)) {
                return AjaxView::from([
                    'bState' => false
                ])->msgNotice($lang->Get('talk_favourite_del_ok'), $lang->Get('attention'), true);
            } else {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
        }

        return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
    }
}
