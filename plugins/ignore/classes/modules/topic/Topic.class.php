<?php

class PluginIgnore_ModuleTopic extends PluginIgnore_Inherit_ModuleTopic
{

    /**
     * Get topics by filter
     *
     * @param  array $aFilter
     * @param  int   $iPage
     * @param  int   $iPerPage
     * @return array
     */
    public function GetTopicsByFilter($aFilter, $iPage = 0, $iPerPage = 0, $aAllowData = array('user' => array(), 'blog' => array('owner' => array(), 'relation_user'), 'vote', 'favourite', 'comment_new'))
    {
        return parent::GetTopicsByFilter($this->_getModifiedFilter($aFilter), $iPage, $iPerPage, $aAllowData);
    }

    /**
     * Get count topics by filter
     *
     * @param array $aFilter
     * @return integer
     */
    public function GetCountTopicsByFilter($aFilter)
    {
        return parent::GetCountTopicsByFilter($this->_getModifiedFilter($aFilter));
    }
    
    /**
     * Modify filter with ignored users
     * @param array $aFilter
     * @return array
     */
    protected function _getModifiedFilter(array $aFilter)
    {
        if ($this->oUserCurrent) {
            $aIgnoredUser = $this->User_GetIgnoredUsersByUser($this->oUserCurrent->getId(), PluginIgnore_ModuleUser::TYPE_IGNORE_TOPICS);
            if (count($aIgnoredUser)) {
                if (isset($aFilter['user_id'])) {
                    //leave posibility view topics throu profile
                    if (is_array($aFilter['user_id'])) {
                        $aFilter['user_id'] = array_diff($aFilter['user_id'], $aIgnoredUser);
                        if (!count($aFilter['user_id'])) {
                            $aFilter['not_user_id'] = $aIgnoredUser;
                        }
                    }
                } else {
                    $aFilter['not_user_id'] = $aIgnoredUser;
                }
            }
        }
        return $aFilter;
    }

}