<?php

namespace App\Actions\Ajax;

use App\Entities\EntityNotification;
use App\Modules\ModuleNotification;
use App\Modules\ModuleNower;
use App\Modules\ModuleUser;
use Engine\LS;
use Engine\Modules\ModuleLogger;
use Engine\Result\View\AjaxView;
use Engine\Routing\Controller;
use Engine\Routing\Exception\Http\BadRequestHttpException;
use Engine\Routing\Exception\Http\ForbiddenHttpException;

class ActionAjaxBan extends Controller
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
     *  Бан пользователя администрацией
     *
     * @param \App\Modules\ModuleUser         $user
     * @param \App\Modules\ModuleNower        $nower
     * @param \App\Modules\ModuleNotification $m_notification
     * @param \Engine\Modules\ModuleLogger    $logger
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventBan(ModuleUser $user, ModuleNower $nower, ModuleNotification $m_notification, ModuleLogger $logger): AjaxView
    {
        if (!$this->currentUser || !($this->currentUser->isAdministrator() || $this->currentUser->isGlobalModerator())) {
            throw new ForbiddenHttpException();
        }

        $iUserId = (int)getRequest('iUserId');

        if ((int)$this->currentUser->getId() == $iUserId) {
            throw new BadRequestHttpException();
        }

        if ((int)getRequest('iUnban')) {
            $user->Unban($iUserId);
            $sLogText = $this->currentUser->getLogin()." разбанил пользователя ".$iUserId;
            $logger->Notice($sLogText);

            $notificationTitle = "<a href='".$this->currentUser->getUserWebPath()."'>".$this->currentUser->getLogin()."</a> разбанил вас на сайте";
            $notification = new EntityNotification([
                'user_id'           => $iUserId,
                'text'              => "",
                'title'             => $notificationTitle,
                'link'              => "",
                'rating'            => 0,
                'notification_type' => 16,
                'target_type'       => "global",
                'target_id'         => -1,
                'sender_user_id'    => $this->currentUser->getId(),
                'group_target_type' => 'global',
                'group_target_id'   => -1
            ]);
            if ($notificationCreated = $m_notification->createNotification($notification)) {
                $nower->PostNotification($notificationCreated);
            }

            return AjaxView::empty();
        }

        $sBanComment = getRequest('sBanComment');
        $iBanHours = getRequest('iBanHours');

        if ((int)$iBanHours) {
            $t = time() + ((int)$iBanHours * 60 * 60);
            $user->Ban($iUserId, $this->currentUser->getId(), date("Y-m-d H:i:s", $t), 0, $sBanComment);
        } else {
            $user->Ban($iUserId, $this->currentUser->getId(), null, 1, $sBanComment);
        }

        $sLogText = $this->currentUser->getLogin()." забанил пользователя ".$iUserId;
        $logger->Notice($sLogText);

        $notificationTitle = "<a href='".$this->currentUser->getUserWebPath()."'>".$this->currentUser->getLogin()."</a> забанил вас на сайте";
        $notification = new EntityNotification([
            'user_id'           => $iUserId,
            'text'              => "",
            'title'             => $notificationTitle,
            'link'              => "",
            'rating'            => 0,
            'notification_type' => 16,
            'target_type'       => "global",
            'target_id'         => -1,
            'sender_user_id'    => $this->currentUser->getId(),
            'group_target_type' => 'global',
            'group_target_id'   => -1
        ]);
        if ($notificationCreated = $m_notification->createNotification($notification)) {
            $nower->PostNotification($notificationCreated);
        }

        return AjaxView::empty();
    }
}
