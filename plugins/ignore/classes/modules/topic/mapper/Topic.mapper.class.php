<?php

/**
 * @method oDb DbSimple_Generic_Database 
 */
class PluginIgnore_ModuleTopic_MapperTopic extends PluginIgnore_Inherit_ModuleTopic_MapperTopic
{
    /**
     * Modify where for query
     * @param array $aFilter
     * @return string 
     */
    protected function buildFilter($aFilter)
    {
        $sWhere = parent::buildFilter($aFilter);

        if (isset($aFilter['not_user_id'])) {
            $sWhere .= " AND t.user_id NOT IN(" . implode(', ', $aFilter['not_user_id']) . ")";
        }
        
        return $sWhere;
    }

}