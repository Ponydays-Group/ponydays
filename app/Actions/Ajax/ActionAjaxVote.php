<?php

namespace App\Actions\Ajax;

use App\Entities\EntityVote;
use App\Modules\ModuleACL;
use App\Modules\ModuleComment;
use App\Modules\ModuleRating;
use App\Modules\ModuleUser;
use App\Modules\ModuleVote;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Result\View\AjaxView;
use Engine\Routing\Controller;

class ActionAjaxVote extends Controller
{
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
     * @param \Engine\Modules\ModuleMessage $message
     * @param \Engine\Modules\ModuleLang    $lang
     *
     * @return \Engine\Result\View\JsonView
     * @throws \Exception
     */
    protected function EventVoteComment(ModuleACL $acl, ModuleVote $vote, ModuleComment $comment, ModuleRating $rating, ModuleMessage $message, ModuleLang $lang): \Engine\Result\View\JsonView
    {
        /**
         * Пользователь авторизован?
         */
        if (!$this->currentUser) {
            $message->AddErrorSingle($lang->Get('need_authorization'), $lang->Get('error'));

            return AjaxView::empty();
        }

        /**
         * Комментарий существует?
         */
        if (!($oComment = LS::Make(ModuleComment::class)->GetCommentById(getRequestStr('idComment', null, 'post')))) {
            $message->AddErrorSingle($lang->Get('comment_vote_error_noexists'), $lang->Get('error'));

            return AjaxView::empty();
        }

        /**
         * Голосует автор комментария?
         */
        if ($oComment->getUserId() == $this->currentUser->getId()) {
            $message->AddErrorSingle($lang->Get('comment_vote_error_self'), $lang->Get('attention'));

            return AjaxView::empty();
        }

        /**
         * Время голосования истекло?
         */
        if (
            Config::Get('acl.vote.comment.limit_time') != 0
            && strtotime($oComment->getDate()) <= time() - Config::Get('acl.vote.comment.limit_time')
        ) {
            $message->AddErrorSingle($lang->Get('comment_vote_error_time'), $lang->Get('attention'));

            return AjaxView::empty();
        }

        /**
         * Пользователь имеет право голоса?
         */
        if (! $acl->CanVoteComment($this->currentUser, $oComment)) {
            $message->AddErrorSingle($lang->Get('comment_vote_error'), $lang->Get('attention'));

            return AjaxView::empty();
        }

        /**
         * Как именно голосует пользователь
         */
        $iValue = getRequestStr('value', null, 'post');
        if (!in_array($iValue, ['1', '-1'])) {
            $message->AddErrorSingle($lang->Get('comment_vote_error_value'), $lang->Get('attention'));

            return AjaxView::empty();
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
        $iVal = (float) $rating->VoteComment(
            $this->currentUser,
            $oComment,
            $iValue,
            $iValueOld,
            $iCountVote,
            $iVoteType
        );
        $oTopicCommentVote->setValue($iVal);

        if ($vote->AddVote($oTopicCommentVote) and $comment->UpdateComment($oComment)) {
            if ($iValueOld == 0) {
                $vote->DeleteVote($oComment->getId(), 'comment', $this->currentUser->getId());
                $message->AddNoticeSingle($lang->Get('comment_vote_deleted'), $lang->Get('attention'));
            } else {
                $message->AddNoticeSingle($lang->Get('comment_vote_ok'), $lang->Get('attention'));
            }
            /**
             * Добавляем событие в ленту
             */
            return AjaxView::from([
                'iRating' => $oComment->getRating(),
                'iCountVote' => $oComment->getCountVote()
            ]);
        } else {
            $message->AddErrorSingle($lang->Get('comment_vote_error'), $lang->Get('error'));

            return AjaxView::empty();
        }
    }

}