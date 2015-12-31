<?php

/**
 * PluginIgnore_ModuleUser
 *
 * @extends ModuleUser
 * @method oMapper ModuleUser_Mapper_User
 *
 */
class PluginIgnore_ModuleUser extends PluginIgnore_Inherit_ModuleUser
{

    const TYPE_IGNORE_COMMENTS = 'comments';
    const TYPE_IGNORE_TOPICS = 'topics';

    /**
     * Ignore user
     * 
     * @param string $sUserId
     * @param string $sUserIgnoreId
     * @param string $sType
     *
     * @return boolean
     */
    public function IgnoreUserByUser($sUserId, $sUserIgnoreId, $sType)
    {
        $this->Cache_Delete("user_ignore_{$sUserId}");
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('topic_update', "topic_update_user_{$sUserIgnoreId}"));
        if ($this->oMapper->IgnoreUserByUser($sUserId, $sUserIgnoreId, $sType) === false) {
            return false;
        }
        return true;
    }

    /**
     * Unignore user
     *
     * @param string $sUserId
     * @param string $sUserIgnoreId
     * @param string $sType
     * @return boolean
     */
    public function UnIgnoreUserByUser($sUserId, $sUserIgnoreId, $sType)
    {
        $this->Cache_Delete("user_ignore_{$sUserId}");
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('topic_update', "topic_update_user_{$sUserIgnoreId}"));
        return $this->oMapper->UnIgnoreUserByUser($sUserId, $sUserIgnoreId, $sType);
    }

    /**
     * Is user ignore user
     *
     * @param type $sUserId
     * @param type $sUserIgnoredId
     * @param type $sType
     * @return boolean
     */
    public function IsUserIgnoredByUser($sUserId, $sUserIgnoredId, $sType)
    {
        $aIgnored = $this->GetIgnoredUsersByUser($sUserId, $sType);
        return in_array($sUserIgnoredId, $aIgnored);
    }

    /**
     * Get ignored user ids by user
     *
     * @param string $sUserId
     * @param string $sType
     * @return array
     */
    public function GetIgnoredUsersByUser($sUserId, $sType = null)
    {
        if (false === ($data = $this->Cache_Get("user_ignore_{$sUserId}"))) {
            if ($data = $this->oMapper->GetIgnoredUsersByUser($sUserId, $sType)) {
                $this->Cache_Set($data, "user_ignore_{$sUserId}", array('users_ignorance'), 60 * 60 * 24 * 1);
            }
        }
        if (!is_null($sType)) {
            $aResult = array();
            foreach ($data as $id => $aTypes) {
                if (array_search($sType, $aTypes) !== false) {
                    array_push($aResult, $id);
                }
            }
            $data = $aResult;
        }
        return $data;
    }

    public function GetForbidIgnoredUsers()
    {
        if (false === ($data = $this->Cache_Get("user_forbid_ignore"))) {
            if ($data = $this->oMapper->GetForbidIgnoredUsers()) {
                $this->Cache_Set($data, "user_forbid_ignore", array(), 60 * 60 * 24 * 1);
            }
        }
        return $data;
    }

    public function AllowIgnoreUser($sUserId)
    {
        $this->Cache_Delete("user_forbid_ignore");
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('users_ignorance'));
        if ($this->oMapper->AllowIgnoreUser($sUserId) === false) {
            return false;
        }
        return true;
    }

    public function ForbidIgnoreUser($sUserId)
    {
        $this->Cache_Delete("user_forbid_ignore");
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('users_ignorance', 'topic_update'));
        if ($this->oMapper->ForbidIgnoreUser($sUserId) === false) {
            return false;
        }
        $this->oMapper->ClearIgnoranceUser($sUserId);
        return true;
    }













































    public function IgnoreBlogByUser($sUserId, $sUserIgnoreId, $sType)
    {
        $this->Cache_Delete("user_ignore_{$sUserId}");
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('topic_update', "topic_update_user_{$sUserIgnoreId}"));
        if ($this->oMapper->IgnoreBlogByUser($sUserId, $sUserIgnoreId, $sType) === false) {
            return false;
        }
        return true;
    }

    /**
     * Unignore user
     *
     * @param string $sUserId
     * @param string $sUserIgnoreId
     * @param string $sType
     * @return boolean
     */
    public function UnIgnoreBlogByUser($sUserId, $sUserIgnoreId, $sType)
    {
        $this->Cache_Delete("user_ignore_{$sUserId}");
        $this->Cache_Clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('topic_update', "topic_update_user_{$sUserIgnoreId}"));
        return $this->oMapper->UnBlogUserByUser($sUserId, $sUserIgnoreId, $sType);
    }

    /**
     * Is user ignore user
     *
     * @param type $sUserId
     * @param type $sUserIgnoredId
     * @param type $sType
     * @return boolean
     */
    public function IsBlogIgnoredByUser($sUserId, $sUserIgnoredId, $sType)
    {
        $aIgnored = $this->GetIgnoredBlogsByUser($sUserId, $sType);
        return in_array($sUserIgnoredId, $aIgnored);
    }

    /**
     * Get ignored user ids by user
     *
     * @param string $sUserId
     * @param string $sType
     * @return array
     */
    public function GetIgnoredBlogsByUser($sUserId, $sType = null)
    {
        if (false === ($data = $this->Cache_Get("user_ignore_{$sUserId}"))) {
            if ($data = $this->oMapper->GetIgnoredBlogsByUser($sUserId, $sType)) {
                $this->Cache_Set($data, "user_ignore_{$sUserId}", array('users_ignorance'), 60 * 60 * 24 * 1);
            }
        }
        if (!is_null($sType)) {
            $aResult = array();
            foreach ($data as $id => $aTypes) {
                if (array_search($sType, $aTypes) !== false) {
                    array_push($aResult, $id);
                }
            }
            $data = $aResult;
        }
        return $data;
    }

}
