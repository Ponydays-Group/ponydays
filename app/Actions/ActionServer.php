<?php

namespace App\Actions;

use App\Modules\ModuleBlog;
use App\Modules\ModuleTalk;
use App\Modules\ModuleTopic;
use App\Modules\ModuleUser;
use Engine\Config;
use Engine\Result\View\JsonView;
use Engine\Routing\Controller;

/**
 * Экшен обработки ajax запросов
 * Ответ отдает в JSON фомате
 *
 * @package actions
 * @since   1.0
 */
class ActionServer extends Controller
{
    /**
     * Текущий пользователь
     *
     * @var \App\Entities\EntityUser|null
     */
    protected $oUserCurrent = null;

    protected function eventDeploy(): JsonView
    {
        if (!hash_equals(getRequest('token'), Config::Get('deploy_token'))) {
            return JsonView::from([
                'success' => false,
                'msg' => 'Wrong deploy token'
            ]);
        }

        $output = [];
        $return = 1;
        exec('bash ./database/deploy.sh 2>&1', $output, $return);
        if ($return != 0) {
            return JsonView::from([
                'success' => false,
                'msg' => 'An error occurred during execution',
                'output' => $output
            ]);
        }

//      LS::Make(ModuleNower::class)->Post('/site-update');

        return JsonView::from([
            'success' => true,
            'msg' => 'The project has been successfully deployed',
            'output' => $output
        ]);
    }

    protected function eventGetUserByKey(ModuleUser $user): JsonView
    {
        if (!hash_equals(getRequest('token'), Config::Get('deploy_token'))) {
            return JsonView::from([
                'success' => false,
                'msg' => 'Wrong deploy token'
            ]);
        }

        $oUser = $user->GetUserBySessionKey(getRequest("key"));
        if (!$oUser) {
            return JsonView::from([
                'success' => false,
                'msg' => 'Wrong session key'
            ]);
        }

        return JsonView::from([
            'iUserId' => $oUser->getId(),
            'sUserAvatar' => $oUser->getProfileAvatar(),
            'sUserLogin' => $oUser->getLogin()
        ]);
    }

    protected function eventHasTopicAccess(ModuleUser $user, ModuleTopic $topic, ModuleBlog $blog): JsonView
    {
        if (!hash_equals(getRequest('token'), Config::Get('deploy_token'))) {
            return JsonView::from(['bAccess' => false]);
        }

        $oUser = $user->GetUserById(getRequest("userId"));
        $oTopic = $topic->GetTopicById(getRequest("topicId"));
//        if (!$oUser) {
////            echo "!u", getRequest("topicId");
//            LS::Make(ModuleViewer::class)->AssignAjax("bAccess", false);
//            return;
//        }
        if (!$oTopic) {
            return JsonView::from(['bAccess' => false]);
        }
        /**
         * Проверяем права на просмотр топика
         */
        if (!$oUser) {
            if ($oTopic->getPublish() && $oTopic->getBlog()->getType() == "open") {
                return JsonView::from(['bAccess' => true]);
            } else {
                return JsonView::from(['bAccess' => false]);
            }
        }
        if (!$oTopic->getPublish() and (!$oUser or ($oUser->getId() != $oTopic->getUserId() and !$oUser->isAdministrator()))) {
            return JsonView::from(['bAccess' => false]);
        }

        /**
         * Определяем права на отображение записи из закрытого блога
         */
        if (
            in_array($oTopic->getBlog()->getType(), ['close', 'invite'])
            and (!$oUser || !in_array($oTopic->getBlog()->getId(), $blog->GetAccessibleBlogsByUser($oUser)))
        ) {
            return JsonView::from(['bAccess' => false]);
        }

        return JsonView::from(['bAccess' => true]);
    }

    function eventHasTalkAccess(ModuleTalk $talk): JsonView
    {
        if (!hash_equals(getRequest('token'), Config::Get('deploy_token'))) {
            return JsonView::from(['bAccess' => false]);
        }

        $sUserId = getRequest("userId");
        $sTalkId = getRequest("talkId");
        /**
         * Пользователь есть в переписке?
         */
        if (!($oTalkUser = $talk->GetTalkUser($sTalkId, $sUserId))) {
            return JsonView::from(['bAccess' => false]);
        }
        /**
         * Пользователь активен в переписке?
         */
        if ($oTalkUser->getUserActive() != ModuleTalk::TALK_USER_ACTIVE) {
            return JsonView::from(['bAccess' => false]);
        }

        return JsonView::from(['bAccess' => true]);
    }
}
