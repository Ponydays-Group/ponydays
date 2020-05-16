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

namespace App\Entities;

use App\Modules\ModuleEditComment;
use Engine\Entity;
use Engine\LS;

class EntityEditCommentData extends Entity
{
    public function getCommentTextSource()
    {
        return $this->_getDataOne('comment_text_source');
    }

    public function getCommentId()
    {
        return $this->_getDataOne('comment_id');
    }

    public function getUserId()
    {
        return $this->_getDataOne('user_id');
    }

    public function getDateAdd()
    {
        return $this->_getDataOne('date_add');
    }

    public function setCommentTextSource($getRequest)
    {
        $this->_aData['comment_text_source'] = $getRequest;
    }

    public function setCommentId($getId)
    {
        $this->_aData['comment_id'] = $getId;
    }

    public function setUserId(int $getId)
    {
        $this->_aData['user_id'] = $getId;
    }

    public function setDateAdd($sDE)
    {
        $this->_aData['date_add'] = $sDE;
    }

    public function save()
    {
        return LS::Make(ModuleEditComment::class)->SaveData($this);
    }
}
