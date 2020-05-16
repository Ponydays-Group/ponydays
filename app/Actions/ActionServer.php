<?php

namespace App\Actions;

use App\Modules\ModuleBlog;
use App\Modules\ModuleTalk;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Action;
use Engine\Config;
use Engine\LS;
use Engine\Modules\ModuleViewer;

/**
 * Экшен обработки ajax запросов
 * Ответ отдает в JSON фомате
 *
 * @package actions
 * @since   1.0
 */
class ActionServer extends Action
{
    /**
     * Текущий пользователь
     *
     * @var \App\Entities\EntityUser|null
     */
    protected $oUserCurrent = null;

    /**
     * Инициализация
     */
    public function Init() { }

    /**
     * Регистрация евентов
     */
    protected function RegisterEvent()
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

    protected function EventDeploy()
    {
        LS::Make(ModuleViewer::class)->SetResponseJson();
        if (!hash_equals(getRequest('token'), Config::Get('deploy_token'))) {
            LS::Make(ModuleViewer::class)->AssignAjax("success", false);
            LS::Make(ModuleViewer::class)->AssignAjax("msg", "Wrong deploy token");

            return;
        }
        $output = [];
        $return = 1;
        exec('bash ./database/deploy.sh 2>&1', $output, $return);
        if ($return != 0) {
            LS::Make(ModuleViewer::class)->AssignAjax("success", false);
            LS::Make(ModuleViewer::class)->AssignAjax("msg", "An error occurred during execution");
            LS::Make(ModuleViewer::class)->AssignAjax("output", $output);

            return;
        }
        LS::Make(ModuleViewer::class)->AssignAjax("success", true);
        LS::Make(ModuleViewer::class)->AssignAjax("msg", "The project has been successfully deployed");
        LS::Make(ModuleViewer::class)->AssignAjax("output", $output);
//      LS::Make(ModuleNower::class)->Post('/site-update');
    }

    function EventGetUserByKey()
    {
        LS::Make(ModuleViewer::class)->SetResponseAjax('json', true, false);
        if (!hash_equals(getRequest('token'), Config::Get('deploy_token'))) {
            return;
        }

        $oUser = LS::Make(ModuleUser::class)->GetUserBySessionKey(getRequest("key"));
        if (!$oUser) {
            return;
        }
        LS::Make(ModuleViewer::class)->AssignAjax("iUserId", $oUser->getId());
        LS::Make(ModuleViewer::class)->AssignAjax("sUserAvatar", $oUser->getProfileAvatar());
        LS::Make(ModuleViewer::class)->AssignAjax("sUserLogin", $oUser->getLogin());
    }

    function EventHasTopicAccess()
    {
        LS::Make(ModuleViewer::class)->SetResponseAjax('json', true, false);
        if (!hash_equals(getRequest('token'), Config::Get('deploy_token'))) {
            LS::Make(ModuleViewer::class)->AssignAjax("bAccess", false);

            return;
        }

        $oUser = LS::Make(ModuleUser::class)->GetUserById(getRequest("userId"));
        $oTopic = LS::Make(ModuleTopic::class)->GetTopicById(getRequest("topicId"));
//        if (!$oUser) {
////            echo "!u", getRequest("topicId");
//            LS::Make(ModuleViewer::class)->AssignAjax("bAccess", false);
//            return;
//        }
        if (!$oTopic) {
            LS::Make(ModuleViewer::class)->AssignAjax("bAccess", false);

            return;
        }
        /**
         * Проверяем права на просмотр топика
         */
        if (!$oUser) {
            if ($oTopic->getPublish() && $oTopic->getBlog()->getType() == "open") {
                LS::Make(ModuleViewer::class)->AssignAjax("bAccess", true);

                return;
            } else {
                LS::Make(ModuleViewer::class)->AssignAjax("bAccess", "NOOOO");

                return;
            }
        }
        if (!$oTopic->getPublish() and (!$oUser or ($oUser->getId() != $oTopic->getUserId()
                    and !$oUser->isAdministrator()))
        ) {
//            echo "PBL";
            LS::Make(ModuleViewer::class)->AssignAjax("bAccess", false);

            return;
        }

        /**
         * Определяем права на отображение записи из закрытого блога
         */
        if (in_array($oTopic->getBlog()->getType(), ['close', 'invite'])
            and (!$oUser
                || !in_array(
                    $oTopic->getBlog()->getId(),
                    LS::Make(ModuleBlog::class)->GetAccessibleBlogsByUser($oUser)
                )
            )
        ) {
//            echo "Close";
            LS::Make(ModuleViewer::class)->AssignAjax("bAccess", false);

            return;
        }
        LS::Make(ModuleViewer::class)->AssignAjax("bAccess", true);

        return;
    }

    function EventHasTalkAccess()
    {
        LS::Make(ModuleViewer::class)->SetResponseAjax('json', true, false);
        if (!hash_equals(getRequest('token'), Config::Get('deploy_token'))) {
            LS::Make(ModuleViewer::class)->AssignAjax("bAccess", false);

            return;
        }

        $sUserId = getRequest("userId");
        $sTalkId = getRequest("talkId");
        /**
         * Пользователь есть в переписке?
         */
        if (!($oTalkUser = LS::Make(ModuleTalk::class)->GetTalkUser($sTalkId, $sUserId))) {
            LS::Make(ModuleViewer::class)->AssignAjax("bAccess", false);

            return;
        }
        /**
         * Пользователь активен в переписке?
         */
        if ($oTalkUser->getUserActive() != ModuleTalk::TALK_USER_ACTIVE) {
            LS::Make(ModuleViewer::class)->AssignAjax("bAccess", false);

            return;
        }
        LS::Make(ModuleViewer::class)->AssignAjax("bAccess", true);

        return;
    }
}
