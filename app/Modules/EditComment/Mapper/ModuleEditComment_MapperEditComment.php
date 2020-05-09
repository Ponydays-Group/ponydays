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

namespace App\Modules\EditComment\Mapper;

use App\Modules\Comment\Entity\ModuleComment_EntityComment;
use App\Modules\EditComment\Entity\ModuleEditComment_EntityData;
use Engine\Config;
use Engine\Mapper;

class ModuleEditComment_MapperEditComment extends Mapper
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
			return new ModuleComment_EntityComment($aRow);
		}
		return null;
    }

    public function GetLastEditData(int $comment_id)
    {
        $sql="SELECT
        *
        FROM
        " . Config::Get('db.table.editcomment') . "
        WHERE
        comment_id=?d
        ORDER BY date_add DESC
        LIMIT 1;";
        if($aRow=$this->oDb->selectRow($sql, $comment_id)) {
            return new ModuleEditComment_EntityData($aRow);
        }
        return null;
    }

    public function GetDataItemsByCommentId(int $comment_id, &$iCount) {
        $sql="SELECT
        *
        FROM
        " . Config::Get('db.table.editcomment') . "
        WHERE
        comment_id=?d
        ORDER BY date_add DESC
        ;";
        $aResult = array();
        if($aRows=$this->oDb->selectPage($iCount, $sql, $comment_id)) {
            foreach ($aRows as $aRow) {
                $aResult[] = new ModuleEditComment_EntityData($aRow);
            }
        }
        return $aResult;
    }

    /**
     * @param $data ModuleEditComment_EntityData
     */
    public function SaveData($data) {
        $sql="INSERT INTO " . Config::Get('db.table.editcomment') ."
        (comment_id, user_id, date_add, comment_text_source)
        VALUES (?d, ?d, ?, ?);";
        if($id=$this->oDb->query($sql, $data->getCommentId(), $data->getUserId(), $data->getDateAdd(), $data->getCommentTextSource())) {
            return true;
        }
        return false;
    }
}
