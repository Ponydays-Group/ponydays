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

use App\Entities\EntityCommentOnline;
use App\Entities\EntityEditCommentData;
use App\Entities\EntityFavourite;
use App\Entities\EntityNotification;
use App\Entities\EntityTopic;
use App\Entities\EntityTopicQuestionVote;
use App\Entities\EntityUser;
use App\Entities\EntityVote;
use App\Modules\ModuleACL;
use App\Modules\ModuleBlog;
use App\Modules\ModuleCast;
use App\Modules\ModuleComment;
use App\Modules\ModuleEditComment;
use App\Modules\ModuleFavourite;
use App\Modules\ModuleGeo;
use App\Modules\ModuleNotification;
use App\Modules\ModuleNower;
use App\Modules\ModuleRating;
use App\Modules\ModuleStream;
use App\Modules\ModuleTalk;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use App\Modules\ModuleVote;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleImage;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleLogger;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleText;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки ajax запросов
 * Ответ отдает в JSON фомате
 *
 * @package actions
 * @since   1.0
 */
class ActionAjax extends Action
{
    /**
     * Текущий пользователь
     *
     * @var EntityUser|null
     */
    protected $oUserCurrent = null;
    /**
     * @var ModuleViewer
     */
    protected $viewer = null;
    /**
     * @var \App\Modules\ModuleUser
     */
    protected $user = null;

    /**
     * Инициализация
     */
    public
    function Init()
    {
        $this->viewer = LS::Make(ModuleViewer::class);
        $this->user = LS::Make(ModuleUser::class);
        /**
         * Устанавливаем формат ответа
         */
        $this->viewer->SetResponseAjax('json');
        /**
         * Получаем текущего пользователя
         */
        $this->oUserCurrent = $this->user->GetUserCurrent();
    }

    /**
     * Регистрация евентов
     */
    protected
    function RegisterEvent()
    {
        $this->AddEventPreg('/^vote$/i', '/^comment$/', 'EventVoteComment');
        $this->AddEventPreg('/^vote$/i', '/^topic$/', 'EventVoteTopic');
        $this->AddEventPreg('/^vote$/i', '/^user$/', 'EventVoteUser');
        $this->AddEventPreg('/^vote$/i', '/^blog$/', 'EventVoteBlog');
        $this->AddEventPreg('/^vote$/i', '/^question$/', 'EventVoteQuestion');
        $this->AddEventPreg('/^favourite$/i', '/^save-tags/', 'EventFavouriteSaveTags');
        $this->AddEventPreg('/^favourite$/i', '/^topic$/', 'EventFavouriteTopic');
        $this->AddEventPreg('/^favourite$/i', '/^comment$/', 'EventFavouriteComment');
        $this->AddEventPreg('/^favourite$/i', '/^talk$/', 'EventFavouriteTalk');
        $this->AddEventPreg('/^stream$/i', '/^comment$/', 'EventStreamComment');
        $this->AddEventPreg('/^stream$/i', '/^topic$/', 'EventStreamTopic');
        $this->AddEventPreg('/^blogs$/i', '/^top$/', 'EventBlogsTop');
        $this->AddEventPreg('/^blogs$/i', '/^self$/', 'EventBlogsSelf');
        $this->AddEventPreg('/^blogs$/i', '/^join$/', 'EventBlogsJoin');
        $this->AddEventPreg('/^preview$/i', '/^text$/', 'EventPreviewText');
        $this->AddEventPreg('/^preview$/i', '/^topic/', 'EventPreviewTopic');
        $this->AddEventPreg('/^upload$/i', '/^image$/', 'EventUploadImage');
        $this->AddEventPreg('/^autocompleter$/i', '/^tag$/', 'EventAutocompleterTag');
        $this->AddEventPreg('/^autocompleter$/i', '/^user$/', 'EventAutocompleterUser');
        $this->AddEventPreg('/^comment$/i', '/^delete$/', 'EventCommentDelete');
        $this->AddEventPreg('/^geo/i', '/^get/', '/^regions$/', 'EventGeoGetRegions');
        $this->AddEventPreg('/^geo/i', '/^get/', '/^cities/', 'EventGeoGetCities');
        $this->AddEventPreg('/^infobox/i', '/^info/', '/^blog/', 'EventInfoboxInfoBlog');
        $this->AddEventPreg('/^askinvite/', 'EventInviteUser');
        $this->AddEvent('topic-lock-control', 'EventTopicLockControl');
        $this->AddEvent('get-object-votes', 'EventGetObjectVotes');
        $this->AddEventPreg('/^ignore$/i', 'EventIgnoreUser');
        $this->AddEventPreg('/^forbid-ignore$/i', 'EventForbidIgnoreUser');
        $this->AddEvent('editcomment-gethistory', 'EventGetHistory');
        $this->AddEvent('editcomment-getsource', 'EventGetSource');
        $this->AddEvent('editcomment-edit', 'EventEdit');
        $this->AddEventPreg('/^comment$/i', 'EventGetComment');
        $this->AddEvent('ban', 'EventBan');
    }

    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */


