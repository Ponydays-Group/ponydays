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

namespace App\Mappers;

use App\Entities\EntityComment;
use App\Entities\EntityEditCommentData;
use Engine\Config;
use Engine\Mapper;

class MapperEditComment extends Mapper
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
			return new EntityComment($aRow);
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
            return new EntityEditCommentData($aRow);
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
                $aResult[] = new EntityEditCommentData($aRow);
            }
        }
        return $aResult;
    }

    /**
     * @param $data EntityEditCommentData
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
