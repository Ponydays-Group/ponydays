 <?php

/**
 * PluginIgnore_HookIgnore
 *
 * Add hooks to tpl
 */
class PluginIgnore_HookIgnore extends Hook
{
    /**
     * Hook registration
     */
    public function RegisterHook()
    {
        $this->AddHook('template_profile_sidebar_show', 'ProfileView', __CLASS__);
        $this->AddHook('profile_whois_show', 'AddJsLang', __CLASS__);
    }

    /**
     * Add lang variables to JS
     */
    public function AddJsLang()
    {
        $this->Lang_AddLangJs(array(
            'plugin.ignore.ignore_user_talks', 'plugin.ignore.disignore_user_talks',
            'plugin.ignore.ignore_user_ok_talk', 'plugin.ignore.disignore_user_ok_talk'
        ));
    }

    /**
     * Add ignore button to user profile
     * 
     * @param array $aData
     *
     * @return string
     */
    public function ProfileView($aData)
    {
        /* @var $oUserProfile ModuleUser_EntityUser */
        $oUserProfile = $aData['oUserProfile'];
        /* @var $oUserCurrent ModuleUser_EntityUser */
        $oUserCurrent = $this->User_GetUserCurrent();

        if ($oUserCurrent) {
            $aForbidIgnore = $this->User_GetForbidIgnoredUsers();
            if (in_array($oUserProfile->getId(), $aForbidIgnore)) {
                $this->Viewer_Assign('bForbidIgnore', true);
            } else if ($oUserCurrent->getId() != $oUserProfile->getId()) {
                $bIgnoredTopics = $this->User_IsUserIgnoredByUser($oUserCurrent->getId(), $oUserProfile->getId(), PluginIgnore_ModuleUser::TYPE_IGNORE_TOPICS);
                $bIgnoredComments = $this->User_IsUserIgnoredByUser($oUserCurrent->getId(), $oUserProfile->getId(), PluginIgnore_ModuleUser::TYPE_IGNORE_COMMENTS);

                $this->Viewer_Assign('bIgnoredTopics', $bIgnoredTopics);
                $this->Viewer_Assign('bIgnoredComments', $bIgnoredComments);
            }

            $aUserBlacklist = $this->Talk_GetBlacklistByUserId($oUserCurrent->getId());
            if (isset($aUserBlacklist[$oUserProfile->getId()])) {
                $bIgnoredTalks = 1;
            } else {
                $bIgnoredTalks = 0;
            }
            $this->Viewer_Assign('bIgnoredTalks', $bIgnoredTalks);

            return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__) . 'profile_ignore.tpl');
        }
    }

}

