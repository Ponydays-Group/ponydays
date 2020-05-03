<?php
/*-------------------------------------------------------
*
*   kEditComment.
*   Copyright © 2012 Alexei Lukin
*
*--------------------------------------------------------
*
*   Official site: http://kerbystudio.ru
*   Contact e-mail: kerby@kerbystudio.ru
*
---------------------------------------------------------
*/

use Engine\Engine;
use Engine\Config;
use Engine\Mapper;

class ModuleEditcomment_MapperEditcomment extends Mapper
{
    public function HasAnswers($sId)
    {
        $sql="SELECT
        comment_id
        FROM
        " . Config::Get('db.table.comment') . "
        WHERE
        comment_pid=?d	and comment_delete=0 and comment_publish=1
        LIMIT 0,1 ;";
        
        if ($aRow=$this->oDb->selectRow($sql, $sId))
        {
            return true;
        }
        return false;
    }

    public function GetFirstAnswer($sId)
    {
        $sql="SELECT
        *
        FROM
        " . Config::Get('db.table.comment') . "
        WHERE
        comment_pid=?d	and comment_delete=0 and comment_publish=1
        LIMIT 1;";
		if ($aRow=$this->oDb->selectRow($sql, $sId)) {
			return Engine::GetEntity('Comment', $aRow);
		}
		return null;
    }
}
