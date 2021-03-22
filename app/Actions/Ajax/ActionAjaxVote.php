<?php

namespace App\Actions\Ajax;

use App\Entities\EntityTopicQuestionVote;
use App\Entities\EntityVote;
use App\Modules\ModuleACL;
use App\Modules\ModuleBlog;
use App\Modules\ModuleComment;
use App\Modules\ModuleRating;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use App\Modules\ModuleVote;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Result\View\AjaxView;
use Engine\Result\View\HtmlView;
use Engine\Result\View\JsonView;
use Engine\Routing\Controller;

class ActionAjaxVote extends Controller
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
     * Голосование за комментарий
     *
     * @param \App\Modules\ModuleACL        $acl
     * @param \App\Modules\ModuleVote       $vote
     * @param \App\Modules\ModuleComment    $comment
     * @param \App\Modules\ModuleRating     $rating
     * @param \Engine\Modules\ModuleLang    $lang
     *
     * @return \Engine\Result\View\JsonView
     * @throws \Exception
     */
    protected function eventVoteComment(ModuleACL $acl, ModuleVote $vote, ModuleComment $comment, ModuleRating $rating, ModuleLang $lang): JsonView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Комментарий существует?
         */
        if (!($oComment = $comment->GetCommentById(getRequestStr('idComment', null, 'post')))) {
            return AjaxView::empty()->msgError($lang->Get('comment_vote_error_noexists'), $lang->Get('error'), true);
        }

        /**
         * Голосует автор комментария?
         */
        if ($oComment->getUserId() == $this->currentUser->getId()) {
            return AjaxView::empty()->msgError($lang->Get('comment_vote_error_self'), $lang->Get('attention'), true);
        }

        /**
         * Время голосования истекло?
         */
        if (Config::Get('acl.vote.comment.limit_time') != 0 && strtotime($oComment->getDate()) <= time() - Config::Get('acl.vote.comment.limit_time')) {
            return AjaxView::empty()->msgError($lang->Get('comment_vote_error_time'), $lang->Get('attention'), true);
        }

        /**
         * Пользователь имеет право голоса?
         */
        if (! $acl->CanVoteComment($this->currentUser, $oComment)) {
            return AjaxView::empty()->msgError($lang->Get('comment_vote_error'), $lang->Get('attention'), true);
        }

        /**
         * Как именно голосует пользователь
         */
        $iValue = getRequestStr('value', null, 'post');
        if (!in_array($iValue, ['1', '-1'])) {
            return AjaxView::empty()->msgError($lang->Get('comment_vote_error_value'), $lang->Get('attention'), true);
        }

        /**
         * Голосуем
         */
        $iValueOld = $iValue;
        $iVoteType = 0; //0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
        $iCountVote = 1;
        if ($oTopicCommentVote = $vote->GetVote($oComment->getId(), 'comment', $this->currentUser->getId())) {
            if ($iValue == $oTopicCommentVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
                $iVoteType = 2;
                $iCountVote = -1;
            } elseif ($oTopicCommentVote->getDirection() != 0) {
                $iValue += $iValue;
                $iVoteType = 1;
                $iCountVote = 0;
            }
            $vote->DeleteVote($oComment->getId(), 'comment', $this->currentUser->getId());
        }

        $oTopicCommentVote = new EntityVote();
        $oTopicCommentVote->setTargetId($oComment->getId());
        $oTopicCommentVote->setTargetType('comment');
        $oTopicCommentVote->setVoterId($this->currentUser->getId());
        $oTopicCommentVote->setDirection($iValueOld);
        $oTopicCommentVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float) $rating->VoteComment($this->currentUser, $oComment, $iValue, $iValueOld, $iCountVote, $iVoteType);
        $oTopicCommentVote->setValue($iVal);

        if ($vote->AddVote($oTopicCommentVote) and $comment->UpdateComment($oComment)) {
            if ($iValueOld == 0) {
                $vote->DeleteVote($oComment->getId(), 'comment', $this->currentUser->getId());
                $notice = $lang->Get('comment_vote_deleted');
            } else {
                $notice = $lang->Get('comment_vote_ok');
            }
            /**
             * Добавляем событие в ленту
             */
            return AjaxView::from([
                'iRating' => $oComment->getRating(),
                'iCountVote' => $oComment->getCountVote()
            ])->msgNotice($notice, $lang->Get('attention'), true);
        } else {
            return AjaxView::empty()->msgError($lang->Get('comment_vote_error'), $lang->Get('error'), true);
        }
    }

    /**
     * Голосование за топик
     *
     * @param \App\Modules\ModuleACL     $acl
     * @param \App\Modules\ModuleVote    $vote
     * @param \App\Modules\ModuleRating  $rating
     * @param \App\Modules\ModuleTopic   $topic
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\JsonView
     * @throws \Exception
     */
    protected function eventVoteTopic(ModuleACL $acl, ModuleVote $vote, ModuleRating $rating, ModuleTopic $topic, ModuleLang $lang): JsonView
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

        /**
         * Голосует автор топика?
         */
        if ($oTopic->getUserId() == $this->currentUser->getId()) {
            return AjaxView::empty()->msgError($lang->Get('topic_vote_error_self'), $lang->Get('attention'), true);
        }

        /**
         * Время голосования истекло?
         */
        if (strtotime($oTopic->getDateAdd()) <= time() - Config::Get('acl.vote.topic.limit_time')) {
            return AjaxView::empty()->msgError($lang->Get('topic_vote_error_time'), $lang->Get('attention'), true);
        }

        /**
         * Как проголосовал пользователь
         */
        $iValue = getRequestStr('value', null, 'post');
        if (!in_array($iValue, [ '1', '-1', '0' ])) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('attention'), true);
        }

        /**
         * Права на голосование
         */
        if (!$acl->CanVoteTopic($this->currentUser, $oTopic) and $iValue) {
            return AjaxView::empty()->msgError($lang->Get('comment_vote_error'), $lang->Get('attention'), true);
        }

        /**
         * Голосуем
         */
        $iValueOld = $iValue;
        $iCountVote = 1;
        $iVoteType = 0; //0 - при добавлении нового голоса, 1 - при его изменении, 2 - при отмене
        if ($oTopicVote = $vote->GetVote($oTopic->getId(), 'topic', $this->currentUser->getId())) {
            if ($iValue == $oTopicVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
                $iVoteType = 2;
                $iCountVote = -1;
            } elseif ($oTopicVote->getDirection() != 0) {
                $iValue += $iValue;
                $iVoteType = 1;
                $iCountVote = 0;
            }
            $vote->DeleteVote($oTopic->getId(), 'topic', $this->currentUser->getId());
        }

        $oTopicVote = new EntityVote();
        $oTopicVote->setTargetId($oTopic->getId());
        $oTopicVote->setTargetType('topic');
        $oTopicVote->setVoterId($this->currentUser->getId());
        $oTopicVote->setDirection($iValueOld);
        $oTopicVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float) $rating->VoteTopic(
            $this->currentUser,
            $oTopic,
            $iValue,
            $iValueOld,
            $iCountVote,
            $iVoteType
        );
        $oTopicVote->setValue($iVal);
        if ($iValue == 1) {
            $oTopic->setCountVoteUp($oTopic->getCountVoteUp() + 1);
        } elseif ($iValue == -1) {
            $oTopic->setCountVoteDown($oTopic->getCountVoteDown() + 1);
        } elseif ($iValue == 0) {
            $oTopic->setCountVoteAbstain($oTopic->getCountVoteAbstain() + 1);
        }

        if ($vote->AddVote($oTopicVote) and $topic->UpdateTopic($oTopic)) {
            $view = AjaxView::from(['iRating' => $oTopic->getRating(), 'iCountVote' => $oTopic->getCountVote()]);

            if ($iValue) {
                if ($iValueOld == 0) {
                    $vote->DeleteVote($oTopic->getId(), 'topic', $this->currentUser->getId());

                    $view->msgNotice($lang->Get('topic_vote_deleted'), $lang->Get('attention'), true);
                } else {
                    $view->msgNotice($lang->Get('topic_vote_ok'), $lang->Get('attention'), true);
                }
            } else {
                $view->msgNotice($lang->Get('topic_vote_ok_abstain'), $lang->Get('attention'), true);
            }

            return $view;
        } else {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }
    }

    /**
     * Голосование за блог
     *
     * @param \App\Modules\ModuleACL     $acl
     * @param \App\Modules\ModuleVote    $vote
     * @param \App\Modules\ModuleRating  $rating
     * @param \App\Modules\ModuleBlog    $blog
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\JsonView
     */
    protected function eventVoteBlog(ModuleACL $acl, ModuleVote $vote, ModuleRating $rating, ModuleBlog $blog, ModuleLang $lang): JsonView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Блог существует?
         */
        if (!($oBlog = $blog->GetBlogById(getRequestStr('idBlog', null, 'post')))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Голосует за свой блог?
         */
        if ($oBlog->getOwnerId() == $this->currentUser->getId()) {
            return AjaxView::empty()->msgError($lang->Get('blog_vote_error_self'), $lang->Get('attention'), true);
        }

        /**
         * Имеет право на голосование?
         */
        switch ($acl->CanVoteBlog($this->currentUser, $oBlog)) {
            case ModuleACL::CAN_VOTE_BLOG_ERROR_CLOSE:
                return AjaxView::empty()->msgError($lang->Get('blog_vote_error_close'), $lang->Get('attention'), true);

            default:
            case ModuleACL::CAN_VOTE_BLOG_FALSE:
                return AjaxView::empty()->msgError($lang->Get('blog_vote_error_acl'), $lang->Get('attention'), true);

            case ModuleACL::CAN_VOTE_BLOG_TRUE:
        }

        /**
         * Как именно голосует пользователь
         */
        $iValue = getRequestStr('value', null, 'post');
        if (in_array($iValue, ['1', '-1'])) {} else {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('attention'), true);
        }

        /**
         * Голосуем
         */
        $iValueOld = $iValue;
        if ($oBlogVote = $vote->GetVote($oBlog->getId(), 'blog', $this->currentUser->getId())) {
            if ($iValue == $oBlogVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
            } elseif ($oBlogVote->getDirection() != 0) {
                $iValue += $iValue;
            }
            $vote->DeleteVote($oBlog->getId(), 'comment', $this->currentUser->getId());
        }
        $oBlogVote = new EntityVote();
        $oBlogVote->setTargetId($oBlog->getId());
        $oBlogVote->setTargetType('blog');
        $oBlogVote->setVoterId($this->currentUser->getId());
        $oBlogVote->setDirection($iValueOld);
        $oBlogVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float) $rating->VoteBlog($this->currentUser, $oBlog, $iValue);
        $oBlogVote->setValue($iVal);
        $oBlog->setCountVote($oBlog->getCountVote() + 1);
        if ($vote->AddVote($oBlogVote) and $blog->UpdateBlog($oBlog)) {
            return AjaxView::from([
                'iCountVote' => $oBlog->getCountVote(),
                'iRating' => $oBlog->getRating()
            ])->msgError($lang->Get('blog_vote_ok'), $lang->Get('attention'), true);
        } else {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('attention'), true);
        }
    }

    /**
     * Голосование за пользователя
     *
     * @param \App\Modules\ModuleACL     $acl
     * @param \App\Modules\ModuleVote    $vote
     * @param \App\Modules\ModuleRating  $rating
     * @param \App\Modules\ModuleUser    $user
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\JsonView
     */
    protected function eventVoteUser(ModuleACL $acl, ModuleVote $vote, ModuleRating $rating, ModuleUser $user, ModuleLang $lang): JsonView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Пользователь существует?
         */
        if (!($oUser = $user->GetUserById(getRequestStr('idUser', null, 'post')))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Голосует за себя?
         */
        if ($oUser->getId() == $this->currentUser->getId()) {
            return AjaxView::empty()->msgError($lang->Get('user_vote_error_self'), $lang->Get('attention'), true);
        }

        /**
         * Имеет право на голосование?
         */
        if (!$acl->CanVoteUser($this->currentUser, $oUser)) {
            return AjaxView::empty()->msgError($lang->Get('user_vote_error_acl'), $lang->Get('attention'), true);
        }

        /**
         * Как проголосовал
         */
        $iValue = getRequestStr('value', null, 'post');
        if (!in_array($iValue, ['1', '-1'])) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('attention'), true);
        }

        $iValueOld = $iValue;
        if ($oUserVote = $vote->GetVote($oUser->getId(), 'user', $this->currentUser->getId())) {
            if ($iValue == $oUserVote->getDirection()) {
                $iValue -= 2 * $iValue;
                $iValueOld = 0;
            } elseif ($oUserVote->getDirection() != 0) {
                $iValue += $iValue;
            }
            $vote->DeleteVote($oUser->getId(), 'user', $this->currentUser->getId());
        }

        /**
         * Голосуем
         */
        $oUserVote = new EntityVote();
        $oUserVote->setTargetId($oUser->getId());
        $oUserVote->setTargetType('user');
        $oUserVote->setVoterId($this->currentUser->getId());
        $oUserVote->setDirection($iValueOld);
        $oUserVote->setDate(date("Y-m-d H:i:s"));
        $iVal = (float) $rating->VoteUser($this->currentUser, $oUser, $iValue);
        $oUserVote->setValue($iVal);

        if ($iValueOld != 0) {
            $oUser->setCountVote($oUser->getCountVote() + 1);
        } else {
            $oUser->setCountVote($oUser->getCountVote() - 1);
        }
        if ($vote->AddVote($oUserVote) and $user->Update($oUser)) {
            $view = AjaxView::from([
                'iRating' => $oUser->getRating(),
                'iSkill' => $oUser->getSkill(),
                'iCountVote' => $oUser->getCountVote()
            ]);

            if ($iValueOld == 0) {
                $vote->DeleteVote($oUser->getId(), 'user', $this->currentUser->getId());

                $view->msgNotice($lang->Get('user_vote_deleted'), $lang->Get('attention'), true);
            } else {
                $view->msgNotice($lang->Get('user_vote_ok'), $lang->Get('attention'), true);
            }

            return $view;
        } else {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }
    }

    /**
     * Голосование за вариант ответа в опросе
     *
     * @param \App\Modules\ModuleTopic   $topic
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\JsonView
     */
    protected function eventVoteQuestion(ModuleTopic $topic, ModuleLang $lang): JsonView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        /**
         * Параметры голосования
         */
        $idAnswer = getRequestStr('idAnswer', null, 'post');
        $idTopic = getRequestStr('idTopic', null, 'post');
        /**
         * Топик существует?
         */
        if (!($oTopic = $topic->GetTopicById($idTopic))) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Тип топика - опрос?
         */
        if ($oTopic->getType() != 'question') {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        /**
         * Уже голосовал?
         */
        if ($oTopicQuestionVote = $topic->GetTopicQuestionVote($oTopic->getId(), $this->currentUser->getId())) {
            return AjaxView::empty()->msgError($lang->Get('topic_question_vote_already'), $lang->Get('error'), true);
        }

        /**
         * Вариант ответа
         */
        $aAnswer = $oTopic->getQuestionAnswers();
        if (!isset($aAnswer[$idAnswer]) and $idAnswer != -1) {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }

        if ($idAnswer == -1) {
            $oTopic->setQuestionCountVoteAbstain($oTopic->getQuestionCountVoteAbstain() + 1);
        } else {
            $oTopic->increaseQuestionAnswerVote($idAnswer);
        }

        $oTopic->setQuestionCountVote($oTopic->getQuestionCountVote() + 1);
        /**
         * Голосуем(отвечаем на опрос)
         */
        $oTopicQuestionVote = new EntityTopicQuestionVote();
        $oTopicQuestionVote->setTopicId($oTopic->getId());
        $oTopicQuestionVote->setVoterId($this->currentUser->getId());
        $oTopicQuestionVote->setAnswer($idAnswer);
        if ($topic->AddTopicQuestionVote($oTopicQuestionVote) and $topic->updateTopic($oTopic)) {
            $local = HtmlView::global("question_result.tpl")->with(['oTopic' => $oTopic]);

            return AjaxView::from(['sText' => $local->fetch()])->msgNotice($lang->Get('topic_question_vote_ok'), $lang->Get('attention'));
        } else {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }
    }
}
