<?php

/**
 * PluginIgnore_ActionAjax
 *
 * Extend base ActionAjax
 *
 * @extends ActionAjax
 */
class PluginIgnore_ActionAjax extends PluginIgnore_Inherit_ActionAjax
{

    protected function RegisterEvent()
    {
        parent::RegisterEvent();

        $this->AddEventPreg('/^ignore$/i', 'EventIgnoreUser');
        $this->AddEvent('ignore-blog', 'EventIgnoreBlog');
        $this->AddEventPreg('/^forbid-ignore$/i', 'EventForbidIgnoreUser');
    }

    /**
     * Allow|forbid ignore user
     */
    protected function EventForbidIgnoreUser()
    {
        // check auth
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }
        // allow only for administrator
        if (!$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'), $this->Lang_Get('error'));
            return;
        }
        // search for user
        if (!$oUser = $this->User_GetUserById(getRequest('idUser'))) {
            $this->Message_AddErrorSingle($this->Lang_Get('user_not_found'), $this->Lang_Get('error'));
            return;
        }

        $aForbidIgnore = $this->User_GetForbidIgnoredUsers();
        if (in_array($oUser->getId(), $aForbidIgnore)) {
            // remove user from forbid ignore list
            if ($this->User_AllowIgnoreUser($oUser->getId())) {
                $this->Message_AddNoticeSingle($this->Lang_Get('plugin.ignore.allow_ignore_user_ok'), $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('sText', $this->Lang_Get('plugin.ignore.forbid_ignore_user'));
            } else {
                $this->Message_AddErrorSingle(
                    $this->Lang_Get('system_error'), $this->Lang_Get('error')
                );
            }
        } else {
            // add user to forbid ignore list
            if ($this->User_ForbidIgnoreUser($oUser->getId())) {
                $this->Message_AddNoticeSingle($this->Lang_Get('plugin.ignore.forbid_ignore_user_ok'), $this->Lang_Get('attention'));
                $this->Viewer_AssignAjax('sText', $this->Lang_Get('plugin.ignore.allow_ignore_user'));
            } else {
                $this->Message_AddErrorSingle(
                    $this->Lang_Get('system_error'), $this->Lang_Get('error')
                );
            }
        }
    }

    /**
     * Ignore|disignore user
     */
    protected function EventIgnoreUser()
    {
        // check auth
        if (!$this->oUserCurrent) {
            $this->Message_AddErrorSingle($this->Lang_Get('need_authorization'), $this->Lang_Get('error'));
            return;
        }

        // search for ignored user
        if (!$oUserIgnored = $this->User_GetUserById(getRequest('idUser'))) {
            $this->Message_AddErrorSingle($this->Lang_Get('user_not_found'), $this->Lang_Get('error'));
            return;
        }

        // is user try to ignore self
        if ($oUserIgnored->getId() == $this->oUserCurrent->getId()) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.ignore.ignore_dissalow_own'), $this->Lang_Get('error'));
            return;
        }
        $sType = getRequest('type');

        if ($sType == PluginIgnore_ModuleUser::TYPE_IGNORE_COMMENTS || $sType == PluginIgnore_ModuleUser::TYPE_IGNORE_TOPICS) {
            if ($this->User_IsUserIgnoredByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                // remove user from ignore list
                if ($this->User_UnIgnoreUserByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                    $this->Message_AddNoticeSingle($this->Lang_Get('plugin.ignore.disignore_user_ok_' . $sType), $this->Lang_Get('attention'));
                    $this->Viewer_AssignAjax('sText', $this->Lang_Get('plugin.ignore.ignore_user_' . $sType));
                } else {
                    $this->Message_AddErrorSingle(
                        $this->Lang_Get('system_error'), $this->Lang_Get('error')
                    );
                }
            } else {
                $aForbidIgnore = $this->User_GetForbidIgnoredUsers();
                //check ignored user in forbid ignored list
                if (in_array($oUserIgnored->getId(), $aForbidIgnore)) {
                    $this->Message_AddErrorSingle($this->Lang_Get('plugin.ignore.ignore_dissalow_this'), $this->Lang_Get('error'));
                    return;
                }

                //add user to ignore list
                if ($this->User_IgnoreUserByUser($this->oUserCurrent->getId(), $oUserIgnored->getId(), $sType)) {
                    $this->Message_AddNoticeSingle($this->Lang_Get('plugin.ignore.ignore_user_ok_' . $sType), $this->Lang_Get('attention'));
                    $this->Viewer_AssignAjax('sText', $this->Lang_Get('plugin.ignore.disignore_user_' . $sType));
                } else {
                    $this->Message_AddErrorSingle(
                        $this->Lang_Get('system_error'), $this->Lang_Get('error')
                    );
                }
            }
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'), $this->Lang_Get('error'));
            return;
        }
    }




























    protected function EventIgnoreBlog()
    {
        User_IgnoreBlogByUser('0', '1', 'blogs');
        $this->Viewer_AssignAjax('sText', GetIgnoredBlogsByUser('0'));
    }

}

