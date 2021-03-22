<?php

namespace App\Actions\Ajax;

use App\Entities\EntityCommentOnline;
use App\Entities\EntityEditCommentData;
use App\Entities\EntityNotification;
use App\Modules\ModuleACL;
use App\Modules\ModuleBlog;
use App\Modules\ModuleCast;
use App\Modules\ModuleComment;
use App\Modules\ModuleEditComment;
use App\Modules\ModuleNotification;
use App\Modules\ModuleNower;
use App\Modules\ModuleStream;
use App\Modules\ModuleTalk;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleHook;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleLogger;
use Engine\Modules\ModuleText;
use Engine\Result\View\AjaxView;
use Engine\Result\View\HtmlView;
use Engine\Routing\Controller;

class ActionAjaxComment extends Controller
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
     * Удаление/восстановление комментария
     *
     * @param \App\Modules\ModuleACL          $acl
     * @param \App\Modules\ModuleTopic        $topic
     * @param \App\Modules\ModuleComment      $comment
     * @param \App\Modules\ModuleStream       $stream
     * @param \App\Modules\ModuleNower        $nower
     * @param \App\Modules\ModuleNotification $m_notification
     * @param \Engine\Modules\ModuleHook      $hook
     * @param \Engine\Modules\ModuleLogger    $logger
     * @param \Engine\Modules\ModuleLang      $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventCommentDelete(ModuleACL $acl, ModuleTopic $topic, ModuleComment $comment, ModuleStream $stream, ModuleNower $nower, ModuleNotification $m_notification, ModuleHook $hook, ModuleLogger $logger, ModuleLang $lang): AjaxView
    {
        /**
         * Есть права на удаление комментария?
         */
        /**
         * Комментарий существует?
         */
        $idComment = getRequestStr('idComment', null, 'post');
        $sDeleteReason = getRequestStr('sDeleteReason', null, 'post');
        if (!($oComment = $comment->GetCommentById($idComment))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        if (!$acl->UserCanDeleteComment($this->currentUser, $oComment, 1)) {
            return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
        }

        $lastdeleteUser = $oComment->getDeleteUserId();
        /**
         * Устанавливаем пометку о том, что комментарий удален
         */
        $oComment->setDelete(($oComment->getDelete() + 1) % 2);
        $oComment->setDeleteReason($sDeleteReason);
        $oComment->setDeleteUserId($this->currentUser->getId());
        $hook->Run('comment_delete_before', ['oComment' => $oComment]);
        if (!$comment->UpdateCommentStatus($oComment)) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        $hook->Run('comment_delete_after', ['oComment' => $oComment]);
        /**
         * Формируем текст ответа
         */
        if ($bState = (bool)$oComment->getDelete()) {
            $sMsg = $lang->Get('comment_delete_ok');
            $sTextToggle = $lang->Get('comment_repair');
            $sLogText = $this->currentUser->getLogin()." удалил комментарий ".$oComment->getId();
            $logger->Notice($sLogText);
        } else {
            $sMsg = $lang->Get('comment_repair_ok');
            $sTextToggle = $lang->Get('comment_delete');
            $sLogText = $this->currentUser->getLogin()." восстановил комментарий ".$oComment->getId();
            $logger->Notice($sLogText);
        }

        /**
         * Отправка уведомления пользователям
         */
        $notificationLink = $topic->GetTopicById($oComment->getTargetId())->getUrl()."#comment".$oComment->getId();
        if ((bool)$oComment->getDelete()) {
            $notificationTitle = "<a href='".$this->currentUser->getUserWebPath()."'>".$this->currentUser->getLogin()
                ."</a> удалил ваш <a href='".$notificationLink."'>комментарий</a>\nПричина: "
                .$oComment->getDeleteReason();
            $notificationType = 7;
        } else {
            $notificationTitle = "<a href='".$this->currentUser->getUserWebPath()."'>".$this->currentUser->getLogin()
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
                'sender_user_id'    => $this->currentUser->getId(),
                'group_target_type' => 'topic',
                'group_target_id'   => $oComment->getTargetId()
            ]
        );
        if ($notificationCreated = $m_notification->createNotification($notification)) {
            $nower->PostNotificationWithComment($notificationCreated, $oComment);
        }

        if ($lastdeleteUser && $this->currentUser->getId() != $lastdeleteUser) {
            $notificationLink = $topic->GetTopicById($oComment->getTargetId())->getUrl()."#comment".$oComment->getId();
            $notificationTitle = "<a href='".$this->currentUser->getUserWebPath()."'>".$this->currentUser->getLogin()."</a> восстановил удаленный вами <a href='".$notificationLink."'>комментарий</a>";
            $notificationText = "";
            $notification = new EntityNotification([
                'user_id'           => $lastdeleteUser,
                'text'              => $notificationText,
                'title'             => $notificationTitle,
                'link'              => $notificationLink,
                'rating'            => 0,
                'notification_type' => 9,
                'target_type'       => 'comment',
                'target_id'         => $oComment->getId(),
                'sender_user_id'    => $this->currentUser->getId(),
                'group_target_type' => 'topic',
                'group_target_id'   => $oComment->getTargetId()
            ]);
            if ($notificationCreated = $m_notification->createNotification($notification)) {
                $nower->PostNotificationWithComment($notificationCreated, $oComment);
            }
        }

        /**
         * Обновление события в ленте активности
         */
        $stream->write(
            $oComment->getUserId(),
            'add_comment',
            $oComment->getId(),
            !$oComment->getDelete()
        );

        return AjaxView::from(['bState' => $bState, 'sTextToggle' => $sTextToggle])->msgNotice($sMsg, $lang->Get('attention'));
    }

    protected function eventGetComment(ModuleACL $acl, ModuleBlog $blog, ModuleComment $comment, ModuleLang $lang): AjaxView
    {
        $idComment = getRequestStr('idComment', null, 'post');
        if (!($oComment = $comment->GetCommentById($idComment))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }
        if ($oComment->getTargetType() != 'topic' or !($oTopic = $oComment->getTarget())) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }
        if (!$oTopic->getPublish() and (!$this->currentUser or ($this->currentUser->getId() != $oTopic->getUserId() and !$this->currentUser->isAdministrator()))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }
        /**
         * Проверяет коммент на удаленность и отдает его только автору, и тем, у кого есть права на удаление.
         */
        if ((!$this->currentUser || ($oComment->getDelete() && !($acl->UserCanDeleteComment($this->currentUser, $oComment, 1) || $this->currentUser->getId() == $oComment->getUserId())))) {
            return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
        }
        if (in_array($oTopic->getBlog()->getType(), ['close', 'invite']) and (!$this->currentUser || !in_array($oTopic->getBlog()->getId(), $blog->GetAccessibleBlogsByUser($this->currentUser)))) {
            return AjaxView::empty()->msgError($lang->Get('blog_close_show'), $lang->Get('not_access'), true);
        }
        $bIgnoreDelete = false;
        if ($this->currentUser) {
            if ($this->currentUser->isAdministrator() || $this->currentUser->isGlobalModerator()) {
                $bIgnoreDelete = true;
            }
        }
        $aResult = $comment->ConvertCommentToArray($oComment, $oTopic->getDateRead(), $bIgnoreDelete);

        return AjaxView::from(["aComment" => $aResult]);
    }

    protected function eventGetHistory(ModuleACL $acl, ModuleUser $user, ModuleBlog $blog, ModuleTalk $talk, ModuleComment $comment, ModuleEditComment $editComment, ModuleText $text, ModuleLang $lang): AjaxView
    {
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
        }

        $oComment = $comment->GetCommentById(getRequest('reply'));
        if (!$oComment) {
            return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
        }

        if ($oComment->getTargetType() == 'talk') {
            if (!($oTalk = $talk->GetTalkById($oComment->getTargetId()))) {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
            /**
             * Пользователь есть в переписке?
             */
            if (!($oTalkUser = $talk->GetTalkUser($oTalk->getId(), $this->currentUser->getId()))) {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
            /**
             * Пользователь активен в переписке?
             */
            if ($oTalkUser->getUserActive() != ModuleTalk::TALK_USER_ACTIVE) {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
        } else {
            if ($oComment->getTargetType() != 'topic' or !($oTopic = $oComment->getTarget())) {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
            if (!$oTopic->getPublish() and (!$this->currentUser or ($this->currentUser->getId() != $oTopic->getUserId() and !$this->currentUser->isAdministrator()))) {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
            /**
             * Проверяет коммент на удаленность и отдает его только автору, и тем, у кого есть права на удаление.
             */
            if ((!$this->currentUser || ($oComment->getDelete() && !($acl->UserCanDeleteComment($this->currentUser, $oComment, 1) || $this->currentUser->getId() == $oComment->getUserId())))) {
                return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
            }
            /**
             * Проверяет коммент на доступность из закрытых блогов.
             */
            if (in_array($oTopic->getBlog()->getType(), ['close', 'invite']) and (!$this->currentUser || !in_array($oTopic->getBlog()->getId(), $blog->GetAccessibleBlogsByUser($this->currentUser)))) {
                return AjaxView::empty()->msgError($lang->Get('blog_close_show'), $lang->Get('not_access'), true);
            }
        }

        $aData = $editComment->GetDataItemsByCommentId($oComment->getId());

        foreach ($aData as $oData) {
            $oUser = $user->GetUserById($oData->getUserId());
            $oData->setText($text->Parser($oData->getCommentTextSource()));
            $oData->setUserLogin($oUser->getLogin());
        }

        return AjaxView::from(['sContent' => HtmlView::global('history.tpl')->with(['aHistory', $aData])->fetch()]);
    }

    protected function eventGetSource(ModuleACL $acl, ModuleComment $comment, ModuleEditComment $editComment, ModuleLang $lang): AjaxView
    {
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
        }

        $oComment = $comment->GetCommentById(getRequest('idComment'));
        if (!$oComment) {
            return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
        }

        $sCheckResult = $acl->UserCanEditComment($this->currentUser, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true) {
            return AjaxView::empty()->msgError($sCheckResult, $lang->Get('error'), true);
        }

        $oEditData = $editComment->GetLastEditData($oComment->getId());
        if ($oEditData) {
            $sCommentSource = $oEditData->getCommentTextSource();
        } elseif (!Config::Get('view.tinymce')) {
            $sCommentSource = str_replace(["<br>", "<br/>"], [""], $oComment->getText());
        } else {
            $sCommentSource = $oComment->getText();
        }

        return AjaxView::from(['sCommentSource' => $sCommentSource, 'bHasHistory' => !is_null($oEditData)]);
    }

    // TODO: too bad
    protected function eventEdit(ModuleACL $acl, ModuleUser $user, ModuleTopic $topic, ModuleTalk $talk, ModuleComment $comment, ModuleEditComment $editComment, ModuleNotification $m_notification, ModuleNower $nower, ModuleLogger $logger, ModuleCast $cast, ModuleText $text, ModuleLang $lang): AjaxView
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
        }

        $oComment = $comment->GetCommentById(getRequest('reply'));
        if (!$oComment) {
            return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
        }

        $sCheckResult = $acl->UserCanEditComment($this->currentUser, $oComment, PHP_INT_MAX);
        if ($sCheckResult !== true) {
            return AjaxView::empty()->msgError($sCheckResult, $lang->Get('error'), true);
        }

        $bMark = getRequestStr('form_comment_mark') == "on";
        if ($bMark) {
            $sText = $text->Parser($text->Mark(getRequestStr('comment_text')));
        } else {
            $sText = $text->Parser(getRequestStr('comment_text'));
        }

        $sText = preg_replace_callback(
            '/@(.*?)\((.*?)\)/',
            function ($matches) use ($oComment, $cast, $user, $topic) {
                $sLogin = $matches[1];
                $sNick = $matches[2];
                $r = "<a href=\"/profile/".$sLogin."/\" class=\"ls-user\">&#64;".$sNick."</a>";
                if ($oTargetUser = $user->getUserByLogin($sLogin)) {
                    $cast->sendCastNotifyToUser("comment", $oComment, $topic->GetTopicById($oComment->getTargetId()), $oTargetUser);

                    return $r;
                }

                return $matches[0];
            },
            $sText
        );
        $sText = preg_replace_callback(
            '/@([a-zA-Zа-яА-Я0-9-_]+)/',
            function ($matches) use ($oComment, $cast, $user, $topic) {
                $sLogin = $matches[1];
                $r = "<a href=\"/profile/".$sLogin."/\" class=\"ls-user\">&#64;".$sLogin."</a>";
                if ($oTargetUser = $user->getUserByLogin($sLogin)) {
                    $cast->sendCastNotifyToUser("comment", $oComment, $topic->GetTopicById($oComment->getTargetId()), $oTargetUser);

                    return $r;
                }

                return $matches[0];
            },
            $sText
        );

        if (mb_strlen($sText, 'utf-8') > Config::Get('module.comment.max_length')) {
            return AjaxView::empty()->msgError(
                $lang->Get('editcomment.err_max_comment_length', ['maxlength' => Config::Get('max_comment_length')]),
                $lang->Get('error'), true
            );
        }

        $sDE = date("Y-m-d H:i:s");

        $view = AjaxView::empty();

        $bEdited = false;
        if ($oComment->getText() == $sText) {
            $bEdited = false;
            $view->with(['bEdited', $bEdited])->msgNotice($lang->Get('editcomment.notice_nothing_changed'));
        } else {
            if (Config::Get('module.editcomment.change_online')) {
                $oComment->setDate($sDE);
            }
            $oComment->setEditCount($oComment->getEditCount() + 1);
            $oComment->setEditDate($sDE);

            if (Config::Get('module.editcomment.add_edit_date')) {
                $local = HtmlView::global('inject_comment_edited.tpl')->with([
                    'oComment' => $oComment,
                    'oUserCurrent' => $this->currentUser
                ]);
                $oComment->setText($sText.$local->fetch());
            } else {
                $oComment->setText($sText);
            }
            $bMark = getRequestStr('form_comment_mark') == "on";
            if ($bMark) {
                $sText = $text->Parser($text->Mark(getRequestStr('comment_text')));
            } else {
                $sText = $text->Parser(getRequestStr('comment_text'));
            }

            $oComment->setText($sText);
            $oComment->setTextHash(md5($oComment->getText()));

            if ($comment->UpdateComment($oComment)) {
                if (Config::Get('module.editcomment.change_online')) {
                    $oCommentOnline = new EntityCommentOnline();
                    $oCommentOnline->setTargetId($oComment->getTargetId());
                    $oCommentOnline->setTargetType($oComment->getTargetType());
                    $oCommentOnline->setTargetParentId($oComment->getTargetParentId());
                    $oCommentOnline->setCommentId($oComment->getId());

                    $comment->AddCommentOnline($oCommentOnline);
                }

                $this->currentUser->setDateCommentLast($sDE);
                $user->Update($this->currentUser);

                $oData = new EntityEditCommentData();
                $oData->setCommentTextSource(getRequest('comment_text'));
                $oData->setCommentId($oComment->getId());
                $oData->setUserId($this->currentUser->getId());
                $oData->setDateAdd($sDE);

                if (!$oData->save()) {
                    return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
                }

                $bEdited = true;
                $view->with([
                    'bEdited' => $bEdited, 'bCanEditMore' =>
                    $acl->UserCanEditComment($this->currentUser, $oComment, PHP_INT_MAX) === true,
                    'sCommentText' => $oComment->getText()
                ]);
            } else {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
        }
        if ($bEdited) {
            if ($oComment->getTargetType() == 'topic') {

                /**
                 * Отправка уведомления пользователям
                 */
                $notificationLink = $topic->GetTopicById($oComment->getTargetId())->getUrl()."#comment".$oComment->getId();
                $notificationTitle =
                    "<a href='".$this->currentUser->getUserWebPath()."'>".$this->currentUser->getLogin()."</a>"
                    ." отредактировал ваш <a href='".$notificationLink
                    ."'>комментарий</a> в посте <a href='/blog/undefined/".$oComment->getTargetId()."'>"
                    .$oComment->getTarget()->getTitle()."</a>";
                $notificationText = "";
                $notification = new EntityNotification([
                    'user_id'           => $oComment->getUserId(),
                    'text'              => $notificationText,
                    'title'             => $notificationTitle,
                    'link'              => $notificationLink,
                    'rating'            => 0,
                    'notification_type' => 6,
                    'target_type'       => 'comment',
                    'target_id'         => $oComment->getId(),
                    'sender_user_id'    => $this->currentUser->getId(),
                    'group_target_type' => 'topic',
                    'group_target_id'   => $oComment->getTargetId()
                ]);
                if ($notificationCreated = $m_notification->createNotification($notification)) {
                    $nower->PostNotificationWithComment($notificationCreated, $oComment);
                }

            } elseif ($oComment->getTargetType() == 'talk') {
                if (!($oTalk = $talk->GetTalkById($oComment->getTargetId()))) {
                    return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
                }
                /**
                 * Отправка уведомления пользователям
                 */
                $notificationLink = "/talk/".$oComment->getTargetId()."#comment".$oComment->getId();
                $notificationTitle =
                    "<a href='".$this->currentUser->getUserWebPath()."'>".$this->currentUser->getLogin()."</a>"
                    ." отредактировал ваш <a href='".$notificationLink."'>комментарий</a> в личке ".$oTalk->getTitle();
                $notificationText = "";
                $notification = new EntityNotification([
                    'user_id'           => $oComment->getUserId(),
                    'text'              => $notificationText,
                    'title'             => $notificationTitle,
                    'link'              => $notificationLink,
                    'rating'            => 0,
                    'notification_type' => 6,
                    'target_type'       => 'comment',
                    'target_id'         => $oComment->getId(),
                    'sender_user_id'    => $this->currentUser->getId(),
                    'group_target_type' => 'talk',
                    'group_target_id'   => $oComment->getTargetId()
                ]);
                if ($notificationCreated = $m_notification->createNotification($notification)) {
                    $nower->PostNotificationWithComment($notificationCreated, $oComment);
                }
            }

            $sLogText = $this->currentUser->getLogin()." редактировал коммент ".$oComment->getId()." ".$ip;
            $logger->Notice($sLogText);
        }

        return $view->with(['bCanEditMore' => $acl->UserCanEditComment($this->currentUser, $oComment, PHP_INT_MAX) === true]);
    }
}
