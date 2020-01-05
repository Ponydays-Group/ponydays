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
        $this->AddEvent('hastalkaccess', 'EventHasTalkAccess');
    }

    /**********************************************************************************
     ************************ РЕАЛИЗАЦИЯ ЭКШЕНА ***************************************
     **********************************************************************************
     */

    protected
    function EventDeploy()
    {
		$this->Viewer_SetResponseJson();
		if(!hash_equals(getRequest('token'), Config::Get('deploy_token'))) {
			$this->Viewer_AssignAjax("success", false);
			$this->Viewer_AssignAjax("msg", "Wrong deploy token");
            return;
        }
		$output = array();
		$return = 1;
        exec('./database/deploy.sh 2>&1', $output, $return);
        if($return != 0) {
        	$this->Viewer_AssignAjax("success", false);
        	$this->Viewer_AssignAjax("msg", "An error occurred during execution");
        	$this->Viewer_AssignAjax("output", $output);
        	return;
		}
        $this->Viewer_AssignAjax("success", true);
        $this->Viewer_AssignAjax("msg", "The project has been successfully deployed");
		$this->Viewer_AssignAjax("output", $output);
//      $this->Nower_Post('/site-update');
    }

    function EventGetUserByKey() {
        $this->Viewer_SetResponseAjax('json', true, false);
        if (getRequest('token')!=Config::Get('deploy_token')) {
            return false;
        }

        $oUser = $this->User_GetUserBySessionKey(getRequest("key"));
        if (!$oUser) {
            return;
        }
        $this->Viewer_AssignAjax("iUserId", $oUser->getId());
        $this->Viewer_AssignAjax("sUserAvatar", $oUser->getProfileAvatar());
        $this->Viewer_AssignAjax("sUserLogin", $oUser->getLogin());
    }

    function EventHasTopicAccess() {
        $this->Viewer_SetResponseAjax('json', true, false);
        if (getRequest('token')!=Config::Get('deploy_token')) {
            $this->Viewer_AssignAjax("bAccess", false);
            return;
        }

        $oUser = $this->User_GetUserById(getRequest("userId"));
        $oTopic = $this->Topic_GetTopicById(getRequest("topicId"));
//        if (!$oUser) {
////            echo "!u", getRequest("topicId");
//            $this->Viewer_AssignAjax("bAccess", false);
//            return;
//        }
        if (!$oTopic) {
            $this->Viewer_AssignAjax("bAccess", false);
            return;
        }
        /**
         * Проверяем права на просмотр топика
         */
        if (!$oUser) {
            if ($oTopic->getPublish() && $oTopic->getBlog()->getType()=="open") {
                $this->Viewer_AssignAjax("bAccess", true);
                return;
            } else {
                $this->Viewer_AssignAjax("bAccess", "NOOOO");
                return;
            }
        }
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

    function EventHasTalkAccess() {
		$this->Viewer_SetResponseAjax('json', true, false);
		if (getRequest('token')!=Config::Get('deploy_token')) {
			$this->Viewer_AssignAjax("bAccess", false);
			return;
		}

		$sUserId = getRequest("userId");
		$sTalkId = getRequest("talkId");
		/**
		 * Пользователь есть в переписке?
		 */
		if (!($oTalkUser = $this->Talk_GetTalkUser($sTalkId, $sUserId))) {
			$this->Viewer_AssignAjax("bAccess", false);
			return;
		}
		/**
		 * Пользователь активен в переписке?
		 */
		if ($oTalkUser->getUserActive() != ModuleTalk::TALK_USER_ACTIVE) {
			$this->Viewer_AssignAjax("bAccess", false);
			return;
		}
		$this->Viewer_AssignAjax("bAccess", true);
		return;
	}
}
