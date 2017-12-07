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
 * Экшен обработки ajax запросов
 * Ответ отдает в JSON фомате
 *
 * @package actions
 * @since 1.0
 */
class ActionServer extends Action

{
    /**
     * Текущий пользователь
     *
     * @var ModuleUser_EntityUser|null
     */
    protected $oUserCurrent = null;

    /**
     * Инициализация
     */
    public

    function Init()
    {
    }

    /**
     * Регистрация евентов
     */
    protected
    function RegisterEvent()
    {
        $this->AddEvent('deploy', 'EventDeploy');
        $this->AddEvent('getuserbykey', 'EventGetUserByKey');
        $this->AddEvent('hastopicaccess', 'EventHasTopicAccess');
    }

    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    protected
    function EventDeploy()
    {
        if (getRequest('token')!=Config::Get('deploy_token')) {
            return false;
        }
        $this->Viewer_SetResponseAjax('json', true, false);
        shell_exec('cd '.dirname(__FILE__)."/../../frontend/".' && git pull && webpack .');
    }

    function EventGetUserByKey() {
        $this->Viewer_SetResponseAjax('json', true, false);
        if (getRequest('token')!=Config::Get('deploy_token')) {
            return false;
        }
        $this->Viewer_SetResponseAjax('json', true, false);
        $oUser = $this->User_GetUserBySessionKey(getRequest("key"));
        if (!$oUser) {
            return;
        }
        $this->Viewer_AssignAjax("iUserId", $oUser->getId());
    }

    function EventHasTopicAccess() {
        $this->Viewer_SetResponseAjax('json', true, false);
        if (getRequest('token')!=Config::Get('deploy_token')) {
            $this->Viewer_AssignAjax("bAccess", false);
            return;
        }
        $this->Viewer_SetResponseAjax('json', true, false);
        $oUser = $this->User_GetUserById(getRequest("userId"));
        $oTopic = $this->Topic_GetTopicById(getRequest("topicId"));
        if (!$oUser) {
//            echo "!u", getRequest("topicId");
            $this->Viewer_AssignAjax("bAccess", false);
            return;
        }
        if (!$oTopic) {
            $this->Viewer_AssignAjax("bAccess", false);
            return;
        }
        /**
         * Проверяем права на просмотр топика
         */
        if (!$oTopic->getPublish() and (!$oUser or ($oUser->getId()!=$oTopic->getUserId() and !$oUser->isAdministrator()))) {
//            echo "PBL";
            $this->Viewer_AssignAjax("bAccess", false);
            return;
        }

        /**
         * Определяем права на отображение записи из закрытого блога
         */
        if(in_array($oTopic->getBlog()->getType(), array('close', 'invite'))
            and (!$oUser
                || !in_array(
                    $oTopic->getBlog()->getId(),
                    $this->Blog_GetAccessibleBlogsByUser($oUser)
                )
            )
        ) {
//            echo "Close";
            $this->Viewer_AssignAjax("bAccess", false);
            return;
        }
        $this->Viewer_AssignAjax("bAccess", true);
        return;
    }
}