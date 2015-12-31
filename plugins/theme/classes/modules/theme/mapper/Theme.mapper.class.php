<?php

class PluginTheme_ModuleTheme_MapperTheme extends Mapper
{
	public function createTheme(PluginTheme_ModuleTheme_EntityTheme $theme){
         $sql = "INSERT INTO ".Config::Get('plugin.theme.table.theme_table')."
 			(
			user_id,
 			theme
 			)
			VALUES(?, ?)
		";
         if ($iId=$this->oDb->query(
            $sql,
            $theme->getUser(), $theme->getTheme()
         ))
        {
             return $iId;
         }         return false;
     }
}

?>