    /**
     * Вывод информации о блоге
     */
    protected function EventInfoboxInfoBlog()
    {
        /**
         * Если блог существует и он не персональный
         */
        if (!is_string(getRequest('iBlogId'))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));

            return;
        }

        if (!($oBlog = LS::Make(ModuleBlog::class)->GetBlogById(getRequest('iBlogId'))) or $oBlog->getType()
            == 'personal'
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('system_error'));

            return;
        }

        /**
         * Получаем локальный вьюер для рендеринга шаблона
         */
        $oViewer = $this->viewer->GetLocalViewer();
        $oViewer->Assign('oBlog', $oBlog);
        if ($oBlog->getType() != 'close' or $oBlog->getUserIsJoin()) {
            /**
             * Получаем последний топик
             */
            $aResult = LS::Make(ModuleTopic::class)->GetTopicsByFilter(
                [
                    'blog_id'       => $oBlog->getId(),
                    'topic_publish' => 1
                ],
                1,
                1
            );
            $oViewer->Assign('oTopicLast', reset($aResult['collection']));
        }

        $oViewer->Assign('oUserCurrent', $this->oUserCurrent);
        /**
         * Устанавливаем переменные для ajax ответа
         */
        $this->viewer->AssignAjax('sText', $oViewer->Fetch("infobox.info.blog.tpl"));
    }



    /* * */



    /**
     * Удаление/восстановление комментария
     *
     */
    protected
    function EventCommentDelete()
    {
        /**
         * Есть права на удаление комментария?
         */
        /**
         * Комментарий существует?
         */
        $idComment = getRequestStr('idComment', null, 'post');
        $sDeleteReason = getRequestStr('sDeleteReason', null, 'post');
        if (!($oComment = LS::Make(ModuleComment::class)->GetCommentById($idComment))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if (!LS::Make(ModuleACL::class)->UserCanDeleteComment($this->oUserCurrent, $oComment, 1)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $lastdeleteUser = $oComment->getDeleteUserId();
        /**
         * Устанавливаем пометку о том, что комментарий удален
         */
        $oComment->setDelete(($oComment->getDelete() + 1) % 2);
        $oComment->setDeleteReason($sDeleteReason);
        $oComment->setDeleteUserId($this->oUserCurrent->getId());
        LS::Make(ModuleHook::class)->Run(
            'comment_delete_before',
            [
                'oComment' => $oComment
            ]
        );
        if (!LS::Make(ModuleComment::class)->UpdateCommentStatus($oComment)) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        LS::Make(ModuleHook::class)->Run(
            'comment_delete_after',
            [
                'oComment' => $oComment
            ]
        );
        /**
         * Формируем текст ответа
         */
        if ($bState = (bool)$oComment->getDelete()) {
            $sMsg = LS::Make(ModuleLang::class)->Get('comment_delete_ok');
            $sTextToggle = LS::Make(ModuleLang::class)->Get('comment_repair');
            $sLogText = $this->oUserCurrent->getLogin()." удалил комментарий ".$oComment->getId();
            LS::Make(ModuleLogger::class)->Notice($sLogText);
        } else {
            $sMsg = LS::Make(ModuleLang::class)->Get('comment_repair_ok');
            $sTextToggle = LS::Make(ModuleLang::class)->Get('comment_delete');
            $sLogText = $this->oUserCurrent->getLogin()." восстановил комментарий ".$oComment->getId();
            LS::Make(ModuleLogger::class)->Notice($sLogText);
        }

        /**
         * Отправка уведомления пользователям
         */
        $notificationLink =
            LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId())->getUrl()."#comment".$oComment->getId(
            );
        if ((bool)$oComment->getDelete()) {
            $notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()
                ."</a> удалил ваш <a href='".$notificationLink."'>комментарий</a>\nПричина: "
                .$oComment->getDeleteReason();
            $notificationType = 7;
        } else {
            $notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()
                ."</a> восстановил ваш <a href='".$notificationLink."'>комментарий</a>";
            $notificationType = 8;
        }
        $notificationText = "";
        $notification = new EntityNotification(
            [
                'user_id'           => $oComment->getUserId(),
                'text'              => $notificationText,
                'title'             => $notificationTitle,
                'link'              => $notificationLink,
                'rating'            => 0,
                'notification_type' => $notificationType,
                'target_type'       => 'comment',
                'target_id'         => $oComment->getId(),
                'sender_user_id'    => $this->oUserCurrent->getId(),
                'group_target_type' => 'topic',
                'group_target_id'   => $oComment->getTargetId()
            ]
        );
        if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
            LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oComment);
        }

        if ($lastdeleteUser && $this->oUserCurrent->getId() != $lastdeleteUser) {
            $notificationLink =
                LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId())->getUrl()."#comment"
                .$oComment->getId();
            $notificationTitle = "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()
                ."</a> восстановил удаленный вами <a href='".$notificationLink."'>комментарий</a>";
            $notificationText = "";
            $notification = new EntityNotification(
                [
                    'user_id'           => $lastdeleteUser,
                    'text'              => $notificationText,
                    'title'             => $notificationTitle,
                    'link'              => $notificationLink,
                    'rating'            => 0,
                    'notification_type' => 9,
                    'target_type'       => 'comment',
                    'target_id'         => $oComment->getId(),
                    'sender_user_id'    => $this->oUserCurrent->getId(),
                    'group_target_type' => 'topic',
                    'group_target_id'   => $oComment->getTargetId()
                ]
            );
            if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
                LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oComment);
            }
        }

        /**
         * Обновление события в ленте активности
         */
        LS::Make(ModuleStream::class)->write(
            $oComment->getUserId(),
            'add_comment',
            $oComment->getId(),
            !$oComment->getDelete()
        );
        /**
         * Показываем сообщение и передаем переменные в ajax ответ
         */
        LS::Make(ModuleMessage::class)->AddNoticeSingle($sMsg, LS::Make(ModuleLang::class)->Get('attention'));
        $this->viewer->AssignAjax('bState', $bState);
        $this->viewer->AssignAjax('sTextToggle', $sTextToggle);

    }

    protected
    function EventInviteUser()
    {
        $a = $_POST["to"];
        $oUserCurrent = $this->user->_GetUserCurrent();
        $oBlog = LS::Make(ModuleBlog::class)->GetBlogById($_POST["blog"]);
        LS::Make(ModuleTalk::class)->SendTalk(
            "Просьба об инвайте",
            "Пользователь <a href='"."/profile/".$oUserCurrent->getLogin()."/' class='user'>"
            ."<i class='icon-user'></i>".$oUserCurrent->getLogin()."</a> просит пригласить его в блог <a href='"
            .$oBlog->getUrlFull()."'>".$oBlog->getTitle()."</a>.",
            $oUserCurrent->getId(),
            $a
        );
    }

    protected
    function EventTopicLockControl()
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Топик существует?
         */
        if (!($oTopic = LS::Make(ModuleTopic::class)->GetTopicById(getRequestStr('idTopic', null, 'post')))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $isAllowLockControlTopic = LS::Make(ModuleACL::class)->IsAllowLockTopicControl($oTopic, $this->oUserCurrent);
        if (!$isAllowLockControlTopic) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $bLockState = getRequestStr('bState', null, 'post') == '1';
        $bStateOld = $oTopic->isControlLocked();
        $oTopic->setLockControl($bLockState);
        if ($bStateOld == $bLockState || LS::Make(ModuleTopic::class)->UpdateControlLock($oTopic)) {
            $sNotice = $bLockState ? 'topic_control_locked' : 'topic_control_unlocked';
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                LS::Make(ModuleLang::class)->Get($sNotice),
                LS::Make(ModuleLang::class)->Get('attention')
            );
            $this->viewer->AssignAjax('bState', $oTopic->isControlLocked());
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
    }

    protected
    function EventGetObjectVotes()
    {
        $targetId = (int)getRequestStr('targetId', null, 'post');
        $targetType = getRequestStr('targetType', null, 'post');
        switch ($targetType) {
            case 'comment':
                $oTarget = LS::Make(ModuleComment::class)->GetCommentById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.comment.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.comment.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.comment.oe_end');
                $date_sort = Config::Get('acl.vote_list.comment.date_sort');
                break;
            case 'topic':
                $oTarget = LS::Make(ModuleTopic::class)->GetTopicById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.topic.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.topic.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.topic.oe_end');
                $date_sort = Config::Get('acl.vote_list.topic.date_sort');
                break;
            case 'blog':
                $oTarget = LS::Make(ModuleBlog::class)->GetBlogById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.blog.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.blog.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.blog.oe_end');
                $date_sort = Config::Get('acl.vote_list.blog.date_sort');
                break;
            case 'user':
                $oTarget = $this->user->GetUserById($targetId);
                $ne_enable_level = Config::Get('acl.vote_list.user.ne_enable_level');
                $oe_enable_level = Config::Get('acl.vote_list.user.oe_enable_level');
                $oe_end = Config::Get('acl.vote_list.user.oe_end');
                $date_sort = Config::Get('acl.vote_list.user.date_sort');
                break;
            default:
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
        }

        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent && $ne_enable_level < 8) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        /**
         * Объект существует?
         */
        if (!$oTarget) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        if (!LS::Make(ModuleACL::class)->CheckSimpleAccessLevel(
            $ne_enable_level,
            $this->oUserCurrent,
            $oTarget,
            $targetType
        )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $aVotes = LS::Make(ModuleVote::class)->GetVoteById($targetId, $targetType);
        $aResult = [];
        foreach ($aVotes as $oVote) {
            $oUser = $this->user->GetUserById($oVote->getVoterId());
            $bShowUser = $oUser
                && (strtotime($oVote->getDate()) > $oe_end
                    || LS::Make(ModuleACL::class)->CheckSimpleAccessLevel(
                        $oe_enable_level,
                        $this->oUserCurrent,
                        $oTarget,
                        $targetType
                    ));
            $aResult[] = [
                'voterName'   => $bShowUser ? $oUser->getLogin() : null,
                'voterAvatar' => $bShowUser ? $oUser->getProfileAvatarPath() : null,
                'value'       => (float)$oVote->getDirection(),
                'date'        => (string)$oVote->getDate().'+03:00',
            ];
        }

        usort($aResult, $date_sort < 0 ? '_gov_s_date_desc' : '_gov_s_date_asc');
        $this->viewer->AssignAjax('aVotes', $aResult);
    }


    /**
     * Allow|forbid ignore user
     */
    protected function EventForbidIgnoreUser()
    {
        // check auth
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        // allow only for administrator
        if (!$this->oUserCurrent->isAdministrator()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        // search for user
        if (!$oUser = $this->user->GetUserById(getRequest('idUser'))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('user_not_found'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        $aForbidIgnore = $this->user->GetForbidIgnoredUsers();
        if (in_array($oUser->getId(), $aForbidIgnore)) {
            // remove user from forbid ignore list
            if ($this->user->AllowIgnoreUser($oUser->getId())) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('allow_ignore_user_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
                $this->viewer->AssignAjax('sText', LS::Make(ModuleLang::class)->Get('forbid_ignore_user'));
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );
            }
        } else {
            // add user to forbid ignore list
            if ($this->user->ForbidIgnoreUser($oUser->getId())) {
                LS::Make(ModuleMessage::class)->AddNoticeSingle(
                    LS::Make(ModuleLang::class)->Get('forbid_ignore_user_ok'),
                    LS::Make(ModuleLang::class)->Get('attention')
                );
                $this->viewer->AssignAjax('sText', LS::Make(ModuleLang::class)->Get('allow_ignore_user'));
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );
            }
        }
    }

    /**
     * Ignore|disignore user
     */
    protected function EventIgnoreUser()
    {
        // check auth
        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('need_authorization'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        // search for ignored user
        if (!$oUserIgnored = $this->user->GetUserById(getRequest('idUser'))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('user_not_found'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }

        // is user try to ignore self
        if ($oUserIgnored->getId() == $this->oUserCurrent->getId()) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('ignore_dissalow_own'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        $sType = getRequest('type');

        if ($sType == ModuleUser::TYPE_IGNORE_COMMENTS || $sType == ModuleUser::TYPE_IGNORE_TOPICS) {
            if ($this->user->IsUserIgnoredByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                // remove user from ignore list
                if ($this->user->UnIgnoreUserByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                    LS::Make(ModuleMessage::class)->AddNoticeSingle(
                        LS::Make(ModuleLang::class)->Get('disignore_user_ok_'.$sType),
                        LS::Make(ModuleLang::class)->Get('attention')
                    );
                    $this->viewer->AssignAjax('sText', LS::Make(ModuleLang::class)->Get('ignore_user_'.$sType));
                } else {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('system_error'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );
                }
            } else {
                $aForbidIgnore = $this->user->GetForbidIgnoredUsers();
                //check ignored user in forbid ignored list
                if (in_array($oUserIgnored->getId(), $aForbidIgnore)) {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('ignore_dissalow_this'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );

                    return;
                }

                //add user to ignore list
                if ($this->user->IgnoreUserByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                    LS::Make(ModuleMessage::class)->AddNoticeSingle(
                        LS::Make(ModuleLang::class)->Get('ignore_user_ok_'.$sType),
                        LS::Make(ModuleLang::class)->Get('attention')
                    );
                    $this->viewer->AssignAjax('sText', LS::Make(ModuleLang::class)->Get('disignore_user_'.$sType));
                } else {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(
                        LS::Make(ModuleLang::class)->Get('system_error'),
                        LS::Make(ModuleLang::class)->Get('error')
                    );
                }
            }
        } else {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
    }

    protected function EventGetHistory()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        $this->viewer->SetResponseAjax('json');

        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        $oComment = LS::Make(ModuleComment::class)->GetCommentById(getRequest('reply'));

        if (!$oComment) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        if ($oComment->getTargetType() == 'talk') {
            if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById($oComment->getTargetId()))) {
                echo "NO TARGET";
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            /**
             * Пользователь есть в переписке?
             */
            if (!($oTalkUser =
                LS::Make(ModuleTalk::class)->GetTalkUser($oTalk->getId(), $this->oUserCurrent->getId()))
            ) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            /**
             * Пользователь активен в переписке?
             */
            if ($oTalkUser->getUserActive() != ModuleTalk::TALK_USER_ACTIVE) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
        } else {
            if ($oComment->getTargetType() != 'topic' or !($oTopic = $oComment->getTarget())) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId() != $oTopic->getUserId(
                        ) and !$this->oUserCurrent->isAdministrator()))
            ) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('system_error'),
                    LS::Make(ModuleLang::class)->Get('error')
                );

                return;
            }
            /**
             * Проверяет коммент на удаленность и отдает его только автору, и тем, у кого есть права на удаление.
             */
            if ((!$this->oUserCurrent
                || ($oComment->getDelete()
                    && !(LS::Make(ModuleACL::class)->UserCanDeleteComment(
                            $this->oUserCurrent,
                            $oComment,
                            1
                        )
                        || $this->oUserCurrent->getId() == $oComment->getUserId())))
            ) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('not_access'),
                    LS::Make(ModuleLang::class)->Get('not_access')
                );
                Router::Action('error');

                return;
            }
            /**
             * Проверяет коммент на доступность из закрытых блогов.
             */
            if (in_array($oTopic->getBlog()->getType(), ['close', 'invite'])
                and (!$this->oUserCurrent
                    || !in_array(
                        $oTopic->getBlog()->getId(),
                        LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($this->oUserCurrent)
                    )
                )
            ) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(
                    LS::Make(ModuleLang::class)->Get('blog_close_show'),
                    LS::Make(ModuleLang::class)->Get('not_access')
                );
                Router::Action('error');

                return;
            }
        }

        $aData = LS::Make(ModuleEditComment::class)->GetDataItemsByCommentId($oComment->getId());

        foreach ($aData as $oData) {
            $oUser = $this->user->GetUserById($oData->getUserId());
            $oData->setText(LS::Make(ModuleText::class)->Parser($oData->getCommentTextSource()));
            $oData->setUserLogin($oUser->getLogin());
        }

        $oViewerLocal = $this->viewer->GetLocalViewer();
        $oViewerLocal->Assign('aHistory', $aData);
        $this->viewer->AssignAjax('sContent', $oViewerLocal->Fetch('history.tpl'));
    }

    protected function EventGetSource()
    {
        /**
         * Устанавливаем формат Ajax ответа
         */
        $this->viewer->SetResponseAjax('json');

        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        $oComment = LS::Make(ModuleComment::class)->GetCommentById(getRequest('idComment'));

        if (!$oComment) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        $sCheckResult = LS::Make(ModuleACL::class)->UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($sCheckResult);

            return;
        }

        $oEditData = LS::Make(ModuleEditComment::class)->GetLastEditData($oComment->getId());

        if ($oEditData) {
            $sCommentSource = $oEditData->getCommentTextSource();
        } elseif (!Config::Get('view.tinymce')) {
            $sCommentSource = str_replace(["<br>", "<br/>"], [""], $oComment->getText());
        } else {
            $sCommentSource = $oComment->getText();
        }

        $this->viewer->AssignAjax('sCommentSource', $sCommentSource);
        $this->viewer->AssignAjax('bHasHistory', !is_null($oEditData));
    }

    protected function EventEdit()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        /**
         * Устанавливаем формат Ajax ответа
         */
        $this->viewer->SetResponseAjax('json');

        if (!$this->oUserCurrent) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        $oComment = LS::Make(ModuleComment::class)->GetCommentById(getRequest('reply'));

        if (!$oComment) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('not_access'));

            return;
        }

        $sCheckResult = LS::Make(ModuleACL::class)->UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true) {
            LS::Make(ModuleMessage::class)->AddErrorSingle($sCheckResult);

            return;
        }

        $bMark = getRequestStr('form_comment_mark') == "on";
        if ($bMark) {
            $sText =
                LS::Make(ModuleText::class)->Parser(LS::Make(ModuleText::class)->Mark(getRequestStr('comment_text')));
        } else {
            $sText = LS::Make(ModuleText::class)->Parser(getRequestStr('comment_text'));
        }

        $sText = preg_replace_callback(
            '/@(.*?)\((.*?)\)/',
            function ($matches) use ($oComment) {
                $sLogin = $matches[1];
                $sNick = $matches[2];
                $r = "<a href=\"/profile/".$sLogin."/\" class=\"ls-user\">&#64;".$sNick."</a>";
                if ($oTargetUser = $this->user->getUserByLogin($sLogin)) {
                    LS::Make(ModuleCast::class)->sendCastNotifyToUser(
                        "comment",
                        $oComment,
                        LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId()),
                        $oTargetUser
                    );

                    return $r;
                }

                return $matches[0];
            },
            $sText
        );
        $sText = preg_replace_callback(
            '/@([a-zA-Zа-яА-Я0-9-_]+)/',
            function ($matches) use ($oComment) {
                $sLogin = $matches[1];
                $r = "<a href=\"/profile/".$sLogin."/\" class=\"ls-user\">&#64;".$sLogin."</a>";
                if ($oTargetUser = $this->user->getUserByLogin($sLogin)) {
                    LS::Make(ModuleCast::class)->sendCastNotifyToUser(
                        "comment",
                        $oComment,
                        LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId()),
                        $oTargetUser
                    );

                    return $r;
                }

                return $matches[0];
            },
            $sText
        );

        if (mb_strlen($sText, 'utf-8') > Config::Get('module.comment.max_length')) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get(
                    'editcomment.err_max_comment_length',
                    ['maxlength' => Config::Get('max_comment_length')]
                )
            );

            return;
        }

        $sDE = date("Y-m-d H:i:s");

        $bEdited = false;
        if ($oComment->getText() == $sText) {
            LS::Make(ModuleMessage::class)->AddNoticeSingle(
                LS::Make(ModuleLang::class)->Get('editcomment.notice_nothing_changed')
            );
            $bEdited = false;
            $this->viewer->AssignAjax('bEdited', $bEdited);
        } else {
            if (Config::Get('module.editcomment.change_online')) {
                $oComment->setDate($sDE);
            }
            $oComment->setEditCount($oComment->getEditCount() + 1);
            $oComment->setEditDate($sDE);
            $oViewerLocal = $this->viewer->GetLocalViewer();
            $oViewerLocal->Assign('oComment', $oComment);
            $oViewerLocal->Assign('oUserCurrent', $this->oUserCurrent);

            if (Config::Get('module.editcomment.add_edit_date')) {
                $oComment->setText($sText.$oViewerLocal->Fetch('inject_comment_edited.tpl'));
            } else {
                $oComment->setText($sText);
            }
            $oComment->setText(
                LS::Make(ModuleText::class)->CommentParser($oComment, getRequestStr('form_comment_mark') == "on", true)
            );
            $oComment->setTextHash(md5($oComment->getText()));

            if (LS::Make(ModuleComment::class)->UpdateComment($oComment)) {
                if (Config::Get('module.editcomment.change_online')) {
                    $oCommentOnline = new EntityCommentOnline();
                    $oCommentOnline->setTargetId($oComment->getTargetId());
                    $oCommentOnline->setTargetType($oComment->getTargetType());
                    $oCommentOnline->setTargetParentId($oComment->getTargetParentId());
                    $oCommentOnline->setCommentId($oComment->getId());

                    LS::Make(ModuleComment::class)->AddCommentOnline($oCommentOnline);
                }

                $this->oUserCurrent->setDateCommentLast($sDE);
                $this->user->Update($this->oUserCurrent);

                $oData = new EntityEditCommentData();
                $oData->setCommentTextSource(getRequest('comment_text'));
                $oData->setCommentId($oComment->getId());
                $oData->setUserId($this->oUserCurrent->getId());
                $oData->setDateAdd($sDE);

                if (!$oData->save()) {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('error'));

                    return;
                }

                $bEdited = true;
                $this->viewer->AssignAjax('bEdited', $bEdited);
                $this->viewer->AssignAjax(
                    'bCanEditMore',
                    LS::Make(ModuleACL::class)->UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX) === true
                );
                $this->viewer->AssignAjax('sCommentText', $oComment->getText());
            } else {
                LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('error'));
            }
        }
        if ($bEdited) {
            if ($oComment->getTargetType() == 'topic') {

                /**
                 * Отправка уведомления пользователям
                 */
                $notificationLink =
                    LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId())->getUrl()."#comment"
                    .$oComment->getId();
                $notificationTitle =
                    "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()."</a>"
                    ." отредактировал ваш <a href='".$notificationLink
                    ."'>комментарий</a> в посте <a href='/blog/undefined/".$oComment->getTargetId()."'>"
                    .$oComment->getTarget()->getTitle()."</a>";
                $notificationText = "";
                $notification = new EntityNotification(
                    [
                        'user_id'           => $oComment->getUserId(),
                        'text'              => $notificationText,
                        'title'             => $notificationTitle,
                        'link'              => $notificationLink,
                        'rating'            => 0,
                        'notification_type' => 6,
                        'target_type'       => 'comment',
                        'target_id'         => $oComment->getId(),
                        'sender_user_id'    => $this->oUserCurrent->getId(),
                        'group_target_type' => 'topic',
                        'group_target_id'   => $oComment->getTargetId()
                    ]
                );
                if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
                    LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oComment);
                }

            } elseif ($oComment->getTargetType() == 'talk') {
                if (!($oTalk = LS::Make(ModuleTalk::class)->GetTalkById($oComment->getTargetId()))) {
                    LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('error'));

                    return;
                }
                /**
                 * Отправка уведомления пользователям
                 */
                $notificationLink = "/talk/".$oComment->getTargetId()."#comment".$oComment->getId();
                $notificationTitle =
                    "<a href='".$this->oUserCurrent->getUserWebPath()."'>".$this->oUserCurrent->getLogin()."</a>"
                    ." отредактировал ваш <a href='".$notificationLink."'>комментарий</a> в личке ".$oTalk->getTitle();
                $notificationText = "";
                $notification = new EntityNotification(
                    [
                        'user_id'           => $oComment->getUserId(),
                        'text'              => $notificationText,
                        'title'             => $notificationTitle,
                        'link'              => $notificationLink,
                        'rating'            => 0,
                        'notification_type' => 6,
                        'target_type'       => 'comment',
                        'target_id'         => $oComment->getId(),
                        'sender_user_id'    => $this->oUserCurrent->getId(),
                        'group_target_type' => 'talk',
                        'group_target_id'   => $oComment->getTargetId()
                    ]
                );
                if ($notificationCreated = LS::Make(ModuleNotification::class)->createNotification($notification)) {
                    LS::Make(ModuleNower::class)->PostNotificationWithComment($notificationCreated, $oComment);
                }
            }

            $sLogText = $this->oUserCurrent->getLogin()." редактировал коммент ".$oComment->getId()." ".$ip;
            LS::Make(ModuleLogger::class)->Notice($sLogText);
        }
        $this->viewer->AssignAjax(
            'bCanEditMore',
            LS::Make(ModuleACL::class)->UserCanEditComment($this->oUserCurrent, $oComment, PHP_INT_MAX) === true
        );
    }

    protected function EventGetComment()
    {
        $idComment = getRequestStr('idComment', null, 'post');
        if (!($oComment = LS::Make(ModuleComment::class)->GetCommentById($idComment))) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        if ($oComment->getTargetType() != 'topic' or !($oTopic = $oComment->getTarget())) {
            $this->Message_AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId() != $oTopic->getUserId()
                    and !$this->oUserCurrent->isAdministrator()))
        ) {
            $this->Message_AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('system_error'),
                LS::Make(ModuleLang::class)->Get('error')
            );

            return;
        }
        /**
         * Проверяет коммент на удаленность и отдает его только автору, и тем, у кого есть права на удаление.
         */
        if ((!$this->oUserCurrent
            || ($oComment->getDelete()
                && !(LS::Make(ModuleACL::class)->UserCanDeleteComment(
                        $this->oUserCurrent,
                        $oComment,
                        1
                    )
                    || $this->oUserCurrent->getId() == $oComment->getUserId())))
        ) {
            $this->Message_AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('not_access'),
                LS::Make(ModuleLang::class)->Get('not_access')
            );
            Router::Action('error');

            return;
        }
        if (in_array($oTopic->getBlog()->getType(), ['close', 'invite'])
            and (!$this->oUserCurrent
                || !in_array(
                    $oTopic->getBlog()->getId(),
                    LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($this->oUserCurrent)
                )
            )
        ) {
            $this->Message_AddErrorSingle(
                LS::Make(ModuleLang::class)->Get('blog_close_show'),
                LS::Make(ModuleLang::class)->Get('not_access')
            );
            Router::Action('error');

            return;
        }
        $bIgnoreDelete = false;
        if ($this->oUserCurrent) {
            if ($this->oUserCurrent->isAdministrator() || $this->oUserCurrent->isGlobalModerator()) {
                $bIgnoreDelete = true;
            }
        }
        $aResult =
            LS::Make(ModuleComment::class)->ConvertCommentToArray($oComment, $oTopic->getDateRead(), $bIgnoreDelete);
        $this->viewer->AssignAjax("aComment", $aResult);
        $this->viewer->DisplayAjax();
    }
}
