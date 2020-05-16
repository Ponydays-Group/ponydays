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

use App\Modules\ModuleACL;
use App\Modules\ModuleBlog;
use App\Modules\ModuleComment;
use App\Modules\ModuleTalk;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Modules\ModuleMessage;
use Engine\Modules\ModuleViewer;
use Engine\Router;

/**
 * Экшен обработки всплывашек (/info/)
 *
 * @package actions
 * @since 1.0
 */
class ActionInfo extends Action
{
    /**
     * Текущий юзер
     *
     * @var \App\Entities\EntityUser|null
     */
    protected $oUserCurrent = null;

    /**
     * Инициализация
     *
     */
    public function Init()
    {
        /**
         * Получаем текущего юзера
         */
        $this->oUserCurrent = LS::Make(ModuleUser::class)->GetUserCurrent();
        LS::Make(ModuleViewer::class)->SetResponseAjax('json');
    }

    /**
     * Регистрация евентов
     */
    protected function RegisterEvent()
    {
        $this->AddEvent('topic', 'EventTopic');
        $this->AddEvent('comment', 'EventComment');
        $this->AddEvent('profile', 'EventProfile');
    }

    protected  function EventTopic() {
        $iTopicId = getRequest('iTopicId');
        if (!($oTopic=LS::Make(ModuleTopic::class)->GetTopicById($iTopicId))) {
            parent::EventNotFound();
            return;
        }
        /**
         * Проверяем права на просмотр топика
         */
        if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId()!=$oTopic->getUserId() and !$this->oUserCurrent->isAdministrator()))) {
            parent::EventNotFound();
            return;
        }

        /**
         * Определяем права на отображение записи из закрытого блога
         */
        if(in_array($oTopic->getBlog()->getType(), array('close', 'invite'))
            and (!$this->oUserCurrent
                || !in_array(
                    $oTopic->getBlog()->getId(),
                    LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($this->oUserCurrent)
                )
            )
        ) {
            LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('blog_close_show'),LS::Make(ModuleLang::class)->Get('not_access'));
            Router::Action('error');
            return;
        }

        $oViewer = LS::Make(ModuleViewer::class)->GetLocalViewer();
        $oViewer->Assign('oTopic', $oTopic);
        LS::Make(ModuleViewer::class)->AssignAjax('sText', $oViewer->Fetch("actions/ActionInfo/topic.tpl"));
    }

    protected  function EventComment() {
        $iCommentId = getRequest('iCommentId');
        if (!($oComment=LS::Make(ModuleComment::class)->GetCommentById($iCommentId))) {
            parent::EventNotFound(); return;
        }
        if ($oComment->getTargetType()=="topic") {
            $oTarget = LS::Make(ModuleTopic::class)->GetTopicById($oComment->getTargetId());
            /**
             * Проверяем права на просмотр топика
             */
            if (!$oTarget->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId() != $oTarget->getUserId() and !$this->oUserCurrent->isAdministrator()))) {
                parent::EventNotFound(); return;
            }

            /**
             * Определяем права на отображение записи из закрытого блога
             */
            if (in_array($oTarget->getBlog()->getType(), array('close', 'invite'))
                and (!$this->oUserCurrent
                    || !in_array(
                        $oTarget->getBlog()->getId(),
                        LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($this->oUserCurrent)
                    )
                )
            ) {
                LS::Make(ModuleMessage::class)->AddErrorSingle(LS::Make(ModuleLang::class)->Get('blog_close_show'), LS::Make(ModuleLang::class)->Get('not_access'));
                Router::Action('error'); return;
            }
        } else {
            if ($this->oUserCurrent == null) {
                echo "NO USER CURRENT";
                parent::EventNotFound(); return;
            }
            $oTarget = LS::Make(ModuleTalk::class)->GetTalkById($oComment->getTargetId());
            if (!($oTalkUser=LS::Make(ModuleTalk::class)->GetTalkUser($oTarget->getId(),$this->oUserCurrent->getId()))) {
                echo "NO TALK USER";
                parent::EventNotFound(); return;
            }
            /**
             * Пользователь активен в переписке?
             */
            if($oTalkUser->getUserActive()!=ModuleTalk::TALK_USER_ACTIVE){
                echo "NO USER ACTIVE";
                parent::EventNotFound(); return;
            }
        }

        $oViewer = LS::Make(ModuleViewer::class)->GetLocalViewer();
        $oViewer->Assign('oTarget', $oTarget);
        $oViewer->Assign('oComment', $oComment);
        if ($oComment->getDelete()) {
			$oComment->setUserDelete(LS::Make(ModuleUser::class)->GetUserById($oComment->getDeleteUserId()));
		}
        $oViewer->Assign('bEnableVoteInfo', LS::Make(ModuleACL::class)->CheckSimpleAccessLevel(Config::Get('acl.vote_list.comment.ne_enable_level'), $this->oUserCurrent, $oComment, 'comment'));
        LS::Make(ModuleViewer::class)->AssignAjax('sText', $oViewer->Fetch("actions/ActionInfo/comment.tpl"));
    }

    protected  function EventProfile() {
        $sLogin = getRequest('sLogin');
        if (!($oUser=LS::Make(ModuleUser::class)->GetUserByLogin($sLogin))) {
            parent::EventNotFound(); return;
        }

        $oViewer = LS::Make(ModuleViewer::class)->GetLocalViewer();

        $oViewer->Assign('oUserProfile', $oUser);
        $oViewer->Assign('iCountTopicUser', LS::Make(ModuleTopic::class)->GetCountTopicsPersonalByUser($oUser->getId(),1));
        $oViewer->Assign('iCountCommentUser', LS::Make(ModuleComment::class)->GetCountCommentsByUserId($oUser->getId(),'topic'));

        LS::Make(ModuleViewer::class)->AssignAjax('sText', $oViewer->Fetch("actions/ActionInfo/profile.tpl"));
    }
}