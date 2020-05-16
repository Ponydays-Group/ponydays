<?php

namespace App\Mappers;

use Engine\Mapper;

class MapperAPI extends Mapper
{
    public function writeKey($iId, $sKey)
    {
        $sql = "INSERT INTO prefix_api_keys (user_id, `uid`) VALUES(?, ?)";
        $iId = $this->oDb->query($sql, $iId, $sKey);

        return true;

    }

    public function readKey($iId)
    {
        $sql = "SELECT `uid` FROM prefix_api_keys WHERE user_id = ?";
        if ($aRows = $this->oDb->query($sql, $iId)) {
            return $aRows[0]['key'];
        } else {
            return false;
        }

    }

    public function deleteKey($sKey)
    {
        $sql = "DELETE FROM prefix_api_keys WHERE uid = ?";
        if ($this->oDb->query($sql, $sKey)) {
            return true;
        } else {
            return false;
        }

    }

    public function getUserByKey($sKey)
    {
        $sql = "SELECT user_id FROM prefix_api_keys WHERE `uid` = ?";
        if ($aRows = $this->oDb->query($sql, $sKey)) {
            return $aRows[0]['user_id'];
        } else {
            return false;
        }

    }

}
