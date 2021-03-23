<?php

namespace App\Actions\Ajax;

use App\Modules\ModuleUser;
use Engine\LS;
use Engine\Modules\ModuleLang;
use Engine\Result\View\AjaxView;
use Engine\Routing\Controller;

class ActionAjaxIgnore extends Controller
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
     * Allow|forbid ignore user
     *
     * @param \App\Modules\ModuleUser    $user
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventForbidIgnoreUser(ModuleUser $user, ModuleLang $lang): AjaxView
    {
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }
        // allow only for administrator
        if (!$this->currentUser->isAdministrator()) {
            return AjaxView::empty()->msgError($lang->Get('not_access'), $lang->Get('error'), true);
        }
        // search for user
        if (!$oUser = $user->GetUserById(getRequest('idUser'))) {
            return AjaxView::empty()->msgError($lang->Get('user_not_found'), $lang->Get('error'), true);
        }

        $aForbidIgnore = $user->GetForbidIgnoredUsers();
        if (in_array($oUser->getId(), $aForbidIgnore)) {
            // remove user from forbid ignore list
            if ($user->AllowIgnoreUser($oUser->getId())) {
                return AjaxView::from(['sText' => $lang->Get('forbid_ignore_user')])->msgNotice($lang->Get('allow_ignore_user_ok'), $lang->Get('attention'));
            } else {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
        } else {
            // add user to forbid ignore list
            if ($user->ForbidIgnoreUser($oUser->getId())) {
                return AjaxView::from(['sText' => $lang->Get('allow_ignore_user')])->msgNotice($lang->Get('forbid_ignore_user_ok'), $lang->Get('attention'));
            } else {
                return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
            }
        }
    }

    /**
     * Ignore|disignore user
     *
     * @param \App\Modules\ModuleUser    $user
     * @param \Engine\Modules\ModuleLang $lang
     *
     * @return \Engine\Result\View\AjaxView
     */
    protected function eventIgnoreUser(ModuleUser $user, ModuleLang $lang): AjaxView
    {
        // check auth
        if (!$this->currentUser) {
            return AjaxView::empty()->msgError($lang->Get('need_authorization'), $lang->Get('error'), true);
        }

        // search for ignored user
        if (!$oUserIgnored = $user->GetUserById(getRequest('idUser'))) {
            return AjaxView::empty()->msgError($lang->Get('user_not_found'), $lang->Get('error'), true);
        }

        // is user try to ignore self
        if ($oUserIgnored->getId() == $this->currentUser->getId()) {
            return AjaxView::empty()->msgError($lang->Get('ignore_dissalow_own'), $lang->Get('error'), true);
        }
        $sType = getRequest('type');

        if ($sType == ModuleUser::TYPE_IGNORE_COMMENTS || $sType == ModuleUser::TYPE_IGNORE_TOPICS) {
            if ($user->IsUserIgnoredByUser($this->currentUser->getId(), $oUserIgnored->getId(), $sType)) {
                // remove user from ignore list
                if ($user->UnIgnoreUserByUser($this->currentUser->getId(), $oUserIgnored->getId(), $sType)) {
                    return AjaxView::from(['sText' => $lang->Get('ignore_user_'.$sType)])->msgNotice($lang->Get('disignore_user_ok_'.$sType), $lang->Get('attention'));
                } else {
                    return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
                }
            } else {
                $aForbidIgnore = $user->GetForbidIgnoredUsers();
                //check ignored user in forbid ignored list
                if (in_array($oUserIgnored->getId(), $aForbidIgnore)) {
                    return AjaxView::empty()->msgError($lang->Get('ignore_dissalow_this'), $lang->Get('error'), true);
                }

                //add user to ignore list
                if ($user->IgnoreUserByUser($this->currentUser->getId(), $oUserIgnored->getId(), $sType)) {
                    return AjaxView::from(['sText' => $lang->Get('disignore_user_'.$sType)])->msgNotice($lang->Get('ignore_user_ok_'.$sType), $lang->Get('attention'));
                } else {
                    return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
                }
            }
        } else {
            return AjaxView::empty()->msgError($lang->Get('system_error'), $lang->Get('error'), true);
        }
    }
}
