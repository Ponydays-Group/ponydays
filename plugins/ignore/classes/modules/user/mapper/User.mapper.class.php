<?php

/**
 * @method oDb DbSimple_Generic_Database 
 */
class PluginIgnore_ModuleUser_MapperUser extends PluginIgnore_Inherit_ModuleUser_MapperUser
{

    /**
     * Ignore user
     * 
     * @param string $sUserId
     * @param string $sUserIgnoreId
     * @param string $sType
     * @return boolean
     */
    public function IgnoreUserByUser($sUserId, $sUserIgnoreId, $sType)
    {
        $sql = "INSERT INTO
                    " . Config::Get('db.table.user_ignore') . "
                (
                user_id,
                user_ignored_id,
                ignore_type
                )
		VALUES(?d, ?d, ?)
                ";
        return $this->oDb->query($sql, $sUserId, $sUserIgnoreId, $sType);
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
        $sql = "DELETE FROM
                    " . Config::Get('db.table.user_ignore') . "
                WHERE 
                    user_id = ?d
                AND    
                    user_ignored_id = ?d
                AND
                    ignore_type = ?
                ";
        return $this->oDb->query($sql, $sUserId, $sUserIgnoreId, $sType);
    }

    /**
     * Get ignored user ids by user
     * 
     * @param string $sUserId
     * @return array
     */
    public function GetIgnoredUsersByUser($sUserId, $sType)
    {
        $sql = "SELECT
                    user_ignored_id,
                    ignore_type
                FROM
                    " . Config::Get('db.table.user_ignore') . "
                WHERE
                    user_id = ?d
                ";
        $aResult = array();
        if ($aRows = $this->oDb->select($sql, $sUserId)) {
            foreach ($aRows as $aRow) {
                $aResult[$aRow['user_ignored_id']][] = $aRow['ignore_type'];
            }
        }
        return $aResult;
    }

    public function GetForbidIgnoredUsers()
    {
        $sql = "SELECT
                    user_id
                FROM
                    " . Config::Get('db.table.user_forbid_ignore');
        $aResult = array();
        if ($aRows = $this->oDb->select($sql)) {
            foreach ($aRows as $aRow) {
                $aResult[] = $aRow['user_id'];
            }
        }
        return $aResult;
    }

    public function ForbidIgnoreUser($sUserId)
    {
        $sql = "INSERT INTO
                    " . Config::Get('db.table.user_forbid_ignore') . "
                (
                user_id
                )
		VALUES(?d)
                ";

        return $this->oDb->query($sql, $sUserId);
    }

    public function AllowIgnoreUser($sUserId)
    {
        $sql = "DELETE FROM
                    " . Config::Get('db.table.user_forbid_ignore') . "
                WHERE
                    user_id = ?d
                ";

        return $this->oDb->query($sql, $sUserId);
    }
    
    public function ClearIgnoranceUser($sUserId)
    {
        $sql = "DELETE FROM
                    " . Config::Get('db.table.user_ignore') . "
                WHERE
                    user_ignored_id = ?d
                ";

        return $this->oDb->query($sql, $sUserId);
    }





















    public function IgnoreBlogByUser($sUserId, $sBlogIgnoreId, $sType)
    {
        $sql = "INSERT INTO
                    " . Config::Get('db.table.user_ignore') . "
                (
                user_id,
                user_ignored_id,
                ignore_type
                )
		VALUES(?d, ?d, ?)
                ";
        return $this->oDb->query($sql, $sUserId, $sBlogIgnoreId, $sType);
    }

    /**
     * Unignore user
     *
     * @param string $sUserId
     * @param string $sUserIgnoreId
     * @param string $sType
     * @return boolean
     */
    public function UnIgnoreBlogByUser($sUserId, $sBlogIgnoreId, $sType)
    {
        $sql = "DELETE FROM
                    " . Config::Get('db.table.user_ignore') . "
                WHERE
                    user_id = ?d
                AND
                    user_ignored_id = ?d
                AND
                    ignore_type = ?
                ";
        return $this->oDb->query($sql, $sUserId, $sBlogIgnoreId, $sType);
    }

    /**
     * Get ignored user ids by user
     *
     * @param string $sUserId
     * @return array
     */
    public function GetIgnoredBlogsByUser($sUserId, $sType)
    {
        $sql = "SELECT
                    user_ignored_id,
                    ignore_type
                FROM
                    " . Config::Get('db.table.user_ignore') . "
                WHERE
                    user_id = ?d
                ";
        $aResult = array();
        if ($aRows = $this->oDb->select($sql, $sUserId)) {
            foreach ($aRows as $aRow) {
                $aResult[$aRow['user_ignored_id']][] = $aRow['ignore_type'];
            }
        }
        return $aResult;
    }

}