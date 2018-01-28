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
     * @var ModuleUser_EntityUser|null
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
        $this->oUserCurrent = $this->User_GetUserCurrent();
        $this->Viewer_SetResponseAjax('json');
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
        if (!($oTopic=$this->Topic_GetTopicById($iTopicId))) {
            return parent::EventNotFound();
        }
        /**
         * Проверяем права на просмотр топика
         */
        if (!$oTopic->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId()!=$oTopic->getUserId() and !$this->oUserCurrent->isAdministrator()))) {
            return parent::EventNotFound();
        }

        /**
         * Определяем права на отображение записи из закрытого блога
         */
        if(in_array($oTopic->getBlog()->getType(), array('close', 'invite'))
            and (!$this->oUserCurrent
                || !in_array(
                    $oTopic->getBlog()->getId(),
                    $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent)
                )
            )
        ) {
            $this->Message_AddErrorSingle($this->Lang_Get('blog_close_show'),$this->Lang_Get('not_access'));
            return Router::Action('error');
        }

        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('oTopic', $oTopic);
        $this->Viewer_AssignAjax('sText', $oViewer->Fetch("actions/ActionInfo/topic.tpl"));
    }

    protected  function EventComment() {
        $iCommentId = getRequest('iCommentId');
        if (!($oComment=$this->Comment_GetCommentById($iCommentId))) {
            return parent::EventNotFound();
        }
        if ($oComment->getTargetType()=="topic") {
            $oTarget = $this->Topic_GetTopicById($oComment->getTargetId());
            /**
             * Проверяем права на просмотр топика
             */
            if (!$oTarget->getPublish() and (!$this->oUserCurrent or ($this->oUserCurrent->getId() != $oTarget->getUserId() and !$this->oUserCurrent->isAdministrator()))) {
                return parent::EventNotFound();
            }

            /**
             * Определяем права на отображение записи из закрытого блога
             */
            if (in_array($oTarget->getBlog()->getType(), array('close', 'invite'))
                and (!$this->oUserCurrent
                    || !in_array(
                        $oTarget->getBlog()->getId(),
                        $this->Blog_GetAccessibleBlogsByUser($this->oUserCurrent)
                    )
                )
            ) {
                $this->Message_AddErrorSingle($this->Lang_Get('blog_close_show'), $this->Lang_Get('not_access'));
                return Router::Action('error');
            }
        } else {
            if ($this->oUserCurrent == null) {
                echo "NO USER CURRENT";
                return parent::EventNotFound();
            }
            $oTarget = $this->Talk_GetTalkById($oComment->getTargetId());
            if (!($oTalkUser=$this->Talk_GetTalkUser($oTarget->getId(),$this->oUserCurrent->getId()))) {
                echo "NO TALK USER";
                return parent::EventNotFound();
            }
            /**
             * Пользователь активен в переписке?
             */
            if($oTalkUser->getUserActive()!=ModuleTalk::TALK_USER_ACTIVE){
                echo "NO USER ACTIVE";
                return parent::EventNotFound();
            }
        }

        $oViewer = $this->Viewer_GetLocalViewer();
        $oViewer->Assign('oTarget', $oTarget);
        $oViewer->Assign('oComment', $oComment);
        if ($oComment->getDelete()) {
			$oComment->setUserDelete($this->User_GetUserById($oComment->getDeleteUserId()));
		}
        $oViewer->Assign('bEnableVoteInfo', $this->ACL_CheckSimpleAccessLevel(Config::Get('acl.vote_list.comment.ne_enable_level'), $this->oUserCurrent, $oComment, 'comment'));
        $this->Viewer_AssignAjax('sText', $oViewer->Fetch("actions/ActionInfo/comment.tpl"));
    }

    protected  function EventProfile() {
        $sLogin = getRequest('sLogin');
        if (!($oUser=$this->User_GetUserByLogin($sLogin))) {
            return parent::EventNotFound();
        }

        $oViewer = $this->Viewer_GetLocalViewer();

        $oViewer->Assign('oUserProfile', $oUser);
        $oViewer->Assign('iCountTopicUser', $this->Topic_GetCountTopicsPersonalByUser($oUser->getId(),1));
        $oViewer->Assign('iCountCommentUser', $this->Comment_GetCountCommentsByUserId($oUser->getId(),'topic'));

        $this->Viewer_AssignAjax('sText', $oViewer->Fetch("actions/ActionInfo/profile.tpl"));
    }
}