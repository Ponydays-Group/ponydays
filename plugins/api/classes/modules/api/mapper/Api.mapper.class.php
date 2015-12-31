<?php

class PluginApi_ModuleApi_MapperApi extends Mapper
{
    public function writeKey($userId, $key){
        $sql = "INSERT INTO ".Config::Get('plugin.api.table.api_keys')."
        (
        user_id,
        key
        )
        VALUES(?, ?) 
        ";
        if ($iId=$this->oDb->query($sql,$userId,$key)){
            return $iId;
        }

    }

}

?>
