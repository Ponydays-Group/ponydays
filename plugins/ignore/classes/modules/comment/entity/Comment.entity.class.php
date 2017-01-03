<?php

class PluginIgnore_ModuleComment_EntityComment extends PluginIgnore_Inherit_ModuleComment_EntityComment
{
    /**
     * Check is comment bad (need to hide)
     * @return boolean 
     */
    public function isBad()
    {
        // is user auth and can ignore comment
        if ($this->User_IsAuthorization()) {
            $oUserCurrent = $this->User_GetUserCurrent();
            $aIgnoredUser = $this->User_GetIgnoredUsersByUser($oUserCurrent->getId(), PluginIgnore_ModuleUser::TYPE_IGNORE_COMMENTS);
            //is comment user in current user ignore list
            if (in_array($this->getUserId(), $aIgnoredUser)) {
                return true;
            }
        }
        return parent::isBad();
    }

}